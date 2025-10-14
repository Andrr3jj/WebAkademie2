<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <h1>Moje nákupy</h1>
      <h5>Online objednávky (4)</h5>
      <div class="line horizontal w-80"></div>
      <div class="objednavky-ziadne" v-if="0 == 1">
        <p class="nadpis">Momentálne su vaše nákupy prázdne</p>
        <p class="podnadpis">Ak si kúpite niečo online, nájdete to tu.</p>
      </div>

      <div class="objednavky">
        <div class="objednavka">
          <div class="riadok">
            <p class="item">Číselný zápis</p>
            <p class="info">11.2.2024</p>
          </div>
          <div class="riadok">
            <p class="nazov">Mamko moja mne še cigan ľubi</p>
            <p class="info">10,50€</p>
          </div>
        </div>
        <div class="objednavka">
          <div class="riadok">
            <p class="item">Číselný zápis</p>
            <p class="info">11.2.2024</p>
          </div>
          <div class="riadok">
            <p class="nazov">Mamko moja mne še cigan ľubi</p>
            <p class="info">10,50€</p>
          </div>
        </div>
        <div class="objednavka">
          <div class="riadok">
            <p class="item">Číselný zápis</p>
            <p class="info">11.2.2024</p>
          </div>
          <div class="riadok">
            <p class="nazov">Mamko moja mne še cigan ľubi</p>
            <p class="info">10,50€</p>
          </div>
        </div>
        <div class="button">
          <p>Zobraziť viac</p>
        </div>
      </div>
    </div>
  </section>
  <div :id="$route.name" class="mobile">
    <section>
      <div class="scroll">
        <h1>Moje nákupy</h1>
        <h5>Online objednávky (4)</h5>
        <div class="line horizontal w-80"></div>
        <div class="objednavky-ziadne" v-if="0 == 1">
          <p class="nadpis">Momentálne su vaše nákupy prázdne</p>
          <p class="podnadpis">Ak si kúpite niečo online, nájdete to tu.</p>
        </div>

        <div class="objednavky">
          <div class="objednavka">
            <div class="riadok">
              <p class="item">Číselný zápis</p>
              <p class="info">11.2.2024</p>
            </div>
            <div class="riadok">
              <p class="nazov">Mamko moja mne še cigan ľubi</p>
              <p class="info">10,50€</p>
            </div>
          </div>
          <div class="objednavka">
            <div class="riadok">
              <p class="item">Číselný zápis</p>
              <p class="info">11.2.2024</p>
            </div>
            <div class="riadok">
              <p class="nazov">Mamko moja mne še cigan ľubi</p>
              <p class="info">10,50€</p>
            </div>
          </div>
          <div class="objednavka">
            <div class="riadok">
              <p class="item">Číselný zápis</p>
              <p class="info">11.2.2024</p>
            </div>
            <div class="riadok">
              <p class="nazov">Mamko moja mne še cigan ľubi</p>
              <p class="info">10,50€</p>
            </div>
          </div>
          <div class="button">
            <p>Zobraziť viac</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);
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

  font-size: 2.5em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%; /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

.objednavky-ziadne {
  display: flex;
  height: 60vh;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.nadpis {
  font-size: 1.8em;
  font-weight: 600;
}

.podnadpis {
  font-size: 1.6em;
  font-weight: 400;
  margin-top: 0.7em;
}

.objednavky {
  display: flex;
  flex-direction: column;
  width: 75%;
  max-width: 45em;
  margin: 3em auto;
  align-items: center;

  .button {
    padding: 0.5em 1em;
    margin: 1.3em auto;
    font-size: 1.5em;
  }
}

.item {
  font-size: 1.3em;
  font-weight: 600;
}

.nazov {
  font-size: 1.2em;
  max-width: 65%;
  text-align: left;
}

.info {
  font-size: 1.3em;
}

.objednavka {
  width: 100%;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  border: 0.3em solid #fef35a;
  padding: 0.8em 1.6em 0.5em;
  border-radius: 0.7rem;
  background: linear-gradient(180deg, #90ca50 70%, #fef35a 100%);
  box-shadow: 0px 4px 4px rgba($color: #000000, $alpha: 0.25),
    inset 0px 4px 4px rgba($color: #000000, $alpha: 0.25);

  margin: 0.8em 0;

  &:first-child {
    margin: 0 0 0.8em;
  }
}

.riadok {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

@media only screen and (max-width: 1000px) {
  h5 {
    font-size: 1.8em;
  }
}

@media only screen and (max-width: 750px) {
  .mobile section h1 {
    font-size: 11.5vw !important;
  }
  section .scroll {
    border-radius: 0;
  }

  .mobile section h5 {
    font-size: 6vw !important;
    padding: 0 10%;
    margin: 0.3em auto 0.7em;
  }

  .mobile section p {
    margin: 0;
    font-size: unset;
  }

  .nadpis {
    font-size: 1.4em;
    font-weight: 600;
  }

  .objednavky-ziadne {
    height: 30vh;
  }

  .objednavky {
    width: 95%;
  }

  .objednavka {
    margin: 0.6em 0;
    &:first-child {
      margin: 0 0 0.6em;
    }
  }
  .podnadpis {
    font-size: 1.2em;
    font-weight: 400;
    margin-top: 0.7em;
  }

  .item {
    font-size: 1.1em;
    font-weight: 600;
  }

  .nazov {
    font-size: 1em;
    max-width: 75%;
    text-align: left;
  }

  .info {
    font-size: 1em;
  }
}
</style>
