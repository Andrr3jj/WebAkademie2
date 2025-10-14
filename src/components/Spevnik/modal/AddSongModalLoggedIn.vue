<template>
  <div>
    <h2>Pridaj text piesne do zoznamu</h2>

    <div class="modal-desc">
      <p>Poznáš skvelú pieseň, ktorú ešte nemáme? Poď ju s nami zdieľať!</p>
      <p>
        Stačí vyplniť niekoľko údajov a my ju po kontrole pridáme do našej
        zbierky.
      </p>
    </div>

    <div class="addsong-grid">
      <div class="addsong-left">
        <span class="section-title">▸ Čo vyplniť:</span>
        <input
          type="text"
          v-model="form.nazovPiesne"
          placeholder="Názov piesne"
          class="big-input"
        />
        <textarea
          v-model="form.textPiesne"
          placeholder="Text piesne"
          class="big-input"
        ></textarea>
      </div>

      <div class="addsong-right">
        <div class="form-group select-group">
          <label>Typ piesne:</label>
          <select
            @change="form.autorska = form.typ === 'Autorská'"
            v-model="form.typPiesne"
          >
            <option>Ľudová</option>
            <option>Autorská</option>
          </select>
        </div>
        <div class="form-group select-group">
          <label>Žáner:</label>
          <select v-model="form.zaner">
            <option>Polka</option>
            <option>Valčík</option>
          </select>
        </div>
        <div class="form-group select-group">
          <label>Pôvod:</label>
          <select v-model="form.povod">
            <option>Česká</option>
            <option>Slovenská</option>
            <option>Iná</option>
          </select>
        </div>
        <div class="form-group modern-question">
          <label class="modern-label">Jedná sa o modernú pieseň?</label>
          <label class="modern-radio">
            <input type="checkbox" v-model="form.isModern" :value="true" />
            <span class="radio-custom"></span>
            Áno, ide o modernú pieseň
          </label>
        </div>
        <template v-if="form.typPiesne === 'Autorská'">
          <div class="modern-note">
            Pri autorskej piesni je potrebné uviesť meno autora textu
          </div>
          <input
            type="text"
            v-model="form.menoAutora"
            placeholder="Meno autora textu"
            class="author-input"
          />
        </template>
      </div>
    </div>

    <div class="footer-grid">
      <div>
        <span class="section-title">▸ Po odoslaní:</span>
        <span class="footer-desc">
          <p>Tvoj návrh najprv skontrolujeme, a ak bude všetko</p>
          <p>v poriadku, pieseň pridáme medzi ostatné.</p>
        </span>
      </div>
      <button class="button Adumu" @click="submitSong">
        Odoslať na kontrolu
      </button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  data() {
    return {
      form: this.getDefaultData(),
    };
  },
  methods: {
    getDefaultData() {
      return {
        nazovPiesne: "",
        textPiesne: "",
        typPiesne: "Ľudová",
        zaner: "Polka",
        povod: "Česká",
        isModern: null,
        menoAutora: "",
        autorska: false,
      };
    },

    // zavoláš po kliknutí “Odoslať na kontrolu”
    submitSong() {
      const fd = new FormData();
      fd.append("nazov", this.form.nazovPiesne);
      const safeText = this.form.textPiesne.replaceAll("\n", "//n");
      fd.append("text_piesne", safeText);
      fd.append("typ", this.form.typPiesne);
      fd.append("zaner", this.form.zaner);
      fd.append("povod", this.form.povod);
      fd.append("jeModerna", this.form.isModern ? "true" : "false");
      fd.append("autorska", this.form.autorska ? "true" : "false");
      fd.append("autor", this.form.menoAutora);

      axios
        .post(this.$store.state.api + "/noty/texty/create.php", fd)
        .then((res) => {
          if (res.data.status === "Request fullfiled") {
            this.$store.state.SnackBarText = "Návrh odoslaný na kontrolu";
            this.resetForm();
          } else {
            this.$store.state.SnackBarText = "Chyba pri odosielaní";
          }
        })
        .catch((err) => {
          console.error(err);
          this.$store.state.SnackBarText = "Chyba siete";
        });
    },

    resetForm() {
      this.form = this.getDefaultData();
      this.$emit("close"); // ak potrebuješ zatvoriť modal
    },
  },
};
</script>

<style scoped lang="scss">
h2 {
  text-align: center;
  font-size: 2.3em;
  font-weight: 700;
  margin: 0.3em auto 0.7em;
  font-family: "Bahnschrift", sans-serif;
}

.modal-desc {
  text-align: center;
  font-size: 1.25em;
  margin-bottom: 1.5em;
  font-family: "Bahnschrift", sans-serif;
}

.addsong-grid {
  display: flex;
  gap: 2.2em;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5em;
  flex-wrap: wrap;
}

.addsong-left {
  flex: 1.1;
  display: flex;
  flex-direction: column;
  gap: 0.45em;
  align-items: flex-end;
}

.section-title {
  font-weight: 700;
  font-size: 1.65em;
  color: #222;
  margin-bottom: 0.18em;
  text-align: left;
  width: 100%;
  display: flex;
  justify-content: flex-start;
}

input,
textarea,
select {
  border-radius: 1.0625em;
  background: #90ca50;
  box-shadow: -7px 5px 15px rgba(0, 0, 0, 0.25) inset,
    0px 4px 4px rgba(0, 0, 0, 0.25);
  padding: 0.6em 1em;
  width: 100%;
  font-size: 1.1em;
  border: none;
  outline: none;
  color: #222;
  margin-bottom: 0.75em;
  box-sizing: border-box;
  transition: box-shadow 0.15s;
}

input:focus,
textarea:focus,
select:focus {
  box-shadow: -9px 7px 20px rgba(0, 0, 0, 0.33) inset,
    0px 5px 10px rgba(0, 0, 0, 0.18);
}

textarea {
  min-height: 210px;
  max-height: 210px;
  resize: vertical;
}

.button {
  font-weight: 600;
  margin-left: auto;
}

.author-input {
  max-width: 27em;
}

select {
  appearance: none;
  background-image: url('data:image/svg+xml;utf8,<svg fill="black" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="6,8 10,12 14,8"/></svg>');
  background-repeat: no-repeat;
  background-position: right 0.9em center;
  background-size: 1em;
  cursor: pointer;
}

.addsong-right {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0;
  min-width: 230px;
  margin-top: 2em;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
}

.select-group label {
  font-weight: 500;
  font-size: 1.3em;
  margin-bottom: 0.18em;
  color: #222;
  text-align: left;
}

.modern-question {
  display: flex;
  flex-direction: column;
  gap: 0.5em;
  font-size: 1em;
}

.modern-label {
  font-weight: 600;
  margin-bottom: 0.2em;
  margin-top: 0.8em;
  font-size: 1.3em;
  text-align: left;
}

.modern-radio {
  display: flex;
  align-items: center;
  gap: 0.45em;
  font-size: 0.95em;
  position: relative;
  cursor: pointer;
}

.modern-radio input[type="checkbox"] {
  opacity: 0;
  position: absolute;
}

.radio-custom {
  width: 1.1em;
  height: 1.1em;
  border: 2px solid #89bd37;
  border-radius: 50%;
  display: inline-block;
  position: relative;
}

.modern-radio input[type="checkbox"]:checked + .radio-custom::after {
  content: "";
  width: 0.55em;
  height: 0.55em;
  background: #89bd37;
  border-radius: 50%;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.modern-note {
  color: #000000;
  font-weight: 100;
  font-size: 1em;
  margin: 1em 0 0.2em;
  text-align: left;
}

.footer-grid {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 2.1em;
  margin-top: 1.1em;
  flex-wrap: wrap;
}

.footer-desc {
  font-size: 1.04rem;
  color: #222;
  max-width: 37em;
  margin-top: 0.18em;

  p {
    text-align: left !important;
    font-size: 1em;
  }
}

@media only screen and (max-width: 560px) {
  .addsong-grid {
    flex-direction: column;
    gap: 0;
  }

  .addsong-left,
  .addsong-right {
    width: 100%;
  }

  h2 {
    font-size: 1.8em;
  }

  .footer-desc {
    font-size: 0.9em;
  }

  .button {
    font-size: 3.3vw;
  }
}
</style>
