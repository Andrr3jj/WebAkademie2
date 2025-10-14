<template>
  <div class="first-step">
    <h2>V tomto kroku je potrebné vybrať z možností</h2>
    <div class="first-step-container">
      <p class="first-step-question">Kto sa prihlasuje na kurz heligónky</p>

      <div class="first-step-choices">
        <label class="radioOption">
          <input
            type="radio"
            name="participant"
            value="parent"
            v-model="model"
          />
          <span class="radioCircle"></span>
          <p>Som rodič a prihlasujem svoje dieťa</p>
        </label>

        <label class="radioOption">
          <input type="radio" name="participant" value="self" v-model="model" />
          <span class="radioCircle"></span>
          <p>Som dospelý a prihlasujem seba</p>
        </label>
      </div>

      <p v-if="attempt && !isValid" class="error">Vyberte jednu z možností.</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "FirstStep",
  props: {
    modelValue: { type: String, default: "" },
    attempt: { type: Boolean, default: false },
  },
  emits: ["update:modelValue", "step-valid"],
  computed: {
    model: {
      get() {
        return this.modelValue;
      },
      set(val) {
        this.$emit("update:modelValue", val);
      },
    },
    isValid() {
      return !!this.model;
    },
  },
  mounted() {
    this.$emit("step-valid", this.isValid);
  },
  watch: {
    model(val) {
      this.$emit("step-valid", !!val);
    },
    attempt() {
      this.$emit("step-valid", this.isValid);
    },
  },
};
</script>

<style lang="scss" scoped>
.first-step {
  text-align: center;
  h2 {
    font-size: 2.5em;
    margin-bottom: 1em;
    font-family: "Bahnschrift", sans-serif;
  }
}
.first-step-container {
  display: flex;
  flex-direction: column;
}
.first-step-question {
  font-size: 2em;
  margin-bottom: 0.5em;
}
.first-step-choices {
  display: flex;
  justify-content: center;
  gap: 2em;
}

.radioOption {
  display: flex;
  align-items: center;
  cursor: pointer;
  font-size: 1em;
  position: relative;
  padding: 0.5em 1em;

  input[type="radio"] {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    margin: 0;
    cursor: pointer;
  }

  p {
    font-size: 1.5em;
    margin: 0;
  }

  .radioCircle {
    width: 1.5em;
    height: 1.5em;
    background-color: #fff;
    border: 1px solid #000;
    border-radius: 50%;
    box-shadow: inset 0 0 0 0.2em #8ec84e;
    transition: 0.2s;
    margin-right: 0.5em;
    flex-shrink: 0;
  }

  input[type="radio"]:checked + .radioCircle {
    background-color: #fef35a;
    box-shadow: inset 0 0 0 0.2em #8ec84e;
  }
}

.error {
  color: #b00020;
  margin-top: 0.75em;
  font-size: 1.1em;
}

@media screen and (max-width: 820px) {
  .first-step-choices {
    flex-direction: column;
    align-items: center;
  }
}
@media screen and (max-width: 750px) {
  .first-step {
    h2 {
      font-size: 4.5vw !important;
      margin-block: 2em;
    }
  }
  .first-step-question {
    font-size: 3.2vw !important;
    margin-bottom: 2em;
  }
  .radioOption p {
    font-size: 3.2vw !important;
  }
}
</style>
