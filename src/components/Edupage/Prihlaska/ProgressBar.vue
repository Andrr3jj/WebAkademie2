<template>
  <div class="step-wizard-container">
    <nav
      class="step-wizard"
      :class="{ 'is-animating': isAnimating }"
      :style="{ '--progressPx': progressPx + 'px' }"
      ref="barEl"
    >
      <div class="wave" aria-hidden="true"></div>
      <div class="wave2" aria-hidden="true"></div>

      <ul class="step-wizard__list">
        <template
          v-for="(step, localIdx) in visibleSteps"
          :key="startIndex + localIdx"
        >
          <li
            class="step-wizard__item"
            :ref="(el) => setItemEl(startIndex + localIdx, el)"
          >
            <div
              :class="[
                'step-wizard__badge',
                (startIndex + localIdx < currentStep ||
                  startIndex + localIdx === currentStep) &&
                  'step-wizard__badge--completed',
                startIndex + localIdx === currentStep &&
                  'step-wizard__badge--active',
              ]"
            >
              <span
                v-if="startIndex + localIdx === currentStep"
                class="step-wizard__number"
              >
                KROK {{ startIndex + localIdx + 1 }}
              </span>
              <span class="step-wizard__label">{{ step.label }}</span>
            </div>
          </li>

          <li
            v-if="
              startIndex + localIdx < steps.length - 1 &&
              localIdx < visibleSteps.length - 1
            "
            class="step-wizard__arrow"
            :class="{ 'is-current': startIndex + localIdx === currentStep }"
          >
            <img src="@/assets/images/icons/sipkyDalej.svg" alt="›" />
          </li>
        </template>
      </ul>
    </nav>
  </div>

  <component
    :is="activeComponent"
    :key="`${currentStep}-${subStep}`"
    v-model="currentData"
    :attempt="attempt"
    @selected="onRoleSelected"
    @step-valid="$emit('step-valid', $event)"
    :role="formDataLocal[0].role"
    :sub-step="subStep"
    v-bind="$attrs"
  />
</template>

<script>
import FirstStep from "./kroky/FirstStep.vue";
import SecondStep from "./kroky/SecondStep.vue";
import ThirdStep from "./kroky/ThirdStep.vue";
import FouthStep from "./kroky/FouthStep.vue";
import FifthStep from "./kroky/FifthStep.vue";

export default {
  name: "ProgressBar",
  inheritAttrs: false,
  props: {
    currentStep: { type: Number, required: true },
    subStep: { type: Number, required: true },
    formData: { type: Array, required: true },
    attempt: { type: Boolean, default: false },
  },
  data() {
    return {
      formDataLocal: JSON.parse(JSON.stringify(this.formData)),
      isAnimating: false,
      pourTimer: null,
      progressPx: 0,
      ro: null,
      windowWidth: typeof window !== "undefined" ? window.innerWidth : 1920,
      itemElMap: {},
    };
  },
  computed: {
    steps() {
      return [
        { label: "Kto sa prihlasuje", component: FirstStep },
        { label: "Informácie o žiakovi", component: SecondStep },
        { label: "Miesta výučby", component: ThirdStep },
        { label: "Forma výučby", component: FouthStep },
        { label: "Potvrdenie", component: FifthStep },
      ];
    },
    maxVisibleSteps() {
      if (this.windowWidth <= 640) return 2;
      if (this.windowWidth <= 1280) return 3;
      if (this.windowWidth <= 1350) return 4;
      return this.steps.length;
    },
    startIndex() {
      if (this.maxVisibleSteps >= this.steps.length) return 0;
      if (this.maxVisibleSteps === 2) {
        const s = this.currentStep;
        return Math.max(
          0,
          Math.min(s, this.steps.length - this.maxVisibleSteps)
        );
      } else {
        const s = this.currentStep - (this.maxVisibleSteps - 1);
        return Math.max(
          0,
          Math.min(s, this.steps.length - this.maxVisibleSteps)
        );
      }
    },
    visibleSteps() {
      return this.steps.slice(
        this.startIndex,
        this.startIndex + this.maxVisibleSteps
      );
    },
    activeComponent() {
      if (this.currentStep === 1) return SecondStep;
      return this.steps[this.currentStep].component;
    },
    currentData: {
      get() {
        const map = {
          0: () => this.formDataLocal[0].role,
          1: () => this.formDataLocal[1],
          2: () => this.formDataLocal[2],
          3: () => this.formDataLocal[3].formOfStudy,
          4: () => this.formDataLocal[4],
        };
        return map[this.currentStep]?.() ?? {};
      },
      set(value) {
        const setters = {
          0: () => (this.formDataLocal[0].role = value),
          1: () => (this.formDataLocal[1] = value),
          2: () => (this.formDataLocal[2] = value),
          3: () => (this.formDataLocal[3].formOfStudy = value),
          4: () => (this.formDataLocal[4] = value),
        };

        const oldVal = JSON.stringify(this.currentData);
        const newVal = JSON.stringify(value);
        if (oldVal === newVal) return;

        setters[this.currentStep]?.();

        this.$emit(
          "update-step",
          JSON.parse(JSON.stringify(this.formDataLocal))
        );
      },
    },
  },
  mounted() {
    this.$nextTick(() => {
      this.reobserveItems();
      this.updateProgressPx();
      window.addEventListener("resize", this.onResize, { passive: true });
    });
  },
  beforeUnmount() {
    clearTimeout(this.pourTimer);
    window.removeEventListener("resize", this.onResize);
    if (this.ro) this.ro.disconnect();
  },
  watch: {
    formData: {
      deep: true,
      handler(v) {
        this.formDataLocal = JSON.parse(JSON.stringify(v));
      },
    },
    currentStep() {
      clearTimeout(this.pourTimer);
      this.isAnimating = false;
      this.$nextTick(() => {
        this.reobserveItems();
        requestAnimationFrame(() => {
          this.updateProgressPx();
          requestAnimationFrame(() => {
            this.isAnimating = true;
            this.pourTimer = setTimeout(() => (this.isAnimating = false), 1400);
          });
        });
      });
    },
    windowWidth() {
      this.$nextTick(() => {
        this.reobserveItems();
        this.updateProgressPx();
      });
    },
    startIndex() {
      this.$nextTick(() => {
        this.reobserveItems();
        this.updateProgressPx();
      });
    },
    maxVisibleSteps() {
      this.$nextTick(() => {
        this.reobserveItems();
        this.updateProgressPx();
      });
    },
  },
  methods: {
    onRoleSelected(role) {
      this.$emit("selected-role", role);
    },
    onStepValid(val) {
      this.$emit("step-valid", val);
    },
    setItemEl(idx, el) {
      if (el) this.itemElMap[idx] = el;
      else delete this.itemElMap[idx];
    },
    updateProgressPx() {
      this.$nextTick(() => {
        requestAnimationFrame(() => {
          const bar = this.$refs.barEl;
          if (!bar) return;
          const curItem = this.itemElMap[this.currentStep];
          if (!curItem || !curItem.isConnected) return;
          const barRect = bar.getBoundingClientRect();
          const iRect = curItem.getBoundingClientRect();
          const pad = 6;
          this.progressPx = Math.min(
            Math.max(0, iRect.right - barRect.left - pad),
            barRect.width
          );
        });
      });
    },
    onResize() {
      this.windowWidth = window.innerWidth;
    },
    reobserveItems() {
      if (!window.ResizeObserver) return;
      if (this.ro) this.ro.disconnect();
      this.ro = new ResizeObserver(() => this.updateProgressPx());
      Object.values(this.itemElMap)
        .filter((el) => el && el.isConnected)
        .forEach((el) => this.ro.observe(el));
    },
  },
};
</script>

<style lang="scss" scoped>
.step-wizard-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  font-size: 90%;
}
.step-wizard {
  position: relative;
  display: flex;
  align-items: center;
  border-radius: 9999px;
  padding: 0.5em 1.5em;
  background: #fef35a;
  filter: drop-shadow(0 6px 10px rgba(0, 0, 0, 0.25));
  overflow: visible;
  width: 100%;
  max-width: 90%;
  transition: padding 0.2s ease;
}
.step-wizard::before {
  content: "";
  position: absolute;
  inset: 0;
  border-radius: inherit;
  pointer-events: none;
  z-index: 0;
}
.step-wizard::after {
  content: "";
  position: absolute;
  top: 16%;
  left: 1%;
  height: 68%;
  width: var(--progressPx, 0px);
  border-radius: inherit;
  background: #90ca50;
  transition: width 0.35s ease;
  box-shadow: inset 0 4px 6px rgba(0, 0, 0, 0.25);
  z-index: 2;
}
.step-wizard .wave,
.step-wizard .wave2 {
  position: absolute;
  top: 0;
  bottom: 0;
  left: -160px;
  width: 120px;
  opacity: 0;
  background: radial-gradient(
      60px 60px at 50% 50%,
      transparent 0 40%,
      transparent 41%
    )
    no-repeat;
  transform: rotate(12deg);
  pointer-events: none;
  z-index: 0;
  clip-path: inset(0 round 9999px);
}
.step-wizard.is-animating > .wave {
  opacity: 0.22;
  animation: pour 1.2s ease-out forwards;
}
.step-wizard.is-animating > .wave2 {
  opacity: 0.16;
  animation: pour 1.2s ease-out 0.1s forwards;
}
@keyframes pour {
  0% {
    left: -160px;
  }
  60% {
    left: calc(var(--progressPx, 0px) - 220px);
  }
  100% {
    left: calc(var(--progressPx, 0px) - 200px);
  }
}
.step-wizard__list {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.6em;
  width: 100%;
  list-style: none;
  margin: 0;
  padding: 0;
  white-space: nowrap;
}
.step-wizard__item {
  display: inline-flex;
  align-items: center;
}
.step-wizard__badge {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0.4em 1em;
  border-radius: 1.25em;
  color: rgba(0, 0, 0, 0.6);
  z-index: auto;
  background: transparent;
}
.step-wizard__badge--completed {
  z-index: auto;
  background: transparent;
}
.step-wizard__badge--active {
  color: #000;
  z-index: auto;
  background: transparent;
}
.step-wizard__number {
  position: absolute;
  top: -1.8em;
  left: 50%;
  transform: translateX(-50%);
  background: #90ca50;
  color: #000;
  font-weight: 700;
  padding: 0.4em 1.2em;
  border-radius: 12px 12px 0 0;
  font-size: 1.25em;
  white-space: nowrap;
  box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.25),
    4px 4px 10px rgba(0, 0, 0, 0.25);
}
.step-wizard__label {
  position: relative;
  z-index: 3;
  line-height: 1;
  white-space: nowrap;
  font-weight: 500;
  font-size: 1.25em;
  text-align: center;
}
.step-wizard__arrow {
  width: 1.5em;
  margin: 0 0.6em;
  opacity: 0;
  visibility: hidden;
  transition: opacity 0.25s ease;
}
.step-wizard__arrow.is-current {
  opacity: 0.85;
  visibility: visible;
}
.step-wizard__arrow img {
  width: 1.5em;
  height: auto;
  display: block;
  filter: drop-shadow(0 1px 0 rgba(255, 255, 255, 0.35));
}
@media (max-width: 1560px) {
  .step-wizard-container {
    font-size: 80%;
  }
  .step-wizard {
    max-width: 67em;
  }
}
@media (max-width: 1350px) {
  .stop-wizard-container {
    font-size: 80%;
  }
  .step-wizard {
    max-width: 63em;
  }
}
@media (max-width: 1300px) {
  .step-wizard {
    max-width: 55em;
  }
}
@media (max-width: 1024px) {
  .step-wizard {
    padding: 0.45rem 1rem;
    max-width: 40em;
  }
  .step-wizard__label {
    font-size: 1.05em;
  }
  .step-wizard__arrow {
    margin: 0 0.5em;
  }
}
@media (max-width: 820px) {
  .step-wizard {
    max-width: 34em;
  }
  .step-wizard__badge {
    padding: 0.4em 0.5em;
  }
  .step-wizard__number {
    left: 45%;
  }
}
@media (max-width: 750px) {
  .step-wizard-container {
    margin-top: 3em;
  }
  .step-wizard {
    max-width: 37em;
  }
  .step-wizard__badge {
    padding: 0.4em 1em;
  }
}
@media screen and (max-width: 640px) {
  .step-wizard-container {
    font-size: 90%;
  }
  .step-wizard {
    max-width: 32em;
  }
}

@media screen and (max-width: 500px) {
  .step-wizard {
    max-width: 28em;
  }
}

@media screen and (max-width: 400px) {
  .step-wizard-container {
    font-size: 80%;
  }
  .step-wizard {
    max-width: 26em;
  }
}
</style>
