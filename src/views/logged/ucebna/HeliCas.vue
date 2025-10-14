<template>
  <section
    class="computer vyber"
    :id="$route.name"
    :class="{ hidden: isHidden, 'display-none': isFullyHidden }"
    ref="vyber"
  >
    <HeliHeader
      :userId="userId"
      :cas="mojCas"
      :umiestnenie="mojaPozicia"
      :splnene="splneneUlohy"
      :celkovoUloh="celkovyPocetUloh"
      @show-modal="openModal"
    />
  </section>
  <section class="computer zapisy" :id="$route.name + '1'">
    <div class="scroll" ref="scrollContainer">
      <LeaderboardView />
    </div>
  </section>
  <div :id="$route.name" class="mobile">
    <section>
      <div class="scroll">
        <HeliHeader
          :userId="userId"
          :cas="mojCas"
          :umiestnenie="mojaPozicia"
          :splnene="splneneUlohy"
          :celkovoUloh="celkovyPocetUloh"
          @show-modal="openModal"
        />
      </div>
    </section>
    <section class="zapisy" :id="$route.name + 'zapisimobil'">
      <div class="scroll">
        <LeaderboardMobile />
      </div>
    </section>
  </div>
  <ModalSkeleton
    v-if="modalOpen"
    :content-component="modalComponent"
    :content-props="modalProps"
    @close="modalOpen = false"
  />
</template>

<script>
import HeliHeader from "@/components/Helicas/HeliHeader.vue";
import LeaderboardView from "@/components/Helicas/LeaderboardVIew.vue";
import LeaderboardMobile from "@/components/Helicas/LeaderboardMobile.vue";
import ModalSkeleton from "@/components/Spevnik/modal/ModalSkeleton.vue";
import axios from "axios";

export default {
  components: {
    HeliHeader,
    LeaderboardView,
    LeaderboardMobile,
    ModalSkeleton,
  },
  data() {
    return {
      data: {},
      mojaPozicia: 0,
      mojCas: { dni: 0, hodiny: 0, minuty: 0 },
      // mojeBody: 0, // nepotrebné ak HeliHeader sám ťahá body cez userId
      splneneUlohy: 10,
      celkovyPocetUloh: 40,
      isHidden: false, // Indikátor skrytia
      isFullyHidden: false,
      hasScrolledPast: false, // Zaručí, že sa animácia spustí len raz
      scrollThreshold: 300, // Uroveň skrytia
      showThreshold: 10, // Úroveň, kde sa znova zobrazí
      modalOpen: false,
      modalComponent: null,
      modalProps: {},
    };
  },
  computed: {
    userId() {
      return this.$store?.state?.user?.id || "";
    },
  },
  mounted() {
    window.scrollTo(0, 0);
    this.nacitajData();
    this.celkoveHranie();
    this.nacitajAchievementsStats(); // ➕ počet všetkých
    this.nacitajSplneneAchievements(); // ➕ počet splnených
    this.nacitajMojuPoziciu();
    this.$nextTick(() => {
      const scrollEl = this.$refs.scrollContainer;
      if (scrollEl) {
        scrollEl.addEventListener("scroll", this.handleScroll);
      }
    });
  },
  beforeUnmount() {
    const scrollEl = this.$refs.scrollContainer;
    if (scrollEl) {
      scrollEl.removeEventListener("scroll", this.handleScroll);
    }
  },
  methods: {
    handleScroll(event) {
      const scrollTop = event.target.scrollTop;

      // Ak používateľ prejde prah
      if (scrollTop > this.scrollThreshold && !this.hasScrolledPast) {
        this.isHidden = true;
        this.hasScrolledPast = true;

        // Po krátkom čase nastavíme display: none
        setTimeout(() => {
          if (this.isHidden) {
            this.isFullyHidden = true;
          }
        }, 100);
      }
      // Ak sa používateľ vráti hore
      else if (scrollTop < this.showThreshold && this.hasScrolledPast) {
        this.isHidden = false;
        this.hasScrolledPast = false;
        this.isFullyHidden = false;
      }
    },
    openModal({ component, props }) {
      this.modalComponent = component;
      this.modalProps = props || {};
      this.modalOpen = true;
    },
    async nacitajData() {
      try {
        const config = {
          method: "get",
          maxBodyLength: Infinity,
          url: this.$store.state.api + "/user/stats/getUsersWatchtime.php/",
        };

        const response = await axios.request(config);
        if (response.data.status === "Request succesfull") {
          this.data = response.data.data;

          const sortable = Object.entries(this.data).sort(
            (a, b) => b[1] - a[1]
          );
          const sortedData = {};
          sortable.forEach(([key, value]) => {
            sortedData[key] = value;
          });
          this.data = sortedData;

          this.zistiMojuPoziciu();
        }
      } catch (error) {
        console.error("Chyba pri načítaní watchtime dát:", error);
      }
    },
    zistiMojuPoziciu() {
      const fullName = (
        this.$store.state.user.name +
        " " +
        this.$store.state.user.surname
      ).trim();
      const entries = Object.entries(this.data); // [ [name, value], ... ] sorted
      const idx = entries.findIndex(([name]) => name.trim() === fullName);
      if (idx === -1) {
        this.mojaPozicia = "neumiestnený";
      } else {
        this.mojaPozicia = idx + 1; // pozícia 1-based
      }
    },
    async nacitajAchievementsStats() {
      try {
        const response = await axios.get(
          "https://heligonkovaakademia.sk/api/achievements/getAchievementsStats.php"
        );
        if (response.data.status === "Request succesfull") {
          this.celkovyPocetUloh = +response.data.data.total || 0;
        }
      } catch (error) {
        console.error("Chyba pri načítaní počtu úloh:", error);
      }
    },
    async nacitajSplneneAchievements() {
      try {
        const response = await axios.get(
          "https://heligonkovaakademia.sk/api/achievements/getAchievements.php",
          {
            params: {
              userId: this.userId,
            },
          }
        );
        if (response.data.status === "Request succesfull") {
          const finished = response.data.data.finished;
          const position = response.data.position;

          this.splneneUlohy = typeof finished === "number" ? finished : 0;
          if (position) {
            this.mojaPozicia = position; // prepíš existujúcu pozíciu ak treba
          }
        }
      } catch (error) {
        console.error("Chyba pri načítaní splnených achievementov:", error);
      }
    },
    async nacitajMojuPoziciu() {
      try {
        const response = await axios.get(
          "https://heligonkovaakademia.sk/api/achievements/getRank.php",
          {
            params: {
              userId: this.userId,
            },
          }
        );
        if (response.data.status === "Request succesfull") {
          const pozicia = response.data.data;
          this.mojaPozicia = isNaN(pozicia) ? pozicia : Number(pozicia);
        }
      } catch (error) {
        console.error("Chyba pri načítaní pozície:", error);
      }
    },
    async celkoveHranie() {
      try {
        const config = {
          method: "get",
          maxBodyLength: Infinity,
          url: this.$store.state.api + "/user/stats/getUserWatchtime.php",
        };

        const response = await axios.request(config);
        if (
          response.data.status === "Request succesfull" &&
          response.data.data
        ) {
          const fullName = (
            this.$store.state.user.name +
            " " +
            this.$store.state.user.surname
          ).trim();

          const minuty = Number(response.data.data[fullName]);

          if (!isNaN(minuty)) {
            this.mojCas = this.rozdelitMinuty(minuty);
          } else {
            console.warn("Minúty pre používateľa neboli nájdené:", fullName);
          }
        }
      } catch (error) {
        console.error("Chyba pri načítaní času hrania:", error);
      }
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
@import "@/assets/sass/_colors.scss";

section {
  height: auto;
}

.table-row:hover,
tbody tr:hover {
  background-color: none !important;
}

.vyber {
  height: auto;
  min-height: 18em;
  max-height: 22em;
  padding: 2% 4%;
  z-index: 1;
  margin-right: 20%;
}

.vyber {
  transition: opacity 0.3s ease, transform 0.3s ease; /* Definovanie plynulosti */
}

.vyber {
  opacity: 1;
  transform: translateY(0);
  height: auto; /* Pre normálnu výšku pred animovaním */
  transition: opacity 0.3s ease-out, transform 0.3 ease-out,
    height 0.3s ease-out, min-height 0.3s ease-out;
}

.vyber.hidden {
  opacity: 0;
  transform: translateY(-100%);
  height: 0;
  min-height: 0;
  transition-duration: 0.1s;
}

.vyber.hidden h1,
.vyber.hidden > div {
  transform: scaleY(0); /* Zmenšovanie na vertikálnu nulu */
  transition: transform 0.3s ease-out; /* Plynulý prechod na vertikálne zmenšenie */
}

.vyber.hidden.display-none {
  display: none;
}

.computer.vyber {
  display: flex;
  width: 100%;
  gap: 0%;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.zapisy {
  height: 100%; //70vh
  display: inline-table;
}

.zapisy {
  transition: transform 0.5s ease-in-out;
}

.zapisy {
  overflow: hidden;
  display: flex;
  flex-direction: column;

  .scroll {
    width: 100%;
  }
}

@media screen and (max-width: 1000px) and (min-width: 600px) {
  .computer.vyber {
    font-size: 100%;
  }
}
</style>
