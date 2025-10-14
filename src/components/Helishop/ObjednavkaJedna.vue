<template>
  <div class="jedna-objednavka">
    <div class="jedna">
      <p class="info">
        Obj-{{ new Date(itemData.timestamp).getFullYear() }}-{{
          itemData.order_real_id
        }}
      </p>
      <p class="info info-email">Od: {{ itemData.user_data.email }}</p>
      <p class="info">
        Dňa: {{ new Date(itemData.timestamp).toLocaleDateString() }}
      </p>
      <p class="info">Produkty: {{ itemData.products_real_cout }}</p>
      <img
        v-if="itemData.status == 'created'"
        src="@//assets/images/icons/nevybavena-objednavka.png"
        alt="Nevybavena objednavka"
      />
      <img
        v-else
        src="@/assets/images/icons/vybavena-objednavka.png"
        alt="Vybavena objednavka"
      />

      <div @click="showInfoToggle" class="button">
        <p>{{ showInfo ? "&nbsp;Skryť&nbsp;" : "Zobraziť" }}</p>
      </div>
      <span
        @click="openDeleteModal"
        class="delete-btn"
        title="Vymazať objednávku"
      >
        <img src="@/assets/images/icons/kos.svg" alt="Vymazať" />
      </span>
    </div>
    <div v-if="showInfo" class="more-info">
      <div class="tovar">
        <div class="riadok">
          <p class="nadpis">Tovar:</p>
          <p class="nadpis ks">Ks:</p>
          <p class="nadpis cena">Cena:</p>
        </div>
        <div
          v-for="([key, product], index) in Object.entries(
            itemData.products_combined
          )"
          :key="key"
          class="riadok"
        >
          <p class="only-info">
            {{ index + 1 }}.
            <strong>
              {{
                product.name.includes(" - ")
                  ? product.name.substring(0, product.name.lastIndexOf(" - "))
                  : product.name
              }}
            </strong>
            {{
              product.name.includes(" - ")
                ? product.name.substring(product.name.lastIndexOf(" - ") + 3)
                : ""
            }}
          </p>
          <p class="only-info kusy ks">
            <strong>{{ product.qty }}</strong>
          </p>
          <p class="only-info kusy">
            <strong
              >{{
                (parseFloat(product.price) || 0).toFixed(2).replace(".", ",")
              }}
              €</strong
            >
          </p>
        </div>
        <div class="riadok border-top">
          <p class="nadpis">Spolu:</p>
          <p class="nadpis ks"></p>
          <p class="nadpis">
            {{
              (parseFloat(itemData.price_total) / 100)
                .toFixed(2)
                .replace(".", ",")
            }}
            €
          </p>
        </div>
      </div>
      <div class="information">
        <div class="box">
          <p class="nadpis">Odberateľ:</p>
          <p class="only-info">
            {{ itemData.user_data.name + " " + itemData.user_data.surname }}
          </p>
          <p class="only-info">{{ itemData.billingAddress }}</p>
        </div>
        <div v-if="itemData.deliveryAddress != ''" class="box">
          <p class="nadpis">Doruč. Adresa:</p>
          <p class="only-info">
            {{ itemData.user_data.name + " " + itemData.user_data.surname }}
          </p>
          <p class="only-info">{{ itemData.deliveryAddress }}</p>
        </div>
        <div class="box">
          <p class="nadpis">Spôs. Doruč.:</p>
          <p>
            {{
              itemData.products_combined["0"]
                ? itemData.products_combined["0"].name
                : "Neznámy sposob dopravy"
            }}
          </p>
          <!-- <p class="only-info">+ dobierka</p> -->
        </div>
        <div class="box">
          <p class="nadpis">Tel. č.:</p>
          <p class="only-info">{{ itemData.user_data.tel }}</p>
        </div>

        <div
          @click="sendedToggle"
          class="button"
          :class="{ green: itemData.status != 'created' }"
        >
          <p>{{ itemData.status != "created" ? "Odoslané" : "Odoslať" }}</p>
        </div>
      </div>
    </div>
    <div class="line horizontal"></div>
  </div>
  <div
    v-if="showDeleteModal"
    class="modal-overlay"
    @click.self="closeDeleteModal"
  >
    <div class="modal-content">
      <h3>Vymazať objednávku?</h3>
      <p>Naozaj chceš vymazať túto objednávku? Táto akcia je nezvratná.</p>
      <div class="modal-buttons">
        <button @click="deleteOrder" class="red-btn">Vymazať</button>
        <button @click="closeDeleteModal" class="gray-btn">Zrušiť</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      showInfo: false,
      vybavena: false,
      itemData: {},
      showDeleteModal: false,
    };
  },
  methods: {
    showInfoToggle() {
      this.showInfo = !this.showInfo;
    },
    sendedToggle() {
      if (this.itemData.status == "created") {
        this.nastavStav();
      } else {
        this.$store.state.SnackBarText =
          "Email o spracovaní sa dá poslať len raz :)";
      }
    },
    async nastavStav() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      data.append("id", this.itemData.order_real_id);
      data.append("status", "confirmed");

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/eshop/changeOrderStatus.php",
        data: data,
      };

      try {
        let response = await axios.request(config);

        if (response.data.status === "Request succesfull") {
          this.$store.state.SnackBarText =
            "Obejdnavka bola nastavená na spracované. Email bol odoslaní zákazníkovy " +
            this.itemData.user_data.name;
          this.itemData.status = "confirmed";
        } else {
          this.$store.state.SnackBarText =
            "Obejdnávka nebola zmenená: " + response.data.data;
          console.log(
            "Pokazilo sa updatetovanie stavu objednávky :>> ",
            response.data
          );
        }
      } catch (error) {
        this.$store.state.SnackBarText = "Objednávka nebola zmenená ";
        console.log("Pokazilo sa updatetovanie stavu objednávky :>> ");
      }
    },
    async nacitajObjednavku() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/eshop/getOrder.php?id=" + this.item.id,
      };

      try {
        let response = await axios.request(config);

        if (response.data.status === "Request succesfull") {
          this.itemData = response.data.data;

          let productDetails = JSON.parse(this.itemData.products_detailed);

          // Uložíme produkty do this.itemData.products_combined
          this.itemData.products_combined = productDetails;
        } else {
          console.error(`⚠ Chyba: Neúspešný request pre`);
        }
      } catch (error) {
        console.error(`❌ Chyba pri načítaní objednávky pre:`, error);
      }
    },
    openDeleteModal() {
      this.showDeleteModal = true;
    },
    closeDeleteModal() {
      this.showDeleteModal = false;
    },
    deleteOrder() {
      // tu spravíš DELETE request alebo metódu na zmazanie objednávky
      // keď je všetko OK, zavrieš modal:
      this.closeDeleteModal();
      // ... a môžeš obnoviť zoznam, refreshnuť atď.
    },
  },
  mounted() {
    console.log("Vypisujem dáta produktu čo som dostal :>> ", this.item);

    this.nacitajObjednavku();
  },
  props: {
    item: {
      type: Object,
      default: () => ({}),
      required: true,
    },
  },
};
</script>

<style lang="scss" scoped>
.jedna {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 0 auto;
  gap: 1%;
  padding: 1em 0;
}

.jedna-objednavka {
  transition-duration: 0.2s;
}

.jedna-objednavka:hover {
  background: rgba($color: #000000, $alpha: 0.01);
}

.jedna-objednavka:has(.more-info) {
  background: rgba(254, 243, 90, 0.5);
  margin-top: 1em;
  padding: 1em 2em 0em;
  border-radius: 2rem;
  transform: scale(1.02);
}

.jedna > .info:first-child {
  width: 16%;
}

.info {
  padding: 0.1em;
  font-size: 1.3em;
  width: 22%;
}

.info-email {
  width: 35%;
  font-size: 1.2em;
}

img {
  width: 1.8em;
}

.button {
  font-size: 0.9em;
}

.information .button {
  height: max-content;
  margin: auto 0 auto auto;
}

.line {
  height: 0.35rem;
}

.more-info {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  justify-content: space-between;
  padding: 0.5em 2% 1.5em;
  width: 96%;
  border-top: 0.2em solid;
}

.riadok .only-info {
  line-height: 1.8em;
}

.only-info {
  font-size: 1em;
  min-width: 5em;
  max-width: 28em;
  text-align: left;
}

.cena {
  min-width: 5em;
}

.border-top {
  border-top: 0.13em solid #000;
}

.kusy {
  text-align: center;
}

.nadpis {
  font-size: 1.2em;
  line-height: 1.7em;
  font-weight: 800;
}

.ks {
  margin-left: auto;
  margin-right: 1em;
  min-width: 3em;
}

.riadok {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.box {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  justify-content: flex-start;
  margin: 0.5em 0;
}

.information {
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
  width: 35%;
  gap: 1em;
  justify-content: space-between;
  margin: 0 0.2em 1em auto;

  .only-info {
    max-width: 8em;
  }
}

.tovar {
  width: 60%;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.33);
  z-index: 1000;
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.25s;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

.modal-content {
  background: #fff;
  padding: 2.2em 2.7em 1.5em 2.7em;
  border-radius: 1em;
  box-shadow: 0 8px 40px 0 #0002;
  text-align: center;
  min-width: 280px;
  transform: translateY(-30px) scale(0.96);
  animation: modalPop 0.33s cubic-bezier(0.4, 1.5, 0.6, 1) forwards;
  opacity: 0;
}

@keyframes modalPop {
  from {
    opacity: 0;
    transform: translateY(-30px) scale(0.96);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-content h3 {
  font-family: "Bahnschrift" sans-serif;
  font-size: 2em;
  margin-bottom: 0.8em;
  color: #d11a2a;
  letter-spacing: -0.01em;
  text-shadow: 1px 1px 0 #fff7;
}

.modal-buttons {
  margin-top: 2em;
  display: flex;
  gap: 1.3em;
  justify-content: center;
}

.red-btn,
.gray-btn {
  padding: 0.7em 1.8em;
  font-size: 1.12em;
  font-family: inherit;
  border-radius: 0.5em;
  border: none;
  outline: none;
  cursor: pointer;
  transition: background 0.17s,
    transform 0.18s cubic-bezier(0.39, 1.6, 0.7, 1.15), box-shadow 0.18s;
  box-shadow: 0 2px 14px 0 #0001;
}

.red-btn {
  background: #d11a2a;
  color: #fff;
}

.red-btn:hover,
.red-btn:focus {
  background: #b10019;
  transform: scale(1.08);
  box-shadow: 0 4px 24px 0 #d11a2a33;
}

.gray-btn {
  background: #ededed;
  color: #2b2b2b;
}

.gray-btn:hover,
.gray-btn:focus {
  background: #ccc;
  transform: scale(1.08);
  box-shadow: 0 4px 24px 0 #aaaaaa33;
}

.delete-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 0.5em;
  margin-right: 0.2em;
  cursor: pointer;
  transition: filter 0.15s;

  img {
    width: 1.2em; // alebo aj 1.5em podľa preferencie
    height: 1.2em;
    display: block;
    margin: 0 auto;
    transition: filter 0.15s, transform 0.18s;
  }

  &:hover img,
  &:focus img {
    filter: grayscale(0) drop-shadow(0 0 4px #e53e3e44);
    transform: scale(1.08) rotate(-12deg);
  }
}

@media only screen and (max-width: 1380px) {
  .jedna {
    font-size: 0.9em;
  }
}
</style>
