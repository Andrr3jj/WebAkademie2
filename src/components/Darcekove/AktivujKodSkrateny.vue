<template>
  <div class="main">
    <img
      src="@/assets/images/gallery/poukazkyListy.png"
      alt="Ikonka darčekových"
    />
    <p class="nadpis">Aktivuj svoj prístup</p>

    <div class="input">
      <div class="row">
        <input
          type="text"
          placeholder="Tvoj unikátny kód"
          v-model="kod"
          @input="formatKod"
        />
      </div>
      <div @click="sendKod()" class="button">
        <p>Aktivovať</p>
      </div>
    </div>
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
              this.$store.commit(
                "SET_GRATULATION",
                `Vaše zápisy ku knižke vám boli pridané. 
        Získavate exkluzívny prístup k sekcii s videami ku knižke!`
              );

              return;
            case "Request failed":
              if (response.data.data == "gift card expired/used") {
                this.$store.state.SnackBarText =
                  "Váša darčeková poukážka vypršala alebo už bola použitá";
                this.$store.commit("SET_GRATULATION", false);
                return;
              }
              this.$store.state.SnackBarText =
                "Overenie darčekovej poukážky zlyhalo";
              return;
            case "Invalid request":
              this.$store.state.SnackBarText =
                "Váša darčeková poukážka musí mať 12 znakov";
              this.$store.commit("SET_GRATULATION", false);
              return;
            default:
              // alert("Neznáma odpoveď");
              this.$store.state.SnackBarText = "Niečo sa pokazilo";
              this.$store.commit("SET_GRATULATION", false);
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
.nadpis {
  font-size: 2.2em;
  font-weight: 700;
  text-align: center;
  margin: 0.5em auto 0.2em;
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
  width: 16em;
}

.input {
  display: flex;
  align-items: center;
  width: 90%;
  flex-direction: column;
  margin: auto;
}

.main {
  width: 90%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

img {
  max-width: 10em;
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
    margin-bottom: 3em;
  }

  .button {
    margin: auto !important;
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
