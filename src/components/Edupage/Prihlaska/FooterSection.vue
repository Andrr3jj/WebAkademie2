<template>
  <div class="form-footer">
    <button v-if="!isFirst" class="button" @click="$emit('back')">
      <p>Vrátiť sa</p>
    </button>

    <p class="form-footer-message">
      Tento formulár vás prevedie celým procesom zápisu. Zaberie vám len pár
      minút.
    </p>

    <button
      class="button"
      :disabled="!isStepValid || isSubmitted"
      @click="handleNext"
    >
      <p>
        {{ isLast ? (isSubmitted ? "Odoslané" : "Dokončiť") : "Pokračovať" }}
      </p>
    </button>
  </div>
</template>

<script>
export default {
  name: "FooterSection",
  props: {
    currentStep: { type: Number, required: true },
    totalSteps: { type: Number, required: true },
    subStep: { type: Number, default: 0 },
    role: { type: String, default: null },
    isStepValid: { type: Boolean, required: true },
    isSubmitted: { type: Boolean, default: false }, // 👈 nový prop
  },
  computed: {
    isFirst() {
      return this.currentStep === 0;
    },
    isLast() {
      return this.currentStep === this.totalSteps - 1;
    },
  },
  methods: {
    handleNext() {
      if (this.isSubmitted) return; // 👈 zablokujeme druhé kliknutie

      const isReallyLast =
        this.currentStep === this.totalSteps - 1 ||
        (this.role === "parent" &&
          this.currentStep === this.totalSteps - 2 &&
          this.subStep === 1);

      if (isReallyLast) {
        this.$emit("form-submit");
      } else {
        this.$emit("next");
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.form-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1em 1.5em;

  .form-footer-message {
    flex: 1;
    margin-top: 0.3em;
    text-align: center;
    font-size: 1.25em;
  }

  .button {
    flex-shrink: 0;
    font-size: 1.125em;
    font-family: "Adumu", sans-serif;
  }

  .button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    box-shadow: none;
  }
}

@media screen and (max-width: 900px) {
  .form-footer {
    flex-direction: column-reverse;

    .form-footer-message {
      margin-top: 1em;
    }
  }

  .mobile section p {
    font-size: 3.2vw;
  }
}
</style>
