<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <div class="nadpis">
        <h5>
          Zoznam Faktúr - Rok
          <input
            type="text"
            v-model="year"
            @keyup="handleKeyPress"
            @click="handleClick"
            @input="nacitajVsetkyMesiace()"
          />
        </h5>
      </div>

      <div class="line horizontal w-80"></div>

      <!-- Dropdowny podľa mesiacov -->
      <div v-for="(invoices, month) in data" :key="month" class="month-section">
        <div class="month-header" @click="toggleMonth(month)">
          <h3>{{ getMonthName(month) }}</h3>
          <h5 class="smaller-h5">
            Počet faktúr: <strong> {{ invoices.length }} </strong>
          </h5>
        </div>
        <table v-if="expandedMonths.includes(month)">
          <thead>
            <tr>
              <th style="width: 70%"></th>
              <th style="width: 30%"></th>
            </tr>
          </thead>
          <tbody>
            <!-- Ak nie sú žiadne faktúry, zobrazí správu -->
            <tr v-if="invoices.length === 0">
              <td style="justify-content: center">
                <p>Žiadne faktúry za tento mesiac</p>
              </td>
              <td></td>
            </tr>
            <tr v-for="(invoice, index) in invoices" :key="index">
              <td v-if="invoices.length > 0">
                <p class="highlight">
                  <strong>{{ index + 1 }}.</strong>
                  {{
                    invoice.replace(
                      "https://heligonkovaakademia.sk/data/uploads/invoices/",
                      ""
                    )
                  }}
                </p>
              </td>
              <td v-if="invoices.length > 0">
                <div @click="otvorFakturu(invoice)" class="button">
                  <a>Zobraziť</a>
                </div>
              </td>
            </tr>
          </tbody>
        </table>

        <div
          v-if="expandedMonths.includes(month) && invoices.length > 0"
          @click="stiahniVsetky(month)"
          class="button download-month"
        >
          <a>Stiahnuť</a>
        </div>
      </div>

      <!-- <div class="search">
        <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
        <input
          @input="oneskoreneVyhladavanie()"
          v-model="year"
          type="text"
          placeholder="Zadajte rok za ktorý chcete vyhľadať faktúry"
        />
      </div> -->
    </div>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);

    // this.nacitajUcty();

    this.actualYear = new Date().getFullYear();
    this.year = this.actualYear;

    if (this.$store.state.userRoles?.roles?.includes("invoice_master")) {
      this.nacitajVsetkyMesiace();
    } else {
      this.$router.go(-1);
    }
  },
  data() {
    return {
      data: {},
      year: 2024,
      mont: 1,
      actualYear: null,
      expandedMonths: [1],
      clickCount: 0,
    };
  },
  methods: {
    getMonthName(month) {
      const months = [
        "Január",
        "Február",
        "Marec",
        "Apríl",
        "Máj",
        "Jún",
        "Júl",
        "August",
        "September",
        "Október",
        "November",
        "December",
      ];
      return months[month - 1];
    },
    toggleMonth(month) {
      if (this.expandedMonths.includes(month)) {
        // Ak je mesiac vyrolovaný, skryjeme ho
        this.expandedMonths = this.expandedMonths.filter((m) => m !== month);
      } else {
        // Ak mesiac nie je vyrolovaný, pridáme ho
        this.expandedMonths.push(month);
      }
    },
    handleKeyPress(event) {
      if (event.key === "ArrowUp" || event.key === "ArrowRight") {
        this.year++;
      } else if (event.key === "ArrowDown" || event.key === "ArrowLeft") {
        this.year--;
      }

      this.nacitajVsetkyMesiace();
    },
    handleClick() {
      this.clickCount++;

      setTimeout(() => {
        if (this.clickCount === 2) {
          this.year--; // Dvojitý klik pridá rok
          this.nacitajVsetkyMesiace();
        } else if (this.clickCount === 3) {
          this.year++; // Trojitý klik uberie rok
          this.nacitajVsetkyMesiace();
        }
        this.clickCount = 0; // Reset kliknutia po vykonaní akcie
      }, 250); // Nastavenie časového limitu na registráciu klikov
    },
    // Pomocná metóda na načítanie faktúr pre všetky mesiace
    async nacitajVsetkyMesiace() {
      this.data = {};
      for (let mesiac = 1; mesiac <= 12; mesiac++) {
        this.month = mesiac;
        await this.nacitajUcty();
      }
    },
    async nacitajUcty() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: `${this.$store.state.api}/invoice/download.php?year=${this.year}&month=${this.month}`,
      };

      try {
        const response = await axios.request(config);

        if (response.data.status === "Request sucesfull") {
          // Ak ešte pre tento mesiac nemáme dáta, inicializujeme ich ako pole
          if (!this.data[this.month]) {
            this.data[this.month] = [];
          }

          // Pridáme nové faktúry do správneho mesiaca
          this.data[this.month] = [
            ...this.data[this.month],
            ...response.data.data,
          ];

          console.log("Aktualizované dáta:", this.data);
        } else {
          console.error("Načítanie nebolo úspešné.");
        }
      } catch (error) {
        console.error("Chyba pri načítaní faktúr:", error);
      }
    },
    oneskoreneVyhladavanie() {
      // Vyčkajte 500 ms a potom zavolajte stiahniID()
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.nacitajUcty();
      }, 500);
    },
    otvorFakturu(invoice) {
      const cesta = invoice.replace(
        "https://heligonkovaakademia.sk/data/uploads/invoices/",
        this.$store.state.api + "/invoice/load_pdf.php?id="
      );
      window.open(cesta, "_blank");
    },
    stiahniVsetky(mesiac) {
      let index = 0;
      const fakturyZaMesiac = this.data[mesiac] || []; // Získať faktúry pre daný mesiac

      const downloadNext = () => {
        if (index < fakturyZaMesiac.length) {
          const url = fakturyZaMesiac[index];

          // Extrahovanie názvu súboru z pôvodnej URL pred nahradením
          const fileName = url.split("/").pop() + ".pdf";

          // Nahradenie časti URL za novú
          const replacedUrl = url.replace(
            "https://heligonkovaakademia.sk/data/uploads/invoices/",
            this.$store.state.api + "/invoice/load_pdf.php?id="
          );

          // Vytvorenie odkazu pre sťahovanie
          const link = document.createElement("a");
          link.href = replacedUrl; // Nová URL pre sťahovanie
          link.download = fileName; // Nastavenie názvu súboru

          // Pridanie a simulovanie kliknutia na odkaz
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);

          // Posun indexu a volanie ďalšej iterácie
          index++;
          setTimeout(downloadNext, 1000); // 1-sekundová pauza
        }
      };

      downloadNext();
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
  font-size: 5.75vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0;
}

h5,
input {
  text-align: center;

  font-size: 2.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%; /* 2.51563rem */
  margin: 0;
  width: auto;
  margin-bottom: 0.3em;
}

input {
  width: 3em;
  font-size: 0.9em;
}

table {
  width: 80%;
  border-collapse: collapse;
  margin: 3em 10%;
  margin-bottom: 6em;
}

th,
td {
  padding: 0.1em 0.5em;
  text-align: left;
  font-size: 1.3em;
}

td:not(td:last-child) {
  border-bottom: 1px solid #dddddd5e;
}

th {
  padding: 0.5em;
}

th:nth-child(2),
td:nth-child(2),
th:nth-child(3),
td:nth-child(3) {
  text-align: center;
}

td:nth-child(2) {
  font-weight: 600;
}

.button {
  width: 2.2em;
  padding: 0.15em 0.8em;
  margin: 0.1em 0;
  a {
    font-size: 0.52em;
    text-align: center;
    display: flex;
    justify-content: center;
    width: 100%;
  }
}

// .all {
//   position: absolute;
//   width: fit-content;
//   top: 9vw;
// }

.download-month {
  margin: 1em auto 0.5em;
  font-size: 1.8em;
  padding: 0.1em 1.3em;
}

.number {
  padding: 0.3em 2em;
  justify-content: center;

  p {
    font-size: 0.8em;
  }
}

.nadpis {
  display: flex;
  align-items: flex-start;
  justify-content: center;
  gap: 3em;
}

td:last-child .button {
  margin-left: auto;
}

.search {
  border-radius: 1.5625rem;
  border: 6px solid #fef35a;
  background: rgba(233, 233, 233, 0.93);
  box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.35);

  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0 0.5em;
  margin: 1em 0;

  width: 40em;
  font-size: 0.8em;
  position: fixed;
  left: 50%;
  transform: translate(-50%, -50%);
  bottom: 0em;

  img {
    width: 2em;
  }

  input {
    width: 100%;

    font-size: 1.25em;
    font-style: normal;
    font-weight: 400;
    line-height: 137.53%; /* 1.71913rem */
    letter-spacing: 0.0625rem;

    margin-bottom: -0.2em;
  }
}

.month-header {
  display: flex;
  justify-content: space-between;
  width: 80%;
  max-width: 35em;
  background-color: #fef35a;
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
  cursor: pointer;
  border-radius: 3em;
  margin: 1.5em auto;
  padding: 0.4em 2.5em;

  h3 {
    font-size: 1.3em;
    letter-spacing: 0.1em;
    margin: auto 0;
  }

  .smaller-h5 {
    font-weight: 600;
    font-size: 1.3em;
    margin: auto 0;
    letter-spacing: 0.03em;
  }
}

table {
  max-width: 45em;
  margin: auto;
  width: 85%;

  thead {
    display: none;
  }

  td:first-child {
    display: flex;
    margin: 0.2em;
  }

  tr:hover .highlight {
    background-color: rgba(142, 200, 78, 0.5);
    border-radius: 0.5em; /* Zaoblené rohy */
    padding: 0.2em 0.5em; /* Vnútorné odsadenie okolo textu */
  }
}
</style>
