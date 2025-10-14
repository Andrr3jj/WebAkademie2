<template>
  <div class="prehlad">
    <h2 class="Bahnschrift">Prehľad bodov</h2>
    <p class="sub Bahnschrift">
      Ktoré ste získali za učenie, hranie a plnenie úloh
    </p>
    <div class="line horizontal"></div>

    <div v-if="isLoading" class="loading">Načítavam...</div>
    <div v-else-if="errorMessage" class="error">{{ errorMessage }}</div>

    <div v-else class="body-flex-table">
      <!-- Hlavicka -->
      <div class="body-row head">
        <div class="cell popis">Získane/ využité body:</div>
        <div class="cell datum">Dátum získania:</div>
        <div class="cell pocet Bahnschrift">Počet:</div>
      </div>
      <!-- Dáta -->
      <div class="body-row" v-for="(polozka, i) in body" :key="i">
        <div class="cell popis">
          <template v-if="polozka.akcia === 'buy'">
            Nákup:
            <span v-if="polozka.produkt">
              {{
                produktyMapa[polozka.produkt]?.nazov ||
                produktyMapa[polozka.produkt]?.name ||
                "#" + polozka.produkt
              }}
            </span>
          </template>
          <template v-else-if="polozka.akcia === 'bonus'"> Bonus </template>
          <template v-else-if="polozka.akcia === 'manual'"> Manuálne </template>
          <template v-else>
            Úloha:
            {{
              polozka.akcia
                ? polozka.akcia.charAt(0).toUpperCase() + polozka.akcia.slice(1)
                : "-"
            }}
          </template>
        </div>
        <div class="cell datum">{{ polozka.datum || "-" }}</div>
        <div
          class="cell pocet"
          :class="{ plus: polozka.pocet > 0, minus: polozka.pocet < 0 }"
        >
          {{ polozka.pocet > 0 ? "+" : "" }}{{ polozka.pocet }}
        </div>
      </div>
      <div v-if="!body.length" class="body-row">
        <div class="cell no-data" colspan="3">Žiadne záznamy.</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PrehladUloh",
  props: {
    userId: {
      type: [String, Number],
      required: true,
    },
  },
  data() {
    return {
      body: [],
      isLoading: false,
      errorMessage: "",
      produktyMapa: {},
    };
  },
  watch: {
    userId: {
      immediate: true,
      handler() {
        this.loadHistory();
      },
    },
  },
  methods: {
    async loadHistory() {
      if (!this.userId) return;
      this.isLoading = true;
      this.errorMessage = "";
      this.body = [];

      try {
        // Volanie API
        const url = `https://heligonkovaakademia.sk/api/helitime/getHelitimeHistory.php?id=${encodeURIComponent(
          this.userId
        )}`;
        const res = await fetch(url, { credentials: "include" });
        const json = await res.json();

        let items = [];

        // Ak je data pole, použijeme priamo
        if (Array.isArray(json.data)) {
          items = json.data;
        }
        // Ak je data string, rozdelíme podľa '|'
        else if (typeof json.data === "string" && json.data.trim()) {
          items = json.data
            .split("|")
            .map((s) => s.trim())
            .filter(Boolean);
        }
        // Záloha na iné možné formáty
        else if (Array.isArray(json.history)) {
          items = json.history;
        } else if (Array.isArray(json)) {
          items = json;
        } else {
          const possible = Object.values(json).find((v) => Array.isArray(v));
          if (Array.isArray(possible)) items = possible;
        }

        // Parsovanie jednotlivých riadkov
        this.body = items
          .map((line) => {
            // Regex pre tvoje záznamy
            const match = line.match(
              /^([0-9. :]+) -> (\w+)(?: product_id:(\d+))? credit_before:(\d+) credit_after:(\d+)$/
            );
            if (!match) return null;
            const datum = match[1];
            const akcia = match[2];
            const produkt = match[3] ?? null;
            const credit_before = +match[4];
            const credit_after = +match[5];
            const pocet = credit_after - credit_before;

            return {
              datum,
              akcia,
              produkt,
              pocet,
            };
          })
          .filter(Boolean); // odstráni nevalidné riadky
      } catch (e) {
        console.error("Chyba pri načítaní helitime histórie:", e);
        this.errorMessage = "Nepodarilo sa načítať históriu bodov.";
      } finally {
        this.isLoading = false;
      }
    },
    async nacitajProduktyMapu() {
      try {
        const axios = require("axios");
        const res = await axios.get(
          `${this.$store.state.api}/product/search.php?details_key=typ&details_value=Zapis&pagination_limit=1000`
        );
        const produkty = res.data.data || [];
        this.produktyMapa = Object.fromEntries(
          produkty.map((p) => [String(p.id), p])
        );
      } catch (e) {
        console.error("Nepodarilo sa načítať produkty:", e);
        this.produktyMapa = {};
      }
    },
  },
  mounted() {
    this.nacitajProduktyMapu();
    this.loadHistory();
  },
};
</script>

<style scoped lang="scss">
.prehlad {
  h2 {
    font-size: 2em;
    font-weight: 600;
    margin-bottom: 0.1em;
    font-family: "Bahnschrift", sans-serif;
  }

  .sub {
    font-size: 1.5em;
    color: #000;
    margin-bottom: 1em;
  }

  .body-flex-table {
    display: flex;
    flex-direction: column;
    gap: 0.3em;
    margin: 2em auto 0 auto;
    max-width: 900px;
    width: 100%;
  }
  .body-row {
    display: flex;
    flex-direction: row;
    align-items: flex-end;
    justify-content: flex-start;
    gap: 2.5em;
    padding: 0.1em 0;
    font-size: 1.16em;

    &.head {
      font-weight: 700;
      font-size: 1.15em;
      margin-bottom: 0.3em;
      .cell {
        color: #000;
        text-align: center;
        font-family: "Bahnschrift", sans-serif;
      }
    }
  }
  .cell {
    flex: 1 1 0;
    min-width: 120px;
    font-size: 1em;
    text-align: center;
    &.datum {
      min-width: 120px;
    }
    &.pocet {
      min-width: 70px;
      font-weight: 700;
      text-align: center;
      font-family: "Adumu", sans-serif;
    }
    &.popis {
      min-width: 180px;
    }
  }
  .plus {
    color: #00913f;
  }
  .minus {
    color: #eb001b;
  }
  .no-data {
    color: #888;
    font-style: italic;
    font-size: 1em;
    padding-top: 0.5em;
    text-align: center;
  }

  @media (max-width: 700px) {
    .body-flex-table {
      max-width: 99vw;
    }
    .body-row,
    .body-row.head {
      gap: 0.8em;
      font-size: 0.95em;

      .cell {
        text-align: left;
      }
    }
    .cell {
      min-width: 50px;
      font-size: 0.98em;
      text-align: left;
      word-break: break-word;
      padding-right: 0.1em;
      &.popis {
        min-width: 120px;
      }
      &.datum {
        min-width: 90px;
      }
      &.pocet {
        text-align: left;
      }
    }
  }
}
</style>
