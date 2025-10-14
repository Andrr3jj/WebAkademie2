<template>
  <div class="time-stats-wrapper">
    <div class="headline">
      <p>Aktuálny čas výučby:</p>
      <div class="tooltip-icon">
        <img
          v-if="!popisOtvor"
          @mouseenter="popisOtvor = true"
          src="@/assets/images/icons/info.svg"
          alt="Doplňujúce info"
        />
        <img
          v-if="popisOtvor"
          @mouseenter="popisOtvor = true"
          @mouseleave="popisOtvor = false"
          src="@/assets/images/icons/info-active.svg"
          alt="Doplňujúce info"
          class="info-active"
        />
      </div>
      <div
        @mouseenter="popisOtvor = true"
        @mouseleave="popisOtvor = false"
        :class="popisOtvor ? 'tooltip-active' : 'tooltip-hidden'"
      >
        <p>
          Na stránke sa automaticky zaznamenáva čas výučby každého, kto sleduje
          číselné zápisy alebo náučné videá. Tento čas sa premieňa na body,
          ktoré s môžeš vymeniť za číselné zápisiy. Všetko nájdeš v sekcii
          HeliČas, kde si môžeš zároveň porovnáť svoj pokrok s ostatnými žiakmi
          v prehľadnej tabuľke.
        </p>
      </div>
    </div>

    <time-play :dobaHranie="dobaHranie" />
  </div>
</template>

<script>
import TimePlay from "./TimePlay.vue";
export default {
  name: "TimeStats",
  components: {
    TimePlay,
  },
  props: {
    dobaHranie: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      popisOtvor: false,
    };
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.time-stats-wrapper {
  margin: 1.5em 0;
  display: flex;
  flex-direction: column;
  width: 20em;
}

.headline {
  position: relative;
  display: flex;
  justify-content: space-between;
  flex-direction: row;
  max-width: 17.2em;
  width: 100%;

  p {
    font-size: 1.6em;
    font-weight: 500;
  }
}

.tooltip-icon {
  position: absolute;
  right: -1.7em;
  top: 0;

  img {
    width: 1.6em;
  }

  .info-active {
    width: 3.2em;
    margin-right: -0.6em;
    margin-top: -0.5em;
  }

  &:hover {
    cursor: pointer;
    transform: scale(1.1);
  }
}

.tooltip-active {
  position: absolute;
  width: 134%;
  background-color: #8ec84e;
  box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.25);
  border-radius: 1em;
  top: 110%;
  left: 58%;
  transform: translateX(-50%);
  padding: 1.3em 0.8em;

  p {
    font-size: 1.4em;
  }
}

.tooltip-hidden {
  display: none;
}

@media screen and (max-width: 750px) {
  .time-stats-wrapper {
    width: 100%;
    align-items: center;
  }
}
</style>
