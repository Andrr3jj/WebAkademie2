<template>
  <section
    class="computer vyber"
    :id="$route.name"
    :class="{ hidden: isHidden, 'display-none': isFullyHidden }"
    ref="vyber"
  >
    <h1>
      Heli <br />
      Shop
    </h1>

    <div class="control">
      <h5>Všetko pre heligónku na jednom mieste.</h5>

      <div class="line horizontal"></div>

      <p class="podnadpis">Učebné materiály, doplnky aj originálny merch.</p>

      <div class="categories">
        <div class="buttons">
          <div
            @click="
              category == 'knizky' ? (category = '') : (category = 'knizky');
              this.resetujProduktyANacitaj();
            "
            class="button"
            :class="{ 'active-category': category === 'knizky' }"
          >
            <p>Knihy</p>
          </div>
        </div>
        <div class="buttons">
          <div
            @click="
              category == 'vaky' ? (category = '') : (category = 'vaky');
              this.resetujProduktyANacitaj();
            "
            class="button"
            :class="{ 'active-category': category === 'vaky' }"
          >
            <p>Vaky</p>
          </div>
        </div>
        <div class="buttons">
          <div
            @click="
              category == 'remene' ? (category = '') : (category = 'remene');
              this.resetujProduktyANacitaj();
            "
            class="button"
            :class="{ 'active-category': category === 'remene' }"
          >
            <p>Remene</p>
          </div>
        </div>
        <div class="buttons">
          <div
            @click="
              category == 'oblecenie'
                ? (category = '')
                : (category = 'oblecenie');
              this.resetujProduktyANacitaj();
            "
            class="button"
            :class="{ 'active-category': category === 'oblecenie' }"
          >
            <p>Merch</p>
          </div>
        </div>
      </div>
    </div>

    <div class="image">
      <img
        src="@/assets/images/gallery/jurajMerch.png"
        alt="Obrázok mikiny heligonkovej akadémii"
        class="noty"
      />
    </div>
  </section>

  <section class="computer zapisy" :id="$route.name + '1'">
    <div class="scroll">
      <p class="search-nadpis">Všetky dostupné produkty</p>
      <div class="categories-search">
        <div class="search">
          <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
          <input v-model="search" type="text" placeholder="Vyhľadať produkt" />
        </div>
        <!-- <div class="layouts">
          <img src="@/assets/images/icons/filter.png" alt="Filtrovanie" />
        </div> -->
      </div>
      <zoznam-produktov
        :search="search"
        :filter="filter"
        :category="category"
      />

      <!-- <div
        @click="nacitajViacProduktov()"
        v-if="nacitatViacej && ziadnePiesneText"
        class="button more"
      >
        <p>Načítať ďalšie</p>
      </div> -->
    </div>
  </section>

  <div class="mobile" :id="$route.name + 'mobil'">
    <div class="scroll">
      <section class="vyber">
        <h1>Heli Shop</h1>

        <div class="control">
          <h5>Všetko pre heligónku na jednom mieste.</h5>

          <div class="line horizontal"></div>

          <p class="podnadpis">
            Učebné materiály, doplnky aj originálny merch.
          </p>

          <div class="categories">
            <div class="buttons">
              <div
                @click="
                  category == 'knizky'
                    ? (category = '')
                    : (category = 'knizky');
                  this.resetujProduktyANacitaj();
                "
                class="button"
                :class="{ 'active-category': category === 'knizky' }"
              >
                <p>Knihy</p>
              </div>
            </div>
            <div class="buttons">
              <div
                @click="
                  category == 'vaky' ? (category = '') : (category = 'vaky');
                  this.resetujProduktyANacitaj();
                "
                class="button"
                :class="{ 'active-category': category === 'vaky' }"
              >
                <p>Vaky</p>
              </div>
            </div>
            <div class="buttons">
              <div
                @click="
                  category == 'remene'
                    ? (category = '')
                    : (category = 'remene');
                  this.resetujProduktyANacitaj();
                "
                class="button"
                :class="{ 'active-category': category === 'remene' }"
              >
                <p>Remene</p>
              </div>
            </div>
            <div class="buttons">
              <div
                @click="
                  category == 'oblecenie'
                    ? (category = '')
                    : (category = 'oblecenie');
                  this.resetujProduktyANacitaj();
                "
                class="button"
                :class="{ 'active-category': category === 'oblecenie' }"
              >
                <p>Merch</p>
              </div>
            </div>
          </div>
        </div>

        <div class="image">
          <img
            src="@/assets/images/gallery/jurajMerch.png"
            alt="Obrázok mikiny heligonkovej akadémii"
            class="noty"
          />
        </div>
      </section>

      <section class="zapisy" :id="$route.name + 'zapisimobil'">
        <p class="search-nadpis">Všetky dostupné produkty</p>
        <div class="categories-search">
          <div class="search">
            <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
            <input
              v-model="search"
              type="text"
              placeholder="Vyhľadať produkt"
            />
          </div>
          <!-- <div class="layouts">
            <img src="@/assets/images/icons/filter.png" alt="Filtrovanie" />
          </div> -->
        </div>
        <zoznam-produktov
          :search="search"
          :filter="filter"
          :category="category"
        />
        <!-- <div
          @click="nacitajViacProdutakktov()"
          v-if="nacitatViacej && ziadnePiesneText"
          class="button more"
        >
          <p>Načítať ďalšie</p>
        </div> -->
      </section>
    </div>
  </div>

  <section class="zapisy tablet" :id="$route.name + 'tablet'">
    <div class="scroll" id="vnorenyScroll">
      <p class="search-nadpis">Všetky dostupné produkty</p>
      <div class="categories-search">
        <div class="search">
          <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
          <input v-model="search" type="text" placeholder="Vyhľadať produkt" />
        </div>
        <!-- <div class="layouts">
          <img src="@/assets/images/icons/filter.png" alt="Filtrovanie" />
        </div> -->
      </div>
      <zoznam-produktov
        :search="search"
        :filter="filter"
        :category="category"
      />
      <!-- <div
        @click="nacitajViacProduktov()"
        v-if="nacitatViacej && ziadnePiesneText"
        class="button more"
      >
        <p>Načítať ďalšie</p>
      </div> -->
    </div>
  </section>
</template>

<script>
import ZoznamProduktov from "../../components/Produkty/ZoznamProduktov.vue";

export default {
  components: {
    ZoznamProduktov,
  },

  mounted() {
    window.scrollTo(0, 0);

    //bolo tu kvoli tou že sa to len adminom zobrazovalo
    // if (!this.$store.state.userRoles?.roles?.includes("admin_master")) {
    //   this.$router.push("/pripravujeme");
    // }

    const scrollElement = document.querySelector(".scroll"); // Nájdenie elementu
    if (scrollElement) {
      scrollElement.addEventListener("scroll", this.handleScroll);
    }
  },

  beforeUnmount() {
    const scrollElement = document.querySelector(".scroll");
    if (scrollElement) {
      scrollElement.removeEventListener("scroll", this.handleScroll);
    }
  },

  methods: {
    handleScroll(event) {
      const scrollTop = event.target.scrollTop;

      // Ak používateľ prejde prah
      if (scrollTop > this.scrollThreshold && !this.hasScrolledPast) {
        this.isHidden = true;
        this.hasScrolledPast = true;

        // Po 500ms nastavíme display: none
        setTimeout(() => {
          if (this.isHidden) {
            this.isFullyHidden = true; // Po animácii nastavíme display: none
          }
        }, 300); // Trvanie animácie
      }
      // Ak sa používateľ vráti hore
      else if (scrollTop < this.showThreshold && this.hasScrolledPast) {
        this.isHidden = false;
        this.hasScrolledPast = false;

        // Resetujeme, aby element bol okamžite viditeľný
        this.isFullyHidden = false;
      }
    },
    handleSpustenieHelishopu() {
      this.heliShopSpusteny = true;
      // Tu môžeš urobiť redirect, fetch, otvorenie komponentu atď.
    },
  },

  data() {
    return {
      category: "",
      search: "",
      filter: "",
      isHidden: false, // Indikátor skrytia
      isFullyHidden: false,
      hasScrolledPast: false, // Zaručí, že sa animácia spustí len raz
      scrollThreshold: 300, // Uroveň skrytia
      showThreshold: 10, // Úroveň, kde sa znova zobrazí

      heliShopSpusteny: false,
    };
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

h1 {
  text-align: left;
  font-size: 3.125em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 3.59375rem */
  letter-spacing: 0.25rem;
  z-index: 1;

  margin: 0;

  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 3px rgba(0, 0, 0, 0.25);
  font-size: 5vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0.1em 0.15em 0.1em;
  margin: 0;
}

h5 {
  color: #001;
  font-size: 2vw;
  margin: auto;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
}

.podnadpis {
  color: #001;
  font-size: 1.4vw;
  font-style: normal;
  font-weight: 500;
  line-height: 115%;
  margin: auto;
}

.search-nadpis {
  color: #001;
  font-size: 1.9em;
  font-style: normal;
  font-weight: 800;
  margin: 0.5em auto 0.5em 0.5em;
  width: max-content;
}

.vyber {
  height: auto;
  min-height: 18em;
  max-height: 22em;
  padding: 2% 4%;
  z-index: 1;

  margin-right: 20%;
  padding-right: 18%;
}

.vyber {
  transition: opacity 0.5s ease, transform 0.5s ease; /* Definovanie plynulosti */
}

.vyber {
  opacity: 1;
  transform: translateY(0);
  height: auto; /* Pre normálnu výšku pred animovaním */
  transition: opacity 0.5s ease-out, transform 0.5s ease-out,
    height 0.5s ease-out, min-height 0.5s ease-out;
}

.vyber.hidden {
  opacity: 0;
  transform: translateY(-100%); /* Posun k hornej hranici pre skrytie */
  height: 0;
  min-height: 0;
  transition-duration: 0.5s;
}

.vyber.hidden h1,
.vyber.hidden > div {
  transform: scaleY(0); /* Zmenšovanie na vertikálnu nulu */
  transition: transform 0.3s ease-out; /* Plynulý prechod na vertikálne zmenšenie */
}

.vyber.hidden.display-none {
  display: none; /* Nastavenie display: none po dokončení animácie */
}

.computer.vyber {
  display: flex;
  width: 100%;
  gap: 0%;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.control {
  display: flex;
  align-items: flex-start;
  justify-content: space-around;
  flex-direction: column;
  gap: 2%;
  width: 100%;
  height: auto;

  position: relative;
  height: 100%;
}

.categories-search,
.categories {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  flex-direction: row;
  font-size: 90%;
}

.categories {
  font-size: 80%;
  width: 95%;
  margin: auto;
}

h5,
.podnadpis,
.categories {
  margin: 0 auto;
}

// .categories-search .button {
//   margin-right: auto;
// }

.search {
  border-radius: 1.5625rem;
  border: 6px solid #fef35a;
  background: rgba(233, 233, 233, 0.93);
  box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.25);

  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0 0.5em;
  width: 50%;
  max-width: 35em;
  margin: 0 0 0.8em;

  img {
    width: 2em;
  }

  input {
    width: 100%;
    font-size: 1.25em;
    font-style: normal;
    font-weight: 400;
    line-height: 137.53%; /* 1.71913rem */
    letter-spacing: 0.0625rem;
  }
}

.layouts {
  display: flex;
  margin: 0 auto 0.8em 1.5em;
  gap: 15%;
  align-items: center;
  justify-content: flex-start;
  cursor: pointer;
  transition-duration: 0.3s;

  & > img {
    width: 3.5em;
    height: auto;
    box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.25);
    border-radius: 0.6rem;

    &:hover {
      transform: scale(1.1);
      transition-duration: 0.2s;
      box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.35);
    }
  }
}

.button {
  font-size: 1.375em;
  font-style: normal;
  font-weight: 400;
  line-height: 137.53%; /* 1.89106rem */
  letter-spacing: 0.06875rem;
  width: 7em;
  align-items: center;
  justify-content: center;
  margin: 0.9em 0;
  padding: 0.5em 1.5em;
  white-space: nowrap;
}

.more {
  margin: 1.5em auto !important;
  padding-left: 2em;
  padding-right: 2em;
  width: 7em !important;
}

.active-category {
  background: #90ca50;
}

.noty {
  position: absolute;
  width: 14em;
  bottom: 5.5em;
  left: 85%;
}

.ghost {
  opacity: 0;
  z-index: -1;
}

//optimalizácia pre Mobil

.computer {
  display: block;
}

.zapisy {
  height: 100%; //70vh
  display: inline-table;
}

/* Keď je .vyber skrytý, posunieme .zapisy hore */
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

.mobile,
.tablet {
  display: none;
  padding-bottom: 0;
}

#vnorenyScroll {
  padding-bottom: 0;
}

.image {
  position: absolute;
  right: 0;
  bottom: 5%;
  top: -5%;
  transform: scale(1.1);
  margin-right: -3em;
}

.image img {
  position: unset;
  height: -webkit-fill-available;
  width: auto;
}

.mobile {
  input {
    font-size: 1.3em;
    font-style: normal;
    font-weight: 400;
    line-height: 137.53%; /* 1.11744rem */
    letter-spacing: 0.04063rem;
  }
  .vyber {
    height: auto;
    font-size: 100%;
    min-height: 11em;
  }

  h1 {
    text-align: center;
    font-size: 5.5vw;
    font-style: normal;
    font-weight: 400;
    line-height: 115%; /* 1.725rem */
    letter-spacing: 0.045rem;
    margin-top: 0.1em;
  }

  .control {
    padding: 0 5% 3%;
  }

  .search {
    width: 100%;
    max-width: unset;
  }

  .layouts {
    margin: 0 0 0.5em 0;
    width: 15%;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;

    & > img {
      width: 3em;
    }
  }

  .button {
    padding: 0.4em 2em;
  }

  .button p {
    font-size: 1.2em;
  }

  .control {
    flex-direction: column;
    height: auto;
  }

  .categories {
    flex-wrap: wrap;
    gap: 3%;
    font-size: 95% !important;
    // justify-content: space-evenly;
  }

  h5 {
    font-size: 2.8vw;
    margin: 0.2em auto;
  }

  .podnadpis {
    font-size: 2vw;
    margin: 0.8em auto 0;
  }
}

@media screen and (max-width: 1000px) {
  .mobile {
    display: block;
  }

  .computer {
    display: none;
  }

  .mobile > .scroll#vnorenyScroll {
    padding-bottom: 10em !important;
  }

  .vyber {
    max-height: unset;
  }
}

@media only screen and (max-width: 1650px) {
  .vyber {
    font-size: 90%;
  }

  .control {
    gap: 1%;
  }
}

@media only screen and (max-width: 1500px) {
  .vyber {
    font-size: 80%;
  }

  .control {
    gap: 0%;
  }

  .vyber {
    height: 40vh;
  }

  .button {
    margin: 0.9em 0;
  }
}

@media only screen and (max-width: 1500px) {
  .vyber {
    font-size: 75%;
  }

  .container {
    gap: 4%;
  }

  .control {
    gap: 0%;
  }

  .vyber {
    height: 35vh;
  }

  .button {
    white-space: nowrap;
    margin: 0.6em 0;
  }

  .search {
    margin: 0em 0 0.5em;
  }
}

@media only screen and (max-width: 1100px) {
  .vyber {
    font-size: 70%;
  }

  .noty {
    right: 0%;
  }
}

@media only screen and (max-width: 1050px) {
  .image {
    display: none;
  }

  .vyber {
    height: 23vh;
    padding-right: 2%;
    margin-bottom: 1.5em;
  }

  .mobile .vyber {
    margin-right: 0;
  }

  .control {
    box-sizing: border-box;
  }
}

@media only screen and (max-width: 1000px) {
  #Helishop {
    display: none;
  }
  #Helishopmobil {
    display: flex;
  }

  #Helishopzapisimobil {
    display: none;
  }

  #Helishoptablet {
    display: inline-table;
  }

  #Helishopzápisy > div.scroll {
    padding-bottom: 0;
  }

  .search {
    max-width: 35em;
    width: 80%;
  }
}

@media only screen and (max-width: 850px) {
  .mobile .button p {
    font-size: 1em;
  }

  .mobile .button {
    width: 6.5em;
  }
}

@media only screen and (min-height: 1200px) {
  .image {
    display: none;
  }

  .computer.vyber {
    padding-right: 6%;
  }
}
@media only screen and (max-width: 750px) {
  .mobile h1 {
    font-size: 9vw !important;
    margin-top: 0;
    text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.25);
  }

  .mobile {
    .podnadpis {
      margin: 0.5em auto 0;
      font-size: 3.3vw;
    }
  }

  .mobile h5 {
    font-size: 4.3vw;
  }

  .mobile {
    padding-bottom: 2em;
  }
  // #Číselné\ zápisy > div.scroll {
  //   padding-bottom: 10em;
  // }

  #Helishopmobil {
    display: block;
  }

  #Helishopzapisimobil {
    display: block;
  }

  #Helishoptablet {
    display: none;
  }

  .categories .buttons .button {
    width: 6.8em;
  }

  .mobile > .scroll {
    padding-bottom: 5em !important;
  }

  .mobile {
    section .button {
      margin: 0.7em auto 0;
      width: 4.7em;

      p {
        margin: unset;
      }
    }
  }
}
@media only screen and (max-width: 600px) {
  .mobile .layouts {
    & > img {
      width: 3.2em;
    }
  }
}
@media only screen and (max-width: 500px) {
  .categories .buttons .button {
    width: 6em;
  }
  .mobile {
    section .button {
      margin: 0.6em auto 0;
      padding: 0.3em 1.3em;

      p {
        font-size: 0.8em;
      }
    }
    .categories-search {
      font-size: 2.5vw;
    }

    .search input {
      padding: 0.3em 0.5em;
      font-size: 0.9em;
    }

    .layouts > img {
      font-size: 2.5vw;
    }

    .search-nadpis {
      font-size: 5vw;
      margin: 0.3em auto 0.5em;
    }
  }
}

@media only screen and (max-width: 430px) {
  .mobile {
    section .button {
      margin: 0.6em auto 0;
      padding: 0.1em 0.7em;

      p {
        font-size: 0.6em;
      }
    }
  }
}
@media only screen and (max-width: 350px) {
  .categories .buttons .button {
    width: 5em;
  }

  .mobile {
    section .button {
      margin: 0.6em auto 0;
      padding: 0.1em 0.6em;

      p {
        font-size: 0.5em;
      }
    }
  }
}
</style>
