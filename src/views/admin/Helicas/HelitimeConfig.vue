<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Nastavenie kreditového systému</h5>
      <div class="line horizontal w-80"></div>

      <div class="form-grid">
        <div class="form-group" v-for="(value, key) in config" :key="key">
          <label :for="key">{{ formatKey(key) }}</label>
          <input :id="key" v-model="config[key]" type="number" class="input" />
        </div>
      </div>

      <div class="centered">
        <button class="button" @click="ulozNastavenie">Uložiť</button>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "AdminAchievementConfig",
  data() {
    return {
      config: {},
    };
  },
  mounted() {
    fetch(
      "https://heligonkovaakademia.sk/api/helitime/config/helitime.config.json"
    )
      .then((res) => res.json())
      .then((data) => {
        this.config = data;
      })
      .catch(() => {
        this.$store.state.SnackBarText = "❌ Chyba pri načítaní konfigurácie";
      });
  },
  methods: {
    formatKey(key) {
      const customNames = {
        credit_trigger_value_notification: "Hodnota upozornenia na kredit",
        helitime_credit_per_timeout: "Kredit za interval",
        helitime_timeout_seconds: "Časový interval (s)",
        helitime_first_play_time_seconds: "Čas prvého znásobeného hrania (s)",
        helitime_first_play_time_multiplicator: "Multiplikátor prvého hrania",
        helitime_event0_start_time_hour: "Začiatok znásobeného eventu (hodiny)",
        helitime_event0_end_time_hour: "Koniec eventu (hodiny)",
        helitime_event0_multiplicator: "Multiplikátor eventu",
        helitime_daily_limit: "Denný limit",
        helitime_daily_limit_reached_points: "Kredit po dosiahnutí maxima",
        helitime_to_eur_value_multiplier: "Konverzia kredit → Cent",
      };

      return (
        customNames[key] ||
        key.replace(/_/g, " ").replace(/\b\w/g, (c) => c.toUpperCase())
      );
    },
    ulozNastavenie() {
      fetch("https://heligonkovaakademia.sk/api/helitime/setSettings.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `settings=${encodeURIComponent(JSON.stringify(this.config))}`,
      })
        .then((res) => res.json())
        .then((res) => {
          if (res.status === "Request succesfull") {
            this.$store.state.SnackBarText = "✅ Nastavenie uložené";
          } else {
            this.$store.state.SnackBarText = "⚠️ Chyba pri ukladaní";
          }
        })
        .catch(() => {
          this.$store.state.SnackBarText = "❌ Chyba pripojenia";
        });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.75vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0;
}

h5 {
  text-align: center;

  font-size: 2.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 2em;
  margin: 3em auto 2em;
  max-width: 1000px;
}

.centered {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 2em;
}

.form-group {
  display: flex;
  flex-direction: column;
  align-items: center;
}

label {
  font-weight: 600;
  margin-bottom: 0.3em;
  font-size: 1.2em;
}

.input {
  border-radius: 1.0625em;
  background: #90ca50;
  box-shadow: -7px 5px 15px 0px rgba(0, 0, 0, 0.25) inset,
    0px 4px 4px 0px rgba(0, 0, 0, 0.25);
  padding: 0.35em 5%;
  box-sizing: border-box;
  text-align: center;
}
</style>
