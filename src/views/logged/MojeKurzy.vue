<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <h1>Online výučba</h1>

      <h5>Pre začiatočníkov, stredne pokročilých a pokročilých</h5>

      <div class="line horizontal w-80"></div>

      <div v-if="section1" id="section1" class="obsah">
        <div
          class="box-item oblubene narocnost naposledy-sledovane"
          data-tour="mojekurzy-difficulty"
        >
          <div class="nadpis">
            <p>
              Vyberte si sekciu <br />
              náročnosti
            </p>
          </div>
          <div class="content">
            <div class="slider">
              <div
                @click="
                  section1 = false;
                  section3 = true;
                  this.stiahnydata();
                "
                class="ovladanie-slider left"
              >
                <img src="@/assets/images/gallery/vlavo.png" alt="Do ľava" />
              </div>
              <div class="box-kurz">
                <img
                  src="@/assets/images/obtiaznost/obtiaznostZac.png"
                  alt="Nazov not"
                />
                <div
                  @click="
                    $router.push({
                      path: '/ucebna/naucne-videa',
                      query: { obtiaznost: 'Začiatočník' },
                    })
                  "
                  class="button"
                >
                  <p>Začať</p>
                </div>
              </div>
              <div
                @click="
                  section1 = false;
                  section2 = true;
                  this.stiahnydata();
                "
                class="ovladanie-slider right"
              >
                <img src="@/assets/images/gallery/vpravo.png" alt="Do prava" />
              </div>
            </div>
            <div class="nazov-piesne text">Začiatočník</div>
          </div>
        </div>

        <div class="popis">
          <p class="nadpis">
            Táto sekcia je určená pre úplných začiatočníkov bez predchádzajúcich
            skúseností s hrou na nástroj.
          </p>
          <p class="text">
            Tu nájdete základné informácie, krok za krokom pokyny a jednoduché
            cvičenia, ktoré Vám pomôžu začať a postupne sa zlepšovať vo Vašej
            hre na heligónke.
          </p>
          <p class="nadpis" data-tour="mojekurzy-songs-heading">
            Zoznam piesní:
          </p>

          <div class="zoznam">
            <p v-for="song in filterSongsName" :key="song">{{ song.name }}</p>
          </div>
        </div>
      </div>
      <div v-if="section2" id="section2" class="obsah">
        <div
          class="box-item oblubene narocnost naposledy-sledovane"
          data-tour="mojekurzy-difficulty"
        >
          <div class="nadpis">
            <p>
              Vyberte si sekciu <br />
              náročnosti
            </p>
          </div>
          <div class="content">
            <div class="slider">
              <div
                @click="
                  section2 = false;
                  section1 = true;
                  this.stiahnydata();
                "
                class="ovladanie-slider left"
              >
                <img src="@/assets/images/gallery/vlavo.png" alt="Do ľava" />
              </div>
              <div class="box-kurz">
                <img
                  src="@/assets/images/obtiaznost/obtiaznostStredPokro.png"
                  alt="Nazov not"
                />
                <div
                  @click="
                    $router.push({
                      path: '/ucebna/naucne-videa',
                      query: { obtiaznost: 'Stredne pokročilý' },
                    })
                  "
                  class="button"
                >
                  <p>Začať</p>
                </div>
              </div>
              <div
                @click="
                  section2 = false;
                  section3 = true;
                  this.stiahnydata();
                "
                class="ovladanie-slider right"
              >
                <img src="@/assets/images/gallery/vpravo.png" alt="Do prava" />
              </div>
            </div>
            <div class="nazov-piesne text">Stredne pokročilý</div>
          </div>
        </div>

        <div class="popis">
          <p class="nadpis">
            Táto sekcia je určená pre tých, ktorí sa chcú posunúť ďalej a
            objaviť nové výzvy v hre na heligónku.
          </p>
          <p class="text">
            Nájdete tu náročnejšie skladby a techniky, ktoré Vám pomôžu
            zdokonaliť Váš hudobný talent a posunúť sa na ďalšiu úroveň.
          </p>
          <p class="nadpis" data-tour="mojekurzy-songs-heading">
            Zoznam piesní:
          </p>

          <div class="zoznam">
            <p v-for="song in filterSongsName" :key="song">{{ song.name }}</p>
          </div>
        </div>
      </div>
      <div v-if="section3" id="section3" class="obsah">
        <div
          class="box-item oblubene narocnost naposledy-sledovane"
          data-tour="mojekurzy-difficulty"
        >
          <div class="nadpis">
            <p>
              Vyberte si sekciu <br />
              náročnosti
            </p>
          </div>
          <div class="content">
            <div class="slider">
              <div
                @click="
                  section3 = false;

                  this.stiahnydata();
                  section2 = true;
                "
                class="ovladanie-slider left"
              >
                <img src="@/assets/images/gallery/vlavo.png" alt="Do ľava" />
              </div>
              <div class="box-kurz">
                <img
                  src="@/assets/images/obtiaznost/obtiaznostPokro.png"
                  alt="Nazov not"
                />
                <div
                  @click="
                    $router.push({
                      path: '/ucebna/naucne-videa',
                      query: { obtiaznost: 'Pokročilý' },
                    })
                  "
                  class="button"
                >
                  <p>Začať</p>
                </div>
              </div>
              <div
                @click="
                  section3 = false;

                  this.stiahnydata();
                  section1 = true;
                "
                class="ovladanie-slider right"
              >
                <img src="@/assets/images/gallery/vpravo.png" alt="Do prava" />
              </div>
            </div>
            <div class="nazov-piesne text">Pokročilý</div>
          </div>
        </div>

        <div class="popis">
          <p class="nadpis">
            Táto sekcia je určená pre skúsených hráčov, ktorí majú za sebou už
            niekoľko skladieb a cítia sa pohodlne s nástrojom.
          </p>
          <p class="text">
            Tu nájdete náročnejšie skladby, techniky a tipy, ktoré Vám pomôžu
            posunúť Vašu hru na vyššiu úroveň.
          </p>
          <p class="nadpis" data-tour="mojekurzy-songs-heading">
            Zoznam piesní:
          </p>

          <div class="zoznam">
            <p v-for="song in filterSongsName" :key="song">{{ song.name }}</p>
          </div>
        </div>
      </div>
    </div>
    <div class="fixed">
      <img
        src="@/assets/images/gallery/JurajOnlineVyucba.png"
        alt="Juraj hrajúci na heligónke"
      />
    </div>
  </section>
  <div class="mobile">
    <section>
      <div class="scroll">
        <h1>Online výučba</h1>

        <h5>Pre začiatočníkov, stredne pokročilých a pokročilých</h5>

        <div class="line horizontal w-80"></div>

        <div v-if="section1" id="section1" class="obsah">
          <div
            class="box-item oblubene narocnost naposledy-sledovane"
            data-tour="mojekurzy-difficulty"
          >
            <div class="nadpis">
              <p>
                Vyberte si sekciu <br />
                náročnosti
              </p>
            </div>
            <div class="content">
              <div class="slider">
                <div
                  @click="
                    section1 = false;
                    section3 = true;
                    this.stiahnydata();
                  "
                  class="ovladanie-slider left"
                >
                  <img src="@/assets/images/gallery/vlavo.png" alt="Do ľava" />
                </div>
                <div class="box-kurz">
                  <img
                    src="@/assets/images/obtiaznost/obtiaznostZac.png"
                    alt="Nazov not"
                  />
                  <div
                    @click="
                      $router.push({
                        path: '/ucebna/naucne-videa',
                        query: { obtiaznost: 'Začiatočník' },
                      })
                    "
                    class="button"
                  >
                    <p>Začať</p>
                  </div>
                </div>
                <div
                  @click="
                    section1 = false;
                    section2 = true;
                    this.stiahnydata();
                  "
                  class="ovladanie-slider right"
                >
                  <img
                    src="@/assets/images/gallery/vpravo.png"
                    alt="Do prava"
                  />
                </div>
              </div>
              <div class="nazov-piesne text">Začiatočník</div>
            </div>
          </div>

          <div class="popis">
            <p class="nadpis">
              Táto sekcia je určená pre úplných začiatočníkov bez
              predchádzajúcich skúseností s hrou na nástroj.
            </p>
            <p class="text">
              Tu nájdete základné informácie, krok za krokom pokyny a jednoduché
              cvičenia, ktoré Vám pomôžu začať a postupne sa zlepšovať vo Vašej
              hre na heligónke.
            </p>
            <p class="nadpis" data-tour="mojekurzy-songs-heading">
              Zoznam piesní:
            </p>

            <div class="zoznam">
              <p v-for="song in filterSongsName" :key="song">{{ song.name }}</p>
            </div>
          </div>
        </div>
        <div v-if="section2" id="section2" class="obsah">
          <div
            class="box-item oblubene narocnost naposledy-sledovane"
            data-tour="mojekurzy-difficulty"
          >
            <div class="nadpis">
              <p>
                Vyberte si sekciu <br />
                náročnosti
              </p>
            </div>
            <div class="content">
              <div class="slider">
                <div
                  @click="
                    section2 = false;
                    section1 = true;
                    this.stiahnydata();
                  "
                  class="ovladanie-slider left"
                >
                  <img src="@/assets/images/gallery/vlavo.png" alt="Do ľava" />
                </div>
                <div class="box-kurz">
                  <img
                    src="@/assets/images/obtiaznost/obtiaznostStredPokro.png"
                    alt="Nazov not"
                  />
                  <div
                    @click="
                      $router.push({
                        path: '/ucebna/naucne-videa',
                        query: { obtiaznost: 'Stredne pokročilý' },
                      })
                    "
                    class="button"
                  >
                    <p>Začať</p>
                  </div>
                </div>
                <div
                  @click="
                    section2 = false;
                    section3 = true;
                    this.stiahnydata();
                  "
                  class="ovladanie-slider right"
                >
                  <img
                    src="@/assets/images/gallery/vpravo.png"
                    alt="Do prava"
                  />
                </div>
              </div>
              <div class="nazov-piesne text">Stredne pokročilý</div>
            </div>
          </div>

          <div class="popis">
            <p class="nadpis">
              Táto sekcia je určená pre tých, ktorí sa chcú posunúť ďalej a
              objaviť nové výzvy v hre na heligónke.
            </p>
            <p class="text">
              Nájdete tu náročnejšie skladby a techniky, ktoré Vám pomôžu
              zdokonaliť Váš hudobný talent a posunúť sa na ďalšiu úroveň.
            </p>
            <p class="nadpis" data-tour="mojekurzy-songs-heading">
              Zoznam piesní:
            </p>

            <div class="zoznam">
              <p v-for="song in filterSongsName" :key="song">{{ song.name }}</p>
            </div>
          </div>
        </div>
        <div v-if="section3" id="section3" class="obsah">
          <div
            class="box-item oblubene narocnost naposledy-sledovane"
            data-tour="mojekurzy-difficulty"
          >
            <div class="nadpis">
              <p>
                Vyberte si sekciu <br />
                náročnosti
              </p>
            </div>
            <div class="content">
              <div class="slider">
                <div
                  @click="
                    section3 = false;
                    section2 = true;
                    this.stiahnydata();
                  "
                  class="ovladanie-slider left"
                >
                  <img src="@/assets/images/gallery/vlavo.png" alt="Do ľava" />
                </div>
                <div class="box-kurz">
                  <img
                    src="@/assets/images/obtiaznost/obtiaznostPokro.png"
                    alt="Nazov not"
                  />
                  <div
                    @click="
                      $router.push({
                        path: '/ucebna/naucne-videa',
                        query: { obtiaznost: 'Pokročilý' },
                      })
                    "
                    class="button"
                  >
                    <p>Začať</p>
                  </div>
                </div>
                <div
                  @click="
                    section3 = false;
                    section1 = true;
                    this.stiahnydata();
                  "
                  class="ovladanie-slider right"
                >
                  <img
                    src="@/assets/images/gallery/vpravo.png"
                    alt="Do prava"
                  />
                </div>
              </div>
              <div class="nazov-piesne text">Pokročilý</div>
            </div>
          </div>

          <div class="popis">
            <p class="nadpis">
              Táto sekcia je určená pre skúsených hráčov, ktorí majú za sebou už
              niekoľko skladieb a cítia sa pohodlne s nástrojom.
            </p>
            <p class="text">
              Tu nájdete náročnejšie skladby, techniky a tipy, ktoré Vám pomôžu
              posunúť Vašu hru na vyššiu úroveň.
            </p>
            <p class="nadpis" data-tour="mojekurzy-songs-heading">
              Zoznam piesní:
            </p>

            <div class="zoznam">
              <p v-for="song in filterSongsName" :key="song">{{ song.name }}</p>
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
    this.stiahnydata();
  },
  data() {
    return {
      section1: true,
      section2: false,
      section3: false,
      filterSongsName: [],
      videosData: [],
    };
  },
  methods: {
    filtrujPesnicky() {
      if (this.section1 == true) {
        this.filterSongs("1");
      } else if (this.section2 == true) {
        this.filterSongs("2");
      } else if (this.section3 == true) {
        this.filterSongs("3");
      } else {
        this.filterSongs("1");
      }
    },
    filterSongs(difficulty) {
      // Vyčistenie filtrovaného poľa pred každým filtrovaním
      this.filterSongsName = [];

      // Filtrácia piesní podľa vybranej ťažkosti
      this.videosData.forEach((song) => {
        if (song.difficulty === difficulty) {
          // Pridanie názvu piesne do filtrovaného poľa
          this.filterSongsName.push({ name: song.name });
          console.log("object :>> ", this.filterSongsName);
        }
      });
    },
    stiahnydata() {
      this.videosData = [];

      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/stats/listVideos.php",
        // headers: {
        //   ...data.getHeaders(),
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          if (response.data.status == "Request succesfull") {
            this.videosData = response.data.data;
            this.filtrujPesnicky();
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
  padding: 0.2em 0.1 0.15em 0.1em;
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

.fixed {
  position: sticky;
  bottom: 5%;
  transform: translateX(-4vw);
  width: min-content;

  img {
    display: flex;
    width: clamp(15em, 50vw, 25em);
  }
}

.obsah {
  display: flex;
  justify-content: row;
  margin: 1em 2.5em;
  gap: 2%;
}

.narocnost {
  flex: 1;
}

.popis {
  flex: 2;
}

p {
  text-align: left;
}

.zoznam p {
  font-size: 1.3em;
  font-weight: 500;
  margin-bottom: 0.2em;
}
.nadpis {
  font-size: 1.5em;
  font-weight: 600;
  margin: 1.3em 0 0.6em;
}

.text {
  font-size: 1.3em;
  font-weight: 400;
  margin: 0.8em 0;
}

.zoznam {
  display: grid;
  grid-template-columns: 1fr 1fr;
  column-gap: 5%;
}

.naposledy-sledovane {
  .nadpis p {
    font-size: 1em;
    text-align: center;
  }

  .content {
    align-items: center;
  }
  .box-kurz {
    background: linear-gradient(180deg, #90ca50 50%, #fef35a 100%);
    box-shadow: 0px 0px 4px rgba($color: #000000, $alpha: 0.25);
    border-radius: 1em;
    border: 0.25em solid #fef35a;

    & > img {
      width: 7em;
      margin-bottom: -0.6em;
    }

    .button {
      font-size: 1.1em;
      border-radius: 0.8rem;
      padding: 0.4em 1.6em;
      transform: scale(1.3);

      img {
        width: 0.7em;
      }

      p {
        font-size: 1em;
      }
    }
  }
}

.slider {
  display: flex;
}

.ovladanie-slider {
  justify-content: center !important;
  filter: drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.35));
  cursor: pointer;
  transition-duration: 0.4s;

  img {
    width: 3.85em;
  }

  &:hover {
    transform: scale(1.2);
    transition-duration: 0.2s;
  }
}

@media only screen and (min-width: 1500px) {
  .obsah {
    width: 90%;
  }
}
@media only screen and (min-width: 1600px) {
  .obsah {
    width: 80%;
  }
}

@media only screen and (max-width: 1000px) {
  .obsah {
    flex-direction: column;
    padding: 0 5%;
  }

  .fixed {
    display: none;
  }
}

@media only screen and (max-width: 750px) {
  .mobile {
    padding-bottom: 7em;
  }
}

.mobile section h1 {
  font-size: 12vw !important;
}

.mobile section h5 {
  margin: 0.5em auto 0.7em;
}

.mobile {
  .box-kurz .button {
    margin: 0;
  }

  .nazov-piesne {
    font-size: 2em !important;
    margin: 0.5em 0 0;
  }

  .narocnost .nadpis {
    margin-top: 0;
  }

  p {
    text-align: center;
  }

  .zoznam {
    grid-template-columns: 1fr;
  }
}

@media only screen and (max-width: 430px) {
  .mobile {
    .text,
    .zoznam p {
      font-size: 1.2em;
    }

    .nadpis {
      font-size: 1.4em;
    }
  }
}
</style>
