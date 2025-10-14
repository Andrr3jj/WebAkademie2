<template>
  <div class="third-step">
    <h2>Naše miesta výučby</h2>
    <p>Vyberte miesto, kde by ste si želali dochádzať na prezenčné hodiny.</p>
    <p>
      Tento výber nám pomôže zaradiť váše dieťa do najvhodnejšej skupiny podľa
      lokality.
    </p>

    <div class="three-column">
      <div class="left-column">
        <img src="@/assets/images/gallery/mapa.png" alt="Mapa" />
      </div>

      <div class="middle-column">
        <label
          v-for="(miesto, index) in leftColumn"
          :key="'L' + index"
          class="radioOption"
        >
          <input
            type="radio"
            name="vyucba"
            :value="miesto.nazov"
            v-model="model"
          />
          <span class="radioCircle"></span>
          <div class="radioText">
            <strong>{{ miesto.nazov }}</strong>
            <div class="podtext">{{ miesto.popis }}</div>
          </div>
        </label>
      </div>

      <div class="right-column">
        <label
          v-for="(miesto, index) in rightColumn"
          :key="'R' + index"
          class="radioOption"
        >
          <input
            type="radio"
            name="vyucba"
            :value="miesto.nazov"
            v-model="model"
          />
          <span class="radioCircle"></span>
          <div class="radioText">
            <strong>{{ miesto.nazov }}</strong>
            <div class="podtext">{{ miesto.popis }}</div>
          </div>
        </label>
      </div>
    </div>

    <p v-if="attempt && !isValid" class="error">
      Vyberte prosím miesto výučby.
    </p>
  </div>
</template>

<script>
export default {
  name: "ThirdStep",
  props: {
    modelValue: { type: Object, default: () => ({ placeOfStudy: "" }) },
    attempt: { type: Boolean, default: false },
  },
  emits: ["update:modelValue", "step-valid"],
  computed: {
    model: {
      get() {
        return this.modelValue?.placeOfStudy || "";
      },
      set(val) {
        this.$emit("update:modelValue", {
          ...this.modelValue,
          placeOfStudy: val,
        });
      },
    },
    isValid() {
      return !!String(this.model).trim();
    },
    leftColumn() {
      return this.miesta.slice(0, Math.ceil(this.miesta.length / 2));
    },
    rightColumn() {
      return this.miesta.slice(Math.ceil(this.miesta.length / 2));
    },
  },
  watch: {
    attempt: {
      immediate: true,
      handler() {
        this.$emit("step-valid", this.isValid);
      },
    },
    model() {
      this.$emit("step-valid", this.isValid);
    },
  },
  data() {
    return {
      miesta: [
        { nazov: "Hlboké nad Váhom", popis: "Obecný úrad" },
        { nazov: "Kamenná Poruba", popis: "Základná škola" },
        { nazov: "Lietavská Svinná", popis: "Základná škola" },
        { nazov: "Zákamenné (Orava)", popis: "Základná škola" },
        { nazov: "Varín", popis: "Obecná knižnica" },
        { nazov: "Skalité", popis: "Stavebniny REDOS" },
        { nazov: "Rajec", popis: "Cirkevná škola" },
        { nazov: "Nededza", popis: "" },
        { nazov: "Belá", popis: "" },
        { nazov: "Online forma výučby", popis: "Prostredníctvom videohovoru" },
      ],
    };
  },
};
</script>

<style lang="scss" scoped>
.third-step {
  text-align: center;
  margin-top: 1em;
  padding: 1em;
}
h2 {
  font-size: 2.5em;
  margin-bottom: 0.5em;
  font-family: "Bahnschrift", sans-serif;
}
p {
  font-size: 1.25em;
  color: #000;
}

.radioOption {
  display: flex;
  align-items: flex-start;
  cursor: pointer;
  font-size: 1em;
  position: relative;
  margin-bottom: 1em;
}
.radioOption input[type="radio"] {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  opacity: 0;
  cursor: pointer;
  margin: 0;
}
.radioCircle {
  width: 1.5em;
  height: 1.5em;
  background-color: #fff;
  border: 1px solid #000;
  border-radius: 50%;
  box-shadow: inset 0 0 0 0.2em #8ec84e;
  transition: background-color 0.2s, box-shadow 0.2s;
  margin-right: 0.75em;
  margin-top: 0.25em;
  flex-shrink: 0;
}
input[type="radio"]:checked + .radioCircle {
  background-color: #fef35a;
  box-shadow: inset 0 0 0 0.2em #8ec84e;
}

.radioText {
  text-align: left;
}
.radioText strong {
  font-size: 0.9375em;
  font-weight: 600;
}
.radioText .podtext {
  font-size: 0.75em;
  color: #000;
  text-align: left;
  opacity: 50%;
}

.three-column {
  display: flex;
  justify-content: center;
  margin-top: 1em;
  gap: 3em;
  align-items: center;
}
.left-column img {
  max-width: 100%;
  width: 15em;
  border-radius: 1em;
}
.middle-column,
.right-column {
  display: flex;
  flex-direction: column;
  padding: 0 1em;
}

.error {
  color: #b00020;
  margin-top: 0.75em;
}

@media screen and (max-width: 999px) {
  .three-column {
    flex-direction: column;
  }
  .right-column {
    margin-left: 2em;
  }
}

@media screen and (max-width: 750px) {
  .third-step {
    h2 {
      font-size: 4.5vw !important;
      margin-block: 2em;
    }
  }
  .mobile section p {
    font-size: 3.2vw !important;
  }
  .three-column {
    margin-top: 2em;
  }
}
</style>
