<template>
  <div
    class="intro-layer"
    :class="[`st-${stage}`]"
    role="dialog"
    aria-modal="true"
  >
    <div class="intro-center">
      <div class="intro-group">
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
        <section class="intro-bubble" :class="[`st-${stage}`]">
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
          <div class="intro-plan" v-if="planItems.length">
            <div class="plan-track" :aria-label="planSummary">
              <template v-for="(item, idx) in planItems" :key="`dot-${idx}`">
                <span
                  class="plan-dot"
                  :class="`status-${item.status}`"
                  :aria-label="`${idx + 1}. ${item.label}`"
                ></span>
                <span
                  v-if="idx < planBridges.length"
                  :key="`connector-${idx}`"
                  class="plan-connector"
                  :class="{
                    'status-done': planBridges[idx].status === 'done',
                    'status-current': planBridges[idx].status === 'current',
                  }"
                  aria-hidden="true"
                ></span>
              </template>
            </div>
            <div class="plan-labels">
              <template v-for="(item, idx) in planItems" :key="`label-${idx}`">
                <span class="plan-label-spacer" aria-hidden="true"></span>
                <span
                  v-if="idx < planBridges.length"
                  class="plan-label"
                  :class="`status-${planBridges[idx].status}`"
                >
                  {{ planBridges[idx].label || "\u00A0" }}
                </span>
              </template>
            </div>
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
import { ref, computed, onMounted, onBeforeUnmount } from "vue";

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

    const planBridges = computed(() => {
      const items = Array.isArray(props.planItems) ? props.planItems : [];
      if (items.length < 2) return [];

      return items.slice(0, -1).map((item, idx) => {
        const rawLabel =
          typeof item?.label === "string" ? item.label.trim() : "";
        const status = item?.status || "upcoming";
        const showLabel = rawLabel && status !== "upcoming";

        return {
          index: item?.index ?? idx,
          status,
          label: showLabel ? rawLabel : "",
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

    onMounted(() => {
      requestAnimationFrame(() => (stage.value = "ready"));
      window.addEventListener("keydown", onKey);
    });
    onBeforeUnmount(() => window.removeEventListener("keydown", onKey));

    return { stage, start, choose, planBridges };
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
  transform: translateY(0.5rem);
  transition: -webkit-clip-path var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1),
    clip-path var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1),
    opacity var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1),
    transform var(--intro-dur) cubic-bezier(0.2, 0.8, 0.2, 1);
}
.intro-bubble.st-ready {
  -webkit-clip-path: var(--maskTo);
  clip-path: var(--maskTo);
  opacity: 1;
  transform: translateY(0);
}
.intro-bubble.st-leave {
  -webkit-clip-path: var(--maskFrom);
  clip-path: var(--maskFrom);
  opacity: 0;
  transform: translateY(0.375rem);
  transition-duration: var(--intro-out);
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
  display: grid;
  gap: clamp(0.375rem, 0.6vw, 0.875rem);
  width: 100%;
  margin-bottom: clamp(0.75rem, 1.4vw, 1.25rem);
  padding: 0 clamp(0.5rem, 1.6vw, 1.25rem);
  box-sizing: border-box;
  overflow: hidden;
}
.plan-track {
  --plan-gap: clamp(0.35rem, 0.9vw, 0.75rem);
  --plan-dot-size: clamp(0.875rem, 1.8vw, 1.05rem);
  display: flex;
  align-items: center;
  width: 100%;
  min-width: 0;
  gap: var(--plan-gap);
  padding-inline: clamp(0.25rem, 1vw, 0.75rem);
  box-sizing: border-box;
}
.plan-labels {
  --plan-gap: clamp(0.35rem, 0.9vw, 0.75rem);
  --plan-dot-size: clamp(0.875rem, 1.8vw, 1.05rem);
  display: flex;
  align-items: flex-start;
  width: 100%;
  min-width: 0;
  gap: var(--plan-gap);
  padding-inline: clamp(0.25rem, 1vw, 0.75rem);
  box-sizing: border-box;
}
.plan-label-spacer {
  flex: 0 0 var(--plan-dot-size);
  height: 0;
}
.plan-dot {
  flex: 0 0 var(--plan-dot-size);
  width: var(--plan-dot-size);
  height: var(--plan-dot-size);
  border-radius: 50%;
  background: #d7e1ea;
  box-shadow: 0 0 0 0.125rem #fff inset;
  transition: background 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
}
.plan-dot.status-done {
  background: var(--ha-yellow);
}
.plan-dot.status-current {
  background: #fff;
  box-shadow: 0 0 0 0.1875rem var(--ha-yellow) inset, 0 0 0 0.125rem #fff;
  transform: scale(1.08);
}
.plan-connector {
  flex: 1 1 clamp(2.5rem, 8vw, 8.5rem);
  min-width: clamp(2.25rem, 6.5vw, 6.5rem);
  width: 100%;
  height: 0.325rem;
  border-radius: 999rem;
  background: #d7e1ea;
  transition: background 0.25s ease;
}
.plan-connector.status-done {
  background: var(--ha-yellow);
}
.plan-connector.status-current {
  background: linear-gradient(90deg, var(--ha-yellow) 0%, #d7e1ea 100%);
}
.plan-label {
  flex: 1 1 clamp(3rem, 10vw, 10rem);
  max-width: clamp(4.25rem, 20vw, 12.5rem);
  text-align: center;
  font-size: clamp(0.78rem, 0.72rem + 0.2vw, 0.92rem);
  font-weight: 700;
  letter-spacing: 0.01em;
  line-height: 1.24;
  color: var(--ha-card-fg);
  display: inline-flex;
  align-items: flex-start;
  justify-content: center;
  min-height: 1.24em;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.plan-label.status-done {
  color: #0f240f;
}
.plan-label.status-current {
  color: var(--ha-card-fg);
  opacity: 0.88;
}
.plan-label.status-upcoming {
  color: var(--ha-card-fg);
  opacity: 0.65;
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
  .intro-avatar-shell {
    display: none;
  }
  .intro-bubble {
    width: min(92vw, 40rem);
  }
  .intro-bubble::after {
    left: calc(50% - 0.625rem);
    top: -0.625rem;
    transform: rotate(45deg);
    box-shadow: 0.1875rem 0.1875rem 0.375rem rgba(0, 0, 0, 0.06);
  }
}
</style>
