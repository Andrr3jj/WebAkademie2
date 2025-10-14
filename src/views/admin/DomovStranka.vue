<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>

      <div
        v-if="
          $store.state.progresEmailov &&
          $store.state.progresEmailov.active &&
          $store.state.progresEmailov.odoslane <
            $store.state.progresEmailov.celkovo
        "
        class="progress-box"
      >
        <p>
          Odosielajú sa emaily:
          {{ $store.state.progresEmailov.odoslane }} /
          {{ $store.state.progresEmailov.celkovo }}
        </p>
      </div>

      <div class="line horizontal w-80"></div>

      <div class="table">
        <div class="column">
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes(
                  'numericRecord_create'
                ),
            }"
            class="tlacidlo"
          >
            <p class="text">Číselný zápis:</p>

            <router-link class="button" to="/admin/pridanie-zapisu">
              <p>Pridať pieseň</p>
            </router-link>
          </div>
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('video_create'),
            }"
            class="tlacidlo"
          >
            <p class="text">Náučné videá:</p>

            <router-link class="button" to="/admin/pridanie-videa">
              <p>Pridať video</p>
            </router-link>
          </div>
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('merch_create'),
            }"
            class="tlacidlo"
          >
            <p class="text">HeliShop:</p>

            <router-link class="button" to="/admin/pridanie-polozky-helishop">
              <p>Pridať položku</p>
            </router-link>
          </div>

          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes(
                  'subscription_edit'
                ),
            }"
            class="tlacidlo"
          >
            <p class="text">Predplatné:</p>
            <router-link class="button" to="/admin/predplatne">
              <p>Upraviť</p>
            </router-link>
          </div>
        </div>
        <div class="line"></div>
        <div class="column">
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes(
                  'numericRecord_edit'
                ),
            }"
            class="tlacidlo"
          >
            <p class="text">Zoznam zápisov:</p>

            <router-link class="button" to="/admin/zoznam-zapisov">
              <p>Zobraziť</p>
            </router-link>
          </div>
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('video_edit'),
            }"
            class="tlacidlo"
          >
            <p class="text">Zoznam videí:</p>

            <router-link class="button" to="/admin/zoznam-videi">
              <p>Zobraziť</p>
            </router-link>
          </div>
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('merch_edit'),
            }"
            class="tlacidlo"
          >
            <p class="text">Zoznam HeliShop:</p>

            <router-link class="button" to="/admin/zoznam-poloziek-helishop">
              <p>Zobraziť</p>
            </router-link>
          </div>
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('coupon_master'),
            }"
            class="tlacidlo"
          >
            <p class="text">Zľavy a kupóny:</p>
            <router-link class="button" to="/admin/zlavy">
              <p>Pridať</p>
            </router-link>
          </div>
        </div>
        <div class="line"></div>
        <div class="column">
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('record_create'),
            }"
            class="tlacidlo"
          >
            <p class="text">Editor zápisov:</p>

            <router-link class="button" to="/admin/editor-zapisov">
              <p>Zobraziť</p>
            </router-link>
          </div>
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('user_master'),
            }"
            class="tlacidlo"
          >
            <p class="text">Zoznam uživateľov:</p>

            <router-link class="button" to="/admin/zoznam-uzivatelov">
              <p>Zobraziť</p>
            </router-link>
          </div>
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes(
                  'helishop_manager'
                ),
            }"
            class="tlacidlo"
          >
            <p class="text">Objednávky:</p>

            <router-link class="button" to="/admin/objednavky-helishop">
              <p>Zobraziť</p>
            </router-link>
          </div>

          <div
            class="tlacidlo"
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('coupon_master'),
            }"
          >
            <p class="text">Darček poukážky:</p>

            <router-link
              class="button"
              to="/admin/generovanie-darcekove-poukazky"
            >
              <p>Generovať</p>
            </router-link>
          </div>
        </div>
      </div>
      <div class="line horizontal w-80"></div>
      <div class="table">
        <div class="column">
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('mail_master'),
            }"
            class="tlacidlo"
          >
            <p class="text">Reklamný email:</p>
            <router-link class="button" to="/admin/reklamny-email">
              <p>Odoslať</p>
            </router-link>
          </div>

          <p class="text">Predplatia:</p>

          <div
            @click="$router.push('/admin/zoznam-predplatitelov')"
            class="predplatne"
            :class="{
              'not-see':
                !this.$store.state.userRoles?.roles?.includes(
                  'subscription_edit'
                ),
            }"
          >
            <div class="typ">
              <div class="nadpis">
                <p class="Adumu">Mesačné</p>
              </div>
              <div class="cislo">
                <p>
                  {{
                    this.predplatneData["Predplatne - neopakujuce sa"][
                      "mesacne"
                    ]
                  }}
                </p>
              </div>
            </div>
            <div class="typ">
              <div class="nadpis">
                <p class="Adumu">Polročné</p>
              </div>
              <div class="cislo">
                <p>
                  {{
                    this.predplatneData["Predplatne - neopakujuce sa"][
                      "pol rocne"
                    ]
                  }}
                </p>
              </div>
            </div>
            <div class="typ">
              <div class="nadpis">
                <p class="Adumu">Ročné</p>
              </div>
              <div class="cislo">
                <p>
                  {{
                    this.predplatneData["Predplatne - neopakujuce sa"]["rocne"]
                  }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="column">
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes(
                  'numericRecord_create'
                ),
            }"
            class="tlacidlo"
          >
            <p class="text">Textový sys:</p>
            <router-link class="button" to="/admin/textovy-system">
              <p>Editovať</p>
            </router-link>
          </div>
        </div>
        <div class="column"></div>
        <div class="column">
          <div
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes('invoice_master'),
            }"
            class="tlacidlo"
          >
            <p class="text">Faktúry:</p>
            <router-link class="button" to="/admin/zoznam-faktur">
              <p>Zobraziť</p>
            </router-link>
          </div>
        </div>
      </div>
      <div class="line horizontal w-80"></div>

      <div class="table">
        <div class="column">
          <router-link
            class="button"
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes(
                  'edupageRegistrationsManager'
                ),
            }"
            to="/admin/online-prihlasky"
          >
            <p>Administrácia prihlášok</p>
          </router-link>
          <router-link
            :class="{
              'not-have-permission':
                !this.$store.state.userRoles?.roles?.includes(
                  'subscription_edit'
                ),
            }"
            class="button"
            to="/admin/nastavenia-helicasu"
          >
            <p>Nastavenie configurácie heličasu</p>
          </router-link>
        </div>
        <div class="line" />
        <div class="column">
          <p class="text">🧪 Testovacie linky:</p>

          <router-link class="button" to="/admin/kalendar">
            <p>Edupage kalendár</p>
          </router-link>
          <router-link class="button" to="/admin/platobne-prikazy">
            <p>Edupage kalendár</p>
          </router-link>
          <router-link class="button" to="/ucebna/helicas-body">
            <p>Helibody</p>
          </router-link>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);
    this.nacitajDataOPredplatnych();
  },
  data() {
    return { predplatneData: {} };
  },
  computed: {
    progresEmailov() {
      return this.$store.state.progresEmailov;
    },
  },
  methods: {
    nacitajDataOPredplatnych() {
      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/subscription/stats.php",
      };
      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.predplatneData = response.data.data;
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

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 3px rgba(0, 0, 0, 0.25);
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

.progress-box {
  background: #fff8dc;
  border: 2px solid #fef35a;
  padding: 1em 2em;
  margin: 1em auto;
  text-align: center;
  font-weight: bold;
  font-size: 1.2em;
  border-radius: 1em;
  color: #444;
  box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
}

h5 {
  text-align: center;
  font-size: 2.3em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  margin: 0 0 0.3em 0;
}

.table {
  display: flex;
  align-items: stretch;
  justify-content: space-between;
  gap: 2em;
  padding: 3em 10%;
}

.column {
  display: flex;
  flex-direction: column;
  gap: 1.5em;
  width: 25%;
  max-width: 14em;
}

.tlacidlo {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.text {
  font-size: 1.5em;
  margin-bottom: 0.2em;
  margin-right: auto;
  font-family: "Bahnschrift", sans-serif;
  font-weight: 600;
}

.button p {
  font-size: 0.73em;
}

.button {
  font-family: "Adumu", sans-serif;
  border-radius: 1.2rem;
  padding: 0.5em 1em;
  justify-content: center;
  width: -webkit-fill-available;
}

.block {
  cursor: not-allowed;
  .button {
    z-index: -1;
  }
}

.nadpis {
  border-radius: 1.5625rem;
  border: 0.3em solid #fef35a;
  background: var(
    --Linear,
    linear-gradient(140deg, #90c94f 35.64%, #fef35a 99.4%)
  );
  box-shadow: 5px 5px 10px 0 rgba(0, 0, 0, 0.35);
  padding: 0.3em 1.3em 0.5em;
  width: 80%;
  max-width: 14em;
  margin: 1em auto 0em;
  font-size: 0.6em;
  border-radius: 1rem;
  padding: 0.3em 2em;
  p {
    font-size: 2.5em;
    font-weight: 500;
  }
}

.predplatne {
  display: flex;
  flex-direction: column;
  margin-top: -15%;
  .typ {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
  }
  .cislo {
    font-size: 1.5em;
    margin-left: 12%;
  }
}

@media only screen and (max-width: 1500px) {
  .table {
    padding: 3em 2%;
  }
}

/* ===== MOBIL: max 450px – nič nemením v štruktúre, len skrývam a jemne upravujem ===== */
@media only screen and (max-width: 450px) {
  /* Skryť len vybrané položky podľa href (bez zásahu do templatu) */
  .tlacidlo:has(a[href*="/admin/pridanie-zapisu"]),
  .tlacidlo:has(a[href*="/admin/pridanie-videa"]),
  .tlacidlo:has(a[href*="/admin/pridanie-polozky-helishop"]),
  .tlacidlo:has(a[href*="/admin/predplatne"]),
  .tlacidlo:has(a[href*="/admin/zoznam-videi"]),
  .tlacidlo:has(a[href*="/admin/zlavy"]),
  .tlacidlo:has(a[href*="/admin/editor-zapisov"]),
  .tlacidlo:has(a[href*="/admin/generovanie-darcekove-poukazky"]),
  .tlacidlo:has(a[href*="/admin/reklamny-email"]),
  .tlacidlo:has(a[href*="/admin/textovy-system"]) {
    display: none !important;
  }

  /* Layout – stĺpcovo, širšie tlačidlá */
  .table {
    flex-direction: column;
    align-items: center;
    gap: 1.2em;
    padding: 1.5em 5%;
  }

  .column {
    width: 100%;
    max-width: none;
    gap: 1em;
    align-items: center;
  }

  .text {
    font-family: "Bahnschrift", sans-serif;
    font-size: 2;
    font-weight: 400;
    text-align: center;
    margin-right: 0;
  }

  .button {
    font-family: "Adumu", sans-serif;
    border-radius: 1rem;
  }
  .button p {
    font-size: 1em;
    margin: 0;
  }

  h1 {
    font-size: clamp(3.2em, 7.2vw, 3em);
    padding: 0.4em 0;
  }
}
</style>
