<template>
  <div class="zapisy-zoznam" :class="{ active: !layouts }">
    <JedenZapis
      v-for="(produktId, index) in this.produktyID"
      :key="index"
      :produktyID="produktyID[index]"
      :produktyData="produktyData[produktyID[index]]"
      :layouts="layouts"
      :creditBuy="creditBuy"
      :multiplier="multiplier"
    />
  </div>
</template>

<script>
import JedenZapis from "@/components/Zapisy/JedenZapis.vue";
export default {
  components: {
    JedenZapis,
  },
  data() {
    return {
      multiplier: null,
    };
  },
  props: {
    produktyID: {
      type: Array,
      default: () => [],
    },
    produktyData: {
      type: Object,
      default: () => ({}),
    },
    layouts: {
      type: Boolean,
      default: true,
    },
    creditBuy: {
      type: Boolean,
      default: () => false,
    },
  },
  methods: {
    async getCreditMultiplier() {
      try {
        const axios = require("axios");
        const url = `${this.$store.state.api}/helitime/config/helitime.config.json`;
        const { data } = await axios.get(url);

        const raw = data?.helitime_to_eur_value_multiplier;
        this.multiplier =
          raw !== undefined && raw !== null ? Number(raw) : null;

        if (Number.isNaN(this.multiplier)) {
          console.warn("helitime_to_eur_value_multiplier nie je číslo:", raw);
          this.multiplier = null;
        }
      } catch (err) {
        console.error("Chyba pri sťahovaní konfigurácie:", err);
        this.multiplier = null;
      }
    },
  },
  watch: {
    creditBuy: {
      immediate: true, // spustí sa aj na začiatku, ale handler si to ošéfuje
      handler(newVal, oldVal) {
        // zavolaj iba keď sa prepla na true a multiplier ešte nemáme
        if (
          newVal &&
          !oldVal &&
          (this.multiplier === null || this.multiplier === 0)
        ) {
          this.getCreditMultiplier();
        }
      },
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";
// sekcia zapisy

.zapisy-zoznam {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-start;
}

.active {
  display: flex;
  width: 100%;
  flex-direction: column;

  padding: 0.4em 0em 0 1.2em;
  box-sizing: border-box;
}
</style>
