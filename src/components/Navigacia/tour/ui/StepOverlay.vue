<template>
  <div class="step-root" aria-hidden="true">
    <div class="guide-spot" :style="spotStyle"></div>

    <div
      class="guide-tooltip guide-theme-ha light"
      :style="tooltipStyle"
      :data-pos="tooltipSide"
      role="dialog"
      aria-modal="true"
    >
      <p class="guide-title">{{ step?.title || "Krátky návod" }}</p>
      <p class="guide-text">{{ step?.text || "" }}</p>

      <div class="guide-actions">
        <span class="guide-progress"
          >Krok {{ currentNumber }}/{{ total }}</span
        >

        <button
          class="guide-btn ghost"
          type="button"
          @click.stop="prev"
          :disabled="!hasPrev"
        >
          Späť
        </button>

        <button class="guide-btn skip" type="button" @click.stop="close">
          Preskočiť
        </button>

        <button class="guide-btn primary" type="button" @click.stop="next">
          {{ isLast ? "Dokončiť" : "Ďalej" }}
        </button>
      </div>

      <button
        class="guide-close"
        type="button"
        @click.stop="close"
        aria-label="Zatvoriť"
      >
        ✕
      </button>
    </div>
  </div>
</template>

<script>
import {
  ref,
  computed,
  watch,
  onMounted,
  onBeforeUnmount,
  nextTick,
} from "vue";
import { tour } from "../tour.js";

export default {
  name: "StepOverlay",
  setup() {
    const open = computed(() => tour.state.open);
    const index = computed(() => tour.state.index);
    const steps = computed(() => tour.state.steps || []);

    // intro (= type: 'intro') sa do poradovníka krokov neráta
    const countedIndexes = computed(() =>
      steps.value.reduce((acc, step, idx) => {
        if (idx === 0 && step?.type === "intro") return acc;
        if (!step || step.type === "intro" || step.__skipped) return acc;
        acc.push(idx);
        return acc;
      }, [])
    );
    const total = computed(() => countedIndexes.value.length);
    const currentNumber = computed(() => {
      const pos = countedIndexes.value.indexOf(index.value);
      return pos >= 0 ? pos + 1 : 0;
    });

    const isLast = computed(() => currentNumber.value >= total.value);
    const hasPrev = computed(() => countedIndexes.value.indexOf(index.value) > 0);
    const step = computed(() => steps.value[index.value] || null);

    const spotStyle = ref({});
    const tooltipStyle = ref({});
    const tooltipSide = ref("bottom");

    let rafId = 0;
    let targetRO = null;

    const clamp = (v, min, max) => Math.min(Math.max(v, min), max);
    const safeNumber = (value) =>
      typeof value === "number" && Number.isFinite(value) ? value : 0;
    const toVw = (value) =>
      `${(safeNumber(value) / Math.max(window.innerWidth || 1, 1)) * 100}vw`;
    const toVh = (value) =>
      `${(safeNumber(value) / Math.max(window.innerHeight || 1, 1)) * 100}vh`;
    const toRem = (value) => `${safeNumber(value) / 16}rem`;

    function blurActive() {
      const ae = document.activeElement;
      if (ae && ae !== document.body && typeof ae.blur === "function")
        ae.blur();
    }

    function waitForBox(el, tries = 0) {
      return new Promise((resolve) => {
        const r =
          el && typeof el.getBoundingClientRect === "function"
            ? el.getBoundingClientRect()
            : null;
        const ok = r && r.width > 0 && r.height > 0;
        if (ok || tries > 20) return resolve();
        setTimeout(() => resolve(waitForBox(el, tries + 1)), 40);
      });
    }

    function pickTarget(s) {
      let el = s?.selector ? document.querySelector(s.selector) : null;
      if (!el) return null;
      if (s.closest) {
        const wrap = el.closest(s.closest);
        if (wrap) el = wrap;
      } else if (s.focus === "img") {
        const img = el.querySelector("img");
        if (img) el = img;
      }
      return el;
    }

    function isMenuLike(el) {
      if (!el) return false;
      const menuAncestor = el.closest(
        ".menu, .Menu, .sidebar, .right-menu, .menuRight, .the-menu, .TheMenu"
      );
      if (menuAncestor) return true;
      const r = el.getBoundingClientRect?.();
      if (!r) return false;
      const vw = window.innerWidth;
      return r.left > vw * 0.55;
    }

    async function updateSpot(scrollIntoView = false) {
      if (!open.value) return;
      const s = step.value;
      if (!s) return;

      let el = pickTarget(s);

      // 👇 kľúčová ochrana – ak nie je Element, krok preskoč a nič nepozoruj
      if (!(el instanceof Element)) {
        skipMissing();
        return;
      }

      detachTargetObserver();
      attachTargetObserver(el);

      await nextTick();
      await waitForBox(el);

      if (scrollIntoView) {
        const scrollTarget =
          s.__scrollTarget instanceof Element ? s.__scrollTarget : el;
        scrollTarget.scrollIntoView({ behavior: "smooth", block: "center" });
        if (scrollTarget !== el) {
          await waitForBox(scrollTarget);
        }
        await waitForBox(el);
      }

      blurActive();
      if (typeof el.blur === "function") el.blur();

      const rect = el.getBoundingClientRect();
      const cs = window.getComputedStyle(el);

      const PAD = s.pad ?? 22;
      const radiusGuess =
        s.radius ??
        Math.max(
          parseFloat(cs.borderTopLeftRadius) || 0,
          parseFloat(cs.borderTopRightRadius) || 0,
          parseFloat(cs.borderBottomLeftRadius) || 0,
          parseFloat(cs.borderBottomRightRadius) || 0,
          18
        );

      let x = Math.max(0, rect.left - PAD);
      let y = Math.max(0, rect.top - PAD);
      let w = Math.min(window.innerWidth - x, rect.width + PAD * 2);
      let h = Math.min(window.innerHeight - y, rect.height + PAD * 2);

      const off = s.offset || { x: 0, y: 0, w: 0, h: 0 };
      x += off.x || 0;
      y += off.y || 0;
      w += off.w || 0;
      h += off.h || 0;

      spotStyle.value = {
        left: toVw(x),
        top: toVh(y),
        width: toVw(w),
        height: toVh(h),
        borderRadius: toRem(radiusGuess),
      };

      const ring = { x, y, w, h };
      const preferred = s.side || (isMenuLike(el) ? "left" : "bottom");
      placeTooltip(ring, preferred);
    }

    function placeTooltip(ring, preferred) {
      const MARGIN = 8;
      const GAP = 18;
      const vw = window.innerWidth;
      const vh = window.innerHeight;

      const card = document.querySelector(".guide-tooltip");
      const cw = card?.offsetWidth || 380;
      const ch = card?.offsetHeight || 180;

      const order = ["bottom", "top", "right", "left"];
      const candidates = [preferred, ...order.filter((s) => s !== preferred)];

      const coords = (side) => {
        if (side === "top") {
          return {
            left: clamp(ring.x + ring.w / 2 - cw / 2, MARGIN, vw - cw - MARGIN),
            top: ring.y - GAP - ch,
          };
        }
        if (side === "bottom") {
          return {
            left: clamp(ring.x + ring.w / 2 - cw / 2, MARGIN, vw - cw - MARGIN),
            top: ring.y + ring.h + GAP,
          };
        }
        if (side === "left") {
          return {
            left: ring.x - GAP - cw,
            top: clamp(ring.y + ring.h / 2 - ch / 2, MARGIN, vh - ch - MARGIN),
          };
        }
        return {
          left: ring.x + ring.w + GAP,
          top: clamp(ring.y + ring.h / 2 - ch / 2, MARGIN, vh - ch - MARGIN),
        };
      };

      const fits = (side) => {
        const p = coords(side);
        return (
          p.left >= MARGIN &&
          p.top >= MARGIN &&
          p.left + cw <= vw - MARGIN &&
          p.top + ch <= vh - MARGIN
        );
      };

      const side = candidates.find(fits) || "bottom";
      const pos = coords(side);

      let arrowX = 24;
      let arrowY = 24;
      if (side === "top" || side === "bottom") {
        arrowX = clamp(ring.x + ring.w / 2 - pos.left, 18, cw - 18);
      } else {
        arrowY = clamp(ring.y + ring.h / 2 - pos.top, 18, ch - 18);
      }

      tooltipSide.value = side;
      tooltipStyle.value = {
        left: toVw(pos.left),
        top: toVh(pos.top),
        "--arrow-x": toRem(arrowX),
        "--arrow-y": toRem(arrowY),
      };
    }

    async function skipMissing() {
      const skipped = await tour.skipCurrentStep?.();
      if (!skipped) {
        if (index.value < steps.value.length - 1) tour.next();
        else tour.close();
      }
    }

    function updateSpotThrottled() {
      if (!open.value) return;
      if (rafId) return;
      rafId = requestAnimationFrame(async () => {
        rafId = 0;
        await updateSpot(false);
      });
    }

    // ✅ bezpečné pripájanie/odpájanie observera
    function attachTargetObserver(el) {
      if (!window.ResizeObserver) return;
      if (!(el instanceof Element)) return;
      detachTargetObserver();
      targetRO = new ResizeObserver(() => updateSpotThrottled());
      try {
        targetRO.observe(el);
      } catch (_) {
        /* no-op */
      }
    }
    function detachTargetObserver() {
      try {
        targetRO?.disconnect?.();
      } catch (_) {
        /* no-op */
      }
      targetRO = null;
    }

    // ovládanie
    const next = () => tour.next();
    const prev = () => tour.prev();
    const close = () => tour.close();

    function onKeydown(e) {
      if (!open.value) return;
      const k = e.key;
      if (k === "ArrowRight") {
        e.preventDefault();
        next();
      } else if (k === "ArrowLeft") {
        e.preventDefault();
        prev();
      } else if (k === "Escape") {
        e.preventDefault();
        close();
      }
    }

    // reakcie
    watch(index, async () => {
      if (open.value) await updateSpot(true);
    });

    onMounted(async () => {
      window.addEventListener("resize", updateSpotThrottled, { passive: true });
      window.addEventListener("scroll", updateSpotThrottled, { passive: true });
      window.addEventListener("keydown", onKeydown);

      await nextTick();
      await updateSpot(true);
    });

    onBeforeUnmount(() => {
      window.removeEventListener("resize", updateSpotThrottled);
      window.removeEventListener("scroll", updateSpotThrottled);
      window.removeEventListener("keydown", onKeydown);
      detachTargetObserver();
    });

    return {
      index,
      total,
      currentNumber,
      isLast,
      hasPrev,
      step,
      spotStyle,
      tooltipStyle,
      tooltipSide,
      prev,
      next,
      close,
    };
  },
};
</script>

<style>
:root {
  --ha-yellow: #90ca50;
  --ha-backdrop: rgba(12, 16, 22, 0.75);
  --ha-card-bg: #f9faf1;
  --ha-card-fg: #0f1419;
  --ha-card-border: #e6ecf2;
  --tour-dur: 320ms;
  --tour-ease: cubic-bezier(0.2, 0.8, 0.2, 1);
}

/* RING / backdrop */
.step-root {
  position: fixed;
  inset: 0;
  z-index: 3995;
  pointer-events: none;
}
.guide-spot {
  position: fixed;
  z-index: 3995;
  pointer-events: none;
  border: 0.1875rem solid var(--ha-yellow);
  border-radius: 1.25rem;
  box-shadow: 0 0 0 625rem var(--ha-backdrop),
    0 0 0 0.375rem rgba(254, 243, 90, 0.22);
  transition: left var(--tour-dur) var(--tour-ease),
    top var(--tour-dur) var(--tour-ease), width var(--tour-dur) var(--tour-ease),
    height var(--tour-dur) var(--tour-ease),
    border-radius var(--tour-dur) var(--tour-ease),
    box-shadow var(--tour-dur) var(--tour-ease);
  will-change: left, top, width, height, border-radius;
}

/* TOOLTIP */
.guide-tooltip.light {
  position: fixed;
  z-index: 4001;
  max-width: min(92vw, 26.25rem);
  padding: clamp(0.625rem, 2.5vw, 0.875rem) clamp(2.125rem, 7vw, 0rem)
    clamp(0.625rem, 2.2vw, 0.75rem) clamp(0.625rem, 2.5vw, 1.875rem);
  border-radius: clamp(0.75rem, 2.4vw, 1.125rem);
  background: var(--ha-card-bg);
  color: var(--ha-card-fg);
  border: 0.0625rem solid var(--ha-card-border);
  box-shadow: 0 1.75rem 4.375rem rgba(0, 0, 0, 0.22),
    0 0.625rem 1.875rem rgba(0, 0, 0, 0.12);
  transition: left var(--tour-dur) var(--tour-ease),
    top var(--tour-dur) var(--tour-ease), transform 160ms ease-out,
    opacity 160ms ease-out;
  pointer-events: auto;
}
.guide-tooltip.light::after {
  content: "";
  position: absolute;
  width: 1rem;
  height: 1rem;
  background: var(--ha-card-bg);
  transform: rotate(45deg);
  box-shadow: -0.1875rem -0.1875rem 0.375rem rgba(0, 0, 0, 0.08);
}
.guide-tooltip.light[data-pos="bottom"]::after {
  top: -0.5rem;
  left: var(--arrow-x, 1.5rem);
}
.guide-tooltip.light[data-pos="top"]::after {
  bottom: -0.5rem;
  left: var(--arrow-x, 1.5rem);
  box-shadow: 0.1875rem 0.1875rem 0.375rem rgba(0, 0, 0, 0.15);
}
.guide-tooltip.light[data-pos="left"]::after {
  right: -0.5rem;
  top: var(--arrow-y, 1.5rem);
  box-shadow: 0.1875rem -0.1875rem 0.375rem rgba(0, 0, 0, 0.12);
}
.guide-tooltip.light[data-pos="right"]::after {
  left: -0.5rem;
  top: var(--arrow-y, 1.5rem);
  box-shadow: -0.1875rem 0.1875rem 0.375rem rgba(0, 0, 0, 0.12);
}

.guide-title {
  margin: 0 0 0.15rem;
  font-weight: 800;
  font-size: clamp(0.98rem, 0.9rem + 0.35vw, 1.06rem);
}
.guide-text {
  margin: 0 0 0.6rem;
  font-weight: 500;
  line-height: 1.45;
  opacity: 0.95;
  font-size: clamp(0.92rem, 0.88rem + 0.25vw, 1rem);
}
.guide-actions {
  display: flex;
  gap: 0.5rem;
  justify-content: flex-end;
  align-items: center;
}
.guide-progress {
  margin-right: auto;
  opacity: 0.75;
  font-size: clamp(0.86rem, 0.8rem + 0.25vw, 0.95rem);
}

.guide-btn {
  appearance: none;
  border: 0.0625rem solid var(--ha-card-border);
  cursor: pointer;
  border-radius: 0.75rem;
  padding: clamp(0.46rem, 0.36rem + 0.6vw, 0.56rem)
    clamp(0.7rem, 0.55rem + 0.8vw, 0.9rem);
  font-weight: 800;
  font-size: clamp(0.86rem, 0.8rem + 0.25vw, 0.98rem);
  transition: transform 0.14s ease, box-shadow 0.14s ease, filter 0.14s ease,
    background 0.14s ease;
}
.guide-btn.primary {
  background: var(--ha-yellow);
  color: #0f240f;
  border-color: #90ca50;
  box-shadow: 0 0 0 0.125rem #fff inset;
}
.guide-btn.primary:hover {
  transform: translateY(-0.0625rem);
  filter: brightness(1.03);
  box-shadow: 0 0.125rem 0 rgba(0, 0, 0, 0.06) inset,
    0 0.5rem 1.125rem rgba(0, 0, 0, 0.12);
}
.guide-btn.ghost {
  background: #fff;
  color: #111827;
}
.guide-btn.ghost:hover {
  transform: translateY(-0.0625rem);
  background: #fff;
  box-shadow: 0 0.5rem 1.125rem rgba(0, 0, 0, 0.1);
}
.guide-btn.skip {
  background: transparent;
  color: #4b5563;
  border-color: transparent;
}
.guide-btn.skip:hover {
  color: #1f2937;
  text-decoration: underline;
}
.guide-btn:disabled {
  opacity: 0.55;
  cursor: default;
  transform: none !important;
  box-shadow: none !important;
}

.guide-close {
  position: absolute;
  top: 0.7rem;
  right: 0.7rem;
  width: clamp(1.625rem, 4.3vw, 1.875rem);
  height: clamp(1.625rem, 4.3vw, 1.875rem);
  border-radius: 0.625rem;
  border: 0.0625rem solid var(--ha-card-border);
  background: #fff;
  color: #111827;
  box-shadow: 0 0.0625rem 0 rgba(0, 0, 0, 0.04);
  transition: transform 0.14s ease, box-shadow 0.14s ease;
}
.guide-close:hover {
  transform: translateY(-0.0625rem);
  box-shadow: 0 0.375rem 0.875rem rgba(0, 0, 0, 0.12);
}

/* hint */
</style>
