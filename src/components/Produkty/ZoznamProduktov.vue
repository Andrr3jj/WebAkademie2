<template>
  <div class="zapisy-zoznam">
    <JedenProdukt
      v-for="(produktyID, index) in produktyID"
      :key="index"
      :produktyID="produktyID"
      :produktyData="produktyData[produktyID]"
      v-show="produktyData[produktyID]"
    />
    <div
      @click="nacitajViacProduktov()"
      v-if="nacitajViac && !ziadnePiesneText && pocet > 5"
      class="button more"
    >
      <p>Načítať ďalšie</p>
    </div>
    <div class="info-popis" v-if="produktyID.length == 0 && ziadnePiesneText">
      <p>Nenašli sa žiadne položky</p>
    </div>
  </div>
</template>

<script>
import JedenProdukt from "@/components/Produkty/JedenProdukt.vue";
export default {
  components: {
    JedenProdukt,
  },
  data() {
    return {
      ziadnePiesneText: false,
      produktyData: {},
      nacitanieBezi: false,
      produktyID: [],
      paginationNumber: 0,
      debounceTimer: null,
      reccursive: 0,
      nacitajViac: false, // Stav, či má byť zobrazené tlačidlo načítať viac
      apiAlreadyCalled: false,
      observer: null,
    };
  },
  props: {
    search: {
      type: String,
      default: "",
    },
    category: {
      type: String,
      default: "",
    },
    pocet: {
      type: Number,
      default: 10,
    },
  },
  watch: {
    search: {
      handler: "handleChange",
      // immediate: true
    },
    category: {
      handler: "handleChange",
    },
  },
  mounted() {
    const checkIfVisibleAndObserve = () => {
      const style = window.getComputedStyle(this.$el);
      if (style.display !== "none" && !this.observer) {
        this.observer = new IntersectionObserver(
          ([entry]) => {
            if (entry.isIntersecting && !this.apiAlreadyCalled) {
              this.apiAlreadyCalled = true;
              this.handleChange(); // simulate initial search/category trigger
            }
          },
          {
            threshold: 0.1,
          }
        );

        this.observer.observe(this.$el);
      }
    };

    this.visibilityCheckInterval = setInterval(checkIfVisibleAndObserve, 200);
  },
  beforeUnmount() {
    if (this.observer) {
      this.observer.disconnect();
    }
    if (this.visibilityCheckInterval) {
      clearInterval(this.visibilityCheckInterval);
    }
  },
  methods: {
    handleChange() {
      clearTimeout(this.debounceTimer); // Zruší predchádzajúci timeout
      this.debounceTimer = setTimeout(() => {
        this.paginationNumber = 0;
        this.resetValues();
        this.nacitajViacProduktov(); // Po 500ms zavolá načítanie produktov
      }, 500);
    },
    nacitajViacProduktov() {
      console.log(
        "this.paginationNumber, this.pocet) :>> ",
        this.paginationNumber,
        this.pocet
      );
      this.nacitajProduktyID(this.paginationNumber, this.pocet);

      console.log(
        "this.paginationNumber, this.pocet)2 :>> ",
        this.paginationNumber,
        this.pocet
      );
      this.paginationNumber = this.paginationNumber + this.pocet;
    },
    ziadnePiesne() {
      this.ziadnePiesneText = this.produktyID.length === 0;
    },
    resetValues() {
      this.produktyID = [];
      this.produktyData = {};

      const poukazkaID = "gift_card5000";
      this.produktyID.push(poukazkaID);
      this.produktyData[poukazkaID] = {
        id: poukazkaID,
        name: "Darčeková poukážka",
        additional_text: "Perfektný darček pre každého",
        price: "50,00",
        discount: "0",
        discount_active: "false",
        category: "poukazka",
        difficulty: "1",
        expiration: "never",
        deleted: "false",
        virtuality: "true",
        free: "false",
        new: "1745583469",
        data: "helishop/giftcards",
        images: [
          "https://heligonkovaakademia.sk/data/images/poukazkaImage.png",
        ],
        details: {
          typ: "gift_card",
          popis:
            "Neviete, čo vybrať? Darčeková poukážka je ideálnym riešením! Obdarovaný si sám vyberie, na čo ju využije – či už na predplatné výučbových videí, číselné zápisy alebo produkty v HeliShope.",
          opis: "Poukážka, ktorú je možné uplatniť pri nákupe v našom Heli shope.",
          farby: [],
          velkosti: [],
          coSaPopis: "",
          preKohoPopis: "",
          obsahPopis: "",
          technickyPopis: "",
          infoPopis: "",
          vydanie: "",
          zoznamPopis: "",
        },
        timestamp: "",
      };
    },
    nacitajProduktyID(paginIndex, paginPocet) {
      if (this.nacitanieBezi) {
        return;
      } else {
        this.nacitanieBezi = true;
      }

      // if (paginPocet < 5) {
      //   let randomCislo = Math.floor(Math.random() * 4); //náhodné číslo od 0-3
      //   paginIndex = paginIndex + randomCislo;
      //   paginPocet = paginPocet + randomCislo;
      // }

      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/search.php?category=" +
          this.category +
          "&search=" +
          this.search +
          "&pagination_index=" +
          paginIndex +
          "&pagination_limit=" +
          paginPocet +
          "&details_key=typ&details_value=Merch&virtuality=false",
        headers: {},
      };

      console.log("config :>> ", config);

      axios
        .request(config)
        .then((response) => {
          // Vytvorenie objektu pre rýchlejšie vyhľadávanie

          if (response.data.status == "Request failed") {
            setTimeout(() => {
              this.reccursive++;
              if (this.reccursive <= 100) {
                this.nacitajProduktyID(paginIndex, paginPocet);
              } else {
                this.reccursive = 0;
              }
              return;
            }, 250);
          }

          const jsonArray = response.data.data.reverse();

          if (jsonArray.length < paginPocet) {
            this.nacitajViac = false;
          } else {
            this.nacitajViac = true;
          }

          console.log("jsonArray :>> ", jsonArray);

          for (let i = 0; i < jsonArray.length; i++) {
            const produkt = jsonArray[i]; // Získanie celého objektu produktu
            const id = produkt.id;
            const timestamp = produkt.timestamp;

            if (this.$route.query.cislo && id == this.$route.query.cislo) {
              continue;
            }
            this.produktyID.push(produkt.id); // Uloženie objektu s ID a timestampom
            this.nacitajProduktyData(id, timestamp);
          }

          this.nacitanieBezi = false;
          this.ziadnePiesne(); // Kontrola, či existujú piesne
        })
        .catch((error) => {
          console.log(error);

          this.nacitanieBezi = false;
          this.ziadnePiesne(); // Kontrola, či existujú piesne
        });
    },
    nacitajProduktyData(id, timestamp) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            let produkt = response.data.data;
            // produkt.price = produkt.price;
            // Formátovanie timestampu do dd.mm.yyyy
            produkt.timestamp = this.formatTimestamp(timestamp);

            this.produktyData[produkt.id] = produkt;

            console.log("this.produktyData :>> ", this.produktyData.slice());
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    formatTimestamp(timestamp) {
      if (!timestamp) return "";

      let parts = timestamp.split(" ")[0].split("-"); // Rozdelenie "YYYY-MM-DD HH:MM:SS" -> ["YYYY", "MM", "DD"]
      return `${parts[2]}.${parts[1]}.${parts[0]}`; // Poskladanie do formátu dd.mm.yyyy
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
  font-size: 80%;
}

.active {
  display: flex;
  width: 100%;
  flex-direction: column;

  padding: 0.4em 0em 0 1.2em;
  box-sizing: border-box;
}

.info-popis {
  font-size: 3em;
  margin: 2em auto 4em auto;
}

@media only screen and (max-width: 750px) {
  .info-popis p {
    font-size: 1em;
  }
}

@media only screen and (max-width: 500px) {
  .info-popis p {
    font-size: 0.8em;
  }

  .info-popis {
    font-size: 2em;
    margin: 1em auto 8em auto;
  }
}
</style>
