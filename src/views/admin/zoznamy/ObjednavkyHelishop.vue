<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <div class="nadpis">
        <h5>Výpisy objednávok Helishop</h5>
      </div>

      <div class="line horizontal w-80"></div>

      <div class="nastavenia">
        <p @click="nastavenia = !nastavenia" class="button first">
          {{
            nastavenia ? "Skryť spôsoby dopravy" : "Zobraziť spôsoby dopravy"
          }}
        </p>
      </div>
      <div v-if="nastavenia" class="nastavenia">
        <!-- <div class="box-ceny left">
          <p>Cena dobierky:</p>
          <input type="text" v-model="cenaDobierkaHodnota" id="" />
          <div class="button" @click="zmenCenuDobierka()"><p>Uložiť</p></div>
        </div> -->
        <div class="box-ceny right">
          <ul>
            <li v-for="(option, index) in deliveryOptions" :key="index">
              <p @dblclick="deleteDelivery(index)" class="row">
                <input
                  type="text"
                  v-model="option.name"
                  placeholder="Názov služby"
                />
                -
                <input
                  type="number"
                  v-model.number="option.price"
                  placeholder="Cena"
                  min="0"
                />
                EUR
              </p>
            </li>
            <div class="button Adumu" @click="updateOption()">Uložiť zmeny</div>
          </ul>
        </div>
      </div>

      <obejdnavky-mesiac
        :dataVyhladavanie="dataVyhladavanie"
      ></obejdnavky-mesiac>

      <div class="search">
        <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
        <input
          @input="oneskoreneVyhladavanie()"
          v-model="search"
          type="text"
          placeholder="Vyhľadávanie podľa čísla objednávky, alebo emailu"
        />
      </div>
    </div>
  </section>
</template>

<script>
import ObejdnavkyMesiac from "../../../components/Helishop/ObejdnavkyMesiac.vue";
export default {
  components: {
    ObejdnavkyMesiac,
  },
  mounted() {
    window.scrollTo(0, 0);

    // this.nacitajUcty();
    this.cenaDobierka();
    this.sposobyDopravy();
  },
  data() {
    return {
      data: {},
      mont: 1,
      actualYear: null,
      vyhladavanie: "",
      expandedMonths: [1],
      clickCount: 0,
      cenaDobierkaHodnota: 0,
      deliveryOptions: [],
      nastavenia: false,
      timer: null,
      search: "",
      dataVyhladavanie: [],
    };
  },
  methods: {
    oneskoreneVyhladavanie() {
      // Vyčkajte 500 ms a potom zavolajte stiahniID()
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.vyhladavanieObjednavky();
      }, 500);
    },
    vyhladavanieObjednavky() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/eshop/searchOrders.php?search=" +
          this.search,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.dataVyhladavanie = response.data;
          } else {
            this.dataVyhladavanie = [];
          }
        })
        .catch((error) => {
          console.log(error);
          this.dataVyhladavanie = [];
        });
    },
    deleteDelivery(index) {
      this.deliveryOptions.splice(index, 1);

      // Skontrolujeme, či ešte existuje nejaká položka s prázdnym `name`
      const hasEmpty = this.deliveryOptions.some((option) => !option.name);

      // Ak nie, pridáme novú prázdnu položku
      if (!hasEmpty) {
        this.pridajPrazdne();
      }
    },
    cenaDobierka() {
      this.cenaDobierkaHodnota = 0;
      fetch(this.$store.state.api + "/eshop/cashOnDelivery.txt")
        .then((response) => response.text()) // Získa textový obsah súboru
        .then((data) => {
          this.cenaDobierkaHodnota = parseFloat(data.trim()) / 100; // Prepočíta text na číslo
          this.cenaDobierkaHodnota = this.cenaDobierkaHodnota.toFixed(2); // Zobrazí číslo s 2 desatinnými miestami (reťazec)
        })
        .catch((error) => console.error("Chyba pri načítaní ceny:", error));
    },
    zmenCenuDobierka() {
      const axios = require("axios");
      let data = new FormData();
      let cenaVcentoch = parseInt(Math.round(this.cenaDobierkaHodnota * 100)); // Prepočíta na centy a pretypuje na celé číslo

      data.append("price", cenaVcentoch);
      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/eshop/updatecashOnDelivery.php",
        // headers: {},
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.$store.state.SnackBarText =
              "Dobierka bola úspešne aktualizovaná";
            this.cenaDobierka();
          }
          console.log("this.produktyData[id] :>> ", cenaVcentoch);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    sposobyDopravy() {
      this.deliveryOptions = [];
      setTimeout(() => {
        fetch(this.$store.state.api + "eshop/delivery.json")
          .then((response) => response.json())
          .then((data) => {
            this.deliveryOptions = data.map((delivery) => {
              let cenaVcentoch = parseInt(delivery.price, 10);
              let cenaEU = (cenaVcentoch / 100).toFixed(2);

              return {
                id: delivery.id,
                name: delivery.name,
                price: cenaEU,
              };
            });

            this.pridajPrazdne();
          })
          .catch((error) => {
            console.error("Chyba pri získavaní dát:", error);
          });
      }, 300);
    },
    pridajPrazdne() {
      let maxId =
        this.deliveryOptions.length > 0
          ? Math.max(...this.deliveryOptions.map((d) => Number(d.id.slice(1)))) // odstránime prvú nulu pred porovnaním
          : 0;

      let newIdNumber = maxId + 1;
      let newId = "0" + newIdNumber.toString(); // prilepíme nulu

      this.deliveryOptions.push({
        id: newId,
        name: "",
        price: 0,
      });
    },
    updateOption() {
      const axios = require("axios");
      console.log("idem robiť updateOption :>> ", this.deliveryOptions);

      // Filter pre položky, ktoré majú nastavené meno aj cenu
      let deliveryData = this.deliveryOptions
        .filter((option) => option.name && option.price) // Odfiltruj prázdne položky
        .map((option) => {
          return {
            id: option.id,
            name: option.name,
            price: Math.round(
              parseFloat(String(option.price).replace(",", ".")) * 100
            ),
          };
        });

      console.log("deliveryData :>> ", deliveryData);

      if (deliveryData.length === 0) {
        this.$store.state.SnackBarText =
          "Žiadne platné možnosti doručenia na uloženie.";
        return;
      }

      let data = new FormData();
      data.append("delivery", JSON.stringify(deliveryData));

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/eshop/updateDelivery.php",
        data: data,
      };

      // Odoslanie dát na server cez axios
      axios
        .request(config, { data })
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.$store.state.SnackBarText =
              "Možnosti doručenia boli úspešne aktualizované";
            this.sposobyDopravy();
          } else {
            this.$store.state.SnackBarText =
              "Chyba pri aktualizácii možností doručenia";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText =
            "Chyba pri aktualizácii možností doručenia, niečo sa pokazilo: " +
            error;
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

h5,
input {
  text-align: center;

  font-size: 2.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%; /* 2.51563rem */
  margin: 0;
  width: auto;
  margin-bottom: 0.3em;
}

input {
  width: 3em;
  font-size: 0.9em;
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
td:nth-child(2),
th:nth-child(3),
td:nth-child(3) {
  text-align: center;
}

td:nth-child(2) {
  font-weight: 600;
}

.button {
  width: 2.2em;
  padding: 0.15em 0.8em;
  margin: 0.1em 0;
  a {
    font-size: 0.52em;
    text-align: center;
    display: flex;
    justify-content: center;
    width: 100%;
  }
}

// .all {
//   position: absolute;
//   width: fit-content;
//   top: 9vw;
// }

.download-month {
  margin: 1em auto 0.5em;
  font-size: 1.8em;
  padding: 0.1em 1.3em;
}

.number {
  padding: 0.3em 2em;
  justify-content: center;

  p {
    font-size: 0.8em;
  }
}

.nadpis {
  display: flex;
  align-items: flex-start;
  justify-content: center;
  gap: 3em;
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
    line-height: 137.53%; /* 1.71913rem */
    letter-spacing: 0.0625rem;

    margin-bottom: -0.2em;
  }
}

table {
  max-width: 45em;
  margin: auto;
  width: 85%;

  thead {
    display: none;
  }

  td:first-child {
    display: flex;
    margin: 0.2em;
  }

  tr:hover .highlight {
    background-color: rgba(142, 200, 78, 0.5);
    border-radius: 0.5em; /* Zaoblené rohy */
    padding: 0.2em 0.5em; /* Vnútorné odsadenie okolo textu */
  }
}

.nastavenia {
  display: flex;
  flex-direction: row;
  gap: 1em;
  justify-content: space-evenly;

  margin: 3em auto 4em 0;
  /* justify-content: left; */

  .button.first {
    font-size: 1em;
    letter-spacing: 0.05em;
    font-weight: 700;

    width: auto;
    padding: 0.3em 1em !important;
  }
}

.box-ceny.right {
  width: 60%;
}
.box-ceny.left .button {
  font-size: 0.7em;
  padding: 0.3em 1.7em;

  p {
    margin: 0 !important;
  }
}

.box-ceny {
  margin: 1em;
  width: 30%;
  display: flex;
  align-items: center;
  flex-direction: column;

  p,
  input {
    font-size: 1.5em;
    margin: 0.7em auto;
  }

  .button {
    width: auto;
    font-size: 1em;
    padding: 0.3em 1.7em;
  }

  .button.Adumu {
    width: fit-content;
    margin: 0em auto 1em;
  }

  ul,
  li,
  p {
    width: 100%;
  }

  .row {
    width: 100%;
    display: flex;
    flex-direction: row;
    justify-content: space-around;
    align-items: center;
    padding: 0.2em 0.7em;
    box-sizing: border-box;

    margin: 0.7em auto;

    input {
      margin: 0 !important;
      width: auto;
      font-size: 0.8em;
    }

    input[type="text"] {
      width: inherit;
    }
  }
}
</style>
