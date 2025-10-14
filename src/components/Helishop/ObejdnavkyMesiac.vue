<template>
  <div
    v-for="(monthData, index) in getFilteredOrders()"
    :key="index"
    class="objednavka-mesiac"
  >
    <div
      v-if="monthData.data.length > 0"
      class="month-header"
      @click="toggleMonth(monthData)"
    >
      <h3>{{ getMonthName(monthData.month) }}</h3>
      <h5 class="smaller-h5">
        Počet objednávok:
        <strong>
          {{ monthData.data.length ? monthData.data.length : 0 }}
        </strong>
      </h5>
      <h5 class="smaller-h5">
        Vybavené objednávky:
        <strong>
          {{
            confirmedCount[monthData.year][monthData.month]
              ? confirmedCount[monthData.year][monthData.month]
              : 0
          }}
        </strong>
      </h5>
    </div>

    <div v-if="monthData.isToggled" class="objednavky">
      <ObjednavkaJedna
        v-for="(order, indexOrder) in monthData.data"
        :key="indexOrder"
        :item="order"
      ></ObjednavkaJedna>
      <div class="line horizontal" style="height: 0.35rem"></div>
    </div>
  </div>

  <div
    v-if="
      nacitaneData.length >= 3 &&
      (nacitaneData[nacitaneData.length - 1]?.data?.length || 0) +
        (nacitaneData[nacitaneData.length - 2]?.data?.length || 0) +
        (nacitaneData[nacitaneData.length - 3]?.data?.length || 0) >
        0
    "
    @click="nacitajDalsich5Mesiacov()"
    class="button"
  >
    <p>Načítaj viac</p>
  </div>
</template>

<script>
import ObjednavkaJedna from "./ObjednavkaJedna.vue";
export default {
  components: {
    ObjednavkaJedna,
  },
  props: {
    dataVyhladavanie: {
      type: Array, // Uisti sa, že očakávaš array
      required: true, // Ak je táto prop povinná
      defualt: () => [],
    },
  },
  watch: {
    dataVyhladavanie(newData) {
      if (newData && Array.isArray(newData.data) && newData.data.length > 0) {
        this.localDataVyhladavanie = newData;

        this.rozdelObjednavkyDoMesiacov();
      } else {
        this.vyhladavam = false;
      }
      console.log(
        "newData :>> ",
        newData,
        this.localDataVyhladavanie,
        this.localDataVyhladavanie.length
      );
    },
  },
  data() {
    return {
      aktualnyDatum: new Date(), // Začneme od aktuálneho mesiaca
      nacitaneData: [],
      localDataVyhladavanieData: [],
      confirmedCount: {},
      confirmedCountBackup: {},
      confirmedCountBackupOnlyOne: 0,
      vyhladavam: false,
      localDataVyhladavanie: [...this.dataVyhladavanie],
    };
  },
  mounted() {
    this.nacitajData();
  },
  methods: {
    getFilteredOrders() {
      // Ak nie je žiadny výsledok vo vyhľadávaní, použije sa nacitaneData
      if (!this.vyhladavam) {
        // Ak je prázdny dotaz, berieme všetky objednávky
        return this.nacitaneData;
      }

      if (this.localDataVyhladavanie.data.length > 0) {
        // Ak existujú dáta na vyhľadávanie (v dataVyhladavanie), vyhľadávame tam
        return this.localDataVyhladavanieData;
      } else {
        // Ak nie, prejdeme na nacitaneData
        return this.nacitaneData;
      }
    },
    toggleMonth(monthData) {
      monthData.isToggled = !monthData.isToggled;
    },
    async nacitajData() {
      this.aktualnyDatum = new Date(); // Reset na aktuálny dátum
      await this.nacitajObjednavky(-5); // Načítanie prvých 5 mesiacov dozadu
    },

    async nacitajDalsich5Mesiacov() {
      await this.nacitajObjednavky(-5); // Posunie sa ďalej o 5 mesiacov
    },
    async nacitajObjednavky(pocetMesiacov) {
      if (this.confirmedCountBackupOnlyOne == 1) {
        this.confirmedCount = JSON.parse(
          JSON.stringify(this.confirmedCountBackup)
        );
        this.confirmedCountBackupOnlyOne--;
      }
      const axios = require("axios");
      let vysledky = [];

      for (let i = 0; i < Math.abs(pocetMesiacov); i++) {
        // Nastavenie prvého a posledného dňa mesiaca s presným časom
        const rok = this.aktualnyDatum.getFullYear();
        const mesiac = this.aktualnyDatum.getMonth();

        // Ak rok ešte neexistuje v objektoch, vytvor ho
        if (!this.confirmedCount[rok]) {
          this.confirmedCount[rok] = {};
        }

        // Ak mesiac ešte neexistuje, nastav ho na 0
        if (!this.confirmedCount[rok][mesiac]) {
          this.confirmedCount[rok][mesiac] = 0;
        }

        let zaciatokMesiaca = new Date(
          rok,
          mesiac,
          1,
          0,
          0,
          0 // 00:00:00
        );
        let koniecMesiaca = new Date(
          rok,
          mesiac + 1,
          0,
          23,
          59,
          59 // 23:59:59
        );

        let fromString = this.formatDate(zaciatokMesiaca);
        let toString = this.formatDate(koniecMesiaca);

        console.log(`📅 Sťahujem objednávky od ${fromString} do ${toString}`);

        let data = new FormData();
        data.append("from", fromString);
        data.append("to", toString);

        let config = {
          method: "post",
          maxBodyLength: Infinity,
          url: this.$store.state.api + "/eshop/getOrdersFromTo.php",
          data: data,
        };

        try {
          let response = await axios.request(config);

          if (response.data.status === "Request succesfull") {
            console.log(`✅ Objednávky pre ${fromString}:`, response.data);

            // Pre prvú položku v response.data pridaj vlastnosti 'isToggled' a 'month'
            const firstItem = {
              ...response.data, // Uchová pôvodnú štruktúru objektu
              isToggled: false, // Predvolená hodnota
              month: mesiac, // Aktuálny mesiac
              year: rok,
            };

            // Skontroluj, či existuje `data` v `firstItem`
            if (firstItem.data && Array.isArray(firstItem.data)) {
              firstItem.data.forEach((item) => {
                if (item.status === "confirmed") {
                  // Zvýš počet potvrdených položiek pre daný mesiac a rok
                  this.confirmedCount[rok][mesiac]++;
                }
              });
            } else {
              console.error("firstItem.data neexistuje alebo nie je pole!");
            }

            // Pridaj túto upravenú položku do vysledky
            vysledky.push(firstItem);
          } else {
            console.error(`⚠ Chyba: Neúspešný request pre ${fromString}`);
          }
        } catch (error) {
          console.error(
            `❌ Chyba pri načítaní objednávok pre ${fromString}:`,
            error
          );
        }

        // Posun na predchádzajúci mesiac
        this.aktualnyDatum.setMonth(this.aktualnyDatum.getMonth() - 1);
      }

      // Pridanie načítaných dát do zoznamu
      this.confirmedCountBackup = JSON.parse(
        JSON.stringify(this.confirmedCount)
      );
      this.nacitaneData.push(...vysledky);
      console.log("📦 Všetky načítané objednávky:", this.nacitaneData);
    },
    rozdelObjednavkyDoMesiacov() {
      let rozdeleneObjednavky = [];
      if (this.confirmedCountBackupOnlyOne == 0) {
        this.confirmedCountBackup = JSON.parse(
          JSON.stringify(this.confirmedCount)
        );
        this.confirmedCountBackupOnlyOne++;
      }
      this.confirmedCount = {};

      // Ak máš len jeden objekt so .data
      const objednavky = this.localDataVyhladavanie?.data || [];

      objednavky.forEach((objednavka) => {
        console.log("objednavka.date:", objednavka.timestamp);
        let date = new Date(objednavka.timestamp);
        console.log("parsed date:", date);
        let rok = date.getFullYear();
        let mesiac = date.getMonth();

        // Skontroluj, či existuje `confirmedCount` pre daný rok a mesiac
        if (!this.confirmedCount[rok]) this.confirmedCount[rok] = {};
        if (!this.confirmedCount[rok][mesiac])
          this.confirmedCount[rok][mesiac] = 0;

        // Skontroluj, či už máme záznam pre tento mesiac
        let mesiacovyZaznam = rozdeleneObjednavky.find(
          (z) => z.month === mesiac && z.year === rok
        );

        if (!mesiacovyZaznam) {
          // Vytvor nový objekt pre tento mesiac
          mesiacovyZaznam = {
            data: [],
            isToggled: false,
            month: mesiac,
            year: rok,
          };
          rozdeleneObjednavky.push(mesiacovyZaznam);
        }

        const upravenaObjednavka = {
          ...objednavka,
        };

        // Pridaj do záznamu mesiaca
        mesiacovyZaznam.data.push(upravenaObjednavka);

        // Spočítaj potvrdené
        if (objednavka.status === "confirmed") {
          this.confirmedCount[rok][mesiac]++;
        }
      });

      // Výsledok nastavíme rovnako ako nacitaneData
      this.localDataVyhladavanieData = rozdeleneObjednavky;
      this.vyhladavam = true;

      console.log(
        "📦 Rozdelené objednávky do mesiacov:",
        this.localDataVyhladavanieData
      );
    },

    getMonthName(dateIndex) {
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

      return months[dateIndex]; // Vrátenie názvu mesiaca
    },
    formatDate(date) {
      let year = date.getFullYear();
      let month = String(date.getMonth() + 1).padStart(2, "0"); // 01-12
      let day = String(date.getDate()).padStart(2, "0"); // 01-31
      let hours = String(date.getHours()).padStart(2, "0"); // 00-23
      let minutes = String(date.getMinutes()).padStart(2, "0"); // 00-59
      let seconds = String(date.getSeconds()).padStart(2, "0"); // 00-59
      return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
    },
  },
};
</script>

<style lang="scss" scoped>
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

  &:hover {
    transform: scale(1.03);
    transition-duration: 0.2s;
  }
}

.objednavka-mesiac {
  width: 95%;
  max-width: 65em;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

.objednavky {
  display: flex;
  flex-direction: column-reverse;
}

.button {
  display: flex;
  width: 10em;
  justify-content: center;
  align-items: center;
  margin: 3em auto 5em;
  font-size: 1em;
}
</style>
