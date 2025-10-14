<template>
  <section v-if="menoPouzivatela" :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Zoznam číselných <span @click="addOwnedNotes()">z</span>ápisov</h5>

      <div class="line horizontal w-80"></div>

      <div class="top-stats" v-if="!isRestricted">
        <span
          v-for="{ norm, label } in allAuthors"
          :key="norm"
          :class="{ active: selectedNormAuthor === norm }"
          @click="vyberAutora(norm)"
        >
          {{ label }}: {{ topStats[label] || 0 }}
        </span>
      </div>
      <div class="line horizontal w-80"></div>

      <table>
        <thead>
          <tr>
            <th style="width: 40%">
              <span style="display: flex; align-items: center">
                Názov pesničky
                <SortArrow
                  column="name"
                  :sortKey="sortKey"
                  :sortDirection="sortDirection"
                  @update:sort="updateSort"
                />
              </span>
            </th>
            <th style="width: 20%; text-align: center">
              <span
                style="
                  display: flex;
                  align-items: center;
                  justify-content: center;
                "
              >
                Autor
                <SortArrow
                  column="author"
                  :sortKey="sortKey"
                  :sortDirection="sortDirection"
                  @update:sort="updateSort"
                />
              </span>
            </th>
            <th style="width: 10%; text-align: center">
              <span
                style="
                  display: flex;
                  align-items: center;
                  justify-content: center;
                "
              >
                Cena
                <SortArrow
                  column="price"
                  :sortKey="sortKey"
                  :sortDirection="sortDirection"
                  @update:sort="updateSort"
                />
              </span>
            </th>
            <th style="width: 20%">
              <div
                style="
                  display: flex;
                  align-items: center;
                  flex-direction: column;
                "
              >
                <div>
                  Predané:
                  <SortArrow
                    column="soldNumber"
                    :sortKey="sortKey"
                    :sortDirection="sortDirection"
                    @update:sort="updateSort"
                  />
                </div>
                <small>(Dokopy)</small>
              </div>
            </th>
            <th style="width: 30%">
              <div
                style="
                  display: flex;
                  align-items: center;
                  flex-direction: column;
                "
              >
                <div>
                  <MonthSwitcher
                    :month="month"
                    :year="year"
                    :sortKey="sortKey"
                    :sortDirection="sortDirection"
                    @update:sort="updateSort"
                    @update:month="month = $event"
                    @update:year="year = $event"
                  />
                </div>
              </div>
            </th>
            <th style="width: 10%"></th>
            <th style="width: 10%"></th>
          </tr>
        </thead>

        <tbody>
          <tr v-for="(item, index) in sortedData" :key="item.id || index">
            <td>
              <strong>{{ sortedData.length - index }}.</strong>
              {{ item.name }}
            </td>
            <td style="text-align: center">{{ item.author || "--" }}</td>
            <td style="text-align: center">
              {{
                typeof item.price === "number"
                  ? item.price.toFixed(2) + "€"
                  : "--"
              }}
            </td>
            <td style="text-align: center">{{ item.soldNumber }}</td>
            <td style="text-align: center">
              <span v-if="predajeLoading">...</span>
              <span v-else>{{ item.soldThisMonth || 0 }}</span>
            </td>
            <td>
              <div @click="upravenieZapisu(item.id)" class="button">
                <a>Upraviť</a>
              </div>
            </td>
            <td>
              <div
                @click="zmazanieZapisu(item.id)"
                title="Vymazať"
                class="red-button button"
              >
                <a>X</a>
              </div>
            </td>
          </tr>
          <tr v-if="sortedData.length === 0">
            <td
              colspan="7"
              style="text-align: center; vertical-align: middle; height: 200px"
            >
              {{ noResultsMessage }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="search">
        <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
        <input
          v-model="search"
          type="text"
          placeholder="Vyhľadať pieseň"
          class="search-input"
        />
      </div>
    </div>
  </section>
</template>

<script>
import MonthSwitcher from "@/components/Functionality/MonthSwitcher.vue";
import SortArrow from "./../../../components/Functionality/SortArrow.vue";

function normalizeName(name) {
  return (name || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/[\u0300-\u036f]/g, "")
    .replace(/\s+/g, " ")
    .trim();
}

export default {
  components: {
    SortArrow,
    MonthSwitcher,
  },
  mounted() {
    window.scrollTo(0, 0);
    this.ziskajPrihlasenehoPouzivatela().then(() => {
      this.nacitajZapisy();
    });
  },
  data() {
    return {
      data: [],
      predane: {},
      search: "",
      addNumber: 0,
      sortKey: "",
      sortDirection: "",
      menoPouzivatela: "",
      restrictedNames: ["Matej Kondela", "Libor Hliník"],
      selectedNormAuthor: "",
      year: new Date().getFullYear(),
      month: new Date().getMonth() + 1,
      predajeLoading: false, // <- loading stav pre mesačné predaje
    };
  },
  computed: {
    noResultsMessage() {
      const q = this.search.trim();
      if (q) {
        return `Nebola nájdená žiadna pieseň pre “${q}”`;
      }
      if (this.selectedNormAuthor) {
        return `Autor “${this.selectedNormAuthor}” nemá žiadne piesne`;
      }
      return `Nie sú tu žiadne záznamy`;
    },
    isRestricted() {
      if (!this.menoPouzivatela.trim()) return false;
      return this.restrictedNames.includes(this.menoPouzivatela.trim());
    },
    authorsMap() {
      const map = {};
      this.data.forEach((item) => {
        if (!item.author) return;
        const norm = normalizeName(item.author);
        if (!map[norm]) {
          map[norm] = item.author.trim();
        }
      });
      return map;
    },
    allAuthors() {
      return Object.entries(this.authorsMap).map(([norm, label]) => ({
        norm,
        label,
      }));
    },
    topStats() {
      const stats = {};
      const labelMap = {};
      this.data.forEach((item) => {
        if (!item.author) return;
        const norm = normalizeName(item.author);
        const label = item.author.trim();
        labelMap[norm] = label;
        if (this.isRestricted) {
          const userNorm = normalizeName(this.menoPouzivatela);
          if (norm !== userNorm) return;
        }
        stats[norm] = (stats[norm] || 0) + 1;
      });
      const result = {};
      for (const [norm, count] of Object.entries(stats)) {
        result[labelMap[norm]] = count;
      }
      return result;
    },
    filteredData() {
      const normalize = (str) =>
        String(str || "")
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .toLowerCase()
          .trim();

      let arr = this.data.slice();

      if (this.isRestricted) {
        const normUser = normalize(this.menoPouzivatela);
        arr = arr.filter(
          (item) => item.author && normalize(item.author) === normUser
        );
      }
      if (this.selectedNormAuthor) {
        arr = arr.filter(
          (item) =>
            item.author && normalize(item.author) === this.selectedNormAuthor
        );
      }
      if (this.search) {
        const q = normalize(this.search);
        arr = arr.filter(
          (item) =>
            (item.name && normalize(item.name).includes(q)) ||
            (item.author && normalize(item.author).includes(q))
        );
      }
      return arr.reverse();
    },
    sortedData() {
      const dataToSort = this.filteredData;
      if (!this.sortKey || !this.sortDirection) return dataToSort;
      return [...dataToSort].sort((a, b) => {
        let aVal = a[this.sortKey];
        let bVal = b[this.sortKey];
        if (this.sortKey === "name" || this.sortKey === "author") {
          aVal = String(aVal).trim();
          bVal = String(bVal).trim();
        }
        if (typeof aVal === "number" && typeof bVal === "number") {
          return this.sortDirection === "asc" ? aVal - bVal : bVal - aVal;
        }
        return this.sortDirection === "asc"
          ? String(aVal).localeCompare(String(bVal), "sk", {
              sensitivity: "base",
            })
          : String(bVal).localeCompare(String(aVal), "sk", {
              sensitivity: "base",
            });
      });
    },
  },
  methods: {
    async ziskajPrihlasenehoPouzivatela() {
      const axios = require("axios");
      try {
        const res = await axios.get(
          "https://heligonkovaakademia.sk/api/user/info/getAdditionalInformation.php"
        );
        if (res.data.status === "Request succesfull") {
          const user = res.data.data;
          this.menoPouzivatela = `${user.name} ${user.surname}`.trim();
        }
      } catch (err) {
        console.error("Chyba pri nacitani pouzivatela", err);
      }
    },
    addOwnedNotes() {
      if (this.addNumber === 5) {
        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();

        let config = {
          method: "get",
          maxBodyLength: Infinity,
          url: this.$store.state.api + "/product/addAllToOwned.php",
          data,
        };

        axios
          .request(config)
          .then(() => {
            this.$store.state.SnackBarText =
              "Pššt 🤫 pridal som ti všetky čísené zápisy 😉";
          })
          .catch(() => {
            this.$store.state.SnackBarText =
              "Nebolo ti súdené dostať zápisy, skús nabudúce";
          });
      } else {
        this.addNumber++;
      }
    },
    nacitajZapisy() {
      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/stats/listZapis.php",
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status === "Request succesfull") {
            this.data = (response.data.data || []).map((item) => {
              let author = "";
              try {
                if (item.details) {
                  const detailsObj = JSON.parse(item.details);
                  author = detailsObj.autor || "";
                }
              } catch (e) {
                console.log();
              }
              let price = null;
              if (
                item.price !== undefined &&
                item.price !== null &&
                !isNaN(Number(item.price))
              ) {
                price = Number(item.price);
              }
              return {
                ...item,
                author: (author || "").trim(),
                price,
              };
            });
            this.statistikaJednotlivoPredanych();
            this.nacitajPredaneZaMesiac(); // volaj hneď (bez setTimeout)
          } else {
            this.data = [];
          }
        })
        .catch((e) => {
          this.data = [];
          console.error("Chyba pri načítaní zápisov", e);
        });
    },
    async nacitajPredaneZaMesiac() {
      const axios = require("axios");
      this.predajeLoading = true;

      this.data = this.data.map((item) => ({
        ...item,
        soldThisMonth: 0,
      }));

      const promises = this.data.map(async (item) => {
        try {
          const res = await axios.get(
            `${this.$store.state.api}/product/stats/listZapisPredaneMesiac.php`,
            {
              params: {
                id: item.id,
                year: this.year,
                month: this.month,
              },
            }
          );
          if (res.data.status === "Request succesfull") {
            return { id: item.id, soldThisMonth: res.data.data };
          }
        } catch (e) {
          console.warn("Chyba pri nacitani predajov za mesiac:", e);
          return { id: item.id, soldThisMonth: 0 };
        }
      });

      const vysledky = await Promise.all(promises);

      this.data = this.data.map((item) => {
        const found = vysledky.find((x) => x.id === item.id);
        return {
          ...item,
          soldThisMonth: found ? found.soldThisMonth : 0,
        };
      });

      this.predajeLoading = false;
    },
    upravenieZapisu(id) {
      this.$router.push({
        path: "/admin/pridanie-zapisu",
        query: { id },
      });
    },
    vyberAutora(norm) {
      this.selectedNormAuthor = this.selectedNormAuthor === norm ? "" : norm;
    },
    zmazanieZapisu(id) {
      const zapis = this.data.find((item) => item.id === id);

      if (this.jeObycajnyAutor) {
        const autor = zapis?.author?.trim()?.toLowerCase() || "";
        const uzivatel = this.menoPouzivatela?.trim()?.toLowerCase() || "";
        if (autor !== uzivatel) {
          alert("Nemáte oprávnenie vymazať tento číselný zápis.");
          return;
        }
      }

      if (confirm("Naozaj chcete vymazať tento číselný zápis?")) {
        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();

        let config = {
          method: "get",
          maxBodyLength: Infinity,
          url: this.$store.state.api + "/product/delete.php/?id=" + id,
          data: data,
        };

        axios
          .request(config)
          .then((response) => {
            if (response.data.status == "Request fullfiled") {
              this.$store.state.SnackBarText = "Zápis bol vymazaný.";
              this.nacitajZapisy();
            } else {
              this.$store.state.SnackBarText = "Zápis sa nepodarilo vymazať.";
            }
          })
          .catch((error) => {
            console.log(error);
            this.$store.state.SnackBarText = "Nastala chyba pri mazaní zápisu.";
          });
      }
    },
    statistikaJednotlivoPredanych() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api + "/order/stats/soldByProductCiselneZapisy.php",
        data,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status === "Request succesfull") {
            this.predane = response.data.data;
          }

          const addSold = (songList) =>
            songList.map((song) => ({
              ...song,
              soldNumber: this.predane[song.name] || 0,
            }));

          this.data = addSold(this.data);
        })
        .catch(console.log);
    },
    updateSort({ key, direction }) {
      this.sortKey = key;
      this.sortDirection = direction;
    },
  },
  watch: {
    month() {
      this.nacitajPredaneZaMesiac();
    },
    year() {
      this.nacitajPredaneZaMesiac();
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
  font-size: 85%;
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

.button {
  min-width: 3.2em;
  width: auto;
  padding: 0.09em 0.55em;
  margin: 0.08em 0;
  font-size: 0.9em;
  border-radius: 1.2em;
  line-height: 1.2;

  a {
    font-size: 0.85em !important;
    padding: 0.04em 0.6em;
  }
}

.button.red-button,
.red-button.button {
  min-width: 2.1em;
  font-size: 0.87em;
  padding: 0.09em 0.5em;
  border-radius: 1.2em;
}

td:last-child .button,
td:last-child .red-button.button {
  margin-left: auto;
}

.top-stats {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 2em 1em; // (vertical, horizontal)
  margin: 2.5em 0 2em 0;
  width: 70%;
  min-height: 3.2em;
  font-size: 1.5em;
  margin-inline: auto;

  span {
    transition: color 0.18s, background 0.18s;
    border-radius: 1.2em;
    padding: 0.18em 0.7em;
    cursor: pointer;
    font-weight: 600;
    background: transparent;
    color: #111;
    box-shadow: 0 0 0 0 transparent;

    &.active {
      background: #fef35a;
      color: #222;
      box-shadow: 0 0 0 2px #fef35a;
    }
  }
}

@media (max-width: 900px) {
  .top-stats {
    font-size: 1.1em;
    gap: 1em 1.5em;
  }
}

.button.red-button {
  width: max-content;
  display: flex;
  justify-content: flex-end;
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

@media screen and (max-width: 1024px) {
  table {
    width: 100%;
    font-size: 0.9em;
    margin: 1em 0.5em;
  }

  th,
  td {
    padding: 0.3em 0.4em;
    font-size: 1em;
  }

  th:nth-child(1),
  td:nth-child(1) {
    width: 40%;
  }

  th:nth-child(2),
  td:nth-child(2) {
    width: 30%;
  }

  th:nth-child(3),
  td:nth-child(3),
  th:nth-child(4),
  td:nth-child(4) {
    width: 15%;
    text-align: center;
  }

  .vytvorenie-zapisu {
    font-size: 1.3em;
    margin: 0.8em auto;
    text-align: center;
  }

  .search {
    width: 90%;
    font-size: 0.9em;
    padding: 0.4em 1em;
    bottom: 1em;

    input {
      font-size: 1em;
    }

    img {
      width: 1.5em;
    }
  }
}
</style>
