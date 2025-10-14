<template>
  <div class="statistika-wrapper">
    <div class="statistika-header">
      <p>Štatistika zápisov:</p>
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
          Tu nájdeš štatistiku svojich piesní, kde si môžeš prezrieť počet
          číselných zápisov s valčíkovým a polkovým rytmom. Snaž sa udržiavať
          vyvážený počet zápisov, aby si sa rovnomerne zlepšoval/a nielen vo
          valčíku, ale aj v polke.
        </p>
      </div>
    </div>

    <div class="statistika-content">
      <div class="stlpec" :style="{ width: sirkaPolka + '%' }" id="polka"></div>
      <div
        class="stlpec"
        :style="{ width: sirkaValcik + '%' }"
        id="valcik"
      ></div>

      <div class="legenda">
        <div class="typ">
          <img src="@/assets/images/icons/elipsaPolka.svg" alt="Silno zelená" />
          <p>
            Polka <span>({{ hodnotaPolka }})</span>
          </p>
        </div>
        <div class="typ">
          <img
            src="@/assets/images/icons/elipsaValcik.svg"
            alt="Slabo zelená"
          />
          <p>
            Valcík <span>({{ hodnotaValcik }})</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "RecordStats",
  props: {
    hodnotaPolka: {
      type: Number,
      default: 0,
    },
    hodnotaValcik: {
      type: Number,
      default: 0,
    },
    sirkaPolka: {
      type: Number,
      default: 50,
    },
    sirkaValcik: {
      type: Number,
      default: 50,
    },
  },
  data() {
    return {
      popisOtvor: false,
    };
  },
};
</script>

<style scoped lang="scss">
.statistika-wrapper {
  margin: 1.5em 0;
  width: 20em;
}

.statistika-header {
  position: relative;
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  width: 112%;
  max-width: 23em;

  p {
    font-size: 1.6em;
    font-weight: 500;
  }
}

.tooltip-icon {
  position: absolute;
  right: 4em;
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
  width: 104%;
  background-color: #8ec84e;
  box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.25);
  border-radius: 1rem;
  top: 105%;
  left: 45%;
  transform: translateX(-50%);
  padding: 1.3em 0.8em;
  z-index: 2;

  p {
    font-size: 1.4em;
  }
}

.tooltip-hidden {
  display: none;
}

.statistika-content {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 1em;
  height: 100%;
  justify-content: flex-start;
  margin-top: 0.5em;
}

.stlpec {
  justify-content: flex-end;
  background: linear-gradient(270deg, #00913f 30%, #fef35a 100%);
  border-radius: 2em;
  padding: 0.8em;
  border: 0.25em solid #fef35a;
  box-shadow: 2.5px 2.5px 5px rgba(0, 0, 0, 0.35),
    inset 2px 2px 10px rgba(0, 0, 0, 0.25);
  box-sizing: border-box;
  min-height: 20%;
  min-width: 2.6em;

  p {
    font-size: 1em;
    font-weight: bold;
  }
}

#valcik {
  background: linear-gradient(270deg, #90ca50 50%, #fef35a 100%);
}

.legenda {
  display: flex;
  justify-content: flex-end;
  gap: 1em;
  margin-top: 0.5em;

  .typ {
    display: flex;
    gap: 0.5em;
    align-items: center;

    img {
      width: 1.7em;
      filter: drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.25));
    }

    p {
      font-size: 1.4em;
      font-weight: 400;
    }
  }
}

@media screen and (max-width: 750px) {
  .statistika-wrapper {
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .tooltip-icon {
    right: 1em;
  }
}
</style>
