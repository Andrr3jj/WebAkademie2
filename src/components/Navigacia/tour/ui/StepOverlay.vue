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
          >Krok {{ index + 1 - firstReal }}/{{ total }}</span
        >

        <button
          class="guide-btn ghost"
          type="button"
          @click.stop="prev"
          :disabled="!hasPrev || isLocked"
        >
          Späť
        </button>

        <div
          class="guide-btn-wrap"
          :class="{ locked: isNavigationLocked }"
          :style="lockStyles"
        >
          <button
            class="guide-btn primary"
            type="button"
            @click.stop="next"
            :disabled="isLocked"
            :aria-busy="isLocked ? 'true' : 'false'"
          >
            {{ isLast ? "Dokončiť" : "Ďalej" }}
          </button>
        </div>
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
    const firstReal = computed(() =>
      steps.value[0]?.type === "intro" ? 1 : 0
    );
    const total = computed(() =>
      Math.max(0, steps.value.length - firstReal.value)
    );

    const isLast = computed(
      () => index.value - firstReal.value >= total.value - 1
    );
    const hasPrev = computed(() => index.value > firstReal.value);
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

    const waitDelay = (ms) =>
      new Promise((resolve) => setTimeout(resolve, Math.max(0, ms)));

    async function skipMissing() {
      if (typeof tour.skipCurrentStep === "function") {
        const skipped = await tour.skipCurrentStep();
        if (skipped) return;
      }
      if (index.value < steps.value.length - 1)
        await tour.next({ force: true });
      else tour.close();
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

      if (!(el instanceof Element)) {
        const MAX_TRIES = 8;
        let attempt = 0;
        while (!(el instanceof Element) && attempt < MAX_TRIES) {
          await waitDelay(120);
          await nextTick();
          el = pickTarget(s);
          attempt += 1;
        }
      }

      // 👇 kľúčová ochrana – ak nie je Element, krok preskoč a nič nepozoruj
      if (!(el instanceof Element)) {
        await skipMissing();
        return;
      }

      detachTargetObserver();
      attachTargetObserver(el);

      await nextTick();
      await waitForBox(el);

      if (scrollIntoView) {
        const scrollTarget =
          s.__scrollTarget instanceof Element ? s.__scrollTarget : el;
        const scrollBehavior =
          s.scrollBehavior && typeof s.scrollBehavior === "string"
            ? s.scrollBehavior
            : "smooth";
        const scrollBlock =
          s.scrollMode && typeof s.scrollMode === "string"
            ? s.scrollMode
            : "center";
        const scrollInline =
          s.scrollInline && typeof s.scrollInline === "string"
            ? s.scrollInline
            : scrollBlock === "nearest"
            ? "nearest"
            : "center";

        try {
          scrollTarget.scrollIntoView({
            behavior: scrollBehavior,
            block: scrollBlock,
            inline: scrollInline,
          });
        } catch (err) {
          // fallback na staršie prehliadače
          scrollTarget.scrollIntoView();
        }
        if (scrollTarget !== el) {
          await waitForBox(scrollTarget);
        }
        await waitForBox(el);
      }

      blurActive();
      if (typeof el.blur === "function") el.blur();

      const rect = el.getBoundingClientRect();
      const cs = window.getComputedStyle(el);

      const defaultPad = () => (window.innerWidth <= 768 ? 14 : 22);
      const PAD = s.pad ?? defaultPad();
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
    const isLocked = computed(
      () => tour.state.transitioning || tour.state.navigationLocked
    );
    const isNavigationLocked = computed(() => tour.state.navigationLocked);

    const lockAngle = ref(0);
    const lockDuration = ref(0);
    const lockStyles = computed(() => ({
      "--lock-angle": `${lockAngle.value.toFixed(2)}deg`,
      "--lock-duration": `${Math.max(0, Math.round(lockDuration.value))}ms`,
    }));

    let lockRaf = 0;

    function cancelLockAnimation() {
      if (lockRaf) {
        cancelAnimationFrame(lockRaf);
        lockRaf = 0;
      }
    }

    const now = () => {
      if (
        typeof performance !== "undefined" &&
        typeof performance.now === "function"
      ) {
        return performance.now();
      }
      return Date.now();
    };

    function runLockAnimation(start, duration) {
      cancelLockAnimation();
      const safeStart = Number(start) || now();
      const safeDuration = Math.max(0, Number(duration) || 0);
      if (!safeDuration) {
        lockAngle.value = 360;
        return;
      }

      lockAngle.value = 0;

      const step = () => {
        const elapsed = Math.max(0, now() - safeStart);
        const progress = Math.min(1, safeDuration ? elapsed / safeDuration : 1);
        lockAngle.value = progress * 360;
        if (progress < 1 && tour.state.navigationLocked) {
          lockRaf = requestAnimationFrame(step);
        } else {
          lockRaf = 0;
        }
      };

      lockRaf = requestAnimationFrame(step);
    }

    watch(
      () => [
        tour.state.navigationLocked,
        tour.state.navigationLockStart,
        tour.state.navigationLockDuration,
      ],
      ([locked, start, duration]) => {
        cancelLockAnimation();
        const safeDuration = Math.max(0, Number(duration) || 0);
        lockDuration.value = safeDuration;

        if (locked && safeDuration > 0) {
          runLockAnimation(start, safeDuration);
        } else if (locked) {
          lockAngle.value = 360;
        } else {
          lockAngle.value = 0;
        }
      },
      { immediate: true }
    );

    const next = () => {
      if (isLocked.value) return;
      tour.next();
    };
    const prev = () => {
      if (isLocked.value) return;
      tour.prev();
    };
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

    function toggleScrollLock(shouldLock) {
      const body = document.body;
      const html = document.documentElement;
      if (!body || !html) return;

      const className = "tour-scroll-lock";

      [body, html].forEach((node) => {
        if (shouldLock) node.classList.add(className);
        else node.classList.remove(className);
      });

      if (shouldLock) {
        const scrollTop = window.scrollY || window.pageYOffset || 0;
        body.dataset.tourScrollTop = String(scrollTop);
        body.style.top = `-${scrollTop}px`;
        body.style.left = "0";
        body.style.right = "0";
        body.style.position = "fixed";
        body.style.width = "100%";
      } else {
        const storedValue = body.dataset.tourScrollTop;
        delete body.dataset.tourScrollTop;
        body.style.top = "";
        body.style.left = "";
        body.style.right = "";
        body.style.position = "";
        body.style.width = "";
        if (storedValue !== undefined) {
          const stored = Number(storedValue) || 0;
          window.scrollTo(0, stored);
        }
      }
    }

    watch(
      open,
      (isOpen) => {
        toggleScrollLock(isOpen);
      },
      { immediate: true }
    );

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
      cancelLockAnimation();
      toggleScrollLock(false);
    });

    return {
      index,
      total,
      firstReal,
      isLast,
      hasPrev,
      step,
      spotStyle,
      tooltipStyle,
      tooltipSide,
      isLocked,
      isNavigationLocked,
      lockStyles,
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

@property --lock-angle {
  syntax: "<angle>";
  initial-value: 0deg;
  inherits: false;
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
  align-items: center;
  justify-content: flex-end;
  gap: 0.5rem;
  flex-wrap: nowrap;
}
.guide-progress {
  margin-right: auto;
  font-weight: 600;
  font-size: clamp(0.82rem, 0.78rem + 0.18vw, 0.9rem);
  color: #111827;
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
  position: relative;
  z-index: 1;
}
.guide-btn.primary {
  background: var(--ha-yellow);
  color: #0f240f;
  border-color: #90ca50;
  box-shadow: 0 0 0 0.125rem #fff inset;
  z-index: 0;
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
.guide-btn:disabled {
  opacity: 0.55;
  cursor: default;
  transform: none !important;
  box-shadow: none !important;
}

.guide-btn-wrap {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  isolation: isolate;
}
.guide-btn.primary::before {
  content: "";
  position: absolute;
  inset: -0.35rem;
  border-radius: 1.25rem;
  border: 0.1875rem solid transparent;
  pointer-events: none;
  z-index: -1;
  opacity: 0;
  transition: opacity 0.18s ease;
  background: linear-gradient(transparent, transparent) padding-box,
    conic-gradient(
        from -90deg,
        rgba(254, 243, 90, 0.85) 0deg,
        rgba(254, 243, 90, 0.85) var(--lock-angle, 0deg),
        rgba(144, 202, 80, 0.12) var(--lock-angle, 0deg),
        rgba(144, 202, 80, 0.12) 360deg
      )
      border-box;
  box-shadow: inset 0 0 0 0.09375rem rgba(144, 202, 80, 0.34),
    0 0 0 0.1875rem rgba(254, 243, 90, 0.4);
}

.guide-btn-wrap.locked .guide-btn.primary::before {
  opacity: 1;
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
@media (max-width: 720px) {
  .guide-tooltip.light {
    max-width: min(94vw, 21.5rem);
    padding: clamp(0.5rem, 3.6vw, 0.75rem) clamp(0.85rem, 5.8vw, 1.1rem)
      clamp(0.6rem, 3.4vw, 0.8rem) clamp(0.85rem, 5.8vw, 1.1rem);
    border-radius: clamp(0.75rem, 5vw, 1rem);
  }

  .guide-title {
    font-size: clamp(0.94rem, 4.6vw, 1.08rem);
  }

  .guide-text {
    font-size: clamp(0.84rem, 4.2vw, 0.96rem);
    margin-bottom: clamp(0.45rem, 3.6vw, 0.65rem);
  }

  .guide-actions {
    gap: clamp(0.35rem, 2.8vw, 0.55rem);
    flex-wrap: nowrap;
  }

  .guide-progress {
    margin-right: auto;
    font-size: clamp(0.78rem, 3.6vw, 0.9rem);
  }

  .guide-btn {
    padding: clamp(0.4rem, 3.4vw, 0.52rem) clamp(0.6rem, 4.8vw, 0.85rem);
    font-size: clamp(0.8rem, 3.8vw, 0.92rem);
  }

  .guide-close {
    top: clamp(0.45rem, 3vw, 0.6rem);
    right: clamp(0.45rem, 3vw, 0.6rem);
    width: clamp(1.4rem, 6vw, 1.7rem);
    height: clamp(1.4rem, 6vw, 1.7rem);
  }
}
.tour-scroll-lock {
  overflow: hidden !important;
  touch-action: none !important;
  overscroll-behavior: contain !important;
}
body.tour-scroll-lock {
  width: 100%;
}
</style>
