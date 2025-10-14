<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <div class="product">
        <div class="left">
          <div class="image">
            <img
              v-if="produktyData.images.length > 1"
              class="arrow left-arrow"
              @click="prevImage"
              src="@/assets/images/gallery/vlavo.png"
            />
            <img
              class="main-image"
              :src="mainImage"
              alt="Hlavný obrázok"
              :class="{ 'bez-shadow': produktyData.category === 'poukazka' }"
            />
            <img
              v-if="produktyData.images.length > 1"
              class="arrow right-arrow"
              @click="nextImage"
              src="@/assets/images/gallery/vpravo.png"
            />
          </div>

          <div
            v-if="produktyData.images.length > 1"
            class="gallery"
            ref="gallery"
            @mousedown="startDrag"
            @mousemove="onDrag"
            @mouseup="endDrag"
            @mouseleave="endDrag"
            @touchstart="startDrag"
            @touchmove="onDrag"
            @touchend="endDrag"
          >
            <img
              v-for="(image, index) in produktyData.images"
              :key="index"
              class="gallery-image"
              :src="image"
              :class="{ active: mainImage === image }"
              @click="moveToMain(index)"
              alt="Galéria"
            />
          </div>
          <div
            v-if="
              viditelnePolia() &&
              Array.isArray(viditelnePolia()) &&
              viditelnePolia().includes('pre-koho') &&
              produktyData.details.preKohoPopis.length >= 3
            "
            class="pre-koho"
          >
            <p class="main-text">Pre koho je kniha určená?</p>
            <p class="text">
              {{ produktyData.details.preKohoPopis }}
            </p>
          </div>
          <div
            v-if="
              viditelnePolia() &&
              Array.isArray(viditelnePolia()) &&
              viditelnePolia().includes('informacie')
            "
            class="informacie"
          >
            <p class="main-text">Informácie k poukážke:</p>
            <p class="text">
              <strong>Platnosť:</strong> 12 mesiacov od zakúpenia <br />
              <strong>Dodanie:</strong> Elektronicky na e-mail(ihneď po
              zaplatení) <br />
              <strong>Použitie:</strong> Pri nákupe stačí v košíku zadať kód z
              poukážky. Postupné čerpanie: Ak nevyčerpáte celú sumu naraz,
              zostatok môžete využiť pri ďalšom nákupe. <br />
              <strong>Kontrola zostatku:</strong> Stavpoukážky si môžete
              kedykoľvek overiť vo svojom profile po zadaní kódu.
            </p>
          </div>
        </div>
        <div class="right">
          <h1>Heli Shop</h1>
          <div class="nadpis">
            <p>{{ produktyData.name }}</p>
          </div>
          <div class="podnadpis">
            <p class="pw-small" v-if="produktyData.additional_text.length > 3">
              {{ produktyData.additional_text }}
            </p>

            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('poradie-cislo') &&
                produktyData.details.vydanie.length > 0
              "
              class="poradie"
            >
              <p class="poradie-cislo Adumu">
                {{ produktyData.details.vydanie }}.
              </p>
              <img
                src="@/assets/images/icons/KnizkaPoradie.png"
                alt="Poradie knižky"
              />
            </div>
          </div>

          <div class="line horizontal"></div>

          <div v-if="produktyData.details.popis.length >= 3" class="popis">
            <p class="text">
              {{ produktyData.details.popis }}
            </p>
          </div>

          <div
            v-if="
              viditelnePolia() &&
              Array.isArray(viditelnePolia()) &&
              viditelnePolia().includes('opis') &&
              produktyData.details.opis.length >= 3
            "
            class="opis"
          >
            <p class="main-text">Opis produktu:</p>
            <p class="text">
              {{ produktyData.details.opis }}
            </p>
          </div>
          <div
            v-if="
              viditelnePolia() &&
              Array.isArray(viditelnePolia()) &&
              viditelnePolia().includes('co-sa') &&
              produktyData.details.coSaPopis.length >= 3
            "
            class="opis"
          >
            <p class="main-text">Čo sa v knihe naučíš?:</p>
            <p class="text text-sm">
              {{ produktyData.details.coSaPopis }}
            </p>
          </div>
          <div
            v-if="
              viditelnePolia() &&
              Array.isArray(viditelnePolia()) &&
              viditelnePolia().includes('technicke') &&
              produktyData.details.technickyPopis.length >= 3
            "
            class="opis"
          >
            <p class="main-text">Technické parametre:</p>
            <p class="text text-sm">
              {{ produktyData.details.technickyPopis }}
            </p>
          </div>

          <div
            v-if="
              viditelnePolia() &&
              Array.isArray(viditelnePolia()) &&
              viditelnePolia().includes('obsah') &&
              produktyData.details.obsahPopis.length >= 3
            "
            class="button obsah-button"
          >
            <div @click="obsahKnizky = !obsahKnizky" class="rozdeleny-button">
              <p>Zobraziť obsah knižky</p>
            </div>
          </div>

          <div v-if="obsahKnizky" class="obsah-stlpce">
            <div class="stlpec">
              <p
                v-for="(riadok, index) in obsahRozdelenyNaStlpce.lavyStlpec"
                :key="'lavy-' + index"
                class="text text-sm"
                :style="{ minHeight: '1.4em' }"
              >
                {{ riadok === "" ? "\u00A0" : riadok }}
              </p>
            </div>

            <div class="stlpec">
              <p
                v-for="(riadok, index) in obsahRozdelenyNaStlpce.pravyStlpec"
                :key="'pravy-' + index"
                class="text text-sm"
                :style="{ minHeight: '1.4em' }"
              >
                {{ riadok === "" ? "\u00A0" : riadok }}
              </p>
            </div>
          </div>

          <div class="interactive">
            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('hodnoty')
              "
              class="hodnoty"
            >
              <p class="main-text bigger-margin">Dostupné hodnoty:</p>
              <div class="size-options">
                <label
                  v-for="hodnota in [30, 50, 100, 150, 200]"
                  :key="hodnota"
                  :class="{ 'active-hodnota': vybranaHodnota == hodnota }"
                >
                  <input
                    type="radio"
                    name="hodnota"
                    :value="hodnota"
                    v-model="vybranaHodnota"
                  />
                  <span>{{ hodnota }}€</span>
                </label>
              </div>
            </div>

            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('farba') &&
                Array.isArray(produktyData.details.farby) &&
                produktyData.details.farby.length > 0
              "
              class="farba"
            >
              <p class="main-text bigger-margin">Farba:</p>
              <div class="farby">
                <div
                  v-for="(color, index) in produktyData.details.farby"
                  :key="index"
                  class="farba-circle"
                >
                  <label>
                    <input
                      type="radio"
                      name="farba"
                      :value="color"
                      v-model="selectedColor"
                    />
                    <div
                      :class="[
                        'farba-inner-circle',
                        { selected: selectedColor === color },
                      ]"
                      :style="{ backgroundColor: getColor(color) }"
                    ></div>
                  </label>
                </div>
              </div>
            </div>
            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('velkost') &&
                Array.isArray(produktyData.details.velkosti) &&
                produktyData.details.velkosti.length > 0
              "
              class="velkost"
            >
              <p class="main-text bigger-margin">Veľkosť:</p>
              <div class="size-options">
                <label
                  v-for="velkost in produktyData.details.velkosti"
                  :key="velkost"
                >
                  <input
                    type="radio"
                    name="velkosti"
                    :value="velkost"
                    v-model="selectedSize"
                  />
                  <span>{{ velkost }}</span>
                </label>
              </div>
            </div>

            <div class="pocet-kusov">
              <p class="main-text bigger-margin">Počet kusov:</p>
              <div class="vyber">
                <img
                  @click="pocetKS == 1 ? pocetKS : pocetKS--"
                  src="@/assets/images/icons/minus.png"
                  alt=""
                />
                <input type="number" v-model="pocetKS" min="1" />
                <img
                  @click="pocetKS++"
                  src="@/assets/images/icons/plus.png"
                  alt=""
                />
              </div>
            </div>

            <div class="cena">
              <p class="main-text bigger-margin">Cena:</p>
              <div class="cena-box">
                <div class="cena-cislo">
                  <p v-if="produktyData.discount_active === 'true'">
                    <strong>{{ vypocitanaCena }}€</strong>
                  </p>
                  <p v-else>
                    {{
                      (
                        parseFloat(produktyData.price.replace(",", ".")) *
                        pocetKS
                      )
                        .toFixed(2)
                        .replace(".", ",")
                    }}€
                  </p>
                </div>
                <div
                  v-if="produktyData.discount_active === 'true'"
                  class="cena-akcia"
                >
                  <p class="akcia">Zľava-{{ produktyData.discount }}%</p>
                  <p>
                    {{
                      (
                        parseFloat(produktyData.price.replace(",", ".")) *
                        pocetKS
                      )
                        .toFixed(2)
                        .replace(".", ",")
                    }}€
                  </p>
                </div>
              </div>
            </div>

            <div class="akcie">
              <div class="button">
                <div @click="pridajDoKosika()" class="rozdeleny-button">
                  <img
                    src="@/assets/images/icons/taska.svg"
                    alt="Pridanie do košika"
                  />
                  <p>Do košíka</p>
                </div>
              </div>

              <div v-if="vKosiku" class="button green">
                <div
                  @click="$router.push('/pokladna')"
                  class="rozdeleny-button"
                >
                  <p>Pokračovať do pokladne</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="line end horizontal w-90"></div>

      <p class="info-text">Ďalšie produkty, ktoré by sa vám mohli páčiť</p>

      <zoznam-produktov :pocet="3"></zoznam-produktov>
    </div>
  </section>

  <div :id="$route.name + 'mobil'" class="mobile">
    <section>
      <div class="scroll">
        <div class="product">
          <div class="left">
            <h1>Heli Shop</h1>
            <div class="nadpis">
              <p>{{ produktyData.name }}</p>
            </div>
            <div class="podnadpis">
              <p
                class="pw-small"
                v-if="produktyData.additional_text.length > 3"
              >
                {{ produktyData.additional_text }}
              </p>

              <div
                v-if="
                  viditelnePolia() &&
                  Array.isArray(viditelnePolia()) &&
                  viditelnePolia().includes('poradie-cislo') &&
                  produktyData.details.vydanie.length > 0
                "
                class="poradie"
              >
                <p class="poradie-cislo Adumu">
                  {{ produktyData.details.vydanie }}.
                </p>
                <img
                  src="@/assets/images/icons/KnizkaPoradie.png"
                  alt="Poradie knižky"
                />
              </div>
            </div>

            <div class="image">
              <img
                v-if="produktyData.images.length > 1"
                class="arrow left-arrow"
                @click="prevImage"
                src="@/assets/images/gallery/vlavo.png"
              />
              <img
                class="main-image"
                :src="mainImage"
                alt="Hlavný obrázok"
                :class="{ 'bez-shadow': produktyData.category === 'poukazka' }"
              />
              <img
                v-if="produktyData.images.length > 1"
                class="arrow right-arrow"
                @click="nextImage"
                src="@/assets/images/gallery/vpravo.png"
              />
            </div>

            <div
              v-if="produktyData.images.length > 1"
              class="gallery"
              @mousedown="startDrag"
              @mousemove="onDrag"
              @mouseup="endDrag"
              @mouseleave="endDrag"
              @touchstart="startDrag"
              @touchmove="onDrag"
              @touchend="endDrag"
            >
              <img
                v-for="(image, index) in produktyData.images"
                :key="index"
                class="gallery-image"
                :src="image"
                :class="{ active: mainImage === image }"
                @click="moveToMain(index)"
                alt="Galéria"
              />
            </div>
            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('pre-koho') &&
                produktyData.details.preKohoPopis.length > 3
              "
              class="pre-koho"
            >
              <p class="main-text">Pre koho je kniha určená?</p>
              <p class="text">
                {{ produktyData.details.preKohoPopis }}
              </p>
            </div>
            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('informacie')
              "
              class="informacie"
            >
              <p class="main-text">Informácie k poukážke:</p>
              <p class="text">
                <strong>Platnosť:</strong> 12 mesiacov od zakúpenia <br />
                <strong>Dodanie:</strong> Elektronicky na e-mail(ihneď po
                zaplatení) <br />
                <strong>Použitie:</strong> Pri nákupe stačí v košíku zadať kód z
                poukážky. Postupné čerpanie: Ak nevyčerpáte celú sumu naraz,
                zostatok môžete využiť pri ďalšom nákupe. <br />
                <strong>Kontrola zostatku:</strong> Stavpoukážky si môžete
                kedykoľvek overiť vo svojom profile po zadaní kódu.
              </p>
            </div>
          </div>
          <div class="right">
            <div class="line horizontal"></div>

            <div class="popis">
              <p class="text">
                {{ produktyData.details.popis }}
              </p>
            </div>

            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('opis') &&
                produktyData.details.opis.length > 3
              "
              class="opis"
            >
              <p class="main-text">Opis produktu:</p>
              <p class="text">
                {{ produktyData.details.opis }}
              </p>
            </div>

            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('co-sa') &&
                produktyData.details.coSaPopis.length > 3
              "
              class="opis"
            >
              <p class="main-text">Čo sa v knihe naučíš?:</p>
              <p class="text text-sm">
                {{ produktyData.details.coSaPopis }}
              </p>
            </div>

            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('technicke') &&
                produktyData.details.technickyPopis.length > 3
              "
              class="opis"
            >
              <p class="main-text">Technické parametre:</p>
              <p class="text text-sm">
                {{ produktyData.details.technickyPopis }}
              </p>
            </div>

            <div
              v-if="
                viditelnePolia() &&
                Array.isArray(viditelnePolia()) &&
                viditelnePolia().includes('obsah') &&
                produktyData.details.obsahPopis.length > 3
              "
              class="button obsah-button"
            >
              <div @click="obsahKnizky = !obsahKnizky" class="rozdeleny-button">
                <p>Zobraziť obsah knižky</p>
              </div>
            </div>

            <div v-if="obsahKnizky" class="obsah">
              <p class="text text-sm">
                {{ produktyData.details.obsahPopis }}
              </p>
            </div>

            <div class="interactive">
              <div
                v-if="
                  viditelnePolia() &&
                  Array.isArray(viditelnePolia()) &&
                  viditelnePolia().includes('hodnoty')
                "
                class="hodnoty"
              >
                <p class="main-text bigger-margin">Dostupné hodnoty:</p>
                <div class="size-options">
                  <label
                    v-for="hodnota in [30, 50, 100, 150, 200]"
                    :key="hodnota"
                    :class="{ 'active-hodnota': vybranaHodnota == hodnota }"
                  >
                    <input
                      type="radio"
                      name="hodnota"
                      :value="hodnota"
                      v-model="vybranaHodnota"
                    />
                    <span>{{ hodnota }}€</span>
                  </label>
                </div>
              </div>
              <div
                v-if="
                  viditelnePolia() &&
                  Array.isArray(viditelnePolia()) &&
                  viditelnePolia().includes('farba') &&
                  Array.isArray(produktyData.details.farby) &&
                  produktyData.details.farby.length > 0
                "
                class="farba"
              >
                <p class="main-text bigger-margin">Farba:</p>
                <div class="farby">
                  <div
                    v-for="(color, index) in produktyData.details.farby"
                    :key="index"
                    class="farba-circle"
                  >
                    <label>
                      <input
                        type="radio"
                        name="farba"
                        :value="color"
                        v-model="selectedColor"
                      />
                      <div
                        :class="[
                          'farba-inner-circle',
                          { selected: selectedColor === color },
                        ]"
                        :style="{ backgroundColor: getColor(color) }"
                      ></div>
                    </label>
                  </div>
                </div>
              </div>
              <div
                v-if="
                  viditelnePolia() &&
                  Array.isArray(viditelnePolia()) &&
                  viditelnePolia().includes('velkost') &&
                  Array.isArray(produktyData.details.velkosti) &&
                  produktyData.details.velkosti.length > 0
                "
                class="velkost"
              >
                <p class="main-text bigger-margin">Veľkosť:</p>
                <div class="size-options">
                  <label
                    v-for="velkost in produktyData.details.velkosti"
                    :key="velkost"
                  >
                    <input
                      type="radio"
                      name="velkosti"
                      :value="velkost"
                      v-model="selectedSize"
                    />
                    <span>{{ velkost }}</span>
                  </label>
                </div>
              </div>

              <div class="pocet-kusov">
                <p class="main-text bigger-margin">Počet kusov:</p>
                <div class="vyber">
                  <img
                    @click="pocetKSM == 1 ? pocetKSM : pocetKSM--"
                    src="@/assets/images/icons/minus.png"
                    alt=""
                  />
                  <input type="number" v-model="pocetKSM" min="1" />
                  <img
                    @click="pocetKSM++"
                    src="@/assets/images/icons/plus.png"
                    alt=""
                  />
                </div>
              </div>

              <div class="cena">
                <p class="main-text bigger-margin">Cena:</p>
                <div class="cena-box">
                  <div class="cena-cislo">
                    <p v-if="produktyData.discount_active === 'true'">
                      <strong>{{ vypocitanaCena }}€</strong>
                    </p>
                    <p v-else>
                      {{
                        (
                          parseFloat(produktyData.price.replace(",", ".")) *
                          pocetKSM
                        )
                          .toFixed(2)
                          .replace(".", ",")
                      }}€
                    </p>
                  </div>
                  <div
                    v-if="produktyData.discount_active === 'true'"
                    class="cena-akcia"
                  >
                    <p class="akcia">Zľava-{{ produktyData.discount }}%</p>
                    <p>
                      {{
                        (
                          parseFloat(produktyData.price.replace(",", ".")) *
                          pocetKSM
                        )
                          .toFixed(2)
                          .replace(".", ",")
                      }}€
                    </p>
                  </div>
                </div>
              </div>

              <div class="akcie">
                <div class="button">
                  <div @click="pridajDoKosika()" class="rozdeleny-button">
                    <img src="@/assets/images/icons/taska.svg" alt="" />
                    <p>Do košíka</p>
                  </div>
                </div>

                <div v-if="vKosiku" class="button green">
                  <div
                    @click="$router.push('/pokladna')"
                    class="rozdeleny-button"
                  >
                    <p>Pokračovať do pokladne</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="line end horizontal w-90"></div>

        <p class="info-text">Ďalšie produkty, ktoré by sa vám mohli páčiť</p>

        <zoznam-produktov :pocet="3"></zoznam-produktov>
      </div>
    </section>
  </div>
</template>

<script>
import ZoznamProduktov from "../../components/Produkty/ZoznamProduktov.vue";

export default {
  mounted() {
    window.scrollTo(0, 0);

    this.viditelnePolia();
  },
  created() {
    const produkt = this.$route.query.produkt;
    const id = this.$route.query.cislo;

    this.nacitajProduktyData(id, produkt);

    if (
      !produkt ||
      ![
        "knizky",
        "vaky",
        "oblecenie",
        "ostatne",
        "remene",
        "poukazka",
      ].includes(produkt)
    ) {
      this.vratSaSpat();
    }
  },
  watch: {
    vybranaHodnota(newVal) {
      // Ak je to poukážka, aktualizuj aj "price"
      if (this.produktyData.category === "poukazka") {
        this.produktyData.price = (newVal || 0).toFixed(2).replace(".", ",");
      }
    },
  },
  data() {
    return {
      selectedColor: null,
      selectedSize: null,
      vybranaHodnota: null,
      pocetKS: 1,
      pocetKSM: 1,
      colors: {
        cierna: "#000000", // čierna
        biela: "#FFFFFF", // biela
        siva: "#808080", // sivá
        tmavomodra: "#000080", // tmavomodrá
        modra: "#0000FF", // modrá
        svetlomodra: "#ADD8E6", // svetlomodrá
        cervena: "#FF0000", // červená
        bordova: "#800000", // bordová
        tmavozelena: "#006400", // tmavozelená
        zelena: "#008000", // zelená
        olivova: "#808000", // olivová
        bezova: "#F5F5DC", // béžová
        hneda: "#A52A2A", // hnedá
        horcicova: "#FFDB58", // horčicová
        oranzova: "#FFA500", // oranžová
        zlta: "#FFFF00", // žltá
        ruzova: "#FFC0CB", // ružová
        fialova: "#800080", // fialová
        tyrkysova: "#40E0D0", // tyrkysová
        mentolova: "#98FF98", // mentolová
        zlatista: "#FFD700", // zlatistá
        strieborna: "#C0C0C0", // strieborná
        viacfarebna: "linear-gradient(to right, #ff7e5f, #feb47b)", // viacfarebná
        maskacova: "#78866B", // maskáčová
        neonova: "#39FF14", // neónová
      },
      produktyData: {},
      images: [],
      mainImage: null,
      vKosiku: false,
      obsahKnizky: false,
      typProduktu: "none",
    };
  },
  components: {
    ZoznamProduktov,
  },
  computed: {
    vypocitanaCena() {
      if (this.produktyData.category === "poukazka") {
        const jednotkova = this.vybranaHodnota || 0;
        const pocet = Math.max(this.pocetKS, this.pocetKSM);
        return (jednotkova * pocet).toFixed(2).replace(".", ",");
      }
      let cena = parseFloat(
        this.produktyData.price.toString().replace(",", ".")
      );
      let zlava = parseFloat(this.produktyData.discount.replace(",", "."));
      let pocetKS = 1;
      if (this.pocetKS > this.pocetKSM) {
        pocetKS = this.pocetKS; // Počet kusov
      } else {
        pocetKS = this.pocetKSM; // Počet kusov
      }

      if (this.produktyData.discount_active === "true") {
        let novaCena = (cena - (cena * zlava) / 100) * pocetKS; // Vypočíta cenu s počtom kusov
        return novaCena.toFixed(2).toString().replace(".", ","); // Prevod na európsky formát
      }

      // Ak nie je zľava, iba upraví formát a vynásobí počtom kusov
      return (cena * pocetKS).toFixed(2).toString().replace(".", ",");
    },
    rozdelenyObsah() {
      const obsah = this.produktyData?.details?.obsahPopis || "";

      return obsah
        .split("\n") // každý riadok, vrátane prázdnych
        .map((riadok) => riadok.replace(/\r/g, "").trimEnd()); // zachováme aj prázdne
    },

    obsahRozdelenyNaStlpce() {
      const polovica = Math.ceil(this.rozdelenyObsah.length / 2);
      const lavyStlpec = this.rozdelenyObsah.slice(0, polovica);
      const pravyStlpec = this.rozdelenyObsah.slice(polovica);

      // zarovnať počet riadkov pre pekné zobrazovanie
      const maxDlzka = Math.max(lavyStlpec.length, pravyStlpec.length);
      while (lavyStlpec.length < maxDlzka) lavyStlpec.push("");
      while (pravyStlpec.length < maxDlzka) pravyStlpec.push("");

      return { lavyStlpec, pravyStlpec };
    },
  },
  methods: {
    getColor(color) {
      // Skontroluj, či farba existuje v objekte colors
      if (this.colors[color]) {
        return this.colors[color];
      } else {
        // Ak farba neexistuje v objekte colors, použije sa predvolená biela
        return "#FFFFFF";
      }
    },
    vratSaSpat() {
      this.$router.push("/helishop"); // Presmerovanie
      this.$store.state.SnackBarText = "Hľadaná kategória produktu sa nenašla";
    },
    nacitajProduktyData(id, produktPar) {
      if (produktPar === "poukazka" && !id) {
        this.produktyData = {
          name: "Darčeková poukážka",
          additional_text: "Perfektný darček pre každého",
          price: "0,00",
          discount: "0",
          discount_active: "false",
          category: "poukazka",
          images: ["https://heligonkovaakademia.sk/data/images/poukazky.png"],
          details: {
            popis:
              "Neviete, čo vybrať? Darčeková poukážka je ideálnym riešením! Obdarovaný si sám vyberie, na čo ju využije – či už na predplatné výučbových videí, číselné zápisy alebo produkty v HeliShope.",
            opis: "Poukážka, ktorú je možné uplatniť pri nákupe v našom Heli shope.",
            preKohoPopis: "",
            coSaPopis: "",
            technickyPopis: "",
            obsahPopis: "",
            farby: [],
            velkosti: [],
          },
        };
        this.mainImage = this.produktyData.images[0];
        document.title = `Heligónková Akadémia | ${this.produktyData.name}`;
        return;
      }

      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            let produkt = response.data.data;

            // Upravíme cenu
            produkt.price = produkt.price.replaceAll(".", ",");

            // Pred konverziou details do objektu prejdeme cez každý prvok a nahradíme "//n" za "\n"
            if (produkt.details && typeof produkt.details === "string") {
              // Najprv parsujeme reťazec na objekt
              let detailsObj = JSON.parse(produkt.details);

              // Funkcia na nahradenie "//n" za "\n" v každom reťazci, ale ponechá pole farieb nezmenené
              const replaceNewlines = (obj) => {
                const updatedObj = {};

                for (let key in obj) {
                  if (typeof obj[key] === "string") {
                    updatedObj[key] = obj[key].replaceAll("//n", "\n");
                  } else if (Array.isArray(obj[key])) {
                    // Ak je hodnota pole (napr. pole farieb), ponecháme ho nezmenené
                    updatedObj[key] = [...obj[key]];
                  } else {
                    updatedObj[key] = obj[key];
                  }
                }
                return updatedObj;
              };

              // Upravené details ako objekt
              produkt.details = replaceNewlines(detailsObj);
            }

            // Aktualizujeme produkt
            this.produktyData = produkt;
            this.mainImage = this.produktyData.images[0];

            if (this.produktyData.category != produktPar) {
              this.produktyData = {};
              this.vratSaSpat();
            }
            document.title = `Heligónková Akadémia | ${this.produktyData.name}`;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    moveToMain(index) {
      this.mainImage = this.produktyData.images[index];
      this.scrollToActiveImage();
    },

    prevImage() {
      const currentIndex = this.produktyData.images.indexOf(this.mainImage);
      const newIndex =
        currentIndex === 0
          ? this.produktyData.images.length - 1
          : currentIndex - 1;

      this.mainImage = this.produktyData.images[newIndex];
      this.scrollToActiveImage();
    },

    nextImage() {
      const currentIndex = this.produktyData.images.indexOf(this.mainImage);
      const newIndex =
        currentIndex === this.produktyData.images.length - 1
          ? 0
          : currentIndex + 1;

      this.mainImage = this.produktyData.images[newIndex];
      this.scrollToActiveImage();
    },

    scrollToActiveImage() {
      const activeIndex = this.produktyData.images.indexOf(this.mainImage);
      if (this.galleryRefs && this.galleryRefs[activeIndex]) {
        this.galleryRefs[activeIndex].scrollIntoView({
          behavior: "smooth",
          inline: "center",
        });
      }
    },
    startDrag(event) {
      this.isDragging = true;
      this.startX =
        (event.type === "touchstart" ? event.touches[0].pageX : event.pageX) -
        this.$refs.gallery.offsetLeft;
      this.scrollLeft = this.$refs.gallery.scrollLeft;
    },

    onDrag(event) {
      if (!this.isDragging) return;
      event.preventDefault();
      const x =
        (event.type === "touchmove" ? event.touches[0].pageX : event.pageX) -
        this.$refs.gallery.offsetLeft;
      const move = (x - this.startX) * 2; // Nastaví rýchlosť posunu
      this.$refs.gallery.scrollLeft = this.scrollLeft - move;
    },

    endDrag() {
      this.isDragging = false;
    },
    async pridajDoKosika() {
      // ak ide o poukážku, použijeme špeciálny request
      if (this.produktyData.category === "poukazka" && !this.produktyData.id) {
        if (!this.vybranaHodnota) {
          this.$store.state.SnackBarText = "Prosím, vyberte hodnotu poukážky.";
          return;
        }

        const cenaVCentoch = this.vybranaHodnota * 100;
        const id = `gift_card${cenaVCentoch}`;
        const pocet = Math.max(this.pocetKS || 1, this.pocetKSM || 1);

        const axios = require("axios");
        let uspesne = 0;

        try {
          for (let i = 0; i < pocet; i++) {
            const response = await axios.get(
              `https://heligonkovaakademia.sk/api/cart/add.php?id=${id}`
            );

            if (
              response.status === 200 &&
              response.data.status === "Request fullfiled"
            ) {
              uspesne++;

              // Ručné pridanie do userCart len raz
              if (i === 0) {
                const uzExistuje = this.$store.state.userCart.some(
                  (item) => item.id === id
                );

                if (!uzExistuje) {
                  this.$store.state.userCart.push({
                    id: id,
                    id_delete: `gift_${this.vybranaHodnota}_${Date.now()}`,
                    count: pocet,
                    description: `Darčeková poukážka v hodnote ${this.vybranaHodnota} €`,
                    price: cenaVCentoch,
                  });
                }
              }
            }
          }

          if (uspesne === pocet) {
            this.vKosiku = true;
            this.$store.state.SnackBarText = "Poukážka bola pridaná do košíka.";
          } else {
            this.$store.state.SnackBarText =
              "Niektoré poukážky sa nepodarilo pridať.";
          }
        } catch (e) {
          console.error(e);
          this.$store.state.SnackBarText = "Chyba pri pridávaní do košíka.";
        }

        return;
      }

      // klasické pridanie pre ostatné produkty
      let pocetKS = Math.max(this.pocetKS, this.pocetKSM);

      if (!this.validujVyber()) return;

      for (let i = 0; i < pocetKS; i++) {
        let metaData = [];
        if (this.selectedColor)
          metaData.push(`Farba: ${this.getNazovFarby(this.selectedColor)}`);
        if (this.selectedSize) metaData.push(`Veľkosť: ${this.selectedSize}`);

        await this.$store.dispatch("pridajDoKosika", {
          id: this.produktyData.id,
          meta_data: metaData.join(", "),
        });
      }
    },
    getNazovFarby(farba) {
      const nazvyFarieb = {
        cierna: "čierna",
        biela: "biela",
        siva: "sivá",
        tmavomodra: "tmavomodrá",
        modra: "modrá",
        svetlomodra: "svetlomodrá",
        cervena: "červená",
        bordova: "bordová",
        tmavozelena: "tmavozelená",
        zelena: "zelená",
        olivova: "olivová",
        bezova: "béžová",
        hneda: "hnedá",
        horcicova: "horčicová",
        oranzova: "oranžová",
        zlta: "žltá",
        ruzova: "ružová",
        fialova: "fialová",
        tyrkysova: "tyrkysová",
        mentolova: "mentolová",
        zlatista: "zlatistá",
        strieborna: "strieborná",
        viacfarebna: "viacfarebná",
        maskacova: "maskáčová",
        neonova: "neónová",
      };

      return nazvyFarieb[farba] || farba; // Ak sa nenašla, vráti pôvodný string
    },
    validujVyber() {
      if (this.viditelnePolia().includes("farby") && !this.selectedColor) {
        this.$store.state.SnackBarText = "Prosím, vyberte farbu.";
        return false;
      }
      if (this.viditelnePolia().includes("velkosti") && !this.selectedSize) {
        this.$store.state.SnackBarText = "Prosím, vyberte veľkosť.";
        return false;
      }
      return true;
    },

    // Získa typ produktu z URL a vráti viditeľné polia
    viditelnePolia() {
      // Overenie, či je parameter "produkt" v URL
      const produkt = this.$route.query.produkt;

      const konfiguracia = {
        knizky: ["co-sa", "obsah", "pre-koho", "poradie-cislo"],
        vaky: ["technicke"],
        oblecenie: ["opis", "farba", "velkost"],
        ostatne: ["pre-koho", "co-sa", "technicke", "opis", "farba", "velkost"],
        remene: ["technicke"],
        poukazka: ["informacie", "hodnoty"],
      };

      return konfiguracia[produkt] || [];
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

h1 {
  z-index: 1;

  margin: 0;

  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 3px rgba(0, 0, 0, 0.25);
  font-size: 6vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0.1em 0.15em 0.1em;
  margin: 0;
}

.nadpis {
  font-weight: 700;
  font-size: 2vw;
  margin: 0.2em 0.3em;
  text-align: center;
}

.podnadpis {
  font-weight: 400;
  font-size: 1.8vw;
  margin: 0.2em 0.3em 0em;
  text-align: center;
  position: relative;
  width: 80%;
}

.poradie-cislo {
  position: absolute;
  top: 45%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #238035;
  font-size: 1.2em;
}

.poradie {
  position: absolute;
  left: 110%;
  transform: translate(-50%, -50%);

  img {
    width: 2.6em;
  }
}

.line {
  margin: 1.1em 0 1.7em;
}

.line.end {
  margin: 3.1em auto 3.7em;
}

.opis,
.pre-koho,
.popis {
  width: 100%;
}

.text {
  text-align: left;
  font-size: 1.3em;
  margin: 0.5em auto;
  width: 95%;
  white-space: break-spaces;
  font-weight: 300;
}

.text-sm {
  font-size: 1.2em;
}

.stlpec .text-sm {
  width: max-content;
  margin-left: 0;
}

.obsah .text-sm {
  font-size: 1.3em;
}

.main-text {
  text-align: left;
  font-size: 1.45em;
  margin: 0.5em auto;
  width: 95%;
  font-weight: 600;
}

.bigger-margin {
  margin: 1em auto;
  width: 100%;
}

.product {
  display: flex;
  justify-content: center;
  font-size: 0.9vw;
}

.interactive {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  justify-content: flex-start;
  align-items: flex-end;
  column-gap: 3vw;
  row-gap: 0.5vw;
  width: 95%;
}

.pw-small {
  font-weight: 200;
}

//css pre farebné vyberanie
.farba-circle {
  border-radius: 50%;
  border: 0.23em solid #000;
  position: relative;
  cursor: pointer;

  &:has(.selected) {
    transform: scale(1.1);
  }
}

.farba-circle input[type="radio"] {
  display: none;
  cursor: pointer;
}

.farba-inner-circle {
  border: 0.18em solid #dedede;
  width: 1.8em;
  height: 1.8em;
  border-color: #dedede;
  box-shadow: inset 0 0.2em 0.3em rgba(0, 0, 0, 0.3);
  border-radius: 50%;
  cursor: pointer;

  &:hover {
    transform: scale(1.1);
    transition-duration: 0.2s;
  }
}

.farba-inner-circle.selected {
  border-color: #8ec84e;
  cursor: pointer;
}

.farba {
  display: flex;
  min-height: 8em;
  flex-direction: column;
  justify-content: space-between;
}

.farby {
  display: flex;
  gap: 0.5em;
  flex-wrap: wrap;
}

//css pre vyberanie veľkostí (hodnoty)
.size-options {
  display: flex;
  gap: 10px;
}

.velkost,
.hodnoty {
  display: flex;
  min-height: 8em;
  flex-direction: column;
  justify-content: space-between;
}

.hodnoty {
  width: 100%;

  .size-options {
    justify-content: space-between;

    label {
      font-size: 1.6em;
      height: 2em;
      width: 3.3em;
    }
  }
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
  height: 2em;
  width: 2.1em;

  &:hover {
    transform: scale(1.1);
    transition-duration: 0.2s;
  }
}

.size-options input {
  display: none;
}

.active-hodnota span {
  background-color: #8ec84e !important;
  color: black !important;
  border-radius: 8px;
  padding: 0.35em 0.7em;
  display: flex;
  align-items: center;
  justify-content: center;
  height: -webkit-fill-available;
  transition: background-color 0.3s, color 0.3s;
}

.size-options input:checked + span {
  background-color: #8ec84e;
  color: black;
  border-radius: 8px;

  width: -webkit-fill-available;
  padding: 0.35em 0.7em;

  display: flex;
  align-items: center;
  justify-content: center;
  height: -webkit-fill-available;
}

// vyberanie množstva produktu
.pocet-kusov {
  display: flex;
  min-height: 8em;
  flex-direction: column;
  justify-content: space-between;
}

.vyber {
  display: flex;
  align-items: center;
  justify-content: center;

  input[type="number"]::-webkit-outer-spin-button,
  input[type="number"]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
  }

  input[type="number"] {
    -moz-appearance: textfield;
  }
}

.vyber img {
  width: 2em;
  height: auto;
  cursor: pointer;

  &:hover {
    transform: scale(1.1);
    transition-duration: 0.2s;
  }
}

.vyber input {
  width: 1.5em;
  height: 1.6em;
  padding: 0.1em 0.2em;
  margin: auto 0.7em;
  font-size: 1.8em;
  font-weight: 800;
  background: #d9d9d9;
  border-radius: 0.5em;
  text-align: center;
}

//cena

.cena {
  display: flex;
  min-height: 8em;
  flex-direction: column;
  justify-content: space-between;
}

.cena-cislo,
.cena-akcia {
  display: flex;
  align-items: center;
  justify-content: center;

  width: auto;
  height: 2.4em;
  padding: 0.3em 0.4em;
  background: #d9d9d9;
  border-radius: 0.6em;
  text-align: center;

  p {
    font-size: 1.8em;
    font-weight: 800;
  }
}

.cena-box {
  display: flex;
  align-items: center;
}

.cena-akcia {
  background: transparent;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;

  p {
    text-decoration: line-through;
    padding: 0 0.3em;
    text-decoration-color: #00913f;
  }

  p.akcia {
    transform: scale(1.4);
    font-size: 0.7em;
    font-weight: 800;
    color: #00913f;
    text-decoration: none;
  }
}

//boxy

.left {
  flex: 0.4;
  width: 43%;
}

.right {
  flex: 0.6;
  margin: 0 3%;
}

//zobraziť obsah
.obsah {
  text-align: left;
  margin-right: auto;
  margin-left: 5%;
}

.obsah-stlpce {
  display: flex;
  gap: 15%;
  justify-content: flex-start;
  width: 95%;
  margin: 0.5em auto;
}

.obsah-button {
  margin-right: auto;
  font-size: 100%;
  margin-bottom: 1em;
}

//do košíka pridať, akcie

.rozdeleny-button {
  display: flex;
  align-items: center;
  justify-content: center;
}

.button {
  margin-top: 1em;
}

.akcie {
  display: flex;
  align-items: center;
  gap: 5%;
  flex-wrap: wrap;
  width: -webkit-fill-available;
  font-size: 70%;
  justify-content: flex-start;
}

//štýl posledného textu
.info-text {
  font-weight: 600;
  font-size: 1.8vw;
  margin: 0 0.3em 0.5em;
}

//obrázok
.image {
  width: 100%;
  position: relative;
  // margin-bottom: 3em;
}

.main-image {
  width: 100%;
  // height: 33em;
  height: auto;
  object-fit: cover;
  border-radius: 1.8em;
  margin-bottom: 1em;
  box-shadow: 3px 3px 7px rgba(0, 0, 0, 0.4);
}

.main-image.bez-shadow {
  box-shadow: none !important;
}

.gallery {
  display: flex;
  overflow-x: scroll;
  width: 100%;
  max-width: 44em;
  padding: 1% 0;
  cursor: grabbing;

  img {
    height: 9em;
    min-width: 8em;
    -o-object-fit: cover;
    object-fit: cover;
    border-radius: 1em;
    margin: 0 0.5em;
    box-shadow: 3px 3px 4px rgba(0, 0, 0, 0.2);
  }

  img.active {
    border: 0.2em solid #fef35a;
  }
}

//šípky
.arrow {
  position: absolute;
  width: 3em;
  top: 50%;
  transform: translate(-50%, -50%);
  cursor: pointer;
  transition-duration: 0.2s;

  &:hover {
    width: 4em;
    transition-duration: 0.4s;
  }
}

.left-arrow {
  left: 2em;
}

.right-arrow {
  right: 0em;
  transform: translateY(-50%);
}

//optimalizácie pre tablet
@media only screen and (max-width: 1200px) {
  .mobile {
    display: block;
    padding-bottom: 2%;
    height: inherit;
  }

  .computer {
    display: none;
  }
}

.mobile {
  .left {
    margin: 0 auto;
    width: 95%;
  }

  .right {
    width: 95%;
    margin: 0 auto;
  }

  .product {
    font-size: 1.1vw;
    display: flex;
    flex-direction: column;
  }

  .image {
    margin-top: 2em;
  }

  h1 {
    font-size: 8vw;
  }

  .nadpis {
    font-size: 3.1vw;
  }

  .podnadpis {
    font-size: 2.4vw;
    width: 70%;
  }

  .poradie {
    top: 50%;
  }

  .gallery {
    max-width: 60vw;
    margin: auto;
  }
}

@media only screen and (max-width: 750px) {
  .mobile .gallery {
    max-width: 100vw;
  }

  .obsah {
    margin: 1.5em auto;
  }

  .poradie-cislo {
    top: 42%;
  }

  .image {
    max-width: 33rem;
  }

  section {
    margin-bottom: 10em;
    height: auto;
  }

  .main-image {
    height: auto;
    max-width: 20em;
  }

  .interactive {
    column-gap: 6vw;
    justify-content: space-around;
  }

  .mobile .product {
    font-size: 2.7vw;
  }

  .bigger-margin {
    margin: 0.8em auto;
  }

  .info-text {
    font-size: 3.8vw;
  }

  .line.end {
    margin: 2em auto !important;
  }

  .farba,
  .velkost,
  .cena,
  .pocet-kusov {
    height: 7em;
  }

  .mobile {
    height: auto;

    h1 {
      font-size: 14vw;
    }

    .nadpis {
      font-size: 5.2vw;
    }

    .podnadpis {
      font-size: 4vw;
    }
  }
}

@media only screen and (max-width: 450px) {
  .mobile section .button p {
    font-size: 3.5vw;
  }
}
</style>
