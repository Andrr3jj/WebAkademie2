<template>
  <section :id="$route.name">
    <form class="scroll" @submit.prevent="upraveniePouzivatela">
      <div class="column">
        <div class="row-form stav">
          <label for="stav"
            >Používateľ {{ pouzivatelData.name }}
            <strong>
              {{
                pouzivatelData.is_admin == 0
                  ? "je BEŽNÝ používateľ"
                  : "je administrátor"
              }}</strong
            ></label
          >
        </div>

        <div
          class="delete-user zrusit click"
          :class="{
            'not-have-permission':
              !this.$store.state.userRoles?.roles?.includes('user_master'),
          }"
          @click="vymazPouzivatela()"
        >
          <p>Vymaž používateľa</p>
          <img
            src="@/assets/images/icons/zrusit.svg"
            alt="Vymazať pouzivatela"
          />
        </div>

        <div class="row-form m-t">
          <label
            v-if="pouzivatelData.is_admin == 0"
            class="roles-label"
            for="roles"
            >Vytvoriť administrátora</label
          >
          <a
            v-if="pouzivatelData.is_admin == 0"
            @click="spravaAdministrator('vytvorenie')"
            class="button gift-button"
          >
            <p>Vytvor</p>
          </a>
          <label
            v-if="pouzivatelData.is_admin == 1"
            class="roles-label"
            for="roles"
            >Zrušiť administrátora</label
          >
          <a
            v-if="pouzivatelData.is_admin == 1"
            @click="spravaAdministrator('zmazanie')"
            class="button gift-button"
          >
            <p>Odstráň</p>
          </a>
        </div>

        <div v-if="pouzivatelData.is_admin" class="row-form m-t">
          <label class="roles-label" for="roles"
            >Vybratie ról pre admina:</label
          >
        </div>

        <div v-if="pouzivatelData.is_admin" class="row-form">
          <!-- Vygenerovanie checkboxov pre každú rolu -->
          <div
            class="row-roles"
            v-for="(role, index) in availableRoles"
            :key="role"
          >
            <label :for="role">{{ roleNames[role] }}</label>
            <input
              type="checkbox"
              :value="role"
              v-model="roleStates[index]"
              :id="role"
            />
          </div>
        </div>

        <a @click="saveRoles()" class="button gift-button roles-button">
          <p>Aktualizuj roly</p>
        </a>
      </div>
      <div class="line"></div>

      <div class="column">
        <div class="row-form">
          <label for="meno">Meno:</label>
          <input
            ref="meno"
            type="text"
            name="meno"
            placeholder="Meno heligonkára"
            v-model="pouzivatelData.name"
          />
        </div>
        <div class="row-form">
          <label for="priezvisko">Priezvizko:</label>
          <input
            ref="priezvisko"
            type="text"
            name="priezvisko"
            placeholder="Priezvisko heligonkára"
            v-model="pouzivatelData.surname"
          />
        </div>
        <div class="row-form">
          <label for="datum">Dátum narodenia:</label>
          <input
            ref="datum"
            type="date"
            name="datum"
            placeholder="dátum narodenia"
            v-model="pouzivatelData.dateOfBirth"
          />
        </div>
        <div class="row-form">
          <label for="mesto">Mesto:</label>
          <input
            ref="mesto"
            type="text"
            name="mesto"
            placeholder="Mesto/dedina"
            v-model="pouzivatelData.billingAddressCity"
          />
        </div>
        <div class="row-form">
          <label for="ulica">Ulica:</label>
          <input
            ref="ulica"
            type="text"
            name="ulica"
            placeholder="Ulica  "
            v-model="pouzivatelData.billingAddressStreet"
          />
        </div>
        <div class="row-form">
          <label for="cislo">Císlo domu:</label>
          <input
            ref="cislo"
            type="text"
            name="cislo"
            placeholder="číslo domu"
            v-model="pouzivatelData.billingAddressHouseNumber"
          />
        </div>
        <div class="row-form">
          <label for="mesto">PSČ:</label>
          <input
            ref="mesto"
            type="text"
            name="mesto"
            placeholder="poštové smerovacie číslo"
            v-model="pouzivatelData.billingAddressPostcode"
          />
        </div>

        <button
          :class="{
            'not-have-permission':
              !this.$store.state.userRoles?.roles?.includes('user_master'),
          }"
          type="submit"
          class="button"
        >
          <p>Upravenie používateľa</p>
        </button>
        <div @click="$router.go(-1)" class="zrusit click">
          <p><strong>←</strong> Vrátiť sa spať</p>
        </div>
      </div>

      <div class="line"></div>

      <div class="column">
        <div class="row-form m-t">
          <label for="nevlastnene-zapisy">Pridaj číselný zápis</label>
        </div>

        <div class="row-form">
          <select
            v-model="vybraniZapisDarovanieId"
            name="nevlastnene-zapisy"
            id="nevlastnene-zapisy"
          >
            <option value="" disabled selected>Darovateľné zápisy</option>
            <option
              v-for="zapis in pouzivatelNevlastenene"
              :key="zapis.id"
              :value="zapis.id"
            >
              {{ zapis.name }} &nbsp; ({{ zapis.id }})
            </option>
          </select>
        </div>

        <div class="row-form m-t">
          <label for="darovanie-predplatne">Daruj predplatné na:</label>
        </div>

        <div class="row-form">
          <select
            v-model="vypranePredplatneDarovanie"
            name="darovanie-predplatne"
            id="darovanie-predplatne"
          >
            <option value="" disabled selected>Doby predplatných</option>
            <option
              v-for="aPredplatne in predplatne"
              :key="aPredplatne"
              :value="aPredplatne"
            >
              {{ dobaPredplatneho(aPredplatne) }}
            </option>
          </select>
        </div>

        <a @click="darovat()" class="button gift-button">
          <p>Daruj 😊</p>
        </a>
      </div>
    </form>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);

    if (typeof this.$route.query.id !== "undefined") {
      this.nacitajPouzivatelskeUdaje();
      this.nacitajNevlastneneZapisy();
    } else {
      this.$store.state.SnackBarText = "Nepodarilo sa nájsť používateĺa";
      this.$router.go(-1);
    }
  },
  data() {
    return {
      pouzivatelData: {},
      pouzivatelNevlastenene: [],
      vybraniZapisDarovanieId: 0,
      predplatne: ["mesiac", "pol rok", "rok"],
      vypranePredplatneDarovanie: "",
      availableRoles: [
        "subscription_edit",
        "video_create",
        "video_edit",
        "video_delete",
        "numericRecord_create",
        "numericRecord_edit",
        "numericRecord_delete",
        "merch_create",
        "merch_edit",
        "merch_delete",
        "record_create",
        "record_edit",
        "record_delete",
        "mail_master",
        "coupon_master",
        "invoice_master",
        "product_pass",
        "user_master",
        "admin_master",
        "helishop_manager",
        "edupageRegistrationsManager",
      ],
      // Mapovanie názvov: originál -> užívateľsky priateľský názov
      roleNames: {
        subscription_edit: "Úprava predplatného",
        video_create: "Vytváranie videí",
        video_edit: "Úprava videí",
        video_delete: "Mazanie videí",
        numericRecord_create: "Tvorba číselných zápisov",
        numericRecord_edit: "Úprava číselných zápisov",
        numericRecord_delete: "Mazanie číselných zápisov",
        merch_create: "Tvorba tovaru",
        merch_edit: "Úprava tovaru",
        merch_delete: "Mazanie tovaru",
        record_create: "Tvorba zápisov editor",
        record_edit: "Úprava zápisov editor",
        record_delete: "Mazanie zápisov editor",
        mail_master: "Odosielanie e-mailov",
        coupon_master: "Správa kupónov",
        invoice_master: "Zobrazenie faktúr",
        product_pass: "Prístup k všetkym produktom",
        user_master: "Správa používateľov",
        admin_master: "Správa administrátorov",
        helishop_manager: "Správca helishopu",
        edupageRegistrationsManager: "Správa prihlášok",
      },
      roleStates: [],
    };
  },
  methods: {
    async spravaAdministrator(akcia) {
      var jeAdmin;
      if (akcia == "vytvorenie") {
        jeAdmin = true;
      } else {
        jeAdmin = false;
      }

      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          `/user/info/setAdminStatus.php?email=${this.pouzivatelData.email}&is_admin=${jeAdmin}`,
        headers: {},
      };

      try {
        const response = await axios.request(config);
        if (response.data.status == "Request succesfull") {
          if (jeAdmin == true) {
            this.$store.state.SnackBarText =
              this.pouzivatelData.name +
              " " +
              this.pouzivatelData.surname +
              " sa stal administrátorom, čo ale nič neznamená bez rolý. Dovoľ mu niečo robiť.";
          } else {
            this.$store.state.SnackBarText =
              this.pouzivatelData.name +
              " " +
              this.pouzivatelData.surname +
              " už nieje naďalej administrátorom, ešte že si ho odstránil nedopadlo by to dobre.";
          }
          this.nacitajPouzivatelskeUdaje();
        } else {
          this.$store.state.SnackBarText =
            this.pouzivatelData.name +
            " " +
            this.pouzivatelData.surname +
            " sa nestal administrátorom, skús nabudúce.";
          this.nacitajPouzivatelskeUdaje();
        }
      } catch (error) {
        (this.$store.state.SnackBarText =
          this.pouzivatelData.name +
          " " +
          this.pouzivatelData.surname +
          " sa nestal administrátorom, skús nabudúce."),
          error;
        this.nacitajPouzivatelskeUdaje();
      }
    },
    async saveRoles() {
      // Vytvoríme objekt obsahujúci len role so stavom true
      const selectedRoles = this.availableRoles.filter(
        (role, index) => this.roleStates[index]
      );

      // Objekt na odoslanie, datý do JSON a zmenené "" na ''
      const rolesToSend = JSON.stringify(selectedRoles);

      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      data.append("email", this.pouzivatelData.email);
      data.append("roles", rolesToSend);

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/user/info/setAdminRoles.php",
        data: data,
      };

      try {
        const response = await axios.request(config);
        if (response.data.status == "Request succesfull") {
          this.$store.state.SnackBarText =
            "Roly pre používateľa boli úspešne uložené";
          console.log("response.data :>> ", response.data);
          this.nacitajPouzivatelskeUdaje();
        } else {
          this.$store.state.SnackBarText =
            "Nepodarilo sa priradiť roly k tomuto používateľovi";
        }
      } catch (error) {
        console.log(error);
        this.$store.state.SnackBarText =
          "Nepodarilo sa priradiť roly k tomuto používateľovi";
      }
    },
    dobaPredplatneho(duration) {
      return duration == "mesiac"
        ? "Mesiac"
        : duration == "pol rok"
        ? "Pol rok"
        : duration == "rok"
        ? "Rok"
        : "";
    },
    async darovat() {
      const userId = this.$route.query.id; // Predpokladám, že ID používateľa máš v query parametri
      const productId = this.vybraniZapisDarovanieId; // ID vybratého zápisu z <select>
      const subscriptionId = this.vypranePredplatneDarovanie;

      if (productId) {
        await this.pridatZapis(userId, productId);
      }

      if (subscriptionId) {
        await this.pridatPredplatne(userId, subscriptionId);
      }
    },
    async pridatPredplatne(userId, duration) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          `/subscription/subcriptionFree.php?user_id=${userId}&duration=${duration}`,
        headers: {},
      };

      try {
        const response = await axios.request(config);
        if (response.data.status == "Request succesfull") {
          this.$store.state.SnackBarText =
            "Predplatné na " +
            this.dobaPredplatneho(duration) +
            " bolo pridané.";
          this.vypranePredplatneDarovanie = "";
        } else {
          this.$store.state.SnackBarText =
            "Náš heligonkár si poradí aj bez predplatného, skús ešte raz neskôr. ";
        }
      } catch (error) {
        (this.$store.state.SnackBarText = "Chyba pri pridávaní predplatného: "),
          error;
      }
    },
    async pridatZapis(userId, productId) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          `/product/addToOwnedFree.php?user_id=${userId}&product_id=${productId}`,
        headers: {},
      };

      try {
        const response = await axios.request(config);
        if (response.data.status == "Request succesfull") {
          this.$store.state.SnackBarText = "Zápis bol úspešne darovaný.";
          this.nacitajNevlastneneZapisy();
        } else {
          this.$store.state.SnackBarText =
            "Náš heligonkár neprijal zápis, skús ešte raz neskôr.";
        }
      } catch (error) {
        (this.$store.state.SnackBarText = "Chyba pri pridávaní zápisu: "),
          error;
      }
    },
    async nacitajNevlastneneZapisy() {
      this.pouzivatelNevlastenene = [];
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/user/stats/getZapisNotOwned.php?user_id=" +
          this.$route.query.id,
        data: data,
      };

      try {
        const response = await axios.request(config);
        const ids = response.data.data;

        // Vytvor základné štruktúrované dáta
        this.pouzivatelNevlastenene = ids.map((id) => ({
          id: parseInt(id),
          name: "",
        }));

        // Paralelné načítanie názvov produktov
        await Promise.all(
          this.pouzivatelNevlastenene.map(async (item) => {
            item.name = await this.nacitajNazovProduktu(item.id);
          })
        );
      } catch (error) {
        console.log(error);
      }
    },

    async nacitajNazovProduktu(id) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
        headers: {},
      };

      try {
        const response = await axios.request(config);
        return response.data.data.name; // Vráť názov produktu
      } catch (error) {
        console.log(error);
        return ""; // Vráť prázdny reťazec, ak požiadavka zlyhá
      }
    },

    nacitajPouzivatelskeUdaje() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/user/info/getAllInformationAboutUser.php/?id=" +
          this.$route.query.id,
        //   headers: {
        //     ...data.getHeaders()
        //   },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            console.log(JSON.stringify(response.data));
            this.pouzivatelData = response.data.data;
            // this.roleStates = response.data.data.roles;

            //**  Stiahnutie rolý a triedenie pre určité pozície  **/
            // Prichádzajúci string rolí, ktorý treba skonvertovať na pole
            const rolesString = response.data.data.roles; // "['role1','role2',...]"

            var validJsonString;

            if (rolesString != null) {
              validJsonString = rolesString.replace(/'/g, '"');
            } else {
              validJsonString = rolesString;
            }
            // Nahradíme jednoduché úvodzovky za dvojité, aby bol reťazec validný JSON

            // Prekonvertujeme na pole pomocou JSON.parse
            const customerRoles = JSON.parse(validJsonString);

            // Inicializujeme roleStates na základe dostupných rolí
            this.roleStates = this.availableRoles.map((role) =>
              customerRoles.includes(role)
            );
            console.log("this.pouzivatelData :>> ", this.pouzivatelData);
          } else {
            this.$store.state.SnackBarText = "Používateľ sa nenašiel";
            this.$router.go(-1);
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    upraveniePouzivatela() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      data.append("id", this.$route.query.id);

      //pridanie s tabulky
      data.append("name", this.pouzivatelData.name);
      data.append("surname", this.pouzivatelData.surname);
      data.append("dateOfBirth", this.pouzivatelData.dateOfBirth);

      //pridanie údajov pre zabránenie zmazaniu
      data.append(
        "billingAddressStreet",
        this.pouzivatelData.billingAddressStreet
      );
      data.append(
        "billingAddressHouseNumber",
        this.pouzivatelData.billingAddressHouseNumber
      );
      data.append("billingAddressCity", this.pouzivatelData.billingAddressCity);
      data.append(
        "billingAddressPostcode",
        this.pouzivatelData.billingAddressPostcode
      );

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/user/info/updateAllInformationAboutUser.php/",
        // headers: {
        //   ...data.getHeaders()
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.$store.state.SnackBarText = "Používateľ bol úspešne upravený";
            this.$router.go(-1);
          } else {
            this.$store.state.SnackBarText = "Používateľ sa neupravil";
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    vymazPouzivatela() {
      if (
        confirm(
          "Naozaj chcete vymazať používateľa, po vymazaní sa navždy stratí"
        )
      ) {
        const axios = require("axios");

        let config = {
          method: "get",
          maxBodyLength: Infinity,
          url:
            this.$store.state.api +
            "/user/delete.php/?email=" +
            this.pouzivatelData.email,
          headers: {},
        };

        axios
          .request(config)
          .then((response) => {
            if (response.data.status == "Request fullfiled") {
              this.$store.state.SnackBarText =
                "Používateľ bol úspešne vymazaný";
              this.$router.go(-1);
            } else {
              this.$store.state.SnackBarText =
                "Nepodarilo sa vymazať používateľa";
            }
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.scroll {
  width: 100%;
  max-width: unset;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  gap: 2%;
}
.column {
  height: auto;
  width: 35%;
  padding: 1%;
  box-sizing: border-box;
}

.row-form {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.m-t {
  margin-top: 1em;
}

.gift-button {
  width: max-content;
  margin: auto;
}

.column:first-child {
  width: 35%;
  padding-left: 3%;
  height: max-content;
}
.column:last-child {
  width: 30%;
  padding-right: 3%;
}

.row-form input,
.row-form textarea,
.row-form select {
  border-radius: 1.0625em;
  background: #90ca50;
  box-shadow: -7px 5px 15px 0px rgba(0, 0, 0, 0.25) inset,
    0px 4px 4px 0px rgba(0, 0, 0, 0.25);
  padding: 0.35em 5%;
  margin: 0.3em auto 1.3em;
  width: 90%;
}

label {
  font-size: 1.5em;
}

input,
select {
  font-size: 1.1em;
}

input::placeholder {
  color: #00000063;
}

.row-form textarea {
  font-size: 0.9em;
  padding: 1em 5%;
}

.row-form select {
  border: none;
  width: 100%;
  cursor: pointer;
}

/* Štýly pre custom input file */
.custom-file-input {
  display: none; /* Schovať natívny input file */
}

.stupnice .button,
.zvukova-stopa .button {
  display: inline-block;
  text-align: center;
  padding: 0.3em 1em 0.1em;
  font-size: 1em;
}

/* Štýly pre vybraný súbor */
.selected-file-info {
  margin-top: 1.5em;
  font-size: 0.6em;
  width: 70%;
  text-align: center;
  cursor: pointer;
}

.stupnice {
  display: flex;
  flex-wrap: wrap;
  flex-direction: row;
  gap: 5%;
  justify-content: center;
  font-size: 1vw;
}

.nazov-stupnice {
  font-size: 1.2em;
}

.stupnica,
.zvukova-stopa {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1em 0;
  width: 30%;
}

.zvukova-stopa {
  font-size: 1vw;
}

.zrusit {
  display: flex;
  gap: 3%;
  align-items: center;
  justify-content: center;
  font-size: 1.5em;

  img {
    width: 1em;
  }
}

button {
  margin: 1em auto;
  border-radius: 0.7em;
}

form > div:nth-child(1) > div:nth-child(6) {
  margin-top: 2em;
}

.checkbox {
  display: flex;
  flex-direction: row;

  input {
    width: auto;
    margin: auto 0.7em;
    /* font-size: 1em; */
    transform: scale(1.5);
    cursor: pointer;
  }

  label {
    font-size: 1.1em;
    font-weight: 100;
  }
}

.block-cursor {
  cursor: not-allowed;
}
.block-click {
  z-index: -1;
}

.row-roles {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  gap: 10%;
  width: 100%;
  line-height: 2em;
  padding: 0.5em;
  box-sizing: border-box;

  &:hover {
    background: #e6e6e6;
    border-radius: 0.5em;
    transition-duration: 0.2s;
    cursor: pointer;
    transform: scale(1.02);
  }

  input {
    width: auto;
    margin: auto 0;
  }

  label {
    text-align: left;
  }
}

.roles-label {
  margin: 2em auto 1em;
  font-size: 1.8em;
  font-weight: 600;
}
.stav {
  text-align: left;
  margin-bottom: 1em;
}

.roles-button {
  margin: 1em auto 2em;
}

.delete-user p {
  color: #c62f2f;
  font-weight: 800;
  font-size: 0.7em;
}

.delete-user {
  display: flex;
  justify-content: space-between;

  img {
    width: 0.8em;
    filter: invert(17%) sepia(83%) saturate(1987%) hue-rotate(340deg)
      brightness(125%) contrast(93%);
  }
}
</style>
