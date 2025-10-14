<template>
  <div class="time-values">
    <div class="cube yellow">
      <p>{{ localDoba.dni }}</p>
    </div>
    <div class="cube lite-green">
      <p>{{ localDoba.hodiny }}</p>
    </div>
    <div class="cube green">
      <p>{{ localDoba.minuty }}</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    dobaHranie: {
      type: Object,
      required: false,
      default: null,
    },
  },
  mounted() {
    // Ak nepríde prop, stiahni hodnoty zo servera
    if (!this.dobaHranie) {
      this.celkoveHranie();
    }
  },
  watch: {
    dobaHranie: {
      immediate: true,
      deep: true,
      handler(nova) {
        if (!nova) return;
        this.localDoba = {
          dni: Number(nova.dni) || 0,
          hodiny: Number(nova.hodiny) || 0,
          minuty: Number(nova.minuty) || 0,
        };
      },
    },
  },
  data() {
    return {
      localDoba: {
        dni: this.dobaHranie?.dni ?? 0,
        hodiny: this.dobaHranie?.hodiny ?? 0,
        minuty: this.dobaHranie?.minuty ?? 0,
      },
      taskStarsData: {},
    };
  },
  methods: {
    vypocitajTaskStars() {
      const { dni, hodiny } = this.localDoba || {};
      const stars = this.vypocitajHviezdy(dni, hodiny);

      this.taskStarsData = {
        ...this.taskStarsData,
        dni,
        hodiny,
        stars,
      };
    },
    vypocitajHviezdy(dni, hodiny) {
      const totalHours = dni * 24 + hodiny;
      if (totalHours < 8) return 0;
      if (totalHours < 24) return 1;
      if (dni === 1) return 2;
      if (dni >= 2 && dni < 5) return 3;
      if (dni >= 5 && dni < 10) return 4;
      return 5;
    },
    celkoveHranie() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/user/stats/getUserWatchtime.php",
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            if (response.data.data == 0 || response.data.data == "0") {
              this.localDoba.minuty = 0;
              this.localDoba.hodiny = 0;
              this.localDoba.dni = 0;
            } else {
              this.localDoba = this.rozdelitMinuty(
                Object.values(response.data.data)[0]
              );
            }
            // Spustíme až keď máme načítané
            this.vypocitajTaskStars();
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    rozdelitMinuty(minuty) {
      const dni = Math.floor(minuty / (24 * 60));
      const zostavajuceHodiny = minuty % (24 * 60);
      const hodiny = Math.floor(zostavajuceHodiny / 60);
      const zostavajuceMinuty = zostavajuceHodiny % 60;
      return { dni, hodiny, minuty: zostavajuceMinuty };
    },
  },
};
</script>

<style lang="scss" scoped>
.time-values {
  display: flex;
  flex-direction: row;
  gap: 1.5em;
  margin-bottom: 2em;
}

.cube {
  padding: 0.4em;
  margin: 1.4em 0;
  border: 0.25em solid #fef35a;
  background: #fef35a;
  border-radius: 1.6em;
  box-shadow: 2.5px 2.5px 5px rgba(0, 0, 0, 0.35),
    inset 2px 2px 10px rgba(0, 0, 0, 0.25);

  width: 4.2em;
  height: 4.2em;
  display: flex;
  align-items: center;
  justify-content: center;

  p {
    font-family: Adumu, Bahnshrift, sans-serif;
    font-weight: 400;
    font-size: 2.2em;
    letter-spacing: 0.05em;
    margin-bottom: -0.3em;
  }
}

.lite-green {
  background: #8ec84e;
}

.green {
  background: #00913f;
}
</style>
