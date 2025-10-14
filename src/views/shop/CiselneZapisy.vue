<template>
  <!-- COMPUTER -->
  <section
    class="computer vyber"
    :id="$route.name"
    :class="{ hidden: isHidden, 'display-none': isFullyHidden }"
  >
    <h1>Číselné zápisy pre heligónku</h1>
    <div class="control">
      <div class="categories-search">
        <div class="search">
          <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
          <input
            @input="oneskoreneVyhladavanie()"
            v-model="search"
            type="text"
            placeholder="Nebudem tu, pôjdem ďalej..."
          />
        </div>
        <div class="layouts">
          <img :src="gridImage" @click="toggleLayout()" />
          <img :src="rowImage" @click="toggleLayout()" />

          <div class="credit-toggle" @click="toggleCreditsBox">
            <img :src="creditImage" />
            <div v-if="showCreditsBox && credits !== null" class="credit-box">
              <p class="Adumu">{{ credits }} bodov</p>
            </div>
          </div>
        </div>
      </div>
      <div class="categories">
        <div v-for="cat in kategorie" :key="cat.kod" class="buttons">
          <div
            class="button"
            :class="{ 'active-category': category === cat.kod }"
            @click="toggleCategory(cat.kod)"
          >
            <p>{{ cat.nazov }}</p>
          </div>
        </div>
      </div>
      <img
        src="@/assets/images/gallery/CiselneZapisyTitle.png"
        alt="Číselné zápisy obrázok"
        class="noty"
      />
    </div>
  </section>

  <section class="computer zapisy" :id="$route.name + '1'">
    <zoznam-zapisov-header v-if="!gridActive" :layouts="gridActive" />
    <div class="scroll">
      <zoznam-zapisov
        :produktyID="produktyID"
        :produktyData="produktyData"
        :layouts="gridActive"
        :creditBuy="showCreditsBox"
      />
      <div
        v-if="nacitatViacej && !nacitanieBezi && produktyID.length > 0"
        class="button more"
        @click="nacitajViacProduktov"
      >
        <p>Načítať ďalšie</p>
      </div>

      <div
        v-if="produktyID.length === 0 && ziadnePiesneText"
        class="info-popis"
      >
        <p>Nenašli sa žiadne piesne</p>
      </div>
    </div>
  </section>

  <!-- MOBILE -->
  <div class="mobile" :id="$route.name">
    <div class="scroll">
      <section class="vyber" :id="$route.name">
        <h1>Číselné zápisy pre heligónku</h1>
        <div class="control">
          <div class="categories-search">
            <div class="search">
              <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
              <input
                @input="oneskoreneVyhladavanie()"
                v-model="search"
                type="text"
                placeholder="Nepôjdem tu, pôjdem ďalej..."
              />
            </div>
            <div class="layouts">
              <img :src="gridImage" @click="toggleLayout()" />
              <img :src="rowImage" @click="toggleLayout()" />

              <div class="credit-toggle" @click="toggleCreditsBox">
                <img :src="creditImage" />
                <div
                  v-if="showCreditsBox && credits !== null"
                  class="credit-box"
                >
                  <p class="Adumu">{{ credits }} bodov</p>
                </div>
              </div>
            </div>
          </div>
          <div class="categories">
            <div v-for="cat in kategorie" :key="cat.kod" class="buttons">
              <div
                class="button"
                :class="{ 'active-category': category === cat.kod }"
                @click="toggleCategory(cat.kod)"
              >
                <p>{{ cat.nazov }}</p>
              </div>
            </div>
          </div>
          <img
            src="@/assets/images/gallery/CiselneZapisyTitle.png"
            alt="Číselné zápisy obrázok"
            class="noty"
          />
        </div>
      </section>
      <section class="zapisy" :id="$route.name + 'zapisimobil'">
        <zoznam-zapisov-header v-if="!gridActive" :layouts="gridActive" />
        <zoznam-zapisov
          :produktyID="produktyID"
          :produktyData="produktyData"
          :layouts="gridActive"
          :creditBuy="showCreditsBox"
        />
        <div
          v-if="nacitatViacej && !nacitanieBezi && produktyID.length > 0"
          class="button more"
          @click="nacitajViacProduktov"
        >
          <p>Načítať ďalšie</p>
        </div>

        <div
          v-if="produktyID.length === 0 && ziadnePiesneText"
          class="info-popis"
        >
          <p>Nenašli sa žiadne piesne</p>
        </div>
      </section>
    </div>
  </div>

  <!-- TABLET -->
  <section class="zapisy tablet" :id="$route.name + 'tablet'">
    <zoznam-zapisov-header v-if="!gridActive" :layouts="gridActive" />
    <div class="scroll" id="vnorenyScroll">
      <zoznam-zapisov
        :produktyID="produktyID"
        :produktyData="produktyData"
        :layouts="gridActive"
        :creditBuy="showCreditsBox"
      />
      <div
        v-if="nacitatViacej && !nacitanieBezi && produktyID.length > 0"
        class="button more"
        @click="nacitajViacProduktov"
      >
        <p>Načítať ďalšie</p>
      </div>

      <div
        v-if="produktyID.length === 0 && ziadnePiesneText"
        class="info-popis"
      >
        <p>Nenašli sa žiadne piesne</p>
      </div>
    </div>
  </section>
</template>

<script>
import { mapState, mapActions } from "vuex";
import ZoznamZapisov from "@/components/Zapisy/ZoznamZapisov.vue";
import ZoznamZapisovHeader from "@/components/Zapisy/HeaderTableZoznamZapisov.vue";

export default {
  components: {
    ZoznamZapisov,
    ZoznamZapisovHeader,
  },
  data() {
    return {
      category: "",
      search: "",
      produktyID: [],
      produktyData: {},
      paginationNumber: 0,
      paginationStep: 20,
      paginationNumberEnd: 20,
      nacitatViacej: true,
      ziadnePiesneText: false,
      nacitanieBezi: false,
      gridActive: true,
      isHidden: false,
      isFullyHidden: false,
      hasScrolledPast: false,
      scrollThreshold: 300,
      showThreshold: 10,
      kategorie: [
        { kod: "polka", nazov: "Polka" },
        { kod: "valcik", nazov: "Valčík" },
        { kod: "ceska", nazov: "České" },
        { kod: "terchovska", nazov: "Terchovské" },
        { kod: "moderna", nazov: "Moderné" },
        { kod: "vianocna", nazov: "Vianočné" },
      ],
    };
  },
  computed: {
    ...mapState("credits", ["showCreditsBox", "credits", "loading", "error"]),

    gridImage() {
      return this.gridActive
        ? require("@/assets/images/icons/gridLayoutActive.png")
        : require("@/assets/images/icons/gridLayout.png");
    },
    rowImage() {
      return this.gridActive
        ? require("@/assets/images/icons/rowLayout.png")
        : require("@/assets/images/icons/rowLayoutActive.png");
    },
    creditImage() {
      return this.showCreditsBox
        ? require("@/assets/images/icons/creditLayoutActive.png")
        : require("@/assets/images/icons/creditLayout.png");
    },
  },
  mounted() {
    window.scrollTo(0, 0);
    const delay = this.$store.state.userRoles?.roles?.includes("product_pass")
      ? 1000
      : 400;
    setTimeout(() => this.nacitajProduktyID(), delay);

    const scrollElement = document.querySelector(".scroll");
    if (scrollElement) {
      scrollElement.addEventListener("scroll", this.handleScroll);
    }

    this.$watch(
      () => this.$store.state.user?.id,
      (val) => {
        if (val && this.showCreditsBox && this.credits === null) {
          this.fetchCredits();
        }
      }
    );
  },
  beforeUnmount() {
    const scrollElement = document.querySelector(".scroll");
    if (scrollElement) {
      scrollElement.removeEventListener("scroll", this.handleScroll);
    }
  },
  methods: {
    ...mapActions("credits", ["toggleCreditsBox", "fetchCredits"]),
    toggleLayout() {
      this.gridActive = !this.gridActive;
    },
    toggleCategory(kod) {
      this.category = this.category === kod ? "" : kod;
      this.resetujProduktyANacitaj();
    },
    ziadnePiesne() {
      this.ziadnePiesneText = this.produktyID.length === 0;
    },
    nacitajViacProduktov() {
      this.paginationNumber += this.paginationStep;
      this.paginationNumberEnd += this.paginationStep;
      this.nacitajProduktyID();
    },
    oneskoreneVyhladavanie() {
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.resetujProduktyANacitaj();
      }, 500);
    },
    resetujProduktyANacitaj() {
      this.produktyID = [];
      this.produktyData = {};
      this.paginationNumber = 0;
      this.paginationNumberEnd = this.paginationStep;
      this.nacitajProduktyID();
    },
    nacitajProduktyID() {
      if (this.nacitanieBezi) return;
      this.nacitanieBezi = true;

      const axios = require("axios");
      const baseUrl = this.$store.state.api;
      const query = new URLSearchParams({
        category: this.category,
        search: this.search,
        pagination_index: this.paginationNumber,
        pagination_limit: this.paginationStep,
        details_key: "typ",
        details_value: "Zapis",
      });

      axios
        .get(`${baseUrl}/product/search.php?${query}`)
        .then((res) => {
          const data = res.data.data?.reverse() || [];

          this.paginationNumber += data.length;
          this.paginationNumberEnd += data.length;

          if (data.length < this.paginationStep) {
            this.nacitatViacej = false;
          } else {
            const checkQuery = new URLSearchParams({
              category: this.category,
              search: this.search,
              pagination_index: this.paginationNumberEnd,
              pagination_limit: 1,
              details_key: "typ",
              details_value: "Zapis",
            });

            const axios = require("axios");
            axios
              .get(`${this.$store.state.api}/product/search.php?${checkQuery}`)
              .then((checkRes) => {
                this.nacitatViacej = checkRes.data.data.length > 0;
              })
              .catch(() => {
                this.nacitatViacej = false;
              });
          }

          const owned = new Set(
            this.$store.state.user?.ownedNotes.map((n) => n.id)
          );

          const nezakupene = data.filter(
            (produkt) => !this.$store.state.user || !owned.has(produkt.id)
          );

          // Pridaj len tie, ktoré nie sú zakúpené
          nezakupene.forEach((produkt) => {
            this.produktyID.push(produkt.id);
            this.nacitajProduktyData(produkt.id, produkt.timestamp);
          });

          // Ak sa zobrazilo menej než paginationStep, aj kvôli zakúpeným, skry tlačidlo
          if (nezakupene.length < this.paginationStep) {
            this.nacitatViacej = false;
          }

          this.nacitanieBezi = false;
          this.ziadnePiesne();
        })
        .catch((err) => {
          console.error("Chyba načítania produktov:", err);
          this.nacitanieBezi = false;
          this.ziadnePiesne();
        });
    },
    nacitajProduktyData(id, timestamp) {
      const axios = require("axios");
      const url = `${this.$store.state.api}/product/loadLimited.php/?id=${id}`;

      axios.get(url).then((res) => {
        if (res.data.status === "Request succesfull") {
          const produkt = res.data.data;
          produkt.price = produkt.price.replaceAll(".", ",");
          produkt.timestamp = this.formatTimestamp(timestamp);
          this.produktyData[produkt.id] = produkt;
        }
      });
    },
    formatTimestamp(ts) {
      if (!ts) return "";
      const [y, m, d] = ts.split(" ")[0].split("-");
      return `${d}.${m}.${y}`;
    },
    handleScroll(event) {
      const scrollTop = event.target.scrollTop;
      if (scrollTop > this.scrollThreshold && !this.hasScrolledPast) {
        this.isHidden = true;
        this.hasScrolledPast = true;
        setTimeout(() => {
          if (this.isHidden) this.isFullyHidden = true;
        }, 300);
      } else if (scrollTop < this.showThreshold && this.hasScrolledPast) {
        this.isHidden = false;
        this.hasScrolledPast = false;
        this.isFullyHidden = false;
      }
    },
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
  line-height: 115%;
  /* 3.59375rem */
  letter-spacing: 0.25rem;

  margin: 0;
}

.vyber {
  height: auto;
  min-height: 17em;
  max-height: 22em;
  padding: 2% 4%;
}

.vyber {
  transition: opacity 0.5s ease, transform 0.5s ease;
  /* Definovanie plynulosti */
}

.vyber {
  opacity: 1;
  transform: translateY(0);
  height: auto;
  /* Pre normálnu výšku pred animovaním */
  transition: opacity 0.5s ease-out, transform 0.5s ease-out,
    height 0.5s ease-out, min-height 0.5s ease-out;
}

.vyber.hidden {
  opacity: 0;
  transform: translateY(-100%);
  /* Posun k hornej hranici pre skrytie */
  height: 0;
  min-height: 0;
  transition-duration: 0.5s;
}

.vyber.hidden h1,
.vyber.hidden > div {
  transform: scaleY(0);
  /* Zmenšovanie na vertikálnu nulu */
  transition: transform 0.3s ease-out;
  /* Plynulý prechod na vertikálne zmenšenie */
}

.vyber.hidden.display-none {
  display: none;
  /* Nastavenie display: none po dokončení animácie */
}

.control {
  display: flex;
  align-items: flex-start;
  flex-direction: column;
  justify-content: flex-end;
  gap: 2%;
  height: auto;

  position: relative;
}

.categories-search,
.categories {
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  flex-direction: row;
  flex-wrap: wrap;
}
.categories-search {
  gap: 1em;
  flex-wrap: nowrap;
}

.categories {
  flex-wrap: wrap-reverse;
  font-size: 80%;
}

.credit-toggle {
  position: relative;
  display: flex;
  align-items: center;
  cursor: pointer;

  & > img {
    width: 3.5em;
    z-index: 2;
    height: auto;
    box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.25);
    border-radius: 0.6em;

    &:hover {
      transform: scale(1.1);
      transition-duration: 0.2s;
      box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.35);
    }
  }
}

.credit-box {
  width: max-content;
  height: 2.2em;
  display: flex;
  border-radius: 0.8em;
  margin-left: -5%;
  margin-right: 1.5em;
  padding: 0.3em 7% 0.3em 10%;
  white-space: nowrap;

  background: #90ca50;
  color: black;
  font-weight: bold;
  align-items: center;
  gap: 0.4em;
  box-shadow: 3px 3px 6px rgba(0, 0, 0, 0.3);

  p {
    font-size: 1.3em;
    letter-spacing: 0.08em;
    margin-bottom: -0.3em;
  }
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
  margin: 1.6em 0 0.8em;

  img {
    width: 2em;
  }

  input {
    width: 100%;
    font-size: 1.25em;
    font-style: normal;
    font-weight: 400;
    line-height: 137.53%;
    /* 1.71913rem */
    letter-spacing: 0.0625rem;
  }
}

.layouts {
  display: flex;
  margin: 1.6em auto 0.8em 1.5em;
  gap: 1em;
  align-items: center;
  justify-content: flex-end;
  cursor: pointer;
  transition-duration: 0.3s;
  z-index: 3;

  & > img {
    width: 3.5em;
    height: auto;
    box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.25);
    border-radius: 0.6em;

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
  line-height: 137.53%;
  /* 1.89106rem */
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

.info-popis {
  font-size: 3em;
  margin: 2em auto 4em auto;
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

.mobile {
  input {
    font-size: 1.3em;
    font-style: normal;
    font-weight: 400;
    line-height: 137.53%;
    /* 1.11744rem */
    letter-spacing: 0.04063rem;
  }

  .vyber {
    height: auto;
    font-size: 100%;
    min-height: 11em;
  }

  h1 {
    text-align: center;
    font-size: 3.5vw;
    font-style: normal;
    font-weight: 400;
    line-height: 115%;
    /* 1.725rem */
    letter-spacing: 0.045rem;
    margin-top: 0.1em;
  }

  .control {
    padding: 0 5% 3%;
  }

  .search {
    max-width: 55%;
    min-width: 4em;
  }

  .layouts {
    margin: 1.6em 0 0.8em 0.5em;
    width: max-content;

    img {
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
    // font-size: 100% !important;
    // justify-content: space-evenly;
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
    height: 27vh;
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
    height: 18vh;
  }

  .button {
    white-space: nowrap;
    margin: 0.6em 0;
  }

  .search {
    margin: 1.3em 0 0.5em;
  }
}

@media only screen and (max-width: 1390px) {
  .categories {
    font-size: 87%;
  }
}

@media only screen and (max-width: 1290px) {
  .categories {
    font-size: 81%;
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
  .noty {
    display: none;
  }

  .vyber {
    height: 17vh;
  }
}

@media only screen and (max-width: 1000px) {
  #Číselné\ zápisyzapisimobil {
    display: none;
  }

  #Číselné\ zápisytablet {
    display: inline-table;
  }

  #Číselné\ zápisy > div.scroll {
    padding-bottom: 0;
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

@media only screen and (max-width: 750px) {
  .mobile h1 {
    font-size: 5.5vw !important;
    text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.25);
  }

  .mobile .credit-box {
    height: 1.5em;
    border-radius: 0.4em;

    p {
      font-size: 0.9em;
      letter-spacing: 0.08em;
      margin-bottom: -0.1em;
    }
  }

  .info-popis p {
    font-size: 1em;
  }

  .mobile {
    padding-bottom: 2em;
  }

  // #Číselné\ zápisy > div.scroll {
  //   padding-bottom: 10em;
  // }

  #Číselné\ zápisyzapisimobil {
    display: block;
  }

  #Číselné\ zápisytablet {
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
    img {
      width: 2.5em;
    }
  }
}

@media only screen and (max-width: 500px) {
  .categories .buttons .button {
    width: 6em;
  }

  .info-popis p {
    font-size: 0.8em;
  }

  .info-popis {
    font-size: 2em;
    margin: 1em auto 8em auto;
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
