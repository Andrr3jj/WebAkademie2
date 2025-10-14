<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <h6>Tu sme pre vás</h6>

      <div class="box">
        <div class="box-item">
          <p class="w-80">
            Chcete sa naučit obľúbenú pieseň, no na našej stránke ste ju
            nenašli?
          </p>
          <p class="w-60">
            Máte otázky ohľadom našich produktov alebo služieb?
          </p>
          <p class="w-60">Máte návrhy na vylepšenie našich služieb?</p>
          <p class="underline">Vaše názory, postrehy sú pre nás dôležité.</p>

          <h4>ČASTO KLADENÉ OTÁZKY</h4>

          <div class="button">
            <router-link to="/caste-otazky">ZISTIŤ VIAC</router-link>
          </div>
        </div>

        <div class="line"></div>

        <div class="box-item">
          <h3>NAPÍŠTE NÁM</h3>

          <form @submit.prevent="odoslanieFormularu()">
            <div class="row">
              <div><img src="@/assets/images/icons/profil.svg" /></div>
              <input
                type="text"
                v-model="meno"
                name=""
                id=""
                placeholder="Meno"
              />
            </div>

            <div class="row">
              <div><img src="@/assets/images/icons/email.svg" /></div>
              <input
                type="text"
                v-model="email"
                name=""
                id=""
                placeholder="Email"
              />
            </div>

            <div class="row">
              <textarea
                rows="4"
                name=""
                id=""
                placeholder="Otázka..."
                v-model="sprava"
              ></textarea>
            </div>

            <button class="button" type="submit">
              <a>ODOSLAŤ</a>
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <div class="mobile" :id="$route.name">
    <div class="scroll">
      <section>
        <TheHeadline></TheHeadline>

        <h6>Tu sme pre vás</h6>

        <div class="box">
          <div class="box-item">
            <p>
              Chcete sa naučit obľúbenú pieseň, no na našej stránke ste ju
              nenašli?
            </p>
            <p>Máte otázky ohľadom našich produktov alebo služieb?</p>
            <p>Máte návrhy na vylepšenie našich služieb?</p>
            <p class="underline">Vaše názory, postrehy sú pre nás dôležité.</p>

            <h4>ČASTO KLADENÉ OTÁZKY</h4>

            <div class="button">
              <router-link to="/caste-otazky">ZISTIŤ VIAC</router-link>
            </div>
          </div>
        </div>
      </section>

      <section>
        <div class="box">
          <div class="box-item">
            <h3>NAPÍŠTE NÁM</h3>

            <form @submit.prevent="odoslanieFormularuMobil()">
              <div class="row">
                <div><img src="@/assets/images/icons/profil.svg" /></div>
                <input
                  type="text"
                  name=""
                  v-model="meno"
                  id=""
                  placeholder="Meno"
                />
              </div>

              <div class="row">
                <div><img src="@/assets/images/icons/email.svg" /></div>
                <input
                  type="text"
                  name=""
                  v-model="email"
                  id=""
                  placeholder="Email"
                />
              </div>

              <div class="row">
                <textarea
                  rows="3"
                  name=""
                  id=""
                  placeholder="Otázka..."
                  v-model="sprava"
                ></textarea>
              </div>

              <button class="button" type="submit">
                <a>ODOSLAŤ</a>
              </button>
            </form>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";

export default {
  mounted() {
    window.scrollTo(0, 0);
  },
  data() {
    return {
      meno: "",
      email: "",
      sprava: "",
    };
  },
  components: { TheHeadline },
  methods: {
    odoslanieFormularu() {
      if (this.podmienkyPreOdoslanie()) {
        this.odoslanie();
      }
    },

    odoslanieFormularuMobil() {
      if (this.podmienkyPreOdoslanie()) {
        this.odoslanie();
      }
    },

    podmienkyPreOdoslanie() {
      if (
        this.meno.trim() === "" ||
        this.email.trim() === "" ||
        this.sprava.trim() === ""
      ) {
        this.$store.state.SnackBarText =
          "Pre odoslanie formuláru je nutné vyplniť všetky polia";
        return false;
      } else if (!this.email.includes("@")) {
        this.$store.state.SnackBarText =
          "Neplatná emailová adresa, chýba znak @";
        return false;
      } else if (!this.email.includes(".")) {
        this.$store.state.SnackBarText =
          "Neplatná emailová adresa, zadajte správnu emailovú adresu";
        return false;
      } else if (this.sprava.length < 15) {
        this.$store.state.SnackBarText = "Napíšte nám aspoň Pekný deň :)";

        return false;
      }

      return true;
    },

    odoslanie() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      data.append("name", this.meno);
      data.append("email", this.email);
      data.append("message", this.sprava);

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/form/pomocAddRecord.php/",
        // headers: {
        //   ...data.getHeaders(),
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log("Odoslaný formulár: ", JSON.stringify(response.data));
          if (response.data.status == "Request succesfull") {
            this.$store.state.SnackBarText = "Formulár bol úspešne odoslaný";

            this.$router.push("/uspesne-odoslanie-formularu");
          } else {
            this.$store.state.SnackBarText =
              "Niečo sa pokazilo. Formulár sa nepodarilo odoslať";
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
@import "@/assets/sass/_colors.scss";

p {
  margin: 0.6em 10%;
  text-align: center;
  font-size: 1.4em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.15625rem */
  width: 80%;
}

.underline {
  font-weight: 600;
  text-decoration-line: underline;
  width: 100%;
}

h4,
h3 {
  font-size: 2em;
  text-align: center;

  line-height: 115%; /* 2.51563rem */
  letter-spacing: 0.05em;
}

h3 {
  margin: 0.3em auto 0.5em;
}

h4 {
  margin: 0.3em auto 0.5em;
}

button {
  margin: 1em auto 0;
}

.mobile h4 {
  margin: 0.5em 0 0;
}

.box {
  padding: 0 3%;
  margin: 0 0 10px;
}

.box-item {
  max-width: 30em;
  justify-content: space-evenly;
}

.w-60 {
  width: 90%;
}

@media only screen and (max-width: 1450px) {
  .w-60 {
    width: 80%;
  }

  .box {
    gap: 3%;
  }
}
@media only screen and (max-width: 1080px) {
  .w-60 {
    width: 90%;
  }
  .w-80 {
    width: 100%;
  }

  h4 {
    font-size: 1.7em;
  }

  .box {
    gap: 3%;
  }
}

@media only screen and (max-width: 1000px) {
  .box-item {
    max-width: unset;
  }

  div.button {
    margin-bottom: 2em;
  }
}
@media only screen and (max-width: 750px) {
  div.button {
    margin-bottom: 0em;
  }
  .mobile {
    padding-bottom: 5em;
  }
}

@media only screen and (max-width: 450px) {
  div.button {
    font-size: 110%;
    margin-bottom: 0;
    margin-top: 1em;
    padding: 0.5em 2.5em;
  }

  .mobile section p {
    font-size: 1.2em;
  }

  .mobile section p.underline {
    font-size: 1.4em;
  }

  h6 {
    font-size: 5vw;
    margin: 0.5em 0 0;
  }
  .box {
    margin: 0.5em 0 1em;
  }
}
</style>
