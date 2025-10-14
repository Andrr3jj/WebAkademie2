<template>
  <div class="content computer">
    <section
      v-if="vlastnenyProduktData?.ciselne_zapisy?.length == 0"
      style="overflow: hidden"
      :id="$route.name"
    >
      <div v-if="isLoading" class="loader"></div>
      <div
        v-if="!overlayVisible"
        class="zapis"
        @click="
          $store.state.overlayVisible = true;
          $store.state.ciselnyZapisObrazky = ciselnyZapisObrazky;
        "
      >
        <img class="zapis" :src="ciselnyZapisObrazky[aktualneCisloStrany]" />
      </div>
      <img
        class="ovladanie"
        id="sipkaLava"
        src="@/assets/images/gallery/sipka.svg"
        alt="Posun doľava"
        v-if="ciselnyZapisObrazky.length != 1"
        @click="
          aktualneCisloStrany == 0
            ? (aktualneCisloStrany = ciselnyZapisObrazky.length - 1)
            : aktualneCisloStrany--
        "
      />
      <img
        class="full-screen-icon"
        src="@/assets/images/icons/fullscreen.png"
        alt="Zobrazenie na celú obrazovku"
        @click="
          $store.state.overlayVisible = true;
          $store.state.ciselnyZapisObrazky = ciselnyZapisObrazky;
        "
      />
      <img
        class="ovladanie jednaStranka"
        id="sipkaPrava"
        src="@/assets/images/gallery/sipka.svg"
        alt="Posun do prava"
        v-if="ciselnyZapisObrazky.length != 1"
        @click="
          aktualneCisloStrany == ciselnyZapisObrazky.length - 1
            ? (aktualneCisloStrany = 0)
            : aktualneCisloStrany++
        "
      />
    </section>

    <section
      class="animate-duration"
      v-if="vlastnenyProduktData?.ciselne_zapisy?.length > 0"
      :id="$route.name"
    >
      <div class="posunutie left">
        <img
          @click="prevPage"
          v-if="currentPage !== 0"
          src="@/assets/images/icons/posunutieZapisu-min.png"
          alt="Posunutie"
        />
      </div>
      <div v-show="isLoading" class="loader"></div>
      <div v-show="!isLoading" class="scroll prepojenie" ref="editorContainer">
        <EditorZobrazenieZapisu
          :key="'editor-' + currentPage"
          :prepojeneZapisyID="vlastnenyProduktData?.ciselne_zapisy"
          :stupnica="aktualnaStupnica"
          :nacitaneNoty="nacitaneNoty"
          :vlastnenyProduktData="vlastnenyProduktDetails"
          :sliceStart="currentPage * visibleCount"
          :sliceEnd="currentPage * visibleCount + (visibleCount - 1)"
          @loading-changed="isLoading = $event"
          @update-stupnica="prepniStupnicu"
          @total-casts="totalCasts = $event"
        />

        <p>Strana {{ currentPage + 1 }} / {{ totalPages }}</p>

        <!--  Prehrávač hudby   -->

        <AudioPlayer
          class="audio"
          :src="vratZvukovuUkazku()"
          :id="vlastnenyProduktData?.id"
          :aktualnePrehravaneId="vlastnenyProduktData?.id"
        >
          <p class="audio-text">Prehrať pieseň</p>
        </AudioPlayer>

        <div
          class="tlacit audio"
          v-if="
            $store.state.user?.edupage_in_calendar ||
            ($store.state.user?.isAdmin &&
              $store.state.userRoles?.roles?.includes('subscription_edit'))
          "
          @click="onPrintClick"
          role="button"
          tabindex="0"
        >
          <div
            class="tlac-img"
            @click.stop="onPrintClick"
            role="button"
            tabindex="0"
          >
            <img
              src="@/assets/images/icons/tlacit.svg"
              alt="Tlač"
              @click.stop="onPrintClick"
            />
          </div>
          <p class="audio-text">Tlačiť</p>
        </div>
      </div>
    </section>

    <section class="safari second animate-duration" :id="$route.name">
      <div class="posunutie pravo">
        <img
          @click="nextPage"
          v-if="
            currentPage !== totalPages - 2 &&
            vlastnenyProduktData?.ciselne_zapisy?.length > 0
          "
          src="@/assets/images/icons/posunutieZapisu-min.png"
          alt="Posunutie"
        />
      </div>
      <div class="scroll">
        <div
          @mouseenter="toggleInfo('info', 'open')"
          @mouseleave="toggleInfo('info', 'close')"
          class="top-open-bar"
        >
          <img
            @click="toggleInfo('info', '')"
            class="toggle-info-btn"
            src="@/assets/images/icons/openInfoBar-min.png"
            alt="Otvoriť informácie o pesničke"
          />

          <div v-if="this.showSongInfoBox" class="bar">
            <SongInfoBox
              :name="vlastnenyProduktData.name"
              :difficulty="vlastnenyProduktData.difficulty"
              :author="vlastnenyProduktDetails.autor"
              :genre="vlastnenyProduktDetails.zaner"
              :tempo="vlastnenyProduktDetails.tempo"
              :stupnice="vlastnenyProduktDetails.stupnice"
              :aktualnaStupnica="aktualnaStupnica"
              :hasCiselneZapisy="
                vlastnenyProduktData?.ciselne_zapisy?.length > 0
              "
              @update-stupnica="prepniStupnicu"
            />
          </div>
        </div>

        <EditorZobrazenieZapisu
          v-if="vlastnenyProduktData?.ciselne_zapisy?.length > 0"
          :key="'editor-' + (currentPage + 1)"
          v-show="currentPage < totalPages - 1"
          :prepojeneZapisyID="vlastnenyProduktData?.ciselne_zapisy"
          :stupnica="aktualnaStupnica"
          :nacitaneNoty="nacitaneNoty"
          :vlastnenyProduktData="vlastnenyProduktDetails"
          :sliceStart="(currentPage + 1) * visibleCount"
          :sliceEnd="(currentPage + 1) * visibleCount + (visibleCount - 1)"
          @loading-changed="isLoading = $event"
          @update-stupnica="prepniStupnicu"
        ></EditorZobrazenieZapisu>

        <div
          @mouseenter="toggleInfo('text', 'open')"
          @mouseleave="toggleInfo('text', 'close')"
          class="bottom-open-bar"
        >
          <div class="info-w-text">
            <img
              @click="toggleInfo('text', '')"
              class="toggle-info-btn"
              src="@/assets/images/icons/openInfoBar-min.png"
              alt="Otvoriť informácie o pesničke"
            />

            <p class="popis-text">Zobraziť text piesne</p>
          </div>

          <div v-if="this.showSongTextBox" class="bar">
            <TextZapis :lyrics="vlastnenyProduktDetails.text" />
          </div>
        </div>
      </div>
      <div class="add-favorite">
        <img
          v-if="!jeOblubeny"
          @click="zmenOblubeny()"
          src="@/assets/images/icons/oblubene-min.png"
          alt=""
        />
        <img
          v-else
          @click="zmenOblubeny()"
          src="@/assets/images/icons/oblubeneAktivne-min.png"
          alt=""
        />
        <p class="popis-text" v-if="!jeOblubeny">Pridať do obľúbených</p>
        <p class="popis-text" v-if="jeOblubeny">Odobrať z obľúbených</p>
      </div>
      <div class="add-favorite right">
        <img
          v-if="jeDokonceny"
          @click="zmenDokonceny()"
          src="@/assets/images/icons/zvladnute-min.png"
          alt=""
        />
        <img
          v-if="!jeDokonceny"
          @click="zmenDokonceny()"
          src="@/assets/images/icons/zvladnutePridat-min.png"
          alt=""
        />

        <p class="popis-text" v-if="!jeDokonceny">Označiť ako zvládnuté</p>
        <p class="popis-text" v-if="jeDokonceny">Pieseň je už zvládnutá</p>
      </div>
    </section>

    <!-- <div class="bg-style"></div> -->
  </div>

  <div class="safari content tablet">
    <section class="second" :id="$route.name">
      <div class="scroll">
        <div class="song">
          <h1>Názov piesne</h1>
          <h5>{{ vlastnenyProduktData?.name }}</h5>

          <div class="line horizontal w-80"></div>

          <div class="dificulty">
            <obtiaznost-zapisov
              :produktObtiaznost="vlastnenyProduktData?.difficulty"
            />
          </div>

          <div class="info">
            <p class="strong">Číselný zápis napísal:</p>
            <p class="info-text">{{ vlastnenyProduktDetails.autor }}</p>
          </div>
          <div
            v-if="vlastnenyProduktData?.ciselne_zapisy?.length > 0"
            class="info-box"
          >
            <div class="info">
              <p class="strong">Tempo hrania:</p>
              <p class="info-text">{{ vlastnenyProduktDetails.tempo }}</p>
            </div>
            <div class="line"></div>
            <div class="info">
              <p class="strong">Žáner piesne:</p>
              <p class="info-text">{{ vlastnenyProduktDetails.zaner }}</p>
            </div>
          </div>
          <div
            v-if="vlastnenyProduktData?.ciselne_zapisy?.length == 0"
            class="info-box"
          >
            <div class="info">
              <p class="strong">Tempo hrania:</p>
              <p class="info-text">{{ vlastnenyProduktDetails.tempo }}</p>
              <p class="strong">Žáner piesne:</p>
              <p class="info-text">{{ vlastnenyProduktDetails.zaner }}</p>
            </div>
            <div class="line"></div>
            <div class="info">
              <p class="strong">Stupnica piesne:</p>
              <div class="info-buttons">
                <div
                  v-for="element in vlastnenyProduktDetails.stupnice"
                  :key="element"
                  class="button Adumu"
                  :class="{ green: aktualnaStupnica == element }"
                  @click="
                    if (
                      aktualnaStupnica != element &&
                      nacitaneNoty == true &&
                      vlastnenyProduktData?.ciselne_zapisy?.length <= 0
                    ) {
                      aktualnaStupnica = element;
                      this.ciselnyZapisObrazky = [];
                      this.cisloStrany = 0;
                      this.aktualneCisloStrany = 0;
                      this.nacitaneNoty = false;
                      this.notyCiselnyZapis();
                    } else {
                      aktualnaStupnica = element;
                    }
                  "
                >
                  <p>
                    {{
                      element == "D" || element == "A"
                        ? element + " - Mol"
                        : element + " - Dur"
                    }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="music">
          <div class="info">
            <p class="strong">Prehrať pieseň</p>

            <div class="prehravac">
              <MusicPlayer
                v-if="this.vlastnenyProduktData?.name != undefined"
                :audioUrl="vratZvukovuUkazku()"
              />
            </div>
          </div>
          <div v-if="vlastnenyProduktDetails.info != ''" class="popis">
            <img
              v-if="!popisOtvor"
              @mouseenter="popisOtvor = true"
              src="@/assets/images/icons/info.svg"
              alt="Doplňujúce info"
            />
            <img
              v-if="popisOtvor"
              @mouseenter="popisOtvor = true"
              @mouseleave="popisOtvor = false"
              src="@/assets/images/icons/info-active.svg"
              alt="Doplňujúce info"
              class="info-active"
            />
          </div>
          <div
            @mouseenter="popisOtvor = true"
            @mouseleave="popisOtvor = false"
            :class="popisOtvor ? 'popis-active' : 'popis-deactive'"
          >
            <p v-if="vlastnenyProduktDetails.info != undefined">
              {{ vlastnenyProduktDetails.info.replaceAll("//n", "\n") }}
            </p>
          </div>
        </div>

        <div class="text">
          <p class="text-piesne">Text piesne:</p>

          <p v-if="vlastnenyProduktDetails.text != undefined">
            {{ vlastnenyProduktDetails.text.replaceAll("//n", "\n") }}
          </p>
        </div>
        <div
          v-if="vlastnenyProduktData?.ciselne_zapisy?.length <= 0"
          @contextmenu.prevent
          @dragstart.prevent
          @selectstart.prevent
          class="images"
        >
          <div v-if="isLoading" class="loader"></div>
          <img
            v-else
            class="zapis"
            :src="ciselnyZapisObrazky[aktualneCisloStrany]"
            alt="načítavanie číselného zápisu..."
          />
        </div>

        <div
          v-if="vlastnenyProduktData?.ciselne_zapisy?.length > 0"
          class="animate-duration"
          style="overflow: hidden"
          :id="$route.name"
        >
          <div v-show="isLoading" class="loader"></div>
          <div v-show="!isLoading" class="scroll prepojenie">
            <EditorZobrazenieZapisu
              :prepojeneZapisyID="vlastnenyProduktData?.ciselne_zapisy"
              :stupnica="aktualnaStupnica"
              :nacitaneNoty="nacitaneNoty"
              :vlastnenyProduktData="vlastnenyProduktDetails"
              @loading-changed="isLoading = $event"
              @update-stupnica="prepniStupnicu"
            ></EditorZobrazenieZapisu>
          </div>
        </div>
      </div>
      <!-- <img
        class="ovladanie"
        id="sipkaLava"
        src="@/assets/images/gallery/sipka.svg"
        alt="Posun do ľava"
        v-if="ciselnyZapisObrazky.length != 1"
        @click="
          aktualneCisloStrany == 0
            ? (aktualneCisloStrany = ciselnyZapisObrazky.length - 1)
            : aktualneCisloStrany--
        "
      /> -->
      <div
        class="tlacit ovladanie"
        v-if="
          $store.state.user?.edupage_in_calendar ||
          ($store.state.user?.isAdmin &&
            $store.state.userRoles?.roles?.includes('subscription_edit'))
        "
        @click="onPrintClick"
        role="button"
        tabindex="0"
      >
        <div
          class="tlac-img"
          @click.stop="onPrintClick"
          role="button"
          tabindex="0"
        >
          <img
            src="@/assets/images/icons/tlacit.svg"
            alt="Tlač"
            @click.stop="onPrintClick"
          />
        </div>
        <p class="audio-text">Tlačiť</p>
      </div>
      <div class="add-favorite">
        <img
          v-if="!jeOblubeny"
          @click="zmenOblubeny()"
          src="@/assets/images/icons/oblubene-min.png"
          alt=""
        />
        <img
          v-else
          @click="zmenOblubeny()"
          src="@/assets/images/icons/oblubeneAktivne-min.png"
          alt=""
        />
      </div>

      <div class="add-naucene">
        <img
          v-if="jeDokonceny"
          @click="zmenDokonceny()"
          src="@/assets/images/icons/zvladnute-min.png"
          alt=""
        />
        <img
          v-if="!jeDokonceny"
          @click="zmenDokonceny()"
          src="@/assets/images/icons/zvladnutePridat-min.png"
          alt=""
        />
      </div>
      <!-- <img
        class="ovladanie"
        id="sipkaPrava"
        src="@/assets/images/gallery/sipka.svg"
        alt="Posun do prava"
        v-if="ciselnyZapisObrazky.length != 1"
        @click="
          aktualneCisloStrany == ciselnyZapisObrazky.length - 1
            ? (aktualneCisloStrany = 0)
            : aktualneCisloStrany++
        "
      /> -->
    </section>

    <!-- <div class="bg-style"></div> -->
  </div>
  <!-- PRINT-ONLY RENDER: len číselný zápis, automaticky zalamovaný na stránky -->
  <div :id="$route.name + '-print-root'" class="print-only">
    <div class="scroll">
      <EditorZobrazenieZapisu
        v-if="vlastnenyProduktData?.ciselne_zapisy?.length > 0"
        :key="'editor-print'"
        :prepojeneZapisyID="vlastnenyProduktData?.ciselne_zapisy"
        :stupnica="aktualnaStupnica"
        :nacitaneNoty="nacitaneNoty"
        :vlastnenyProduktData="vlastnenyProduktDetails"
        @update-stupnica="prepniStupnicu"
      />

      <!-- fallback pre prípady bez prepojených zápisov: vytlačí aktuálnu jednu stranu obrázku -->
      <div v-else class="images">
        <img
          class="zapis"
          :src="ciselnyZapisObrazky[aktualneCisloStrany]"
          alt="Číselný zápis"
        />
      </div>
    </div>
  </div>
</template>

<script>
import EditorZobrazenieZapisu from "@/components/Zapisy/EditorZobrazenieZapisu.vue";
import MusicPlayer from "../../../components/Functionality/MusicPlayer.vue";
import ObtiaznostZapisov from "../../../components/Functionality/ObtiaznostZapisov.vue";
import AudioPlayer from "../../../components/Functionality/AudioPlayer.vue";
import SongInfoBox from "@/components/Zapisy/Ciselny-zapis-cmp/InfoZapis.vue";
import TextZapis from "../../../components/Zapisy/Ciselny-zapis-cmp/TextZapis.vue";

export default {
  errorCaptured(err, vm, info) {
    console.error("❌ Chyba v child-komponente:", info, err);
    console.log("📋 Props toho childu boli:", vm.$props);
    return false;
  },
  components: {
    MusicPlayer,
    ObtiaznostZapisov,
    EditorZobrazenieZapisu,
    AudioPlayer,
    SongInfoBox,
    TextZapis,
  },
  computed: {
    totalPages() {
      const vc = this.visibleCount || 1;
      return vc > 0 ? Math.ceil(this.totalCasts / vc) : 0;
    },
  },
  data() {
    return {
      popisOtvor: false,
      noty: false, //toto odstranim ked to bduem prepajat
      vlastnenyProduktData: {},
      vlastnenyProduktDetails: {},
      jeOblubeny: false,
      jeDokonceny: false,
      invalidId: null,
      idleTime: 0,
      cisloStrany: 0,
      aktualnaStupnica: "",
      ciselnyZapisObrazky: [],
      aktualneCisloStrany: 0,
      nacitaneNoty: false,
      isLoading: true,
      interval: null,
      time: 1,
      notyReccursive: 0,
      showSongInfoBox: false,
      showSongTextBox: false,
      visibleCount: 5,
      totalCasts: 0,
      resizeTimeout: null,
      currentPage: 0,
      timeIntervalSeconds: 60, // výchozí, nahradí sa z konfigurácie
      helitimeConfig: null,
    };
  },
  mounted() {
    window.scrollTo(0, 0);

    this.nacitajProdukt();
    setTimeout(() => {
      this.zistiOblubeny();
      this.zistiDokonceny();
    }, 250);

    //meranie času
    this.loadHelitimeConfig().then(() => {
      this.timeMeaseure(); // až keď máme config
    });

    // Priradenie funkcie resetIdleTime k udalostiam pohybu myši a stlačeniu klávesnice
    window.addEventListener("mousemove", this.resetIdleTime);
    window.addEventListener("keypress", this.resetIdleTime);
    window.addEventListener("touchstart", this.resetIdleTime, {
      passive: true,
    });

    // pri zmene veľkosti okna
    // window.addEventListener("resize", this.computeVisibleCountSimple);
    window.addEventListener("resize", this.onResizeDebounced);

    // Keyboard navigation for prev/next page
    window.addEventListener("keydown", this.onKeydown);
    // Clean print presmerovanie (iba keď je tlač povolená)
    window.addEventListener("keydown", this._onKeydownCleanPrint);

    // Recompute visibleCount after initial render
    this.$nextTick(() => {
      this.computeVisibleCountSimple();
    });
  },

  beforeUnmount() {
    // Odstranění posluchačů událostí pohybu myší a stisku klávesnice
    window.removeEventListener("mousemove", this.resetIdleTime);
    window.removeEventListener("keypress", this.resetIdleTime);
    window.removeEventListener("touchstart", this.resetIdleTime, {
      passive: true,
    });
    // window.removeEventListener("resize", this.computeVisibleCountSimple);
    window.removeEventListener("resize", this.onResizeDebounced);
    window.removeEventListener("keydown", this.onKeydown);
    window.removeEventListener("keydown", this._onKeydownCleanPrint);

    // Zastavení všech časovačů, asynchronních operací atd.
    clearInterval(this.interval);
    if (this.resizeObserver) {
      this.resizeObserver.disconnect();
    }
  },
  methods: {
    async loadHelitimeConfig() {
      try {
        const response = await fetch(
          "https://heligonkovaakademia.sk/api/helitime/config/helitime.config.json"
        );
        const config = await response.json();
        this.helitimeConfig = config;

        const secs = Number(config.helitime_timeout_seconds) || 60;
        this.timeIntervalSeconds = secs + 1;
        console.log(
          "this.timeIntervalSeconds :>> ",
          this.timeIntervalSeconds,
          "config.",
          config.helitime_timeout_seconds
        );
        // bonus logika môže byť riešená priamo pri pridávaní
      } catch (error) {
        console.error("Nepodarilo sa načítať helitime konfiguráciu", error);
      }
    },
    // === Čistá tlač cez novú Window (bez zvyšku DOM) ===
    printClean() {
      try {
        // nájdi renderovaný print-root
        const root = document.querySelector("[id$='-print-root']");
        if (!root) {
          console.warn("printClean: nenašiel som print-root");
          window.print();
          return;
        }

        // Získaj jeho HTML (už renderované Vue-om)
        const html = this._buildPrintHtml(root.outerHTML);

        // Otvor nové okno
        const w = window.open("", "_blank", "noopener,noreferrer");
        if (!w) {
          window.print();
          return;
        }
        w.document.open();
        w.document.write(html);
        w.document.close();

        // počkaj na načítanie obrázkov a potom tlač
        const done = () => {
          try {
            w.focus();
            // eslint-disable-next-line
          } catch (e) {}
          w.print();
          // voliteľne zavrieť okno po tlači
          w.addEventListener("afterprint", () => {
            try {
              w.close();
              // eslint-disable-next-line
            } catch (e) {}
          });
          // fallback zatvorenie po 2 min
          setTimeout(() => {
            try {
              w.close();
              // eslint-disable-next-line
            } catch (e) {}
          }, 120000);
        };

        const imgs = Array.from(w.document.images || []);
        if (imgs.length === 0) {
          done();
        } else {
          let loaded = 0;
          const onImg = () => {
            loaded++;
            if (loaded >= imgs.length) done();
          };
          imgs.forEach((img) => {
            if (img.complete) onImg();
            else {
              img.onload = onImg;
              img.onerror = onImg;
            }
          });
        }
      } catch (e) {
        console.error("printClean error", e);
        window.print();
      }
    },

    _buildPrintHtml(inner) {
      // Nazbieraj všetky štýly z aktuálneho dokumentu (style + link rel=stylesheet)
      const styleTags = Array.from(document.querySelectorAll("style"))
        .map((el) => `<style>${el.innerHTML}</style>`)
        .join("\n");
      const linkTags = Array.from(
        document.querySelectorAll('link[rel="stylesheet"]')
      )
        .map((el) => `<link rel="stylesheet" href="${el.href}" />`)
        .join("\n");

      // Minimálny izolovaný HTML dokument so štýlmi a @page
      return `<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <base href="${document.baseURI}">
  <title>${document.title || "Print"}</title>
  ${linkTags}
  ${styleTags}
  <style>
    /* Na istotu: presne naše pravidlá pre print popup */
    @page { size: A4 portrait; margin: 10mm 8mm 10mm 8mm;}
    html, body { margin:0; padding:0; background:#fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* Základné nulovanie a pekné zalamovanie */
    *, *::before, *::after { box-sizing: border-box; }
    img, canvas, svg { max-width: 100%; height: auto; page-break-inside: avoid; break-inside: avoid; }
    .cast { page-break-inside: avoid; break-inside: avoid; }

    /* Bez vnútorných okrajov – obsah ide od kraja po kraj */
    .print-page { padding: 0; }

    /* Zobraz iba print-root obsah, ostatné globálne "hide" pravidlá z tvojej appky tu neplatia */
    @media print {
      body { margin:0 !important; }
    }
  </style>
</head>
<body>
  <div class="print-page">${inner}</div>
</body>
</html>`;
    },

    // Zachytenie Ctrl/Cmd+P a presmerovanie na clean print, ale iba ak je tlač povolená
    _onKeydownCleanPrint(e) {
      const key = String(e.key).toLowerCase();
      const wantsPrint = (e.ctrlKey || e.metaKey) && key === "p";
      if (!wantsPrint) return;
      const allowed = !!(
        (typeof window !== "undefined" && window.haCanPrint) ||
        (this.$store &&
          this.$store.state &&
          this.$store.state.user &&
          (this.$store.state.user.edupage_in_calendar ||
            (this.$store.state.user.isAdmin &&
              this.$store.state.userRoles?.roles?.includes(
                "subscription_edit"
              ))))
      );
      if (!allowed) return; // keď nie je povolené, nech rieši EventBlocker
      e.preventDefault();
      window.print();
    },

    onPrintClick(e) {
      try {
        e && e.stopPropagation && e.stopPropagation();
        // eslint-disable-next-lin
      } catch (_) {
        void 0;
      }
      try {
        e && e.preventDefault && e.preventDefault();
        // eslint-disable-next-lin
      } catch (_) {
        void 0;
      }
      // bezpečne zavolaj natívnu tlač
      window.print();
    },
    nextPage() {
      console.log(
        "informacie :>> ",
        this.currentPage,
        this.totalPages,
        this.totalCasts,
        this.visibleCount
      );
      if (this.currentPage < this.totalPages - 1) {
        this.currentPage++;
      }
      console.log(
        "informacie :>> ",
        this.currentPage,
        this.totalPages,
        this.totalCasts,
        this.visibleCount
      );
    },
    prevPage() {
      console.log(
        "informacie :>> ",
        this.currentPage,
        this.totalPages,
        this.totalCasts,
        this.visibleCount
      );
      if (this.currentPage > 0) {
        this.currentPage--;
      }
      console.log(
        "informacie :>> ",
        this.currentPage,
        this.totalPages,
        this.totalCasts,
        this.visibleCount
      );
    },
    onKeydown(event) {
      const key = event.key.toLowerCase();
      if (key === "arrowright" || key === "d") {
        this.nextPage();
      } else if (key === "arrowleft" || key === "a") {
        this.prevPage();
      }
    },
    onResizeDebounced() {
      // vymažeme predchádzajúci plán, ak ešte neprešiel
      clearTimeout(this.resizeTimeout);
      // naplánujeme nový výpočet za 150 ms
      this.resizeTimeout = setTimeout(() => {
        this.computeVisibleCountSimple();
      }, 150);
    },
    prepniStupnicu(element) {
      if (
        this.aktualnaStupnica !== element &&
        this.nacitaneNoty === true &&
        this.vlastnenyProduktData?.ciselne_zapisy?.length <= 0
      ) {
        this.aktualnaStupnica = element;
        this.ciselnyZapisObrazky = [];
        this.cisloStrany = 0;
        this.aktualneCisloStrany = 0;
        this.nacitaneNoty = false;
        this.notyCiselnyZapis();
      } else {
        this.aktualnaStupnica = element;
      }
    },
    timeMeaseure() {
      console.log("robím timeMeasure");
      clearInterval(this.interval);

      const step = Number(this.timeIntervalSeconds) || 60; // minúty medzi tickmi
      const idleLimit = 23 * step; // po toľkých minútach nečinnosti pauza

      this.idleTime = 0;

      console.log(
        "this.idleTime, step, idleLimit :>> ",
        this.idleTime,
        step,
        idleLimit
      );

      this.interval = setInterval(() => {
        if (this.idleTime >= idleLimit) {
          console.log(
            "⏸️ Pozastavené kvôli neaktivite. Pohni myšou/klávesnicou."
          );
          return;
        }

        this.pridajCasSledovania();

        this.idleTime += step;
      }, 1000 * step);
    },
    showOverlay() {
      this.overlayVisible = true;
    },
    resetIdleTime() {
      this.idleTime = 0; // Resetovať neaktívny čas
      this.time = 1;
    },
    nacitajProdukt() {
      const axios = require("axios");

      let cislo = window.location.href.split("?cislo=")[1];
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: `${this.$store.state.api}/product/load.php/?id=${cislo}`,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          switch (response.data.status) {
            case "Request succesfull":
              // Spracovanie úspešnej response.data.statuse
              this.vlastnenyProduktData = response.data.data;

              var cleanedJsonString = this.vlastnenyProduktData.details.replace(
                /[\n\t]/g,
                ""
              );
              var jsonObject = JSON.parse(cleanedJsonString);
              this.vlastnenyProduktDetails = jsonObject;

              this.aktualnaStupnica = this.vlastnenyProduktDetails.stupnice[0];
              console.log("this.aktualnaStupnica :>> ", this.aktualnaStupnica);

              if (this.vlastnenyProduktData.ciselne_zapisy?.length <= 0) {
                this.notyCiselnyZapis();
              }

              console.log("data", this.vlastnenyProduktDetails);
              return;

            case "Request failed":
              if (response.data.data === "Product not found") {
                // Spracovanie, keď produkt nie je nájdený
                this.$store.state.SnackBarText = "Produkt sa nenašiel";
                this.$router.go(-1);
              } else if (response.data.data === "Product not found in owned") {
                // Spracovanie, keď produkt nie je nájdený medzi vlastnenými

                this.$store.state.SnackBarText =
                  "Pre zobrazenie číselného zápisu musíte číslený zápis vlastniť";
                this.$router.go(-1);
              } else if (
                response.data.data === "Email not found / Multiple found"
              ) {
                // Spracovanie, keď email nie je nájdený alebo existuje viacero rovnakých emailov
                this.$store.state.SnackBarText = "Email sa nenašiel";
                this.$router.go(-1);
              } else if (response.data.data === "Not logged in") {
                // Spracovanie, keď užívateľ nie je prihlásený
                this.$store.state.SnackBarText = "Nie ste prihlásený";
                // this.$router.go(-1);
              } else if (response.data.data === "Trying too often") {
                // Spracovanie, keď sa pokúšate príliš často
                this.$store.state.SnackBarText = "Niečo sa pokazilo";
                this.$router.go(-1);
              } else {
                // Ak nezadáte žiadne ďalšie podmienky, môžete použiť tento else na neznáme odpovede
                this.$store.state.SnackBarText = "Niečo sa pokazilo";
                this.$router.go(-1);
              }
              return;

            case "Invalid request":
              // Spracovanie neplatnej požiadavky
              this.$store.state.SnackBarText = "Neaplatný id kľúč";
              this.$router.go(-1);
              return;

            default:
              // Ak nezadáte žiadne ďalšie podmienky, môžete použiť tento default na neznáme odpovede
              this.$store.state.SnackBarText = "Niečo sa pokazilo";
              this.$router.go(-1);
              return;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    notyCiselnyZapis() {
      const axios = require("axios");

      this.isLoading = true;
      this.notyReccursive++;

      var ciselnyZapisLoverCase = this.vlastnenyProduktData.name
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/\s/g, "_")
        .toLowerCase();

      // toto chce mi jurik dať preč
      // "../data/uploads/product/notes/" +
      // ciselnyZapisLoverCase +

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/pdf2jpg.php?path=" +
          ciselnyZapisLoverCase +
          "-" +
          this.aktualnaStupnica +
          ".pdf&page=" +
          this.cisloStrany +
          "&id=" +
          this.vlastnenyProduktData.id,
        // headers: { }
      };

      console.log("url :>> ", config.url);

      axios
        .request(config)
        .then(() => {
          // Assuming the response is the image blob
          this.ciselnyZapisObrazky.push(config.url);
          this.cisloStrany++;
          if (this.notyReccursive <= 40) {
            this.notyCiselnyZapis(); // Recursive call to fetch the next page
          }
          console.log("this.nacitaneNoty :>> ", this.nacitaneNoty);
        })
        .catch((error) => {
          if (error.response && error.response.status === 500) {
            console.log("All pages loaded or an error occurred");
            this.nacitaneNoty = true;
            this.isLoading = false;
          } else {
            console.log(error);
          }
        });
    },
    vratZvukovuUkazku() {
      return (
        this.$store.state.api +
        "/product/files/load.php/?id=" +
        this.vlastnenyProduktData.id +
        "&subdir=details&file=" +
        this.vlastnenyProduktData.name
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase() +
        "-Z.mp3"
      );
    },
    zistiDokonceny() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/user/finish/isProductsFinished.php/?id=" +
          this.vlastnenyProduktData.id,
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          if (response.data.data == "False") {
            this.jeDokonceny = false;
          } else {
            this.jeDokonceny = true;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    zmenDokonceny() {
      var cesta = "";
      if (!this.jeDokonceny) {
        cesta =
          this.$store.state.api +
          "/user/finish/finishProduct.php/?id=" +
          this.vlastnenyProduktData.id;
      } else {
        cesta =
          this.$store.state.api +
          "/user/finish/unfinishProduct.php/?id=" +
          this.vlastnenyProduktData.id;
      }
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: cesta,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request succesfull") {
            // if (!this.jeDokonceny) {
            //   this.$store.state.SnackBarText = "Produkt je dokončený";
            // } else {
            //   this.$store.state.SnackBarText = "Produkt už nieje dokončený";
            // }

            this.zistiDokonceny();
          } else {
            this.$store.state.SnackBarText =
              "Nepodarilo sa zmeniť dokončenosť produktu";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Niečo sa pokazilo";
        });
    },
    zistiOblubeny() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/user/like/isProductsLiked.php/?id=" +
          this.vlastnenyProduktData.id,
        // headers: { }
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          if (response.data.data == "False") {
            this.jeOblubeny = false;
          } else {
            this.jeOblubeny = true;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    zmenOblubeny() {
      var cesta = "";
      if (!this.jeOblubeny) {
        cesta =
          this.$store.state.api +
          "user/like/likeProduct.php/?id=" +
          this.vlastnenyProduktData.id;
      } else {
        cesta =
          this.$store.state.api +
          "user/like/unlikeProduct.php/?id=" +
          this.vlastnenyProduktData.id;
      }
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: cesta,
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request succesfull") {
            // if (!this.jeOblubeny) {
            //   this.$store.state.SnackBarText = "Produkt je obľúbený";
            // } else {
            //   this.$store.state.SnackBarText = "Produkt už nieje obľúbený";
            // }

            this.zistiOblubeny();
          } else {
            this.$store.state.SnackBarText =
              "Nepodarilo sa zmeniť obľúbenosť produktu";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Niečo sa pokazilo";
        });
    },
    async pridajCasSledovania() {
      const axios = require("axios");
      const cislo = new URLSearchParams(window.location.search).get("cislo");
      console.log("idem pridať čas sledovania :>> ", this.timeIntervalSeconds);

      try {
        const requests = [
          axios.get(
            `${this.$store.state.api}/user/watchtime/addProductWatchtime.php/?id=${cislo}`
          ),
          axios.get(`${this.$store.state.api}/helitime/addTime.php`),
          axios.get(`${this.$store.state.api}/achievements/add7Day.php`),
        ];

        const results = await Promise.allSettled(requests);
        results.forEach((r, i) => {
          const name = ["addTime", "addProductWatchtime"][i];
          if (r.status === "fulfilled")
            console.log(`✅ ${name}:`, r.value?.data);
          else console.warn(`⚠️ ${name} zlyhalo:`, r.reason);
        });
      } catch (err) {
        console.error("❌ Neočakávaná chyba pri pridávaní času", err);
      }
    },

    toggleInfo(what, smer) {
      // Štandardné prepínanie
      if (what === "text") {
        if (smer == "open") {
          this.showSongTextBox = true;
          this.showSongInfoBox = false;
        } else if (smer == "close") {
          this.showSongTextBox = false;
          this.showSongInfoBox = false;
        } else {
          this.showSongTextBox = !this.showSongTextBox;
          this.showSongInfoBox = false;
        }
      } else {
        if (smer == "open") {
          this.showSongInfoBox = true;
          this.showSongTextBox = false;
        } else if (smer == "close") {
          this.showSongInfoBox = false;
          this.showSongTextBox = false;
        } else {
          this.showSongInfoBox = !this.showSongInfoBox;
          this.showSongTextBox = false;
        }
      }
    },

    computeVisibleCountSimple(retries = 100) {
      const box = this.$refs.editorContainer;
      if (!box) return;
      const firstCast = box.querySelector(".cast");
      if (!firstCast) {
        if (retries > 0) {
          setTimeout(() => this.computeVisibleCountSimple(retries - 1), 100);
        } else {
          this.visibleCount = 5;
        }
        return;
      }
      const h =
        firstCast.offsetHeight +
        parseFloat(getComputedStyle(firstCast).marginBottom);
      this.visibleCount = Math.max(
        1,
        Math.floor(
          (window.innerHeight - box.getBoundingClientRect().top - 200) / h
        )
      );

      console.log("info :>> ", box, firstCast, h, this.visibleCount);
    },
  },
  watch: {
    isLoading(newVal) {
      if (newVal === false) {
        this.$nextTick(() => {
          this.computeVisibleCountSimple();
        });
      }
    },
    "vlastnenyProduktData.name"(newName) {
      if (newName) {
        document.title = `${newName} - Heligónková akadémia`;
      }
    },
    "vlastnenyProduktData.ciselne_zapisy": {
      handler(zapisy) {
        if (Array.isArray(zapisy) && zapisy.length === 0) {
          this.showSongInfoBox = true;
        }
      },
      immediate: true,
    },
    totalCasts: {
      handler(newTotal) {
        this.showSongInfoBox = this.visibleCount >= this.totalCasts;

        console.log(
          "produkt pre podmienku :>> ",
          this.visibleCount,
          this.totalCasts,
          newTotal
        );
      },
    },
    visibleCount: {
      handler(newTotal) {
        this.showSongInfoBox = this.visibleCount >= this.totalCasts;

        console.log(
          "produkt pre podmienku :>> ",
          this.visibleCount,
          this.totalCasts,
          newTotal
        );
      },
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

section .scroll {
  height: 94%;
  margin: 3% 0.1em 3% 0;
}

.scroll.prepojenie {
  height: 97%;
  padding-left: 1.5%;
  padding-top: 2%;
  margin: 1% 0.1em 3% 0;
}

.safari {
  will-change: transform;
  transform: translateZ(0);
}

//info popis

.popis {
  position: absolute;
  top: 0em;
  left: 28vw;
  transition-duration: 0.2s;

  img {
    width: 1.8em;
  }

  & .info-active {
    width: 3.2em;
    margin-left: -0.5em;
    margin-top: -0.6em;
  }

  &:hover {
    cursor: pointer;
    transform: scale(1.1);
  }
}

.tlacit {
  display: flex;
  align-items: center;
  position: relative;
  z-index: 1000;
  pointer-events: auto;
}

.tlacit.ovladanie {
  display: flex;
  flex-direction: column;
  gap: 0.4em;
  left: 1em;
}

.tlac-img {
  background-color: #fef35a;
  box-shadow: 0.1em 0.1em 1em #00000040;
  border-radius: 1rem;
  padding: 0.4em 0.7em;
  margin: 0 1em;
  width: 1.2em;
  z-index: 3;
  cursor: pointer;
  pointer-events: auto;

  display: flex;
  justify-content: center;
  align-items: center;

  transition: transform 0.25s ease, box-shadow 0.25s ease;

  &:hover {
    transform: perspective(600px) rotateX(10deg) rotateY(-10deg) scale(1.05);
    box-shadow: 0.3em 0.3em 1.2em #00000060;
  }

  &:active {
    transform: perspective(600px) rotateX(0deg) rotateY(0deg) scale(0.95);
    box-shadow: 0.1em 0.1em 0.6em #00000060;
  }
}

.popis-active {
  position: absolute;
  width: 80%;
  background-color: #8ec84e;
  box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.25);
  border-radius: 1rem;
  top: 35%;
  left: 50%;
  transform: translateX(-50%);
  padding: 1.3em 0.8em;

  p {
    white-space: break-spaces;
  }
}

.popis-deactive {
  display: none;
}

//až tu končí

.button:not(.add-naucene) {
  padding: 0.3em 1.2em;
  font-size: 2.2em;
}

.info {
  margin: auto 0;
}

.add-naucene {
  position: absolute;
  bottom: 1em;
  left: 50%;
  width: 11.8em;
  font-weight: 900;
  font-size: 120%;
  transform: translateX(-50%);
  justify-content: center;
}

.content {
  height: 100%;
  gap: 2%;
  display: flex;
  flex-direction: row;
  width: 100%;
  width: -webkit-fill-available;
}

.green {
  background: #8ec84e;
}

section {
  box-shadow: 0px 5px 8px 0px rgba(0, 0, 0, 0.5);
  padding: 0;
  background: #ededed;
}

.zapis {
  width: 100%;
  height: 100%;
  object-fit: contain;
  cursor: pointer;
}

img.zapis {
  pointer-events: none;
  /* Zabráni klikaniu na obrázky */
}

.full-screen-icon {
  position: absolute;
  bottom: 2%;
  width: 2em;
  border-radius: 0.4rem;
  right: 1.2em;
  padding: 0.7em;
  box-shadow: 3px 3px 5px rgba(0, 0, 0, 0.15);
  cursor: pointer;

  &:hover {
    transform: scale(1.1em);
    transition-duration: 0.3s;
  }
}

.add-favorite {
  position: fixed;
  width: max-content !important;
  bottom: 1em;
  left: 0.8vw;
  width: 3em;
  filter: drop-shadow(4px 3px 7px rgba(0, 0, 0, 0.25));

  display: flex;
  flex-direction: column;
  gap: 0.4em;
  align-items: center;

  img {
    width: 3.3em;
    cursor: pointer;

    &:hover {
      transform: scale(1.1);
      transition-duration: 0.2s;
    }
  }
}

.add-favorite.right {
  left: unset;
  right: 0.8vw;
}

.ovladanie {
  position: absolute;
  bottom: 0.7em; //1em
  width: 4em;
  cursor: pointer;
  filter: drop-shadow(4px 4px 6px rgba(0, 0, 0, 0.25));
  transition-duration: 0.2s;
  z-index: 10;
}

#sipkaLava {
  left: 1em;

  &:hover {
    transform: scale(1.2) rotate(-20deg);
  }
}

#sipkaPrava {
  right: 1em;
  transform: scaleX(-1);

  &:hover {
    transform: scaleX(-1) scale(1.2) rotate(-20deg);
  }
}

.bg-style {
  border-radius: 1.1875rem;
  border: 6px solid #fef35a;
  background: #90ca50;
  box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
  filter: blur(1.5px);
  position: absolute;

  width: 15em;
  height: 84vh;
  top: 7vh;
  left: 38.5%;
  transform: translateX(-50%);
  z-index: -1;
}

.jednaStranka {
  display: none;
}

.tablet {
  display: none;
}

.second {
  background-color: #ededed;

  margin-left: -2%;
  border-top-left-radius: 1.5em;
  border-bottom-left-radius: 1.5em;
}

//loading not animácia

.loader {
  border: 8px solid #f3f3f3;
  /* Light grey */
  border-top: 8px solid #90ca50;
  /* Blue */
  border-radius: 50%;
  width: 60px;
  height: 60px;
  animation: spin 2s linear infinite;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: auto;
  top: 43%;
  transform: translate(-50%, -50%);
  position: relative;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

.zapis {
  display: block;
  margin: auto;
}

//vratene staré štýli kvoli funkgovaní na mobile
.showInfo {
  width: 0;
  opacity: 0;
  /* Pridaj zmenu opacity pre jemnejší efekt */
  overflow: hidden;
  /* Skryje obsah počas animácie */
}

.showInfo.active {
  width: 100%;
  /* Alebo iná hodnota podľa potreby */
  opacity: 1;
  display: block;
}

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 4.5vw;
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

  font-size: 2.1875em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

.text p {
  white-space: break-spaces;
}

.text-piesne {
  text-align: center;
  font-size: 1.875em;
  font-style: normal;
  font-weight: 800;
  margin: 1.1em auto 0.7em;
}

.info-text {
  text-align: center;

  font-size: 1.5625em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%;
  /* 1.79688rem */
  margin-top: 0.3em;
}

.prehravac {
  margin-top: 0.5em;
}

.strong {
  color: #000;
  text-align: center;
  font-size: 1.5625em;
  font-style: normal;
  font-weight: 600;
  line-height: 115%;
  /* 1.79688rem */
}

.info-box {
  display: flex;
  justify-content: center;
  gap: 3vw;
  margin: 1.5em 0;
  align-items: stretch;

  .line {
    height: auto;
  }

  .info:first-child {
    max-width: 40%;
  }
}

.music {
  position: relative;
  margin: auto;
}

.info-buttons {
  display: flex;
  align-items: center;
  font-size: 60%;
  gap: 1em;
  margin: 2em;
  flex-direction: column;
  justify-content: space-around;

  .button {
    margin-bottom: 0.5em;
  }
}

.dificulty {
  transform: rotate(180deg);
  margin: auto;
  margin-top: -1.3em; //-1em
  width: 6em;
}

.text p:not(.text-piesne) {
  margin: 1em auto;
}

.text {
  padding-bottom: 5em;
}

.ma-b {
  margin-bottom: 0.5em;
}

//audio

.audio {
  position: absolute !important;
  top: 1vh;
  left: 0.2em;
  flex-direction: column;
  gap: 0.4em;
  font-size: 1em;
}

.audio.tlacit {
  left: unset;
  right: 0.4em;
}

.tlac-img img {
  width: 1em;
}

.audio-text {
  font-size: 0.65em;
  font-weight: 400;
}

//nové štýli pre openBar

.top-open-bar .toggle-info-btn {
  position: fixed;
  top: 3em;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 9em;
  border: none;
  border-radius: 1rem;
  padding: 0.5em 1em;
  cursor: pointer;
  z-index: 50;
  opacity: 0.8;
  transition-duration: 0.2s;

  &:hover {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1.1);
  }
}

.bottom-open-bar .toggle-info-btn {
  transform: rotate(180deg);
  cursor: pointer;
  z-index: 50;
  opacity: 0.8;
  transition-duration: 0.2s;
  width: 9em;
  border: none;
  border-radius: 1rem;
  padding: 0.5em 1em;

  &:hover {
    transform: scale(1.1) rotate(180deg);
    opacity: 1;

    p {
      opacity: 1;
    }
  }
}

.popis-text {
  font-weight: 500;
  font-size: 0.9em;
  text-align: center;
  opacity: 0.6;
}

.info-w-text {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 0;

  position: fixed;
  top: unset;
  bottom: 3em;
  transform: translate(-50%, +50%);

  z-index: 50;
  left: 50%;
}

//koniec

//posuvač

.posunutie {
  position: absolute;
  top: 50%;
  z-index: 2;

  img {
    width: 2em;
    height: auto;
    cursor: pointer;
    transition-duration: 0.2s;
  }
}

.left {
  left: -1.6%;

  transform: translate(-50%, -50%) rotate(180deg);

  img {
    &:hover {
      transform: scale(1.03);
    }
  }
}

.pravo {
  right: -1.5%;
  transform: translate(+50%, -50%);

  img {
    &:hover {
      transform: scale(1.03);
    }
  }
}

//koniec

@media only screen and (max-width: 1100px) {
  .tablet #sipkaLava {
    display: block;
  }

  .bg-style {
    display: none;
  }

  .scroll.prepojenie {
    height: 100%;
    padding-right: 0;
    padding-left: 4%;
  }

  .ovladanie {
    width: 3.5em;
  }

  .add-naucene {
    font-size: 100%;
    width: max-content;
    display: flex;
    height: auto;
    left: 35%;
    transform: translateX(-50%);
    bottom: 0.6em;
    filter: drop-shadow(4px 3px 7px rgba(0, 0, 0, 0.25));

    img {
      width: 3.7em;
    }
  }

  .add-favorite {
    font-size: 100%;
    width: max-content;
    left: 68%;
    transform: translateX(-50%);
    bottom: 0.6em;

    img {
      width: 3.7em;
    }
  }

  .second {
    font-size: 2vw;
    margin-left: auto;
    margin-right: auto;
  }

  .popis {
    right: 8vw;
    left: unset;
  }

  .computer {
    display: none;
  }

  h1 {
    font-size: 8.2vw;
  }

  .tablet {
    display: flex;

    .zapis {
      max-height: 80vh;
      border-radius: 1rem;
    }
  }

  .images {
    padding: 0 0 3em 1em;
  }

  .loader {
    width: 40px;
    height: 40px;
  }
}

@media only screen and (max-width: 750px) {
  .second {
    font-size: 2.3vw;
  }

  .tablet {
    height: auto;
    align-items: flex-start;
  }

  section {
    margin-bottom: 10em;
    height: auto;
  }

  h1 {
    font-size: 12vw;
  }

  .add-naucene {
    bottom: 1em;
  }

  .add-favorite {
    bottom: 1em;
  }

  .ovladanie {
    width: 10vw;
  }

  .loader {
    width: 30px;
    height: 30px;

    border: 5px solid #f3f3f3;
    /* Light grey */
    border-top: 5px solid #90ca50;
    /* Blue */
    margin-bottom: 4em;
  }

  .scroll {
    overflow-y: hidden;
  }

  .scroll.prepojenie {
    margin-bottom: 5em;
  }
}

@media only screen and (max-width: 500px) {
  .second {
    font-size: 3.3vw;
  }

  h1 {
    font-size: 12vw;
  }

  .ovladanie {
    width: 9vw;
  }
}
/* === PRINT LAYOUT === */
.print-only {
  display: none;
}

@media print {
  /* Základ: vytlačiť len náš print-root */
  :host {
    all: initial;
  }
  body {
    margin: 0 !important;
  }

  /* Skryť všetko ostatné v tejto view okrem print-root */
  .content,
  .computer,
  .tablet,
  .posunutie,
  .ovladanie,
  .add-favorite,
  .add-naucene,
  .top-open-bar,
  .bottom-open-bar,
  .audio,
  .music,
  .text,
  .popis,
  .info-w-text {
    display: none !important;
  }

  /* Zobraziť print-only sekciu */
  [id$="-print-root"].print-only {
    display: block !important;
  }

  /* Odstrániť pozadia a tiene, nech je to čisté pre tlač */
  section,
  [id$="-print-root"] {
    background: #fff !important;
    box-shadow: none !important;
    padding: 0 !important;
    margin: 0 !important;
  }

  /* Umožniť prirodzené zalamovanie obsahu na nové stránky */
  [id$="-print-root"] .scroll {
    height: auto !important;
    overflow: visible !important;
  }

  /* Zabránime roztrhnutiu jednotlivých častí zápisu cez stránku */
  .cast {
    break-inside: avoid;
    page-break-inside: avoid;
  }
  canvas,
  svg {
    max-width: 100% !important;
    height: auto !important;
    page-break-inside: avoid;
    break-inside: avoid;
  }

  /* Schovaj žltý dekor a radiusy v tlači */
  .bg-style {
    display: none !important;
  }
  [id$="-print-root"] {
    border-radius: 0 !important;
    border: none !important;
  }

  /* Zvýšenie veľkosti bez odsekovania – preferuj zoom (lepšie pre page breaky) */
  [id$="-print-root"] {
    zoom: 2; /* ⇦ nastav mierku tu (1.6 – 2.0) */
    transform: none !important;
    width: 100% !important;
  }

  /* Fallback pre prehliadače bez zoom (napr. Firefox) */
  @supports not (zoom: 2) {
    [id$="-print-root"] {
      transform: scale(1.8);
      transform-origin: top left;
      width: calc(100% / 1.8) !important; /* zachová plnú šírku */
    }
  }

  /* istota: nič zbytočne neodsúva obsah vnútri */
  [id$="-print-root"] .scroll {
    padding: 0 !important;
    margin: 0 !important;
  }

  [id$="-print-root"],
  [id$="-print-root"] > .scroll {
    border: 0 !important;
    border-radius: 0 !important;
    outline: none !important;
  }

  [id$="-print-root"],
  [id$="-print-root"] > .scroll {
    background: #fff !important;
    box-shadow: none !important;
    margin: 0 !important;
    padding: 0 !important;
  }
}
</style>

<style>
/* Globálne pravidlá iba pre tlač */
@media print {
  /* Pokus minimalizovať okraj – finálne riadi prehliadač / tlačiareň */
  @page {
    size: A4 portrait;
    margin: 0;
  }

  html,
  body {
    margin: 0 !important;
    padding: 0 !important;
    background: #fff !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  /* 1) Schovaj komplet celý DOM... */
  body * {
    visibility: hidden !important;
  }

  /* 2) ...okrem nášho print-rootu (a jeho detí) */
  [id$="-print-root"],
  [id$="-print-root"] * {
    visibility: visible !important;
  }

  /* 3) Umiestni print-root cez celý "paper" */
  [id$="-print-root"] {
    position: static !important; /* nech sa láme na stránky */
    inset: auto !important;
    width: 100% !important;
    margin: 0 !important;
    background: #fff !important;
    box-shadow: none !important;
    padding: 0 !important;
  }

  /* Zamedz roztrhnutiu blokov v rámci stránky */
  .cast {
    break-inside: avoid;
    page-break-inside: avoid;
  }
  canvas,
  svg {
    page-break-inside: avoid;
    break-inside: avoid;
    max-width: 100% !important;
    height: auto !important;
  }

  /* Lepšia farebná vernosť pri tlači (ak podporuje prehliadač) */
  html,
  body {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  .menu {
    display: none !important;
  }
  #app {
    padding: 2% !important;
    width: 100% !important;
  }

  div.information .info-buttons .button {
    background: transparent !important;
    box-shadow: none !important;
    margin: 1em auto 0.2em !important;
  }

  .template {
    font-size: 100% !important;
  }

  .drzanie img {
    z-index: 1 !important;
  }
}
</style>
