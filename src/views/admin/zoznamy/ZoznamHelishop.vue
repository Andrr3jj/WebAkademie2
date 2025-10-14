<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Zoznam HeliShop</h5>

      <div class="line horizontal w-80"></div>

      <table>
        <thead>
          <tr>
            <th style="width: 35%">
              Názov:
              <SortArrow
                column="name"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 15%">
              Typ:
              <SortArrow
                column="category"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 15%">
              Cena:
              <SortArrow
                column="price"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th class="sold" style="width: 20%">
              Predané:
              <SortArrow
                column="soldNumber"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 20%"></th>
          </tr>
        </thead>
        <tbody v-if="Object.keys(dataVyhladavanie).length === 0">
          <!-- Tu môžete dynamicky generovať riadky podľa potreby -->
          <tr v-for="(item, index) in sortedData" :key="index">
            <td>
              <strong>{{ index + 1 }}. </strong>
              {{ item.name }}
            </td>
            <td>
              {{ item && item.category ? item.category : "Nedefinovaný typ" }}
            </td>
            <td>{{ item && item.price ? item.price : "0" }} €</td>
            <td class="sold">
              {{ item && item.soldNumber ? item.soldNumber : "0" }} ks
            </td>
            <td>
              <div @click="upravenieZapisu(item.id)" class="button">
                <a>Upraviť</a>
              </div>
            </td>
          </tr>
          <!-- Pridajte ďalšie riadky podľa potreby -->
        </tbody>
        <tbody v-else>
          <!-- Tu môžete dynamicky generovať riadky podľa potreby -->
          <tr v-for="(item, index) in dataVyhladavanie" :key="index">
            <td>
              <strong>{{ index + 1 }}. </strong>
              {{ item.name }}
            </td>
            <td>
              {{ item && item.category ? item.category : "Nedefinovaný typ" }}
            </td>
            <td>{{ item && item.price ? item.price : "0" }} €</td>
            <td class="sold">
              {{ item && item.soldNumber ? item.soldNumber : "0" }} ks
            </td>
            <td>
              <div @click="upravenieZapisu(item.id)" class="button">
                <a>Upraviť</a>
              </div>
            </td>
          </tr>
          <!-- Pridajte ďalšie riadky podľa potreby -->
        </tbody>
      </table>

      <div class="search">
        <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
        <input
          @input="oneskoreneVyhladavanie()"
          type="text"
          placeholder="Vyhľadať pieseň"
          v-model="search"
        />
      </div>
    </div>
  </section>
</template>

<script>
import SortArrow from "@/components/Functionality/SortArrow.vue";

export default {
  components: { SortArrow },
  mounted() {
    window.scrollTo(0, 0);
    this.nacitajProdukty();

    this.data = this.data.slice(); // Premeníme `data` iba raz
  },
  data() {
    return {
      data: [],
      search: "",
      predane: {},
      dataVyhladavanie: [],
      sortKey: "",
      sortDirection: "",
    };
  },
  computed: {
    reversedData() {
      return this.data.slice();
    },
    sortedData() {
      const base =
        Object.keys(this.dataVyhladavanie).length === 0
          ? this.data.slice()
          : this.dataVyhladavanie;

      if (!this.sortKey || !this.sortDirection) return base;

      return [...base].sort((a, b) => {
        const aVal = a[this.sortKey];
        const bVal = b[this.sortKey];

        // Pokúsime sa spracovať ako čísla
        const aNum = parseFloat(String(aVal).replace(",", "."));
        const bNum = parseFloat(String(bVal).replace(",", "."));

        const bothNumeric = !isNaN(aNum) && !isNaN(bNum);

        let comparison = 0;
        if (bothNumeric) {
          comparison = aNum - bNum;
        } else {
          // Fallback na porovnanie reťazcov
          const aStr = String(aVal).localeCompare(String(bVal), undefined, {
            numeric: true,
            sensitivity: "base",
          });
          comparison = aStr;
        }

        return this.sortDirection === "asc" ? comparison : -comparison;
      });
    },
  },
  methods: {
    nacitajProdukty() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/stats/listMerch.php",
      };

      axios
        .request(config)
        .then(async (response) => {
          // Musíme použiť `async`, lebo vnútri budeme čakať na `Promise.all`
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request succesfull") {
            this.data = response.data.data;

            // Spracovanie každého objektu
            const processedItems = await Promise.all(
              this.data.map(async (item) => {
                try {
                  // Parsing "details"
                  const details = JSON.parse(item.details);

                  // Počkáme na načítanie ceny a kategórie
                  const { price, category } = await this.nacitajDataProduktu(
                    item.id
                  );

                  // Vrátenie objektu so všetkými údajmi
                  return {
                    ...item, // Zachovanie pôvodných údajov
                    details, // Parsed details objekt
                    price, // Cena
                    category, // Kategória
                  };
                } catch (error) {
                  console.error(
                    `Chyba pri spracovaní produktu ${item.id}:`,
                    error
                  );
                  return item; // Ak nastane chyba, vrátime pôvodný item bez ceny/kategórie
                }
              })
            );

            // Až teraz priradíme správne spracované údaje
            this.data = processedItems;

            this.statistikaJednotlivoPredanych();
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    updateSort({ key, direction }) {
      this.sortKey = key;
      this.sortDirection = direction;
    },

    nacitajDataProduktu(id) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
      };

      return axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          if (response.data.status == "Request succesfull") {
            return {
              price: response.data.data.price.replace(".", ","),
              category: response.data.data.category,
            };
          } else {
            throw new Error("Neúspešná odpoveď z API");
          }
        })
        .catch((error) => {
          console.log("Chyba pri načítaní údajov produktu:", error);
          return { price: null, category: null }; // Vrátiť null hodnoty pri chybe
        });
    },

    upravenieZapisu(id) {
      // Presmerovanie na inú stránku s query parametrom "id"
      this.$router.push({
        path: "/admin/pridanie-polozky-helishop",
        query: { id: id },
      });
    },
    vyhladavanieZapisu() {
      if (this.search.trim() === "") {
        this.dataVyhladavanie = [];
        return;
      }
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/search.php?virtuality=false&search=" +
          this.search,
      };

      axios
        .request(config)
        .then(async (response) => {
          if (response.data.status == "Request succesfull") {
            let noveData = response.data.data;

            // Doplnenie už existujúcich údajov, ak sú k dispozícii
            noveData = noveData.map((novyItem) => {
              const existujuci = this.data.find(
                (item) => item.id === novyItem.id
              );
              return existujuci
                ? { ...novyItem, ...existujuci } // Ak máme staré údaje, ponecháme ich
                : { ...novyItem, price: null, category: null, details: null }; // Inak prázdne hodnoty
            });

            // Počkáme na načítanie chýbajúcich údajov
            this.dataVyhladavanie = await Promise.all(
              noveData.map(async (item) => {
                if (
                  item.price !== null &&
                  item.category !== null &&
                  item.details !== null
                ) {
                  return item; // Už máme dáta, nemusíme ich znova načítať
                }

                try {
                  const { price, category } = await this.nacitajDataProduktu(
                    item.id
                  );
                  const details = JSON.parse(item.details || "{}");
                  return { ...item, price, category, details };
                } catch (error) {
                  console.error(
                    `Chyba pri načítaní doplnkových údajov pre produkt ${item.id}:`,
                    error
                  );
                  return item;
                }
              })
            );
            setTimeout(() => {
              this.statistikaJednotlivoPredanych();
            }, 200);
          } else {
            setTimeout(() => {
              this.statistikaJednotlivoPredanych();
            }, 200);
          }
        })
        .catch((error) => {
          console.log(error);
          this.dataVyhladavanie = [];
        });
    },
    oneskoreneVyhladavanie() {
      // Vyčkajte 500 ms a potom zavolajte stiahniID()
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.vyhladavanieZapisu();
      }, 500);
    },
    statistikaJednotlivoPredanych() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/order/stats/soldByProduct.php",
        // headers: {
        //   ...data.getHeaders()
        // },
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          if (response.data.status == "Request succesfull") {
            this.predane = response.data.data;
          }

          // Prejdenie prvej sady dát a pridanie informácie o počte predaných kusov
          const mergedData = this.data.map((song) => {
            const id = song.id;
            const soldNumber = this.predane[id] || 0; // Ak nie je počet predaných kusov pre danú pieseň k dispozícii, nastavíme na 0

            // Vytvorenie nového objektu s pridanou informáciou o počte predaných kusov
            return { ...song, soldNumber };
          });

          this.data = mergedData;

          // Prejdenie prvej sady dát a pridanie informácie o počte predaných kusov pre predané kusy
          const mergedDataPredane = this.dataVyhladavanie.map((song) => {
            const id = song.id;
            const soldNumber = this.predane[id] || 0; // Ak nie je počet predaných kusov pre danú pieseň k dispozícii, nastavíme na 0

            // Vytvorenie nového objektu s pridanou informáciou o počte predaných kusov
            return { ...song, soldNumber };
          });

          this.dataVyhladavanie = mergedDataPredane;

          // 'mergedData' teraz obsahuje spojené dáta s informáciami o počte predaných kusov
          console.log(mergedData);
        })
        .catch((error) => {
          console.log(error);
        });
    },
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

  font-size: 2.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

table {
  width: 80%;
  border-collapse: collapse;
  margin: 3em 10%;
  margin-bottom: 6em;
}

th,
td {
  padding: 0.1em 0.5em;
  text-align: left;
  font-size: 1.3em;
}

td:not(td:last-child) {
  border-bottom: 1px solid #dddddd5e;
}

th {
  padding: 0.5em;
}

th:nth-child(2),
td:nth-child(2) {
  text-align: center;
}

.sold {
  text-align: right;
}

.button {
  width: 2.2em;
  padding: 0.15em 0.8em;
  margin: 0.1em 0;

  a {
    font-size: 0.52em;
  }
}

td:last-child .button {
  margin-left: auto;
}

.search {
  border-radius: 1.5625rem;
  border: 6px solid #fef35a;
  background: rgba(233, 233, 233, 0.93);
  box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.35);

  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0 0.5em;
  margin: 1em 0;

  width: 40em;
  font-size: 0.8em;
  position: fixed;
  left: 50%;
  transform: translate(-50%, -50%);
  bottom: 0em;

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

    margin-bottom: -0.2em;
  }
}

@media only screen and (max-width: 1500px) {
  table {
    margin: 3em 2%;
    width: 98%;
  }
}
</style>
