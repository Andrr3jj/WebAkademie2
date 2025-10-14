<template>
  <div class="uloha">
    <div class="icon">
      <img :src="imageSrc" />
    </div>
    <div class="info">
      <div class="vrch">
        <p class="nadpis">{{ lokalnaUloha.nadpis }}</p>
        <p class="dokoncene" :class="{ finished: lokalnaUloha.finished }">
          {{
            lokalnaUloha.dokonceneDatum == ""
              ? ""
              : "Dokončené dňa: " + lokalnaUloha.dokonceneDatum
          }}
        </p>
      </div>
      <p class="popis">{{ lokalnaUloha.popis }}</p>
      <div class="spodok">
        <p class="popistok">Dokončených</p>
        <div class="snackbar">
          <div class="progress-bar">
            <div
              class="progress"
              :style="{ width: lokalnaUloha.progress + '%' }"
            ></div>
          </div>
          <span class="percentage">{{ lokalnaUloha.progress }}%</span>
        </div>
      </div>
    </div>
    <div class="action" :class="{ finished: lokalnaUloha.finished }">
      <p class="odmena">Odmena: {{ lokalnaUloha.odmena }} {{ pocetBodov() }}</p>
      <div
        class="button"
        :class="{
          disabled: lokalnaUloha.progress < 100 || lokalnaUloha.finished,
        }"
        @click="ziskajOdmenu"
      >
        <p>
          {{
            lokalnaUloha.finished
              ? "Získané"
              : lokalnaUloha.progress < 100
              ? "Splň výzvu"
              : "Získať"
          }}
        </p>
      </div>
    </div>
  </div>
</template>

<script>
const images = require.context("@/assets/images/vyzvy", false, /\.png$/);
export default {
  props: {
    ulohaData: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      lokalnaUloha: { ...this.ulohaData },
    };
  },
  watch: {
    ulohaData: {
      handler(nova) {
        this.lokalnaUloha = { ...nova };
      },
      deep: true,
      immediate: true,
    },
  },
  computed: {
    imageSrc() {
      const fileName =
        this.lokalnaUloha.nadpis
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replaceAll(" ", "-")
          .toLowerCase() + ".png";

      try {
        return images(`./${fileName}`);
      } catch {
        return ""; // nič nezobrazí
      }
    },
  },
  methods: {
    pocetBodov(pocet = this.lokalnaUloha.odmena) {
      const n = Number(pocet);
      if (n === 1) return "bod";
      if (n === 2 || n === 3 || n === 4) return "body";
      return "bodov";
    },
    async ziskajOdmenu() {
      if (this.lokalnaUloha.finished) return;

      if (this.lokalnaUloha.progress < 100) {
        this.$store.state.SnackBarText =
          "❗ Túto výzvu musíš najprv splniť na 100 %, aby si získal odmenu.";
        return;
      }

      try {
        const res = await fetch(
          `/api/achievements/claimReward.php?reward_id=${encodeURIComponent(
            this.lokalnaUloha.id
          )}`,
          { credentials: "include" }
        );
        const result = await res.json();

        if (result.status === "Request succesfull") {
          this.lokalnaUloha.finished = true;
          this.lokalnaUloha.dokonceneDatum = new Date().toLocaleDateString(
            "sk-SK"
          );
          this.$store.state.SnackBarText = `🎉 Získavate ${
            this.lokalnaUloha.odmena ?? ""
          } ${this.pocetBodov ? this.pocetBodov() : ""} za výzvu: „${
            this.lokalnaUloha.nadpis
          }“`;

          // Odošli zmenu rodičovi (odporúčam poslať kópiu)
          this.$emit("uloha-zmenena", { ...this.lokalnaUloha });
        } else {
          this.$store.state.SnackBarText =
            "⚠️ Odmenu sa nepodarilo získať. Skús to neskôr alebo kontaktuj podporu.";
        }
      } catch (err) {
        console.error("Chyba pri pripisovaní odmeny:", err);
        this.$store.state.SnackBarText =
          "❌ Chyba pri pripisovaní odmeny. Skús to neskôr.";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.uloha {
  display: flex;
  justify-content: space-evenly;
  gap: 2%;
  align-items: center;
  box-shadow: 0.2em 0.2em 0.5em rgba(0, 0, 0, 0.3);
  border: 0.3em solid #fef35a;
  border-radius: 1.4rem;
  padding: 0.7em 1em 0em;
  margin: 1.3em auto;
}

.icon img {
  height: 4.8em;
  width: auto;
  margin: auto;
}

.vrch {
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: space-between;
}

.info {
  width: 40vw;
}

.nadpis {
  font-size: 1.8em;
  font-weight: 700;
  text-align: left;
}

.dokoncene,
.popis {
  font-size: 1.2em;
  text-align: left;
}

.odmena {
  font-weight: 600;
  text-align: center;
  font-size: 1.1em;
}

.popistok {
  font-size: 0.9em;
  opacity: 50%;
  text-align: left;
}

.action {
  display: flex;
  gap: 0.7em;
  flex-direction: column;
  align-items: center;
  margin: 0.5em 0;
}

.button {
  font-size: 0.85em;
}

.button.disabled {
  background-color: #ccc;
  cursor: not-allowed;
  opacity: 0.6;
}

//snacbar

.snackbar {
  display: flex;
  align-items: center;
  width: 100%;
}

.finished {
  z-index: -1;
  opacity: 40%;
}

.progress-bar {
  width: 100%;
  height: 0.35em;
  background: #90ca50;
  border-radius: 5px;
  margin-right: 1em;
  overflow: hidden;

  display: flex;
  align-items: center;
}

.progress {
  height: 50%;
  background: #079440;
  transition: width 0.2s ease-in-out;
  border-radius: 5px;
  margin: auto 0.08em;
  width: 30%;
}

.percentage {
  font-weight: bold;
}

@media only screen and (max-width: 750px) {
  .vrch {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }

  .button {
    margin-top: 0.5em !important;
  }
}

@media only screen and (max-width: 500px) {
  .uloha {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    /* align-content: flex-start; */
    justify-content: center;
  }

  .info {
    width: 100%;
  }

  .action {
    gap: 0;
  }
}
</style>
