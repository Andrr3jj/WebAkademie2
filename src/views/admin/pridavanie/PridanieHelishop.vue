<template>
  <section :id="$route.name">
    <form class="scroll" @submit.prevent="zapisanieZapisu">
      <div class="column">
        <div class="row-form">
          <label for="produkt-typ">Voľba produktu:</label>
          <select ref="produkt" v-model="typProduktu" name="produkt-typ">
            <option value="none" disabled selected>Typ</option>
            <option value="knizky">Knižky</option>
            <option value="vaky">Vaky</option>
            <option value="remene">Remene</option>
            <option value="oblecenie">Oblečenie</option>
            <option value="ostatne">Otatné</option>
          </select>
        </div>

        <div
          v-if="viditelnePolia.includes('vydanie-produktu')"
          class="row-form"
        >
          <label for="vydanie-produktu">Vydanie:</label>
          <input
            ref="vydanie"
            type="text"
            name="vydanie-produktu"
            placeholder="1"
            v-model="produktyDetaily.vydanie"
          />
        </div>
        <div v-if="viditelnePolia.includes('nazov-produktu')" class="row-form">
          <label for="nazov-produktu">Názov produktu:</label>
          <input
            ref="nazov"
            type="text"
            name="nazov-produktu"
            placeholder="Krátke tričko"
            v-model="produktyData.nazov"
          />
        </div>
        <div
          v-if="viditelnePolia.includes('podnadpis-produktu')"
          class="row-form"
        >
          <label for="podnadpis-produktu">Podnadpis produktu:</label>
          <input
            ref="podnadpis"
            type="text"
            name="podnadpis-produktu"
            placeholder="Výstižný popis produktu"
            v-model="produktyData.podnadpis"
          />
        </div>

        <div v-if="viditelnePolia.includes('popis-produktu')" class="row-form">
          <label for="popis-produktu">Popis:</label>
          <textarea
            name="popis-produktu"
            ref="popis"
            v-model="produktyDetaily.popis"
            cols="30"
            rows="10"
          ></textarea>
        </div>
        <div v-if="viditelnePolia.includes('opis-produktu')" class="row-form">
          <label for="opis-produktu">Opis produktu:</label>
          <textarea
            name="opis-produktu"
            ref="opis"
            v-model="produktyDetaily.opis"
            cols="30"
            rows="9"
          ></textarea>
        </div>
        <div
          v-if="viditelnePolia.includes('info-popis-produktu')"
          class="row-form"
        >
          <label for="info-popis-produktu">informácie k poukážke:</label>
          <textarea
            name="info-popis-produktu"
            ref="info-popis"
            v-model="produktyDetaily.infoPopis"
            cols="30"
            rows="9"
          ></textarea>
        </div>
        <div
          v-if="viditelnePolia.includes('co-sa-popis-produktu')"
          class="row-form"
        >
          <label for="co-sa-popis-produktu">Čo sa v knihe naučíš:</label>
          <textarea
            name="co-sa-popis-produktu"
            ref="co-sa-popis"
            v-model="produktyDetaily.coSaPopis"
            cols="30"
            rows="9"
          ></textarea>
        </div>
        <div
          v-if="viditelnePolia.includes('pre-koho-popis-produktu')"
          class="row-form"
        >
          <label for="pre-koho-popis-produktu">Pre koho je kniha určená:</label>
          <textarea
            name="pre-koho-popis-produktu"
            ref="pre-koho-popis"
            v-model="produktyDetaily.preKohoPopis"
            cols="30"
            rows="9"
          ></textarea>
        </div>
        <div
          v-if="viditelnePolia.includes('obsah-popis-produktu')"
          class="row-form"
        >
          <label for="obsah-popis-produktu">Obsah knižky:</label>
          <textarea
            name="obsah-popis-produktu"
            ref="obsah-popis"
            v-model="produktyDetaily.obsahPopis"
            cols="30"
            rows="9"
          ></textarea>
        </div>
        <div
          v-if="viditelnePolia.includes('technicky-popis-produktu')"
          class="row-form"
        >
          <label for="technicky-popis-produktu">Technické parametre:</label>
          <textarea
            name="technicky-popis-produktu"
            ref="technicky-popis"
            v-model="produktyDetaily.technickyPopis"
            cols="30"
            rows="9"
          ></textarea>
        </div>
        <div
          v-if="viditelnePolia.includes('zoznam-popis-produktu')"
          class="row-form"
        >
          <label for="zoznam-popis-produktu">Zoznam piesni:</label>
          <textarea
            name="zoznam-popis-produktu"
            ref="zoznam-popis"
            v-model="produktyDetaily.zoznamPopis"
            cols="30"
            rows="9"
          ></textarea>
        </div>
      </div>

      <div class="line"></div>

      <div class="column">
        <div v-if="viditelnePolia.includes('farba-produktu')" class="row-form">
          <label for="farba-produktu">Dostupná farba:</label>
          <div v-for="(farba, index) in produktyDetaily.farby" :key="index">
            <select
              v-model="produktyDetaily.farby[index]"
              @change="pridajNovySelect(index)"
              @contextmenu="odstranSelect(index, $event)"
            >
              <option disabled value="">Vyber farbu</option>
              <option
                v-for="moznost in farbyMoznosti"
                :key="moznost"
                :value="moznost"
              >
                {{ moznost }}
              </option>
            </select>
          </div>
        </div>
        <div v-if="viditelnePolia.includes('velkosti')" class="row-form">
          <label for="velkosti">Dostupné veľkosti:</label>
          <div class="size-options">
            <label v-for="size in ['XS', 'S', 'M', 'L', 'XL']" :key="size">
              <input
                type="checkbox"
                :value="size"
                v-model="produktyDetaily.velkosti"
              />
              <span>{{ size }}</span>
            </label>
          </div>
        </div>

        <div v-if="viditelnePolia.includes('cena-produktu')" class="row-form">
          <label for="cena-produktu">Cena produktu EUR:</label>
          <input
            ref="cena"
            type="text"
            name="cena-produktu"
            placeholder="5.99"
            v-model="produktyData.cena"
          />
        </div>

        <div v-if="viditelnePolia.includes('zlava-produktu')" class="row-form">
          <label for="zlava-produktu">Zľava %:</label>
          <input
            ref="zlava"
            type="text"
            name="zlava-produktu"
            placeholder="13"
            v-model="produktyData.zlava"
          />
        </div>

        <div
          v-if="viditelnePolia.includes('aktivovana-zlava-produktu')"
          class="row-form checkbox"
        >
          <input
            ref="aktivovana-zlava"
            v-model="produktyData.aktivovanaZlava"
            type="checkbox"
            name="aktivovana-zlava-produktu"
          />
          <label for="aktivovana-zlava">Aktivovať zľavu:</label>
        </div>

        <div v-if="viditelnePolia.includes('fotky-produktu')" class="row-form">
          <label for="fotky-produktu">Fotky produktu:</label>
          <div class="zvukova-stopa">
            <label for="fotky-produktu" class="button Adumu"> Pridať </label>
            <input
              type="file"
              id="fotky-produktu"
              class="custom-file-input"
              name="fotky-produktu"
              ref="fokty"
              @change="handleFileChange"
              accept="image/png"
              multiple
            />
            <!-- Zobrazovanie vybraných obrázkov -->
            <div class="photos">
              <div
                v-for="(item, index) in produktyData.images"
                :key="index"
                class="photo"
                :class="{ selected: index == 0 }"
              >
                <img
                  :src="item.src"
                  :alt="'Obrázok ' + (index + 1)"
                  @click="moveToFirst(index)"
                />
                <img
                  src="@/assets/images/icons/zrusit.svg"
                  alt="Vymazať"
                  class="delete"
                  @click="removePhoto(index)"
                />
              </div>
            </div>
          </div>
        </div>

        <button type="submit" class="button">
          <p v-if="!odosielanie">
            {{ upravovanieZapisu ? "Upraviť produkt" : "Pridať produkt" }}
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

      <div class="column">
        <div
          :class="{
            'not-have-permission':
              !this.$store.state.userRoles?.roles?.includes('merch_delete'),
          }"
          v-if="upravovanieZapisu"
          @click="vymazZapis(0)"
          class="zrusit click"
        >
          <p>Skryť produkt</p>
          <img src="@/assets/images/icons/zrusit.svg" alt="Vymazať produkt" />
        </div>
        <div
          :class="{
            'not-have-permission':
              !this.$store.state.userRoles?.roles?.includes('merch_delete'),
          }"
          v-if="upravovanieZapisu"
          @click="vymazZapis(1)"
          class="zrusit click"
        >
          <p>Vymazať produkt</p>
          <img
            src="@/assets/images/icons/nespravne.svg"
            alt="Vymazať produkt"
          />
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

      this.nacitajDataProduktu();
    }
  },
  data() {
    return {
      odosielanie: false,
      upravovanieZapisu: false,
      typProduktu: "none",
      produktyData: {
        nazov: "",
        podnadpis: "",
        cena: "",
        zlava: "",
        aktivovanaZlava: false,
        data: "",
        images: [],
      },
      produktyDetaily: {
        typ: "merch",
        popis: "",
        opis: "",
        farby: [""],
        velkosti: [],
        vydanie: "",
        infoPopis: "",
        coSaPopis: "",
        preKohoPopis: "",
        obsahPopis: "",
        technickyPopis: "",
        zoznamPopis: "",
      },
      farbyMoznosti: [
        "cierna", // čierna
        "biela", // biela
        "siva", // sivá
        "tmavomodra", // tmavomodrá
        "modra", // modrá
        "svetlomodra", // svetlomodrá
        "cervena", // červená
        "bordova", // bordová
        "tmavozelena", // tmavozelená
        "zelena", // zelená
        "olivova", // olivová
        "bezova", // béžová
        "hneda", // hnedá
        "horcicova", // horčicová
        "oranzova", // oranžová
        "zlta", // žltá
        "ruzova", // ružová
        "fialova", // fialová
        "tyrkysova", // tyrkysová
        "mentolova", // mentolová
        "zlatista", // zlatistá
        "strieborna", // strieborná
        "viacfarebna", // viacfarebná
        "maskacova", // maskáčová
        "neonova", // neónová
      ],
    };
  },
  computed: {
    /*
      Všetky možnosti:
      nazov-produktu
      vydanie-produktu
podnadpis-produktu
popis-produktu
opis-produktu
info-popis-produktu
co-sa-popis-produktu
pre-koho-popis-produktu
obsah-popis-produktu
technicky-popis-produktu
zoznam-popis-produktu
farba-produktu
velkosti
cena-produktu
zlava-produktu
aktivovana-zlava-produktu
fotky-produktu

    */
    viditelnePolia() {
      const konfiguracia = {
        knizky: [
          "nazov-produktu",
          "vydanie-produktu",
          "podnadpis-produktu",
          "popis-produktu",
          "co-sa-popis-produktu",
          "obsah-popis-produktu",
          "pre-koho-popis-produktu",
          "cena-produktu",
          "zlava-produktu",
          "aktivovana-zlava-produktu",
          "fotky-produktu",
        ],
        vaky: [
          "nazov-produktu",
          "podnadpis-produktu",
          "popis-produktu",
          "technicky-popis-produktu",
          "cena-produktu",
          "zlava-produktu",
          "aktivovana-zlava-produktu",
          "fotky-produktu",
        ],
        oblecenie: [
          "nazov-produktu",
          "podnadpis-produktu",
          "popis-produktu",
          "opis-produktu",
          "farba-produktu",
          "velkosti",
          "cena-produktu",
          "zlava-produktu",
          "aktivovana-zlava-produktu",
          "fotky-produktu",
        ],
        ostatne: [
          "nazov-produktu",
          "podnadpis-produktu",
          "popis-produktu",
          "opis-produktu",
          "info-popis-produktu",
          "co-sa-popis-produktu",
          "pre-koho-popis-produktu",
          "obsah-popis-produktu",
          "technicky-popis-produktu",
          "zoznam-popis-produktu",
          "farba-produktu",
          "velkosti",
          "cena-produktu",
          "zlava-produktu",
          "aktivovana-zlava-produktu",
          "fotky-produktu",
        ],
        remene: [
          "nazov-produktu",
          "podnadpis-produktu",
          "popis-produktu",
          "technicky-popis-produktu",
          "cena-produktu",
          "zlava-produktu",
          "aktivovana-zlava-produktu",
          "fotky-produktu",
        ],
      };

      return konfiguracia[this.typProduktu] || [];
    },
  },
  methods: {
    pridajNovySelect(index) {
      // Ak užívateľ vybral farbu a je to posledný select, pridá nový
      if (
        this.produktyDetaily.farby[index] &&
        index === this.produktyDetaily.farby.length - 1
      ) {
        this.produktyDetaily.farby.push(""); // Pridá ďalší prázdny select
      }
    },
    odstranSelect(index, event) {
      event.preventDefault(); // Zabránime otvoreniu kontextového menu
      // Ak je v poli viac ako 1 select, odstránime vybraný
      if (this.produktyDetaily.farby.length > 1) {
        this.produktyDetaily.farby.splice(index, 1); // Odstráni select na danom indexe
      }
    },
    zapisanieZapisu() {
      if (this.upravovanieZapisu) {
        this.upravenieZapisu();
      } else {
        this.vytvorenieProduktu();
      }
    },
    // Vaša existujúca metóda handleFileChange by sa mala aktualizovať nasledovne
    handleFileChange(event) {
      const files = event.target.files; // Získame všetky vybrané súbory
      if (files.length > 0) {
        Array.from(files).forEach((file) => {
          const reader = new FileReader();
          reader.onload = (e) => {
            this.produktyData.images.push({ src: e.target.result });
            // console.log("this.produktyData.images :>> ", this.produktyData.images);
          };
          reader.readAsDataURL(file);
        });
      }
    },
    removePhoto(index) {
      this.produktyData.images.splice(index, 1);
    },
    moveToFirst(index) {
      if (index !== 0) {
        const clickedPhoto = this.produktyData.images.splice(index, 1)[0]; // Odstránime kliknutú fotku
        this.produktyData.images.unshift(clickedPhoto); // Presunieme ju na začiatok poľa
      }
    },
    base64ToBlob(base64, mime) {
      // Funkcia na prevod base64 na Blob
      const byteString = atob(base64.split(",")[1]); // Odstráni "data:image/xxx;base64,"
      const ab = new ArrayBuffer(byteString.length);
      const ua = new Uint8Array(ab);
      for (let i = 0; i < byteString.length; i++) {
        ua[i] = byteString.charCodeAt(i);
      }
      return new Blob([ab], { type: mime });
    },
    vytvorenieProduktu() {
      if (this.podmienkyPreOdoslanie()) {
        this.odosielanie = true;

        const axios = require("axios");
        const produktyData = require("form-data");

        //orpavenie ceny na bodku pre databázu
        if (this.produktyData.cena.includes(",")) {
          this.produktyData.cena = this.produktyData.cena.replace(",", ".");
        }

        // Prejdi cez všetky vlastnosti objektu a nahradíme "\n" na "//n" (oprava formátovania)
        Object.keys(this.produktyDetaily).forEach((key) => {
          if (typeof this.produktyDetaily[key] === "string") {
            this.produktyDetaily[key] = this.produktyDetaily[key].replaceAll(
              "\n",
              "//n"
            );
          }
        });

        //pre upravenie názvu
        var produktNazov = this.produktyData.nazov
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase();

        // Pred pridaním do FormData, odstráň prázdne položky z pola "farby"
        this.produktyDetaily.farby = this.produktyDetaily.farby.filter(
          (farba) => farba !== ""
        );

        let data = new produktyData();
        data.append("virtuality", false);
        data.append("free", false);
        data.append("new", 0);
        data.append("category", this.typProduktu);
        data.append("name", this.produktyData.nazov);
        data.append("difficulty", 1);
        data.append("expiration", "never");
        data.append("price", this.produktyData.cena);
        data.append("details", JSON.stringify(this.produktyDetaily));
        data.append("data", "helishop/");

        for (let i = 0; i < this.produktyData.images.length; i++) {
          // Získame base64 kód obrázka
          const base64 = this.produktyData.images[i].src;

          // Preveď base64 na Blob (napr. pre PNG obrázok)
          const blob = this.base64ToBlob(base64, "image/png"); // Môžeš zmeniť typ podľa potreby

          // Pridaj Blob do FormData
          data.append(
            "data_files[]",
            blob,
            produktNazov + "-" + i + ".png" // Môžeš prispôsobiť príponu podľa typu obrázka
          );
        }

        //položky pre helishop
        data.append("discount", this.produktyData.zlava);
        data.append("discount_active", this.produktyData.aktivovanaZlava);
        data.append("additional_text", this.produktyData.podnadpis);

        console.log("tieto dáta chcem poslať data :>> ", data);

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
              this.$store.state.SnackBarText = "Produkt bol úspešne pridaný";
              this.$router.push("/admin");
            } else {
              this.$store.state.SnackBarTex = "Produkt sa nepodarilo pridať";
              this.odosielanie = false;
            }
          })
          .catch((error) => {
            console.log(error);
            this.$store.state.SnackBarText = "Produkt sa nepodarilo pridať";
            this.odosielanie = false;
          });
      }
    },
    async upravenieZapisu() {
      if (this.podmienkyPreOdoslanie()) {
        this.odosielanie = true;

        const axios = require("axios");
        const produktyData = require("form-data");

        //orpavenie ceny na bodku pre databázu
        if (this.produktyData.cena.includes(",")) {
          this.produktyData.cena = this.produktyData.cena.replace(",", ".");
        }

        // Prejdi cez všetky vlastnosti objektu a nahradíme "\n" na "//n" (oprava formátovania)
        Object.keys(this.produktyDetaily).forEach((key) => {
          if (typeof this.produktyDetaily[key] === "string") {
            this.produktyDetaily[key] = this.produktyDetaily[key].replaceAll(
              "\n",
              "//n"
            );
          }
        });

        //pre upravenie názvu
        var produktNazov = this.produktyData.nazov
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase();

        // Pred pridaním do FormData, odstráň prázdne položky z pola "farby"
        this.produktyDetaily.farby = this.produktyDetaily.farby.filter(
          (farba) => farba !== ""
        );

        let data = new produktyData();
        data.append("id", this.$route.query.id);
        data.append("virtuality", false);
        data.append("free", false);
        data.append("new", 0);
        data.append("category", this.typProduktu);
        data.append("name", this.produktyData.nazov);
        data.append("difficulty", 1);
        data.append("expiration", "never");
        data.append("price", this.produktyData.cena);
        data.append("details", JSON.stringify(this.produktyDetaily));
        data.append("data", this.produktyData.data);
        data.append("deleted", false);

        // ⚡ Prejdeme obrázky a upravíme formát
        for (let i = 0; i < this.produktyData.images.length; i++) {
          const imgSrc = this.produktyData.images[i].src;

          if (imgSrc.startsWith("data:image")) {
            // Base64 → Blob
            const blob = this.base64ToBlob(imgSrc, "image/png");
            data.append("data_files[]", blob, produktNazov + "-" + i + ".png");
          } else {
            // URL → Blob
            const blob = await this.urlToBlob(imgSrc);
            data.append("data_files[]", blob, produktNazov + "-" + i + ".png");
          }
        }

        //položky pre helishop
        data.append("discount", this.produktyData.zlava);
        data.append("discount_active", this.produktyData.aktivovanaZlava);
        data.append("additional_text", this.produktyData.podnadpis);

        console.log("tieto dáta chcem poslať data :>> ", data);

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
              this.$store.state.SnackBarText = "Produkt bol úspešne pridaný";
              this.$router.push("/admin");
            } else {
              this.$store.state.SnackBarTex = "Produkt sa nepodarilo pridať";
              this.odosielanie = false;
            }
          })
          .catch((error) => {
            console.log(error);
            this.$store.state.SnackBarText = "Produkt sa nepodarilo pridať";
            this.odosielanie = false;
          });
      }
    },
    async urlToBlob(url) {
      const response = await fetch(url);
      const blob = await response.blob();
      return blob;
    },
    podmienkyPreOdoslanie() {
      if (this.typProduktu == "none") {
        this.$store.state.SnackBarText = "Musíš vybrať typ produktu";
        return false;
      }
      if (!this.typProduktu) {
        this.$store.state.SnackBarText = "Musíš vybrať voľbu produktu!";
        return false;
      }
      if (!this.produktyData.nazov.trim()) {
        this.$store.state.SnackBarText = "Musíš zadať názov produktu!";
        return false;
      }
      if (!/^\d+(,\d{1,2})?$/.test(this.produktyData.cena.trim())) {
        this.$store.state.SnackBarText = "Musíš zadať platnú cenu produktu!";
        return false;
      }
      if (this.produktyData.images.length === 0) {
        this.$store.state.SnackBarText =
          "Musíš pridať aspoň jednu fotku produktu!";
        return false;
      }
      return true;
    },
    nacitajDataProduktu() {
      const axios = require("axios");
      var src = "/product/loadLimited.php/?id=";

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + src + this.$route.query.id,
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          if (response.data.status == "Request succesfull") {
            this.mapProductData(response.data.data);
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    mapProductData(product) {
      const details = JSON.parse(product.details);
      this.produktyData.nazov = product.name;
      this.produktyData.cena = product.price.replace(".", ",");
      this.produktyData.zlava = product.discount;
      this.produktyData.podnadpis = product.additional_text.replaceAll(
        "//n",
        "\n"
      );
      this.produktyData.aktivovanaZlava = product.discount_active === "true";
      this.produktyData.images = product.images.map((img) => ({ src: img }));

      this.typProduktu = product.category;
      this.produktyData.data = product.data;

      this.produktyDetaily.typ = details.typ;
      this.produktyDetaily.vydanie = details.vydanie;
      this.produktyDetaily.popis = details.popis.replaceAll("//n", "\n");
      this.produktyDetaily.opis = details.opis.replaceAll("//n", "\n");

      this.produktyDetaily.farby = details.farby;
      if (
        this.viditelnePolia.includes("farba-produktu") &&
        details.farby.length == 0
      ) {
        this.produktyDetaily.farby.push("");
      }

      this.produktyDetaily.velkosti = details.velkosti;
      this.produktyDetaily.infoPopis = details.infoPopis.replaceAll(
        "//n",
        "\n"
      );
      this.produktyDetaily.coSaPopis = details.coSaPopis.replaceAll(
        "//n",
        "\n"
      );
      this.produktyDetaily.preKohoPopis = details.preKohoPopis.replaceAll(
        "//n",
        "\n"
      );
      this.produktyDetaily.obsahPopis = details.obsahPopis.replaceAll(
        "//n",
        "\n"
      );
      this.produktyDetaily.technickyPopis = details.technickyPopis.replaceAll(
        "//n",
        "\n"
      );
      this.produktyDetaily.zoznamPopis = details.zoznamPopis.replaceAll(
        "//n",
        "\n"
      );
    },
    vymazZapis(parameterArg) {
      if (confirm("Ste si istý že chcete NAVŽDY vymazať produkt z predaja?")) {
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
                "Produkt s názvom " +
                this.produktyData.nazov +
                " bol stiahnutý z predaja";

              this.$router.go(-1);
            } else {
              this.$store.state.SnackBarText =
                "Produkt: " +
                this.produktyData.nazov +
                " sa nepodarilo odstrániť z predaja";
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
  width: 50%;
  padding: 1%;
  box-sizing: border-box;
}

input,
select {
  max-width: 18em;
  margin-left: 0 !important;
}

.row-form {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin: 0.5em 0;
}

.column:first-child {
  width: 40%;
  padding-left: 3%;
}
.column:last-child {
  width: 10%;
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

.row-form:has(.zvukova-stopa) {
  margin-top: 1em;
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
  width: 100%;
  display: flex;
  align-items: flex-start;
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

///css pre helishop
///
.size-options {
  display: flex;
  gap: 10px;
  margin: 1em auto 1.3em 0;
}

.size-options label {
  background-color: #ddd;
  color: black;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  text-align: center;
  transition: background-color 0.3s, color 0.3s;

  font-size: 1.2em;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 2.2em;
  width: 2.1em;
}

.size-options input {
  display: none;
}

.size-options input:checked + span {
  background-color: #fef35a;
  color: black;
  border-radius: 8px;

  width: -webkit-fill-available;
  padding: 0.35em 0.7em;

  display: flex;
  align-items: center;
  justify-content: center;
  height: -webkit-fill-available;
}

.photo {
  position: relative;
  cursor: pointer;
  &:hover {
    transform: scale(1.05);
    transition-duration: 0.3s;
  }
}
.photos {
  width: 110%;
  display: flex;
  justify-content: flex-start;
  align-items: center;
  flex-wrap: wrap;
}

.photo img:first-child {
  width: 7em;
  height: 7em;
  object-fit: cover;
  border-radius: 0.7em;
  margin: 1em 0.5em;
  box-shadow: 4px 4px 4px rgba(0, 0, 0, 0.25);
}
.photo.selected {
  img:first-child {
    border: 0.2em solid #fef05a;
    &:hover {
      transform: scale(1.09);
      transition-duration: 0.3s;
    }
  }
}

.photo .delete {
  width: 0.7em;
  height: auto;
  position: absolute;
  right: 15%;
  top: 17%;
  cursor: pointer;
  transition-duration: 0.3s;

  &:hover {
    transform: scale(1.2);
    transition-duration: 0.3s;
  }
}
</style>
