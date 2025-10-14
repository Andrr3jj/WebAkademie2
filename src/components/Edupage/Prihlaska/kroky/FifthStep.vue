<template>
  <div class="fifth-step">
    <template v-if="submitted">
      <div class="done">
        <h2>Ďakujeme, prihláška bola odoslaná ✅</h2>
        <p class="lead">
          Poslali sme vám potvrdzovací e-mail s detailmi. Ak by nedorazil,
          skontrolujte spam alebo nás kontaktujte.
        </p>
        <div v-if="submitError" class="warn">
          {{ submitError }}
        </div>
        <div class="next-signup">
          <p>Chcete prihlásiť ďalšieho žiaka alebo seba?</p>
          <button class="button" @click="$emit('restart')">
            Prihlásiť ďalšieho
          </button>
        </div>
      </div>
    </template>

    <template v-else>
      <div class="two-columns">
        <div class="first-column">
          <h2>Skoro hotovo!</h2>
          <p class="lead">
            Potvrďte prosím zmluvné podmienky, zvoľte preferovaný čas výučby a
            ak nám chcete niečo doplniť, nechajte nám krátku poznámku. Po
            odoslaní vám prihlášku potvrdíme e-mailom. 🎵
          </p>
        </div>

        <div class="second-column">
          <div class="field">
            <label for="note">Poznámka k prihláške (voliteľné)</label>
            <textarea
              id="note"
              v-model="form.note"
              rows="4"
              placeholder="Napíšte poznámku (ak chcete)"
            ></textarea>
          </div>

          <div class="field" :class="{ invalid: attempt && errors.lessonTime }">
            <label for="lessonTime">Čas ktorý vám vyhovuje</label>
            <select id="lessonTime" v-model="form.lessonTime">
              <option value="">Vyberte čas</option>
              <option
                v-for="time in timeOptions"
                :key="time.value"
                :value="time.value"
              >
                {{ time.label }}
              </option>
            </select>
            <small v-if="attempt && errors.lessonTime" class="error">
              {{ errors.lessonTime }}
            </small>
          </div>

          <div
            class="field terms"
            :class="{ invalid: attempt && errors.termsAccepted }"
          >
            <label for="terms">Zmluvné podmienky</label>
            <p class="terms-file">
              <a :href="termsUrl" target="_blank" rel="noopener"
                >Zmluvné podmienky (PDF)</a
              >
            </p>

            <label class="checkboxOption">
              <input id="terms" type="checkbox" v-model="form.termsAccepted" />
              <span class="checkboxCircle" aria-hidden="true"></span>
              <span class="text"
                >Súhlasím so zmluvnými podmienkami Heligónkovej Akadémie.</span
              >
            </label>
            <small v-if="attempt && errors.termsAccepted" class="error">
              {{ errors.termsAccepted }}
            </small>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script>
export default {
  name: "FifthStep",
  props: {
    modelValue: { type: Object, default: () => ({}) },
    attempt: { type: Boolean, default: false },
    submitted: { type: Boolean, default: false },
    submitError: { type: String, default: "" },
  },
  emits: ["update:modelValue", "step-valid", "restart"],
  data() {
    return {
      termsUrl:
        "https://heligonkovaakademia.sk/data/documents/zmluvne-podmienky-prihlaska.pdf",
      localForm: JSON.parse(JSON.stringify(this.modelValue || {})),
      lastValidState: null,
    };
  },
  computed: {
    // template používa `form.*`
    form() {
      return this.localForm;
    },
    timeOptions() {
      const out = [];
      let h = 13,
        m = 0;
      while (h < 20 || (h === 20 && m === 0)) {
        const hh = String(h).padStart(2, "0");
        const mm = String(m).padStart(2, "0");
        out.push({ value: `${hh}:${mm}`, label: `${hh}:${mm}` });
        m += 30;
        if (m >= 60) {
          m = 0;
          h++;
        }
      }
      return out;
    },
    errors() {
      const f = this.localForm || {};
      const e = {};
      if (!f.lessonTime) e.lessonTime = "Vyberte čas výučby.";
      if (!f.termsAccepted)
        e.termsAccepted = "Musíte súhlasiť so zmluvnými podmienkami.";
      return e;
    },
    isValid() {
      return Object.keys(this.errors).length === 0;
    },
  },
  mounted() {
    this.emitValidIfChanged();
  },
  watch: {
    modelValue: {
      deep: true,
      immediate: true,
      handler(nv) {
        const a = JSON.stringify(nv || {});
        const b = JSON.stringify(this.localForm || {});
        if (a !== b) {
          this.localForm = JSON.parse(JSON.stringify(nv || {}));
        }
      },
    },
    localForm: {
      deep: true,
      handler(nv) {
        const payload = JSON.parse(JSON.stringify(nv || {}));
        const a = JSON.stringify(payload);
        const b = JSON.stringify(this.modelValue || {});
        if (a !== b) {
          this.$emit("update:modelValue", payload);
        }
        this.emitValidIfChanged();
      },
    },
    attempt: {
      immediate: true,
      handler() {
        this.emitValidIfChanged();
      },
    },
  },
  methods: {
    emitValidIfChanged() {
      const cur = this.isValid;
      if (cur !== this.lastValidState) {
        this.lastValidState = cur;
        this.$emit("step-valid", cur);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.fifth-step {
  padding: 2em;
  color: #222;
  line-height: 1.6;
}

.two-columns {
  display: flex;
  gap: 5em;
  align-items: center;
  justify-content: center;
}

.first-column,
.second-column {
  width: 100%;
}

.first-column {
  max-width: 30.5em;
}
.second-column {
  max-width: 32em;
}

h2 {
  font-size: 2.5em;
  font-family: "Bahnchrift", sans-serif;
  font-weight: bold;
}
.lead {
  font-size: 1.5em;
}

.field {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  font-size: 0.95em;
  margin-top: 1.2em;

  label {
    margin-bottom: 0.4em;
    font-size: 1.5em;
    font-weight: 600;
  }

  textarea,
  select {
    width: 100%;
    max-width: 28em;
    border-radius: 1.0625em;
    background: #90ca50;
    color: #000;
    box-shadow: inset -0.4375em 0.3125em 0.9375em rgba(0, 0, 0, 0.25),
      0 0.25em 0.25em rgba(0, 0, 0, 0.18);
    padding: 0.7em 1em;
    box-sizing: border-box;
    border: none;
    font-size: 1em;
    appearance: none;
  }

  select {
    width: 30%;
  }
  textarea {
    min-height: 5em;
  }
}

.error {
  color: #b00020;
  margin-top: 0.4em;
  font-size: 0.95em;
}

.field.invalid select,
.field.invalid .checkboxCircle {
  outline: 2px solid #b00020;
  outline-offset: 2px;
}

.terms .terms-file {
  margin-bottom: 1em;
}
.terms .terms-file a {
  text-decoration: underline;
  color: rgba(0, 0, 0, 0.5);
}

.checkboxOption {
  display: inline-flex;
  align-items: center;
  gap: 0.6em;
  cursor: pointer;
  user-select: none;
  position: relative;
  font-size: 0.957em !important;

  input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 1.4em;
    height: 1.4em;
    margin: 0;
  }

  .checkboxCircle {
    width: 1.4em;
    height: 1.4em;
    background-color: #fff;
    border: 0.09em solid #000;
    border-radius: 50%;
    box-shadow: inset 0 0 0 0.22em #8ec84e;
    transition: background-color 0.2s, box-shadow 0.2s;
    display: inline-block;
  }

  input[type="checkbox"]:checked + .checkboxCircle {
    background-color: #fef35a;
    box-shadow: inset 0 0 0 0.22em #8ec84e;
  }
}

.done {
  max-width: 46em;
  margin: 0 auto;
  text-align: center;
}
.next-signup {
  margin-top: 1.2em;
  display: flex;
  flex-direction: column;
  gap: 0.8em;
  align-items: center;
}
.button {
  display: inline-flex;
  align-items: center;
  gap: 0.6em;
  border: 0;
  border-radius: 999px;
  padding: 0.6em 1.1em;
  font-weight: 700;
  font-size: 1.1em;
  cursor: pointer;
  color: #000;
  background: #fef35a;
  box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
}
.warn {
  margin-top: 0.8em;
  padding: 0.8em 1em;
  border-radius: 12px;
  background: #fff7f7;
  color: #b00020;
  border: 1px solid #ffd6d6;
}

@media screen and (max-width: 1024px) {
  .two-columns {
    flex-direction: column;
  }
  .second-column {
    max-width: 27em;
  }
}

@media screen and (max-width: 750px) {
  h2 {
    font-size: 4.7vw !important;
  }
  .lead {
    font-size: 3.2vw !important;
  }
  .second-column {
    font-size: 90%;
  }
}
</style>
