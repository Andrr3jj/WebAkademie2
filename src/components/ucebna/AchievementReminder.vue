<template>
  <div
    class="vyzva-card"
    :class="{
      highlight: ulohaData.typ === '7-dni',
      invite: ulohaData.typ === 'pozvat-priatelov',
    }"
  >
    <div class="vyzva-main">
      <div class="vyzva-icon">
        <img v-if="imageSrc" :src="imageSrc" alt="ikona výzvy" />
      </div>
      <div class="vyzva-content">
        <!-- Pozvi priateľov: nadpis + popis + button spolu -->
        <div
          v-if="ulohaData.typ === 'pozvat-priatelov'"
          class="vyzva-invite-content"
        >
          <div class="vyzva-header">
            <span class="vyzva-title" v-html="ulohaData.nadpis"></span>
          </div>
          <div class="vyzva-subtitle-description">
            <span class="vyzva-subtitle" v-html="ulohaData.podnadpis"></span>
            <span class="vyzva-description" v-html="ulohaData.popis"></span>
          </div>
          <button
            class="button invite-btn"
            @click="copyReferral"
            :disabled="referralLoading"
          >
            Pozvať
          </button>
        </div>

        <!-- 7-dňová výzva -->
        <template v-else-if="ulohaData.typ === '7-dni'">
          <div class="vyzva-header">
            <span class="vyzva-title" v-html="ulohaData.nadpis"></span>
          </div>
          <div class="vyzva-description" v-html="ulohaData.popis"></div>
          <div class="vyzva-days-row-with-btn">
            <div class="vyzva-days-row">
              <div
                v-for="den in 7"
                :key="den"
                class="vyzva-day"
                :class="{
                  active: den <= (ulohaData.dniSplnene || 0),
                  current: den === (ulohaData.dniSplnene || 0) + 1,
                }"
                :title="`Deň ${den}`"
              ></div>
              <span class="vyzva-days-label"
                >{{ ulohaData.dniSplnene || 0 }}/7</span
              >
            </div>
            <button
              class="button"
              :class="{
                disable: ulohaData.dniSplnene < 7 || ulohaData.finished,
              }"
              @click="handleClaim"
            >
              {{ ulohaData.finished ? "Získané" : "Vyzdvihnúť" }}
            </button>
          </div>
        </template>

        <!-- Učivo na poslednej hodine -->
        <template
          v-else-if="ulohaData.typ === 'posledna-hodina' && mozeVidietPoslednu"
        >
          <div class="posledna-main">
            <div class="posledna-header">
              <span class="posledna-title">Učivo na poslednej hodine:</span>
            </div>

            <div class="posledna-rows">
              <div class="posledna-row">
                <span class="posledna-label">Hodina:</span>
                <span class="posledna-value">{{ formattedHodina }}</span>
              </div>
              <div class="posledna-row">
                <span class="posledna-label">Pieseň:</span>
                <span class="posledna-value">{{ piesneText }}</span>
              </div>
              <div class="posledna-row">
                <span class="posledna-label">Poznámka:</span>
                <span class="posledna-value">{{
                  ulohaData.poznamka || "—"
                }}</span>
              </div>
            </div>

            <button class="button" @click="$emit('zobraz-vsetko')">
              Zobraziť všetko
            </button>
          </div>
        </template>

        <!-- Ostatné výzvy -->
        <template v-else>
          <div class="vyzva-header">
            <span class="vyzva-title" v-html="ulohaData.nadpis"></span>
          </div>
          <div class="vyzva-description" v-html="ulohaData.popis"></div>
          <div class="vyzva-progress-row-with-btn">
            <div class="vyzva-progress-bar">
              <div
                class="vyzva-progress"
                :style="{ width: progressWidth }"
              ></div>
            </div>
            <span class="vyzva-progress-label">{{ displayProgress }}</span>
            <button class="button" @click="presmerujNaUlohy">Úlohy</button>
          </div>
        </template>
      </div>
    </div>
  </div>
</template>

<script>
const images = require.context("@/assets/images/vyzvy", false, /\.png$/);

export default {
  name: "AchievementReminder",
  props: {
    ulohaData: { type: Object, required: true },
    role: { type: String, default: null },
  },
  data() {
    return {
      referralLoading: false,
      showCopied: false,
    };
  },
  computed: {
    imageSrc() {
      if (
        this.ulohaData?.typ === "posledna-hodina" &&
        this.ulohaData.iconName
      ) {
        const safe = this.ulohaData.iconName.toLowerCase().replace(/\s+/g, "-");
        try {
          return images(`./${safe}`);
        } catch {
          return "";
        }
      }
      const nadpis = this.ulohaData?.nadpis;
      if (!nadpis || typeof nadpis !== "string") {
        try {
          return images("./default.png");
        } catch {
          return "";
        }
      }
      const fileName =
        nadpis
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replaceAll(" ", "-")
          .toLowerCase() + ".png";
      try {
        return images(`./${fileName}`);
      } catch {
        return images.keys().includes("./default.png")
          ? images("./default.png")
          : "";
      }
    },
    normalizedType() {
      const t = (this.ulohaData?.typ || "").toString().toLowerCase().trim();
      const map = {
        "7-dni": "7-dni",
        "7dni": "7-dni",
        "7_dni": "7-dni",
        "pozvat-priatelov": "pozvat-priatelov",
        "pozvi-priatelov": "pozvat-priatelov",
        "posledna-hodina": "posledna-hodina",
        posledna_hodina: "posledna-hodina",
        poslednahodina: "posledna-hodina",
      };
      return map[t] || t;
    },

    isInvite() {
      return this.normalizedType === "pozvat-priatelov";
    },

    is7dni() {
      return this.normalizedType === "7-dni";
    },

    mozeVidietPoslednu() {
      return ["ziak", "rodic", "student", "parent"].includes(
        (this.role || "").toString().toLowerCase().trim()
      );
    },

    isPosledna() {
      return (
        this.normalizedType === "posledna-hodina" && this.mozeVidietPoslednu
      );
    },
    formattedHodina() {
      const d = this.ulohaData?.hodina;
      if (!d) return "—";
      const [y, m, day] = d.split("-").map(Number);
      const date = new Date(y, (m || 1) - 1, day || 1);
      const mesiace = [
        "januára",
        "februára",
        "marca",
        "apríla",
        "mája",
        "júna",
        "júla",
        "augusta",
        "septembra",
        "októbra",
        "novembra",
        "decembra",
      ];
      return `${date.getDate()}. ${
        mesiace[date.getMonth()]
      } ${date.getFullYear()}`;
    },
    piesneText() {
      const p = this.ulohaData?.piesne;
      if (!p) return "—";
      return Array.isArray(p) ? p.join(", ") : String(p);
    },
    progressWidth() {
      let p = Number(this.ulohaData.progress) || 0;
      if (p < 0) p = 0;
      if (p > 100) p = 100;
      return `${p}%`;
    },
    displayProgress() {
      const p = Number(this.ulohaData.progress) || 0;
      return `${Math.round(p)}%`;
    },
    referralCode() {
      return this.$store.state.referral?.code || null;
    },
    referralUrl() {
      return this.referralCode
        ? `https://heligonkovaakademia.sk?reflink=${this.referralCode}/`
        : "";
    },
  },
  methods: {
    presmerujNaUlohy() {
      this.$router.push("/ucebna/ulohy");
    },
    handleClaim() {
      if (this.ulohaData.dniSplnene < 7) {
        this.$store.state.SnackBarText = "Ešte nemáš splnených 7 dní!";
        return;
      }
      if (this.ulohaData.finished) {
        this.$store.state.SnackBarText = "Odmena už bola vyzdvihnutá.";
        return;
      }
      this.$emit("akcia");
    },
    async copyReferral() {
      this.referralLoading = true;
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/user/info/getInviteCode.php"
        );
        const result = await res.json();
        if (result.status === "Request succesfull" && result.data) {
          const url = `https://heligonkovaakademia.sk?reflink=${result.data}/`;
          await navigator.clipboard.writeText(url);
          this.$store.state.SnackBarText =
            "Link bol skopírovaný! Pošli ho kamarátovi.";
          this.showCopied = true;
          setTimeout(() => (this.showCopied = false), 2000);
        } else {
          this.$store.state.SnackBarText =
            "Nepodarilo sa načítať referral odkaz.";
        }
      } catch (e) {
        this.$store.state.SnackBarText =
          "Nepodarilo sa načítať referral odkaz.";
      }
      this.referralLoading = false;
    },
  },
};
</script>

<style scoped lang="scss">
.vyzva-card {
  border-radius: 2em;
  border: 0.36em solid #fff45b;
  box-shadow: 0.2em 0.2em 1.2em #0002;
  display: flex;
  align-items: stretch;
  justify-content: space-between;
  gap: 1.5em;
  padding: 1.8em 2em;
  margin: 1.3em 0;
  width: 100%;
  min-width: 0;
  position: relative;
  z-index: 1;
  min-height: 6em;
}

.vyzva-main {
  display: flex;
  flex-direction: row;
  align-items: center;
  flex: 1 1 0;
  min-width: 0;
  gap: 2em;
}

.vyzva-icon {
  flex-shrink: 0;
  display: flex;
  align-items: center;
  justify-content: center;

  img {
    height: 5.5em;
    width: 5.5em;
    display: block;
    object-fit: contain;
  }
}

.vyzva-content {
  display: flex;
  flex-direction: column;
  gap: 0.65em;
  min-width: 0;
}

.vyzva-header {
  display: flex;
  align-items: flex-end;
  gap: 1em;
}

.vyzva-title {
  font-size: 2em;
  font-weight: bold;
  color: #000;
  font-family: inherit;
}

.vyzva-description {
  font-size: 1.2em;
  color: black;
  font-weight: 500;
  text-align: left;
  font-family: inherit;
  width: 17em;
}

.vyzva-invite-content {
  width: 28em;
  display: flex;
  flex-direction: column;

  .vyzva-header {
    margin-bottom: 0.5em;
  }

  .vyzva-subtitle-description {
    display: flex;
    flex-direction: column;

    .vyzva-subtitle {
      font-size: 1.4em;
      text-align: left;
      font-weight: bold;
    }
  }

  .button {
    margin-left: auto;
    margin-top: -2.5em;
  }
}

.vyzva-days-row-wrap {
  display: flex;
  flex-direction: column;
  font-size: 70%;
}

.vyzva-days-row {
  display: flex;
  align-items: center;
  gap: 0.5em;
}

.vyzva-days-row-with-btn {
  display: flex;
  align-items: center;
  font-size: 90%;
}

.button {
  font-size: 1.2em;
  color: #000;
  font-family: "Adumu", sans-serif;
}

.button.disable {
  background-color: #ccc;
  cursor: not-allowed;
  opacity: 0.6;
}

.vyzva-day {
  width: 20px;
  height: 20px;
  border-radius: 0.8em;
  border: 0.11em solid #d9d9d9;
  background: #d9d9d9;
  transition: 0.14s background;
  display: inline-block;

  &.active {
    background: #90ca50;
    border-color: #90ca50;
  }
}

.vyzva-days-label {
  font-size: 1.3em;
  font-weight: 700;
  color: #000;
  margin-left: 0.2em;
  min-width: 2em;
}

.vyzva-progress-row {
  display: flex;
  align-items: center;
  gap: 1.1em;
  margin-top: 0.8em;
  margin-bottom: 0.1em;
}

.vyzva-progress-row-with-btn {
  display: flex;
  align-items: center;
  gap: 1em;
  width: 30em;
  font-size: 90%;
}

.vyzva-progress-bar {
  width: 100%;
  height: 1em;
  background: #90ca50;
  border-radius: 5px;
  margin-right: 1em;
  overflow: hidden;
  display: flex;
  align-items: center;
}

.vyzva-progress {
  height: 51%;
  background: #079440;
  transition: width 0.2s ease-in-out;
  border-radius: 5px;
  margin: auto 0.3em;
  width: 30%;
}

.vyzva-progress-label {
  font-size: 1.35em;
  font-weight: 700;
  color: #222;
  min-width: 2.5em;
}

.vyzva-action {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  justify-content: center;
  min-width: 9.5em;
  margin-left: 1.2em;
  gap: 1em;
}

.vyzva-btn {
  background: #ffe94e;
  color: #222;
  font-size: 1.36em;
  font-weight: 900;
  padding: 0.5em 1.5em;
  border-radius: 1.5em;
  border: none;
  box-shadow: 0.12em 0.22em 0.7em #0001;
  cursor: pointer;
  letter-spacing: 0.02em;
  transition: background 0.18s;
  outline: none;
  margin-top: 0.6em;
}

.vyzva-content {
  .posledna-main {
    .posledna-header {
      font-size: 1.5em;
      text-align: left;
      font-weight: 700;
    }
    .posledna-rows {
      display: flex;
      flex-direction: column;
      align-items: flex-start;

      .posledna-row {
        display: flex;
        gap: 1em;

        .posledna-label {
          font-size: 1em;
          font-weight: 900;
        }
      }
    }
    .button {
      margin-top: 0.5em;
      padding: 0.2em 1.5em;
    }
  }
}

.odmena {
  font-weight: 700;
  color: #32311c;
  text-align: center;
  font-size: 1em;
  margin-bottom: 0.5em;
  margin-top: 0.6em;
}

.dokoncene {
  font-size: 1.1em;
  color: #a2a2a2;
  text-align: left;
  font-weight: 600;
}

@media screen and (max-width: 1600px) {
  .vyzva-card {
    min-height: 10em;
  }

  .vyzva-days-row-with-btn {
    width: 26em;
  }

  .vyzva-progress-row-with-btn {
    width: 30em;
  }

  .vyzva-progress-row-with-btn,
  .vyzva-days-row-with-btn {
    display: block;

    .button {
      margin-left: auto;
      margin-right: auto;
      margin-top: 1em;
    }
  }

  .vyzva-invite-content {
    .button {
      margin-top: 0;
      margin-right: auto;
    }
  }
}

@media (max-width: 60em) {
  .vyzva-card {
    flex-direction: column;
    gap: 1em;
    padding: 1.1em 0.6em;
  }

  .vyzva-main {
    gap: 1em;
    flex-direction: column;
    align-items: flex-start;
  }

  .vyzva-icon img {
    height: 4.5em;
    width: 4.5em;
  }

  .vyzva-action {
    align-items: stretch;
    min-width: 0;
    margin-left: 0;
  }
}

@media (min-width: 750px) and (max-width: 1024px) {
  .vyzva-days-row {
    gap: 0.2em;
  }

  .vyzva-card {
    min-height: 13em;
  }

  .vyzva-subtitle {
    text-align: center !important;
  }

  .vyzva-days-row-with-btn {
    .button {
      margin-left: 0;
      margin-right: 0;
    }
  }

  .vyzva-days-row-with-btn,
  .vyzva-progress-row-with-btn {
    width: 100%;

    .button {
      margin-left: auto;
      margin-right: auto;
      margin-top: 1em;
    }
  }

  .vyzva-progress-row-with-btn {
    .vyzva-pregress-label {
      display: block;
      margin-top: 1em;
    }
  }
}

@media screen and (max-width: 1024px) {
  .vyzva-card {
    width: 70%;
    justify-content: center;
  }

  .vyzva-main {
    align-items: center;
  }

  .vyzva-title {
    width: 100%;
    text-align: center;
  }

  .vyzva-description {
    width: 100%;
    text-align: center;
  }

  .vyzva-progress-row-with-btn,
  .vyzva-days-row-with-btn {
    display: block;
  }

  .vyzva-day {
    width: 23px;
    height: 23px;
  }
}

@media screen and (max-width: 750px) {
  .vyzva-card {
    min-height: 8em;
  }

  .vyzva-main {
    flex-direction: row;
    justify-content: center;
  }

  .vyzva-day {
    width: 25px;
    height: 25px;
  }
}
</style>
