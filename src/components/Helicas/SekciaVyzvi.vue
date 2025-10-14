<template>
  <div v-for="vyzva in vyzvyData" :key="vyzva.id" class="vyzvy-sekcia">
    <p class="nadpis">{{ vyzva.nazov }}:</p>
    <div class="vyzvy">
      <jednaUloha
        v-for="uloha in vyzva.ulohyData"
        :key="uloha.id"
        :ulohaData="uloha"
        @uloha-zmenena="aktualizujUlohu"
      />
      <div v-if="vyzva.zamknute" class="lock">
        <img
          src="@/assets/images/icons/zamka.png"
          alt="Zamknuté vyzvihnutie odmien"
        />
      </div>
    </div>
  </div>
</template>

<script>
import JednaUloha from "./JednaUloha.vue";

export default {
  components: {
    JednaUloha,
  },
  data() {
    return {
      vyzvyData: [],
    };
  },
  mounted() {
    fetch("/api/achievements/getAchievements.php")
      .then((res) => res.json())
      .then((res) => {
        const rawData = Object.values(res.data)
          .filter((uloha) => uloha.id !== "7_day")
          .filter((uloha) => uloha.id !== "401");

        const grouped = {
          učenie: [],
          piesne: [],
          videá: [],
          komunita: [],
          aktivita: [],
        };

        rawData.forEach((uloha) => {
          const id = parseInt(uloha.id);
          if (!isNaN(id)) {
            if (id < 100) grouped.učenie.push(uloha);
            else if (id < 200) grouped.piesne.push(uloha);
            else if (id < 300) grouped.videá.push(uloha);
            else if (id < 400) grouped.komunita.push(uloha);
            else if (id < 500) grouped.aktivita.push(uloha);
          }
        });

        this.vyzvyData = [
          {
            nazov: "Výzvy súvisiace s učením",
            zamknute: false,
            ulohyData: grouped.učenie.map(this.mapUloha),
          },
          {
            nazov: "Výzvy súvisiace s tvojimi piesňami",
            zamknute: false,
            ulohyData: grouped.piesne.map(this.mapUloha),
          },
          {
            nazov: "Výzvy súvisiace s náučnými videami",
            zamknute: false,
            ulohyData: grouped.videá.map(this.mapUloha),
          },
          {
            nazov: "Výzvy súvisiace s komunitou",
            zamknute: false,
            ulohyData: grouped.komunita.map(this.mapUloha),
          },
          {
            nazov: "Výzvy súvisiace s aktivitou na stránke",
            zamknute: false,
            ulohyData: grouped.aktivita.map(this.mapUloha),
          },
        ];
      })
      .catch((err) => {
        console.error("Chyba pri načítaní výziev:", err);
      });
  },
  methods: {
    mapUloha(uloha) {
      const actual = parseFloat(uloha.points_actual) || 0;
      const max = parseFloat(uloha.points_max) || 1;
      return {
        id: uloha.id,
        nadpis: uloha.name,
        popis: uloha.text,
        dokonceneDatum: uloha.finished_date,
        progress: Math.round((actual / max) * 100),
        odmena: Number(uloha.reward),
        finished: uloha.reward_claimed === "true",
      };
    },

    aktualizujUlohu(zmenenaUloha) {
      for (const vyzva of this.vyzvyData) {
        const index = vyzva.ulohyData.findIndex((u) => u.id == zmenenaUloha.id);
        if (index !== -1) {
          this.$set(vyzva.ulohyData, index, { ...zmenenaUloha });
          break;
        }
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.nadpis {
  font-size: 1.8em;
  text-align: left;
  font-weight: 900;
  width: 98%;
  max-width: 34em;
  margin: 1.4em auto 0.5em;
}

.vyzvy {
  max-width: 60em;
  width: 95%;
  margin: 1.5em auto;
  position: relative;
}

.lock {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.lock img {
  opacity: 20%;
  width: 10em;
}

.vyzvy:has(.lock) {
  z-index: -1;
  opacity: 40%;
}

@media only screen and (max-width: 900px) {
  .vyzvy {
    font-size: 1.3vw;
  }
}

@media only screen and (max-width: 750px) {
  .vyzvy {
    font-size: unset;
  }
}

@media only screen and (max-width: 500px) {
  .nadpis {
    text-align: center;
  }
}
</style>
