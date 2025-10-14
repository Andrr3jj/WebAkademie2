<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="line horizontal w-80"></div>
      <p class="center">
        Zabudli ste heslo? Nezúfajte! Na tejto podstránke môžete jednoducho
        vytvoriť nové heslo a získať prístup k svojmu účtu.
      </p>

      <form @submit.prevent="overeniePredOdoslanim()">
        <div class="full-row">
          <label for="adresa">Vytvorte heslo: </label>
          <div class="row">
            <div><img src="@/assets/images/icons/heslo.svg" /></div>
            <input
              :type="showInput ? 'text' : 'password'"
              name="email"
              v-model="formData.heslo"
            />
          </div>
        </div>

        <div class="full-row">
          <label for="adresa">Potvrďte heslo: </label>
          <div class="row">
            <div><img src="@/assets/images/icons/heslo.svg" /></div>
            <input
              :type="showInput ? 'text' : 'password'"
              name="email"
              v-model="formData.overenieHesla"
            />
          </div>
        </div>
        <div
          @click="showInput ? (showInput = false) : (showInput = true)"
          v-if="!showInput"
          class="underline"
        >
          Zobraziť
        </div>
        <div
          @click="showInput ? (showInput = false) : (showInput = true)"
          v-if="showInput"
          class="underline"
        >
          Skryť
        </div>

        <button class="button" type="submit">
          <a> Zmeniť heslo </a>
        </button>
      </form>
    </div>
  </section>

  <div :id="$route.name" class="mobile">
    <div class="scroll">
      <section>
        <TheHeadline></TheHeadline>

        <div class="line horizontal w-80"></div>
        <p class="center">
          Zabudli ste heslo? Nezúfajte! Na tejto podstránke môžete jednoducho
          vytvoriť nové heslo a získať prístup k svojmu účtu.
        </p>

        <form @submit.prevent="overeniePredOdoslanim()">
          <div class="full-row">
            <label for="adresa">Vytvorte heslo: </label>
            <div class="row">
              <div><img src="@/assets/images/icons/heslo.svg" /></div>
              <input
                :type="showInput ? 'text' : 'password'"
                name="email"
                v-model="formData.heslo"
              />
            </div>
          </div>

          <div class="full-row">
            <label for="adresa">Potvrďte heslo: </label>
            <div class="row">
              <div><img src="@/assets/images/icons/heslo.svg" /></div>
              <input
                :type="showInput ? 'text' : 'password'"
                name="email"
                v-model="formData.overenieHesla"
              />
            </div>
          </div>
          <div
            @click="showInput ? (showInput = false) : (showInput = true)"
            v-if="!showInput"
            class="underline"
          >
            Zobraziť
          </div>
          <div
            @click="showInput ? (showInput = false) : (showInput = true)"
            v-if="showInput"
            class="underline"
          >
            Skryť
          </div>

          <button class="button" type="submit">
            <a> Zmeniť heslo </a>
          </button>
        </form>
      </section>
    </div>
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";

export default {
  mounted() {
    window.scrollTo(0, 0);

    if (
      !this.$route.hash.includes("recoveryKey=") ||
      !this.$route.hash.split("=")[1]
    ) {
      this.$router.push("/");
      setTimeout(() => {
        this.$store.state.SnackBarText = "Váš obnovovací kľúč sa stratil";
      }, 1000);
    }
  },
  components: { TheHeadline },
  data() {
    return {
      formData: {
        heslo: "",
        overenieHesla: "",
      },
      showInput: false,
    };
  },
  created() {
    console.log(this.$route.hash.substring(1).split("=")[1]);
  },
  methods: {
    overeniePredOdoslanim() {
      if (this.isFormValid()) {
        // Vykonajte ďalšie kroky, napríklad odoslanie formulára
        this.resetovanie();
      }
    },
    resetovanie() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      data.append("recoveryKey", this.$route.hash.substring(1).split("=")[1]);
      data.append("newPassword", this.formData.heslo);

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api + "/user/auth/forgotPasswordSetNewPassword.php",
        // headers: {
        //   ...data.getHeaders(),
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data.data));
          switch (response.data.status) {
            case "Request succesfull":
              this.$store.state.SnackBarText = "Vaše heslo bolo úspešne zmené";
              this.$router.push("/");
              return;
            case "Request failed":
              switch (response.data.data) {
                case "New password is same as old":
                  this.$store.state.SnackBarText =
                    "Vaše heslo sa nemôže zhodovať s heslom, ktoré sa už používa";
                  return;
                case "recoveryKey not found":
                  this.$store.state.SnackBarText =
                    "Platnosť obnovovacieho kľúču skončila";
                  return;

                case "Trying too often":
                  this.$store.state.SnackBarText =
                    "Niečo sa pokazilo, skúste neskôr prosím";
                  return;
                default:
                  this.$store.state.SnackBarText =
                    "Niečo sa pokazilo. Ak problém pretrváva prosím kontaktujte nás.";
                  return;
              }
            case "Invalid request":
              this.$store.state.SnackBarText =
                "Prosím vyplnte všetky povinné polia";
              return;
            default:
              this.$store.state.SnackBarText =
                "Niečo sa pokazilo. Ak problém pretrváva prosím kontaktujte nás.";
              return;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    isFormValid() {
      // Overenie, či email nie je prázdny a obsahuje znak '@'
      if (
        this.formData.heslo.trim() === "" ||
        this.formData.overenieHesla.trim() === ""
      ) {
        this.$store.state.SnackBarText =
          "Pre zmenu hesla je potrebné napísať heslo do obidvoch polí.";
        return false;
      } else if (this.formData.heslo.length < 8) {
        this.$store.state.SnackBarText = "Heslo musí mať aspoň 8 znakov";

        return false;
      } else if (!/[A-Z]/.test(this.formData.heslo)) {
        this.$store.state.SnackBarText =
          "Heslo musí obsahovať aspoň jedno veľké písmeno";

        return false;
      } else if (!/\d/.test(this.formData.heslo)) {
        this.$store.state.SnackBarText =
          "Heslo musí obsahovať aspoň jedno číslo";
        return false;
      } else if (this.formData.heslo != this.formData.overenieHesla) {
        this.$store.state.SnackBarText = "Vaše heslá sa nezhodujú";
        return false;
      } else return true;
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.scroll {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.line.horizontal {
  min-height: 0.5rem;
}

.center {
  text-align: center;
  // margin-bottom: 1em;
}

form {
  max-width: 45em;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.full-row {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: 30em;
}

.row {
  margin: 0.5em 0 1em;
  max-width: 30em;
  width: 100%;
}

p {
  margin: 1.5em auto 3em auto;
  text-align: right;
  font-size: 1.5em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.15625rem */
  width: 90%;
  max-width: 40em;

  &:has(strong) {
    width: 60%;
  }
}

label {
  font-size: 1.5em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%;
  margin-right: auto;
}

.underline {
  font-size: 1.56em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 1.79688rem */
  letter-spacing: 0.09em;
  text-decoration-line: underline;
  text-align: center;
  margin: 0.5em auto 1em;
  transition-duration: 0.5s;
  cursor: pointer;
  transition-duration: 0.2s;

  &:hover {
    transform: scale(1.1);
  }
}
h4,
h3 {
  font-size: 2em;
  text-align: center;
  // margin: 2em auto 0.6em;
  margin: 0.8em auto 0em;

  line-height: 115%; /* 2.51563rem */
  letter-spacing: 0.05em;
}

.button {
  padding: 0.6em 0.9em 0.5em;
  margin: 0 auto 3em;
}

.box-item {
  padding: 3em 0;
}

.button.green {
  margin: 1.8em 18% auto auto;
}

.absolute-img {
  position: absolute;
  bottom: -0.6em;
  left: -1em;
  width: 31em;
  filter: drop-shadow(10px 5px 10px rgba(0, 0, 0, 0.35));
}

.mobile {
  .row {
    max-width: unset;
  }
  h3 {
    font-size: 2.4em;
  }
  .center {
    margin: 1.5em 5% 1em;
    width: 90%;
  }

  .full-row {
    width: 100%;
  }

  .line.horizontal {
    min-height: 0.4rem;
    margin: auto;
    margin-top: 0.5em;
  }

  section {
    width: 100%;

    form {
      width: 85%;
      max-width: unset;
      margin: 0.6em auto 0.3em;
      font-size: 90%;
    }
    .button {
      margin: 1em auto 1.5em;
    }
  }

  .underline {
    font-size: 1.55em;
  }

  .recovery {
    padding: 3em 0;
  }
}

@media screen and (max-width: 1700px) {
  .absolute-img {
    left: -4%;
    width: 29em;
  }

  h4,
  h3 {
    margin: 2em auto 0.6em;
  }
}

@media screen and (max-width: 1600px) {
  .absolute-img {
    left: -8%;
  }
}

@media screen and (max-width: 1510px) {
  .absolute-img {
    display: none;
  }
}
</style>
