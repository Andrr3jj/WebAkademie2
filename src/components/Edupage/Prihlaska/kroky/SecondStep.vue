<template>
  <div class="second-step">
    <h2>Takmer hotovo! Ešte pár informácií o vás.</h2>
    <p class="subtext">
      Tieto informácie sú z vášho účtu. Ak sú správne, pokračujte ďalej.
    </p>

    <div class="fields">
      <div class="column-group">
        <div class="column">
          <div class="field" :class="{ invalid: attempt && errors.name }">
            <label for="name">Meno:</label>
            <input
              id="name"
              type="text"
              v-model="form.name"
              placeholder="Zadajte meno"
              autocomplete="given-name"
            />
            <small v-if="attempt && errors.name" class="error">
              {{ errors.name }}
            </small>
          </div>

          <div class="field" :class="{ invalid: attempt && errors.surname }">
            <label for="surname">Priezvisko:</label>
            <input
              id="surname"
              type="text"
              v-model="form.surname"
              placeholder="Zadajte priezvisko"
              autocomplete="family-name"
            />
            <small v-if="attempt && errors.surname" class="error">
              {{ errors.surname }}
            </small>
          </div>

          <div
            class="field"
            :class="{ invalid: attempt && errors.dateOfBirth }"
          >
            <label for="dateOfBirth">Dátum narodenia:</label>
            <input id="dateOfBirth" type="date" v-model="form.dateOfBirth" />
            <small v-if="attempt && errors.dateOfBirth" class="error">
              {{ errors.dateOfBirth }}
            </small>
          </div>

          <div
            class="field"
            :class="{ invalid: attempt && errors.placeOfBirth }"
          >
            <label for="placeOfBirth">Miesto narodenia:</label>
            <input
              id="placeOfBirth"
              type="text"
              v-model="form.placeOfBirth"
              placeholder="Zadajte miesto narodenia"
            />
            <small v-if="attempt && errors.placeOfBirth" class="error">
              {{ errors.placeOfBirth }}
            </small>
          </div>
        </div>

        <div class="column">
          <div class="field" :class="{ invalid: attempt && errors.email }">
            <label for="email">Email:</label>
            <input
              id="email"
              type="email"
              v-model="form.email"
              placeholder="napr. priezvisko@priklad.sk"
              autocomplete="email"
            />
            <small v-if="attempt && errors.email" class="error">
              {{ errors.email }}
            </small>
          </div>

          <div class="field" :class="{ invalid: attempt && errors.phone }">
            <label for="phone">Telefónne číslo:</label>
            <input
              id="phone"
              type="tel"
              v-model="form.phone"
              @input="onPhoneInput"
              placeholder="+421 900 123 456"
              autocomplete="tel"
              pattern="^\+?\d[\d\s]{6,}$"
            />
            <small v-if="attempt && errors.phone" class="error">
              {{ errors.phone }}
            </small>
          </div>

          <div class="field" :class="{ invalid: attempt && errors.address }">
            <label for="address">Mesto bydliska:</label>
            <input
              id="address"
              type="text"
              v-model="form.address"
              placeholder="Mesto alebo obec"
              autocomplete="address-level2"
            />
            <small v-if="attempt && errors.address" class="error">
              {{ errors.address }}
            </small>
          </div>

          <div class="field" :class="{ invalid: attempt && errors.experience }">
            <label for="experience">Úroveň skúseností:</label>
            <select id="experience" v-model="form.experience">
              <option disabled value="">Vyberte zo zoznamu</option>
              <option value="beginner">Začiatočník</option>
              <option value="intermediate">Stredne pokročilý</option>
              <option value="advanced">Pokročilý</option>
            </select>
            <small v-if="attempt && errors.experience" class="error">
              {{ errors.experience }}
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "SecondStep",
  props: {
    modelValue: { type: Object, required: true },
    attempt: { type: Boolean, default: false },
    role: { type: String, default: null },
  },
  emits: ["update:modelValue", "step-valid"],
  data() {
    return {
      localForm: JSON.parse(JSON.stringify(this.modelValue || {})),
      lastValidState: null,
    };
  },
  computed: {
    user() {
      return this.$store.state.user; // údaje zo storu
    },
    form() {
      return this.localForm;
    },
    errors() {
      const f = this.localForm || {};
      const e = {};
      const req = (v) => !!String(v ?? "").trim();
      const emailOk = (v) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v);
      const phoneOk = (v) =>
        /^(\+421\s?9\d{2}\s?\d{3}\s?\d{3}|09\d{2}\s?\d{3}\s?\d{3})$/.test(
          String(v).trim()
        );

      if (!req(f.name)) e.name = "Povinné pole";
      if (!req(f.surname)) e.surname = "Povinné pole";
      if (!req(f.dateOfBirth)) e.dateOfBirth = "Vyberte dátum";
      if (!req(f.placeOfBirth)) e.placeOfBirth = "Povinné pole";
      if (!req(f.address)) e.address = "Povinné pole";
      if (!req(f.experience)) e.experience = "Vyberte úroveň";

      if (!req(f.email)) e.email = "Povinné pole";
      else if (!emailOk(f.email)) e.email = "Neplatný email";

      if (!req(f.phone)) e.phone = "Povinné pole";
      else if (!phoneOk(f.phone)) e.phone = "Neplatný formát";

      return e;
    },
    isValid() {
      return Object.keys(this.errors).length === 0;
    },
  },
  mounted() {
    this.prefillIfSelf();
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
    role: {
      immediate: true,
      handler() {
        this.prefillIfSelf();
      },
    },
    user: {
      deep: true,
      immediate: true,
      handler() {
        this.prefillIfSelf();
      },
    },
  },
  methods: {
    prefillIfSelf() {
      if (this.role === "self" && this.user && Object.keys(this.user).length) {
        console.log("Prefillujem zo storu:", this.user);
        this.localForm = {
          ...this.localForm,
          name: this.user.name || "",
          surname: this.user.surname || "",
          email: this.user.email || "",
          phone: this.user.phone || "",
          address: this.user.residence || "",
          dateOfBirth: this.user.birthDate || "",
          placeOfBirth: this.user.birthPlace || "",
          experience: this.user.experience || "",
        };
      }
    },
    emitValidIfChanged() {
      const cur = this.isValid;
      if (cur !== this.lastValidState) {
        this.lastValidState = cur;
        this.$emit("step-valid", cur);
      }
    },
    onPhoneInput(e) {
      let phone = String(e.target.value || "").replace(/\s+/g, "");
      if (/^\+4219\d{8}$/.test(phone)) {
        phone = phone.replace(/^(\+421)(9\d{2})(\d{3})(\d{3})$/, "$1 $2 $3 $4");
      } else if (/^09\d{8}$/.test(phone)) {
        phone = phone.replace(/^(09\d{2})(\d{3})(\d{3})$/, "$1 $2 $3");
      }
      this.localForm = { ...this.localForm, phone };
    },
  },
};
</script>

<style lang="scss" scoped>
.second-step {
  text-align: center;
  margin-top: 1em;
  padding: 1em;
  font-size: 90%;

  h2 {
    font-size: 2.5em;
    margin-bottom: 0.5em;
    font-family: "Bahnschrift", sans-serif;
  }

  .subtext {
    font-size: 1.75em;
    margin-bottom: 0.5em;
    color: #000;
  }
}

.fields {
  display: flex;
  justify-content: center;
}
.column-group {
  display: flex;
  gap: 2em;
  align-items: center;
}
.column {
  display: flex;
  flex-direction: column;
  gap: 1em;
}

.field {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  font-size: 0.875em;

  label {
    margin-bottom: 0.25em;
    font-size: 1em;
  }
  input,
  select {
    border-radius: 1.0625em;
    background: #90ca50;
    box-shadow: inset -0.4375em 0.3125em 0.9375em rgba(0, 0, 0, 0.25),
      0 0.25em 0.25em rgba(0, 0, 0, 0.25);
    padding: 0.7em 5%;
    width: 25em;
    box-sizing: border-box;
    border: none;
    font-size: 1em;
  }
  select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: none;
  }
}

.error {
  color: #b00020;
  margin-top: 0.25em;
  font-size: 0.9em;
}
.field.invalid input,
.field.invalid select {
  outline: 2px solid #b00020;
  outline-offset: 2px;
}

@media screen and (max-width: 1023px) {
  .column-group {
    flex-direction: column;
  }
}
@media screen and (max-width: 750px) {
  .second-step {
    h2 {
      font-size: 4.5vw !important;
      margin-block: 2em;
    }
    .subtext {
      font-size: 3.2vw !important;
    }
  }
}
</style>
