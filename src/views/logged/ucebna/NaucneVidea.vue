<!-- Iný PC a M!!! -->

<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <img
        class="ovladanie"
        id="sipkaDozadu"
        src="@/assets/images/gallery/sipka.svg"
        alt="Posun do zadu"
        @click="$router.go(-1)"
      />

      <div class="popis">
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
      </div>
      <div
        @mouseenter="popisOtvor = true"
        @mouseleave="popisOtvor = false"
        :class="popisOtvor ? 'popis-active' : 'popis-deactive'"
      >
        <p>
          Na tejto podstránke nájdeš zoznam videí v danej sekcii. Pre čo
          najlepšie osvojenie každej piesne odporúčame postupovať od začiatku a
          nepreskakovať videá. V každom z výukových videí postupne ukazujeme
          nové prstoklady a techniky hry na heligónke, pričom každý krok je
          dôležitý pre tvoj pokrok. Dodržiavaním tejto postupnosti si zabezpečíš
          plynulé a efektívne učenie, ktoré ti umožní zvládnuť hru na heligónke
          na vyššej úrovni.
          <br />Preto je dôležité postupovať synchronizovane a nevynechávať
          žiadne videá, aby si dosiahol/la najlepšie výsledky.
        </p>
      </div>
      <h1>{{ obtiaznost }}</h1>

      <h5>{{ popis }}</h5>

      <div class="line horizontal w-80"></div>

      <div class="zoznam">
        <div
          v-for="(item, index, count) in this.produktyData[difficulty]"
          :key="index"
          class="kurz"
        >
          <div class="nahlad">
            <p class="Adumu cislo nadpis"># {{ count + 1 }}</p>
            <div class="nadhlad-obrazok">
              <img
                :src="
                  stiahniTitulniObrazok(this.produktyData[difficulty][index].id)
                "
                alt="Kurz"
              />
            </div>
            <div
              @click="
                $router.push({
                  path: '/ucebna/naucne-video',
                  query: {
                    cislo: this.produktyData[difficulty][index].id,
                  },
                })
              "
              class="button"
            >
              <p class="play">Prehrať</p>
            </div>
          </div>
          <div class="spec">
            <p class="Adumu nadpis">Názov piesne</p>
            <p class="nazov">{{ this.produktyData[difficulty][index].name }}</p>
            <p class="Adumu nadpis">Dĺžka videa</p>
            <p class="dlzka">
              {{ this.produktyData[difficulty][index].details.dlzkaVidea }}
            </p>
          </div>
          <div class="zvladnute">
            <div
              :key="produktyData[difficulty][index].id"
              @click="zmenDokoncene(produktyData[difficulty][index].id)"
              :class="{
                button: !produktyData[difficulty][index].jeDokoncene,
                'button green': produktyData[difficulty][index].jeDokoncene,
              }"
            >
              <p v-if="!produktyData[difficulty][index].jeDokoncene">
                Označiť ako zvládnuté
              </p>
              <!-- Vykresliť text podľa stavu jeDokoncene -->
              <p v-else>Zvládnuté</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div class="mobile">
    <section>
      <div class="scroll">
        <div
          @mouseenter="popisOtvor = true"
          @mouseleave="popisOtvor = false"
          :class="popisOtvor ? 'popis-active' : 'popis-deactive'"
        >
          <p>
            Na tejto podstránke nájdeš zoznam videí v danej sekcii. Pre čo
            najlepšie osvojenie každej piesne odporúčame postupovať od začiatku
            a nepreskakovať videá. V každom z výukových videí postupne ukazujeme
            nové prstoklady a techniky hry na heligónke, pričom každý krok je
            dôležitý pre tvoj pokrok. Dodržiavaním tejto postupnosti si
            zabezpečíš plynulé a efektívne učenie, ktoré ti umožní zvládnuť hru
            na heligónke na vyššej úrovni.
            <br />Preto je dôležité postupovať synchronizovane a nevynechávať
            žiadne videá, aby si dosiahol/la najlepšie výsledky.
          </p>
        </div>
        <h1>{{ obtiaznost }}</h1>

        <h5>{{ popis }}</h5>

        <div class="line horizontal w-80"></div>

        <div class="zoznam">
          <div
            v-for="(item, index, count) in this.produktyData[difficulty]"
            :key="index"
            class="kurz"
          >
            <div
              @click="
                $router.push({
                  path: '/ucebna/naucne-video',
                  query: {
                    cislo: this.produktyData[difficulty][index].id,
                    data: this.produktyData[difficulty],
                  },
                })
              "
              class="nahlad"
            >
              <div class="nadhlad-obrazok">
                <img
                  :src="
                    stiahniTitulniObrazok(
                      this.produktyData[difficulty][index].id
                    )
                  "
                  alt="Kurz"
                />
              </div>
            </div>
            <div class="spec">
              <p class="Adumu nadpis">Názov piesne: #{{ count + 1 }}</p>
              <p class="nazov">
                {{ this.produktyData[difficulty][index].name }}
              </p>
              <p class="Adumu nadpis">Dĺžka videa</p>
              <p class="dlzka">
                {{ this.produktyData[difficulty][index].details.dlzkaVidea }}
              </p>
            </div>
            <div class="zvladnute">
              <div
                :key="produktyData[difficulty][index].id"
                @click="zmenDokoncene(produktyData[difficulty][index].id)"
                :class="{
                  button: !produktyData[difficulty][index].jeDokoncene,
                  'button green': produktyData[difficulty][index].jeDokoncene,
                }"
              >
                <p v-if="!produktyData[difficulty][index].jeDokoncene">
                  Označiť ako zvládnuté
                </p>
                <!-- Vykresliť text podľa stavu jeDokoncene -->
                <p v-else>Zvládnuté</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);

    if (
      !("obtiaznost" in this.$route.query) ||
      (this.obtiaznost != "Začiatočník" &&
        this.obtiaznost != "Stredne pokročilý" &&
        this.obtiaznost != "Pokročilý")
    ) {
      this.$router.push("/ucebna/moje-kurzy");
    }

    if (this.obtiaznost == "Začiatočník") {
      this.popis = "Hráme na heligónke prvé piesne";
      this.difficulty = "lahke";
    } else if (this.obtiaznost == "Stredne pokročilý") {
      this.popis = "Rozvíjame zručnosti na heligónke";
      this.difficulty = "stredne";
    } else {
      this.popis = "Vzdelávacia sekcia pre pokročilých heligonkárov";
      this.difficulty = "tazke";
    }

    this.nacitajProduktyID();
  },
  data() {
    return {
      obtiaznost: this.$route.query.obtiaznost,
      difficulty: "",
      popis: "",
      popisOtvor: false,
      produktyID: [],
      produktyData: {
        lahke: {},
        stredne: {},
        tazke: {},
      },
    };
  },
  methods: {
    nacitajProduktyID() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/search.php?&pagination_index=0&pagination_limit=10000&details_key=typ&details_value=Video",
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          const jsonArray = response.data.data;

          for (let i = 0; i < jsonArray.length; i++) {
            const id = jsonArray[i].id;
            this.produktyID.push(id);
            this.nacitajProduktyData(id);
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nacitajProduktyData(id) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            if (response.data.data.difficulty == 1) {
              this.produktyData.lahke[response.data.data.id] =
                response.data.data;

              this.produktyData.lahke[response.data.data.id].details =
                JSON.parse(
                  this.produktyData.lahke[
                    response.data.data.id
                  ].details.replace(/[\n\t]/g, "")
                );
              this.zistiDokonceny(id);
            } else if (response.data.data.difficulty == 2) {
              this.produktyData.stredne[response.data.data.id] =
                response.data.data;

              this.produktyData.stredne[response.data.data.id].details =
                JSON.parse(
                  this.produktyData.stredne[
                    response.data.data.id
                  ].details.replace(/[\n\t]/g, "")
                );
              this.zistiDokonceny(id);
            } else {
              this.produktyData.tazke[response.data.data.id] =
                response.data.data;

              this.produktyData.tazke[response.data.data.id].details =
                JSON.parse(
                  this.produktyData.tazke[
                    response.data.data.id
                  ].details.replace(/[\n\t]/g, "")
                );
              this.zistiDokonceny(id);
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    zistiDokonceny(id) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/user/finish/isProductsFinished.php/?id=" +
          id,
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.data == "False") {
            this.produktyData[this.difficulty][id].jeDokoncene = false;
          } else {
            this.produktyData[this.difficulty][id].jeDokoncene = true;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    zmenDokoncene(id) {
      var cesta = "";
      if (!this.produktyData[this.difficulty][id].jeDokoncene) {
        cesta =
          this.$store.state.api + "/user/finish/finishProduct.php/?id=" + id;
      } else {
        cesta =
          this.$store.state.api + "/user/finish/unfinishProduct.php/?id=" + id;
      }
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: cesta,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            if (!this.produktyData[this.difficulty][id].jeDokoncene) {
              this.$store.state.SnackBarText = "Produkt je dokončený";
              console.log(
                "this.produktyData[this.difficulty][id].jeDokoncene :>> ",
                this.produktyData[this.difficulty][id].jeDokoncene
              );
              this.produktyData[this.difficulty][id].jeDokoncene = true;
              console.log(
                "this.produktyData[this.difficulty][id].jeDokoncene :>> ",
                this.produktyData[this.difficulty][id].jeDokoncene
              );
            } else {
              this.$store.state.SnackBarText = "Produkt už nieje dokončený";
              this.produktyData[this.difficulty][id].jeDokoncene = false;
            }

            this.zistiDokonceny();
          } else {
            this.$store.state.SnackBarText =
              "Nepodarilo sa zmeniť dokončenosť produktu";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Niečo sa pokazilo";
        });
    },
    stiahniTitulniObrazok(id) {
      return (
        this.$store.state.api +
        "/product/files/load.php/?id=" +
        this.produktyData[this.difficulty][id].id +
        "&subdir=details&file=" +
        this.produktyData[this.difficulty][id].name
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase() +
        "-T.png"
      );
    },
  },
  watch: {
    produktyData: {
      handler(newValue, oldValue) {
        // Ak sa zmení niektorá z hodnôt jeDokoncene vo vašich dátach
        // môžeme vyvolať $forceUpdate() na aktualizáciu zobrazenia
        console.log(newValue, oldValue);
        this.$forceUpdate();
      },
      deep: true, // sledovanie zmien aj v hĺbkovo vnorených dátach
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
  font-size: 6.1vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0.1em 0.15em 0.1em;
  margin: 0;
}

h5 {
  color: #000;

  font-size: 2.3vw;

  font-style: normal;
  font-weight: 700;
  line-height: 115%; /* 2.875em */
  margin: 1.1em auto 0.5em;
  width: 95%;
}

.ovladanie {
  position: absolute;
  top: 0.3em; //1em
  left: 0.3em;
  width: 4em;
  cursor: pointer;
  transition-duration: 0.2s;

  transform: scaleX(-1);

  &:hover {
    transform: scaleX(-1.2) scaleY(1.2) rotate(-20deg);
  }
}

.popis {
  position: absolute;
  top: 0.6em;
  right: 0.6em;
  transition-duration: 0.2s;

  img {
    width: 1.8em;
  }

  & .info-active {
    width: 3.2em;
    // margin-left: -0.5em;
    // margin-top: -0.6em;
  }
  &:hover {
    cursor: pointer;
    transform: scale(1.1);
  }
}

.popis-active {
  position: absolute;
  width: clamp(20em, 50%, 40em);
  background-color: #8ec84e;
  box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.25);
  border-radius: 1rem;
  top: 3.8em;
  right: 0;
  // transform: translateX(-50%);
  padding: 1.3em 0.8em;
}

.popis-deactive {
  display: none;
}

.zoznam {
  width: 75%;
  margin: 0.5em auto;
}

.kurz {
  display: flex;
  flex-direction: row;
  gap: 2%;
  margin: 1em auto;

  .nahlad {
    flex: 2;
  }

  .spec {
    flex: 5;
  }

  .zvladnute {
    flex: 3;
  }

  .nahlad,
  .spec,
  .zvladnute {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .spec {
    align-items: flex-start;
  }

  img {
    border: 0.28rem solid #fef35a;
    border-radius: 1rem;
    box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.35);
    width: 12em;
    height: 7em;
    object-fit: cover;
  }

  .button {
    padding: 0.5em 1.1em;
    width: min-content;
    margin: 0.3em auto;
    font-size: 1.15em;
    white-space: nowrap;
  }

  .nadpis,
  .nazov,
  .dlzka {
    font-size: 1.5em;
    letter-spacing: 1px;
    text-align: left;
  }

  .nazov {
    margin-bottom: 0.2em;
  }
}

@media only screen and (max-width: 1500px) {
  .zoznam {
    width: 85%;
  }
}

@media only screen and (max-width: 1250px) {
  .zoznam {
    width: 95%;
    font-size: 90%;
  }
}

@media only screen and (max-width: 950px) {
  .zoznam {
    font-size: 80%;
  }
}
@media only screen and (max-width: 850px) {
  .zoznam {
    font-size: 70%;
  }
}

@media only screen and (max-width: 750px) {
  .zoznam {
    font-size: 90%;
  }

  .kurz {
    margin: 2em auto;
  }
  .mobile {
    padding-bottom: 7em;
  }
}

@media only screen and (max-width: 650px) {
  .zoznam {
    font-size: 100%;
  }

  .kurz {
    flex-direction: column;
    align-items: center;

    .spec {
      align-items: center;
    }

    img {
      width: 18em;
      height: 10em;
      margin: 1em;
    }
  }

  .mobile section p {
    font-size: 1.1em;
  }
}

@media only screen and (max-width: 450px) {
  .kurz {
    img {
      width: 16em;
      height: 9em;
    }

    .nadpis,
    .nazov,
    .dlzka {
      font-size: 1.35em;
      // margin: 0.1em auto;
    }

    .spec {
      margin-top: 0.5em;
    }
  }
}
</style>
