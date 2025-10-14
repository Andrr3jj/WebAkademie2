<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="box">
        <div class="box-item">
          <p>Pre zobrazenie obsahu sa prosím prihláste.</p>
          <p>Ak ešte nemáte účet, môžete sa registrovať nižšie</p>

          <div class="button green">
            <router-link to="/registracia">Registrovať</router-link>
          </div>
        </div>

        <div class="line"></div>

        <div class="box-item">
          <h3>PRIHLÁSIŤ SA</h3>

          <form @submit.prevent="prihlasenie">
            <div class="row">
              <div><img src="@/assets/images/icons/email.svg" /></div>
              <input
                ref="email"
                type="text"
                name="email"
                id=""
                placeholder="Email"
                v-model="formData.email"
              />
            </div>

            <div class="row">
              <div><img src="@/assets/images/icons/heslo.svg" /></div>
              <input
                ref="heslo"
                type="password"
                name="heslo"
                id=""
                placeholder="Heslo"
                v-model="formData.heslo"
              />
            </div>

            <div class="underline">
              <router-link to="/zabudnute-heslo">Zabudnuté heslo</router-link>
            </div>

            <button class="button" type="submit">
              <img src="@/assets/images/icons/prihlasenie.svg" alt="" /><a
                >Prihlásiť sa</a
              >
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

        <div class="box">
          <div class="box-item">
            <p>Pre zobrazenie obsahu sa prosím prihláste.</p>
            <p>Ak ešte nemáte účet, môžete sa registrovať nižšie</p>

            <div class="button green">
              <router-link to="/registracia">Registrovať</router-link>
            </div>
          </div>
        </div>
      </section>

      <section>
        <div class="box">
          <div class="box-item">
            <h3>PRIHLÁSIŤ SA</h3>

            <form @submit.prevent="prihlasenieMobil">
              <div class="row">
                <div><img src="@/assets/images/icons/email.svg" /></div>
                <input
                  ref="emailMobil"
                  type="text"
                  name="email"
                  id=""
                  placeholder="Email"
                  v-model="formData.email"
                />
              </div>

              <div class="row">
                <div><img src="@/assets/images/icons/heslo.svg" /></div>
                <input
                  ref="hesloMobil"
                  type="password"
                  name="heslo"
                  id=""
                  placeholder="Heslo"
                  v-model="formData.heslo"
                />
              </div>

              <div class="underline">
                <router-link to="/zabudnute-heslo">Zabudnuté heslo</router-link>
              </div>

              <button class="button" type="submit">
                <img src="@/assets/images/icons/prihlasenie.svg" alt="" /><a
                  >Prihlásiť sa</a
                >
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

    setTimeout(() => {
      if (this.$store.state.user) {
        // Ak je používateľ prihlásený
        // window.history.go(-1);
        this.$router.push("/");
      }
    }, 1000); // Počkaj 500 ms pred kontrolou stavu prihlásenia
  },
  components: { TheHeadline },
  data() {
    return {
      formData: {
        email: "",
        heslo: "",
      },
    };
  },
  methods: {
    prihlasenie() {
      if (this.isFormValid()) {
        // Vykonajte ďalšie kroky, napríklad odoslanie formulára
        this.$store.dispatch("prihlasenie", {
          email: this.$refs.email.value,
          heslo: this.$refs.heslo.value,
        });
      }
    },
    prihlasenieMobil() {
      if (this.isFormValid()) {
        // Vykonajte ďalšie kroky, napríklad odoslanie formulára
        this.$store.dispatch("prihlasenie", {
          email: this.$refs.emailMobil.value,
          heslo: this.$refs.hesloMobil.value,
        });
      }
    },
    isFormValid() {
      if (
        this.formData.email.trim() === "" &&
        this.formData.heslo.trim() === ""
      ) {
        this.$store.state.SnackBarText =
          "Pre prihlásenie je nutné vyplniť všetky polia formuláru";
        return false;
      } else if (this.formData.email.trim() === "") {
        this.$store.state.SnackBarText = "Prosím zadajte email";
        return false;
      } else if (this.formData.heslo.trim() === "") {
        this.$store.state.SnackBarText = "Prosím zadajte heslo";
        return false;
      } else if (!this.formData.email.includes("@")) {
        this.$store.state.SnackBarText =
          "Neplatná emailová adresa, chýba znak @";
        return false;
      }

      return true;
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

p {
  margin: 0.6em auto 0.6em auto;
  text-align: center;
  font-size: 1.8em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%;
  width: 85%;
  max-width: 14em;

  &:has(strong) {
    width: 70%;
  }
}

.underline {
  font-size: 1.2em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 1.79688rem */
  letter-spacing: 0.12em;
  text-decoration-line: underline;
  text-align: center;
  margin: 1em;
  transition-duration: 0.5s;

  &:hover {
    letter-spacing: 0.3em;
    transition-duration: 0.3s;
  }
}
h4,
h3 {
  font-size: 2em;
  text-align: center;

  line-height: 115%; /* 2.51563rem */
  letter-spacing: 0.05em;
}

.scroll {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.button {
  padding: 0.5em 0.9em 0.5em;
  margin: 0 auto;
}

.box {
  margin: auto;
  width: 100%;
}

.box-item {
  padding: 3em 0;

  &:first-child {
    margin-top: auto;
  }
}

.button.green {
  margin: 1.8em auto auto auto;
}

.absolute-img {
  position: absolute;
  bottom: 0;
  left: -3%;
  width: 14vw;
  max-width: 16em;
  filter: drop-shadow(10px 5px 10px rgba(0, 0, 0, 0.35));
}

.mobile .absolute-img {
  max-width: 20em;
}

.mobile section:nth-child(1) > .box > .box-item {
  align-items: flex-end;

  .button {
    margin: 3em auto auto auto;
    font-size: 4vw;
  }
}

.mobile section p {
  font-size: 4.7vw;
  margin: 0.5em auto;
}

.mobile p:has(strong) {
  width: 65vw;
  margin-top: 1em;
}

@media only screen and (max-width: 1500px) {
  .absolute-img {
    display: none;
  }
}

@media only screen and (max-width: 1000px) {
  .box {
    gap: 0;
  }

  p {
    margin: 0.4em auto;
    padding-left: 0;
    text-align: center;
  }
}

@media screen and (max-width: 750px) {
  .absolute-img {
    left: -4%;
    width: 30vw;
    max-width: 13em;
    display: block;
  }

  .box {
    margin: 2em auto;
  }

  p {
    text-align: center;
  }
}
</style>
