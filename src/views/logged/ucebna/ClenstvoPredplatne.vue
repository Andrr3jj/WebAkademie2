<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <h1>Heligonková Akadémia</h1>
      <h5>Vyberte si predplatné a rozvíjajte svoju hru na heligónke s nami!</h5>

      <div class="line horizontal w-80"></div>

      <p v-if="zakupenePredplatne" class="info">
        Ak chcete získať prístup k online výučbe, musíte si zakúpiť predplatné.
      </p>

      <p v-if="!zakupenePredplatne" class="info">
        Obsah je dostupný len pre predplatitelov. <br />
        Ak chcete získať prístup k online výučbe, musíte si zakúpiť predplatné.
      </p>

      <div class="items">
        <div class="item">
          <div v-if="predplatne.mesacne.detaily.aktivnaZlava" class="zlava">
            <p>
              Zľava {{ predplatne.mesacne.detaily.zlava }}%
              <span v-if="this.uznanyKupon.stav"
                >+ {{ uznanyKupon.hodnotaInt }}%</span
              >
            </p>
          </div>
          <div
            v-if="uznanyKupon.stav && !predplatne.mesacne.detaily.aktivnaZlava"
            class="zlava"
          >
            <p>Zľava {{ uznanyKupon.hodnotaInt }}%</p>
          </div>

          <div class="item-info">
            <p class="doba Adumu">{{ predplatne.mesacne.name }}</p>
            <p class="nazov">Predplatné</p>

            <div class="line horizontal"></div>

            <p class="cena">
              {{ this.formatovanaCena("mesacne") }}
              EUR
            </p>

            <p v-if="this.neformatovanaUspora('mesacne') > 0" class="usetrite">
              Ušetríte {{ this.formatovanaUspora("mesacne") }} €
            </p>

            <!-- <div
              v-if="!predplatne.mesacne.detaily.aktivnaZlava"
              class="opakovane"
            >
              <input
                type="checkbox"
                id="pravidelny-odber-pc"
                class="newsletter"
                v-model="opakovanaPladba"
              />
              <label for="pravidelny-odber-pc"
                >Po ukončení, znova zakúpiť</label
              >
            </div> -->

            <p class="zrusenie">
              {{ predplatne.mesacne.name }} predplatné <br />
              je možné zrušiť

              <img
                v-if="!popisOtvor.mesiac"
                @mouseenter="popisOtvor.mesiac = true"
                src="@/assets/images/icons/info.svg"
                alt="Doplňujúce info"
              />
              <img
                v-if="popisOtvor.mesiac"
                @mouseenter="popisOtvor.mesiac = true"
                @mouseleave="popisOtvor.mesiac = false"
                src="@/assets/images/icons/info-active-white.svg"
                alt="Doplňujúce info"
                class="info-active"
              />
            </p>

            <div
              @mouseenter="popisOtvor.mesiac = true"
              @mouseleave="popisOtvor.mesiac = false"
              :class="popisOtvor.mesiac ? 'popis-active' : 'popis-deactive'"
            >
              <p>
                Predplatné je možné zrušiť kedykoľvek, avšak finančné
                prostriedky Vám nebudú vrátené. Členstvo sa automaticky zruší po
                uplynutí doby predplatného.
              </p>
            </div>

            <div @click="makePayment('mesiac')" class="button">
              <img src="@/assets/images/icons/taska.svg" alt="" />
              <p>Zaplatiť</p>
            </div>
          </div>
        </div>
        <div class="item">
          <div v-if="predplatne.polrocne.detaily.aktivnaZlava" class="zlava">
            <p>
              Zľava {{ predplatne.polrocne.detaily.zlava }}%
              <span v-if="this.uznanyKupon.stav"
                >+ {{ uznanyKupon.hodnotaInt }}%</span
              >
            </p>
          </div>
          <div
            v-if="uznanyKupon.stav && !predplatne.polrocne.detaily.aktivnaZlava"
            class="zlava"
          >
            <p>Zľava {{ uznanyKupon.hodnotaInt }}%</p>
          </div>

          <div class="item-info">
            <p class="doba Adumu">{{ predplatne.polrocne.name }}</p>
            <p class="nazov">Predplatné</p>

            <div class="line horizontal"></div>

            <p class="cena">
              {{ this.formatovanaCena("polrocne") }}
              EUR
            </p>

            <p v-if="this.neformatovanaUspora('polrocne') > 0" class="usetrite">
              Ušetríte {{ this.formatovanaUspora("polrocne") }} €
            </p>

            <p class="zrusenie">
              {{ predplatne.polrocne.name }} predplatné <br />
              je jednorázové

              <img
                v-if="!popisOtvor.polrok"
                @mouseenter="popisOtvor.polrok = true"
                src="@/assets/images/icons/info.svg"
                alt="Doplňujúce info"
              />
              <img
                v-if="popisOtvor.polrok"
                @mouseenter="popisOtvor.polrok = true"
                @mouseleave="popisOtvor.polrok = false"
                src="@/assets/images/icons/info-active-white.svg"
                alt="Doplňujúce info"
                class="info-active"
              />
            </p>

            <div
              @mouseenter="popisOtvor.polrok = true"
              @mouseleave="popisOtvor.polrok = false"
              :class="popisOtvor.polrok ? 'popis-active' : 'popis-deactive'"
            >
              <p>
                Predplatné je jednorazové a automaticky zaniká po jeho ukončení.
                Ak chcete pokračovať v členstve, musíte si predplatné opätovne
                zakúpiť.
              </p>
            </div>

            <div @click="makePayment('pol rok')" class="button">
              <img src="@/assets/images/icons/taska.svg" alt="" />
              <p>Zaplatiť</p>
            </div>
          </div>
        </div>
        <div class="item">
          <div v-if="predplatne.rocne.detaily.aktivnaZlava" class="zlava">
            <p>
              Zľava {{ predplatne.rocne.detaily.zlava }}%
              <span v-if="this.uznanyKupon.stav"
                >+ {{ uznanyKupon.hodnotaInt }}%</span
              >
            </p>
          </div>
          <div
            v-if="uznanyKupon.stav && !predplatne.rocne.detaily.aktivnaZlava"
            class="zlava"
          >
            <p>Zľava {{ uznanyKupon.hodnotaInt }}%</p>
          </div>

          <div class="item-info">
            <p class="doba Adumu">{{ predplatne.rocne.name }}</p>
            <p class="nazov">Predplatné</p>

            <div class="line horizontal"></div>

            <p class="cena">
              {{ this.formatovanaCena("rocne") }}
              EUR
            </p>

            <p v-if="this.neformatovanaUspora('rocne') > 0" class="usetrite">
              Ušetríte {{ this.formatovanaUspora("rocne") }} €
            </p>

            <p class="zrusenie">
              {{ predplatne.rocne.name }} predplatné <br />
              je jednorázové

              <img
                v-if="!popisOtvor.rok"
                @mouseenter="popisOtvor.rok = true"
                src="@/assets/images/icons/info.svg"
                alt="Doplňujúce info"
              />
              <img
                v-if="popisOtvor.rok"
                @mouseenter="popisOtvor.rok = true"
                @mouseleave="popisOtvor.rok = false"
                src="@/assets/images/icons/info-active-white.svg"
                alt="Doplňujúce info"
                class="info-active"
              />
            </p>

            <div
              @mouseenter="popisOtvor.rok = true"
              @mouseleave="popisOtvor.rok = false"
              :class="popisOtvor.rok ? 'popis-active' : 'popis-deactive'"
            >
              <p>
                Predplatné je jednorazové a automaticky zaniká po jeho ukončení.
                Ak chcete pokračovať v členstve, musíte si predplatné opätovne
                zakúpiť.
              </p>
            </div>

            <div @click="makePayment('rok')" class="button">
              <img src="@/assets/images/icons/taska.svg" alt="" />
              <p>Zaplatiť</p>
            </div>
          </div>
        </div>
      </div>

      <p class="info">
        <strong v-if="!uznanyKupon.stav"
          >Ak máte zľavový kód, neváhajte ho využiť.</strong
        >
        <strong v-if="uznanyKupon.stav"
          >Aktuálne sa využíva zľavový kód: {{ uznanyKupon.nazov }}</strong
        >
      </p>

      <div class="row">
        <input
          @input="overKupon()"
          @change="overKupon()"
          type="text"
          name="coupon"
          id="coupon"
          v-model="kuponKod"
          placeholder="Zľavový kód"
        />
        <p class="percentage">-{{ uznanyKupon.hodnota }}%</p>
        <div class="more-info">
          <img
            v-if="!popisOtvor.coupon"
            @mouseenter="popisOtvor.coupon = true"
            src="@/assets/images/icons/info.svg"
            alt="Doplňujúce info"
          />
          <img
            v-if="popisOtvor.coupon"
            @mouseenter="popisOtvor.coupon = true"
            @mouseleave="popisOtvor.coupon = false"
            src="@/assets/images/icons/info-active-white.svg"
            alt="Doplňujúce info"
            class="info-active"
          />

          <div
            @mouseenter="popisOtvor.coupon = true"
            @mouseleave="popisOtvor.coupon = false"
            :class="popisOtvor.coupon ? 'popis-active' : 'popis-deactive'"
          >
            <p>
              Vaša zľava sa automaticky odpočíta z aktuálnej zvýhodnenej ceny
              mesačného, polročného alebo ročného predplatného.
            </p>
          </div>
        </div>
      </div>

      <p v-if="!uznanyKupon.stav" class="info-sml">
        Stačí ho zadať do políčka vyššie a získate zľavu na svoje predplatné.
      </p>
      <p v-if="uznanyKupon.stav" class="info-sml">
        Pre zmenu využívaného kupónu je potrebné prepísať aktuálny kupón čím sa
        zruší.
      </p>

      <div class="line horizontal w-80"></div>

      <p class="info">Zistite viac o našom online vzdelávaní a videách tu!</p>
      <router-link to="/naucne-videa" class="button">
        <a> Náučné videá </a>
      </router-link>
    </div>
  </section>
  <div :id="$route.name" class="mobile">
    <section>
      <div class="scroll">
        <h1>Heligonková Akadémia</h1>
        <h5>
          Vyberte si predplatné a rozvíjajte svoju hru na heligónke s nami!
        </h5>

        <div class="line horizontal w-80"></div>

        <p v-if="zakupenePredplatne" class="info">
          Ak chcete získať prístup k online výučbe, musíte si zakúpiť
          predplatné.
        </p>

        <p v-if="!zakupenePredplatne" class="info">
          Obsah je dostupný len pre predplatitelov. <br />
          Ak chcete získať prístup k online výučbe, musíte si zakúpiť
          predplatné.
        </p>

        <div class="items">
          <div class="item">
            <div v-if="predplatne.mesacne.detaily.aktivnaZlava" class="zlava">
              <p>
                Zľava {{ predplatne.mesacne.detaily.zlava }}%
                <span v-if="this.uznanyKupon.stav"
                  >+ {{ uznanyKupon.hodnotaInt }}%</span
                >
              </p>
            </div>
            <div
              v-if="
                uznanyKupon.stav && !predplatne.mesacne.detaily.aktivnaZlava
              "
              class="zlava"
            >
              <p>Zľava {{ uznanyKupon.hodnotaInt }}%</p>
            </div>

            <div class="item-info">
              <p class="doba Adumu">{{ predplatne.mesacne.name }}</p>
              <p class="nazov">Predplatné</p>

              <div class="line horizontal"></div>

              <p class="cena">
                {{ this.formatovanaCena("mesacne") }}
                EUR
              </p>

              <p
                v-if="this.neformatovanaUspora('mesacne') > 0"
                class="usetrite"
              >
                Ušetríte {{ this.formatovanaUspora("mesacne") }} €
              </p>

              <!-- <div
                v-if="!predplatne.mesacne.detaily.aktivnaZlava"
                class="opakovane"
              >
                <input
                  type="checkbox"
                  id="pravidelny-odber"
                  class="newsletter"
                  v-model="opakovanaPladba"
                />
                <label for="pravidelny-odber">Po ukončení, znova zakúpiť</label>
              </div> -->

              <p class="zrusenie">
                {{ predplatne.mesacne.name }} predplatné <br />
                je možné zrušiť

                <img
                  v-if="!popisOtvor.mesiac"
                  @mouseenter="popisOtvor.mesiac = true"
                  src="@/assets/images/icons/info.svg"
                  alt="Doplňujúce info"
                />
                <img
                  v-if="popisOtvor.mesiac"
                  @mouseenter="popisOtvor.mesiac = true"
                  @mouseleave="popisOtvor.mesiac = false"
                  src="@/assets/images/icons/info-active-white.svg"
                  alt="Doplňujúce info"
                  class="info-active"
                />
              </p>

              <div
                @mouseenter="popisOtvor.mesiac = true"
                @mouseleave="popisOtvor.mesiac = false"
                :class="popisOtvor.mesiac ? 'popis-active' : 'popis-deactive'"
              >
                <p>
                  Predplatné je možné zrušiť kedykoľvek, avšak finančné
                  prostriedky Vám nebudú vrátené. Členstvo sa automaticky zruší
                  po uplynutí doby predplatného.
                </p>
              </div>

              <div @click="makePayment('mesiac')" class="button">
                <img src="@/assets/images/icons/taska.svg" alt="" />
                <p>Zaplatiť</p>
              </div>
            </div>
          </div>
          <div class="item">
            <div v-if="predplatne.polrocne.detaily.aktivnaZlava" class="zlava">
              <p>
                Zľava {{ predplatne.polrocne.detaily.zlava }}%
                <span v-if="this.uznanyKupon.stav"
                  >+ {{ uznanyKupon.hodnotaInt }}%</span
                >
              </p>
            </div>
            <div
              v-if="
                uznanyKupon.stav && !predplatne.polrocne.detaily.aktivnaZlava
              "
              class="zlava"
            >
              <p>Zľava {{ uznanyKupon.hodnotaInt }}%</p>
            </div>

            <div class="item-info">
              <p class="doba Adumu">{{ predplatne.polrocne.name }}</p>
              <p class="nazov">Predplatné</p>

              <div class="line horizontal"></div>

              <p class="cena">
                {{ this.formatovanaCena("polrocne") }}
                EUR
              </p>

              <p
                v-if="this.neformatovanaUspora('polrocne') > 0"
                class="usetrite"
              >
                Ušetríte {{ this.formatovanaUspora("polrocne") }} €
              </p>

              <p class="zrusenie">
                {{ predplatne.polrocne.name }} predplatné <br />
                je jednorázové

                <img
                  v-if="!popisOtvor.polrok"
                  @mouseenter="popisOtvor.polrok = true"
                  src="@/assets/images/icons/info.svg"
                  alt="Doplňujúce info"
                />
                <img
                  v-if="popisOtvor.polrok"
                  @mouseenter="popisOtvor.polrok = true"
                  @mouseleave="popisOtvor.polrok = false"
                  src="@/assets/images/icons/info-active-white.svg"
                  alt="Doplňujúce info"
                  class="info-active"
                />
              </p>

              <div
                @mouseenter="popisOtvor.polrok = true"
                @mouseleave="popisOtvor.polrok = false"
                :class="popisOtvor.polrok ? 'popis-active' : 'popis-deactive'"
              >
                <p>
                  Predplatné je jednorazové a automaticky zaniká po jeho
                  ukončení. Ak chcete pokračovať v členstve, musíte si
                  predplatné opätovne zakúpiť.
                </p>
              </div>

              <div @click="makePayment('pol rok')" class="button">
                <img src="@/assets/images/icons/taska.svg" alt="" />
                <p>Zaplatiť</p>
              </div>
            </div>
          </div>
          <div class="item">
            <div v-if="predplatne.rocne.detaily.aktivnaZlava" class="zlava">
              <p>
                Zľava {{ predplatne.rocne.detaily.zlava }}%
                <span v-if="this.uznanyKupon.stav"
                  >+ {{ uznanyKupon.hodnotaInt }}%</span
                >
              </p>
            </div>
            <div
              v-if="uznanyKupon.stav && !predplatne.rocne.detaily.aktivnaZlava"
              class="zlava"
            >
              <p>Zľava {{ uznanyKupon.hodnotaInt }}%</p>
            </div>

            <div class="item-info">
              <p class="doba Adumu">{{ predplatne.rocne.name }}</p>
              <p class="nazov">Predplatné</p>

              <div class="line horizontal"></div>

              <p class="cena">
                {{ this.formatovanaCena("rocne") }}
                EUR
              </p>

              <p v-if="this.neformatovanaUspora('rocne') > 0" class="usetrite">
                Ušetríte {{ this.formatovanaUspora("rocne") }} €
              </p>

              <p class="zrusenie">
                {{ predplatne.rocne.name }} predplatné <br />
                je jednorázové

                <img
                  v-if="!popisOtvor.rok"
                  @mouseenter="popisOtvor.rok = true"
                  src="@/assets/images/icons/info.svg"
                  alt="Doplňujúce info"
                />
                <img
                  v-if="popisOtvor.rok"
                  @mouseenter="popisOtvor.rok = true"
                  @mouseleave="popisOtvor.rok = false"
                  src="@/assets/images/icons/info-active-white.svg"
                  alt="Doplňujúce info"
                  class="info-active"
                />
              </p>

              <div
                @mouseenter="popisOtvor.rok = true"
                @mouseleave="popisOtvor.rok = false"
                :class="popisOtvor.rok ? 'popis-active' : 'popis-deactive'"
              >
                <p>
                  Predplatné je jednorazové a automaticky zaniká po jeho
                  ukončení. Ak chcete pokračovať v členstve, musíte si
                  predplatné opätovne zakúpiť.
                </p>
              </div>

              <div @click="makePayment('rok')" class="button">
                <img src="@/assets/images/icons/taska.svg" alt="" />
                <p>Zaplatiť</p>
              </div>
            </div>
          </div>
        </div>

        <p class="info">
          <strong v-if="!uznanyKupon.stav"
            >Ak máte zľavový kód, neváhajte ho využiť.</strong
          >
          <strong v-if="uznanyKupon.stav"
            >Aktuálne sa využíva zľavový kód: {{ uznanyKupon.nazov }}</strong
          >
        </p>

        <div class="row">
          <input
            @input="overKupon()"
            @change="overKupon()"
            type="text"
            name="coupon"
            id="coupon"
            v-model="kuponKod"
            placeholder="Zľavový kód"
          />
          <p class="percentage">-{{ uznanyKupon.hodnota }}%</p>
          <div class="more-info">
            <img
              v-if="!popisOtvor.coupon"
              @mouseenter="popisOtvor.coupon = true"
              src="@/assets/images/icons/info.svg"
              alt="Doplňujúce info"
            />
            <img
              v-if="popisOtvor.coupon"
              @mouseenter="popisOtvor.coupon = true"
              @mouseleave="popisOtvor.coupon = false"
              src="@/assets/images/icons/info-active-white.svg"
              alt="Doplňujúce info"
              class="info-active"
            />

            <div
              @mouseenter="popisOtvor.coupon = true"
              @mouseleave="popisOtvor.coupon = false"
              :class="popisOtvor.coupon ? 'popis-active' : 'popis-deactive'"
            >
              <p>
                Vaša zľava sa automaticky odpočíta z aktuálnej zvýhodnenej ceny
                mesačného, polročného alebo ročného predplatného.
              </p>
            </div>
          </div>
        </div>

        <p v-if="!uznanyKupon.stav" class="info-sml">
          Stačí ho zadať do políčka vyššie a získate zľavu na svoje predplatné.
        </p>
        <p v-if="uznanyKupon.stav" class="info-sml">
          Pre zmenu využívaného kupónu je potrebné prepísať aktuálny kupón čím
          sa zruší.
        </p>

        <div class="line horizontal w-80"></div>

        <p class="info">Zistite viac o našom online vzdelávaní a videách tu!</p>
        <router-link to="/naucne-videa" class="button">
          <a> Náučné videá </a>
        </router-link>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);
    this.overZakupenePredplatne();
    this.overPlatnostKuponov();

    for (let i = 1; i < 4; i++) {
      this.nacitajPredplatne(i);
    }
  },
  data() {
    return {
      zakupenePredplatne: false,
      opakovanaPladba: false,
      popisOtvor: {
        mesiac: false,
        polrok: false,
        rok: false,
        coupon: false,
      },
      predplatne: {
        mesacne: {
          id: 1,
          cena: "",
          name: "",
          expiration: "",
          detaily: {},
        },
        polrocne: {
          id: 2,
          cena: "",
          name: "",
          expiration: "",
          detaily: {},
        },
        rocne: {
          id: 3,
          cena: "",
          name: "",
          expiration: "",
          detaily: {},
        },
      },
      predplatneData: {},
      uznanyKupon: {
        stav: false,
        nazov: "",
        hodnota: "0",
        hodnotaInt: 0,
        zlava: 0,
      },
      kuponKod: "",
    };
  },
  methods: {
    overPlatnostKuponov() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/subscription/validateCouponInCart.php",
        // headers: {},
      };
      axios
        .request(config)
        .then((response) => {
          setTimeout(() => {
            this.nacitajKupon();
          }, 500);
          console.log("response :>> ", response);
        })
        .catch((error) => {
          console.log(error);
        });
    },
    overKupon() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/subscription/addCoupon.php/?code=" +
          this.kuponKod,
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request fullfiled") {
            this.$store.state.SnackBarText = "Kupón sa úspešne použil";
            this.nacitajKupon();
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    zmazanieKuponu() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/subscription/removeCoupon.php/?code=" +
          this.uznanyKupon.nazov,
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request fullfiled") {
            this.nacitajKupon();
            this.$store.state.SnackBarText = "Kupón bol vymazaný";
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nacitajKupon() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/subscription/loadCoupon.php",
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request sucessfull") {
            if (response.data.data.length == 0) {
              this.uznanyKupon = [];
              this.uznanyKupon.stav = false;
              return;
            } else {
              console.log("response.data.data :>> ", response.data.data);

              var kupon = JSON.parse(response.data.data);
              console.log(kupon);
              this.uznanyKupon.nazov = kupon.code;

              // Prevod hodnoty na číslo s dvomi desatinnými miestami
              let newValue = parseFloat(kupon.value.replace(",", ".")).toFixed(
                2
              );

              // Zamenenie bodky za čiarku
              newValue = newValue.replace(".", ",");
              this.uznanyKupon.hodnota = newValue;

              this.uznanyKupon.hodnotaInt = parseFloat(kupon.value);

              // this.uznanyKupon.zlava = (
              //   (this.uznanyKupon.hodnotaInt / 100) *
              //   this.produktyCena
              // ).toFixed(2);

              // this.noCouponProduktyCena = this.produktyCena;

              // this.produktyCena = this.produktyCena - this.uznanyKupon.zlava;

              this.uznanyKupon.stav = true;

              if (this.kuponKod == "") {
                this.kuponKod = this.uznanyKupon.nazov;
              }
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nacitajPredplatne(ID) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + ID,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (ID == 1) {
            this.predplatne.mesacne.cena = response.data.data.price;
            this.predplatne.mesacne.name = response.data.data.name;
            this.predplatne.mesacne.expiration = response.data.data.expiration;
            this.predplatne.mesacne.detaily = JSON.parse(
              response.data.data.details
            );
          } else if (ID == 2) {
            this.predplatne.polrocne.cena = response.data.data.price;
            this.predplatne.polrocne.name = response.data.data.name;
            this.predplatne.polrocne.expiration = response.data.data.expiration;
            this.predplatne.polrocne.detaily = JSON.parse(
              response.data.data.details
            );
          } else if (ID == 3) {
            this.predplatne.rocne.cena = response.data.data.price;
            this.predplatne.rocne.name = response.data.data.name;
            this.predplatne.rocne.expiration = response.data.data.expiration;
            this.predplatne.rocne.detaily = JSON.parse(
              response.data.data.details
            );
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    formatovanaUspora(doba) {
      let uspora = 0;
      if (this.predplatne[doba].detaily.aktivnaZlava) {
        console.log("Aktívna zľava pre dobu:", doba);

        uspora =
          (this.predplatne[doba].cena * this.predplatne[doba].detaily.zlava) /
          100;

        console.log("Počiatočná úspora:", uspora);
      }

      if (this.uznanyKupon.stav && this.uznanyKupon.hodnotaInt) {
        console.log("Uznaný kupón nájdený, stav:", this.uznanyKupon.stav);
        console.log("Hodnota kupónu:", this.uznanyKupon.hodnotaInt);

        let dodatocnaUspora =
          ((this.predplatne[doba].cena - uspora) *
            this.uznanyKupon.hodnotaInt) /
          100;
        console.log("Dodatocná úspora z kupónu:", dodatocnaUspora);

        uspora += dodatocnaUspora;
      }

      console.log("Celková úspora po kupóne:", uspora);

      console.log("Žiadna aktívna zľava pre dobu:", doba);
      return uspora.toString().replace(".", ",");
    },

    neformatovanaUspora(doba) {
      let uspora = 0;
      console.log("Výpočet neformátovanej úspory pre dobu:", doba);

      // Ak je aktívna zľava v rámci predplatného
      if (this.predplatne[doba].detaily.aktivnaZlava) {
        uspora =
          (this.predplatne[doba].cena * this.predplatne[doba].detaily.zlava) /
          100;
        console.log("Úspora so zľavou:", uspora);
      }

      // Ak existuje uznaný kupón s hodnotou
      if (this.uznanyKupon.stav && this.uznanyKupon.hodnotaInt) {
        console.log("Uznaný kupón nájdený, stav:", this.uznanyKupon.stav);
        console.log("Hodnota kupónu:", this.uznanyKupon.hodnotaInt);

        const dodatocnaUspora =
          ((this.predplatne[doba].cena - uspora) *
            this.uznanyKupon.hodnotaInt) /
          100;
        console.log("Dodatocná úspora z kupónu:", dodatocnaUspora);

        uspora += dodatocnaUspora; // Pridáme úsporu z kupónu k celkovej úspore
      }

      console.log("Celková neformátovaná úspora:", uspora);

      return uspora; // Vrátime celkovú úsporu
    },

    formatovanaCena(doba) {
      console.log("Výpočet formátovanej ceny pre dobu:", doba);

      if (this.predplatne[doba].detaily.aktivnaZlava || this.uznanyKupon.stav) {
        console.log("Aktívna zľava pre dobu:", doba);

        var cena = this.predplatne[doba].cena - this.neformatovanaUspora(doba);

        console.log("Cena po zľave:", cena);

        return cena.toString().replace(".", ",");
      } else {
        console.log("Žiadna aktívna zľava, cena:", this.predplatne[doba].cena);

        return this.predplatne[doba].cena.toString().replace(".", ",");
      }
    },

    overZakupenePredplatne() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/subscription/isSubscribed.php/",
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.data == "Is subscribed") {
            this.$router.push("/ucebna/clenstvo-zakupene");
            console.log(response.data);
          } else {
            if (this.$route.query.data == "predplatne") {
              this.zakupenePredplatne = true;
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    makePayment(doba) {
      if (!this.isFormValidAddressValue()) {
        this.$store.state.SnackBarText =
          "Pre zakúpenie predplatného musíte vyplniť fakturačné údaje v sekcii Moja učebňa > Nastavenia účtu";
        return;
      }
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/subscription/paymentCreateSubscription.php/?duration=" +
          doba +
          "&recurent=" +
          this.opakovanaPladba,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.data == "Not logged in") {
            this.$store.state.SnackBarText =
              "Pre zakúpenie členstva musíte byť prihlásený";
          } else if (response.data.status == "Request failed") {
            this.$store.state.SnackBarText =
              "Niečo sa pokazilo prosím skúste neskôr alebo nás kontaktujte";
          } else {
            window.location.href = response.data.data;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    isFormValidAddressValue() {
      if (
        this.$store.state.user.name === "" ||
        this.$store.state.user.name == undefined ||
        this.$store.state.user.name == null
      ) {
        return false;
      }

      if (
        this.$store.state.user.surname === "" ||
        this.$store.state.user.surname == undefined ||
        this.$store.state.user.surname == null
      ) {
        return false;
      }

      if (
        this.$store.state.user.billingAddressStreet === "" ||
        this.$store.state.user.billingAddressStreet == undefined ||
        this.$store.state.user.billingAddressStreet == null
      ) {
        return false;
      }

      if (
        this.$store.state.user.billingAddressHouseNumber == "" ||
        this.$store.state.user.billingAddressHouseNumber == undefined ||
        this.$store.state.user.billingAddressHouseNumber == null
      ) {
        return false;
      }

      if (
        this.$store.state.user.billingAddressCity == "" ||
        this.$store.state.user.billingAddressCity == undefined ||
        this.$store.state.user.billingAddressCity == null
      ) {
        return false;
      }

      if (
        this.$store.state.user.billingAddressPostcode === "" ||
        this.$store.state.user.billingAddressPostcode == undefined ||
        this.$store.state.user.billingAddressPostcode == null
      ) {
        return false;
      }

      return true;
    },
  },
  watch: {
    kuponKod(newValue, oldValue) {
      // Spusti metódu len vtedy, ak predtým bol kód ne-prázdny a teraz je prázdny
      if (oldValue !== "" && newValue === "") {
        this.zmazanieKuponu();
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

h1,
h5,
p {
  text-align: center;
}

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 6.1vw;
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
  color: #000;

  font-size: 2.3vw;

  font-style: normal;
  font-weight: 700;
  line-height: 115%; /* 2.875em */
  margin: 0.5em auto 0.7em;
  width: 95%;

  margin-left: auto;
  margin-right: auto;
  padding: 0;
}

.info {
  font-size: 1.7em;
  font-weight: 500;
  margin: 0.7em auto;
}

.info-sml {
  font-size: 1.2em;
  font-weight: 400;
  margin: 0.7em auto;
  max-width: 90%;
}

.items {
  display: flex;
  justify-content: space-around;
  gap: 6%;
  width: 90%;
  max-width: 65em;
  margin: 3em auto 2em;
}

.item-info {
  display: flex;
  flex-direction: column;
  height: 19.5em;
}

.item {
  padding: 1.5em;

  border-radius: 1.5625rem;
  border: 0.4em solid #fef35a;
  background: var(
    --Linear,
    linear-gradient(140deg, #90c94f 35.64%, #fef35a 99.4%)
  );
  box-shadow: 5px 5px 10px 0 rgba(0, 0, 0, 0.35);
  position: relative;

  .line.horizontal {
    height: 0.4rem;
  }
}

.zlava {
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translate(-50%, -50%);
  border-radius: 1rem;
  border: 0.3em solid #fef35a;
  background: #fef35a;
  box-shadow: 5px 5px 10px 0 rgba(0, 0, 0, 0.35),
    inset 4px 4px 4px rgba(0, 0, 0, 0.2);
  padding: 0.6em 1em 0.4em;
  width: auto;
  p {
    font-size: 1.2em;
    white-space: nowrap;
    font-weight: 700;
  }
}

.doba {
  font-size: 2.8em;
  font-weight: 500;
}

.nazov {
  font-size: 1.75em;
  font-weight: 500;
  margin-bottom: 0.3em;
}

.cena {
  font-size: 1.7em;
  font-weight: 600;
  margin: 0.8em auto auto;
}

.usetrite {
  font-size: 1.5em;
  font-weight: 400;
  text-decoration: underline;
  margin-bottom: 0.8em;
}

.opakovane {
  font-size: 0.9em;
  font-weight: 600;
  margin-bottom: 1.4em;
  white-space: normal;

  input {
    width: auto;
  }
}

.zrusenie {
  font-size: 1em;
  font-size: 400;
  margin-bottom: 0.4em;
}

.button {
  width: 6em;
  justify-content: center;
  padding: 0.4em 0.9em;
  p {
    font-size: 0.7em;
  }

  img {
    width: 0.8em;
  }
}

a.button {
  margin: auto auto 2em;
  width: 8.5em;
  padding: 0.4em 1em;
}

.opakovane {
  width: 110%;
  margin-left: -5%;
}

.opakovane input {
  display: none;
}

.opakovane input + label {
  position: relative;
  padding-left: 10%;
  cursor: pointer;
}

.opakovane input + label:before {
  content: "";
  position: absolute;
  left: 0;
  top: 0;
  width: 1em;
  height: 1em;
  border: 2px solid #000;
  background-color: transparent;
  border-radius: 0.35em;
  box-sizing: border-box;
}

.opakovane input:checked + label:before {
  background-color: #f5ea57;
}

//popis

.zrusenie {
  position: relative;
}

.zrusenie img {
  position: absolute;
  bottom: 0.25em;
  right: 1.7em;
  transition-duration: 0.2s;
  width: 0.9em;

  &.info-active {
    width: 1.8em;
    bottom: -0.65em;
    right: 1.3em;
  }
  &:hover {
    cursor: pointer;
    transform: scale(1.1);
  }
}

.row img {
  position: absolute;
  transition-duration: 0.2s;
  width: 1.4em;
  height: auto;

  &.info-active {
    width: 1.8em;
    bottom: -0.65em;
  }
  &:hover {
    cursor: pointer;
    transform: scale(1.2);
  }
}

.popis-active {
  position: absolute;
  width: 80%;
  background-color: #ededed;
  box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.25);
  border-radius: 0.8rem;
  top: 78%;
  left: 50%;
  transform: translateX(-50%);
  padding: 0.6em;
  p {
    font-size: 0.7em !important;
  }
}

.row .popis-active {
  width: 95%;
}

.popis-deactive {
  display: none;
}

.percentage {
  font-size: 1.3em;
  font-weight: 300;
  position: absolute;
  right: 12%;
  top: 53%;
  transform: translateY(-50%);
}

//koniec popisu

.row {
  max-width: 22em;
  margin: auto;
  position: relative;
}

.row > div {
  background: transparent;
  box-shadow: none;
}

input#coupon {
  padding: 0.4em 3em 0.4em 0.5em;
}

@media only screen and (max-width: 1400px) {
  .items {
    font-size: 1vw;
  }

  .item .line.line.horizontal {
    height: 0.3rem;
  }

  h5 {
    padding-left: 5%;
    padding-right: 5%;
  }
}
@media only screen and (max-width: 1000px) {
  h1 {
    font-size: 9vw;
  }

  h5 {
    margin: 0em auto 0.2em;
    font-size: 3.5vw;
  }

  .info {
    font-size: 1.5em;
  }
}

.mobile {
  .items {
    font-size: 1em;
    flex-wrap: wrap;
    margin: 2em auto 2em;
  }

  .info {
    margin: 1.5em auto 1em;
    width: 90%;
  }

  .item {
    margin: 2em auto;

    .button {
      padding: 0.4em 0.9em;
      margin: 0;
    }
  }

  .scroll {
    overflow: hidden;
    margin-bottom: 2em;
  }
}

@media only screen and (max-width: 450px) {
  .scroll > .button {
    font-size: 1em;
  }

  .info {
    font-size: 1.3em;
  }
}
</style>
