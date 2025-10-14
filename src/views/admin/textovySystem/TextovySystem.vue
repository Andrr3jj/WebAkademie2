<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Zoznam a pridavanie textov</h5>
      <div class="line not horizontal w-80"></div>
      <div class="box-text">
        <div class="left">
          <div class="title">
            <h2 class="nadpis Bahnshrift">Zoznam piesni</h2>
            <p class="button">{{ this.dataTextov.schvalene.length }}</p>
          </div>

          <div class="line horizontal"></div>
          <table>
            <thead>
              <th style="width: 65%"></th>
              <th style="width: 25%"></th>
              <th style="width: 10%"></th>
            </thead>

            <tbody>
              <tr v-for="item in dataTextov.schvalene" :key="item.id">
                <td>
                  {{ item.nazov }}
                </td>
                <td>
                  <div
                    @click="nacitajKonretnyText(item.id)"
                    class="button edit"
                  >
                    <a>Upraviť</a>
                  </div>
                </td>
                <td>
                  <div
                    @click="zmazanieTextu(item.id)"
                    title="Vymazať"
                    class="red-button button"
                  >
                    <a>X</a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
          <div class="pagination-controls">
            <button
              class="button"
              @click="prevPage()"
              :disabled="paginationIndex === 0"
            >
              Späť
            </button>

            <button
              class="button"
              @click="nextPage()"
              :disabled="lastBatchCount < paginationLimit"
            >
              Ďalej
            </button>
          </div>
        </div>

        <div class="right">
          <div class="title">
            <h2 class="nadpis Bahnshrift">Návrhy od používateľov</h2>
            <p class="button">{{ this.dataTextov.neschvalene.length }}</p>
          </div>
          <div class="line horizontal"></div>

          <table>
            <thead>
              <th style="width: 65%"></th>
              <th style="width: 25%"></th>
              <th style="width: 10%"></th>
            </thead>

            <tbody>
              <tr v-for="(item, index) in dataTextov.neschvalene" :key="index">
                <td>
                  {{ item.nazov }}
                </td>
                <td>
                  <div
                    @click="nacitajKonretnyText(item.id)"
                    class="button edit"
                  >
                    <a>Pridať</a>
                  </div>
                </td>
                <td>
                  <div
                    @click="zmazanieTextu(item.id)"
                    title="Vymazať"
                    class="red-button button"
                  >
                    <a>X</a>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>

          <div class="line horizontal"></div>
          <div class="addsong-grid">
            <div class="addsong-left">
              <span class="section-title">▸ Čo vyplniť:</span>
              <input
                type="text"
                v-model="data.nazov"
                placeholder="Názov piesne"
                class="big-input"
              />
              <textarea
                v-model="data.text_piesne"
                placeholder="Text piesne"
                class="big-input"
              ></textarea>
              <div class="form-group">
                <label>Produkt na prepojenie</label>
                <select v-model.number="data.product_id">
                  <option value="0">-- Číselný zápis --</option>
                  <option
                    v-for="noty in zoradenyEditorZoznam"
                    :key="noty.id"
                    :value="noty.id"
                  >
                    {{ noty.name }}
                  </option>
                </select>
              </div>
            </div>

            <div class="addsong-right">
              <div class="form-group select-group">
                <label>Typ piesne:</label>
                <select
                  @change="data.autorska = data.typ === 'Autorská'"
                  v-model="data.typ"
                >
                  <option>Ľudová</option>
                  <option>Autorská</option>
                </select>
              </div>
              <div class="form-group select-group">
                <label>Žáner:</label>
                <select v-model="data.zaner">
                  <option>Polka</option>
                  <option>Valčík</option>
                  <option>Čardáš</option>
                </select>
              </div>
              <div class="form-group select-group">
                <label>Pôvod:</label>
                <select v-model="data.povod">
                  <option>Česká</option>
                  <option>Slovenská</option>
                  <option>Iná</option>
                </select>
              </div>
              <div class="form-group select-group">
                <label>Kategória piesne:</label>
                <select v-model="data.kategoria">
                  <option>Polka</option>
                  <option>Valčík</option>
                  <option>Česká</option>
                  <option>Terchovská</option>
                  <option>Moderná</option>
                </select>
              </div>
              <div class="form-group modern-question">
                <label class="modern-label">Jedná sa o modernú pieseň?</label>
                <label class="modern-radio">
                  <input
                    type="checkbox"
                    v-model="data.jeModerna"
                    :value="true"
                  />
                  <span class="radio-custom"></span>
                  Áno, ide o modernú pieseň
                </label>
              </div>
              <template v-if="data.autorska">
                <div class="modern-note">
                  Pri autorskej piesni je potrebné uviesť meno autora textu
                </div>
                <input
                  type="text"
                  v-model="data.autor"
                  placeholder="Meno autora textu"
                  class="author-input"
                />
              </template>
            </div>
          </div>

          <div class="footer-grid">
            <div>
              <span class="section-title">▸ Po odoslaní:</span>
              <span class="footer-desc"> </span>
            </div>
            <button
              v-if="isDataChanged"
              class="button Adumu"
              @click="resetData()"
            >
              Zrušiť
            </button>
            <button
              v-if="upravujem == 0"
              class="button Adumu"
              @click="vytvorenieTextu()"
            >
              Pridať
            </button>
            <button v-else class="button Adumu" @click="upravenieTextu()">
              Upraviť
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);
    this.nacitajTexty();

    this.nacitajInfoEditorZapisy();
  },
  data() {
    return {
      data: this.getDefaultData(),
      dataTextov: {
        schvalene: [],
        neschvalene: [],
      },
      editorZoznam: [],
      upravujem: 0,
      paginationIndex: 0,
      paginationLimit: 20,
      lastBatchCount: 0,
    };
  },
  methods: {
    getDefaultData() {
      return {
        nazov: "",
        text_piesne: "",
        product_id: 0,
        favorite: "",
        hidden: "",
        typ: "",
        zaner: "",
        povod: "",
        kategoria: "",
        jeModerna: false,
        autorska: false,
        autor: "",
        schvalene: 1,
      };
    },
    resetData() {
      this.data = this.getDefaultData();
      this.upravujem = 0;
    },

    loadMore() {
      if (this.lastBatchCount < this.paginationLimit) return;
      this.paginationIndex += this.paginationLimit;
      this.nacitajTexty();
    },

    async nacitajTexty() {
      const axios = require("axios");
      try {
        const res = await axios.get(
          this.$store.state.api + "/noty/texty/listAll.php",
          {
            params: {
              pagination_index: this.paginationIndex,
              pagination_limit: this.paginationLimit,
            },
          }
        );

        if (res.data.status === "Request succesfull") {
          const schvalene = res.data.data.schvalene || [];
          const neschvalene = res.data.data.neschvalene || [];

          // zobrazovanie
          this.dataTextov.schvalene = schvalene;
          this.dataTextov.neschvalene = neschvalene;

          // kľúčový fix: veľkosť dávky rátaj z oboch polí (pred filtrovaním)
          const receivedBatchCount = schvalene.length + neschvalene.length;
          this.lastBatchCount = receivedBatchCount;
        }
      } catch (e) {
        console.error(e);
      }
    },
    nextPage() {
      if (this.lastBatchCount < this.paginationLimit) return;
      this.paginationIndex += this.paginationLimit;
      this.nacitajTexty();
    },
    prevPage() {
      if (this.paginationIndex === 0) return;
      this.paginationIndex = Math.max(
        0,
        this.paginationIndex - this.paginationLimit
      );
      this.nacitajTexty();
    },
    upravenieTextu() {
      const axios = require("axios");
      let data = new FormData();

      if (this.upravujem == 0) {
        this.$store.state.SnackBarTex = "Pre upravenie si musíte vybrať text";
      }

      this.data.text_piesne = this.data.text_piesne.replaceAll("\n", "//n");

      data.append("nazov", this.data.nazov);
      data.append("text_piesne", this.data.text_piesne);
      data.append("product_id", this.data.product_id);
      data.append("kategoria", this.data.kategoria);
      data.append("autorska", this.data.autorska ? "true" : "false");
      if (this.data.autorska) {
        data.append("autor", this.data.autor);
      }
      data.append("jeModerna", this.data.jeModerna ? "true" : "false");
      data.append("typ", this.data.typ);
      data.append("zaner", this.data.zaner);
      data.append("povod", this.data.povod);
      data.append("schvalene", 1);
      data.append("id", this.upravujem);

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/texty/update.php",
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request fullfiled") {
            this.resetData();
            this.nacitajTexty();
            this.$store.state.SnackBarText = "Text bol úspešne uložený";
          } else {
            this.$store.state.SnackBarText = "Text sa nepodarilo načítať";
          }
          this.upravujem = 0;
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Nastala chyba pri načítaní textu.";
        });
    },
    nacitajKonretnyText(id) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/texty/load.php/?id=" + id,
        // data: data,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.data = response.data.data;

            this.data.text_piesne = this.data.text_piesne.replaceAll(
              "//n",
              "\n"
            );
            this.upravujem = id;
          } else {
            this.$store.state.SnackBarText = "Text sa nepodarilo načítať";
            this.resetData();
            this.upravujem = 0;
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Nastala chyba pri načítaní textu.";
        });
    },
    zmazanieTextu(id) {
      if (confirm("Naozaj chcete vymazať tento text?")) {
        const axios = require("axios");

        let config = {
          method: "get",
          maxBodyLength: Infinity,
          url: this.$store.state.api + "/noty/texty/delete.php/?id=" + id,
          // data: data,
        };

        axios
          .request(config)
          .then((response) => {
            if (response.data.status == "Request fullfiled") {
              this.$store.state.SnackBarText = "Text bol vymazaný.";
              this.nacitajTexty(); // aktualizuj zoznam po vymazaní
            } else {
              this.$store.state.SnackBarText = "Text sa nepodarilo vymazať.";
            }
          })
          .catch((error) => {
            console.log(error);
            this.$store.state.SnackBarText = "Nastala chyba pri mazaní Textu.";
          });
      }
    },
    vytvorenieTextu() {
      const axios = require("axios");
      let data = new FormData();

      this.data.text_piesne = this.data.text_piesne.replaceAll("\n", "//n");

      data.append("nazov", this.data.nazov);
      data.append("text_piesne", this.data.text_piesne);
      data.append("product_id", this.data.product_id);
      data.append("kategoria", this.data.kategoria);
      data.append("autorska", this.data.autorska ? "true" : "false");
      if (this.data.autorska) {
        data.append("autor", this.data.autor);
      }
      data.append("jeModerna", this.data.jeModerna ? "true" : "false");
      data.append("typ", this.data.typ);
      data.append("zaner", this.data.zaner);
      data.append("povod", this.data.povod);

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/texty/create.php",
        // headers: {
        //   "Content-Type": "multipart/form-data",
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          if (response.data.status == "Request fullfiled") {
            this.$store.state.SnackBarText = "Text bol úspešne pridaný";

            this.nacitajTexty();
            this.resetData();
            console.log("text pridanie responze :>> ", response.data);
          } else {
            this.$store.state.SnackBarTex = "Text sa nepridal";
            this.odosielanie = false;
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Text sa nepodarilo pridať";
          this.odosielanie = false;
        });
    },
    nacitajInfoEditorZapisy() {
      //táto metóda slúži na priradenie not k produktu
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/stats/listZapis.php",
        // headers: {
        //   "Content-Type": "multipart/form-data",
        // },
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request succesfull") {
            //poznámka
            this.editorZoznam = response.data.data;
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Nepodarilo sa načítať editor";
        });
    },
  },
  computed: {
    reversedData() {
      let zoznam = this.data.slice().reverse();
      if (this.jeObycajnyAutor) {
        zoznam = zoznam.filter(
          (item) =>
            item.author &&
            item.author.trim().toLowerCase() ===
              this.menoPouzivatela.toLowerCase()
        );
      }
      return zoznam;
    },
    isDataChanged() {
      return (
        JSON.stringify(this.data) !== JSON.stringify(this.getDefaultData())
      );
    },
    zoradenyEditorZoznam() {
      return [...this.editorZoznam].sort((a, b) =>
        a.name.localeCompare(b.name)
      );
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
  width: 100%;
  border-collapse: collapse;
  margin: 0 !important;
  margin-bottom: 6em;
}

th,
td {
  padding: 0.1em 0.5em;
  text-align: left;
  font-size: 0.83em;
}

.left {
  flex: 0.4;
}

.right {
  flex: 0.6;
  align-items: stretch;
}

.right .title,
.right table,
.right > .line {
  width: 80%;
  margin-left: 0 !important;
}

.not {
  margin: 2em auto;
}

.nadpis {
  font-weight: 900;
  margin: 0;
  font-size: 1.8em;
}

.line:not(.not) {
  margin: 1em 0;

  &.horizontal {
    height: 0.3rem;
  }
}

.title {
  width: 100%;
  display: flex;
  flex-direction: row;
  align-content: center;
  justify-content: space-between;
  margin: 1em auto;

  .button {
    font-size: 100%;
    font-weight: 900;
  }
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

.button.edit {
  padding: 0.15em 0.8em;
  margin: 0.1em 0;
  font-size: 150%;
  width: max-content;

  a {
    font-size: 0.52em;
  }
}

.pagination-controls {
  display: flex;
  align-items: center;
  gap: 1em;
  margin: 1em 0;

  .button {
    font-size: 1.2em;
  }
}

.pagination-controls button[disabled] {
  opacity: 0.5;
  cursor: not-allowed;
}

.button.red-button {
  width: max-content;
  display: flex;
  justify-content: flex-end;
  font-size: 80%;
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

.modal-desc {
  text-align: center;
  font-size: 1.25em;
  margin-bottom: 1.5em;
  font-family: "Bahnschrift", sans-serif;
}

.addsong-grid {
  display: flex;
  gap: 2.2em;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 1.5em;
  flex-wrap: wrap;
}

.addsong-left {
  flex: 1.1;
  display: flex;
  flex-direction: column;
  gap: 0.45em;
  align-items: flex-end;
}

.section-title {
  font-weight: 700;
  font-size: 1.65em;
  color: #222;
  margin-bottom: 0.18em;
  text-align: left;
  width: 100%;
  display: flex;
  justify-content: flex-start;
}

input,
textarea,
select {
  border-radius: 1.0625em;
  background: #90ca50;
  box-shadow: -7px 5px 15px rgba(0, 0, 0, 0.25) inset,
    0px 4px 4px rgba(0, 0, 0, 0.25);
  padding: 0.6em 1em;
  width: 100%;
  font-size: 1.1em;
  border: none;
  outline: none;
  color: #222;
  margin-bottom: 0.75em;
  box-sizing: border-box;
  transition: box-shadow 0.15s;
}

input:focus,
textarea:focus,
select:focus {
  box-shadow: -9px 7px 20px rgba(0, 0, 0, 0.33) inset,
    0px 5px 10px rgba(0, 0, 0, 0.18);
}

textarea {
  min-height: 210px;
  max-height: 210px;
  resize: vertical;
}

.button {
  font-weight: 600;
  margin-left: auto;
}

.author-input {
  max-width: 27em;
}

select {
  appearance: none;
  background-image: url('data:image/svg+xml;utf8,<svg fill="black" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><polygon points="6,8 10,12 14,8"/></svg>');
  background-repeat: no-repeat;
  background-position: right 0.9em center;
  background-size: 1em;
  cursor: pointer;
}

.addsong-right {
  flex: 1;
  display: flex;
  flex-direction: column;
  gap: 0;
  min-width: 230px;
  margin-top: 2em;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.1rem;
  width: -webkit-fill-available;
}

label {
  font-weight: 500;
  font-size: 1.3em;
  margin-bottom: 0.18em;
  color: #222;
  text-align: left;
}

.modern-question {
  display: flex;
  flex-direction: column;
  gap: 0.5em;
  font-size: 1em;
}

.modern-label {
  font-weight: 600;
  margin-bottom: 0.2em;
  margin-top: 0.8em;
  font-size: 1.3em;
  text-align: left;
}

.modern-radio {
  display: flex;
  align-items: center;
  gap: 0.45em;
  font-size: 0.95em;
  position: relative;
  cursor: pointer;
}

.modern-radio input[type="checkbox"] {
  opacity: 0;
  position: absolute;
}

.radio-custom {
  width: 1.1em;
  height: 1.1em;
  border: 2px solid #89bd37;
  border-radius: 50%;
  display: inline-block;
  position: relative;
}

.modern-radio input[type="checkbox"]:checked + .radio-custom::after {
  content: "";
  width: 0.55em;
  height: 0.55em;
  background: #89bd37;
  border-radius: 50%;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.modern-note {
  color: #000000;
  font-weight: 100;
  font-size: 1em;
  margin: 1em 0 0.2em;
  text-align: left;
}

.footer-grid {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 2.1em;
  margin-top: 1.1em;
  flex-wrap: wrap;
  width: 100%;

  .button {
    font-size: 120%;
  }
}

.footer-desc {
  font-size: 1.04rem;
  color: #222;
  max-width: 37em;
  margin-top: 0.18em;

  p {
    text-align: left !important;
    font-size: 1em;
  }
}

.box-text {
  display: flex;
  gap: 3em;
  margin: 2em 0;
}

@media only screen and (max-width: 560px) {
  .addsong-grid {
    flex-direction: column;
    gap: 0;
  }

  .addsong-left,
  .addsong-right {
    width: 100%;
  }

  h2 {
    font-size: 1.8em;
  }

  .footer-desc {
    font-size: 0.9em;
  }

  .button {
    font-size: 3.3vw;
  }
}
</style>
