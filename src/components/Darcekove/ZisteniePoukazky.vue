<template>
  <div class="gift-balance">
    <div class="info-block">
      <h2>Ako na to?</h2>
      <ol>
        <li>Zadajte kód z darčekovej poukážky</li>
        <li>Poukážka sa automaticky aktivuje v košíku</li>
        <li>Ak je hodnota nákupu vyššia, zvyšok môžete doplatiť kartou</li>
        <li>
          Ak nevyužijete celú sumu, zostávajúci kredit zostane na vašej poukážke
          a môžete ho použiť neskôr
        </li>
      </ol>
      <div class="form-group">
        <VoucherInput
          v-model="kod"
          placeholder="Zadajte kód poukážky"
          @submit="potvrdiKod"
        />
        <button class="button" @click="potvrdiKod">Potvrdiť</button>
      </div>
    </div>

    <div class="balance-block">
      <p>Aktuálny zostatok na vašej darčekovej poukážke:</p>
      <h3>{{ formatMena(zostatok) }}</h3>
    </div>
    <p class="note">
      Ak máte viac darčekových poukážok, môžete zadať viacero kódov – ich
      hodnota sa automaticky spočíta!
    </p>
    <GratulaciaOznamenie
      v-if="gratulaciaModal"
      :text="gratulaciaText"
      @close="gratulaciaModal = false"
    />
  </div>
</template>

<script>
import VoucherInput from "@/components/Functionality/VoucherInput.vue";
import GratulaciaOznamenie from "@/components/Functionality/GratulaciaOznamenie.vue";
import axios from "axios";

export default {
  name: "ZistenieHodnoty",
  components: { VoucherInput, GratulaciaOznamenie },
  data() {
    return {
      kod: "",
      zostatok: 0,
      gratulaciaModal: false,
      gratulaciaText: "",
    };
  },
  methods: {
    async potvrdiKod() {
      if (!this.kod || this.kod.replace(/[^A-Za-z0-9]/g, "").length < 8) {
        this.showSnackbar("Zadajte platný kód poukážky.");
        return;
      }

      const api = "https://heligonkovaakademia.sk/api/cart/";
      const applyApi = "https://heligonkovaakademia.sk/api/product/gift_card/";

      try {
        // 1. Skús, či je na predplatné/zápisy zdarma
        let res = await axios.get(applyApi + "apply.php", {
          params: { code: this.kod },
        });

        // Ak vráti data (access), zobraz gratuláciu a skonči
        if (
          (res?.data?.status === "Request succesfull" ||
            res?.data?.status === "Request fullfiled") &&
          res.data.data &&
          res.data.data.length > 0
        ) {
          // Tu si môžeš upraviť text podľa toho čo je v res.data.data (zoznam accesov, názvy atď.)
          const produkty = res.data.data
            .map((item) => item.name || item.title || item)
            .join(", ");
          this.gratulaciaText =
            "Gratulujeme! Práve ste získali prístup k: " +
            produkty +
            ". Ďakujeme za využitie darčekovej poukážky.";
          this.gratulaciaModal = true;
          this.zostatok = 0;
          await this.nacitajPoukazky(); // pre istotu, ak by niečo zmenilo stav
          return;
        }

        // 2. Skús ako hodnotovú (addGiftCard)
        res = await axios.get(api + "addGiftCard.php", {
          params: { code: this.kod },
        });

        if (res?.data?.status === "Request fullfiled") {
          this.showSnackbar("Poukážka bola pridaná do košíka.");
          await this.nacitajPoukazky();
          return;
        }

        // Chybové stavy
        if (res?.data?.status === "Request failed") {
          if (res.data.data === "Already used") {
            this.showSnackbar("Tento kód poukážky už bol použitý.");
            return;
          }
          if (res.data.data === "Already in cart") {
            this.showSnackbar("Tento kód už je pridaný vo vašom košíku.");
            await this.nacitajPoukazky();
            return;
          }
        }

        // Neplatný/nesprávny kód
        this.showSnackbar("Poukážka je neplatná alebo už bola použitá.");
        await this.nacitajPoukazky();
      } catch (err) {
        this.showSnackbar("Chyba komunikácie so serverom.");
        await this.nacitajPoukazky();
      }
    },
    formatMena(suma) {
      return suma ? suma.toFixed(2).replace(".", ",") + " €" : "0,00 €";
    },
    showSnackbar(text) {
      if (this.$store) this.$store.state.SnackBarText = text;
      else alert(text);
    },
    async nacitajPoukazky() {
      try {
        const api = "https://heligonkovaakademia.sk/api/cart/";
        const res = await axios.get(api + "loadGiftCard.php");
        if (Array.isArray(res.data.data)) {
          const poukazky = res.data.data.map((item) =>
            typeof item === "string" ? JSON.parse(item) : item
          );
          const suma = poukazky
            .filter((p) => !p.used_timestamp) // len podla timestamp
            .reduce((sum, v) => sum + (Number(v.value) || 0), 0);
          this.zostatok = suma;
        } else {
          this.zostatok = 0;
        }
      } catch (err) {
        this.showSnackbar("Chyba pri načítaní zostatku.");
        this.zostatok = 0;
      }
    },
  },
  async mounted() {
    await this.nacitajPoukazky();
  },
};
</script>

<style scoped lang="scss">
@import "@/assets/sass/_colors.scss";

.gift-balance {
  display: flex;
  justify-content: space-evenly;
  flex-wrap: wrap;
  padding: 2em;
  border-radius: 1em;
  font-family: sans-serif;
  margin-top: 2em;

  .info-block {
    max-width: 500px;

    h2 {
      font-size: 1.7em;
      font-weight: bold;
      text-align: left;
      font-family: "Bahnschrift", sans-serif !important;
    }

    ol {
      padding-left: 1.5em;
      margin-bottom: 1em;
      font-family: "Bahnschrift", sans-serif !important;

      li {
        font-size: 1.4em;
        text-align: left;
        width: 97%;
      }
    }

    .voucher-input {
      width: 60%;
    }

    .form-group {
      display: flex;
      gap: 0.5em;
      margin-bottom: 2em;
      font-family: "Bahnschrift", sans-serif !important;
      align-items: center;
      flex-wrap: wrap;

      .row {
        width: 100%;
        max-width: 20em;
      }

      input,
      select {
        font-size: 1.4em;
        font-style: normal;
        font-weight: 300;
        line-height: 115%;
        background: transparent;
        border: none;
        padding: 0.4em 0.5em;
        text-align: left;
        width: 100%;
      }

      .button {
        font-family: "Adumu", sans-serif;
        font-size: 1.2em;
        padding: 1em 2em;
      }
    }
  }

  .balance-block {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    min-width: 240px;
    padding: 1em;

    p {
      font-size: 1.7em;
      width: 15em;
      line-height: 115%;
      text-align: center;
    }

    h3 {
      font-size: 2.2em;
      font-weight: bold;
      margin: 0.5em 0 0;
      font-family: "Bahnschrift", sans-serif !important;
    }
  }

  .note {
    font-size: 1.4em;
    margin-top: 1em;
    font-family: "Bahnschrift", sans-serif !important;
    text-align: center;
    width: 100%;
  }

  @media (max-width: 768px) {
    flex-direction: column;
    align-items: center;
    padding: 1em;

    .form-group {
      flex-direction: column;
      align-items: stretch;

      .gift-balance .info-block .form-group .button {
        margin-top: 1em;
        font-size: 1em;
      }
    }

    .balance-block p {
      font-size: 1.4em;
      width: 100%;
    }
  }
}
</style>
