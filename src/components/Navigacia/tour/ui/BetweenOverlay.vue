<template>
  <div
    class="intro-layer"
    :class="[`st-${stage}`]"
    role="dialog"
    aria-modal="true"
  >
    <div class="intro-center">
      <div class="intro-group" :class="{ 'is-compact': isCompact }">
        <!-- AVATAR + efekty -->
        <div class="intro-avatar-shell" :class="[`st-${stage}`]">
          <div class="intro-halo" aria-hidden="true"></div>
          <img class="intro-avatar" :src="avatarUrl" alt="Sprievodca" />
          <span
            v-for="n in 8"
            :key="`sp-${n}`"
            class="sparkle"
            :style="`--i:${n}`"
            aria-hidden="true"
          ></span>
        </div>

        <!-- Bublina -->
        <section
          class="intro-bubble"
          :class="[`st-${stage}`, { 'is-compact': isCompact }]"
          :style="bubbleStyle"
          ref="bubbleEl"
        >
          <button
            class="guide-close intro-close"
            type="button"
            @click="$emit('close')"
            aria-label="Ukončiť"
          >
            ✕
          </button>
          <h2 class="intro-title">
            <template v-if="variant === 'intro'">
              Ahoj, som Andrej <span class="wave">👋</span>
            </template>
            <template v-else>
              {{ title || "Skvelé! Domovská stránka je hotová 🎉" }}
            </template>
          </h2>

          <p class="intro-lead" v-if="variant === 'intro'">
            Prevediem ťa krátkym návodom, aby ti na našej stránke nič nešlo
            mimo.
          </p>
          <p class="intro-lead" v-else-if="text">
            {{ text }}
          </p>

          <!-- plánik – ponecháme aj pre medzikrok -->
          <div class="intro-plan" v-if="planSegments.length">
            <ol class="plan-summary" :aria-label="planSummary">
              <li
                v-for="(item, idx) in planSegments"
                :key="`plan-${idx}`"
                class="plan-summary__item"
                :class="`status-${item.status}`"
                :aria-label="`${idx + 1}. ${item.label} – ${statusLabel(
                  item.status
                )}`"
              >
                <span
                  class="plan-summary__icon"
                  :class="`status-${item.status}`"
                >
                  <span
                    v-if="item.status === 'done'"
                    class="icon"
                    aria-hidden="true"
                    >✓</span
                  >
                  <span
                    v-else-if="item.status === 'current'"
                    class="icon current"
                    aria-hidden="true"
                  ></span>
                  <span v-else class="icon upcoming" aria-hidden="true"></span>
                </span>
                <div class="plan-summary__text">
                  <span class="plan-summary__title">{{ item.label }}</span>
                  <div class="plan-summary__meta">
                    <span class="plan-summary__status">{{
                      statusLabel(item.status)
                    }}</span>
                    <span v-if="item.bridgeLabel" class="plan-summary__bridge">
                      Ďalej: {{ item.bridgeLabel }}
                    </span>
                  </div>
                </div>
              </li>
            </ol>
          </div>

          <div
            class="intro-actions"
            :class="{ 'is-branch': variant !== 'intro' }"
          >
            <p v-if="planItems.length" class="plan-tip small action-tip">
              Tip: používaj šípky <span class="kbd">←</span>
              <span class="kbd">→</span> a <span class="kbd">Esc</span> pre
              zatvorenie.
            </p>
            <!-- intro: Začať -->
            <button
              v-if="variant === 'intro'"
              class="guide-btn primary"
              type="button"
              @click="start"
            >
              Začať
            </button>

            <!-- medzikrok: voľby -->
            <template v-else>
              <div class="branch-options">
                <button
                  v-for="(opt, i) in options"
                  :key="i"
                  class="guide-btn primary"
                  type="button"
                  @click="choose(opt)"
                >
                  {{ opt?.label || "Pokračovať" }}
                </button>
              </div>
            </template>
          </div>
        </section>
      </div>
    </div>
  </div>
</template>

<script>
import {
  ref,
  computed,
  onMounted,
  onBeforeUnmount,
  watch,
  nextTick,
} from "vue";

export default {
  name: "BetweenOverlay",
  props: {
    variant: { type: String, default: "intro" }, // 'intro' | 'branch'
    avatarUrl: { type: String, required: true },
    visibleTotal: { type: Number, default: 0 },
    planItems: { type: Array, default: () => [] },
    planSummary: { type: String, default: "" },
    planHint: { type: String, default: "" },
    title: { type: String, default: "" },
    text: { type: String, default: "" },
    options: { type: Array, default: () => [] }, // [{label, to|goto|steps}]
  },
  emits: ["start", "choose", "close"],
  setup(props, { emit }) {
    const stage = ref("enter"); // enter -> ready -> leave
    const bubbleEl = ref(null);
    const bubbleScale = ref(1);
    let resizeObserver = null;
    let viewportResizeListener = null;

    const ensureNextFrame = (fn) => {
      if (typeof requestAnimationFrame === "function") {
        requestAnimationFrame(fn);
      } else {
        setTimeout(fn, 16);
      }
    };

    const updateScale = () => {
      if (typeof window === "undefined") {
        bubbleScale.value = 1;
        return;
      }
      const el = bubbleEl.value;
      if (!el) {
        bubbleScale.value = 1;
        return;
      }

      const viewport = window.visualViewport || window;
      const viewportHeight = Math.max(
        0,
        Number(
          viewport?.height || viewport?.innerHeight || window.innerHeight || 0
        )
      );
      if (!viewportHeight) {
        bubbleScale.value = 1;
        return;
      }

      const margin = viewportHeight <= 720 ? 40 : 64;
      const available = Math.max(160, viewportHeight - margin);
      const bubbleHeight = el.scrollHeight || el.offsetHeight || 0;
      if (!bubbleHeight) {
        bubbleScale.value = 1;
        return;
      }

      const ratio = available / bubbleHeight;
      const clamped = Math.min(
        1,
        Math.max(0.75, Number.isFinite(ratio) ? ratio : 1)
      );
      bubbleScale.value = clamped;
    };

    const queueUpdateScale = () => {
      ensureNextFrame(() => {
        nextTick(() => updateScale());
      });
    };

    const planBridges = computed(() => {
      const items = Array.isArray(props.planItems) ? props.planItems : [];
      if (items.length < 2) return [];

      return items.slice(0, -1).map((item, idx) => {
        const next = items[idx + 1] || null;
        const rawLabel =
          (typeof item?.bridgeLabel === "string"
            ? item.bridgeLabel.trim()
            : "") || (typeof next?.label === "string" ? next.label.trim() : "");
        const prevStatus = item?.status || "upcoming";
        const nextStatus = next?.status || "upcoming";

        let status = "upcoming";
        if (prevStatus === "done" && nextStatus === "done") status = "done";
        else if (
          prevStatus === "done" ||
          prevStatus === "current" ||
          nextStatus === "current"
        )
          status = "current";

        const showLabel = rawLabel && status !== "upcoming";

        return {
          index: item?.index ?? idx,
          status,
          label: showLabel ? rawLabel : "",
          collapsed: status === "done",
        };
      });
    });

    const leave = async () => {
      stage.value = "leave";
      await new Promise((r) => setTimeout(r, 360));
    };

    const start = async () => {
      await leave();
      emit("start");
    };

    const choose = async (opt) => {
      await leave();
      emit("choose", opt || null);
    };

    const onKey = (e) => {
      if (e.key === "Escape") {
        e.preventDefault();
        emit("close");
      } else if (e.key === "ArrowRight") {
        e.preventDefault();
        if (props.variant === "intro") start();
        else if (props.options?.length) choose(props.options[0]);
      }
    };

    let restoreBodyScroll = null;

    const lockBodyScroll = () => {
      if (typeof window === "undefined") return;
      const body = document.body;
      const doc = document.documentElement;
      if (!body || !doc) return;

      const previous = {
        bodyOverflow: body.style.overflow,
        bodyTouch: body.style.touchAction,
        docOverflow: doc.style.overflow,
        docTouch: doc.style.touchAction,
      };

      restoreBodyScroll = () => {
        body.style.overflow = previous.bodyOverflow;
        body.style.touchAction = previous.bodyTouch;
        doc.style.overflow = previous.docOverflow;
        doc.style.touchAction = previous.docTouch;
      };

      body.style.overflow = "hidden";
      body.style.touchAction = "none";
      doc.style.overflow = "hidden";
      doc.style.touchAction = "none";
    };

    onMounted(() => {
      requestAnimationFrame(() => {
        stage.value = "ready";
        queueUpdateScale();
      });
      window.addEventListener("keydown", onKey);
      if (typeof window !== "undefined") {
        window.addEventListener("resize", queueUpdateScale);
        window.addEventListener("orientationchange", queueUpdateScale);
        if (window.visualViewport) {
          viewportResizeListener = () => queueUpdateScale();
          window.visualViewport.addEventListener(
            "resize",
            viewportResizeListener
          );
          window.visualViewport.addEventListener(
            "scroll",
            viewportResizeListener
          );
        }
      }

      if (typeof ResizeObserver !== "undefined" && bubbleEl.value) {
        resizeObserver = new ResizeObserver(queueUpdateScale);
        resizeObserver.observe(bubbleEl.value);
      }

      lockBodyScroll();
    });

    onBeforeUnmount(() => {
      window.removeEventListener("keydown", onKey);
      if (typeof window !== "undefined") {
        window.removeEventListener("resize", queueUpdateScale);
        window.removeEventListener("orientationchange", queueUpdateScale);
        if (window.visualViewport && viewportResizeListener) {
          window.visualViewport.removeEventListener(
            "resize",
            viewportResizeListener
          );
          window.visualViewport.removeEventListener(
            "scroll",
            viewportResizeListener
          );
        }
      }
      if (resizeObserver) {
        try {
          resizeObserver.disconnect();
        } catch (e) {
          void 0;
        }
        resizeObserver = null;
      }
      if (typeof restoreBodyScroll === "function") {
        restoreBodyScroll();
      }
    });

    const bubbleStyle = computed(() => ({
      "--bubble-scale": bubbleScale.value,
    }));

    const isCompact = computed(() => bubbleScale.value < 0.999);

    const watchedSources = [
      () => props.planItems,
      () => props.planSummary,
      () => props.options,
      () => props.text,
      () => props.title,
      () => props.variant,
      () => props.visibleTotal,
    ];

    watchedSources.forEach((source) => {
      watch(
        source,
        () => {
          queueUpdateScale();
        },
        { deep: true }
      );
    });

    const planSegments = computed(() => {
      const items = Array.isArray(props.planItems) ? props.planItems : [];
      const bridges = planBridges.value;
      return items.map((item, idx) => {
        const label =
          (typeof item?.label === "string" && item.label.trim()) ||
          `Etapa ${idx + 1}`;
        const status = item?.status || "upcoming";
        return {
          ...item,
          index: item?.index ?? idx,
          label,
          status,
          bridgeLabel: bridges[idx]?.label || "",
        };
      });
    });

    const statusLabel = (status) => {
      switch (status) {
        case "done":
          return "Hotovo";
        case "current":
          return "Práve prechádzaš";
        default:
          return "Čaká";
      }
    };

    return {
      stage,
      start,
      choose,
      planBridges,
      planSegments,
      statusLabel,
      bubbleEl,
      bubbleStyle,
      isCompact,
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
  --intro-dur: 420ms;
  --intro-out: 340ms;

  --intro-gap: clamp(0.5rem, 2.2vw, 1.375rem);
  --intro-avatar-w: clamp(15.625rem, 22vw, 20.625rem);
  --intro-bubble-w: clamp(26.25rem, 42vw, 45rem);
}

@media (max-width: 64rem) {
  :root {
    --intro-avatar-w: clamp(11.5rem, 24vw, 15.5rem);
    --intro-bubble-w: clamp(22rem, 48vw, 34rem);
  }
}

/* vrstva */
.intro-layer {
  position: fixed;
  inset: 0;
  z-index: 4100;
  background: var(--ha-backdrop);
  pointer-events: auto;
  opacity: 0;
  transition: opacity var(--intro-dur) ease;
}
.intro-layer.st-ready {
  opacity: 1;
}
.intro-layer.st-leave {
  opacity: 0;
  transition-duration: var(--intro-out);
}

.intro-center {
  position: fixed;
  inset: 0;
  display: flex;
  align-items: center;
  justify-content: center;
}
.intro-group {
  display: flex;
  align-items: center;
  gap: var(--intro-gap);
  padding: clamp(0.5rem, 1.4vw, 1rem);
  max-width: min(
    96vw,
    calc(var(--intro-avatar-w) + var(--intro-gap) + var(--intro-bubble-w))
  );
}
.intro-group.is-compact {
  gap: clamp(0.45rem, 1vw, 0.85rem);
}

/* avatar + efekty */
.intro-avatar-shell {
  position: relative;
  width: var(--intro-avatar-w);
  min-width: 12.5rem;
  transform-origin: 60% 65%;
  opacity: 0;
  transform: scale(0.78) rotate(-18deg);
  filter: drop-shadow(0 1rem 2rem rgba(0, 0, 0, 0.22));
  transition: transform var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1),
    opacity var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1);
}
.intro-avatar-shell.st-ready {
  opacity: 1;
  transform: scale(1) rotate(0);
}
.intro-avatar-shell.st-leave {
  opacity: 0;
  transform: scale(0.86) rotate(12deg);
  transition-duration: var(--intro-out);
}

.intro-avatar {
  width: 100%;
  height: auto;
  pointer-events: none;
  animation: avatarFloat 4s ease-in-out infinite;
}
@keyframes avatarFloat {
  0% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-0.375rem);
  }
  100% {
    transform: translateY(0);
  }
}

/* fixný lúč zľava šikmo hore */
.intro-halo {
  position: absolute;
  inset: -12% -6% -10% -48%;
  z-index: -1;
  border-radius: 50%;
  background: radial-gradient(
      68.75rem 32.5rem at -28% 78%,
      rgba(144, 202, 80, 0.28),
      rgba(144, 202, 80, 0.14) 45%,
      transparent 70%
    ),
    radial-gradient(
      48.75rem 22.5rem at -16% 86%,
      rgba(255, 247, 160, 0.2),
      transparent 68%
    );
  filter: blur(0.625rem);
  transform: rotate(-14deg);
}
.sparkle {
  --i: 1;
  position: absolute;
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 50%;
  background: #cfe9a5;
  box-shadow: 0 0 0.5rem 0.125rem rgba(144, 202, 80, 0.55);
  left: 50%;
  top: 88%;
  transform-origin: -2.5rem calc(-0.75rem * var(--i));
  animation: sparkleOrbit calc(2.2s + 0.12s * var(--i)) linear infinite;
  opacity: 0;
}
@keyframes sparkleOrbit {
  0% {
    transform: rotate(0) translateX(2.875rem) rotate(0);
    opacity: 0;
  }
  10% {
    opacity: 0.9;
  }
  60% {
    opacity: 0.7;
  }
  100% {
    transform: rotate(360deg) translateX(2.875rem) rotate(360deg);
    opacity: 0;
  }
}

/* bublina */
.intro-bubble {
  position: relative;
  width: var(--intro-bubble-w);
  background: var(--ha-card-bg);
  color: var(--ha-card-fg);
  border: 0.0625rem solid var(--ha-card-border);
  border-radius: 1.25rem;
  box-shadow: 0 1.75rem 4.375rem rgba(0, 0, 0, 0.22),
    0 0.625rem 1.875rem rgba(0, 0, 0, 0.12);
  padding: clamp(1.125rem, 2.4vw, 1.625rem) clamp(1.375rem, 2.8vw, 2rem);
  --maskFrom: inset(0 0 100% 0 round 1.25rem);
  --maskTo: inset(0 0 0 0 round 1.25rem);
  -webkit-clip-path: var(--maskFrom);
  clip-path: var(--maskFrom);
  opacity: 0;
  transform-origin: top center;
  transform: translateY(0.5rem) scale(var(--bubble-scale, 1));
  transition: -webkit-clip-path var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1),
    clip-path var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1),
    opacity var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1),
    transform var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1);
}
.intro-bubble.st-ready {
  -webkit-clip-path: var(--maskTo);
  clip-path: var(--maskTo);
  opacity: 1;
  transform: translateY(0) scale(var(--bubble-scale, 1));
}
.intro-bubble.st-leave {
  -webkit-clip-path: var(--maskFrom);
  clip-path: var(--maskFrom);
  opacity: 0;
  transform: translateY(0.375rem) scale(var(--bubble-scale, 1));
  transition-duration: var(--intro-out);
}
.intro-bubble.is-compact {
  --intro-bubble-w: clamp(18rem, 72vw, 32rem);
}

.intro-bubble::after {
  content: "";
  position: absolute;
  left: -0.75rem;
  top: 50%;
  width: 1.5rem;
  height: 1.5rem;
  transform: translateY(-50%) rotate(45deg);
  background: var(--ha-card-bg);
  border-left: 0.0625rem solid var(--ha-card-border);
  border-top: 0.0625rem solid var(--ha-card-border);
  box-shadow: -0.1875rem -0.1875rem 0.375rem rgba(0, 0, 0, 0.06);
}

.intro-title {
  margin: 0 0 0.5rem;
  font-size: clamp(1.4rem, 1.05rem + 1.6vw, 2.05rem);
  font-weight: 900;
}
.wave {
  display: inline-block;
  transform-origin: 70% 70%;
}
.intro-lead {
  margin: 0 0 0.75rem;
  font-size: clamp(1rem, 0.9rem + 0.45vw, 1.15rem);
  line-height: 1.55;
}

.intro-plan {
  width: 100%;
  margin: 0 auto clamp(0.9rem, 1.4vw, 1.45rem);
  padding: 0 clamp(0.4rem, 1.4vw, 1.15rem);
  box-sizing: border-box;
}
.plan-summary {
  list-style: none;
  margin: 0;
  padding: 0;
  display: flex;
  flex-direction: column;
  gap: clamp(0.55rem, 0.8vw, 0.9rem);
}
.plan-summary__item {
  display: flex;
  align-items: flex-start;
  gap: clamp(0.6rem, 1vw, 0.95rem);
  padding: clamp(0.5rem, 0.6rem + 0.4vw, 0.95rem)
    clamp(0.7rem, 0.9rem + 0.6vw, 1.5rem);
  border-radius: 1.15rem;
  background: rgba(255, 255, 255, 0.88);
  box-shadow: 0 0.85rem 2.1rem rgba(12, 24, 12, 0.08);
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.plan-summary__item.status-current {
  transform: translateY(-0.1rem);
  box-shadow: 0 1.1rem 2.4rem rgba(12, 24, 12, 0.12);
  background: rgba(255, 255, 255, 0.94);
}
.plan-summary__item.status-done {
  background: rgba(144, 202, 80, 0.18);
}
.plan-summary__icon {
  flex: 0 0 clamp(2.1rem, 2.6vw, 2.45rem);
  width: clamp(2.1rem, 2.6vw, 2.45rem);
  height: clamp(2.1rem, 2.6vw, 2.45rem);
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: clamp(1rem, 0.9rem + 0.4vw, 1.35rem);
  color: #0f240f;
  background: #e6ecf2;
  box-shadow: inset 0 0 0 0.125rem rgba(15, 36, 15, 0.12);
}
.plan-summary__icon.status-done {
  background: var(--ha-yellow);
  box-shadow: inset 0 0 0 0.125rem rgba(15, 36, 15, 0.18);
}
.plan-summary__icon.status-current {
  background: #fff;
  box-shadow: inset 0 0 0 0.1875rem var(--ha-yellow),
    0 0 0 0.0625rem rgba(15, 36, 15, 0.08);
}
.plan-summary__icon .icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
}
.plan-summary__icon .icon.current {
  width: clamp(0.85rem, 0.9rem + 0.2vw, 1.05rem);
  height: clamp(0.85rem, 0.9rem + 0.2vw, 1.05rem);
  border-radius: 50%;
  background: var(--ha-yellow);
  box-shadow: 0 0 0 0.1875rem rgba(144, 202, 80, 0.32);
}
.plan-summary__icon .icon.upcoming {
  width: clamp(0.55rem, 0.6rem + 0.2vw, 0.75rem);
  height: clamp(0.55rem, 0.6rem + 0.2vw, 0.75rem);
  border-radius: 50%;
  background: rgba(15, 36, 15, 0.25);
}
.plan-summary__text {
  flex: 1 1 auto;
  display: flex;
  flex-direction: column;
  gap: clamp(0.25rem, 0.35vw, 0.35rem);
}
.plan-summary__title {
  font-size: clamp(1.02rem, 0.98rem + 0.3vw, 1.28rem);
  font-weight: 800;
  color: var(--ha-card-fg);
  line-height: 1.35;
}
.plan-summary__meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: clamp(0.35rem, 0.45vw, 0.55rem);
  font-size: clamp(0.85rem, 0.82rem + 0.2vw, 0.98rem);
  font-weight: 600;
  color: rgba(15, 36, 15, 0.72);
}
.plan-summary__status {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.2rem 0.65rem;
  border-radius: 999rem;
  background: rgba(15, 36, 15, 0.08);
}
.plan-summary__item.status-done .plan-summary__status {
  background: rgba(144, 202, 80, 0.28);
  color: #0f240f;
}
.plan-summary__item.status-current .plan-summary__status {
  background: rgba(144, 202, 80, 0.22);
  color: #0f240f;
  background: rgba(144, 202, 80, 0.22);
  box-shadow: inset 0 0 0 0.0625rem rgba(144, 202, 80, 0.45),
    0 0.25rem 0.7rem rgba(16, 64, 16, 0.08);
}
.plan-summary__bridge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.2rem 0.65rem;
  border-radius: 999rem;
  background: rgba(255, 255, 255, 0.75);
  box-shadow: inset 0 0 0 0.0625rem rgba(15, 36, 15, 0.12);
}
.plan-summary__item.status-current .plan-summary__bridge {
  background: rgba(144, 202, 80, 0.18);
  box-shadow: inset 0 0 0 0.0625rem rgba(15, 36, 15, 0.12);
}

.plan-tip.small {
  opacity: 0.8;
  font-size: clamp(0.9rem, 0.85rem + 0.2vw, 0.95rem);
}
.kbd {
  font-family: ui-monospace, Menlo, Consolas, monospace;
  font-weight: 700;
  font-size: 0.9rem;
  padding: 0.1rem 0.45rem;
  border: 0.0625rem solid #e6ecf2;
  border-radius: 0.5rem;
  background: #f9fafb;
}

.intro-actions {
  margin-top: 0.75rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.625rem;
  justify-content: flex-end;
  align-items: center;
}
.intro-actions.is-branch {
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}
.intro-actions .action-tip {
  margin-right: auto;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.intro-actions.is-branch .action-tip {
  margin-right: 0;
  justify-content: center;
}
.branch-options {
  display: flex;
  flex-wrap: wrap;
  gap: 0.625rem;
  justify-content: center;
  width: 100%;
}
.branch-options .guide-btn {
  min-width: clamp(8.75rem, 20vw, 13.75rem);
}
.guide-btn {
  appearance: none;
  border: 0.0625rem solid var(--ha-card-border);
  border-radius: 0.75rem;
  cursor: pointer;
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

.intro-close {
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
.intro-close:hover {
  transform: translateY(-0.0625rem);
  box-shadow: 0 0.375rem 0.875rem rgba(0, 0, 0, 0.12);
}

@media (max-width: 56.25rem) {
  .intro-center {
    align-items: flex-end;
    padding: clamp(1rem, 4vw, 2.5rem) 0;
  }

  .intro-group {
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    gap: clamp(0.75rem, 3vw, 1.75rem);
    max-width: min(92vw, 40rem);
  }
  .intro-group.is-compact {
    gap: clamp(0.55rem, 2.4vw, 1.15rem);
  }

  .intro-bubble {
    order: 1;
    width: min(92vw, 40rem);
    text-align: center;
  }

  .intro-bubble::after {
    top: auto;
    bottom: -0.625rem;
    left: calc(50% - 0.625rem);
    transform: rotate(45deg);
    box-shadow: 0.1875rem 0.1875rem 0.375rem rgba(0, 0, 0, 0.06);
  }

  .intro-avatar-shell {
    order: 2;
    width: min(60vw, 16.5rem);
    min-width: 0;
    transform-origin: 50% 75%;
  }
}

@media (max-width: 40rem) {
  .intro-layer {
    overflow: hidden;
    padding: clamp(0.85rem, 5vw, 1.6rem) clamp(0.75rem, 6vw, 1.5rem);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .intro-center {
    position: static;
    inset: auto;
    min-height: auto;
    align-items: center;
    justify-content: center;
    padding: 0;
  }

  .intro-group {
    width: 100%;
    max-width: min(100%, 26rem);
    padding: 0;
    gap: clamp(0.65rem, 4.6vw, 1.35rem);
  }

  .intro-avatar-shell {
    width: clamp(8.75rem, 40vw, 11.5rem);
    margin: 0 auto;
  }

  .intro-bubble {
    width: 100%;
    max-width: min(94vw, 24rem);
    text-align: left;
    padding: clamp(0.85rem, 5.2vw, 1.2rem) clamp(0.85rem, 5.8vw, 1.3rem);
    border-radius: 0.9rem;
    max-height: none;
    overflow: visible;
  }
  .intro-bubble.is-compact {
    padding: clamp(0.75rem, 4.6vw, 1rem) clamp(0.75rem, 4.8vw, 1.1rem);
  }

  .intro-bubble::after {
    display: none;
  }

  .intro-title {
    font-size: clamp(1.2rem, 5.6vw, 1.5rem);
  }

  .intro-lead {
    font-size: clamp(0.9rem, 4.6vw, 1.03rem);
  }

  .intro-plan {
    margin-bottom: clamp(0.6rem, 3.8vw, 1rem);
    padding: 0 clamp(0.35rem, 4.2vw, 0.85rem);
  }

  .plan-summary {
    gap: clamp(0.45rem, 3.4vw, 0.8rem);
  }

  .plan-summary__item {
    padding: clamp(0.5rem, 3.8vw, 0.8rem) clamp(0.55rem, 4.8vw, 0.95rem);
  }
  .intro-group.is-compact .plan-summary__item {
    padding: clamp(0.45rem, 3.2vw, 0.7rem) clamp(0.5rem, 4vw, 0.85rem);
  }

  .plan-summary__title {
    font-size: clamp(0.94rem, 4.4vw, 1.12rem);
  }
  .intro-group.is-compact .plan-summary__title {
    font-size: clamp(0.88rem, 4vw, 1.04rem);
  }

  .plan-summary__meta {
    font-size: clamp(0.78rem, 4vw, 0.9rem);
  }
  .intro-group.is-compact .plan-summary__meta {
    font-size: clamp(0.74rem, 3.8vw, 0.86rem);
  }

  .intro-actions {
    flex-direction: column;
    align-items: stretch;
    gap: clamp(0.45rem, 3.6vw, 0.7rem);
    text-align: left;
  }
  .intro-group.is-compact .intro-actions {
    gap: clamp(0.35rem, 3vw, 0.6rem);
  }

  .intro-actions .action-tip {
    margin-right: 0;
    justify-content: flex-start;
  }

  .intro-actions.is-branch {
    align-items: stretch;
    text-align: left;
  }

  .branch-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(10rem, 1fr));
    gap: clamp(0.45rem, 3.6vw, 0.7rem);
  }

  .branch-options .guide-btn {
    min-width: 0;
    width: 100%;
  }

  .guide-btn.primary,
  .guide-btn.ghost,
  .guide-btn.skip {
    width: 100%;
    justify-content: center;
  }

  .intro-close {
    top: clamp(0.45rem, 3.4vw, 0.65rem);
    right: clamp(0.45rem, 3.4vw, 0.65rem);
    width: clamp(1.45rem, 6.6vw, 1.75rem);
    height: clamp(1.45rem, 6.6vw, 1.75rem);
  }
}
</style>
