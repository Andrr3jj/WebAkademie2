<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <h1>Aktuálne predplatné</h1>

      <div class="line horizontal w-80"></div>

      <p class="nadpisy">Typ predplatného:</p>

      <div class="item">
        <p class="nazov Adumu">
          {{
            predplatne.subscription_type == "mesiac"
              ? "Mesačné"
              : predplatne.subscription_type == "rok"
              ? "Ročné"
              : "Polročné"
          }}
        </p>
      </div>

      <p class="nadpisy">Platnosť predplatného od / do:</p>

      <div class="podnadpisy">
        {{ predplatne.subscription_valid_from }} -
        {{ predplatne.subscription_valid_to }}
      </div>

      <p class="nadpisy">Počet dní do konca predplatného</p>

      <div class="podnadpisy">
        {{ this.predplatne.subscription_duration
        }}{{
          this.predplatne.subscription_duration == 1
            ? " deň"
            : this.predplatne.subscription_duration == 2 ||
              this.predplatne.subscription_duration == 3 ||
              this.predplatne.subscription_duration == 4
            ? " dni"
            : " dní"
        }}
      </div>

      <div v-if="predplatne.subscription_is_recurent == 1" class="zrusenie">
        <p>
          Máte možnosť zrušiť Vaše predplatné kedykoľvek.

          <img
            v-if="!popisOtvor"
            @mouseenter="popisOtvor = true"
            src="@/assets/images/icons/info.svg"
            alt="Doplňujúce info"
          />
          <img
            v-if="popisOtvor"
            @mouseenter="popisOtvor = true"
            @mouseleave="popisOtvor = false"
            src="@/assets/images/icons/info-active.svg"
            alt="Doplňujúce info"
            class="info-active"
          />
        </p>

        <div
          @mouseenter="popisOtvor = true"
          @mouseleave="popisOtvor = false"
          :class="popisOtvor ? 'popis-active' : 'popis-deactive'"
        >
          <p>
            Predplatné je možné zrušiť kedykoľvek, avšak finančné prostriedky
            Vám nebudú vrátené. Členstvo sa automaticky zruší po uplynutí doby
            predplatného.
          </p>
        </div>
      </div>

      <div
        @click="naozajZruseniePredplatne = true"
        class="button"
        v-if="
          predplatne.subscription_is_recurent == 1 && !naozajZruseniePredplatne
        "
      >
        <p>Zrušenie predplatného</p>
      </div>

      <div
        @click="this.zruseniePredplatne()"
        class="button"
        v-if="
          predplatne.subscription_is_recurent == 1 && naozajZruseniePredplatne
        "
      >
        <p>Chcem zrušiť predplatné</p>
      </div>
    </div>
  </section>
  <div :id="$route.name" class="mobile">
    <section>
      <div class="scroll">
        <h1>Aktuálne predplatné</h1>

        <div class="line horizontal w-80"></div>

        <p class="nadpisy">Typ predplatného:</p>

        <div class="item">
          <p class="nazov Adumu">
            {{
              predplatne.subscription_type == "mesiac"
                ? "Mesačné"
                : predplatne.subscription_type == "rok"
                ? "Ročné"
                : "Polročné"
            }}
          </p>
        </div>

        <p class="nadpisy">Platnosť predplatného od / do:</p>

        <div class="podnadpisy">
          {{ predplatne.subscription_valid_from }} -
          {{ predplatne.subscription_valid_to }}
        </div>

        <p class="nadpisy">Počet dní do konca predplatného</p>

        <div class="podnadpisy">
          {{ this.predplatne.subscription_duration
          }}{{
            this.predplatne.subscription_duration == 1
              ? " deň"
              : this.predplatne.subscription_duration == 2 ||
                this.predplatne.subscription_duration == 3 ||
                this.predplatne.subscription_duration == 4
              ? " dni"
              : " dní"
          }}
        </div>

        <div v-if="predplatne.subscription_is_recurent == 1" class="zrusenie">
          <p>
            Máte možnosť zrušiť Vaše <br />predplatné kedykoľvek.

            <img
              v-if="!popisOtvor"
              @mouseenter="popisOtvor = true"
              src="@/assets/images/icons/info.svg"
              alt="Doplňujúce info"
            />
            <img
              v-if="popisOtvor"
              @mouseenter="popisOtvor = true"
              @mouseleave="popisOtvor = false"
              src="@/assets/images/icons/info-active.svg"
              alt="Doplňujúce info"
              class="info-active"
            />
          </p>

          <div
            @mouseenter="popisOtvor = true"
            @mouseleave="popisOtvor = false"
            :class="popisOtvor ? 'popis-active' : 'popis-deactive'"
          >
            <p>
              Predplatné je možné zrušiť kedykoľvek, avšak finančné prostriedky
              Vám nebudú vrátené. Členstvo sa automaticky zruší po uplynutí doby
              predplatného.
            </p>
          </div>
        </div>

        <div
          @click="naozajZruseniePredplatne = true"
          class="button"
          v-if="
            predplatne.subscription_is_recurent == 1 &&
            !naozajZruseniePredplatne
          "
        >
          <p>Zrušenie predplatného</p>
        </div>

        <div
          @click="this.zruseniePredplatne()"
          class="button"
          v-if="
            predplatne.subscription_is_recurent == 1 && naozajZruseniePredplatne
          "
        >
          <p>Chcem zrušiť predplatné</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);

    this.overZakupenePredplatne();

    this.nacitajInformaciePredplatne();
  },
  data() {
    return {
      zakupenePredplatne: false,
      popisOtvor: false,
      predplatne: {
        subscription_type: null,
        subscription_valid_from: false,
        subscription_valid_to: null,
        subscription_is_recurent: 1,
        subscription_cancel_id: null,
      },
      predplatneData: {},
      naozajZruseniePredplatne: false,
    };
  },
  methods: {
    overZakupenePredplatne() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/user/isSubscribed.php/",
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          //pred spustením odkomentovať!
          //   if (response.data.data != "Is subscribed") {
          //     this.$router.push("/ucebna/clenstvo");
          //   }
          console.log(response);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nacitajInformaciePredplatne() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "subscription/getSubscriptionData.php/",
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          console.log("Dáta z clenstva", response.data);
          if (response.data.status == "Request succesfull") {
            this.predplatne = response.data.data;
            this.formatovanieDatumov();
          } else {
            this.$store.state.SnackBarText =
              "Nepodarilo sa načítať informácie o predplatnom";
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    formatovanieDatumov() {
      // Unix timestamp
      let subscriptionValidFromTimestamp =
        this.predplatne.subscription_valid_from;
      let subscriptionValidToTimestamp = this.predplatne.subscription_valid_to;

      // Prevod na dátum
      let subscriptionValidFromDate = new Date(
        subscriptionValidFromTimestamp * 1000
      );
      let subscriptionValidToDate = new Date(
        subscriptionValidToTimestamp * 1000
      );

      // Dnešný dátum
      let today = new Date();

      // Rozdiel v milisekundách medzi dátumom do a dnešným dátumom
      let timeDifference = subscriptionValidToDate - today;

      // Prevod milisekúnd na dni
      let daysDifference = Math.floor(timeDifference / (1000 * 60 * 60 * 24));

      // Výpis
      this.predplatne.subscription_valid_from =
        subscriptionValidFromDate.toLocaleDateString();
      this.predplatne.subscription_valid_to =
        subscriptionValidToDate.toLocaleDateString();
      this.predplatne.subscription_duration = daysDifference + 1;

      console.log(daysDifference);
      console.log(this.predplatne.subscription_duration);
    },

    zruseniePredplatne() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/subscription/cancelSubscription.php/",
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          console.log(
            "Zrušenie predplatného: " + JSON.stringify(response.data)
          );
          if (response.data.status == "Request succesfull") {
            this.$store.state.SnackBarText =
              "Vaše predplatné bolo úspešne zrušené";
            setTimeout(() => {
              this.nacitajInformaciePredplatne();
            }, 500);
          } else {
            this.$store.state.SnackBarText =
              "Vaše predplatné sa nepodarilo zrušiť";
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

h1,
h5,
p {
  text-align: center;
}

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 6.1vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0;
}

.line {
  margin-top: 1em;
}

.item {
  padding: 0.6em 3.5em;

  border-radius: 1.2rem;
  border: 0.4em solid #fef35a;
  background: var(
    --Linear,
    linear-gradient(140deg, #90c94f 35.64%, #fef35a 99.4%)
  );
  box-shadow: 5px 5px 10px 0 rgba(0, 0, 0, 0.35);
  position: relative;
  width: fit-content;
  margin: auto;
  p {
    font-size: 2.5em;
    margin-top: 5px;
  }
}

.button {
  width: fit-content;
  justify-content: center;
  margin: 1.5em auto 0.5em;
  padding: 0.4em 1.6em;
  font-size: 1.7em;
  p {
    font-size: 0.8em;
  }

  img {
    width: 0.8em;
  }
}

.nadpisy {
  font-size: 1.8em;
  font-weight: 600;
  margin: 1.3em auto 0.5em;
}

.podnadpisy {
  font-size: 1.5em;
  font-weight: 400;
  margin: 1em auto;
}

.zrusenie {
  font-size: 1.4em;
  font-weight: 400;

  & > p {
    height: 1em;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    justify-content: center;
    gap: 0;
  }
}

//popis

.zrusenie {
  position: relative;
}

.zrusenie img {
  position: relative;
  top: 0;
  left: 2%;
  transition-duration: 0.2s;
  width: 1em;

  &.info-active {
    width: 1.8em;
    top: -28%;
  }
  &:hover {
    cursor: pointer;
    transform: scale(1.1);
  }
}

.popis-active {
  position: absolute;
  width: 80%;
  background-color: #8ec84f;
  box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.25);
  border-radius: 0.8rem;
  top: 135%;
  left: 50%;
  transform: translateX(-50%);
  padding: 0.6em;
  p {
    font-size: 1em !important;
  }
}

.popis-deactive {
  display: none;
}

//koniec popisu

@media only screen and (max-width: 1400px) {
  .items {
    font-size: 1vw;
  }

  .item .line.line.horizontal {
    height: 0.2rem;
  }

  h5 {
    padding-left: 5%;
    padding-right: 5%;
  }
}
@media only screen and (max-width: 1000px) {
  h1 {
    font-size: 9vw;
  }

  h5 {
    margin: 0em auto 0.2em;
    font-size: 3.5vw;
  }

  .info {
    font-size: 1.5em;
  }
}

.mobile {
  .items {
    font-size: 1em;
    flex-wrap: wrap;
    margin: 2em auto 2em;
  }

  .info {
    margin: 1.5em auto 1em;
    width: 90%;
  }

  .item {
    margin: 1em auto;

    .button {
      padding: 0.4em 0.9em;
      margin: 0;
    }
  }

  .scroll {
    overflow: hidden;
    margin-bottom: 2em;
  }

  .zrusenie > p {
    font-size: 1em !important;

    height: 2em;

    margin-top: 3em;
  }

  .popis-active {
    top: 52%;

    p {
      font-size: 0.8em !important;
    }
  }

  .scroll > .button {
    font-size: 1em;
    padding: 0.8em 2.5em;
    margin: 3em auto 0;

    p {
      font-size: 1.3em;
    }
  }
}

@media only screen and (max-width: 750px) {
  .mobile {
    padding-bottom: 7em;
  }
}

@media only screen and (max-width: 450px) {
  .item {
    padding: 0.3em 3em;
  }

  .item p {
    font-size: 2.2em;
  }

  .info {
    font-size: 1em;
  }

  .nadpisy {
    font-size: 1.5em;
  }

  .podnadpisy {
    font-size: 1.4em;
  }
}
</style>
