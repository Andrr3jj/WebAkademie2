<template>
  <div class="main">
    <div class="left">
      <p class="nadpis">Nahraj si svoje zápisy do zoznamu!</p>
      <p class="text">
        Použi kód s knihy a nahraj si všetky svoje číselné zápisy do online
        priestoru. Tak ich budeš mať vždy pri sebe, dostupné kdekoľvek a
        kedykoľvek!
      </p>
    </div>
    <div class="right">
      <div class="postup">
        <div class="obsah">
          <p class="nadpis2">Ako na to?</p>
          <ol>
            <li>Zadaj kód z knižky do poľa nižšie</li>
            <li>
              Potvrď kód a zápisy sa automaticky pridajú do tvojho zoznamu
              piesní
            </li>
            <li>
              Užívaj si rýchly a pohodlný prístup k svojim piesňam na akomkoľvek
              zariadení
            </li>
          </ol>
        </div>
        <img
          class="image"
          src="@/assets/images/gallery/poukazkyListy.png"
          alt="Ikonka darčekových"
        />
      </div>
      <div class="input">
        <div class="row">
          <input
            type="text"
            placeholder="Zadajte kód z knižky"
            v-model="kod"
            @input="formatKod"
          />
        </div>
        <div @click="sendKod()" class="button">
          <p>Potvrdiť</p>
        </div>
      </div>
    </div>
    <img
      src="@/assets/images/gallery/poukazkyListy.png"
      alt="Ikonka darčekových"
    />
  </div>
</template>

<script>
export default {
  data() {
    return {
      kod: "",
    };
  },
  methods: {
    formatKod() {
      // 1. Odstránime všetky nealfanumerické znaky
      let cleaned = this.kod.replace(/[^A-Za-z0-9]/g, "");

      // 2. Prevedieme na veľké písmená
      cleaned = cleaned.toUpperCase();

      // 3. Orezanie na max 12 znakov
      cleaned = cleaned.slice(0, 12);

      // 4. Vloženie "-" po každom 4. znaku
      const formatted = cleaned.match(/.{1,4}/g)?.join("-") || "";

      // 5. Nastavíme späť
      this.kod = formatted;
    },
    sendKod() {
      if (this.kod.length != 14) {
        this.$store.state.SnackBarText =
          "Váša darčeková poukážka musí mať 12 znakov";
        return;
      }
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/gift_card/apply.php?code=" +
          this.kod,
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          switch (response.data.status) {
            case "Request succesfull":
              this.$store.state.SnackBarText =
                "Váša darčeková poukážka sa úspešne použila";
              this.$store.dispatch("pouzivatelVlastneneProdukty");
              return;
            case "Invalid request":
              this.$store.state.SnackBarText =
                "Váša darčeková poukážka musí mať 12 znakov";
              return;
            default:
              // alert("Neznáma odpoveď");
              this.$store.state.SnackBarText = "Niečo sa pokazilo";
              return;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.nadpis,
.nadpis2 {
  font-size: 1.5em;
  font-weight: 700;
  text-align: left;
  margin: 2em auto 0.4em 0;
}
.nadpis2 {
  font-size: 1.3em;
  margin: 1em auto 0.4em 0;
}

.text {
  font-size: 1.2em;
  text-align: left;
  margin: 0 auto 0 0;
  max-width: 32em;
}

.left {
  justify-content: flex-start !important;
}

ol {
  padding-left: 1.2em;
  margin: 0.5em 0;
  margin-right: auto;
}

li {
  text-align: left;
  font-size: 1.05em;
  padding-left: 0.3em;
  margin: 0.4em 0;
}

.button {
  padding: 0.2em 0.9em !important;
  width: max-content;

  p {
    font-size: 0.7em;
  }
}

input {
  font-size: 1em;
  width: 13em;
}

.input {
  display: flex;
  align-items: center;
  gap: 1.2em;
  justify-content: flex-start;
  margin-right: auto;
}

img {
  position: absolute;
  right: -5%;
  width: 9em;
  top: 42%;
  transform: translateY(-50%);
}

.main {
  position: relative;
  width: 90%;
}

.image {
  display: none;
}

.postup {
  margin-right: auto;
}

@media only screen and (max-width: 1300px) {
  .scroll {
    display: flex;
    flex-direction: column;
  }
}

@media only screen and (max-width: 750px) {
  input {
    width: 15em;
    font-size: 1.3em;
  }

  .input {
    margin-right: unset;
  }
  .button {
    margin: auto !important;
  }

  img {
    display: none;
  }
  .image {
    display: flex;
  }

  .postup {
    margin-right: auto;
    display: flex;
    transition: none;
    align-items: center;
    justify-content: center;
    gap: 2%;
    width: 100%;
  }

  .image {
    position: relative;
    top: 0;
    right: 0;
    transform: unset;
    margin: 0.4em;
    font-size: 2.5vw;
    margin-top: -7vw;
    margin-right: -5vw;
  }
}

@media only screen and (max-width: 450px) {
  .input {
    flex-direction: column;
  }
}
</style>
