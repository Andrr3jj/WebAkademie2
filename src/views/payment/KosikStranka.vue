<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="line horizontal"></div>

      <div class="box">
        <div v-if="$store.state.user == null" class="box-item left">
          <p class="nadpis">Váš nákupný košík je prázdny!</p>
          <p>
            Prihláste sa, aby ste mohli ukladať položky v nákupnom košíku alebo
            sa dostať k tým, ktoré ste už uložili.
          </p>

          <router-link to="/prihlasenie" class="login button">
            <img
              src="@/assets/images/icons/prihlasenie.svg"
              alt=""
            /><router-link to="/prihlasenie">Prihlásiť sa</router-link>
          </router-link>
        </div>
        <div
          v-if="$store.state.user != null && this.userCart.length === 0"
          class="box-item left"
        >
          <p class="nadpis">Váš nákupný košík je prázdny!</p>
          <p>
            Keď pridáte niečo do košíka, zobrazí sa to tu. <br />Stačí si
            vybrať.
          </p>
          <div class="second">
            <routerLink to="naucne-videa" class="box-main">
              <img src="@/assets/images/gallery/NaucneVideaTitle.png" alt="" />
              <p>Náučné videá</p>
            </routerLink>
            <routerLink to="ciselne-zapisy" class="box-main">
              <img
                src="@/assets/images/gallery/CiselneZapisyTitle.png"
                alt=""
              />
              <p>Číselné zápisy</p>
            </routerLink>
            <routerLink to="helishop" class="box-main">
              <img src="@/assets/images/gallery/MerchTitle.png" alt="" />
              <p>HeliShop</p>
            </routerLink>
          </div>
        </div>

        <div
          v-if="$store.state.user != null && this.userCart.length !== 0"
          class="box-item left"
        >
          <ul
            v-if="Object.keys(this.produktyData).length == userCart.length"
            class="cart"
          >
            <kosik-produkt
              v-for="item in userCart"
              :key="item.id"
              :notes="{ id: item.id, count: item.count }"
              :produktyData="produktyData[item.id_delete]"
              :umiestnenie="'kosik'"
              @zmenitKuponNacitany="nacitajCenu"
            ></kosik-produkt>
          </ul>
          <div class="line horizontal"></div>

          <div class="gift_card">
            <h4 class="m-right">PLATBA</h4>
            <p class="platba-ako m-right">Ako chcete zaplatiť?</p>
            <div
              class="a_gift-cart"
              @click="zobrazPoukazkuInput = !zobrazPoukazkuInput"
            >
              <p>Pridať darčekové karty</p>
              <img src="@/assets/images/icons/darcek.svg" alt="" />
            </div>
            <div v-if="zobrazPoukazkuInput" class="voucher-input-wrap">
              <VoucherInput
                v-model="kodDarcekovejPoukazky"
                placeholder="Zadajte kód darčekovej poukážky"
                @submit.stop.prevent="pridajDarcekovuPoukazku"
              />
              <button
                @click.stop.prevent="pridajDarcekovuPoukazku"
                class="button"
              >
                Pridať
              </button>
            </div>
          </div>

          <div class="debet-cart">
            <div class="line"></div>
            <p>Karta</p>
            <div class="prijimame">
              <img src="@/assets/images/platby/platby1.png" alt="Maestro" />
              <img src="@/assets/images/platby/platby2.png" alt="Mastercadt" />
              <img src="@/assets/images/platby/platby3.png" alt="VISA" />
            </div>
          </div>
        </div>
        <div class="box-item right">
          <h4>ZHRNUTIE OBJEDNÁVKY</h4>

          <div class="zlavy">
            <p>Zľavy</p>
            <p
              v-if="!chcemZadatKupon && !uznanyKupon.stav"
              @click="chcemZadatKupon = true"
              class="uplatnenie-zlavy"
            >
              Uplatniť zľavu
            </p>
            <p
              v-if="chcemZadatKupon"
              @click="chcemZadatKupon = false"
              class="uplatnenie-zlavy"
            >
              Nechcem zľavu
            </p>
            <p
              v-if="uznanyKupon.stav"
              @click="zmazanieKuponu()"
              class="uplatnenie-zlavy"
            >
              Aktívny kupón
            </p>
          </div>

          <!-- Zľavy (kupóny aj poukážky) - jedna sekcia -->
          <div class="zlavy-list">
            <!-- Kupón -->
            <div v-if="uznanyKupon.stav" class="zlava-wrap">
              <div class="zlava-type-label">Kupón</div>
              <div
                class="uznany-kupon flex-row"
                @click="zmazanieKuponu()"
                style="cursor: pointer"
                title="Vymazať kupón"
              >
                <span class="kupon-kod">
                  {{ uznanyKupon.nazov }} ({{ uznanyKupon.hodnota }}%)
                </span>
                <span class="zlava-suma">-{{ produktyCenaCouponLess }}€</span>
              </div>
            </div>

            <!-- Input na kupón (nerobím úpravy, len roztiahnuť na šírku) -->
            <form v-if="chcemZadatKupon" @submit.prevent="odoslanieKuponu()">
              <div class="zlava-type-label">Kupón</div>
              <div class="row full-width">
                <input
                  type="text"
                  placeholder="Zľavový kód"
                  name="code"
                  id="code"
                  @input="kuponZmena()"
                  @change="kuponZmena()"
                  v-model="kuponKod"
                  class="full-width"
                />
              </div>
            </form>

            <!-- Darčeková poukážka -->
            <div
              v-for="(poukazka, i) in giftCardsInCart"
              :key="poukazka.code"
              class="zlava-wrap"
            >
              <div class="zlava-type-label">Darčeková poukážka</div>
              <div
                class="uznany-kupon flex-row one-gift-card"
                @click="odstranDarcekovuPoukazku(poukazka.code)"
                style="cursor: pointer"
                title="Vymazať poukážku"
              >
                <span class="kupon-kod">
                  🎁 {{ poukazka.code }}
                  <span v-if="poukazka.value">
                    ({{ Number(poukazka.value).toFixed(2).replace(".", ",") }}
                    €)
                  </span>
                </span>
                <span class="zlava-suma">{{ giftCardDiscounts[i] }}€</span>
              </div>
            </div>
          </div>

          <p class="small" v-if="$store.state.user == null">
            Ak chcete využiť svoje osobné ponuky, prihláste sa.
          </p>

          <div class="line horizontal"></div>

          <div v-if="sposobyDopravy.length != 0" class="sposob">
            <div class="suma">
              <p><b>Spôsob dopravy</b></p>
              <p></p>
            </div>
            <div class="size-options">
              <label v-for="item in sposobyDopravy" :key="item">
                <input
                  type="radio"
                  :value="item.id"
                  v-model="sposobDopravy"
                  @change="nastavSposobDopravy(item.id)"
                />
                <span
                  >{{ item.name }} ( +
                  {{
                    (item.price / 100)
                      .toFixed(2)
                      .toString()
                      .replaceAll(".", ",")
                  }}
                  € )</span
                >
              </label>
            </div>
          </div>
          <div class="line horizontal" v-if="sposobyDopravy.length != 0"></div>

          <div class="suma">
            <p><b>Celkom</b></p>
            <p>{{ produktyCena }}€</p>
          </div>
          <div class="prijimame">
            <p>Prijímame</p>
            <img src="@/assets/images/platby/platby1.png" alt="Maestro" />
            <img src="@/assets/images/platby/platby2.png" alt="Mastercadt" />
            <img src="@/assets/images/platby/platby3.png" alt="VISA" />
          </div>

          <div class="button green">
            <a @click.prevent="pokracovanieDoPokladneValid()"
              >Pokračovať do pokladne</a
            >
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="mobile" :id="$route.name">
    <section>
      <div class="scroll">
        <TheHeadline></TheHeadline>

        <div class="line horizontal"></div>

        <div class="box">
          <div v-if="$store.state.user == null" class="box-item left">
            <p class="nadpis">Váš nákupný košík je prázdny!</p>
            <p>
              Prihláste sa, aby ste mohli ukladať položky v nákupnom košíku
              alebo sa dostať k tým, ktoré ste už uložili.
            </p>

            <router-link to="/prihlasenie" class="login button">
              <img
                src="@/assets/images/icons/prihlasenie.svg"
                alt=""
              /><router-link to="/prihlasenie">Prihlásiť sa</router-link>
            </router-link>
          </div>
          <div
            v-if="$store.state.user != null && this.userCart.length === 0"
            class="box-item left"
          >
            <p class="nadpis">Váš nákupný košík je prázdny!</p>
            <p>
              Keď pridáte niečo do košíka, zobrazí sa to tu. Stačí si vybrať.
            </p>
            <div class="second">
              <routerLink to="naucne-videa" class="box-main">
                <img
                  src="@/assets/images/gallery/NaucneVideaTitle.png"
                  alt=""
                />
                <p>Náučné videá</p>
              </routerLink>
              <routerLink to="ciselne-zapisy" class="box-main">
                <img
                  src="@/assets/images/gallery/CiselneZapisyTitle.png"
                  alt=""
                />
                <p>Číselné zápisy</p>
              </routerLink>
              <routerLink to="helishop" class="box-main">
                <img src="@/assets/images/gallery/MerchTitle.png" alt="" />
                <p>HeliShop</p>
              </routerLink>
            </div>
          </div>

          <div
            v-if="$store.state.user != null && this.userCart.length !== 0"
            class="box-item left"
          >
            <ul
              v-if="Object.keys(this.produktyData).length == userCart.length"
              class="cart"
            >
              <kosik-produkt
                v-for="item in userCart"
                :key="item.id"
                :notes="{ id: item.id, count: item.count }"
                :produktyData="produktyData[item.id_delete]"
                :umiestnenie="'kosik'"
                @zmenitKuponNacitany="nacitajCenu"
              ></kosik-produkt>
            </ul>

            <div class="line horizontal"></div>

            <div class="gift_card">
              <h4 class="m-right">PLATBA</h4>
              <p class="platba-ako m-right">Ako chcete zaplatiť?</p>
              <div
                class="a_gift-cart"
                @click="zobrazPoukazkuInput = !zobrazPoukazkuInput"
              >
                <p>Pridať darčekové karty</p>
                <img src="@/assets/images/icons/darcek.svg" alt="" />
              </div>
              <div v-if="zobrazPoukazkuInput" class="voucher-input-wrap">
                <VoucherInput
                  v-model="kodDarcekovejPoukazky"
                  placeholder="Zadajte kód darčekovej poukážky"
                  @submit.stop.prevent="pridajDarcekovuPoukazku"
                />
                <button
                  @click.stop.prevent="pridajDarcekovuPoukazku"
                  class="button"
                >
                  Pridať
                </button>
              </div>
            </div>

            <div class="debet-cart">
              <div class="line"></div>
              <p>Karta</p>
              <div class="prijimame">
                <img src="@/assets/images/platby/platby1.png" alt="Maestro" />
                <img
                  src="@/assets/images/platby/platby2.png"
                  alt="Mastercadt"
                />
                <img src="@/assets/images/platby/platby3.png" alt="VISA" />
              </div>
            </div>
          </div>
          <div class="box-item right">
            <h4>ZHRNUTIE OBJEDNÁVKY</h4>

            <div class="zlavy">
              <p>Zľavy</p>
              <p
                v-if="!chcemZadatKupon && !uznanyKupon.stav"
                @click="chcemZadatKupon = true"
                class="uplatnenie-zlavy"
              >
                Uplatniť zľavu
              </p>
              <p
                v-if="chcemZadatKupon"
                @click="chcemZadatKupon = false"
                class="uplatnenie-zlavy"
              >
                Nechcem zľavu
              </p>
              <p
                v-if="uznanyKupon.stav"
                @click="zmazanieKuponu()"
                class="uplatnenie-zlavy"
              >
                Aktívny kupón
              </p>
            </div>

            <!-- Zľavy (kupóny aj poukážky) - jedna sekcia -->
            <div class="zlavy-list">
              <!-- Kupón -->
              <div v-if="uznanyKupon.stav" class="zlava-wrap">
                <div class="zlava-type-label">Kupón</div>
                <div
                  class="uznany-kupon flex-row"
                  @click="zmazanieKuponu()"
                  style="cursor: pointer"
                  title="Vymazať kupón"
                >
                  <span class="kupon-kod">
                    {{ uznanyKupon.nazov }} ({{ uznanyKupon.hodnota }}%)
                  </span>
                  <span class="zlava-suma">-{{ produktyCenaCouponLess }}€</span>
                </div>
              </div>

              <!-- Input na kupón (nerobím úpravy, len roztiahnuť na šírku) -->
              <form v-if="chcemZadatKupon" @submit.prevent="odoslanieKuponu()">
                <div class="zlava-type-label">Kupón</div>
                <div class="row full-width">
                  <input
                    type="text"
                    placeholder="Zľavový kód"
                    name="code"
                    id="code"
                    @input="kuponZmena()"
                    @change="kuponZmena()"
                    v-model="kuponKod"
                    class="full-width"
                  />
                </div>
              </form>

              <!-- Darčeková poukážka -->
              <div
                v-for="(poukazka, i) in giftCardsInCart"
                :key="poukazka.code"
                class="zlava-wrap"
              >
                <div class="zlava-type-label">Darčeková poukážka</div>
                <div
                  class="uznany-kupon flex-row one-gift-card"
                  @click="odstranDarcekovuPoukazku(poukazka.code)"
                  style="cursor: pointer"
                  title="Vymazať poukážku"
                >
                  <span class="kupon-kod">
                    🎁 {{ poukazka.code }}
                    <span v-if="poukazka.value">
                      ({{ Number(poukazka.value).toFixed(2).replace(".", ",") }}
                      €)
                    </span>
                  </span>
                  <span class="zlava-suma">
                    {{ i === 0 ? gift_card_discount : "0,00" }}€
                  </span>
                </div>
              </div>
            </div>

            <p class="small" v-if="$store.state.user == null">
              Ak chcete využiť svoje osobné ponuky, prihláste sa.
            </p>
            <div class="line horizontal"></div>

            <div v-if="sposobyDopravy.length != 0" class="sposob">
              <div class="suma">
                <p><b>Spôsob dopravy</b></p>
                <p></p>
              </div>
              <div class="size-options">
                <label v-for="item in sposobyDopravy" :key="item">
                  <input
                    type="radio"
                    :value="item.id"
                    v-model="sposobDopravy"
                    @change="nastavSposobDopravy(item.id)"
                  />
                  <span
                    >{{ item.name }} ( +
                    {{
                      (item.price / 100)
                        .toFixed(2)
                        .toString()
                        .replaceAll(".", ",")
                    }}
                    € )</span
                  >
                </label>
              </div>
            </div>
            <div
              class="line horizontal"
              v-if="sposobyDopravy.length != 0"
            ></div>

            <div class="suma">
              <p><b>Celkom</b></p>
              <p>{{ produktyCena }}€</p>
            </div>
            <div class="prijimame">
              <p>Prijímame</p>
              <img src="@/assets/images/platby/platby1.png" alt="Maestro" />
              <img src="@/assets/images/platby/platby2.png" alt="Mastercadt" />
              <img src="@/assets/images/platby/platby3.png" alt="VISA" />
            </div>

            <div class="button green">
              <a @click.prevent="pokracovanieDoPokladneValid()"
                >Pokračovať do pokladne</a
              >
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";
import KosikProdukt from "@/components/Functionality/KosikProdukt.vue";
import VoucherInput from "@/components/Functionality/VoucherInput.vue";

export default {
  data() {
    return {
      produktyData: {},
      produktyCenaCouponLess: "",
      gift_card_discount: "",
      produktyCena: "",
      chcemZadatKupon: false,
      uznanyKupon: {
        stav: false,
        nazov: "LETO2024",
        hodnota: "10,00",
        hodnotaInt: 0,
        zlava: 0,
      },
      kuponKod: "",
      kuponNacitany: false,
      intervalId: null,
      sposobDopravy: null,
      sposobyDopravy: [],
      userCart: [],
      zobrazPoukazkuInput: false,
      kodDarcekovejPoukazky: "",
      giftCardsInCart: [],
    };
  },
  beforeUnmount() {
    clearInterval(this.intervalId);
  },
  async mounted() {
    window.scrollTo(0, 0);

    this.copyKosik();
    this.nacitajCenu();
    this.overKupon();
    // this.nacitajProdukty();

    await this.nacitajGiftCardsInCart();

    this.intervalId = setInterval(() => {
      console.log("intervaliiiik :>> ");
      console.log(Object.keys(this.produktyData).length, this.userCart.length);
      console.log(this.produktyData, this.$store.state.userCart);

      if (Object.keys(this.produktyData).length !== this.userCart.length) {
        this.nacitajProdukty();
        console.log("interval emit konší :>> ");
      }
      if (!this.kuponNacitany && this.userCart.length == 0) {
        this.nacitajProdukty();

        this.kuponNacitany = true;
      }
    }, 500);
  },

  components: { TheHeadline, KosikProdukt, VoucherInput },

  watch: {
    "$store.state.userCart": {
      handler(newVal) {
        console.log("userCart sa zmenil!", newVal);
        this.copyKosik().then(() => {
          this.nacitajProdukty();
        });
      },
      deep: true, // Sleduje aj zmeny vo vnútri objektov
    },
  },

  computed: {
    giftCardDiscounts() {
      return this.giftCardsInCart.map((gc) => {
        const raw = parseFloat(String(gc.value).replace(",", ".")) || 0;
        return raw.toFixed(2).replace(".", ",");
      });
    },
  },

  methods: {
    copyKosik() {
      return new Promise((resolve) => {
        this.userCart = JSON.parse(JSON.stringify(this.$store.state.userCart));
        resolve(); // Po vykonaní kódu môžeme vrátiť úspešné vyvolanie
      });
    },
    pokracovanieDoPokladneValid() {
      if (this.userCart.length === 0) {
        this.$store.state.SnackBarText =
          "Pre zaplatenie musíte mať vybratý aspoň 1 produkt";
        return false;
      }

      if (
        (this.sposobDopravy === null || this.sposobDopravy === 0) &&
        this.sposobyDopravy.length != 0
      ) {
        this.$store.state.SnackBarText =
          "Pred zaplatením kvôli fyzickému produktu prosím vyberte spôsob dopravy";
        return false;
      }

      //ak je všetko nad tým splnené ideme dalej
      this.$router.push("/pokladna");
      return true;
    },
    async nacitajCenu() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "cart/getCartPriceAdvandec.php",
        headers: {},
      };

      try {
        const response = await axios.request(config);
        if (response.data && response.data.data) {
          this.produktyCenaCouponLess = response.data.data["coupon_discount"];
          this.produktyCena = response.data.data["price_total"];
          // >>> TOTO SI PRIDAJ <<<
          this.gift_card_discount = response.data.data["gift_card_discount"];
          // Debug, aby si videla hodnoty
          console.log("spravna cena je :>> ", response.data.data);
          console.log("gift_card_discount je: ", this.gift_card_discount);
        } else {
          console.log("Nedostali sme správne údaje z API.");
        }
      } catch (error) {
        console.log(error);
      }
    },
    nacitajSposobyDopravy() {
      const axios = require("axios");

      let config = {
        method: "get",
        url: this.$store.state.api + "/eshop/delivery.json",
      };

      axios
        .request(config)
        .then((response) => {
          this.sposobyDopravy = response.data; // Uloží získané dáta
          console.log("this.sposobyDopravy :>> ", this.sposobyDopravy);
        })
        .catch((error) => {
          console.error("Chyba pri načítaní spôsobov dopravy:", error);
        });
    },
    nastavSposobDopravy(id) {
      const axios = require("axios");
      let doprava = "";

      if (id === -1) {
        doprava = "";
      } else {
        doprava = "?delivery=" + id; // id = "01"
      }

      let config = {
        method: "get",
        url: `${this.$store.state.api}/cart/setDelivery.php` + doprava,
      };

      axios
        .request(config)
        .then((response) => {
          console.log("Spôsob dopravy bol nastavený:", response.data);

          if (id !== -1) {
            this.nacitajCenu();
          }

          this.sposobDopravy = response.data.data.delivery; // nechaj ako string, aby ostala "01"
        })
        .catch((error) => {
          console.error("Chyba pri nastavovaní dopravy:", error);
        });
    },
    async nacitajGiftCardsInCart() {
      try {
        const axios = require("axios");
        const res = await axios.get(
          this.$store.state.api + "cart/loadGiftCard.php"
        );
        if (Array.isArray(res.data.data)) {
          this.giftCardsInCart = res.data.data
            .map((item) => (typeof item === "string" ? JSON.parse(item) : item))
            .filter((gc) => !gc.used_timestamp); // NEPOUŽÍVAJ !gc.used_by
        } else {
          this.giftCardsInCart = [];
        }
      } catch (err) {
        this.giftCardsInCart = [];
      }
    },
    async pridajDarcekovuPoukazku() {
      if (
        !this.kodDarcekovejPoukazky ||
        this.kodDarcekovejPoukazky.length < 8
      ) {
        this.$store.state.SnackBarText =
          "Zadajte platný kód darčekovej poukážky.";
        return;
      }
      this.zobrazPoukazkuInput = false;
      try {
        const axios = require("axios");
        let res = await axios.get(
          this.$store.state.api + "cart/addGiftCard.php",
          {
            params: { code: this.kodDarcekovejPoukazky },
          }
        );
        if (
          res?.data?.status === "Request succesfull" ||
          res?.data?.status === "Request fullfiled"
        ) {
          this.$store.state.SnackBarText = "Darčeková poukážka bola pridaná!";
          this.kodDarcekovejPoukazky = "";
          await this.nacitajGiftCardsInCart();
          await this.nacitajCenu();
        } else if (res?.data?.status === "Request failed") {
          if (res.data.data === "Already used") {
            this.$store.state.SnackBarText = "Táto poukážka už bola použitá.";
          } else if (res.data.data === "Already in cart") {
            this.$store.state.SnackBarText = "Táto poukážka je už v košíku.";
          } else {
            this.$store.state.SnackBarText = "Neplatný kód alebo chyba.";
          }
        } else {
          this.$store.state.SnackBarText = "Neplatný kód alebo chyba.";
        }
      } catch (err) {
        this.$store.state.SnackBarText = "Chyba komunikácie so serverom.";
      }
    },
    async odstranDarcekovuPoukazku(code) {
      try {
        const axios = require("axios");
        const res = await axios.get(
          this.$store.state.api + "cart/removeGiftCard.php",
          { params: { code } }
        );
        if (res?.data?.status === "Request fullfiled") {
          this.$store.state.SnackBarText = "Poukážka bola vymazaná z košíka.";
          await this.nacitajGiftCardsInCart();
          await this.nacitajCenu();
        } else {
          this.$store.state.SnackBarText = "Nepodarilo sa odstrániť poukážku.";
        }
      } catch (err) {
        this.$store.state.SnackBarText = "Chyba pri mazaní poukážky.";
      }
    },
    async nacitajProdukty() {
      console.log("idem nacitavať produkty :>> ");

      let docasneProdukty = {}; // dočasné úložisko produktov
      let promises = [];

      if (this.userCart != null && Array.isArray(this.userCart)) {
        this.sposobyDopravy = [];

        this.userCart.forEach((product) => {
          console.log("id, count :>> ", product.id, product.count);

          // vytvárame Promise pre každý produkt
          const promise = this.nacitajDataOProduktoch(
            product.id,
            product.count,
            product.id_delete,
            product.description
          ).then((data) => {
            if (data) {
              docasneProdukty[product.id_delete] = data;
            }
          });

          promises.push(promise);
        });

        // Počkám, kým sa všetky requesty dokončia
        await Promise.all(promises);

        console.log(
          "Načítané produkty v košíku (docasneProdukty):",
          docasneProdukty
        );

        // Uložím dáta do hlavného objektu
        this.produktyData = Object.keys(docasneProdukty).length
          ? docasneProdukty
          : {};
      } else {
        console.log("userCart je null alebo nie je pole");
        this.produktyData = {};
      }

      setTimeout(() => {
        this.overPlatnostKuponovVKosiku();
      }, 500);
    },
    nacitajKupon() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "cart/loadCoupon.php",
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request sucessfull") {
            if (response.data.data.length == 0) {
              this.uznanyKupon = [];
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

              this.uznanyKupon.stav = true;
              this.chcemZadatKupon = false;
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    overPlatnostKuponovVKosiku() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/cart/validateCouponInCart.php",
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
    async nacitajDataOProduktoch(id, count, id_delete, description) {
      // Ak ide o darčekovú poukážku, vytvoríme statický objekt
      if (typeof id === "string" && id.startsWith("gift_card")) {
        const hodnotaCent = Number(id.replace("gift_card", ""));
        const hodnotaEur = (hodnotaCent / 100).toFixed(2).replace(".", ",");

        return {
          id: id,
          id_delete: id_delete,
          name: "Darčeková poukážka",
          additional_text: "",
          hodnotaPoukazky: hodnotaEur,
          count: count,
          description: description || "",
          price: hodnotaEur,
          details: JSON.stringify({
            typ: "gift_card",
            popis: "Darčeková poukážka pre milovníkov heligónky.",
            opis: "Poukážka, ktorú je možné uplatniť pri nákupe v našom Heli shope.",
            preKohoPopis: "",
            coSaPopis: "",
            technickyPopis: "",
            obsahPopis: "",
            farby: [],
            velkosti: [],
          }),
          virtuality: "true",
          category: "poukazky",
          deleted: "false",
          free: "false",
          new: "",
          difficulty: "0",
          expiration: "never",
          images: [
            "https://heligonkovaakademia.sk/data/images/poukazkaImage.png",
          ],
        };
      }

      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
        headers: {},
      };

      try {
        const response = await axios.request(config);
        if (response.data && response.data.data) {
          const produkt = response.data.data;

          produkt.count = count;
          produkt.id_delete = id_delete;
          produkt.description = description;

          // nahradí bodku za čiarku v cene
          produkt.price = produkt.price.replace(".", ",");

          // Ak zistíme, že produkt nie je virtuálny, načítame dopravu
          if (
            this.sposobyDopravy.length === 0 &&
            produkt.virtuality === "false"
          ) {
            this.nacitajSposobyDopravy();
            this.nastavSposobDopravy(-1);
          }

          return produkt;
        } else {
          console.log("Nedostali sme správne údaje z API.");
          return null;
        }
      } catch (error) {
        console.log(error);
        return null;
      }
    },
    kuponZmena() {
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.overKupon();
      }, 500);
    },
    overKupon() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api + "/cart/addCoupon.php/?code=" + this.kuponKod,
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request fullfiled") {
            this.$store.state.SnackBarText = "Kupón sa úspešne použil";
            this.nacitajKupon();
            this.nacitajCenu();
            this.chcemZadatKupon = false;
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
          "/cart/removeCoupon.php/?code=" +
          this.uznanyKupon.nazov,
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request fullfiled") {
            this.nacitajKupon();
            this.nacitajCenu();
            this.$store.state.SnackBarText = "Kupón bol vymazaný";
            this.uznanyKupon.stav = false;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.box {
  margin-top: 1em;
  padding: 0 3%;
  align-items: flex-start;
  justify-content: space-around;
}

.box:has(.cart) {
  gap: 10%;
}

.second {
  display: flex;
  align-items: flex-end;
  justify-content: center;
  gap: 5%;

  .box-main {
    display: flex;
    flex-direction: column;
    align-items: center;

    p {
      white-space: nowrap;
      margin-top: 1em;
    }

    &:nth-child(2) > img {
      transform: scale(1.07) translateY(-0.2em);
    }

    &:hover {
      transform: scale(1.15);
      transition-duration: 0.1s;
    }
  }

  img {
    width: 9vw;
  }

  p {
    font-size: 100%;
  }

  a:not(:last-child) p {
    margin-right: 10%;
  }

  a:last-child p {
    margin-right: 5%;
  }
}

.scroll > .line {
  width: 80%;
  margin: auto;
  margin-top: 1em;
}

h4 {
  font-size: 1.9em;
  text-align: center;

  line-height: 115%;
  /* 2.51563rem */
  letter-spacing: 0.1em;
}

.mobile h4 {
  margin: 0.5em 0 0;
}

.box-item {
  justify-content: center !important;
  gap: 7%;
}

.box-item:last-child > div:not(.button) {
  display: flex;
  width: 100%;
  align-items: center;
  justify-content: flex-start;
  margin: 0.6em auto;

  & p:last-child {
    margin-left: auto;
  }
}

section p {
  text-align: center;
  font-size: 1.5625em;
  font-weight: 400;
  line-height: 115%;
}

.prijimame p,
.small {
  font-size: 1em;
  margin: 0.6em 0;
}

.prijimame {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  align-items: center;
  gap: 5%;

  img:nth-child(1) {
    width: auto;
    height: 1.75rem;
  }

  img:nth-child(2) {
    width: auto;
    height: 1.4375rem;
  }

  img:nth-child(3) {
    width: auto;
    height: 1.4375rem;
  }
}

.size-options {
  display: flex;
  gap: 10px;
  margin: 1em auto 1.3em 0;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
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

  display: flex;
  flex-direction: row;
  align-items: center;
  width: 100%;
  justify-content: center;
  // padding: 0.3em;
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
  // height: -webkit-fill-available;
  height: inherit;
}

.sposob .suma {
  display: flex;
  justify-content: flex-start;
  width: 100%;
}

.button {
  margin-top: 1em;
  padding: 0.3em 0.7em;
}

.button:not(.login) a {
  font-size: 90%;
}

.nadpis {
  font-size: 2.1875em;
  font-style: normal;
  font-weight: 600;
}

.right {
  flex: 1.5;
  max-width: 24em;
  min-width: 24em;

  position: sticky;
  top: 0;
}

.left {
  flex: 2;
  max-width: 35em;
  align-self: center;
  margin-top: 40px;

  & > p:nth-child(2) {
    margin: 0.7em 0;
  }

  .button {
    margin-top: 0;
  }
}

//košík

.cart {
  width: 100%;
  display: flex;
  gap: 1.5em;
  flex-direction: column;
  height: auto;
  margin-bottom: 2em;
}

.zlava-type-label {
  font-size: 1.12em;
  font-weight: 700;
  color: #212121;
  margin-bottom: 0.5em;
  margin-left: 0.2em;
  margin-top: 0.4em;
  text-align: left;
  letter-spacing: 0.02em;
  width: 100%;
}

.zlava-type-label.left {
  text-align: left;
  align-items: flex-start;
}

.zlava-wrap {
  margin-bottom: 0.44em;
  width: 100%;
  align-items: flex-start;
  display: flex;
  flex-direction: column;
}

.zlavy-list {
  width: 100%;
  margin-top: 0.5em;
  display: flex;
  flex-direction: column;
  gap: 0.15em;
  align-items: flex-start;
}

.uznany-kupon.flex-row,
.one-gift-card.flex-row {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 1em;
  background: #90ca50;
  border-radius: 1em;
  font-size: 1.2em;
  box-sizing: border-box;
  padding: 0.25em 0.7em;
  cursor: pointer;
  transition: background 0.23s, transform 0.13s;
  width: 100%;

  &:hover {
    background: #9fd266;
    transform: scale(0.97);
  }
}

.zlava-type {
  font-size: 0.95em;
  font-weight: 600;
  color: #2d4422;
  margin-right: 1em;
  min-width: 5.2em;
}

.kupon-kod {
  font-size: 0.98em;
  font-weight: 500;
  color: #232323;
  flex: 1 1 auto;
  text-align: left;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 7em;
}

.zlava-suma {
  font-size: 1.06em;
  font-weight: bold;
  color: #fff134;
  margin-left: auto;
  min-width: 4.2em;
  text-align: right;
}

.uznany-kupon,
.active-discount {
  font-size: 90%;
  background: #90ca50;
  padding: 0.5em 1em;
  border-radius: 1em;
  box-sizing: border-box;
  transition-duration: 0.5s;

  &:hover {
    transform: scale(0.97);
    transition-duration: 0.2s;
    background: #9fd266;
    cursor: pointer;
  }
}

.sposob {
  display: flex;
  flex-direction: column;
  align-items: flex-start !important;
}

.debet-cart {
  display: flex;
  width: 100%;
  align-items: center;
  gap: 3%;
  height: 3em;
  margin-top: 1em;

  p {
    font-size: 1.55em;

    font-weight: 700;
    line-height: 120%;
    /* 1.875rem */
  }

  .prijimame {
    margin-left: auto;
    font-size: 90%;
  }

  .line {
    width: 0.3rem;
    align-self: stretch;
  }
}

.gift_card {
  flex-direction: column;
  align-items: flex-start !important;
  gap: 1em;
  display: flex;
  width: 100%;
  margin-top: 2em;

  h4 {
    margin: 0 0 1em 0;
  }

  .platba-ako {
    font-weight: bold;
    margin-bottom: 0.5em;
  }

  .a_gift-cart {
    width: 100%;
    display: flex;
    justify-content: space-between;
    cursor: pointer;
  }

  .a_gift-cart p:hover {
    text-decoration: underline;
  }
}

:deep(.voucher-input-wrap) {
  width: 100%;

  .button {
    margin-top: 0.5em;
    justify-content: center;
    width: 60%;
    font-family: "Adumu", sans-serif;
  }
}

:deep(.voucher-input) {
  width: 100%;
  align-items: flex-start;

  .row {
    width: 100%;
  }
}

.added-gift-cards {
  width: 100%;
  flex-direction: column;

  .one-gift-card {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 1.2em;
  }

  .remove-gift-card {
    cursor: pointer;
  }
}

@media only screen and (max-width: 1400px) {
  .box:has(.cart) {
    font-size: 85%;
  }
}

@media screen and (max-width: 1000px) {
  .second img {
    width: 16vw;
  }

  section p {
    margin: 0.3em 0;
  }

  .box {
    align-items: center;
  }

  .small {
    font-size: 1.4em;
  }

  .left,
  .right {
    max-width: none;
    min-width: unset;

    width: 100%;
  }

  .nadpis,
  h4 {
    margin-bottom: 0.8em;
  }

  .right .button {
    padding: 0.5em 1.5em;
  }

  .left .button {
    display: none;
  }

  .left {
    margin-bottom: 0.5em;
    margin-top: 2em;
  }

  .zlava-type-label .left {
    margin-bottom: 0.5em !important;
  }
}

@media only screen and (max-width: 750px) {
  .second img {
    width: 23vw;
  }

  .nadpis {
    font-size: 1.9em;
    font-style: normal;
    font-weight: 500;
  }

  section p {
    text-align: center;
    font-size: 1.4em;
    font-weight: 400;
    line-height: 115%;
  }

  .mobile {
    section p {
      margin: 0.3em 0;
    }

    .box {
      align-items: center;
    }

    .small {
      font-size: 3vw;
      letter-spacing: 0.2vw;
    }

    .left,
    .right {
      max-width: none;
      min-width: unset;

      width: 100%;
    }

    .nadpis,
    h4 {
      margin-bottom: 0.4em;
    }

    .right .button {
      padding: 0.5em 1.5em;
      font-size: 4vw;
    }

    .left .button {
      display: none;
    }

    .left {
      margin-bottom: 3em;
      margin-top: 0;
    }
  }

  .mobile section {
    // margin-bottom: 10em;
    height: auto;
  }

  .mobile .scroll {
    padding-bottom: 2em;
  }

  .zlava-type-label .left {
    margin-bottom: 0.5em !important;
  }
}

@media (max-width: 600px) {
  .uznany-kupon.flex-row,
  .one-gift-card.flex-row {
    font-size: 1.2em;
    padding: 0.19em 0.45em;
    gap: 0.4em;
  }

  .zlava-type {
    font-size: 1.2em;
    min-width: 3.8em;
    margin-right: 0.6em;
  }

  .kupon-kod {
    font-size: 1.2em;
    min-width: 5em;
  }

  .zlava-suma {
    font-size: 1.2em;
    min-width: 2.5em;
  }
}

@media only screen and (max-width: 450px) {
  .mobile section h4 {
    font-size: 5.5vw;
  }

  .cart {
    font-size: 85%;
  }
}
</style>
