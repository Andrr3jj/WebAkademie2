<template>
  <section :id="$route.name">
    <form class="scroll" @submit.prevent="zapisanieZapisu">
      <div class="column">
        <div class="row-form">
          <label for="nazov-piesne">Názov videa:</label>
          <input
            ref="nazov"
            type="text"
            name="nazov-piesne"
            placeholder="Ako hrať na heligonke?"
            v-model="zapisyData.nazov"
            :class="{ 'block-click block-cursor': upravovanieZapisu }"
          />

          <div class="row-form">
            <label for="obtiaznost-piesne">Zaradenie do sekcie:</label>
            <select
              ref="obtiaznost"
              v-model="zapisyData.obtiaznost"
              name="obtiaznost-piesne"
            >
              <option value="1">Začiatočník</option>
              <option value="2">Stredne pokročilý</option>
              <option value="3">Pokročilý</option>
            </select>
          </div>

          <label for="dlzka-piesne">Dĺžka videa:</label>
          <input
            ref="dlzka"
            type="text"
            name="dlzka-piesne"
            placeholder="13:30"
            v-model="zapisyDetaily.dlzkaVidea"
          />
        </div>

        <div class="row-form">
          <label for="stupnica-piesne">Video:</label>
          <div class="stupnice">
            <div class="stupnica">
              <label
                for="video"
                class="button Adumu"
                @click="openFileInput('V')"
              >
                Pridať
              </label>
              <input
                type="file"
                id="video"
                class="custom-file-input"
                ref="fileInputV"
                @change="handleFileChange('V', $event)"
                accept=".mp4"
              />
              <!-- Zobrazovanie vybraného súboru -->
              <div
                @click="clearSelectedFile('V')"
                v-if="selectedFileNames.V"
                class="selected-file-info"
              >
                {{ selectedFileNames.V }}
              </div>
            </div>
          </div>
        </div>
        <div class="row-form">
          <label for="zvuk-piesne">Tit. obrázok:</label>
          <div class="zvukova-stopa">
            <label
              for="customFileT"
              class="button Adumu"
              @click="openFileInput('T')"
            >
              Pridať
            </label>
            <input
              type="file"
              id="customFileT"
              class="custom-file-input"
              ref="fileInputT"
              @change="handleFileChange('T', $event)"
              accept=".png"
            />
            <!-- Zobrazovanie vybraného súboru -->
            <div
              @click="clearSelectedFile('T')"
              v-if="selectedFileNames.T"
              class="selected-file-info"
            >
              {{ selectedFileNames.T }}
            </div>
          </div>
        </div>
      </div>

      <div class="line"></div>

      <div class="column">
        <div class="row-form">
          <label for="text-piesne">Text piesne</label>
          <textarea
            name="text-piesne"
            ref="text"
            v-model="zapisyDetaily.text"
            cols="30"
            rows="15"
          ></textarea>
        </div>
        <div class="row-form">
          <label for="info-piesne">Informácie k piesni</label>
          <textarea
            name="info-piesne"
            ref="info"
            v-model="zapisyDetaily.info"
            cols="30"
            rows="10"
          ></textarea>
        </div>

        <button type="submit" class="button">
          <p v-if="!odosielanie">
            {{ upravovanieZapisu ? "Upraviť video" : "Pridať video" }}
          </p>
          <p v-if="odosielanie">Nahrávanie</p>
        </button>
        <div v-if="!odosielanie" @click="$router.go(-1)" class="zrusit click">
          <img src="@/assets/images/icons/zrusit.svg" alt="Zrušiť" />
          <p>Zrušiť</p>
        </div>
        <div v-if="odosielanie" class="zrusit click">
          <p>Prosím neopúšťajte stránku ...</p>
        </div>
      </div>

      <div class="line"></div>

      <div class="column">
        <div
          :class="{
            'not-have-permission':
              !this.$store.state.userRoles?.roles?.includes('video_delete'),
          }"
          v-if="upravovanieZapisu"
          @click="vymazZapis(0)"
          class="zrusit click"
        >
          <p>Skry video</p>
          <img src="@/assets/images/icons/zrusit.svg" alt="Vymazať video" />
        </div>
      </div>
      <div class="column">
        <div
          :class="{
            'not-have-permission':
              !this.$store.state.userRoles?.roles?.includes('video_delete'),
          }"
          v-if="upravovanieZapisu"
          @click="vymazZapis(1)"
          class="zrusit click"
        >
          <p>Vymaž video</p>
          <img src="@/assets/images/icons/nespravne.svg" alt="Vymazať video" />
        </div>
      </div>
    </form>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);

    if (typeof this.$route.query.id !== "undefined") {
      this.upravovanieZapisu = true;

      this.nacitajDataZapisu();
    }
  },
  data() {
    return {
      selectedFileNames: {
        V: "",
        T: "",
      },
      zapisyData: {
        nazov: "",
        obtiaznost: "",
        cena: 1,
      },
      zapisyDetaily: {
        typ: "Video",
        text: "",
        info: "",
        dlzkaVidea: "",
      },
      selectedFiles: [],
      selectedFilesStupnica: [],
      selectedFilesSong: [],
      selectedFilesSongStupnica: [],
      odosielanie: false,
      upravovanieZapisu: false,
    };
  },
  methods: {
    zapisanieZapisu() {
      if (this.upravovanieZapisu) {
        this.upravenieZapisu();
      } else {
        this.vytvorenieZapisu();
      }
    },
    openFileInput(stupnica) {
      // Reset hodnoty príslušného inputu file

      if (stupnica == "T") {
        if (this.selectedFilesSongStupnica.indexOf(stupnica) != -1) {
          this.clearSelectedFile(stupnica);
        }
      } else {
        if (this.selectedFilesStupnica.indexOf(stupnica) != -1) {
          this.clearSelectedFile(stupnica);
        }
      }

      this.$refs[`fileInput${stupnica}`].value = "";
      // Otvorenie príslušného natívneho inputu file
      // this.$refs[`fileInput${stupnica}`].click();
    },
    // Vaša existujúca metóda handleFileChange by sa mala aktualizovať nasledovne
    handleFileChange(stupnica, event) {
      if (stupnica == "T") {
        this.selectedFilesSong.push(event.target.files[0]);
        this.selectedFilesSongStupnica.push(stupnica);
        this.selectedFileNames[stupnica] = event.target.files[0].name;
      } else {
        this.selectedFiles.push(event.target.files[0]);
        this.selectedFilesStupnica.push(stupnica);

        this.selectedFileNames[stupnica] = event.target.files[0].name;
      }

      console.log(this.selectedFiles);
      console.log(this.selectedFilesStupnica);
    },

    clearSelectedFile(stupnica) {
      this.selectedFileNames[stupnica] = "";

      if (stupnica == "T") {
        this.selectedFilesSong.pop();
        this.selectedFilesSongStupnica.pop();
      } else {
        this.selectedFiles.splice(
          this.selectedFilesStupnica.indexOf(stupnica),
          1
        );
        this.selectedFilesStupnica.splice(
          this.selectedFilesStupnica.indexOf(stupnica),
          1
        );
      }
      console.log(this.selectedFiles);
      console.log(this.selectedFilesStupnica);
    },
    vytvorenieZapisu() {
      if (this.podmienkyPreOdoslanie()) {
        this.odosielanie = true;
        console.log(this.odosielanie);

        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();
        if (
          this.selectedFiles.length == 0 ||
          this.selectedFilesSong.length == 0
        ) {
          this.$store.state.SnackBarText =
            "Pre pridanie videa je potrebné pridať video a titulný obrázok";
          this.odosielanie = false;
          return;
        }

        this.zapisyDetaily.text = this.zapisyDetaily.text.replaceAll(
          "\n",
          "//n"
        );
        this.zapisyDetaily.info = this.zapisyDetaily.info.replaceAll(
          "\n",
          "//n"
        );

        const zapisyDetaily = JSON.stringify(this.zapisyDetaily);
        var videoNazov = this.zapisyData.nazov
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase();

        data.append("virtuality", true);
        data.append("free", false);
        data.append("name", this.zapisyData.nazov);
        data.append("price", 1);
        data.append("difficulty", this.zapisyData.obtiaznost);
        data.append("expiration", "never");
        data.append("details", zapisyDetaily);
        data.append("data", "videos/" + videoNazov);

        for (let i = 0; i < this.selectedFiles.length; i++) {
          data.append(
            "data_files[]",
            this.selectedFiles[i],
            videoNazov + "-" + this.selectedFilesStupnica[i] + ".mp4"
          );
          console.log(
            "pridávanie súboru videa",
            videoNazov + "-" + this.selectedFilesStupnica[i] + ".mp4",
            this.selectedFiles[i]
          );
        }

        data.append(
          "details_files[]",
          this.selectedFilesSong[0],
          videoNazov + "-T" + ".png"
        );

        console.log("pridávanie titulného obrázku", this.selectedFilesSong[0]);

        console.log(JSON.stringify(data), data);
        let config = {
          method: "post",
          maxBodyLength: Infinity,
          url: this.$store.state.api + "/product/create.php/",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          data: data,
        };

        axios
          .request(config)
          .then((response) => {
            if (response.data.status == "Request fullfiled") {
              console.log(JSON.stringify(response.data));
              this.$store.state.SnackBarText = "Video bolo úspešne pridané";
              this.$router.push("/admin");
            } else {
              console.log(JSON.stringify(data), data);
              this.$store.state.SnackBarText = "Video sa nepodarilo pridať";
              this.odosielanie = false;
            }
          })
          .catch((error) => {
            console.log(error);
            this.$store.state.SnackBarText = "Video sa nepodarilo pridať";
            this.odosielanie = false;
          });
      }
    },
    upravenieZapisu() {
      if (this.podmienkyPreOdoslanie()) {
        this.odosielanie = true;
        console.log(this.odosielanie);

        const axios = require("axios");
        const zapisyData = require("form-data");
        if (this.selectedFileNames.V == "" || this.selectedFileNames.T == "") {
          this.$store.state.SnackBarText =
            "Pre pridanie videa je potrebné pridať video a titulný obrázok";
          this.odosielanie = false;
          return;
        }

        this.zapisyDetaily.text = this.zapisyDetaily.text.replaceAll(
          "\n",
          "//n"
        );
        this.zapisyDetaily.info = this.zapisyDetaily.info.replaceAll(
          "\n",
          "//n"
        );

        const zapisyDetaily = JSON.stringify(this.zapisyDetaily);
        var videoNazov = this.zapisyData.nazov
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase();

        let data = new zapisyData();
        data.append("id", this.$route.query.id);
        data.append("virtuality", true);
        data.append("name", this.zapisyData.nazov);
        data.append("price", 1);
        data.append("free", false);
        data.append("difficulty", this.zapisyData.obtiaznost);
        data.append("expiration", "never");
        data.append("details", zapisyDetaily);
        data.append("data", "videos/" + videoNazov);

        if (this.selectedFiles.length != 0) {
          for (let i = 0; i < this.selectedFiles.length; i++) {
            data.append(
              "data_files[]",
              this.selectedFiles[i],
              videoNazov + "-V.mp4"
            );
          }
        }
        if (this.selectedFilesSong.length != 0) {
          data.append(
            "details_files[]",
            this.selectedFilesSong[0],
            videoNazov + "-T" + ".png"
          );
        }

        let config = {
          method: "post",
          maxBodyLength: Infinity,
          url: this.$store.state.api + "/product/update.php/",
          headers: {
            "Content-Type": "multipart/form-data",
          },
          data: data,
        };

        axios
          .request(config)
          .then((response) => {
            console.log(JSON.stringify(response.data));
            this.$store.state.SnackBarText = "Video bolo úspešne upravené";
            this.$router.push("/admin");
          })
          .catch((error) => {
            console.log(error);
            this.$store.state.SnackBarText = "Video sa nepodarilo upraviť";
            this.odosielanie = false;
          });
      }
    },
    nacitajDataZapisu() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/loadLimited.php/?id=" +
          this.$route.query.id,
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          if (response.data.status == "Request succesfull") {
            var detailsData = response.data.data.details;

            // Odstránenie nových riadkov a tabulátorov zo zadaného reťazca
            let cleanedJsonString = detailsData.replace(/[\n\t]/g, "");

            // Parsovanie správneho JSON reťazca
            let jsonObject = JSON.parse(cleanedJsonString);

            detailsData = jsonObject;

            //nastavovanie údajov pre edit

            this.zapisyData.nazov = response.data.data.name;
            this.zapisyData.obtiaznost = response.data.data.difficulty;

            this.zapisyDetaily.dlzkaVidea = detailsData.dlzkaVidea;
            this.zapisyDetaily.text = detailsData.text.replaceAll("//n", "\n");
            this.zapisyDetaily.info = detailsData.info.replaceAll("//n", "\n");

            this.selectedFileNames.V =
              this.zapisyData.nazov
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/\s/g, "_")
                .toLowerCase() + "-V.mp4";

            this.selectedFileNames.T =
              this.zapisyData.nazov
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/\s/g, "_")
                .toLowerCase() + "-T.png";
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    vymazZapis(parameterArg) {
      if (confirm("Ste si istý že chcete vymazať video?")) {
        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();
        let parameter = "";

        if (parameterArg == 1) {
          parameter = "&permanent=true";
        }

        let config = {
          method: "get",
          maxBodyLength: Infinity,
          url:
            this.$store.state.api +
            "/product/delete.php/?id=" +
            this.$route.query.id +
            parameter,
          // headers: {
          //   ...data.getHeaders(),
          // },
          data: data,
        };

        axios
          .request(config)
          .then((response) => {
            console.log(JSON.stringify(response.data));
            if (response.data.status == "Request fullfiled") {
              this.$store.state.SnackBarText =
                "Video s názvom " +
                this.zapisyData.nazov +
                " bolo naždy vymazané";

              this.$router.go(-1);
            } else {
              this.$store.state.SnackBarText =
                "Video: " + this.zapisyData.nazov + " sa nepodarilo vymazať";
            }
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    podmienkyPreOdoslanie() {
      if (
        this.zapisyData.nazov.trim() !== "" &&
        this.zapisyData.obtiaznost.trim() !== ""
      ) {
        return true;
      } else {
        this.$store.state.SnackBarText =
          "Nepodarilo sa vytvoriť video, pre odoslanie vyplňťe všetky polia";
        return false;
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

.column:first-child {
  width: 35%;
  padding-left: 3%;
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
</style>
