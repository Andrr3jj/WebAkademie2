<template>
  <transition name="info-box">
    <div class="song-info-box">
      <div class="song">
        <h1>Názov piesne</h1>
        <h5>{{ name }}</h5>

        <div class="line horizontal w-80"></div>

        <div class="dificulty">
          <obtiaznost-zapisov :produktObtiaznost="difficulty" />
        </div>

        <div class="info">
          <p class="strong">Číselný zápis napísal:</p>
          <p class="info-text">{{ author }}</p>
        </div>

        <!-- if there are numeric notations, show only tempo and genre -->
        <div v-if="hasCiselneZapisy" class="info-box">
          <div class="info">
            <p class="strong">Tempo hrania:</p>
            <p class="info-text">{{ tempo }}</p>
          </div>
          <div class="line"></div>
          <div class="info">
            <p class="strong">Žáner piesne:</p>
            <p class="info-text">{{ genre }}</p>
          </div>
        </div>

        <!-- if none, also show selectable scales -->
        <div v-else class="info-box">
          <div class="info">
            <p class="strong">Tempo hrania:</p>
            <p class="info-text">{{ tempo }}</p>
            <p class="strong">Žáner piesne:</p>
            <p class="info-text">{{ genre }}</p>
          </div>
          <div class="line"></div>
          <div class="info">
            <p class="strong">Stupnica piesne:</p>
            <div class="info-buttons">
              <div
                v-for="element in stupnice"
                :key="element"
                class="button Adumu"
                :class="{ green: aktualnaStupnica === element }"
                @click="onStupnicaSelected(element)"
              >
                <p>
                  {{
                    element === "D" || element === "A"
                      ? element + " - Mol"
                      : element + " - Dur"
                  }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <div class="line horizontal w-80 last-line"></div>

        <p class="strong">Ďalšie obľúbené piesne:</p>
        <p v-if="noFavoriteItem" class="info-text">
          Nemáte žiadne obľúbené číselné zápisi
        </p>

        <carousel-zapis
          v-if="!noFavoriteItem"
          :produktyID="produktyID"
          :produktyData="produktyData"
        />
      </div>
    </div>
  </transition>
</template>

<script>
import ObtiaznostZapisov from "@/components/Functionality/ObtiaznostZapisov.vue";
import CarouselZapis from "./CarouselZapis.vue";

export default {
  name: "SongInfoBox",
  components: { ObtiaznostZapisov, CarouselZapis },
  props: {
    name: { type: String, required: true },
    difficulty: { type: String, required: true },
    author: { type: String, required: true },
    genre: { type: String, required: true },
    tempo: { type: String, required: true },
    stupnice: { type: Array, required: true },
    aktualnaStupnica: { type: String, required: true },
    hasCiselneZapisy: { type: Boolean, required: true },
  },
  data() {
    return {
      noFavoriteItem: false,
      produktyID: [],
      filteredFavorites: [],
      produktyData: {},
    };
  },
  mounted() {
    this.nacitajIneProduktyID();
  },
  methods: {
    onStupnicaSelected(element) {
      this.$emit("update-stupnica", element);
    },
    nacitajIneProduktyID() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/user/like/getProductsLiked.php/",
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status === "Request succesfull") {
            if (response.data.data.length === 0) {
              this.noFavoriteItem = true;
              return;
            }

            const favIds = response.data.data; // [ 3, 7, 18, ... ]
            this.noFavoriteItem = favIds.length === 0;

            // Vytvoríme si Set všetkých ID, ktoré reálne vlastníš
            const ownedIds = new Set(
              this.$store.state.user?.ownedNotes.map((n) => n.id) || []
            );

            // Filtrovať iba tie ID z favIds, ktoré vlastníš
            const filteredFavIds = favIds.filter((id) => ownedIds.has(id));

            // Uložíme si ich do `produktyID` a hneď voláme nacitajProduktyData()
            filteredFavIds.forEach((id) => {
              this.produktyID.push(id);
              this.nacitajProduktyData(id);
            });

            if (this.produktyID.length === 0) {
              this.noFavoriteItem = true;
            }

            console.log("this.produktyId :>> ", this.produktyId);
            console.log("this.produktyData :>> ", this.produktyData);
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
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.produktyData[response.data.data.id] = response.data.data;
            console.log(JSON.stringify(response.data.data));
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style scoped lang="scss">
.song-info-box {
  position: absolute;
  top: 13px;
  left: 50%;
  transform: translateX(-50%);
  background: #ededed;
  border: 4px solid #fef35a;
  border-radius: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  padding: 1.5em;
  width: 90%;
  z-index: 49;
  box-sizing: border-box;
}

.button {
  font-size: 0.8em;
  margin: 0.5em auto;
  width: max-content;
}

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 3.5vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0;
}

h5 {
  text-align: center;

  font-size: 1.4875em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

.dificulty {
  transform: rotate(180deg);
  margin: auto;
  margin-top: -1.3em; //-1em
  width: 6em;
}

.info-text {
  text-align: center;

  font-size: 1em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%;
  /* 1.79688rem */
  margin-top: 0.3em;
  margin-bottom: 0.6em;
}

.line {
  width: 0.25rem;
}

.line.horizontal {
  height: 0.3rem;
}

.strong {
  color: #000;
  text-align: center;
  font-size: 1.06em;
  font-style: normal;
  font-weight: 600;
  line-height: 115%;
  margin-top: 0.6em;
  /* 1.79688rem */
}

.info-box {
  display: flex;
  justify-content: center;
  gap: 3vw;
  margin: 1em 0;
  align-items: stretch;

  .line {
    height: auto;
  }

  .info:first-child {
    max-width: 40%;
  }
}

.last-line {
  margin-bottom: 1.6em;
}
.song-info-box {
  transform-origin: top center;
}

.info-box-enter-active,
.info-box-leave-active {
  transition: all 0.3s ease;
}

.info-box-enter-from,
.info-box-leave-to {
  opacity: 0;
  transform: scale(0.8) translateY(-20px);
}

.info-box-enter-to,
.info-box-leave-from {
  opacity: 1;
  transform: scale(1) translateY(0);
}
</style>
