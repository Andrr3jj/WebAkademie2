<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Zľavové kódy</h5>

      <div class="line horizontal w-80"></div>

      <form class="table add" @submit.prevent="pridanieKuponu">
        <table>
          <thead>
            <tr>
              <th style="width: 30%">Názov:</th>
              <th style="width: 20%">Typ:</th>
              <th style="width: 15%">Doba trvania:</th>
              <th style="width: 15%">Zľava v %:</th>
              <th style="width: 20%"></th>
            </tr>
          </thead>
          <tbody>
            <tr class="not-hover">
              <td>
                <div class="row">
                  <input
                    type="text"
                    name="nazov"
                    id="nazov"
                    placeholder="Heligonka20"
                    v-model="nazov"
                  />
                </div>
              </td>
              <td>
                <div class="row row-form">
                  <select name="typ" id="typ" v-model="typ">
                    <option value="kosik" selected>Košík</option>
                    <option value="predplatne">Predplatné</option>
                  </select>
                </div>
              </td>
              <td>
                <div class="row">
                  <input
                    type="number"
                    name="doba"
                    id="doba"
                    placeholder="7"
                    v-model="doba"
                  />
                </div>
              </td>
              <td>
                <div class="row">
                  <input
                    type="text"
                    name="zlava"
                    id="zlava"
                    placeholder="10"
                    v-model="zlava"
                  />
                </div>
              </td>
              <td>
                <button
                  :class="{
                    'not-have-permission':
                      !this.$store.state.userRoles?.roles?.includes(
                        'coupon_master'
                      ),
                  }"
                  class="button"
                  type="submit"
                >
                  <a>Pridať</a>
                </button>
              </td>
            </tr>
            <!-- Pridajte ďalšie riadky podľa potreby -->
          </tbody>
        </table>
      </form>

      <div class="line horizontal w-80"></div>

      <table class="table">
        <thead>
          <tr>
            <th style="width: 35%">
              Názov:
              <SortArrow
                column="code"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 20%">
              Typ:
              <SortArrow
                column="urlType"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 15%">
              Do zrušenia:
              <SortArrow
                column="valid_until_day"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 10%">
              Zľava:
              <SortArrow
                column="value"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 20%"></th>
          </tr>
        </thead>
        <tbody v-if="getAllCoupons().length > 0">
          <tr v-for="(item, index) in sortedCoupons" :key="index">
            <td>
              <strong>{{ index + 1 }}. </strong> {{ item.code }}
            </td>
            <td>
              <!-- Zobrazíme typ kupónu (napr. 'subscription' alebo 'product') -->
              {{ formatUrlType(item.urlType) }}
            </td>
            <td>
              {{ item.valid_until_string }}
            </td>
            <td>{{ item.value }} %</td>
            <td>
              <div
                :class="{
                  'not-have-permission':
                    !this.$store.state.userRoles?.roles?.includes(
                      'coupon_master'
                    ),
                }"
                @click="vymazKupon(item.id, item.urlType)"
                class="button"
              >
                <a>Zrušiť</a>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</template>

<script>
import SortArrow from "@/components/Functionality/SortArrow.vue";
export default {
  components: { SortArrow },
  mounted() {
    window.scrollTo(0, 0);

    this.nacitajKupony("kosik");
    this.nacitajKupony("predplatne");
  },
  data() {
    return {
      zlava: "",
      nazov: "",
      typ: "",
      doba: 0,
      kuponyData: {},
      sortKey: "",
      sortDirection: "",
    };
  },
  computed: {
    sortedCoupons() {
      const coupons = this.getAllCoupons();
      if (!this.sortKey || !this.sortDirection) return coupons;

      return [...coupons].sort((a, b) => {
        const aVal = a[this.sortKey];
        const bVal = b[this.sortKey];

        if (typeof aVal === "number" && typeof bVal === "number") {
          return this.sortDirection === "asc" ? aVal - bVal : bVal - aVal;
        }

        return this.sortDirection === "asc"
          ? String(aVal).localeCompare(String(bVal))
          : String(bVal).localeCompare(String(aVal));
      });
    },
  },
  methods: {
    updateSort({ key, direction }) {
      this.sortKey = key;
      this.sortDirection = direction;
    },
    formatUrlType(url) {
      if (url.includes("product")) {
        return "Košík";
      } else if (url.includes("subscription")) {
        return "Predplatné";
      }
      return url; // Vráti pôvodný text, ak nie je v pravidlách
    },
    getAllCoupons() {
      const allCoupons = [];

      if (!this.kuponyData || typeof this.kuponyData !== "object") {
        return allCoupons; // Vrátime prázdne pole, ak nie je kuponyData korektné
      }

      for (const key in this.kuponyData) {
        if (Array.isArray(this.kuponyData[key])) {
          this.kuponyData[key].forEach((coupon) => {
            allCoupons.push({
              ...coupon,
              urlType: key,
            });
          });
        }
      }

      return allCoupons;
    },
    pridanieKuponu() {
      //orpavenie ceny na bodku pre databázu
      if (this.zlava.includes(",")) {
        this.zapisyData.cena = this.zapisyData.cena.replace(",", ".");
      }

      if (!this.podmienkyPreOdoslanie()) {
        return;
      }

      // Vytvorenie dátumu, kedy kupón bude vypršaný (o polnoci)
      let d = new Date();
      d.setDate(d.getDate() + this.doba);
      d.setHours(0, 0, 0, 0); // Nastavenie času na polnoc

      // Preloženie dátumu na časovú pečiatku a vrátenie
      let timestamp = d.getTime() / 1000;

      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      data.append("type", "PERCENT");
      data.append("value", this.zlava);
      data.append("valid_until", timestamp);

      if (this.nazov != "" || this.nazov != undefined) {
        data.append("code", this.nazov);
      }

      var urlType = "";

      if (this.typ == "predplatne") {
        urlType = "/subscription/coupon/create.php";
      } else {
        urlType = "/product/coupon/create.php/";
      }

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + urlType,
        // headers: {
        //   ...data.getHeaders(),
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request fullfiled") {
            this.$store.state.SnackBarText =
              "Kupón z názvom " + this.nazov + " bol úspešne pridaný";

            this.nacitajKupony("kosik");
            this.nacitajKupony("predplatne");

            this.zlava = 0;
            this.nazov = "";
            this.doba = 0;
          } else {
            this.$store.state.SnackBarText = "Kupón sa nepodarilo pridať";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Kupón sa nepodarilo pridať";
        });
    },
    nacitajKupony(typ) {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      if (!this.kuponyData[urlType]) {
        this.kuponyData[urlType] = [];
      }

      var urlType = "";

      if (typ == "predplatne") {
        urlType = "/subscription/coupon/list.php";
      } else {
        urlType = "/product/coupon/list.php";
      }

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + urlType,
        // headers: {
        //   ...data.getHeaders(),
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log("dáta kupónov" + JSON.stringify(response.data));

          if (response.data.status == "Request succesfull") {
            this.kuponyData[urlType] = response.data.data || [];
            // Ak sa úspešne načítajú kupóny, spustíme metódu getRemainingDays pre každý kupón
            this.kuponyData[urlType].forEach((kupon) => {
              kupon.valid_until_day = this.getRemainingDays(
                kupon.valid_until * 1000
              );
            });
            // Odstránime kupóny s neplatným valid_until
            this.kuponyData[urlType] = this.kuponyData[urlType].filter(
              (kupon) => kupon.valid_until_day > 0
            );

            this.kuponyData[urlType].forEach((kupon) => {
              // Zobraziť správnu formu podľa počtu dní
              if (kupon.valid_until_day === 1) {
                kupon.valid_until_string = kupon.valid_until_day + " deň";
              } else if (
                kupon.valid_until_day > 1 &&
                kupon.valid_until_day < 5
              ) {
                kupon.valid_until_string = kupon.valid_until_day + " dni";
              } else {
                kupon.valid_until_string = kupon.valid_until_day + " dní";
              }
            });
          } else {
            this.$store.state.SnackBarText = "Nepodarilo sa načítať kupóny";
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    vymazKupon(id, typ) {
      if (
        window.confirm(
          "Naozaj chcete zrušiť platnosť kupónu pred uplynutím doby? Kupón sa nebude dať spätne aktivovať."
        )
      ) {
        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();

        var urlType = "";

        if (typ == "/product/coupon/list.php") {
          urlType = "/product/coupon/delete.php?id=";
        } else {
          urlType = "/subscription/coupon/delete.php?id=";
        }

        let config = {
          method: "get",
          maxBodyLength: Infinity,
          url: this.$store.state.api + urlType + id,
          // headers: {
          //   ...data.getHeaders()
          // },
          data: data,
        };

        axios
          .request(config)
          .then((response) => {
            console.log(JSON.stringify(response.data));

            if (response.data.status == "Request fullfiled") {
              this.$store.state.SnackBarText =
                "Váš kupón s id: " + id + " bol vymazaný";
              this.nacitajKupony("kosik");
              this.nacitajKupony("predplatne");
            } else {
              this.$store.state.SnackBarText = "Nepodarilo sa vymazať kupón";
            }
          })
          .catch((error) => {
            this.$store.state.SnackBarText =
              "Nepodarilo sa vymazať kupón" + error;
          });
      }
    },
    getRemainingDays(validUntil) {
      // Aktuálna časová pečiatka
      let now = Date.now();

      // Časová pečiatka konca platnosti kupónu
      let expiration = validUntil;

      // Vypočítať rozdiel medzi časovými pečiatkami v milisekundách
      let difference = expiration - now;

      // Prepočet na dni
      let remainingDays = Math.ceil(difference / (1000 * 60 * 60 * 24));

      return remainingDays;
    },
    podmienkyPreOdoslanie() {
      // Regulárny výraz na overenie, či zlava obsahuje len čísla, bodku a čiarku
      const regex = /^[0-9.,]+$/;

      if (
        this.nazov.trim() !== "" &&
        this.zlava.trim() !== "" &&
        regex.test(this.zlava) && // Overiť, či zlava obsahuje len čísla, bodku a čiarku
        !isNaN(this.doba) &&
        this.doba > 0
      ) {
        return true; // Všetky podmienky sú splnené, môžete odoslať údaje
      } else {
        this.$store.state.SnackBarText =
          "Nepodarilo sa vytvoriť zápis, pre odoslanie vyplňte správne všetky polia";
        return false; // Niektoré podmienky nie sú splnené, neodosielajte údaje
      }
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

.table {
  width: 80%;
  border-collapse: collapse;
  margin: 2em 10%;
  margin-bottom: 6em;
  max-width: unset;
}

.add {
  margin-bottom: 2em;
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
  padding: 0.5em 0.1em;
}

th:nth-child(2),
td:nth-child(2) {
  text-align: center;
}

.button {
  width: 2.2em;
  padding: 0.05em 0.8em;
  margin: 0.1em 0;
  justify-content: center;

  a {
    font-size: 0.52em;
  }
}

td:last-child .button {
  margin-left: auto;
}

form .button {
  width: 4em;
}

.add {
  .row {
    width: 90%;
    margin: 0;
    margin-right: auto;
  }

  input {
    font-size: 0.9em;
    padding: 0.4em 1em;
  }

  th,
  td {
    text-align: left;
  }

  tr th {
    font-weight: 400;
    font-size: 1.6em;
    padding-left: 0.1em;
  }

  td {
    padding-left: 0.1em;
  }

  td:not(td:last-child) {
    border-bottom: none;
  }

  .button {
    padding: 0.15em 0.8em;
  }
}

.row-form select {
  border-radius: 1.0625em;
  background: #90ca50;
  box-shadow: -7px 5px 15px 0px rgba(0, 0, 0, 0.25) inset,
    0px 4px 4px 0px rgba(0, 0, 0, 0.25);
  padding: 0.35em 5%;
  width: 90%;
  border: none;
  width: 100%;
  cursor: pointer;
}

@media only screen and (max-width: 1500px) {
  .table {
    margin: 2em 2%;
    width: 98%;
  }
}
</style>
