<template>
  <div class="content computer">
    <section class="first" :id="$route.name">
      <video
        class="lesson-video"
        data-tour="lesson-video"
        ref="videoPlayer"
        controls
        controlslist="nodownload"
        @loadedmetadata="onLoadedMetadata"
        @timeupdate="onTimeUpdate"
        @seeking="onSeeking"
        @seeked="onSeeked"
        @ended="onEnded"
      >
        <source
          ref="videoSource"
          v-if="shouldShowVideo"
          :src="videoSrc"
          type="video/mp4"
        />
      </video>

      <div class="obsah scroll">
        <p class="nadpis-aktualnej Adumu">Názov piesne:</p>
        <p class="nazov-aktualnej">{{ vlastnenyProduktData.name }}</p>

        <div class="line horizontal w-70"></div>

        <div class="text-aktualny">
          <p>{{ vlastnenyProduktData.details.info.replaceAll("//n", "\n") }}</p>
        </div>

        <div v-if="showText" class="text-aktualny">
          <p class="center">
            {{ vlastnenyProduktData.details.text.replaceAll("//n", "\n") }}
          </p>
        </div>

        <div
          @click="toggleText()"
          v-if="!showText"
          class="button"
          data-tour="lyrics-toggle"
          role="button"
          aria-label="Text piesne"
        >
          <p>Text piesne</p>
        </div>
        <div
          @click="toggleText()"
          v-else
          class="button"
          data-tour="lyrics-toggle"
          role="button"
          aria-label="Skryť text"
        >
          <p>Skryť text</p>
        </div>

        <div class="zvladnute">
          <div
            @click="zmenDokoncene()"
            v-if="!vlastnenyProduktData.jeDokoncene"
            class="button"
            data-tour="mark-complete"
            role="button"
            aria-label="Označiť ako zvládnuté"
          >
            <p>Označiť ako zvládnuté</p>
          </div>
          <div
            @click="zmenDokoncene()"
            v-else
            class="button green"
            data-tour="mark-complete"
            role="button"
            aria-label="Zvládnuté"
          >
            <p>Zvládnuté</p>
          </div>
        </div>
      </div>
    </section>

    <section class="second" :id="$route.name">
      <div class="scroll">
        <div class="song">
          <h1>Online výučba</h1>
          <h5 v-if="vlastnenyProduktData.difficulty == 1">Začiatočník</h5>
          <h5 v-else-if="vlastnenyProduktData.difficulty == 2">
            Stredne pokročilý
          </h5>
          <h5 v-else>Pokročilý</h5>

          <div class="line horizontal w-80"></div>

          <div class="dificulty">
            <img
              src="@/assets/images/obtiaznost/obtiaznostZac.png"
              alt="Začiatočník"
              v-if="vlastnenyProduktData.difficulty == 1"
            />
            <img
              src="@/assets/images/obtiaznost/obtiaznostStredPokro.png"
              alt="Stredne pokročilý"
              v-else-if="vlastnenyProduktData.difficulty == 2"
            />
            <img
              src="@/assets/images/obtiaznost/obtiaznostPokro.png"
              alt="Pokročilý"
              v-else
            />
          </div>

          <div>
            <p class="info-next">Ďalšie pesničky</p>
          </div>
        </div>

        <div class="zoznam">
          <div
            @click="onSongClick(item.id)"
            v-for="(item, index, count) in this.produktyData[difficulty]"
            :key="index"
            class="item"
          >
            <img
              v-if="this.produktyData[difficulty][index].id != undefined"
              :src="
                stiahniTitulniObrazok(this.produktyData[difficulty][index].id)
              "
              alt="Kurz"
              :class="{
                'green-border':
                  this.produktyData[difficulty][index].jeDokoncene,
              }"
            />
            <img v-else src="@/assets/images/gallery/defaultKurz.png" alt="" />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#{{ count + 1 }}</p>
              </div>
              <p class="info-text">
                {{ this.produktyData[difficulty][index].name }}
              </p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">
                {{ this.produktyData[difficulty][index].details.dlzkaVidea }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <div class="content tablet">
    <section class="second" :id="$route.name">
      <div class="scroll">
        <div class="song">
          <h1>Online výučba</h1>
          <h5 v-if="vlastnenyProduktData.difficulty == 1">Začiatočník</h5>
          <h5 v-else-if="vlastnenyProduktData.difficulty == 2">
            Stredne pokročilý
          </h5>
          <h5 v-else>Pokročilý</h5>

          <div class="line horizontal w-80"></div>

          <div class="dificulty">
            <img
              src="@/assets/images/obtiaznost/obtiaznostZac.png"
              alt="Začiatočník"
              v-if="vlastnenyProduktData.difficulty == 1"
            />
            <img
              src="@/assets/images/obtiaznost/obtiaznostStredPokro.png"
              alt="Stredne pokročilý"
              v-else-if="vlastnenyProduktData.difficulty == 2"
            />
            <img
              src="@/assets/images/obtiaznost/obtiaznostPokro.png"
              alt="Pokročilý"
              v-else
            />
          </div>
        </div>

        <video
          ref="videoPlayerTablet"
          data-tour="lesson-video"
          controls
          controlslist="nodownload"
          @loadedmetadata="onLoadedMetadata"
          @timeupdate="onTimeUpdate"
          @seeking="onSeeking"
          @seeked="onSeeked"
          @ended="onEnded"
        >
          <source
            ref="videoSourceTablet"
            v-if="shouldShowVideo"
            :src="videoSrc"
            type="video/mp4"
          />
          Váš prehliadač nepodporuje prehrávanie videa, prosím skúste použiť
          chrome.
        </video>
        <div class="obsah">
          <p class="nadpis-aktualnej Adumu">Názov piesne:</p>
          <p class="nazov-aktualnej">{{ vlastnenyProduktData.name }}</p>

          <div class="line horizontal w-70"></div>

          <div class="text-aktualny">
            <p>
              {{ vlastnenyProduktData.details.info.replaceAll("//n", "\n") }}
            </p>
          </div>

          <div v-if="showText" class="text-aktualny">
            <p class="center">
              {{ vlastnenyProduktData.details.text.replaceAll("//n", "\n") }}
            </p>
          </div>
          <div class="buttons">
            <div
              @click="zmenDokoncene()"
              v-if="!vlastnenyProduktData.jeDokoncene"
              class="button"
              data-tour="mark-complete"
            >
              <p>Označiť ako zvládnuté</p>
            </div>
            <div
              @click="zmenDokoncene()"
              v-else
              class="button green"
              data-tour="mark-complete"
            >
              <p>Zvládnuté</p>
            </div>
            <div
              @click="toggleText()"
              v-if="!showText"
              class="button"
              data-tour="lyrics-toggle"
            >
              <p>Text piesne</p>
            </div>
            <div
              @click="toggleText()"
              v-else
              class="button"
              data-tour="lyrics-toggle"
            >
              <p>Skryť text</p>
            </div>
          </div>
        </div>

        <div>
          <p class="info-next">Ďalšie pesničky</p>
        </div>

        <div class="zoznam">
          <div
            @click="onSongClick(item.id)"
            v-for="(item, index, count) in this.produktyData[difficulty]"
            :key="index"
            class="item"
          >
            <img
              v-if="this.produktyData[difficulty][index].id != undefined"
              :src="
                stiahniTitulniObrazok(this.produktyData[difficulty][index].id)
              "
              alt="Kurz"
              :class="{
                'green-border':
                  this.produktyData[difficulty][index].jeDokoncene,
              }"
            />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#{{ count + 1 }}</p>
              </div>
              <p class="info-text">
                {{ this.produktyData[difficulty][index].name }}
              </p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">
                {{ this.produktyData[difficulty][index].details.dlzkaVidea }}
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      popisOtvor: false,
      noty: false,
      vlastnenyProduktData: {},
      produktyID: [],
      produktyData: {
        lahke: {},
        stredne: {},
        tazke: {},
      },
      showText: false,
      difficulty: "",
      shouldShowVideo: false,
      interval: null,
      videoSrc: "",
      idleTime: 0,
      time: 1,
      watchedSeconds: 0, // reálne odpozerané sekundy
      lastUpdateTs: 0, // čas posledného ticku (performance.now)
      lastVideoTime: 0, // last currentTime (na výpočet delta)
      recentSeek: false, // či prebehol nedávno seek
      sentWatched: false, // či už odoslané "pozreté"
      antiSkipEnabled: true, // zap/vyp ochranu proti skoku na koniec
      timeIntervalSeconds: 60, // default, prepisuje sa z configu
      requiredPercent: 0.6, // 90 % reálne odpozerané
      lastSecondsWindow: 10, // posledných 10 s
      antiSkipMinPercent: 0.6, // na anti-skip pri skoku na koniec (môžeš nechať 0.6 alebo koľko chceš)
      seekCooldownMs: 2000,
    };
  },
  mounted() {
    window.scrollTo(0, 0);

    this.nacitajProdukt();

    this.nastavObtiaznost();

    function checkAndLoadProducts() {
      if (this.vlastnenyProduktData.id !== undefined) {
        this.nacitajProduktyID();
        clearInterval(intervalId);
      }
    }

    //meranie času
    this.loadHelitimeConfig();

    const intervalId = setInterval(checkAndLoadProducts.bind(this), 250);

    window.addEventListener("mousemove", this.resetIdleTime);
    window.addEventListener("keypress", this.resetIdleTime);
    window.addEventListener("touchstart", this.resetIdleTime, {
      passive: true,
    });
  },
  beforeUnmount() {
    this.stopVideo();
    window.removeEventListener("mousemove", this.resetIdleTime);
    window.removeEventListener("keypress", this.resetIdleTime);
    window.removeEventListener("touchstart", this.resetIdleTime, {
      passive: true,
    });
    clearInterval(this.interval);
  },
  watch: {
    "$route.query.cislo"(newValue) {
      console.log("newValue :>> ", newValue);
      this.resetIdleTime();
      this.$nextTick(() => {
        window.scrollTo(0, 0);

        this.shouldShowVideo = false;

        this.stopVideo();

        this.nacitajProdukt();

        this.nastavObtiaznost();

        this.produktyData = {
          lahke: {},
          stredne: {},
          tazke: {},
        };

        function checkAndLoadProducts() {
          if (this.vlastnenyProduktData.id !== undefined) {
            this.nacitajProduktyID();
            clearInterval(intervalId);
          }
        }

        const intervalId = setInterval(checkAndLoadProducts.bind(this), 250);
      });
    },
  },
  methods: {
    async loadHelitimeConfig() {
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/helitime/config/helitime.config.json",
          { credentials: "include" }
        );
        const cfg = await res.json();
        const secs = Number(cfg.helitime_timeout_seconds) + 1 || 60;

        this.timeIntervalSeconds = secs;
        console.log("[HELTIME] Config načítaný → interval:", secs, "s");

        // Po načítaní configu reštartujeme meranie, aby sa aplikoval nový interval
        this.restartHeliTimer();
      } catch (err) {
        console.warn(
          "[HELTIME] Nepodarilo sa načítať config, idem s defaultom 60s.",
          err
        );
        this.restartHeliTimer();
      }
    },
    restartHeliTimer() {
      clearInterval(this.interval);
      this.timeMeaseure();
    },
    onLoadedMetadata(e) {
      const v = e.target;
      this.watchedSeconds = 0;
      this.lastUpdateTs = performance.now();
      this.lastVideoTime = v.currentTime || 0;
      this.recentSeek = false;
      this.sentWatched = false;

      console.log("[VIDEO] loadedmetadata:", {
        duration: v.duration,
        currentTime: v.currentTime,
      });
    },

    onTimeUpdate(e) {
      const v = e.target;
      if (!v || !isFinite(v.duration) || v.duration <= 0) return;

      const now = performance.now();
      const dt = (now - this.lastUpdateTs) / 1000; // čas od posledného ticku (s)
      const dpos = Math.abs(v.currentTime - this.lastVideoTime); // posun na časovej osi (s)

      // Heuristika "reálne pozreté": rátame len malé, plynulé posuny
      // (nie obrovský skok za milisekundu).
      const plausibleMove = dpos <= dt * 1.7 + 0.25 && dt < 5;

      if (!v.paused && v.playbackRate > 0 && plausibleMove) {
        this.watchedSeconds += dpos;
      } else {
        // podozrivé skoky/seeky nepočítame
        // console.log("[VIDEO] skip/seek alebo pauza – nezapočítavam", { dpos, dt });
      }

      this.lastUpdateTs = now;
      this.lastVideoTime = v.currentTime;

      // ...
      this.lastUpdateTs = now;
      this.lastVideoTime = v.currentTime;

      // ⬇️ sem vlož tento nový výpočet
      const remaining = v.duration - v.currentTime;
      const progress = v.duration > 0 ? this.watchedSeconds / v.duration : 0;
      const inLastWindow = remaining <= this.lastSecondsWindow;

      const kvalifikujeNaZaver =
        inLastWindow && progress >= this.requiredPercent && !this.recentSeek;
      // ⬆️ koniec nového výpočtu

      // Throttle info logy každých ~15s prehrávania, nech nie je konzola spam
      if (Math.floor(this.watchedSeconds) % 15 === 0) {
        console.log("[VIDEO] progress:", {
          watchedSeconds: Math.floor(this.watchedSeconds),
          currentTime: Math.floor(v.currentTime),
          remaining: Math.ceil(remaining),
          duration: Math.floor(v.duration),
        });
      }

      if (!this.sentWatched && kvalifikujeNaZaver) {
        this.sentWatched = true;
        console.log(
          "✅ [VIDEO] Splnené podmienky v posledných 10 s – označujem ako pozreté."
        );
        this.oznacAkoPozrete();
      }
    },

    onSeeking(e) {
      const v = e.target;
      this.recentSeek = true;

      if (
        this.antiSkipEnabled &&
        v.duration &&
        v.currentTime >= v.duration - this.lastSecondsWindow &&
        this.watchedSeconds / v.duration < 0.6 // alebo použi this.antiSkipMinPercent, ak ho máš
      ) {
        const backTo = Math.max(0, v.duration - this.lastSecondsWindow - 5);
        console.warn(
          "⛔️ [VIDEO] Preskočenie na koniec zablokované. Návrat na:",
          backTo.toFixed(1),
          "s"
        );
        try {
          v.currentTime = backTo;
        } catch (err) {
          console.warn("Nemohol som nastaviť currentTime:", err);
        }
      }
    },
    onSeeked() {
      setTimeout(() => (this.recentSeek = false), this.seekCooldownMs);
    },

    onEnded(e) {
      const v = e.target;
      if (!this.sentWatched && v && isFinite(v.duration) && v.duration > 0) {
        const progress = this.watchedSeconds / v.duration;
        const qualify = progress >= this.requiredPercent && !this.recentSeek;

        console.log("[VIDEO] ended decision:", {
          watchedSeconds: +this.watchedSeconds.toFixed(2),
          duration: +v.duration.toFixed(2),
          progress: +(progress * 100).toFixed(1) + "%",
          requiredPercent: this.requiredPercent * 100 + "%",
          recentSeek: this.recentSeek,
          qualify,
        });

        if (qualify) {
          this.sentWatched = true;
          console.log(
            "✅ [VIDEO] ended → podmienky splnené, označujem ako pozreté."
          );
          this.oznacAkoPozrete();
        } else {
          const reasons = [];
          if (progress < this.requiredPercent)
            reasons.push("málo reálne odpozerané");
          if (this.recentSeek) reasons.push("čerstvý seek");
          console.log("ℹ️ [VIDEO] ended → NESPLNENÉ:", reasons.join(" + "));
        }
      }
    },

    async oznacAkoPozrete() {
      try {
        const id =
          Number(this.vlastnenyProduktData?.id) ||
          Number(this.$route.query.cislo);

        if (!id) {
          console.error(
            "❌ [VIDEO] Nemám ID produktu (ani vo vlastnenyProduktData, ani v route)."
          );
          return;
        }

        console.log("🏁 [VIDEO] Označujem ako pozreté, posielam id =", id);

        const res = await fetch(
          "https://heligonkovaakademia.sk/api/achievements/videoWatched.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            credentials: "include", // dôležité kvôli PHP session
            body: new URLSearchParams({ id: String(id) }),
          }
        );

        const text = await res.text();
        let parsed;
        try {
          parsed = JSON.parse(text);
        } catch {
          parsed = text;
        }
        console.log(`✅ [VIDEO] videoWatched.php (${res.status}) →`, parsed);
      } catch (err) {
        console.error("❌ [VIDEO] Chyba pri označovaní (achievements):", err);
      }

      console.log("🏁 [VIDEO] Nastavujem produkt na dokončený (idempotentne).");
      this.zmenDokoncene(true);
    },
    resetIdleTime() {
      this.idleTime = 0;
      this.time = 0;
    },
    timeMeaseure() {
      clearInterval(this.interval);

      const stepSec = Number(this.timeIntervalSeconds) || 60; // sekundy medzi tickmi
      const idleLimitSec = 23 * stepSec; // po 23× intervale pauza

      // Reset neaktívneho času a štart
      this.idleTime = 0;

      const tick = () => {
        // Ak je používateľ "dlho ticho", nič nepripisujeme až do ďalšieho pohybu
        if (this.idleTime >= idleLimitSec) {
          console.log(
            "⏸️ [HELTIME] Pozastavené kvôli neaktivite (≥",
            idleLimitSec,
            "s)."
          );
          return;
        }

        // Aktívny režim → voláme API (pridanie watchtime)
        console.log("⏱️ [HELTIME] tick → pridávam čas (step", stepSec, "s)");
        this.pridajCasSledovania();

        // Ak neprišla aktivita, rátame neaktivitu o jeden krok
        this.idleTime += stepSec;
      };

      // 🔔 Okamžitý prvý tick (nečakáme stepSec)
      tick();
      this.interval = setInterval(tick, stepSec * 1000);
    },
    stopVideo() {
      const videoPlayer = this.$refs.videoPlayer;
      const videoSource = this.$refs.videoSource;
      if (videoPlayer && videoSource) {
        videoPlayer.pause();
        videoSource.src = "";
        videoPlayer.load();
      }
      const videoPlayerTablet = this.$refs.videoPlayerTablet;
      const videoSourceTablet = this.$refs.videoSourceTablet;
      if (videoPlayerTablet && videoSourceTablet) {
        videoPlayerTablet.pause();
        videoSourceTablet.src = "";
        videoPlayerTablet.load();
      }
    },
    onSongClick(id) {
      this.stopVideo();
      this.$router.replace({
        path: "/ucebna/naucne-video",
        query: {
          cislo: id,
        },
      });
    },
    zistiDokoncenyProdukty(id) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/user/finish/isProductsFinished.php/?id=" +
          id,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.data == "False") {
            this.produktyData[this.difficulty][id].jeDokoncene = false;
          } else {
            this.produktyData[this.difficulty][id].jeDokoncene = true;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nastavObtiaznost() {
      if (this.vlastnenyProduktData.difficulty != undefined) {
        if (this.vlastnenyProduktData.difficulty == 1) {
          this.difficulty = "lahke";
        } else if (this.vlastnenyProduktData.difficulty == 2) {
          this.difficulty = "stredne";
        } else {
          this.difficulty = "tazke";
        }
      } else {
        setTimeout(() => {
          this.nastavObtiaznost();
        }, 250);
      }
    },
    nacitajProduktyID() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/search.php?&pagination_index=0&pagination_limit=1000&details_key=typ&details_value=Video",
      };

      axios
        .request(config)
        .then((response) => {
          const jsonArray = response.data.data;

          for (let i = 0; i < jsonArray.length; i++) {
            const id = jsonArray[i].id;
            if (id != this.vlastnenyProduktData.id) {
              this.produktyID.push(id);
              this.nacitajProduktyData(id);
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nacitajProduktyData(id) {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            if (response.data.data.difficulty == 1) {
              this.produktyData.lahke[response.data.data.id] =
                response.data.data;
              this.produktyData.lahke[response.data.data.id].details =
                JSON.parse(
                  this.produktyData.lahke[
                    response.data.data.id
                  ].details.replace(/[\n\t]/g, "")
                );
            } else if (response.data.data.difficulty == 2) {
              this.produktyData.stredne[response.data.data.id] =
                response.data.data;
              this.produktyData.stredne[response.data.data.id].details =
                JSON.parse(
                  this.produktyData.stredne[
                    response.data.data.id
                  ].details.replace(/[\n\t]/g, "")
                );
            } else {
              this.produktyData.tazke[response.data.data.id] =
                response.data.data;
              this.produktyData.tazke[response.data.data.id].details =
                JSON.parse(
                  this.produktyData.tazke[
                    response.data.data.id
                  ].details.replace(/[\n\t]/g, "")
                );
            }
            this.zistiDokoncenyProdukty(id);
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nacitajProdukt() {
      this.vlastnenyProduktData = {};
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/load.php/?id=" +
          this.$route.query.cislo,
      };

      axios
        .request(config)
        .then((response) => {
          switch (response.data.status) {
            case "Request succesfull":
              this.vlastnenyProduktData = response.data.data;

              this.vlastnenyProduktData.details = JSON.parse(
                this.vlastnenyProduktData.details.replace(/[\n\t]/g, "")
              );

              document.title = `Heligónková Akadémia | Náučné video ${this.vlastnenyProduktData.name}`;

              this.zistiDokonceny();

              this.shouldShowVideo = true;
              this.videoSrc = this.vratVideo();
              return;

            case "Request failed":
              if (response.data.data === "Product not found") {
                this.$store.state.SnackBarText = "Produkt sa nenašiel";
                this.$router.go(-1);
              } else if (response.data.data === "Product not found in owned") {
                this.$store.state.SnackBarText =
                  "Pre zobrazenie číselného zápisu musíte číslený zápis vlastniť";
                this.$router.go(-1);
              } else if (
                response.data.data === "Email not found / Multiple found"
              ) {
                this.$store.state.SnackBarText = "Email sa nenašiel";
                this.$router.go(-1);
              } else if (response.data.data === "Not logged in") {
                this.$store.state.SnackBarText = "Nie ste prihlásený";
              } else if (response.data.data === "Trying too often") {
                this.$store.state.SnackBarText = "Niečo sa pokazilo";
                this.$router.go(-1);
              } else {
                this.$store.state.SnackBarText = "Niečo sa pokazilo";
                this.$router.go(-1);
              }
              return;

            case "Invalid request":
              this.$store.state.SnackBarText = "Neplatný id kľúč";
              this.$router.go(-1);
              return;

            default:
              this.$store.state.SnackBarText = "Niečo sa pokazilo";
              this.$router.go(-1);
              return;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    vratVideo() {
      return (
        this.$store.state.api +
        "/product/files/load.php/?id=" +
        this.vlastnenyProduktData.id +
        "&file=" +
        this.vlastnenyProduktData.name
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase() +
        "-V.mp4"
      );
    },
    toggleText() {
      this.showText = !this.showText;
    },
    zistiDokonceny() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/user/finish/isProductsFinished.php/?id=" +
          parseInt(this.vlastnenyProduktData.id, 10),
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.data == "False") {
            this.vlastnenyProduktData.jeDokoncene = false;
          } else {
            this.vlastnenyProduktData.jeDokoncene = true;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    zmenDokoncene(
      target /* optional: true = nastaviť na dokončené, false = odobrať, undefined = toggle */
    ) {
      const id = this.vlastnenyProduktData.id;
      const axios = require("axios");

      let shouldFinish;
      if (typeof target === "boolean") {
        shouldFinish = target; // hard-set režim
      } else {
        shouldFinish = !this.vlastnenyProduktData.jeDokoncene; // toggle
      }

      const cesta = shouldFinish
        ? `${this.$store.state.api}/user/finish/finishProduct.php/?id=${id}`
        : `${this.$store.state.api}/user/finish/unfinishProduct.php/?id=${id}`;

      console.log(
        "[FINISH] posielam:",
        shouldFinish ? "FINISH" : "UNFINISH",
        "→",
        cesta
      );

      axios
        .get(cesta)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.vlastnenyProduktData.jeDokoncene = shouldFinish;
            console.log(
              "[FINISH] OK → jeDokoncene =",
              this.vlastnenyProduktData.jeDokoncene
            );
            // pre istotu si môžeš osviežiť zo servera:
            // this.zistiDokonceny();
          } else {
            this.$store.state.SnackBarText =
              "Nepodarilo sa zmeniť dokončenosť produktu";
            console.warn("[FINISH] response != succesfull:", response.data);
          }
        })
        .catch((error) => {
          console.error("[FINISH] chyba:", error);
          this.$store.state.SnackBarText = "Niečo sa pokazilo";
        });
    },
    stiahniTitulniObrazok(id) {
      return (
        this.$store.state.api +
        "/product/files/load.php/?id=" +
        this.produktyData[this.difficulty][id].id +
        "&subdir=details&file=" +
        this.produktyData[this.difficulty][id].name
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .replace(/\s/g, "_")
          .toLowerCase() +
        "-T.png"
      );
    },
    pridajCasSledovania() {
      const axios = require("axios");
      const cislo = this.$route.query.cislo;

      axios
        .get(
          this.$store.state.api +
            "/user/watchtime/addProductWatchtime.php/?id=" +
            cislo
        )
        .then((response) => {
          console.log("✅ addProductWatchtime:", response.data);
        })
        .catch((error) => {
          console.error("❌ Chyba pri addProductWatchtime:", error);
        });

      axios;
      axios
        .get(`${this.$store.state.api}/helitime/addTime.php`)
        .then((response) => {
          console.log("✅ addTime.php:", response.data);
        })
        .catch((error) => {
          console.error("❌ Chyba pri addTime.php:", error);
        });
      axios
        .get("https://heligonkovaakademia.sk/api/helitime/addTime.php")
        .then((response) => {
          console.log("✅ addTime.php:", response.data);
        })
        .catch((error) => {
          console.error("❌ Chyba pri addTime.php:", error);
        });
      axios
        .get(`${this.$store.state.api}/achievements/add7Day.php`)
        .then((response) => {
          console.log("✅ addTime.php:", response.data);
        })
        .catch((error) => {
          console.error("❌ Chyba pri addTime.php:", error);
        });
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

section .scroll.obsah {
  padding: 0 5% 1em;
}

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 3vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0;
}

.center {
  text-align: center !important;
}

h5 {
  text-align: center;

  font-size: 1.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  margin: 0.6em auto;
}

section .scroll {
  height: 98%;
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
  margin-top: 0.3em;
}

.nadpis-aktualnej {
  font-size: 2.2em;
}

.nazov-aktualnej {
  font-size: 1.8em;
}

.text-aktualny p {
  text-align: left;
  font-size: 1.1em;
  white-space: break-spaces;
}

.green-border {
  border: 0.18rem solid #90ca50 !important;
}

.text-aktualny {
  margin: 1em 0 3em;
}

.first .line {
  margin: 0.4em auto 2em;
}

.strong {
  color: #000;
  text-align: center;
  font-size: 1.5625em;
  font-style: normal;
  font-weight: 600;
  line-height: 115%;
}

.content {
  height: 100%;
  gap: 3.5%;
  display: flex;
  flex-direction: row;
  width: 100%;
  width: -webkit-fill-available;
}

.first {
  width: 65%;
  display: flex;
  flex-direction: column;
  padding: 1.85%;
}

.second {
  width: 35%;
}

.green {
  background: #8ec84e;
}

.text p:not(.text-piesne) {
  margin: 1em auto;
}

.text {
  padding-bottom: 5em;
}

section {
  box-shadow: 0px 5px 8px 0px rgba(0, 0, 0, 0.5);
  padding: 0;
}

.ma-b {
  margin-bottom: 0.5em;
}

.jednaStranka {
  display: none;
}

.tablet {
  display: none;
}

.second {
  background-color: #ededed;
}

.dificulty {
  transform: rotate(180deg) translateY(1.3em);
  z-index: -1;
  position: relative;

  img {
    width: 4.8em;
  }
}

.info-next {
  margin-bottom: 1em;
  margin-top: -0.5em;
  font-size: 1.5em;
}

.more {
  margin: 1em auto 0;
  width: 2em;
  transition-duration: 0.2s;
  cursor: pointer;

  &:hover {
    transform: scale(1.1);
  }
}

.buttons {
  display: flex;
  justify-content: space-around;

  .button {
    font-size: 1.1em;
  }
}

video {
  width: 100%;
  height: auto;
  object-fit: cover;
  border-radius: 2rem;
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
  outline: 0.2em solid #fef35a;
  filter: drop-shadow(0.2px 0.2px 10px rgba(0, 0, 0, 0.3));
}

.item {
  display: flex;
  flex-direction: row;
  gap: 2%;
  font-size: 0.95vw;
  justify-content: center;
  margin: 1em 0;
  cursor: pointer;

  .info {
    align-items: flex-start;
    width: 55%;
  }

  .info-text {
    font-size: 1em;
    text-align: left;
  }

  img {
    border: 0.18rem solid #fef35a;
    border-radius: 0.7rem;
    box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.35);
    width: 35%;
    height: 6em;
    object-fit: cover;
    margin: auto 0.1em;
  }

  .nadpis {
    justify-content: space-between;
    display: flex;

    p {
      font-size: 1.1em;
      letter-spacing: 1px;
    }
  }

  .nazov {
    margin-bottom: 0.2em;
  }
}

@media only screen and (max-width: 1100px) {
  .tablet section {
    width: 100%;

    .w-70 {
      margin: 1em auto;
    }

    .buttons .button {
      font-size: 0.9em;
    }

    .text-aktualny p {
      text-align: center;
    }

    .obsah {
      margin-bottom: 3em;
    }

    .item .nadpis p {
      font-size: 2.1em;
    }

    .item img {
      border-radius: 1.2rem;
      height: 18em;
    }

    .item .info-text {
      font-size: 2em;
    }

    .item {
      margin: 3em 0;
    }

    .scroll {
      margin: 2% 0;
      padding: 0 3%;
      height: 96%;
    }
  }

  .second {
    font-size: 2vw;
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
    }
  }

  .images {
    padding-bottom: 2em;
  }
}

@media only screen and (max-width: 750px) {
  .second {
    font-size: 2.3vw;
  }

  .tablet {
    height: auto;
    align-items: flex-start;

    video {
      border-radius: 1rem;
    }

    section .item .nadpis p {
      font-size: 3em;
    }

    .item .info {
      width: 100%;
    }

    section .item .info-text {
      font-size: 2.9em;
    }

    .obsah > p,
    .text-aktualny p {
      margin: 0.3rem auto;
    }

    .buttons {
      flex-direction: column-reverse;
      align-items: center;

      .button {
        font-size: 1.2em;
        margin: 0.5em auto;
      }
    }
  }

  section {
    margin-bottom: 10em;
    height: auto;
  }

  h1 {
    font-size: 12vw;
  }

  .add-naucene {
    left: 44%;
    bottom: 1em;
    font-size: 140%;
  }

  .add-favorite {
    bottom: 0.9em;
    left: 72.2%;

    img {
      width: 3.7em;
    }
  }

  .text {
    padding-bottom: 2em;
  }

  .ovladanie {
    width: 11vw;
  }

  .images {
    padding-bottom: 2em;
  }
}

@media only screen and (max-width: 500px) {
  .second {
    font-size: 3.3vw;
  }

  h1 {
    font-size: 12vw;
  }

  .add-naucene {
    left: 44%;

    bottom: 0.7em;
    font-size: 110%;
  }

  .add-favorite {
    bottom: 0.4em;
    left: 71%;

    img {
      width: 3em;
    }
  }

  .text {
    padding-bottom: 2em;
  }

  .ovladanie {
    width: 11vw;
  }

  .images {
    padding-bottom: 2em;
  }
}

@media only screen and (max-width: 400px) {
  .add-naucene p {
    font-size: 0.8em;
  }
}
</style>
