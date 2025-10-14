<template>
  <div v-if="produktyOblubeneDataArr.length !== 0" class="box-item oblubene">
    <div class="nadpis">
      <p>Obľúbené zápisy:</p>
    </div>
    <div class="content">
      <div class="slider">
        <div
          @click="
            oblubene == 0
              ? (oblubene = produktyOblubeneDataArr.length - 1)
              : oblubene--
          "
          class="ovladanie-slider left"
        >
          <img src="@/assets/images/gallery/vlavo.png" alt="Do ľava" />
        </div>
        <div class="box-zapis">
          <img
            :src="
              produktyOblubeneDataArr[oblubene].image ||
              require('@/assets/images/gallery/zapisDefault.png')
            "
            alt="Nazov not"
          />
          <div
            @click="
              $router.push({
                path: 'ucebna/ciselny-zapis',
                query: { cislo: produktyOblubeneDataArr[oblubene].id },
              })
            "
            class="button"
          >
            <img src="@/assets/images/icons/hrat.svg" alt="" />
            <p>Hrať</p>
          </div>
        </div>
        <div
          @click="
            oblubene == produktyOblubeneDataArr.length - 1
              ? (oblubene = 0)
              : oblubene++
          "
          class="ovladanie-slider right"
        >
          <img src="@/assets/images/gallery/vpravo.png" alt="Do prava" />
        </div>
      </div>
      <div class="nazov-piesne">
        {{ produktyOblubeneDataArr[oblubene].name }}
      </div>
    </div>
  </div>
  <div v-else class="box-item oblubene">
    <div class="nadpis">
      <p>Obľúbené zápisy:</p>
    </div>
    <div class="content">
      <div class="slider">
        <div class="ovladanie-slider left">
          <img src="@/assets/images/gallery/vlavo.png" alt="Do ľava" />
        </div>
        <div class="box-zapis">
          <img src="@/assets/images/gallery/zapisDefault.png" alt="Nazov not" />
          <div class="button">
            <img src="@/assets/images/icons/hrat.svg" alt="" />
            <p>Hrať</p>
          </div>
        </div>
        <div class="ovladanie-slider right">
          <img src="@/assets/images/gallery/vpravo.png" alt="Do prava" />
        </div>
      </div>
      <div class="nazov-piesne">Žiadne obľúbené piesne</div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    produktyOblubeneData: {
      type: Array,
      default: () => [],
    },
    oblubenePesnickyID: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      oblubene: 0,
    };
  },
  computed: {
    produktyOblubeneDataArr() {
      // Z objektu urob pole podľa ID v oblubenePesnickyID (zachováš poradie a konzistentnosť)
      return this.oblubenePesnickyID
        .map((id) => this.produktyOblubeneData.find((p) => p && p.id == id))
        .filter(Boolean);
    },
  },
  watch: {
    produktyOblubeneDataArr(newVal) {
      if (this.oblubene >= newVal.length) {
        this.oblubene = 0;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.box-item {
  justify-content: center;
}

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.5vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  // padding: 0.2em 0 0.2em 0.1em;
  padding: 0.02em 0 0.1em 0.1em;
  margin: 0 0 0.05em;
}

//box

.box {
  margin: 0.5em auto;
}

.m-t {
  margin-top: 2em;
}

//cas výučby

.nadpis,
.content {
  display: flex;
  justify-content: space-between;
  flex-direction: row;
  width: 100%;
  margin: 0.01em;
  max-width: 16em;
}

.content > div {
  display: flex;
  flex-direction: column;
}

.content:has(.cas) {
  margin-top: auto;
}

.nadpis p {
  font-size: 1.875em;
  font-weight: 500;
}

.cas-popis {
  justify-content: space-around;

  p {
    font-size: 1.5em;
    font-weight: 500;
    text-align: left;
  }
}

.hviezdy {
  justify-content: flex-end;
}

.hviezda {
  margin: 0.2em 0.1em 0.2em 0.5em;
  width: 2.2em;
}

.cube {
  padding: 0.4em;
  margin: 0.4em 0;
  border: 0.25em solid #fef35a;
  background: #fef35a;
  border-radius: 0.6rem;
  box-shadow: 2.5px 2.5px 5px rgba($color: #000000, $alpha: 0.35),
    inset 2px 2px 10px rgba($color: #000000, $alpha: 0.25);

  width: 2.8em;
  height: 2.8em;
  display: flex;
  align-items: center;
  justify-content: center;

  p {
    font-family: Adumu, Bahnshrift, sans-serif;
    font-weight: 400;
    font-size: 1.7em;
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

//info / popis
.nadpis {
  position: relative;
}

.popis {
  position: absolute;
  right: 0;
  top: 0;

  img {
    width: 1.6em;
  }

  & .info-active {
    width: 3.2em;
    margin-right: -0.6em;
    margin-top: -0.5em;
  }

  &:hover {
    cursor: pointer;
    transform: scale(1.1);
  }
}

.popis-active {
  position: absolute;
  width: 110%;
  background-color: #8ec84e;
  box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.25);
  border-radius: 1rem;
  top: 105%;
  left: 50%;
  transform: translateX(-50%);
  padding: 1.3em 0.8em;

  p {
    font-size: 1em;
  }
}

.popis-deactive {
  display: none;
}

//statistika zápisov

.stlpec {
  justify-content: flex-end;
  background: linear-gradient(180deg, #00913f 30%, #fef35a 100%);
  border-radius: 2em;
  padding: 0.6em;
  border: 0.25em solid #fef35a;
  box-shadow: 2.5px 2.5px 5px rgba($color: #000000, $alpha: 0.35),
    inset 2px 2px 10px rgba($color: #000000, $alpha: 0.25);
  box-sizing: border-box;
  min-height: 20%;
  min-width: 2.6em;

  p {
    font-size: 1em;
  }
}

#valcik {
  background: linear-gradient(180deg, #90ca50 50%, #fef35a 100%);
}

.content:has(.stlpec) {
  align-items: flex-end;
  height: 100%;
  justify-content: flex-start;
  gap: 5%;
}

.typ {
  display: flex;
  gap: 10%;
  justify-content: flex-start;
  align-items: center;
  margin: 0.2em;
  width: 100%;

  img {
    width: 1.3em;
    filter: drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.25));
  }

  p {
    font-size: 1.1em;
    font-weight: 600;
  }
}

//oblubene zápisiy

.oblubene {
  .content {
    flex-direction: column;
    margin-right: 10px;
  }

  .slider {
    flex-direction: row;
  }

  .nadpis {
    justify-content: center;
  }
}

.box-zapis {
  background: linear-gradient(180deg, #90ca50 50%, #fef35a 100%);
  box-shadow: 0px 0px 4px rgba($color: #000000, $alpha: 0.25);
  border-radius: 1.5em;
  margin-top: 3em;
  border: 0.25em solid #fef35a;

  & > img {
    width: 7em;
    margin: -2em 0 0.6em;
  }

  .button {
    font-size: 1.3em;
    border-radius: 0.8rem;
    padding: 0.4em 1.6em;
    transform: scale(1.3);

    img {
      width: 0.7em;
    }

    p {
      font-size: 1em;
    }
  }
}

.ovladanie-slider {
  justify-content: center !important;
  filter: drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.35));
  cursor: pointer;
  transition-duration: 0.2s;

  img {
    width: 3.85em;
    margin-top: 2em;
  }

  &:hover {
    transform: scale(1.2);
    transition-duration: 0.2s;
  }
}

.nazov-piesne {
  margin: 0.8em auto 0;
  width: 80%;
  font-size: 1.4em;
  font-weight: 500;
}

//statistika
.statistika {
  flex: 2;
  align-items: center;
  margin-top: 76px;
  justify-content: flex-end;

  .zvladnute {
    background: #fef35a;
    padding: 0.5em 2em 0.4em;
    border-radius: 2rem;
    filter: drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.35));
    margin: 0 auto 2em 8vw;

    display: flex;
    justify-content: flex-end;

    width: 65%;
    max-width: 41em;
    min-width: 25em;

    p {
      font-size: 1.4em;
      font-weight: 800;
      padding-right: 20px;
    }
  }
}

//naposledy sledované

.naposledy-sledovane {
  .nadpis {
    justify-content: center;
  }

  .content {
    align-items: center;
    margin: 20px 0 0 5px;
  }

  .box-kurz {
    background: linear-gradient(180deg, #90ca50 50%, #fef35a 100%);
    box-shadow: 0px 0px 4px rgba($color: #000000, $alpha: 0.25);
    border-radius: 1em;
    border: 0.25em solid #fef35a;

    & > img {
      width: 7em;
      margin-bottom: -0.6em;
    }

    .button {
      font-size: 1.1em;
      border-radius: 0.8rem;
      padding: 0.4em 1.6em;
      transform: scale(1.3);

      img {
        width: 0.7em;
      }

      p {
        font-size: 1em;
      }
    }
  }
}

//andrej + navigácia (fixed)
.fixed {
  // position: fixed;
  // bottom: 0;
  position: sticky;
  bottom: 5vh;
  display: flex;
  align-items: flex-end;
  width: 100%;

  img {
    position: absolute;
    width: 24vw;
    max-width: 27em;
    left: -3vw;
    z-index: 3;
  }

  .navigation-ucet {
    width: 80%;
    background: #fef35a;
    padding: 0.6em 2em 0.5em;
    border-radius: 2rem;
    filter: drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.35));
    margin: 1em auto 3em 8%;
    z-index: 0;
    display: flex;
    justify-content: space-around;

    a:hover {
      transform: scale(1.1);
      transition-duration: 0.2s;
      cursor: pointer;
    }

    & a:first-child {
      margin-left: 20%;
    }

    a {
      font-size: 1.4em;
      font-weight: 800;
    }
  }
}

.settings-inline {
  display: flex;
  margin-bottom: 2.5em;
  margin-right: 1em;
  cursor: pointer;

  svg {
    width: 3.19em;
    height: 3.5em;
  }
}

@media only screen and (max-width: 1280px) {
  .settings-inline {
    margin-right: 0;
  }
}

//optimalizácia pre tablet

@media only screen and (max-width: 1200px) {
  .statistika .zvladnute {
    margin: 1.5em 0 4px 80px;
  }

  .statistika {
    justify-content: center;
    margin-top: 10%;
  }

  .box {
    gap: 1%;
  }

  .naposledy-sledovane .conten {
    align-items: center;
    margin: 20px 28px 0 0;
  }

  .navigation-ucet {
    & a:first-child {
      margin-left: 28%;
    }
  }
}

@media only screen and (max-width: 1050px) {
  .statistika {
    margin-top: 14%;
  }

  .naposledy {
    margin-top: 20px;
  }
}

@media only screen and (max-width: 1000px) {
  .box-item {
    margin: 1.5em 0;
  }

  .content:has(.stlpec) {
    height: 15em;
  }

  .box:not(.m-t) {
    flex-direction: column-reverse;
  }

  .nadpis {
    margin-bottom: 1em;
  }

  .fixed .navigation-ucet a {
    font-size: 1.2em;
  }

  .fixed .navigation-ucet {
    width: 68.5%;
    transform: translateX(10%);
  }
}

//optimalizácia pre mobil

@media only screen and (max-width: 1500px), (max-height: 750px) {
  .box:not(.m-t) {
    margin-bottom: 5em;
  }
}

.mobile {
  .fixed .navigation-ucet {
    margin: 1.4em 2%;
    width: 96%;
    padding: 0.4em 0.6em;
    box-sizing: border-box;
    transform: translateX(0);
  }

  .fixed .navigation-ucet a:first-child {
    margin: 0;
  }

  .statistika .zvladnute p {
    font-size: 1.4em;
    font-weight: 600;
    padding-right: 0;
  }

  .fixed .navigation-ucet a {
    font-size: 1.4em;
    font-weight: 600;
  }

  .mobile {
    padding-bottom: 7em;
  }

  .fixed img {
    width: 44vw;
    bottom: 3.4em;
    left: -2vw;
  }

  .naposledy-sledovane {
    margin: 1em 0 4em;
  }

  .fixed {
    bottom: 0;
  }

  .box:not(.m-t) {
    margin-bottom: 0.5em;
  }

  .statistika .zvladnute {
    filter: drop-shadow(5px 5px 4px rgba(0, 0, 0, 0.25));
    margin: 2em 1em 0 auto;
  }

  .box-item.statistika {
    font-size: 2.1vw;
  }

  .box-zapis .button,
  .box-kurz .button {
    padding: 0.2em 1.6em;
    margin-top: 0;

    p {
      font-size: 1.2em;
    }
  }

  .box-item {
    margin: 1em 0;
  }
}

@media only screen and (max-width: 830px) {
  .mobile {
    display: block !important;
    padding-bottom: 7em;
  }
  .settings-inline {
    margin-bottom: 1em;
  }
}

@media only screen and (max-width: 450px) {
  .mobile {
    .fixed .navigation-ucet a {
      font-size: 1em;
    }
  }
}
</style>
