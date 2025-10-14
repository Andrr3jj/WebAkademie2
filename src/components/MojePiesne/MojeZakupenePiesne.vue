<template>
  <section :id="$route.name + 'maPiesne'" class="computer mobile">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="line horizontal w-80"></div>

      <p v-if="novePiesne" class="nadpis">Nové zakúpené piesne:</p>
      <zoznam-zapisov
        v-if="novePiesne"
        :produktyID="produktyNoveID"
        :produktyData="produktyNoveData"
      />

      <div v-if="novePiesne" class="line horizontal w-80"></div>

      <div class="buttons">
        <div
          v-if="zoznamPiesni == 'oblubene'"
          @click="
            nacitajIneProduktyID('dokoncene');
            ascending = undefined;
          "
          class="button"
        >
          <p>Obľúbené</p>
        </div>
        <div
          v-if="zoznamPiesni == 'dokoncene'"
          @click="
            nacitajProduktyID();
            ascending = undefined;
          "
          class="button"
        >
          <p>Dokončené</p>
        </div>
        <div
          v-if="zoznamPiesni == 'zoznam'"
          @click="
            nacitajIneProduktyID('oblubene');
            ascending = undefined;
          "
          class="button"
        >
          <p>Zoznam piesní</p>
        </div>
        <div class="layouts">
          <img
            :key="gridActive"
            :src="gridImage"
            alt="Zobrazenie vedľa seba"
            @click="toggleLayout()"
          />
          <img
            :key="gridActive"
            :src="rowImage"
            alt="Zobrazenie pod sebou"
            @click="toggleLayout()"
          />
        </div>
        <div @click="zoradeniePodlaNarocnosti()" class="button">
          <p v-if="ascending == undefined">Zoradiť podľa náročnosti</p>
          <p v-else-if="ascending">Podľa náročnosti ⬆</p>
          <p v-else>Podľa náročnosti ⬇</p>
        </div>
      </div>

      <zoznam-zapisov-header
        v-if="!gridActive"
        :layouts="gridActive"
        class="zapisy-table"
      ></zoznam-zapisov-header>
      <zoznam-zapisov
        :produktyID="produktyID"
        :produktyData="produktyData"
        :layouts="gridActive"
      />

      <div class="tipy-a-obrazok">
        <div class="tipy">
          <p class="nadpis-tipov">Tipy na zlepšenie hry na heligónke:</p>
          <div class="tip">
            <p class="nadpis-tipu">Rozdelenie melódie a basov</p>
            <p class="text-tipu">
              Začnite tým, že sa naučíte melodickú časť piesne samostatne, bez
              basov. Neskôr sa sústreďte len na basy. Toto oddelené cvičenie
              posilní Váš základ a pomôže zlepšiť techniku.
            </p>
          </div>
          <div class="tip">
            <p class="nadpis-tipu">
              Postupné spájanie melodickej a basovej časti
            </p>
            <p class="text-tipu">
              Keď sa cítite pohodlne s každou časťou samostatne, začnite ich
              postupne spájať. Skúšajte postupné zapájanie basov do melodickej
              časti a naopak. Urobte to v pomalom tempe, aby ste mohli presne
              pracovať na spojení oboch častí.
            </p>
          </div>
          <div class="tip">
            <p class="nadpis-tipu">Opakovanie a postupné zrýchľovanie</p>
            <p class="text-tipu">
              Opakujte tieto kroky, až kým nebudete mať istotu v oboch častiach
              piesne. Postupne zrýchľujte tempo, aby ste si upevnili svoje
              zručnosti a zlepšili rýchlosť a presnosť hry.
            </p>
            <article>
              Pamätajte, že trpezlivosť je kľúčová. S týmto postupom by ste mali
              vidieť postupné zlepšenie vo svojej hre na heligónke.
            </article>
          </div>
        </div>
        <img
          src="@/assets/images/gallery/ZoznamPiesniObrazok.png"
          alt="Juraj a andrej sa učia na svojich chybách"
        />
      </div>
    </div>
  </section>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";
import ZoznamZapisov from "../Zapisy/ZoznamZapisov.vue";
import ZoznamZapisovHeader from "../../components/Zapisy/HeaderTableZoznamZapisov.vue";

export default {
  components: { TheHeadline, ZoznamZapisov, ZoznamZapisovHeader },
  data() {
    return {
      produktyID: [],
      allProduktyID: [],
      produktyData: {},
      produktyNoveID: [],
      produktyNoveData: {},
      paginationNumber: 0,
      paginationNumberEnd: 20,
      nacitatViacej: true,
      ascending: undefined,
      componentKey: 0,
      oblubeneProduktyID: [],
      zoznamPiesni: "",
      novePiesne: false,
      gridActive: true,

      // ➕ lokálne vlastnené ID načítané zo servera (nechávame store nedotknutý)
      ownedIdsFromServer: null,
    };
  },
  async mounted() {
    window.scrollTo(0, 0);

    // 🔸 najprv si vytiahni čerstvé owned ID priamo zo servera – pre tento view
    await this.refreshOwnedIdsForView();

    // po vstupe zhasni badge a nastav baseline na *aktuálny serverový počet*
    this.consumeNewBadgeAndSetBaseline();

    // potom načítaj dáta (použije ownedIdsFromServer, ak je k dispozícii)
    this.nacitajProduktyID();
    this.nacitajNoveProdukty();
  },
  computed: {
    gridImage() {
      return this.gridActive
        ? require("@/assets/images/icons/gridLayoutActive.png")
        : require("@/assets/images/icons/gridLayout.png");
    },
    rowImage() {
      return this.gridActive
        ? require("@/assets/images/icons/rowLayout.png")
        : require("@/assets/images/icons/rowLayoutActive.png");
    },
  },
  methods: {
    // —— nové pomocné metódy ——
    getUserKey() {
      const u = this.$store?.state?.user;
      if (!u) return null;
      return (
        (u.id != null && String(u.id)) ||
        (u.email && String(u.email).toLowerCase()) ||
        "guest"
      );
    },
    consumeNewBadgeAndSetBaseline() {
      try {
        const uKey = this.getUserKey();
        if (!uKey) return;
        const curCount =
          (this.ownedIdsFromServer && this.ownedIdsFromServer.length) ||
          this.$store.state.user?.ownedNotes?.length ||
          0;
        localStorage.setItem(`ha_songs_alert_${uKey}`, "0"); // zhasnúť badge
        localStorage.setItem(`ha_songs_last_seen_${uKey}`, String(curCount)); // baseline
      } catch {
        /* noop */
      }
    },
    async refreshOwnedIdsForView() {
      // vezmi čerstvý zoznam vlastnených zápisov pre daného usera (iba na zobrazenie)
      try {
        if (!this.$store.state.user) return;
        const axios = require("axios");
        const url = this.$store.state.api + "/product/getOwned.php";
        const resp = await axios.get(url);
        if (
          resp?.data?.status === "Request succesfull" &&
          Array.isArray(resp.data.data)
        ) {
          // môžu chodiť ako {id:..., expire:...} alebo len čísla – podpor oboje
          const arr = resp.data.data.map((it) =>
            typeof it === "object" ? it.id : it
          );
          this.ownedIdsFromServer = arr
            .map((n) => Number(n))
            .filter((n) => !Number.isNaN(n));
        }
      } catch (e) {
        // ak padne, nič sa nedeje – stránka beží s tým čo má v store
        this.ownedIdsFromServer = null;
      }
    },
    // —— pôvodné metódy (zachované), len drobná úprava vo filtrovaní ——
    toggleLayout() {
      this.gridActive = !this.gridActive;
      this.$forceUpdate();
    },
    nacitajNoveProdukty() {
      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/getMarkedAsNew.php",
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            if (response.data.data.length === 0) {
              this.novePiesne = false;
              return;
            } else {
              this.novePiesne = true;
            }

            for (let i = 0; i < response.data.data.length; i++) {
              this.produktyNoveID.push(response.data.data[i]);
              this.nacitajProduktyNoveData(response.data.data[i]);
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nacitajIneProduktyID(typ) {
      const axios = require("axios");

      let cesta = "";
      if (typ == "oblubene") {
        cesta = "/user/like/getProductsLiked.php/";
        this.zoznamPiesni = "oblubene";
      } else {
        cesta = "/user/finish/getProductsFinished.php/";
        this.zoznamPiesni = "dokoncene";
      }

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + cesta,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            if (response.data.data.length === 0) {
              if (typ == "oblubene") {
                this.$store.state.SnackBarText =
                  "Nemáte žiadne obľúbené piesne";
                setTimeout(() => {
                  this.nacitajIneProduktyID("dokoncene");
                }, 1000);
              } else {
                this.$store.state.SnackBarText =
                  "Nemáte žiadne dokončené piesne";
                setTimeout(() => {
                  this.nacitajProduktyID();
                }, 1000);
              }
              return;
            }

            this.oblubeneProduktyID = response.data.data;

            const vsetkyProduktyID = this.allProduktyID;
            this.produktyID = [];
            this.produktyData = {};

            const oblubeneProduktyIDArray = Array.from(this.oblubeneProduktyID);
            for (let i = 0; i < oblubeneProduktyIDArray.length; i++) {
              const item = oblubeneProduktyIDArray[i];

              for (let j = 0; j < vsetkyProduktyID.length; j++) {
                const note = vsetkyProduktyID[j];
                if (item === note) {
                  this.produktyID.push(note);
                  this.nacitajProduktyData(note);
                }
              }
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nacitajProduktyID() {
      const axios = require("axios");

      this.zoznamPiesni = "zoznam";
      this.produktyID = [];
      this.produktyData = {};

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/search.php?" +
          "&pagination_index=0&pagination_limit=10000&details_key=typ&details_value=Zapis",
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          // ✅ ZDROJ OWNED IDS: preferuj čerstvé zo servera, inak store
          const ownedIdsRaw =
            Array.isArray(this.ownedIdsFromServer) &&
            this.ownedIdsFromServer.length
              ? this.ownedIdsFromServer
              : (this.$store.state.user?.ownedNotes || []).map((n) =>
                  typeof n === "object" ? n.id : n
                );

          const ownedNotesIds = {};
          ownedIdsRaw.forEach((id) => {
            const num = Number(id);
            if (!Number.isNaN(num)) ownedNotesIds[num] = true;
          });

          const jsonArray = response.data.data || [];

          if (jsonArray.length < 20) {
            this.nacitatViacej = false;
          } else {
            this.nacitatViacej = true;
          }

          for (let i = 0; i < jsonArray.length; i++) {
            const id = jsonArray[i].id;
            if (ownedNotesIds[id]) {
              this.produktyID.push(id);
              this.nacitajProduktyData(id);
            }
          }

          this.allProduktyID = this.produktyID;
        })
        .catch((error) => {
          console.log(error);
        });
    },

    resetujProduktyANacitaj() {
      this.produktyID = [];
      this.produktyData = {};
      this.paginationNumberEnd = 20;
      this.paginationNumber = 0;
      this.nacitajProduktyID();
    },

    nacitajViacProduktov() {
      this.paginationNumberEnd = this.paginationNumberEnd + 20;
      if (this.paginationNumberEnd >= 20) {
        this.paginationNumber = this.paginationNumber + 20;
      }
      this.nacitajProduktyID();
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
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    nacitajProduktyNoveData(id) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.produktyNoveData[response.data.data.id] = response.data.data;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    zoradeniePodlaNarocnosti() {
      if (this.ascending === undefined) {
        this.ascending = true;
      } else {
        this.ascending = !this.ascending;
      }

      const itemsArray = Object.values(this.produktyData);
      const sortedItems = [];
      const sortedIds = [];

      for (let i = 0; i < itemsArray.length; i++) {
        let inserted = false;
        const item = itemsArray[i];
        const difficulty = parseInt(item.difficulty);

        if (sortedItems.length === 0) {
          sortedItems.push(item);
          sortedIds.push(item.id);
          continue;
        }

        for (let j = 0; j < sortedItems.length; j++) {
          const sortedItem = sortedItems[j];
          const sortedDifficulty = parseInt(sortedItem.difficulty);

          if (
            (this.ascending && difficulty < sortedDifficulty) ||
            (!this.ascending && difficulty > sortedDifficulty)
          ) {
            sortedItems.splice(j, 0, item);
            sortedIds.splice(j, 0, item.id);
            inserted = true;
            break;
          }
        }

        if (!inserted) {
          sortedItems.push(item);
          sortedIds.push(item.id);
        }
      }

      this.produktyData = {};
      sortedItems.forEach((item) => {
        this.produktyData[item.id] = item;
      });

      this.produktyID = sortedIds;
    },
  },
};
</script>

<style lang="scss" scoped>
.nadpis {
  font-size: 2em;
  font-style: normal;
  font-weight: 600;
  margin: 0.7em 0 1em;
  padding: 0 5%;
}

section p {
  text-align: center;
  font-size: 1.4em;
  font-weight: 400;
  line-height: 115%;
  margin: auto;
}

.line {
  margin: auto;
}

.ciselne-zapisy {
  width: 11em;
  margin: 2em auto 1em;
  transform: translateX(0.5em);
  transition-duration: 0.3s;

  &:hover {
    transform: translateX(0.5em) scale(1.1);
    transition-duration: 0.2s;
  }
}

h3 {
  text-align: center;
  font-size: 2.2em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.875rem */
  margin: auto;
}

.link:hover h3 {
  text-decoration: underline;
}

.buttons {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 1.5em 2.5% 0.5em;
  width: 95%;
}

.button p {
  font-size: 0.8em;
  width: 15.7em;
  padding: 0.1em 0;
}

.button {
  // padding: 0.3em 0.8em;
  padding: 0.5em 1em;
}

.mobile .scroll {
  padding-bottom: 0;
}

//typi hry

.tipy-a-obrazok {
  display: flex;
  flex-direction: row;
  align-items: flex-end;
  justify-content: center;
  img {
    width: 40%;
  }
}
.tipy {
  padding: 4em 0 4em 5%;
  width: 75%;
}
.nadpis-tipov {
  font-size: 1.5vw;
  font-style: normal;

  font-weight: 700;
  text-transform: uppercase;
  text-align: left;
}

.nadpis-tipu {
  text-align: left;
  margin: 1em 0 0.1em;

  font-size: 1.2496vw;
  font-style: normal;
  font-weight: 700;
}

.text-tipu {
  font-size: 0.9vw;
  font-style: normal;
  font-weight: 400;
  text-align: left;
}
.mobile {
  padding-bottom: 20px;
}

article {
  margin-top: 2em;
  text-align: left;
  width: 80%;

  font-size: 1vw;
  font-style: normal;
  font-weight: 600;
}

.layouts {
  display: flex;
  margin: auto;
  gap: 15%;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition-duration: 0.3s;

  & > img {
    width: 3em;
    height: auto;
    box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.25);
    border-radius: 0.6rem;

    &:hover {
      transform: scale(1.1);
      transition-duration: 0.2s;
      box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.35);
    }
  }
}

.zapisy-table {
  margin: 1.5em 1em 0.5em;
}
.zapisy-zoznam {
  padding: 0 1.5em 0 1.5em;
}

@media only screen and (max-width: 1250px) {
  .nadpis-tipov {
    font-size: 1.875em;
  }

  .nadpis-tipu {
    font-size: 1.5625em;
  }

  .text-tipu {
    font-size: 1.125em;
  }

  article {
    font-size: 1.25em;
  }

  .tipy-a-obrazok {
    display: flex;
    flex-direction: column;

    img {
      width: 100%;
      max-width: 30em;
    }
  }

  .tipy {
    padding: 4em 5% 0;
    width: 90%;
  }
}

@media only screen and (max-width: 1230px) {
  .buttons {
    font-size: 90%;
  }
}

@media only screen and (max-width: 1060px) {
  .buttons {
    font-size: 80%;
  }

  .button p {
    width: auto;
  }
}

@media only screen and (max-width: 1000px) {
  .button p {
    font-size: 0.7em;
  }

  .zapisy-zoznam {
    padding: 0.4em 0 0 1.2em;
  }
}

@media only screen and (max-width: 900px) {
  .button p {
    font-size: 0.6em;
    // width: 16.2em;
  }
}

@media only screen and (max-width: 850px) {
  .buttons {
    justify-content: space-evenly;
  }
}

@media only screen and (max-width: 750px) {
  .w-70 {
    width: 90% !important;
  }
  .nadpis {
    margin: 1em 0 0.5em;
    font-size: 1.5em;
  }

  .buttons {
    justify-content: space-between;
  }

  .button p {
    font-size: 0.8em;
  }

  .mobile {
    height: auto;

    .scroll {
      overflow: visible;
      padding-bottom: 10em;
      margin-bottom: -10em;
    }
  }

  .tipy {
    padding: 2em 5% 0;
  }

  .tipy-a-obrazok img {
    margin: auto;
    max-width: 25em;
  }

  .nadpis-tipov {
    font-size: 1.5625em;
  }

  .nadpis-tipu {
    font-size: 1.125em;
  }

  .text-tipu {
    font-size: 0.9375em;
  }

  article {
    font-size: 1.0625em;
    width: 100%;
  }
}

@media only screen and (max-width: 600px) {
  .button p {
    font-size: 0.65em;
  }
}

@media only screen and (max-width: 510px) {
  .button p {
    font-size: 0.6em;
    letter-spacing: 0.05em;
  }

  .buttons {
    margin: 1.5em 1% 0.5em;
    width: 96%;
    gap: 2%;

    font-size: 3.1vw;

    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
  }

  .layouts {
    order: 4;
    font-size: 100%;
    margin: 1em auto 0.5em 2em;
  }
}
</style>
