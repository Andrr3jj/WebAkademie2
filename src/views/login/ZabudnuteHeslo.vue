<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <TheHeadline></TheHeadline>
      <p class="center">
        Ak ste zabudli svoje heslo, nemajte obavy. Nižšie zadajte svoju
        e-mailovú adresu a my Vám zašleme odkaz na obnovenie hesla. Po kliknutí
        na odkaz budete môcť vytvoriť nové heslo a získať opäť prístup k svojmu
        účtu.
      </p>

      <h3>OBNOVIŤ HESLO</h3>

      <form @submit.prevent="overeniePredOdoslanim()">
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

        <div class="underline">
          Prosím, uistite sa, že zadáte správnu e-mailovú adresu spojenú s Vaším
          účtom.
        </div>

        <button class="button" type="submit">
          <a> Odoslať </a>
        </button>
      </form>
    </div>
  </section>
  <div :id="$route.name" class="mobile">
    <div class="scroll">
      <section>
        <TheHeadline></TheHeadline>
        <p class="center">
          Ak ste zabudli svoje heslo, nemajte obavy. Nižšie zadajte svoju
          e-mailovú adresu a my Vám zašleme odkaz na obnovenie hesla. Po
          kliknutí na odkaz budete môcť vytvoriť nové heslo a získať opäť
          prístup k svojmu účtu.
        </p>
      </section>

      <section class="recovery">
        <h3>OBNOVIŤ HESLO</h3>

        <form @submit.prevent="overeniePredOdoslanim()">
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

          <div class="underline">
            Prosím, uistite sa, že zadáte správnu e-mailovú adresu spojenú s
            Vaším účtom.
          </div>

          <button class="button" type="submit">
            <a> Odoslať </a>
          </button>
        </form>
      </section>
    </div>
    <img
      class="absolute-img"
      src="@/assets/images/gallery/AndrejReset.png"
      alt=""
    />
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";

export default {
  mounted() {
    window.scrollTo(0, 0);
  },
  components: { TheHeadline },
  data() {
    return {
      formData: {
        email: "",
      },
    };
  },
  methods: {
    overeniePredOdoslanim() {
      if (this.isFormValid()) {
        // Vykonajte ďalšie kroky, napríklad odoslanie formulára
        this.resetovanie();
      }
    },
    resetovanie() {
      this.$store.dispatch("resetovanieHesla", this.$refs.email.value);
    },
    isFormValid() {
      // Overenie, či email nie je prázdny a obsahuje znak '@'
      if (this.formData.email.trim() === "") {
        this.$store.state.SnackBarText =
          "Pre odoslanie resetovacieho emailu je nutné vyplniť email";
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

.scroll {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.center {
  text-align: center;
  // margin-bottom: 1em;
}

form {
  max-width: 45em;
}

.row {
  margin: 1.3em auto;
  max-width: 25em;
}

p {
  margin: 0.6em 10% 0.6em auto;
  text-align: right;
  font-size: 1.5em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.15625rem */
  width: 75%;

  &:has(strong) {
    width: 60%;
  }
}

.underline {
  font-size: 1em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 1.79688rem */
  letter-spacing: 0.09em;
  text-decoration-line: underline;
  text-align: center;
  margin: 2em auto;
  transition-duration: 0.5s;
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
  margin: 0 auto;
}

.box-item {
  padding: 3em 0;
}

.button.green {
  margin: 1.8em 18% auto auto;
}

.mobile {
  .row {
    max-width: 35em;
  }
  h3 {
    font-size: 2.4em;
  }
  .center {
    margin: 1.5em 5% 1em;
    width: 90%;
  }

  section {
    width: 100%;

    form {
      width: 90%;
      max-width: unset;
      margin: 0.6em 5% 0.3em;
    }

    .button {
      padding: 0.4em 2.5em;
    }
  }

  .underline {
    font-size: 1.05em;
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

@media only screen and (max-width: 450px) {
  .recovery {
    font-size: 2.8vw;
  }
}
</style>
