<template>
  <section :id="$route.name">
    <form form @submit.prevent="upraveniePredplatneho" class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Predplatné</h5>

      <div class="line horizontal w-80"></div>

      <div class="obsah">
        <div class="column">
          <div class="nadpis"><p class="Adumu">Mesačné</p></div>

          <div class="row-form">
            <label for="cena-mesacne">Cena predplatného:</label>
            <input
              ref="predplatne.mesacne.cena"
              type="text"
              name="cena-mesacne"
              placeholder="2,25 EUR"
              v-model="predplatne.mesacne.cena"
            />
          </div>

          <div class="row-form">
            <label for="zlava-mesacne">Zľava predplatného:</label>
            <input
              ref="predplatne.mesacne.detaily.zlava"
              type="text"
              name="zlava-mesacne"
              placeholder="10 %"
              v-model="predplatne.mesacne.detaily.zlava"
            />
          </div>

          <div class="row-form checkbox">
            <input
              ref="predplatne.mesacne.detaily.aktivnaZlava"
              v-model="predplatne.mesacne.detaily.aktivnaZlava"
              type="checkbox"
              name="aktivovat-mesacne"
            />
            <label for="aktivovat-mesacne">Aktivovať zľavu:</label>
          </div>
        </div>
        <div class="column">
          <div class="nadpis"><p class="Adumu">Polročné</p></div>

          <div class="row-form">
            <label for="cena-polrocne">Cena predplatného:</label>
            <input
              ref="predplatne.polrocne.cena"
              type="text"
              name="cena-polrocne"
              placeholder="2,25 EUR"
              v-model="predplatne.polrocne.cena"
            />
          </div>

          <div class="row-form">
            <label for="zlava-polrocne">Zľava predplatného:</label>
            <input
              ref="predplatne.polrocne.detaily.zlava"
              type="text"
              name="zlava-polrocne"
              placeholder="10 %"
              v-model="predplatne.polrocne.detaily.zlava"
            />
          </div>

          <div class="row-form checkbox">
            <input
              ref="predplatne.polrocne.detaily.aktivnaZlava"
              v-model="predplatne.polrocne.detaily.aktivnaZlava"
              type="checkbox"
              name="aktivovat-polrocne"
            />
            <label for="aktivovat-polrocne">Aktivovať zľavu:</label>
          </div>
        </div>
        <div class="column">
          <div class="nadpis"><p class="Adumu">Ročné</p></div>

          <div class="row-form">
            <label for="cena-rocne">Cena predplatného:</label>
            <input
              ref="predplatne.rocne.cena"
              type="text"
              name="cena-rocne"
              placeholder="2,25 EUR"
              v-model="predplatne.rocne.cena"
            />
          </div>

          <div class="row-form">
            <label for="zlava-rocne">Zľava predplatného:</label>
            <input
              ref="predplatne.rocne.detaily.zlava"
              type="text"
              name="zlava-rocne"
              placeholder="10 %"
              v-model="predplatne.rocne.detaily.zlava"
            />
          </div>

          <div class="row-form checkbox">
            <input
              ref="predplatne.rocne.detaily.aktivnaZlava"
              v-model="predplatne.rocne.detaily.aktivnaZlava"
              type="checkbox"
              name="aktivovat-rocne"
            />
            <label for="aktivovat-rocne">Aktivovať zľavu:</label>
          </div>
        </div>
      </div>
      <div class="column">
        <button
          :class="{
            'not-have-permission':
              !this.$store.state.userRoles?.roles?.includes(
                'subscription_edit'
              ),
          }"
          type="submit"
          class="button"
        >
          <p>Uložiť</p>
        </button>
        <div @click="$router.go(-1)" class="zrusit click">
          <img src="@/assets/images/icons/zrusit.svg" alt="Zrušiť" />
          <p>Zrušiť</p>
        </div>
      </div>
    </form>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);

    for (let i = 1; i < 4; i++) {
      this.nacitajPredplatne(i);
    }
  },
  data() {
    return {
      predplatne: {
        mesacne: {
          id: 1,
          cena: "",
          name: "",
          expiration: "",
          detaily: {},
        },
        polrocne: {
          id: 2,
          cena: "",
          name: "",
          expiration: "",
          detaily: {},
        },
        rocne: {
          id: 3,
          cena: "",
          name: "",
          expiration: "",
          detaily: {},
        },
      },
      predplatneData: {},
    };
  },
  methods: {
    nacitajPredplatne(ID) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/load.php/?id=" + ID,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (ID == 1) {
            this.predplatne.mesacne.cena = response.data.data.price;
            this.predplatne.mesacne.name = response.data.data.name;
            this.predplatne.mesacne.expiration = response.data.data.expiration;
            this.predplatne.mesacne.detaily = JSON.parse(
              response.data.data.details
            );
          } else if (ID == 2) {
            this.predplatne.polrocne.cena = response.data.data.price;
            this.predplatne.polrocne.name = response.data.data.name;
            this.predplatne.polrocne.expiration = response.data.data.expiration;
            this.predplatne.polrocne.detaily = JSON.parse(
              response.data.data.details
            );
          } else if (ID == 3) {
            this.predplatne.rocne.cena = response.data.data.price;
            this.predplatne.rocne.name = response.data.data.name;
            this.predplatne.rocne.expiration = response.data.data.expiration;
            this.predplatne.rocne.detaily = JSON.parse(
              response.data.data.details
            );
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    upraveniePredplatneho() {
      this.aktualizujPredplane(1);
      this.aktualizujPredplane(2);
      this.aktualizujPredplane(3);
    },
    aktualizujPredplane(ID) {
      var typ = "";
      if (ID == 1) {
        typ = "mesacne";
      } else if (ID == 2) {
        typ = "polrocne";
      } else if (ID == 3) {
        typ = "rocne";
      }

      //orpavenie ceny na bodku pre databázu
      if (
        this.predplatne[typ].cena != undefined &&
        this.predplatne[typ].cena.includes(",")
      ) {
        this.predplatne[typ].cena = this.predplatne[typ].cena.replace(",", ".");
      }

      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      data.append("id", this.predplatne[typ].id);
      data.append("free", "false");
      data.append("deleted", "false");
      data.append("new", "");
      data.append("virtuality", "true");
      data.append("category", "predplatne");
      data.append("name", this.predplatne[typ].name);
      data.append("difficulty", "1");
      data.append("expiration", this.predplatne[typ].expiration);
      data.append("price", this.predplatne[typ].cena);
      data.append("details", JSON.stringify(this.predplatne[typ].detaily));

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/update.php/",
        //   headers: {},
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          if (response.data.status == "Request fullfiled") {
            this.$store.state.SnackBarText =
              "Upravenie cien predplatného bolo úspešné";
            this.$router.push("/admin");
          } else {
            this.$store.state.SnackBarText =
              "Nepodarilo sa upraviť ceny predplatného";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText =
            "Nepodarilo sa upraviť ceny predplatného";
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
  line-height: 115%; /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

.scroll {
  width: 100%;
  max-width: unset;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 2%;
}
.column {
  height: auto;
  width: 33.333%;
  max-width: 19em;
  padding: 1%;
  box-sizing: border-box;
  align-self: center;
}

.row-form {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.column:first-child {
  width: 33.333%;
  padding-left: 3%;
}
.column:last-child {
  width: 33.333%;
  padding-right: 3%;
}

.row-form input,
.row-form textarea,
.row-form select {
  border-radius: 1.0625em;
  background: #90ca50;
  box-shadow: -7px 5px 15px 0px rgba(0, 0, 0, 0.25) inset,
    0px 4px 4px 0px rgba(0, 0, 0, 0.25);
  padding: 0.35em 5%;
  margin: 0.3em auto 1.3em;
  width: 90%;
}

label {
  font-size: 1.5em;
}

input,
select {
  font-size: 1.1em;
}

input::placeholder {
  color: #00000063;
}

.row-form textarea {
  font-size: 0.9em;
  padding: 1em 5%;
}

.row-form select {
  border: none;
  width: 100%;
  cursor: pointer;
}

/* Štýly pre custom input file */
.custom-file-input {
  display: none; /* Schovať natívny input file */
}

.stupnice .button,
.zvukova-stopa .button {
  display: inline-block;
  text-align: center;
  font-size: 1em;
}

/* Štýly pre vybraný súbor */
.selected-file-info {
  margin-top: 1.5em;
  font-size: 0.6em;
  width: 70%;
  text-align: center;
  cursor: pointer;
}

.stupnice {
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
  gap: 5%;
  justify-content: center;
  font-size: 1vw;
}

.nazov-stupnice {
  font-size: 1.2em;
}

.stupnica,
.zvukova-stopa {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1em 0;
  width: 30%;
}

.zvukova-stopa {
  font-size: 1vw;
}

.zrusit {
  display: flex;
  gap: 3%;
  align-items: center;
  justify-content: center;
  font-size: 1.5em;

  img {
    width: 1em;
  }
}

button {
  margin: 1em auto;
  border-radius: 0.7em;
  padding: 0.3em 3em 0.3em;
}

form > div:nth-child(1) > div:nth-child(6) {
  margin-top: 2em;
}

.checkbox {
  display: flex;
  flex-direction: row;

  input {
    width: auto;
    margin: auto 0.7em;
    /* font-size: 1em; */
    transform: scale(1.5);
    cursor: pointer;
  }

  label {
    font-size: 1.1em;
    font-weight: 100;
  }
}

.obsah {
  display: flex;
  justify-content: center;
  justify-content: space-around;
  height: 40vh;
}

.nadpis {
  border-radius: 1.5625rem;
  border: 0.3em solid #fef35a;
  background: var(
    --Linear,
    linear-gradient(140deg, #90c94f 35.64%, #fef35a 99.4%)
  );
  box-shadow: 5px 5px 10px 0 rgba(0, 0, 0, 0.35);
  padding: 0.3em 1.3em 0.5em;
  width: 80%;
  max-width: 14em;
  margin: 1em auto 2em;

  p {
    font-size: 2.5em;
    font-weight: 500;
  }
}
</style>
