<template>
  <div
    v-if="
      ($store.state.user != null &&
        $store.state.user.ownedNotes &&
        $store.state.user.ownedNotes.some(
          (note) => note.id === produktyID.toString()
        )) ||
      $store.state.userRoles?.roles?.includes('product_pass')
    "
    class="box-zapis-flex"
    :class="{ activeRowLayout: !layouts }"
    :style="flexCenter ? { 'justify-content': 'flex-start' } : {}"
  >
    <div class="zapis zakupene" v-if="layouts">
      <div class="box-zapis">
        <div class="obtiaznost">
          <obtiaznost-zapisov :produktObtiaznost="produktyData.difficulty" />
        </div>
        <div class="duch" :style="{ height: sourceElementHeight + 'px' }"></div>
        <div class="img-box-zapis flip-card-front">
          <div class="img-zapis">
            <img
              src="@/assets/images/gallery/zapisDefault.png"
              alt="Prednastavené nezakúpené noty"
              class="zapis-noty"
            />
          </div>
          <div ref="sourceElement" class="cena">
            <p @resize="skontrolujVysku()">{{ produktyData.name }}</p>
          </div>
        </div>
        <div class="img-box-zapis flip-card-back">
          <div class="text">
            <p class="bigger">Určené pre</p>
            <p>{{ produktDetaily.urcenePre }}</p>
            <p class="bigger">Stupnica</p>
            <p v-for="element in produktDetaily.stupnice" :key="element">
              {{
                element == "D" || element == "A"
                  ? element + " - Mol"
                  : element + " - Dur"
              }}
            </p>
            <p class="bigger">Obtiažnosť</p>
            <p>
              {{
                produktyData.difficulty == 1
                  ? "Veľmi ľahká"
                  : produktyData.difficulty == 2
                  ? "Ľahká"
                  : produktyData.difficulty == 3
                  ? "Stredne ťažká"
                  : produktyData.difficulty == 4
                  ? "Ťažká"
                  : produktyData.difficulty == 5
                  ? "Veľmi ťažká"
                  : produktyData.difficulty == 6
                  ? "Expert"
                  : "Neurčená"
              }}
            </p>
          </div>
        </div>
      </div>
      <div
        @click="
          $router.push({
            path: '/ucebna/ciselny-zapis',
            query: { cislo: produktyID },
          })
        "
        class="button"
      >
        <div class="samostatny-button">
          <img src="@/assets/images/icons/hrat.svg" alt="" />
          <p>Hrať</p>
        </div>
      </div>
    </div>
    <div class="zapis zakupene rowLayout" v-if="!layouts">
      <div class="name">
        <p class="table">
          {{ produktyData.name != "" ? produktyData.name : "Žiadny názov" }}
        </p>
      </div>
      <div class="info">
        <p class="table">
          {{
            produktDetaily.urcenePre && produktDetaily.urcenePre !== ""
              ? produktDetaily.urcenePre.substring(0, 7)
              : "Neurč"
          }}
        </p>
      </div>
      <div class="info">
        <div
          v-if="produktDetaily.stupnice && produktDetaily.stupnice.length > 0"
        >
          <p
            class="table"
            v-for="element in produktDetaily.stupnice"
            :key="element"
          >
            {{
              element == "D" || element == "A"
                ? element + " - Mol"
                : element + " - Dur"
            }}
          </p>
        </div>
        <p v-else class="table">Žiadna</p>
      </div>
      <div class="info">
        <p class="table">
          {{
            produktyData.difficulty == 1
              ? "Najľahšia"
              : produktyData.difficulty == 2
              ? "Ľahká"
              : produktyData.difficulty == 3
              ? "Stredná"
              : produktyData.difficulty == 4
              ? "Ťažká"
              : produktyData.difficulty == 5
              ? "Najťažšia"
              : produktyData.difficulty == 6
              ? "Expert"
              : "Neurčená"
          }}
        </p>
      </div>
      <div class="price">
        <p class="table info-mobile">
          {{
            produktDetaily.urcenePre && produktDetaily.urcenePre !== ""
              ? produktDetaily.urcenePre.substring(0, 7)
              : "Neurč"
          }}
        </p>
        <div
          v-if="produktDetaily.stupnice && produktDetaily.stupnice.length > 0"
        >
          <p
            class="info-mobile"
            v-for="element in produktDetaily.stupnice"
            :key="element"
          >
            {{
              element == "D" || element == "A"
                ? element + " - Mol"
                : element + " - Dur"
            }}
          </p>
        </div>
        <p v-else class="info-mobile">Žiadna</p>
        <p class="table info-mobile">
          {{
            produktyData.difficulty == 1
              ? "Najľahšia"
              : produktyData.difficulty == 2
              ? "Ľahká"
              : produktyData.difficulty == 3
              ? "Stredná"
              : produktyData.difficulty == 4
              ? "Ťažká"
              : produktyData.difficulty == 5
              ? "Najťažšia"
              : produktyData.difficulty == 6
              ? "Expert"
              : "Neurčená"
          }}
        </p>

        <p class="table">&nbsp;</p>
      </div>
      <div class="akcia">
        <div
          @click="
            $router.push({
              path: '/ucebna/ciselny-zapis',
              query: { cislo: produktyID },
            })
          "
          class="button"
        >
          <div class="samostatny-button">
            <img src="@/assets/images/icons/hrat.svg" alt="" />
            <p>Hrať</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div
    v-else
    class="box-zapis-flex"
    :class="{ activeRowLayout: !layouts }"
    :style="flexCenter ? { 'justify-content': 'flex-start' } : {}"
  >
    <div class="zapis" v-if="layouts">
      <div class="nadpis">
        <p>{{ produktyData.name }}</p>
      </div>
      <div class="box-zapis">
        <div class="obtiaznost">
          <obtiaznost-zapisov :produktObtiaznost="produktyData.difficulty" />
        </div>
        <div class="img-box-zapis flip-card-front">
          <div class="img-zapis">
            <img
              src="@/assets/images/gallery/zapisDefault.png"
              alt="Prednastavené nezakúpené noty"
              class="zapis-noty"
            />
            <img
              v-if="novinka != ''"
              src="@/assets/images/gallery/novinka.png"
              alt="Novinka"
              class="novinka"
            />
          </div>
          <div class="cena">
            <h4 v-if="!creditBuy">{{ produktyData.price }}€</h4>
            <h4 v-else>{{ priceInCredit() }}</h4>
          </div>
        </div>
        <div class="img-box-zapis flip-card-back">
          <div class="text">
            <p class="bigger">Určené pre</p>
            <p>{{ produktDetaily.urcenePre }}</p>
            <p class="bigger">Stupnica</p>
            <p v-for="element in produktDetaily.stupnice" :key="element">
              {{
                element == "D" || element == "A"
                  ? element + " - Mol"
                  : element + " - Dur"
              }}
            </p>
            <p class="bigger">Obtiažnosť</p>
            <p>
              {{
                produktyData.difficulty == 1
                  ? "Veľmi ľahká"
                  : produktyData.difficulty == 2
                  ? "Ľahká"
                  : produktyData.difficulty == 3
                  ? "Stredne ťažká"
                  : produktyData.difficulty == 4
                  ? "Ťažká"
                  : produktyData.difficulty == 5
                  ? "Veľmi ťažká"
                  : produktyData.difficulty == 6
                  ? "Expert"
                  : "Neurčená"
              }}
            </p>
          </div>
        </div>
      </div>
      <div class="button">
        <div
          v-if="!creditBuy"
          @click="
            $store.dispatch('pridajDoKosika', {
              id: produktyData.id,
              meta_data: '',
            })
          "
          class="rozdeleny-button"
        >
          <img src="@/assets/images/icons/taska.svg" alt="" />
          <p>Do košíka</p>
        </div>
        <div
          v-else
          @click="buyWithCredit(produktyData.id)"
          class="rozdeleny-button"
        >
          <p>Zakúpiť</p>
        </div>
        <a
          class="hrat-ukazku rozdeleny-button"
          @click="vratZvukovuUkazku()"
          @mouseleave="stopAudio()"
        >
          <img src="@/assets/images/icons/zvuk.svg" alt="Hrať zvukovú ukážku" />
        </a>
      </div>
      <p class="time-add">Pridané: {{ produktyData.timestamp }}</p>
      <audio
        v-if="this.zvukovaUkazka != ''"
        controls
        ref="audioPlayer"
        style="display: none"
      >
        <source :src="this.zvukovaUkazka" type="audio/mpeg" />
        Váš prehliadač nepodporuje prehrávanie zvuku
      </audio>
    </div>

    <div class="zapis zakupene rowLayout" v-if="!layouts">
      <div class="name">
        <p class="table">
          {{ produktyData.name != "" ? produktyData.name : "Žiadny názov" }}
        </p>
      </div>
      <div class="info">
        <p class="table">
          {{
            produktDetaily.urcenePre && produktDetaily.urcenePre !== ""
              ? produktDetaily.urcenePre.substring(0, 7)
              : "Neurč"
          }}
        </p>
      </div>
      <div class="info">
        <div
          v-if="produktDetaily.stupnice && produktDetaily.stupnice.length > 0"
        >
          <p
            class="table"
            v-for="element in produktDetaily.stupnice"
            :key="element"
          >
            {{
              element == "D" || element == "A"
                ? element + " - Mol"
                : element + " - Dur"
            }}
          </p>
        </div>
        <p v-else class="table">Žiadna</p>
      </div>
      <div class="info">
        <p class="table">
          {{
            produktyData.difficulty == 1
              ? "Najľahšia"
              : produktyData.difficulty == 2
              ? "Ľahká"
              : produktyData.difficulty == 3
              ? "Stredná"
              : produktyData.difficulty == 4
              ? "Ťažká"
              : produktyData.difficulty == 5
              ? "Najťažšia"
              : produktyData.difficulty == 6
              ? "Expert"
              : "Neurčená"
          }}
        </p>
      </div>
      <div class="price">
        <p class="table info-mobile">
          {{
            produktDetaily.urcenePre && produktDetaily.urcenePre !== ""
              ? produktDetaily.urcenePre.substring(0, 7)
              : "Neurč"
          }}
        </p>
        <div
          v-if="produktDetaily.stupnice && produktDetaily.stupnice.length > 0"
        >
          <p
            class="info-mobile"
            v-for="element in produktDetaily.stupnice"
            :key="element"
          >
            {{
              element == "D" || element == "A"
                ? element + " - Mol"
                : element + " - Dur"
            }}
          </p>
        </div>
        <p v-else class="info-mobile">Žiadna</p>
        <p class="table info-mobile">
          {{
            produktyData.difficulty == 1
              ? "Najľahšia"
              : produktyData.difficulty == 2
              ? "Ľahká"
              : produktyData.difficulty == 3
              ? "Stredná"
              : produktyData.difficulty == 4
              ? "Ťažká"
              : produktyData.difficulty == 5
              ? "Najťažšia"
              : produktyData.difficulty == 6
              ? "Expert"
              : "Neurčená"
          }}
        </p>

        <p v-if="!creditBuy" class="table">
          {{ produktyData.price != "" ? produktyData.price : "0,00" }}€
        </p>
        <p v-else class="table">
          {{ priceInCredit() }}
        </p>
      </div>
      <div class="akcia">
        <div class="button">
          <div
            v-if="!creditBuy"
            @click="
              $store.dispatch('pridajDoKosika', {
                id: produktyData.id,
                meta_data: '',
              })
            "
            class="rozdeleny-button"
          >
            <img src="@/assets/images/icons/taska.svg" alt="" />
            <p>Do košíka</p>
          </div>
          <div
            v-else
            @click="buyWithCredit(produktyData.id)"
            class="rozdeleny-button"
          >
            <p>Zakúpiť</p>
          </div>
          <a
            class="hrat-ukazku rozdeleny-button"
            @click="vratZvukovuUkazku()"
            @mouseleave="stopAudio()"
          >
            <img
              src="@/assets/images/icons/zvuk.svg"
              alt="Hrať zvukovú ukážku"
            />
          </a>
        </div>
        <audio
          v-if="this.zvukovaUkazka != ''"
          controls
          ref="audioPlayer"
          style="display: none"
        >
          <source :src="this.zvukovaUkazka" type="audio/mpeg" />
          Váš prehliadač nepodporuje prehrávanie zvuku
        </audio>
      </div>
    </div>
  </div>
</template>

<script>
import ObtiaznostZapisov from "../Functionality/ObtiaznostZapisov.vue";

export default {
  components: { ObtiaznostZapisov },
  data() {
    return {
      sourceElementHeight: 0,
      produktDetaily: {},
      zvukovaUkazka: "",
      novinka: "",
      audioPlaying: false,
      reccursive: 0,
    };
  },
  mounted() {
    this.skontrolujVysku();
    // Pridajte poslúchača udalostí zmeny veľkosti okna
    window.addEventListener("resize", this.skontrolujVysku);

    // this.stihaniProduktyData();

    // Akcia, ktorá sa vykoná pri zmene elementu
    const observer = new ResizeObserver(() => {
      this.skontrolujVysku();
    });

    // Registrujeme observer na sledovanie zmeny veľkosti elementu
    observer.observe(this.$refs.sourceElement);

    console.log(this.produktyID);
    console.log(this.produktyData);
  },

  beforeUnmount() {
    // Odstráňte poslúchača udalostí pred zničením komponentu
    window.removeEventListener("resize", this.skontrolujVysku);
  },
  methods: {
    // napr. v tom istom komponente
    async buyWithCredit(id) {
      const axios = require("axios");

      try {
        // GET s cookies (ak používaš session na API)
        const url = `${this.$store.state.api}/helitime/buyWithCredit.php?product_id=${id}`;
        const { data } = await axios.get(url, { withCredentials: true });

        if (data?.status === "Request succesfull") {
          // Snackbar – ideálne cez mutation, ale ak ju ešte nemáš:
          this.$store.state.SnackBarText =
            "Číselný zápis bol priradený do vášho účtu";

          // ⬇️ Aktualizuj kredity z Vuexu
          await this.$store.dispatch("credits/fetchCredits");

          // ⬇️ Pridaj notu medzi vlastnené, aby sa hneď skryla v zozname
          // (V array-e je to reaktívne)
          this.$store.state.user.ownedNotes.push({ id, expires: "never" });
        } else {
          this.$store.state.SnackBarText =
            "Nepodarilo sa zakúpiť číselný zápis za kredity.";
        }
      } catch (err) {
        console.error("Chyba pri priraďovaní zápisu", id, err);
        this.$store.state.SnackBarText =
          "Nastala chyba pri nákupe za kredity. Skús to znova.";
      }
    },
    priceInCredit() {
      var cena = String(this.produktyData.price)
        .replace(",", ".")
        .replace(/[^\d.-]/g, "");
      cena = Number(cena * 100) || 0;
      cena = Math.ceil(cena / this.multiplier);
      return cena.toFixed(0);
    },
    skontrolujVysku() {
      const sourceElement = this.$refs.sourceElement;

      // Overí, či je sourceElement definovaný pred pokusom získať clientHeight
      if (sourceElement) {
        this.sourceElementHeight = sourceElement.clientHeight - 30;
      }
    },
    overstavNovinka() {
      const now = Math.floor(new Date().getTime() / 1000); // Prevedie aktuálny timestamp na sekundy

      if (this.produktyData.new <= now) {
        this.novinka = "";
      } else {
        this.novinka = this.produktyData.new;
      }
    },
    stihaniProduktyData() {
      if (!this.produktyData?.details?.trim()) {
        return;
      }
      try {
        let cleanedJsonString = this.produktyData.details.replace(
          /[\n\t]/g,
          ""
        );
        let jsonObject = JSON.parse(cleanedJsonString);
        this.produktDetaily = jsonObject;

        console.log("this.produktDetaily :>> ", this.produktDetaily);
        console.log(
          "this.produktyData.details :>> ",
          this.produktyData.details
        );

        this.overstavNovinka();

        if (this.produktDetaily.urcenePre == "trojradova-heligonka") {
          this.produktDetaily.urcenePre = "Trojradovú heligónku";
        } else {
          this.produktDetaily.urcenePre = "Dvojradovú heligónku";
        }
      } catch (error) {
        console.error("Error occurred, retrying:", error);
        if (this.reccursive >= 10) {
          return;
        }
        // Zavolá funkciu znova
        setTimeout(() => {
          // Upravte časový interval podľa potreby
          // Napríklad, ak chcete, aby sa kód vykonal znova po 1 sekunde, použite 1000
          this.reccursive++;
          this.stihaniProduktyData();
        }, 200);
      }
    },

    vratZvukovuUkazku() {
      this.zvukovaUkazka =
        this.$store.state.api +
        "/product/files/load.php/?id=" +
        this.produktyID +
        "&subdir=details&file=" +
        this.produktyData.name
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase() +
        "-Z.mp3";

      setTimeout(() => {
        this.toggleAudio();
      }, 200);
    },

    toggleAudio() {
      const audio = this.$refs.audioPlayer;
      if (this.audioPlaying) {
        audio.pause();
      } else {
        audio.play();
      }
      this.audioPlaying = !this.audioPlaying;
    },
    stopAudio() {
      const audio = this.$refs.audioPlayer;
      if (this.audioPlaying) {
        audio.pause();
        this.audioPlaying = false;
      }
    },
  },
  watch: {
    produktyData: {
      immediate: true, // spustí handler aj pri prvej inicializácii
      handler(newVal) {
        if (newVal && newVal.details) {
          // reset reccursive ak chceš novú várku pokusov
          this.reccursive++;
          this.stihaniProduktyData();
        }
      },
    },
  },

  props: {
    produktyID: {
      type: Array,
      default: () => [],
    },
    produktyData: {
      type: Object,
      default: () => ({}),
    },
    layouts: {
      type: Boolean,
      default: () => true,
    },
    creditBuy: {
      type: Boolean,
      default: () => false,
    },
    flexCenter: {
      type: Boolean,
      default: () => false,
    },
    multiplier: {
      type: [Number, String],
      default: null,
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";
// sekcia zapisy

.time-add {
  font-weight: 400;
  font-family: "Bahnshrift";
  margin: 0 auto;
  font-size: 0.9em;
  color: #7c7c7c;
  letter-spacing: 0.03em;
}

.zapisy {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: center;
  align-items: flex-start;
}
.zapis {
  display: flex;
  width: 12em;
  flex-direction: column;
  padding: 1em;
  // justify-content: flex-end;
  justify-content: space-evenly;
}

.nadpis {
  margin: auto;
}

.nadpis p {
  font-size: 1.4em;
  font-style: normal;
  font-weight: 700;
  line-height: 120%; /* 1.875rem */
}

.img-box-zapis {
  border-radius: 1.5625em;
  border: 0.3em solid #fef35a;
  background: var(
    --Linear,
    linear-gradient(140deg, #90c94f 35.64%, #fef35a 99.4%)
  );
  box-shadow: 3px 3px 5px 0px rgba(0, 0, 0, 0.25);

  width: 90%;
  margin: 0 auto;

  .text {
    padding: 0.5em;
    box-sizing: border-box;

    display: flex;
    flex-direction: column;
    justify-content: space-around;
    height: 100%;
  }

  .bigger {
    font-size: 1.325em;
    font-style: normal;
    font-weight: 700;

    line-height: 137.53%; /* 1.54719rem */
  }

  p {
    font-size: 1.08em;
    font-style: normal;
    font-weight: 400;

    line-height: 130%; /* 1.21875rem */
  }
}

.button {
  font-size: 1.375em;
  font-style: normal;
  font-weight: 400;
  line-height: 137.53%;

  width: 9em;
  align-items: center;
  justify-content: center;
  margin: 0.8em 0;
  padding: 0.5em 1.5em;
  white-space: nowrap;

  border-radius: unset;
  background: transparent;
  box-shadow: unset;
  gap: 4%;
}

.rozdeleny-button {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;

  padding: 0.3em 0.6em;
  border-radius: 1.5rem;

  filter: drop-shadow(-4px 0px 5px rgba(0, 0, 0, 0.15));
  background: #fef35a;
  box-shadow: 3px 3px 5px 0px rgba(0, 0, 0, 0.25);
}

.button .samostatny-button {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  width: 100%;
  // padding: 0.3em 0.6em;
  padding: 0.3em 1.5em;
  border-radius: 1.5rem;
  filter: drop-shadow(-4px 0px 5px rgba(0, 0, 0, 0.15));
  background: #fef35a;
  box-shadow: 3px 3px 4px 0px rgba(0, 0, 0, 0.25);

  p {
    font-size: 1.4em;
  }
}

.rozdeleny-button:not(.hrat-ukazku) {
  border-top-right-radius: 0.7rem;
  border-bottom-right-radius: 0.7rem;
}

.img-zapis {
  padding: 0.6em;
  padding-bottom: 0;
  margin-bottom: -0.4em;
  position: relative;

  .zapis-noty {
    width: 100%;
    height: 11em;
    object-fit: cover;
    object-position: top;
    z-index: -1;
    position: relative;
  }
}

.img-box-zapis .cena {
  border-radius: 0rem 0rem 1.4375em 1.4375em;
  background: var(
    --Linear,
    linear-gradient(140deg, #90c94f 35.64%, #fef35a 99.4%)
  );

  display: flex;
  align-items: center;
  justify-content: center;
  height: 2.9em;
}

.zakupene .img-box-zapis .cena {
  // height: auto;
  height: 3.5em;
}
.zakupene .img-box-zapis .cena p {
  padding: 0.5em;
  font-weight: 500;
}

.novinka {
  position: absolute;
  top: 0.8em;
  left: 0.8em;
  width: 8em;
}

.cena h4 {
  font-size: 1.55em;
  font-style: normal;
  font-weight: 400;
  line-height: 137.53%; /* 2.14894rem */

  margin: 0;
}

.obtiaznost {
  margin: auto;
  margin-bottom: -1.2em;
  width: 5em;
}

.box-zapis {
  transition: transform 0.8s;
  transform-style: preserve-3d;
}

.zapis:hover .box-zapis {
  transform: rotateY(180deg);
}

.hrat-ukazku {
  height: 100%;
  padding-right: 0.6em;
  background-position: center;
  background-size: cover;
  margin-bottom: 0 !important;
  transition-duration: 0.2s;

  border-top-left-radius: 0.7rem;
  border-bottom-left-radius: 0.7rem;
}

/* Position the front and back side */
.flip-card-front,
.flip-card-back {
  position: absolute;
  -webkit-backface-visibility: hidden; /* Safari */
  height: 14.5em;
  left: 50%;
  backface-visibility: hidden;
  transform: translateX(-50%);
}

.zakupene .flip-card-front,
.zakupene .flip-card-back {
  // height: auto;
  height: 15em;
  top: 2.9em;
}

// .zakupene .button {
//   margin-top: 1em !important;
// }

/* Style the front side (fallback if image is missing) */
.flip-card-front {
  background-color: #bbb;
  color: black;
}

/* Style the back side */
.flip-card-back {
  background-color: dodgerblue;
  color: white;
  transform: rotateY(180deg) translateX(50%);
}

.button {
  width: auto;
  margin-top: 11.5em !important;
  padding: 0;

  .rozdeleny-button:not(.hrat-ukazku) img {
    margin-right: 0.2em;
    width: 0.7em;
  }

  a {
    display: flex;
    align-items: center;
    padding: 0;
    justify-content: center;
    transition-duration: 0.2s;

    &:hover {
      transform: scale(1.1);
    }
  }

  p {
    font-size: 0.75em;
  }

  a img {
    width: 1em;
    padding: 0.3em 0.55em 0.5em 0.35em;
    margin: 0 -0.2em -0.2em 0;
    transition-duration: 1s;
    &:hover {
      transform: scale(1.2);
    }
  }
}

.box-zapis-flex {
  width: 20%;
  display: flex;
  justify-content: center;
}

//ROW layouts

.rowLayout {
  display: flex;
  flex-direction: row;
  width: 100%;
  align-items: center;
  justify-content: space-around;
  margin: 0.4em 0.5em;

  .name {
    width: 34%;

    p {
      width: 100%;
      text-align: left;
    }
  }
  .info {
    width: 13%;
  }
  .price {
    width: 8%;
    p {
      width: 100%;
      text-align: right;
    }
  }
  .akcia {
    width: 19%;
    font-size: 70%;
    display: flex;
    justify-content: center;

    .rozdeleny-button {
      font-size: 120%;
    }
  }

  p.table,
  p.info-mobile {
    font-weight: 500;
    font-size: 1.2em;
  }

  .info-mobile {
    display: none;
  }
}
.activeRowLayout {
  width: 100% !important;
  transition-duration: 0.2s;

  .button {
    margin: 0 !important;
  }

  &:hover {
    background: #90ca508c;
    border-radius: 1rem;
    transform: scale(1.01);
    transition-duration: 0.4s;
    box-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
  }
}

@media only screen and (max-width: 1500px) {
  .box-zapis-flex {
    width: 25%;
  }
}
@media only screen and (max-width: 1250px) {
  .rowLayout .akcia {
    font-size: 60%;
  }
}

@media only screen and (max-width: 1050px) {
  .box-zapis-flex {
    width: 33.3333%;
  }
}
@media only screen and (max-width: 1000px) {
  .rowLayout {
    gap: 3%;

    .name {
      width: 60%;
    }
    .info {
      width: 0;
      display: none;
    }
    .price {
      width: 15%;
    }
    .akcia {
      width: 25%;
      font-size: 70%;
    }
  }

  .info-mobile {
    display: flex !important;
    justify-content: flex-end;
  }
}

@media only screen and (max-width: 850px) {
  .mobile .button p {
    font-size: 0.85em;
  }

  // .mobile .button {
  //   width: 6.5em;
  // }

  .box-zapis-flex {
    width: 50%;
  }
}

@media only screen and (max-width: 750px) {
  .mobile {
    section .button {
      // padding: 0.5em 1.5em;
      padding: 0;
      margin: 0.6em 0;
      white-space: nowrap;
      width: auto;

      p {
        margin: unset;
        margin-bottom: -0.2em;
      }
    }
  }

  .nadpis p {
    font-size: 1.2em;
  }

  .img-box-zapis {
    border-radius: 1.3rem;

    .cena {
      border-radius: 0rem 0rem 1rem 1rem;
    }
  }

  .button a img {
    width: 0.9em;
  }

  .box-zapis-flex {
    width: 33.33333333333%;
  }

  .rowLayout {
    margin: 0.4em 1.2em 0.4em 0.5em;
  }
}

@media only screen and (max-width: 650px) {
  .rowLayout {
    margin: 0.8em 1.2em 0.8em 0.5em;
  }

  .rowLayout {
    gap: 3%;

    .name {
      width: 45%;
    }
    .price {
      width: 15%;
    }
    .akcia {
      width: 40%;
      font-size: 70%;
    }
  }
}
@media only screen and (max-width: 550px) {
  .box-zapis-flex {
    width: 50%;
  }
}
@media only screen and (max-width: 500px) {
  .mobile {
    section .button {
      margin: 0.6em auto 0;
      // padding: 0.3em 1.3em;
      padding: 0;

      p {
        font-size: 0.8em;
      }
    }
  }

  .time-add {
    margin: 0.7em auto 0;
  }

  .img-box-zapis .cena {
    height: 2.8em;
  }

  .zakupene .img-box-zapis .cena {
    // height: auto;
    height: 3.3em;
  }
}

@media only screen and (max-width: 430px) {
  .mobile {
    section .button {
      margin: 0.6em auto 0;
      // padding: 0.2em 1.2em;
      padding: 0;

      p {
        font-size: 0.75em;
      }
    }
  }
  .box-zapis-flex {
    font-size: 80%;
  }

  .rowLayout {
    // margin: 0.8em 2em 0.8em 0.5em;
    margin: 0.8em 0;
  }
}

@media only screen and (max-width: 350px) {
  .mobile {
    section .button {
      margin: 0.4em auto 0;
      // padding: 0.2em 1.1em;
      padding: 0;

      p {
        font-size: 0.7em;
      }
    }
  }
}
</style>
