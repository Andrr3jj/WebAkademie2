<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Hromadné posielanie reklamných mailov</h5>

      <form @submit.prevent="spravaMailov">
        <div class="column">
          <div class="row-form">
            <label for="predmet">Predmet:</label>
            <input
              ref="predmet"
              type="text"
              name="predmet"
              placeholder="Text"
              v-model="predmet"
            />
          </div>

          <div class="row-form">
            <label for="mail-správa">Správa</label>
            <textarea
              name="mail-správa"
              v-model="mailText"
              cols="30"
              rows="10"
              placeholder="text správy ktorý sa zobrazí ak používateľ nemá k dispozícii html mail"
            ></textarea>
          </div>

          <div class="row-form">
            <label for="zvuk-piesne">Príloha:</label>
            <div class="zvukova-stopa">
              <label
                for="customFileT"
                class="button Adumu"
                @click="openFileInput()"
              >
                Pridať
              </label>
              <input
                type="file"
                id="customFileT"
                class="custom-file-input"
                ref="mailHTML"
                @change="handleFileSelect($event)"
                accept=".png"
              />
              <div
                @click="clearSelectedFile()"
                v-if="fileName != ''"
                class="selected-file-info"
              >
                {{ fileName }}
              </div>
            </div>
          </div>

          <p v-if="odosielaniePrebieha" style="text-align: center">
            Odosielajú sa emaily {{ odoslaneCount }}/{{ celkovyPocet }}
          </p>

          <button
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('mail_master'),
            }"
            type="submit"
            class="button"
          >
            <p>Odoslať</p>
          </button>

          <div @click="$router.go(-1)" class="zrusit click">
            <img src="@/assets/images/icons/zrusit.svg" alt="Zrušiť" />
            <p>Zrušiť</p>
          </div>
        </div>

        <div class="line"></div>

        <div class="column">
          <p class="nadpis">Naposledy odoslané maily</p>
          <ul class="historia-mailov">
            <li v-for="item in historiaMailov" :key="item.id" class="mail">
              <p>{{ item.timestamp }}</p>
              <p>{{ item.subject }}</p>
            </li>
          </ul>
        </div>
      </form>
    </div>
  </section>
</template>

<script>
export default {
  mounted() {
    // Po načítaní komponentu automaticky scrollne hore a načíta históriu mailov
    window.scrollTo(0, 0);
    this.nacitanieHistorieMailov();
  },
  data() {
    return {
      predmet: "",
      mailText: "",
      mailHTML: "", // base64 obrázok
      fileName: "", // názov vybraného obrázku
      historiaMailov: [],
      odosielaniePrebieha: false, // či aktuálne prebieha odosielanie
      odoslaneCount: 0, // koľko emailov už bolo poslaných
      celkovyPocet: 0, // celkový počet príjemcov
    };
  },
  methods: {
    // Otvorí input na výber obrázku
    openFileInput() {
      this.$refs.mailHTML.value = "";
    },

    // Spracuje výber súboru, načíta base64 reťazec
    handleFileSelect(event) {
      const file = event.target.files[0];
      this.fileName = file.name;
      const reader = new FileReader();
      reader.onload = () => {
        this.mailHTML = reader.result;
      };
      reader.readAsDataURL(file);
    },

    // Vymaže zvolený obrázok
    clearSelectedFile() {
      this.mailHTML = "";
      this.fileName = "";
    },

    // Validácia formulára
    spravaMailov() {
      if (this.mailHTML === "" || this.mailText === "") {
        this.$store.state.SnackBarText = "Vlož obrázok aj text správy";
        return;
      }
      if (this.predmet === "") {
        this.$store.state.SnackBarText = "Napíš predmet správy";
        return;
      }
      this.ulozenieMailu(); // Spustí odosielanie
    },

    // Uloží HTML šablónu mailu na server
    ulozenieMailu() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      const mailStruktura = `
    <!DOCTYPE html>
    <html><body>
      <img src="%image%" alt="Obrázok" style="max-width: 100%; height: auto;"/>
    </body></html>
  `;

      data.append("email_txt", this.mailText);
      data.append("email_html", mailStruktura);

      axios
        .post(this.$store.state.api + "/mail/adsEditEmail.php/", data)
        .then((response) => {
          if (response.data.status === "Request succesfull") {
            this.odoslanieMailov(); // spustí odosielanie na pozadí a presmeruje
          } else {
            this.$store.state.SnackBarText = "Nastala chyba pri úprave emailu";
          }
        })
        .catch((error) => {
          console.error(error);
          this.$store.state.SnackBarText = "Chyba pri ukladaní šablóny";
        });
    },

    // Hlavná funkcia na odosielanie emailov
    odoslanieMailov() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      data.append("subject", this.predmet);
      data.append("image", this.mailHTML);

      // ⏱ Voliteľne si môžeš zobraziť "odosielanie prebieha"
      this.odosielaniePrebieha = true;

      // 🔥 Pošli požiadavku bez čakania na odpoveď
      axios
        .post(this.$store.state.api + "/mail/adsSendEmail.php", data)
        .catch((err) => {
          console.error("Chyba pri spustení odosielania mailov:", err);
        });

      // ✅ Hneď pokračuj ďalej, nič nečakáš
      this.$store.state.SnackBarText = "Odosielanie mailov prebieha na pozadí";
      this.$router.push("/admin");
    },

    // Načíta históriu posledných hromadných emailov
    nacitanieHistorieMailov() {
      const axios = require("axios");
      axios
        .get(this.$store.state.api + "/mail/load.php")
        .then((response) => {
          if (response.data.status === "Request succesfull") {
            this.historiaMailov = response.data.data.map((item) => {
              let [d, t] = item.timestamp.split(" ");
              let newDate = d.split("-").reverse().join(".");
              let newTime = t.substring(0, 5);
              return { ...item, timestamp: `${newDate} ${newTime}` };
            });
          } else {
            this.historiaMailov = [
              { id: "0", timestamp: "20--0", subject: "Žiadne maily" },
            ];
          }
        })
        .catch(console.log);
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

h5 {
  text-align: center;

  font-size: 2.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%; /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

.nadpis {
  font-size: 2em;
  font-weight: 600;
  margin-bottom: 1em;
}

.mail {
  font-size: 1.3em;
  display: flex;
  justify-content: space-between;
}

.scroll {
  width: 100%;
  max-width: unset;
  display: flex;
  flex-direction: column;
  justify-content: space-evenly;
  gap: 2%;
}
.column {
  height: auto;
  width: 43%;
  padding: 1%;
  box-sizing: border-box;
}

form {
  display: flex;
  height: 80%;
  max-width: 100%;
  width: 100%;
  justify-content: space-evenly;
}

.row-form {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.column:first-child {
  padding-left: 3%;
}
.column:last-child {
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
</style>
