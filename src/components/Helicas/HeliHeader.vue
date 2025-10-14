<template>
  <div class="heli-header">
    <h1>
      Heli <br class="br" />
      Čas
    </h1>
    <div class="control">
      <h5>Učte sa, zbierajte body a získavajte odmeny!</h5>

      <div class="line horizontal w-90"></div>

      <div class="stats">
        <div class="your-stats">
          <p class="podnadpis">
            Tvoj celkový čas:
            <span class="time-cubes">
              <span class="cube yellow">
                <p>{{ cas?.dni ?? 0 }}</p>
              </span>
              <span class="cube lite-green">
                <p>{{ cas?.hodiny ?? 0 }}</p>
              </span>
              <span class="cube green">
                <p>{{ cas?.minuty ?? 0 }}</p>
              </span>
            </span>
          </p>

          <p class="podnadpis">
            Tvoj aktuálny stav bodov:
            <span class="body">
              <template v-if="creditsLoading">...</template>
              <template v-else-if="creditsError">0</template>
              <template v-else>{{ credits }}</template>
            </span>
          </p>
        </div>

        <div class="line vertical"></div>

        <div class="position">
          <p class="podnadpis">
            Moje umiestnenie:
            <span>{{ displayUmiestnenie }}</span>
          </p>
        </div>
      </div>
    </div>

    <div class="tasks">
      <div class="task-item">
        <p class="task">Splň úlohy a ziskaj bonusové body!</p>
        <button class="button Adumu" @click="presmerujNaUlohy">Úlohy</button>
      </div>
      <p class="goal">Splnené {{ splneneUloh }}/{{ totalUloh }}</p>
      <div class="task-item second-item">
        <p class="task">Získané body</p>
        <button class="button Adumu" @click="otvorModal">Prehľad</button>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState } from "vuex";
import PrehladUloh from "@/components/Helicas/PrehladUloh.vue";

export default {
  name: "HeliHeader",
  props: {
    userId: { type: [String, Number], required: true },
    cas: { type: Object, required: true }, // { dni, hodiny, minuty }
    body: { type: Number, required: false, default: 0 }, // fallback, už sa nepoužíva ak načítavame z API
    umiestnenie: { type: [Number, String], required: false, default: null },
    splnene: { type: Number, default: 0 },
    celkovoUloh: { type: Number, default: 40 },
  },
  data() {
    return {
      showModal: false,
      totalUloh: 0,
      splneneUloh: 0,
    };
  },
  computed: {
    ...mapState("credits", {
      credits: (state) => state.credits,
      creditsLoading: (state) => state.loading,
      creditsError: (state) => !!state.error,
    }),
    modalComponent() {
      return PrehladUloh;
    },
    displayUmiestnenie() {
      if (
        this.umiestnenie === null ||
        this.umiestnenie === undefined ||
        this.umiestnenie === "" ||
        this.umiestnenie === "A" ||
        (typeof this.umiestnenie === "number" && isNaN(this.umiestnenie))
      ) {
        return "neumiestnený";
      }
      if (!isNaN(Number(this.umiestnenie)) && this.umiestnenie !== "") {
        return Number(this.umiestnenie);
      }
      return this.umiestnenie;
    },
  },
  mounted() {
    this.nacitajStats();
    if (this.credits === null && this.$store.state.user?.id) {
      this.$store.dispatch("credits/fetchCredits");
    }
  },
  methods: {
    async nacitajStats() {
      this.loadingStats = true;
      this.statsError = false;
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/achievements/getAchievementsStats.php",
          {
            credentials: "include",
          }
        );
        const json = await res.json();
        this.totalUloh = json.data?.total ?? 0;
        this.splneneUloh = json.data?.finished ?? 0;
      } catch (e) {
        this.statsError = true;
        this.totalUloh = 0;
        this.splneneUloh = 0;
      } finally {
        this.loadingStats = false;
      }
    },
    presmerujNaUlohy() {
      this.$router.push("/ucebna/ulohy");
    },
    otvorModal() {
      this.$emit("show-modal", {
        component: PrehladUloh,
        props: { userId: this.userId },
      });
    },
    zatvorModal() {
      this.showModal = false;
    },
  },
};
</script>

<style scoped lang="scss">
@import "@/assets/sass/_colors.scss";

h1 {
  text-align: left;
  font-size: 3.125em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%;
  /* 3.59375rem */
  letter-spacing: 0.25em;
  z-index: 1;
  margin: 0;
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 3px rgba(0, 0, 0, 0.25);
  font-size: 5vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0.1em 0.15em 0.1em;
  margin: 0;
}

h5 {
  color: #001;
  font-size: 2vw;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  margin: 0 0 0 0;
  text-align: center;
  margin-bottom: 0.5em;
}

.heli-header {
  display: flex;
  align-items: center;
  width: 100%;
}

.podnadpis {
  color: #001;
  font-size: 1.5vw;
  font-style: normal;
  font-weight: 500;
  line-height: 115%;

  .body {
    margin: 0 0.5em;
  }
}

.control {
  display: flex;
  align-items: center;
  justify-content: space-around;
  flex-direction: column;
  gap: 2%;
  width: 100%;
  height: auto;
  position: relative;
  height: 100%;
}

.stats {
  display: flex;
}

.your-stats {
  display: flex;
  flex-direction: column;
  gap: 1em;
  margin: 0.5em 1em;

  p {
    text-align: left;
  }
}

.time-cubes {
  display: inline-flex;
  gap: 0.3em;
  margin: 0 0.5em;
  vertical-align: middle;
  font-size: 80%;
}

.cube {
  width: 1.8em;
  height: 1.8em;
  padding: 0.4em;
  padding-top: 0.45em;
  border: 0.15em solid #fef35a;
  background: #fef35a;
  border-radius: 0.6em;
  box-shadow: 2.5px 2.5px 5px rgba($color: #000000, $alpha: 0.35),
    inset 2px 2px 10px rgba($color: #000000, $alpha: 0.25);
  display: flex;
  align-items: center;
  justify-content: center;
  box-sizing: border-box;

  p {
    font-family: Adumu, Bahnshrift, sans-serif;
    font-weight: 400;
    font-size: 0.8em;
    letter-spacing: 0.05em;
    color: #000000;
  }
}

.lite-green {
  background: #8ec84e;
}

.green {
  background: #00913f;
}

.line {
  width: 0.25em;
}

.position {
  display: flex;
  align-items: center;
  margin-left: 2em;

  span {
    margin: 0 0.5em;
  }
}

.tasks {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0.5em;
  margin-top: 1em;
  padding: 0.5em;
  width: 19%;
}

.task-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.task {
  font-size: 1.25em;
  line-height: 130%;
  text-align: center;
}

.button {
  margin-bottom: 5%;
  font-size: 1.2em;
  font-weight: 700;
  color: black;
}

.goal {
  font-size: 1em;
}

@media screen and (max-width: 1000px) and (min-width: 600px) {
  .headline-content {
    transform: scale(0.88);
    transform-origin: top center;
    padding: 1% 2%;
  }

  h1 {
    font-size: 6vw;
  }

  .podnadpis {
    font-size: 1em;
    padding-left: 0.5em;
    padding-right: 0.5em;
  }

  .your-stats {
    margin: 0;
    margin-right: 0.5em;

    p {
      text-align: center;
      padding: 0.5em;
    }
  }

  .position {
    margin-left: 0em;
  }

  .tasks {
    width: 15%;
  }

  .task {
    font-size: 0.8em;
  }

  .button {
    font-size: 0.9em;
    color: black;
  }

  .goal {
    font-size: 0.9em;
  }
}

@media screen and (max-width: 752px) {
  .br {
    display: none;
  }

  h1 {
    font-size: 9vw;
    line-height: 110%;
  }

  h5 {
    font-size: 4.5vw;
    margin-bottom: 1em;
  }

  .heli-header {
    display: block;
  }

  .control {
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 1em;
    width: 100%;
    padding: 1em 0.5em;
  }

  .stats {
    width: 100%;
    justify-content: center;
  }

  .your-stats {
    align-items: center;
    text-align: center;
    margin: 0;
    gap: 0.5em;
    width: 30%;
  }

  .your-stats p {
    text-align: center;
  }

  .position {
    margin: 1em;
    align-items: center;
    text-align: center;
  }

  .time-cubes {
    margin: 0.5em 0;
  }

  .cube {
    width: 2em;
    height: 2em;

    p {
      font-size: 0.6em;
    }
  }

  .podnadpis {
    font-size: 0.9em;
    text-align: center;
  }

  .tasks {
    width: 100%;
    padding: 0;
    margin-top: 0;
    align-items: flex-start !important;
    text-align: center;
    margin-bottom: 0.5em;
    flex-direction: row;
  }

  .task-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 40%;
  }

  .second-item {
    .task {
      margin-bottom: 0;
    }
  }

  .task {
    font-size: 1.1em;
    padding: 0 1em;
    margin-bottom: -1em;
  }

  .button {
    font-size: 0.9em;
    padding: 0.5em 1.2em;
  }

  .goal {
    font-size: 1em;
    margin-top: 0.5em;
  }
}

@media screen and (max-width: 500px) {
  .your-stats {
    width: 50%;
  }
}
</style>
