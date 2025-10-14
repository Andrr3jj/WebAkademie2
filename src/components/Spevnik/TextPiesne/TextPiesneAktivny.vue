<template>
  <div class="text-piesne" v-if="songDataInternal">
    <h1 class="fade-in">Názov piesne</h1>
    <h5 class="fade-in-delayed">
      {{ songDataInternal.nazov || "Neznámý názov" }}
    </h5>
    <div class="line horizontal w-40"></div>

    <div class="info">
      <span>
        Typ piesne: <strong>{{ songDataInternal.typ || "-" }}</strong>
      </span>
      <span>
        Žáner: <strong>{{ songDataInternal.zaner || "-" }}</strong>
      </span>
      <span>
        Pôvod: <strong>{{ songDataInternal.povod || "-" }}</strong>
      </span>
      <span v-if="songDataInternal.autor && songDataInternal.autor.trim()">
        Autor: <strong>{{ songDataInternal.autor }}</strong>
      </span>
    </div>

    <div class="piesnovy-blok">
      <div class="text-cast">
        <div class="text">
          <div
            v-for="(sloha, i) in rozdelText"
            :key="i"
            class="sloha fade-in"
            :style="{ animationDelay: 0.1 * i + 's' }"
          >
            <span
              v-for="(riadok, idx) in sloha"
              :key="idx"
              class="riadok"
              :style="{ display: idx ? 'block' : 'inline' }"
              >{{ riadok }}</span
            >
          </div>
        </div>
        <div class="akcie fade-in-delayed">
          <div class="akcia">
            <button
              class="akcia-btn"
              @click="toggleFavorite"
              :aria-label="
                isFavorite ? 'Odstrániť z obľúbených' : 'Pridať do obľúbených'
              "
              :class="{ favorite: isFavorite }"
            >
              <img
                :src="heartIconUrl"
                alt="srdce"
                class="icon"
                width="26"
                height="21"
                aria-hidden="true"
              />
            </button>
            <p>
              {{
                isFavorite ? "Odstrániť z obľúbených" : "Pridať do obľúbených"
              }}
            </p>
          </div>
          <div class="akcia">
            <GreenMusicPlayer v-if="audioUrl" :audio-url="audioUrl" />
            <p>Prehrať pieseň</p>
          </div>
        </div>
      </div>
      <div class="zapis-cast fade-in-slow" v-if="zapisPreHeligonku">
        <h3 class="zapis-nadpis">Zápis pre heligónku</h3>
        <JedenZapis
          :produktyID="zapisPreHeligonku.id"
          :produktyData="zapisPreHeligonku"
          :layouts="true"
          :flexCenter="false"
        />
      </div>
    </div>
    <div class="line horizontal w-80"></div>
    <h4 class="fade-in-delayed">Ďalšie piesne, ktoré by si mohol poznať</h4>
    <ZapisGrid
      :produkty="dalsieProdukty"
      :active-name="songDataInternal.nazov"
      :active-id="songDataInternal.id"
      :active-product-id="songDataInternal.product_id"
    />
  </div>
  <div v-else>
    <div class="text-piesne">
      Pieseň neexistuje alebo sa nepodarilo načítať.
    </div>
  </div>
</template>

<script>
import JedenZapis from "@/components/Zapisy/JedenZapis.vue";
import ZapisGrid from "@/components/Spevnik/TextPiesne/ZapisGrid.vue";
import GreenMusicPlayer from "./GreenMusicPlayer.vue";

const heartImg = new URL("@/assets/images/icons/HeartIcon.svg", import.meta.url)
  .href;

export default {
  name: "TextPiesneAktivny",
  components: {
    JedenZapis,
    ZapisGrid,
    GreenMusicPlayer,
  },
  props: {
    songDetail: { type: Object, default: null },
    dalsieProdukty: {
      type: Array,
      default: () => [],
    },
  },
  data() {
    return {
      zapisPreHeligonku: null,
      audio: null,
      isPlaying: false,
      audioUrl: "",
      isFavorite: false,
      lastViewedId: null,
      songDataInternal: null,
      lastProcessedId: null,
    };
  },

  computed: {
    rozdelText() {
      if (!this.songDataInternal || !this.songDataInternal.text_piesne)
        return [];

      return this.songDataInternal.text_piesne
        .split("//n//n")
        .filter(Boolean)
        .map((raw) => {
          const riadky = raw.split("//n").filter(Boolean);
          if (riadky.length > 1 && /^\d+\.$/.test(riadky[0].trim())) {
            riadky[0] = `${riadky[0]} ${riadky[1]}`;
            riadky.splice(1, 1);
          }
          return riadky;
        });
    },
    heartIcon() {
      return heartImg;
    },
  },
  methods: {
    async toggleFavorite() {
      if (!this.songDataInternal?.id) return;

      try {
        const body = new URLSearchParams({
          id: this.songDataInternal.id,
        }).toString();

        const res = await fetch(
          "https://heligonkovaakademia.sk/api/noty/texty/favorite.php",
          {
            method: "POST",
            credentials: "include",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body,
          }
        );

        const json = await res.json();
        if (json.status === "Request succesfull") {
          this.isFavorite = json.data === "favorite";

          this.$store.state.SnackBarText = this.isFavorite
            ? "Pieseň pridaná do obľúbených"
            : "Pieseň odstránená z obľúbených";
        }
      } catch (e) {
        console.warn("Chyba pri označovaní obľúbených:", e);
      }
    },
    async nacitajProdukt(id) {
      try {
        const res = await fetch(
          `https://heligonkovaakademia.sk/api/product/loadLimited.php?id=${id}`
        );
        const json = await res.json();

        if (json.status?.toLowerCase().includes("succes")) {
          const p = json.data;
          this.zapisPreHeligonku = {
            id: id.toString(),
            name: p.name || "Zápis",
            price: p.price || "0.00",
            difficulty: Number(p.difficulty) || 0,
            details: p.details || "",
            new: p.new || "",
            timestamp: p.timestamp || "",
          };
        } else {
          this.zapisPreHeligonku = null;
        }
      } catch (e) {
        console.error("Chyba pri načítaní produktu:", e);
        this.zapisPreHeligonku = null;
      }
    },
    async addView(id) {
      try {
        await fetch(
          "https://heligonkovaakademia.sk/api/noty/texty/addView.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ id }).toString(),
            credentials: "include",
          }
        );
      } catch (e) {
        console.warn("Nepodarilo sa započítať zobrazenie:", e);
      }
    },
    async getAudioUrl(product_id) {
      if (!product_id) return "";

      try {
        const res = await fetch(
          `https://heligonkovaakademia.sk/api/product/loadLimited.php?id=${product_id}`
        );
        const json = await res.json();

        const nazov = json?.data?.name;
        if (!nazov) return "";

        const normalizedName = nazov
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase();

        return `https://heligonkovaakademia.sk/api/product/files/load.php/?id=${product_id}&subdir=details&file=${normalizedName}-Z.mp3`;
      } catch (e) {
        console.warn("Chyba pri fetchovaní názvu pesničky:", e);
        return "";
      }
    },
  },
  mounted() {
    const val = this.songDetail;
    // Ak ešte nemáme songDetail alebo je to rovnaké ID ako naposledy spracované, nič nerob
    if (!val || !val.id || this.lastProcessedId === val.id) return;

    // Označíme si, že sme už toto ID spracovali
    this.lastProcessedId = val.id;

    // Interná kópia detailu piesne
    this.songDataInternal = JSON.parse(JSON.stringify(val));

    // ✅ addView - len raz pre nové ID
    this.addView(val.id);

    // ✅ Získanie ID aktuálneho používateľa a kontrola "favorite"
    (async () => {
      try {
        const res1 = await fetch(
          "https://heligonkovaakademia.sk/api/user/info/getAdditionalInformation.php",
          { credentials: "include" }
        );
        const data1 = await res1.json();
        const userId = String(data1?.data?.id || "");

        const parsedFavorites = JSON.parse(this.songDetail.oblubene || "[]");
        this.isFavorite =
          Array.isArray(parsedFavorites) && parsedFavorites.includes(userId);
      } catch (e) {
        console.warn("Chyba pri zisťovaní obľúbených:", e);
      }
    })();

    // ✅ Načítanie produktu a audia (len ak existuje product_id)
    if (val.product_id && val.product_id !== "0") {
      this.nacitajProdukt(val.product_id);
      this.getAudioUrl(val.product_id).then((url) => {
        this.audioUrl = url;
      });
    } else {
      this.zapisPreHeligonku = null;
      this.audioUrl = "";
    }
  },
};
</script>

<style lang="scss" scoped>
/* --- Animácie --- */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in {
  animation: fadeInUp 0.6s ease-out forwards;
  opacity: 0;
}

.fade-in-delayed {
  animation: fadeInUp 0.6s ease-out forwards;
  animation-delay: 0.3s;
  opacity: 0;
}

.fade-in-slow {
  animation: fadeInUp 1s ease-out forwards;
  opacity: 0;
}

/* --- Štýl stránky --- */
h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.75vw;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke: 0.04em black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0;
}

h5 {
  text-align: center;
  font-size: 2.8em;
  font-weight: 300;
  margin: 0.5em 0 0.3em;
}

h4 {
  font-size: 2em;
  text-align: center;
  font-family: "Bahnschrift", sans-serif;
}

h3 {
  font-family: "Bahnschrift", sans-serif;
  font-size: 1.5em;
}

.w-40 {
  width: 40%;
  margin-inline: auto;
}

.w-80 {
  width: 80%;
  margin-inline: auto;
  margin-top: 4em;
}

.info {
  display: flex;
  gap: 3em;
  font-size: 1.1em;
  margin: 2em 0 2.5em 0;
  color: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;

  span {
    color: rgba(0, 0, 0, 0.5);
    white-space: nowrap;
    font-weight: normal;
  }
}

@media (max-width: 700px) {
  .info {
    flex-direction: column;
    gap: 0.6em;
    align-items: center;
    font-size: 1em;
  }
}

.piesnovy-blok {
  display: flex;
  justify-content: space-evenly;
  gap: 3em;
  align-items: flex-start;
  margin-top: 5em;
  flex-wrap: wrap;
  margin-bottom: 5em;
}

.text {
  margin-top: 2em;
}

.sloha {
  margin-bottom: 1em;
  font-size: 1.2em;
  text-align: center;
  display: block;
}

.cislo {
  display: inline;
  font-weight: normal;
  font-size: 1em;
  /* rovnaké ako riadok */
  color: #000;
  margin-right: 0.25em;
  vertical-align: baseline;
}

.riadok {
  display: inline;
  font-weight: normal;
}

.sloha .riadok:not(:first-child) {
  display: block;
}

@media (max-width: 700px) {
  .riadok {
    font-size: 1.1em;
  }

  .cislo {
    font-size: 1em;
  }
}

.akcie {
  display: flex;
  gap: 2em;
  margin-top: 2em;
  justify-content: center;
}

.akcia {
  display: flex;
  flex-direction: column;
  align-items: center;

  p {
    font-size: 0.75em;
    color: rgba(0, 0, 0, 0.5);
    margin-top: 0.5em;
  }
}

.akcia-btn {
  display: flex;
  align-items: center;
  gap: 0.5em;
  background-color: rgba(254, 243, 90, 0.7);
  border: none;
  border-radius: 17px;
  padding: 0.6em 1.2em;
  font-weight: bold;
  font-size: 0.95em;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.2s ease;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.15);
}

.akcia-btn:hover {
  background-color: rgba(254, 243, 90, 0.85);
  transform: translateY(-2px) scale(1.03);
}

.akcia-btn:active {
  transform: scale(0.96);
  box-shadow: none;
}

.akcia-btn .ikona {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 21px;
}

.zapis-cast {
  text-align: center;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.zapis-nadpis {
  font-size: 1.5625em;
  font-weight: bold;
  margin-bottom: 0.5em;
  max-width: 15.625em;
}

@media screen and (max-width: 1024px) {
  .zapis-cast {
    width: 100%;
  }
}
</style>
