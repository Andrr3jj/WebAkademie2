<template>
  <section class="computer zapisy" :id="$route.name">
    <div class="scroll" ref="scrollContainer">
      <HelibodyHeader />
      <HelibodyLeaderboard
        :items="starsList"
        :disableFetch="true"
        :totalAchievementsProp="0"
        :limit="30"
      />
    </div>
  </section>
  <div :id="`${$route.name}-m`" class="mobile">
    <section>
      <div class="scroll" ref="scrollContainerMobile">
        <HelibodyHeader />
        <HelibodyLeaderboardMobile />
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
import HelibodyHeader from "@/components/Edupage/HeliBody/HelibodyHeader.vue";
import HelibodyLeaderboard from "@/components/Edupage/HeliBody/HelibodyLeaderBoard.vue";
import HelibodyLeaderboardMobile from "@/components/Edupage/HeliBody/HelibodyLeaderBoardMobile.vue";
import ModalSkeleton from "@/components/Spevnik/modal/ModalSkeleton.vue";
import axios from "axios";

export default {
  components: {
    HelibodyHeader,
    HelibodyLeaderboard,
    HelibodyLeaderboardMobile,
    ModalSkeleton,
  },
  data() {
    return {
      // Jediný zdroj pravdy pre leaderboard aj štatistiky
      starsList: [],
      loading: false,
      loadError: null,

      // Lokálne UI stavy
      mojaPozicia: 0,
      mojCas: { dni: 0, hodiny: 0, minuty: 0 },
      isHidden: false,
      isFullyHidden: false,
      hasScrolledPast: false,
      scrollThreshold: 300,
      showThreshold: 10,

      modalOpen: false,
      modalComponent: null,
      modalProps: {},
    };
  },
  computed: {
    apiBase() {
      return this.$store?.state?.api || "";
    },
    userId() {
      return this.$store?.state?.user?.id || "";
    },
    // Nájdeme záznam prihláseného používateľa v zozname
    meEntry() {
      if (!this.userId || !Array.isArray(this.starsList)) return null;
      return (
        this.starsList.find((u) => String(u.id) === String(this.userId)) || null
      );
    },
  },
  mounted() {
    window.scrollTo(0, 0);
    this.fetchStars();

    // Scroll listenery (desktop + mobile)
    this.$nextTick(() => {
      const desktop = this.$refs.scrollContainer;
      if (desktop) desktop.addEventListener("scroll", this.handleScroll);

      const mobile = this.$refs.scrollContainerMobile;
      if (mobile) mobile.addEventListener("scroll", this.handleScroll);
    });
  },
  beforeUnmount() {
    const desktop = this.$refs.scrollContainer;
    if (desktop) desktop.removeEventListener("scroll", this.handleScroll);

    const mobile = this.$refs.scrollContainerMobile;
    if (mobile) mobile.removeEventListener("scroll", this.handleScroll);
  },
  methods: {
    // ===== API =====
    async fetchStars() {
      this.loading = true;
      this.loadError = null;
      try {
        const url = `${this.apiBase}/edupage/listStars.php`;
        const { data } = await axios.get(url);
        if (
          data?.status === "Request succesfull" &&
          Array.isArray(data?.data)
        ) {
          // Zachovaj poradie z API (bez preusporiadania)
          this.starsList = (data.data || []).map((u) => ({
            ...u,
            calculatedStars: this.calculateStarsFromHelitime(
              this.asNum(u.helitime, 0)
            ),
          }));

          // Vypočítaj pozíciu a odohraný čas pre mňa
          this.computeMyStats();
        } else {
          this.loadError = "Neočakávaná štruktúra odpovede.";
        }
      } catch (e) {
        this.loadError = e?.message || String(e);
      } finally {
        this.loading = false;
      }
    },

    computeMyStats() {
      const me = this.meEntry;
      if (!me) {
        this.mojaPozicia = "neumiestnený";
        this.mojCas = { dni: 0, hodiny: 0, minuty: 0 };
        return;
      }

      // Pozícia podľa poradia v zozname z API
      const idx = this.starsList.findIndex(
        (u) => String(u.id) === String(this.userId)
      );
      this.mojaPozicia = idx >= 0 ? idx + 1 : "neumiestnený";

      const helitimeMinutes = this.asNum(me.helitime, 0);
      this.mojCas = this.minutesToDHM(helitimeMinutes);
    },

    // ===== Utility =====
    asNum(v, fallback = 0) {
      if (v === null || v === undefined || v === "") return fallback;
      const n = Number(v);
      return Number.isFinite(n) ? n : fallback;
    },
    minutesToDHM(totalMinutes) {
      const dni = Math.floor(totalMinutes / (24 * 60));
      const remH = totalMinutes % (24 * 60);
      const hodiny = Math.floor(remH / 60);
      const minuty = remH % 60;
      return { dni, hodiny, minuty };
    },
    calculateStarsFromHelitime(mins) {
      if (mins >= 14400) return 5;
      if (mins >= 7200) return 4;
      if (mins >= 2880) return 3;
      if (mins >= 1440) return 2;
      if (mins >= 480) return 1;
      return 0;
    },

    // ===== UI =====
    handleScroll(event) {
      const scrollTop = event.target.scrollTop || 0;
      if (scrollTop > this.scrollThreshold && !this.hasScrolledPast) {
        this.isHidden = true;
        this.hasScrolledPast = true;
        setTimeout(() => {
          if (this.isHidden) this.isFullyHidden = true;
        }, 100);
      } else if (scrollTop < this.showThreshold && this.hasScrolledPast) {
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
