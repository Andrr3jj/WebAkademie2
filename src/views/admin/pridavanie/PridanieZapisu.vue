<template>
  <section :id="$route.name">
    <form class="scroll" @submit.prevent="zapisanieZapisu">
      <div class="column">
        <div :class="{ 'block-cursor': upravovanieZapisu }" class="row-form">
          <label for="nazov-piesne">Názov piesne:</label>
          <input
            ref="nazov"
            type="text"
            name="nazov-piesne"
            placeholder="Na orave dobre..."
            v-model="zapisyData.nazov"
            :class="{ 'block-click': upravovanieZapisu }"
          />
        </div>
        <div class="row-form">
          <label for="autor">Číselný zápis napísal:</label>
          <input
            ref="autor"
            v-model="zapisyDetaily.autor"
            type="text"
            name="autor"
            placeholder="Andrej Trnka"
          />
        </div>
        <div class="row-form">
          <label for="tempo-piesne">Tempo piesne:</label>
          <input
            ref="tempo"
            v-model="zapisyDetaily.tempo"
            type="text"
            name="tempo-piesne"
            placeholder="Pomalé"
          />
        </div>
        <div class="row-form">
          <label for="zaner-piesne">Žáner piesne:</label>
          <input
            ref="zaner"
            v-model="zapisyDetaily.zaner"
            type="text"
            name="zaner-piesne"
            placeholder="Ľudová - Valčík"
          />
        </div>

        <div
          v-if="vyber != null || vyber == 'pdf' || vyber == 'prepojenie'"
          class="row-form vyber"
        >
          <label>Vyber prepojenie alebo PDF</label>

          <div class="checkbox">
            <div class="checkbox">
              <input
                v-model="vyber"
                type="radio"
                name="vyberMoznost"
                value="pdf"
              />
              <label for="vyberPdf">PDF</label>
            </div>
            <input
              v-model="vyber"
              type="radio"
              name="vyberMoznost"
              value="prepojenie"
            />
            <label for="vyberPrepojenie">Prepojenie</label>
          </div>
        </div>

        <div v-if="vyber == 'pdf' || vyber == null" class="row-form">
          <label for="stupnica-piesne">Stupnica:</label>
          <div class="stupnice">
            <div class="stupnica">
              <p class="nazov-stupnice">F - Dur</p>
              <label
                for="customFileF"
                class="button Adumu"
                @click="openFileInput('F')"
              >
                Pridať
              </label>
              <input
                type="file"
                id="customFileF"
                class="custom-file-input"
                ref="fileInputF"
                @change="handleFileChange('F', $event)"
                accept=".pdf"
              />
              <!-- Zobrazovanie vybraného súboru -->
              <div
                @click="clearSelectedFile('F')"
                v-if="selectedFileNames.F"
                class="selected-file-info"
              >
                {{ selectedFileNames.F }}
              </div>
            </div>
            <div class="stupnica">
              <p class="nazov-stupnice">C - Dur</p>
              <label
                for="customFileC"
                class="button Adumu"
                @click="openFileInput('C')"
              >
                Pridať
              </label>
              <input
                type="file"
                id="customFileC"
                class="custom-file-input"
                ref="fileInputC"
                @change="handleFileChange('C', $event)"
                accept=".pdf"
              />
              <!-- Zobrazovanie vybraného súboru -->
              <div
                @click="clearSelectedFile('C')"
                v-if="selectedFileNames.C"
                class="selected-file-info"
              >
                {{ selectedFileNames.C }}
              </div>
            </div>
            <div class="stupnica">
              <p class="nazov-stupnice">G - Dur</p>
              <label
                for="customFileG"
                class="button Adumu"
                @click="openFileInput('G')"
              >
                Pridať
              </label>
              <input
                type="file"
                id="customFileG"
                class="custom-file-input"
                ref="fileInputG"
                @change="handleFileChange('G', $event)"
                accept=".pdf"
              />
              <!-- Zobrazovanie vybraného súboru -->
              <div
                @click="clearSelectedFile('G')"
                v-if="selectedFileNames.G"
                class="selected-file-info"
              >
                {{ selectedFileNames.G }}
              </div>
            </div>
            <div class="stupnica">
              <p class="nazov-stupnice">D - Mol</p>
              <label
                for="customFileD"
                class="button Adumu"
                @click="openFileInput('D')"
              >
                Pridať
              </label>
              <input
                type="file"
                id="customFileD"
                class="custom-file-input"
                ref="fileInputD"
                @change="handleFileChange('D', $event)"
                accept=".pdf"
              />
              <!-- Zobrazovanie vybraného súboru -->
              <div
                @click="clearSelectedFile('D')"
                v-if="selectedFileNames.D"
                class="selected-file-info"
              >
                {{ selectedFileNames.D }}
              </div>
            </div>
            <div class="stupnica">
              <p class="nazov-stupnice">A - Mol</p>
              <label
                for="customFileA"
                class="button Adumu"
                @click="openFileInput('A')"
              >
                Pridať
              </label>
              <input
                type="file"
                id="customFileA"
                class="custom-file-input"
                ref="fileInputA"
                @change="handleFileChange('A', $event)"
                accept=".pdf"
              />
              <!-- Zobrazovanie vybraného súboru -->
              <div
                @click="clearSelectedFile('A')"
                v-if="selectedFileNames.A"
                class="selected-file-info"
              >
                {{ selectedFileNames.A }}
              </div>
            </div>
            <div class="stupnica">
              <p class="nazov-stupnice">B - Dur</p>
              <label
                for="customFileB"
                class="button Adumu"
                @click="openFileInput('B')"
              >
                Pridať
              </label>
              <input
                type="file"
                id="customFileB"
                class="custom-file-input"
                ref="fileInputB"
                @change="handleFileChange('B', $event)"
                accept=".pdf"
              />
              <!-- Zobrazovanie vybraného súboru -->
              <div
                @click="clearSelectedFile('B')"
                v-if="selectedFileNames.B"
                class="selected-file-info"
              >
                {{ selectedFileNames.B }}
              </div>
            </div>
          </div>
        </div>
        <div v-if="vyber == 'prepojenie'" class="row-form">
          <label for="prepojenie">Prepojenie:</label>

          <div
            v-for="(selected, index) in vybraneZapisy"
            :key="index"
            class="row-form"
          >
            <select
              v-model="vybraneZapisy[index]"
              @change="pridajSelect(index)"
            >
              <option value="">-- Číselný zápis --</option>
              <option
                v-for="noty in zoradenyEditorZoznam"
                :key="noty.id"
                :value="noty.id"
              >
                {{ noty.nazov }} | {{ noty.autor }} ({{ noty?.stupnica }})
              </option>
            </select>
          </div>
        </div>
        <div class="row-form">
          <label for="zvuk-piesne">Zvuková stopa:</label>
          <div class="zvukova-stopa">
            <label
              for="customFileZ"
              class="button Adumu"
              @click="openFileInput('Z')"
            >
              Pridať
            </label>
            <input
              type="file"
              id="customFileZ"
              class="custom-file-input"
              ref="fileInputZ"
              @change="handleFileChange('Z', $event)"
              accept=".mp3"
            />
            <!-- Zobrazovanie vybraného súboru -->
            <div
              @click="clearSelectedFile('Z')"
              v-if="selectedFileNames.Z"
              class="selected-file-info"
            >
              {{ selectedFileNames.Z }}
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
            {{ upravovanieZapisu ? "Upraviť pieseň" : "Pridať pieseň" }}
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
              !this.$store.state.userRoles?.roles?.includes(
                'numericRecord_delete'
              ),
          }"
          v-if="upravovanieZapisu"
          @click="vymazZapis(0)"
          class="zrusit click"
        >
          <p>Skry zápis</p>
          <img src="@/assets/images/icons/zrusit.svg" alt="Skry zapis" />
        </div>
        <div
          :class="{
            'not-have-permission':
              !this.$store.state.userRoles?.roles?.includes(
                'numericRecord_delete'
              ),
          }"
          v-if="upravovanieZapisu"
          @click="vymazZapis(1)"
          class="zrusit click"
        >
          <p>Vymaž zápis</p>
          <img src="@/assets/images/icons/nespravne.svg" alt="Skry zapis" />
        </div>
        <div class="row-form">
          <label for="obtiaznost-piesne">Obtiažnosť piesne:</label>
          <select
            ref="obtiaznost"
            v-model="zapisyData.obtiaznost"
            name="obtiaznost-piesne"
          >
            <option value="1">Veľmi ľahká</option>
            <option value="2">Ľahká</option>
            <option value="3">Stredne ťažká</option>
            <option value="4">Ťažká</option>
            <option value="5">Veľmi ťažká</option>
            <option value="6">Expert</option>
          </select>
        </div>
        <div class="row-form">
          <label for="urcene-pre">Určené pre:</label>
          <select
            ref="urcenePre"
            v-model="zapisyDetaily.urcenePre"
            name="urcene-pre"
          >
            <option value="dvojradova-heligonka">Dvojradovú heligónku</option>
            <option value="trojradova-heligonka">Trojradovú heligónku</option>
          </select>
        </div>
        <div class="row-form">
          <label for="zaradenie">Zaradenie:</label>
          <select
            ref="zaradenie"
            v-model="zapisyData.zaradenie"
            name="zaradenie"
          >
            <option value="polka">Polka</option>
            <option value="valcik">Valčík</option>
            <option value="ceska">Česká</option>
            <option value="terchovska">Terchovská</option>
            <option value="moderna">Moderná</option>
            <option value="vianocna">Vianočná</option>
          </select>
        </div>

        <div class="row-form">
          <label for="statistikakategoria">Typ:</label>
          <select
            ref="statistikakategoria"
            v-model="zapisyDetaily.statistikakategoria"
            name="statistikakategoria"
          >
            <option value="polka">Polka</option>
            <option value="valcik">Valčík</option>
          </select>
        </div>

        <div class="row-form">
          <label for="cena-piesne">Cena piesne EUR:</label>
          <input
            ref="cena"
            v-model="zapisyData.cena"
            type="text"
            name="cena-piesne"
            placeholder="2.00"
          />
        </div>
        <div class="row-form">
          <label for="novinka-piesne">Novinka (deň):</label>
          <input
            ref="novinka"
            v-model="zapisyData.novinka"
            type="text"
            name="novinka-piesne"
            placeholder="10"
          />
        </div>

        <div class="row-form checkbox">
          <input
            ref="registracnaPesnickaCheckbox"
            v-model="zapisyData.registracnaPesnicka"
            type="checkbox"
            name="registracna-pesnicka"
          />
          <label for="registracna-pesnicka">Registračná pesnička:</label>
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
      this.productID = this.$route.query.id;
    }
    this.nacitajInfoEditorZapisy();
  },
  data() {
    return {
      selectedFileNames: {
        F: "",
        C: "",
        G: "",
        D: "",
        A: "",
        B: "",
        Z: "",
      },
      zapisyData: {
        nazov: "",
        obtiaznost: "",
        zaradenie: "",
        cena: "",
        novinka: "10",
        registracnaPesnicka: false,
      },
      zapisyDetaily: {
        typ: "Zapis",
        autor: "",
        tempo: "",
        zaner: "",
        text: "",
        info: "",
        urcenePre: "",
        statistikakategoria: "",
        stupnice: [],
      },
      selectedFiles: [],
      selectedFilesStupnica: [],
      stupnicePrepojenie: [],
      selectedFilesSong: [],
      selectedFilesSongStupnica: [],
      odosielanie: false,
      upravovanieZapisu: false,
      vyber: null,
      vybraneZapisy: [""],
      vybraneZapisyPred: [""],
      editorZoznam: [],
      productID: 0,
    };
  },
  computed: {
    zoradenyEditorZoznam() {
      return [...this.editorZoznam].sort((a, b) =>
        a.nazov.localeCompare(b.nazov)
      );
    },
  },
  methods: {
    nacitajInfoEditorZapisy() {
      //táto metóda slúži na priradenie not k produktu
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/list.php",
        // headers: {
        //   "Content-Type": "multipart/form-data",
        // },
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request succesfull") {
            //poznámka
            this.editorZoznam = response.data.data;
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Nepodarilo sa načítať editor";
        });
    },
    pridajSelect(index) {
      // Ak sa posledný select nevybral ako defaultný, pridá sa nový
      if (
        this.vybraneZapisy[index] !== "" &&
        index === this.vybraneZapisy.length - 1
      ) {
        this.vybraneZapisy.push("");
      }

      // Ak sa posledný vybral ako defaultný, odstráni ho
      while (
        this.vybraneZapisy[this.vybraneZapisy.length - 1] === "" &&
        this.vybraneZapisy[this.vybraneZapisy.length - 2] === ""
      ) {
        this.vybraneZapisy.pop();
      }

      const stupnice = this.editorZoznam
        .filter((zapis) => this.vybraneZapisy.includes(zapis.id)) // Vyber len tie, ktorých id je vo vybraneZapisy
        .map((zapis) => (zapis.stupnica ? zapis.stupnica.charAt(0) : "")); // Ulož len prvý znak stupnice

      console.log("stupnice :>> ", stupnice);
      this.stupnicePrepojenie = stupnice;
    },
    zapisanieZapisu() {
      if (this.upravovanieZapisu) {
        this.upravenieZapisu();
      } else {
        this.vytvorenieZapisu();
      }
    },
    openFileInput(stupnica) {
      // Reset hodnoty príslušného inputu file

      if (stupnica == "Z") {
        if (this.selectedFilesSongStupnica.indexOf(stupnica) != -1) {
          this.clearSelectedFile(stupnica);
        }
      } else {
        if (this.selectedFilesStupnica.indexOf(stupnica) != -1) {
          this.clearSelectedFile(stupnica);
        }
      }

      this.$refs[`fileInput${stupnica}`].value = "";
      this.zapisyDetaily.stupnice.push(stupnica);
      // Otvorenie príslušného natívneho inputu file
      // this.$refs[`fileInput${stupnica}`].click();
    },
    // Vaša existujúca metóda handleFileChange by sa mala aktualizovať nasledovne
    handleFileChange(stupnica, event) {
      if (stupnica == "Z") {
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

      if (this.upravovanieZapisu) {
        this.zapisyDetaily.stupnice = this.zapisyDetaily.stupnice.filter(
          (item) => item !== stupnica
        );
      }

      if (stupnica == "Z") {
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

        const axios = require("axios");
        const zapisyData = require("form-data");

        if (this.selectedFiles.length == 0 && this.vyber == "pdf") {
          this.$store.state.SnackBarText =
            "Pre pridanie číselného zápisu je potrebné pridať aspoň jeden zápis";
          this.odosielanie = false;
          return;
        }

        if (this.selectedFilesSong.length == 0) {
          this.$store.state.SnackBarText =
            "Pre pridanie číselného zápisu je potrebné pridať zvukovú ukážku";
          this.odosielanie = false;
          return;
        }

        this.zapisyDetaily.stupnice = this.selectedFilesStupnica;

        console.log("zapisyDetaily ", this.zapisyDetaily);
        this.zapisyDetaily.text = this.zapisyDetaily.text.replaceAll(
          "\n",
          "//n"
        );
        this.zapisyDetaily.info = this.zapisyDetaily.info.replaceAll(
          "\n",
          "//n"
        );
        console.log("zapisyDetaily ", this.zapisyDetaily);

        let zapisyDetailyJSON = JSON.stringify(this.zapisyDetaily);
        console.log("zapisyDetaily ", zapisyDetailyJSON);
        // zapisyDetailyJSON = zapisyDetailyJSON.replaceAll("\n", "//n");

        console.log("zapisyDetailyJSON ", zapisyDetailyJSON);

        var zapisyNazov = this.zapisyData.nazov
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase();

        //orpavenie ceny na bodku pre databázu
        if (this.zapisyData.cena.includes(",")) {
          this.zapisyData.cena = this.zapisyData.cena.replace(",", ".");
        }

        let data = new zapisyData();
        data.append("virtuality", true);
        data.append("free", this.zapisyData.registracnaPesnicka);
        data.append("new", this.zapisyData.novinka * 86400);
        data.append("category", this.zapisyData.zaradenie);
        data.append("name", this.zapisyData.nazov);
        data.append("difficulty", this.zapisyData.obtiaznost);
        data.append("expiration", "never");
        data.append("price", this.zapisyData.cena);
        data.append("details", zapisyDetailyJSON);
        data.append("data", "notes/"); //má končiť notes/

        if (this.vyber == "pdf") {
          for (let i = 0; i < this.selectedFiles.length; i++) {
            data.append(
              "data_files[]",
              this.selectedFiles[i],
              zapisyNazov + "-" + this.selectedFilesStupnica[i] + ".pdf"
            );
          }
        }

        if (this.selectedFilesSong.length != 0) {
          data.append(
            "details_files[]",
            this.selectedFilesSong[0],
            zapisyNazov + "-Z" + ".mp3"
          );
        }

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
            console.log(JSON.stringify(response.data));
            if (response.data.status == "Request fullfiled") {
              this.$store.state.SnackBarText =
                "Číselný zápis bol úspešne pridaný";
              if (this.vyber == "prepojenie") {
                for (let i = 0; i < this.vybraneZapisy.length; i++) {
                  this.stiahnutieNot(this.vybraneZapisy[i]);
                }
              }
              this.nacitanieUpravyPoVytvoreni();
            } else {
              this.$store.state.SnackBarTex =
                "Číselný zápis sa nepodarilo pridať";
              this.odosielanie = false;
            }
          })
          .catch((error) => {
            console.log(error);
            this.$store.state.SnackBarText =
              "Číselný zápis sa nepodarilo pridať";
            this.odosielanie = false;
          });
      }
    },
    nacitanieUpravyPoVytvoreni() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/stats/listZapis.php",
        // headers: {
        //   ...data.getHeaders()
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          const id = response.data.data[response.data.data.length - 1];
          this.$router.push({
            path: "/admin/pridanie-zapisu",
            query: { id: id.id },
          });
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarTex =
            "Nepodarilo sa presmerovať na úpravu zápisu";
        });
    },
    stiahnutieNot(id) {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/load.php?id=" + id,
        // headers: {
        //   ...data.getHeaders()
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request succesfull") {
            const zapisData = {
              id: response.data.data.id,
              data: JSON.parse(response.data.data.data),
              nazov: response.data.data.nazov,
              autor: response.data.data.autor,
              stupnica: response.data.data.stupnica,
              pocet_casti: response.data.data.pocet_casti,
              doby: response.data.data.pocet_dob,
              riadky: response.data.data.pocet_riadkov,
              text: response.data.data.text_piesne,
              pata: response.data.data.pata,
            };

            this.upravitPrepojenieNot(zapisData);
          } else {
            this.$store.state.SnackBarTex =
              "Pokazilo sa sťahovanie dát číselných zápisov pre prepojenie";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarTex =
            "Pokazilo sa sťahovanie dát číselných zápisov pre prepojenie";
        });
    },
    upravitPrepojenieNot(zapisData) {
      //táto metóda slúži na priradenie not k produktu
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      // Pridaj dáta do FormData
      data.append("id", Number(zapisData.id));
      data.append("data", JSON.stringify(zapisData.data));
      data.append("nazov", zapisData.nazov || "Číselný zápis");
      data.append("autor", zapisData.autor || "Neznámy autor");
      data.append("stupnica", zapisData.stupnica || "C dur");
      data.append("pocet_casti", zapisData.pocet_casti);
      data.append("pocet_dob", zapisData.doby);
      data.append("pocet_riadkov", zapisData.riadky);
      data.append("text_piesne", zapisData.text || "");
      data.append("pata", zapisData.pata || "Heligónková akadémia");
      data.append("product_id", this.productID);
      if (
        (this.vybraneZapisyPred.includes(zapisData.id) &&
          !this.vybraneZapisy.includes(zapisData.id)) ||
        (this.vyber == "pdf" && this.vybraneZapisy.length > 0)
      ) {
        data.append("product_id", 0);
      }

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/update.php",
        // headers: {
        //   "Content-Type": "multipart/form-data",
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request fullfiled") {
            //poznámka
          } else {
            this.$store.state.SnackBarText =
              "Nepodarilo sa prepojiť číslený zápis";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText =
            "Nepodarilo sa prepojiť číslený zápis";
        });
    },
    upravenieZapisu() {
      if (this.podmienkyPreOdoslanie()) {
        this.odosielanie = true;

        const axios = require("axios");
        const zapisyData = require("form-data");

        if (
          (this.selectedFileNames.length == 0 ||
            this.zapisyDetaily.stupnice.length == 0) &&
          (this.vyber == "pdf" || this.vyber == null)
        ) {
          this.$store.state.SnackBarText =
            "Pre upravenie číselného zápisu je potrebné pridať aspoň jeden zápis";
          this.odosielanie = false;
          return;
        }

        // this.zapisyDetaily.stupnice = this.selectedFilesStupnica;

        console.log("zapisyDetaily ", this.zapisyDetaily);
        this.zapisyDetaily.text = this.zapisyDetaily.text.replaceAll(
          "\n",
          "//n"
        );
        this.zapisyDetaily.info = this.zapisyDetaily.info.replaceAll(
          "\n",
          "//n"
        );
        console.log("zapisyDetaily ", this.zapisyDetaily);

        // zapisyDetailyJSON = zapisyDetailyJSON.replaceAll("\n", "//n");

        var zapisyNazov = this.zapisyData.nazov
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase();

        //orpavenie ceny na bodku pre databázu
        if (this.zapisyData.cena.includes(",")) {
          this.zapisyData.cena = this.zapisyData.cena.replace(",", ".");
        }

        let data = new zapisyData();
        data.append("id", this.$route.query.id);
        data.append("virtuality", true);
        data.append("free", this.zapisyData.registracnaPesnicka);
        data.append(
          "new",
          Math.floor((Date.now() + 4 * 24 * 60 * 60 * 1000) / 1000)
        );
        data.append("category", this.zapisyData.zaradenie);
        data.append("name", this.zapisyData.nazov);
        data.append("difficulty", this.zapisyData.obtiaznost);
        data.append("expiration", "never");
        data.append("price", this.zapisyData.cena);
        data.append("deleted", false);

        data.append("data", this.zapisyData.data);

        if (this.vyber == "pdf") {
          for (let i = 0; i < this.selectedFiles.length; i++) {
            data.append(
              "data_files[]",
              this.selectedFiles[i],
              zapisyNazov + "-" + this.selectedFilesStupnica[i] + ".pdf"
            );
          }
        }

        if (this.selectedFilesSong.length != 0) {
          data.append(
            "details_files[]",
            this.selectedFilesSong[0],
            zapisyNazov + "-Z" + ".mp3"
          );
        }

        // Najprv odstránenie existujúcich duplikátov v `stupnice`
        this.zapisyDetaily.stupnice = [...new Set(this.zapisyDetaily.stupnice)];

        let mergedValues = null;
        // Spojenie hodnôt
        if (this.vyber == "pdf") {
          mergedValues = [
            ...this.zapisyDetaily.stupnice,
            ...this.selectedFilesStupnica,
          ];
        } else if (this.vyber == "prepojenie") {
          mergedValues = this.stupnicePrepojenie.slice();
        }

        console.log("mergedValues :>> ", mergedValues);

        console.log("this.stupnicePrepojenie :>> ", this.stupnicePrepojenie);

        console.log(
          this.zapisyDetaily.stupnice,
          this.selectedFilesStupnica,
          mergedValues
        );

        // Odstránenie duplikátov
        //this.zapisyDetaily.stupnice = mergedValues;

        // Odstránenie duplikátov pomocou Set
        this.zapisyDetaily.stupnice = [...new Set(mergedValues)];

        let zapisyDetailyJSON = JSON.stringify(this.zapisyDetaily);
        console.log("zapisyDetaily ", zapisyDetailyJSON);

        data.append("details", zapisyDetailyJSON);

        console.log(this.zapisyDetaily.stupnice);

        console.log("data :>> ", data);

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
            if (response.data.status == "Request fullfiled") {
              this.$store.state.SnackBarText =
                "Číselný zápis bol úspešne upravený";
              if (this.vyber == "prepojenie") {
                // Získame položky, ktoré sú v array1, ale nie v array2 (jedinečné)
                var onlyInArray1 = [
                  ...new Set(
                    this.vybraneZapisy.filter(
                      (item) => !this.vybraneZapisyPred.includes(item)
                    )
                  ),
                ];

                // Získame položky, ktoré sú v array2, ale nie v array1 (všetky nové položky)
                var onlyInArray2 = [
                  ...new Set(
                    this.vybraneZapisyPred.filter(
                      (item) => !this.vybraneZapisy.includes(item)
                    )
                  ),
                ];

                for (let i = 0; i < onlyInArray1.length; i++) {
                  this.stiahnutieNot(onlyInArray1[i]);
                }
                for (let i = 0; i < onlyInArray2.length; i++) {
                  this.stiahnutieNot(onlyInArray2[i]);
                }
              } else if (this.vyber == "pdf" && this.vybraneZapisy.length > 0) {
                for (let i = 0; i < this.vybraneZapisy.length; i++) {
                  this.stiahnutieNot(this.vybraneZapisy[i]);
                }
              }

              this.odosielanie = false;
              // this.$router.push("/admin");
            } else {
              this.$store.state.SnackBarTex =
                "Číselný zápis sa nepodarilo upraviť";
              this.odosielanie = false;
            }
          })
          .catch((error) => {
            console.log(error);
            this.$store.state.SnackBarText =
              "Číselný zápis sa nepodarilo upraviť";
            this.odosielanie = false;
          });
      }
    },
    podmienkyPreOdoslanie() {
      if (
        this.zapisyData.nazov.trim() !== "" &&
        this.zapisyData.obtiaznost.trim() !== "" &&
        this.zapisyData.cena.trim() !== "" &&
        this.zapisyData.zaradenie.trim() !== "" &&
        this.zapisyDetaily.autor.trim() !== "" &&
        this.zapisyDetaily.tempo.trim() !== "" &&
        this.zapisyDetaily.zaner.trim() !== "" &&
        this.zapisyDetaily.urcenePre.trim() !== "" &&
        this.zapisyDetaily.typ.trim() !== ""
      ) {
        return true;
      } else {
        this.$store.state.SnackBarText =
          "Nepodarilo sa vytvoriť zápis, pre odoslanie vyplňťe všetky polia";
        return false;
      }
    },
    nacitajDataZapisu() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/load.php/?id=" +
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

            this.zapisyDetaily.autor = detailsData.autor;

            this.zapisyData.nazov = response.data.data.name;
            this.zapisyData.obtiaznost = response.data.data.difficulty;
            this.zapisyData.cena = response.data.data.price;
            this.zapisyData.zaradenie = response.data.data.category;
            this.zapisyData.novinka = 0;
            this.zapisyData.registracnaPesnicka = response.data.data.free;
            this.zapisyData.data = response.data.data.data;
            this.zapisyDetaily.autor = detailsData.autor;
            this.zapisyDetaily.tempo = detailsData.tempo;
            this.zapisyDetaily.zaner = detailsData.zaner;
            this.zapisyDetaily.urcenePre = detailsData.urcenePre;
            this.zapisyDetaily.typ = detailsData.typ;
            this.zapisyDetaily.text = detailsData.text.replaceAll("//n", "\n");
            this.zapisyDetaily.info = detailsData.info.replaceAll("//n", "\n");
            this.zapisyDetaily.statistikakategoria =
              detailsData.statistikakategoria;
            this.zapisyDetaily.stupnice = detailsData.stupnice;

            console.log(
              "this.zapisyDetaily.stupnice :>> ",
              this.zapisyDetaily.stupnice
            );

            var arrayList = ["F", "C", "G", "D", "A", "B"];

            for (let i = 0; i < arrayList.length; i++) {
              if (this.zapisyDetaily.stupnice.includes(arrayList[i])) {
                this.selectedFileNames[arrayList[i]] =
                  this.zapisyData.nazov
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/\s/g, "_")
                    .toLowerCase() +
                  "-" +
                  arrayList[i] +
                  ".pdf";
                console.log(
                  "object :>> ",
                  this.selectedFileNames[arrayList[i]],
                  arrayList[i],
                  this.selectedFileNames
                );
              }
            }

            this.selectedFileNames.Z =
              this.zapisyData.nazov
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "")
                .replace(/\s/g, "_")
                .toLowerCase() + ".mp3";

            if (response.data.data.ciselne_zapisy.length == 0) {
              this.vyber = "pdf";
            } else {
              this.vyber = "prepojenie";
              this.vybraneZapisy = response.data.data.ciselne_zapisy;
              //týmto vytváram kópiu daného pola a nie len odkaz
              this.vybraneZapisyPred = this.vybraneZapisy.slice();
              this.vybraneZapisy.push("");
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    vymazZapis(parameterArg) {
      if (confirm("Ste si istý že chcete stiahnuť s predaja číselný zápis?")) {
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
                "Číselný zápis s názvom " +
                this.zapisyData.nazov +
                " bol stiahnutý z predaja";

              this.$router.go(-1);
            } else {
              this.$store.state.SnackBarText =
                "Zápis: " +
                this.zapisyData.nazov +
                " sa nepodarilo osdtrániť z predaja";
            }
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },
    resetujStupnice(novaHodnota) {
      console.log("novaHOdnota :>> ", novaHodnota);
      this.stupnicePrepojenie = [];
      this.selectedFileNames = [];
      this.selectedFiles = [];
      this.vybraneZapisy = [""];
    },
  },
  watch: {
    vyber(novaHodnota, staraHodnota) {
      if (staraHodnota === null) {
        return; // Ak bola pôvodná hodnota null, nič nerob
      }
      this.resetujStupnice(novaHodnota);
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

.block-cursor {
  cursor: not-allowed;
}
.block-click {
  z-index: -1;
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

.vyber {
  gap: 0.5em;
  input {
    box-shadow: unset;
  }
}

label {
  text-align: left;
}
</style>
