<template>
  <TheLoader v-if="!$store.state.isLoaded" />

  <AdminLogin
    v-if="
      $store.state.isLoaded &&
      $store.state.user != null &&
      $store.state.user.isAdmin
    "
    class="admin"
  />

  <!-- JEDINÁ inštancia router-view -->
  <div class="container">
    <router-view v-if="$store.state.isLoaded" />
  </div>

  <TheMenu v-if="isLoaded && !isOpenCalendar" />

  <TheInfoMenu
    v-if="$store.state.ui.info.open"
    :student-id="$store.state.ui.info.lesson?.studentIds?.[0] || null"
    :duo-student-id="$store.state.ui.info.lesson?.studentIds?.[1] || null"
    :calendar-id="$store.state.ui.info.lesson?.id || null"
    :calendar-day-name="$store.state.ui.info.lesson?.dayName || ''"
    :calendar-hour="$store.state.ui.info.lesson?.hour || ''"
    :form-of-study="$store.state.ui.info.lesson?.formOfStudy || ''"
  />

  <TheMenuMobile v-if="isLoaded && !isOpenCalendar" />

  <ImageOverlay
    :images="$store.state.ciselnyZapisObrazky"
    :visible="$store.state.overlayVisible"
    @close="
      $store.state.overlayVisible = false;
      $store.state.ciselnyZapisObrazky = [];
    "
  />

  <gratulacia-oznamenie
    v-if="gratulation && gratulation.show"
    :text="gratulation.message"
    @close="$store.commit('SET_GRATULATION', false)"
  />

  <snack-bar />

  <div id="print-warning" v-show="printWarningVisible" data-nosnippet>
    <div class="warning-box">
      <strong>⚠️ Screenshotovanie a tlač tejto stránky je zakázaná.</strong>
      <p>
        Tento obsah je chránený a určený výhradne pre interné alebo súkromné
        použitie.<br />
      </p>
    </div>
  </div>

  <!-- Globálny guide overlay (jedenkrát v appke) -->
  <GuideOverlay />

  <!-- Globálne FAB tlačidlo – spustí tour -->
  <transition name="fab-fade" appear>
    <button
      v-if="guideFabVisible"
      class="guide-fab"
      type="button"
      @click="startGlobalTour"
      aria-label="Spustiť krátky návod"
    >
      ?
    </button>
  </transition>

  <div class="version">
    <version-show />
  </div>
</template>

<script>
/* eslint-disable */
import axios from "axios";
import TheMenu from "@/components/Menu/TheMenu.vue";
import TheMenuMobile from "@/components/Menu/TheMenuMobile.vue";
import TheLoader from "@/components/Functionality/TheLoader.vue";
import SnackBar from "@/components/Functionality/SnackBar.vue";
import AdminLogin from "@/components/Functionality/AdminLogin.vue";
import ImageOverlay from "@/components/Functionality/FullScreenZapisy.vue";
import GratulaciaOznamenie from "@/components/Functionality/GratulaciaOznamenie.vue";
import TheInfoMenu from "@/components/Edupage/TheInfoMenu.vue";
import VersionShow from "./components/Helicas/Fungovanie/VersionShow.vue";
import GuideOverlay from "@/components/Navigacia/GuideOverlay.vue";
import { tour } from "@/components/Navigacia/tour/tour"; // <-- TOUR API

const apiFriend = axios.create({
  baseURL: "/api/friend",
  withCredentials: true,
});

export default {
  components: {
    TheMenu,
    TheMenuMobile,
    TheLoader,
    SnackBar,
    AdminLogin,
    ImageOverlay,
    GratulaciaOznamenie,
    TheInfoMenu,
    VersionShow,
    GuideOverlay,
  },
  data() {
    return {
      isLoaded: false,
      intervalAdmin: null,
      intervalUser: null,

      // notifikácie
      notifyNextAt: 0,
      notifyIsChecking: false,
      notifyActiveMs: 0,
      notifyLastActivityAt: 0,
      notifyMinActiveMs: 0,
      notifyTimer: null,
      notifyTriggeredThisSession: false,

      // online ping
      globalOnlineTimer: null,
      onVisHandler: null,
      _onVisHandler: null,
      printWarningVisible: false,
    };
  },
  computed: {
    isOpenCalendar() {
      return this.$store.state.ui?.info?.open === true;
    },
    gratulation() {
      return this.$store.state.info.gratulation;
    },
    guideFabVisible() {
      return !tour.state.open;
    },
  },
  methods: {
    /* ---------------- TOUR spúšťač (hlavná zmena) ---------------- */
    // Pomocný runner: čaká na GuideOverlay a spustí tour + otvorí overlay
    async runTour({ mode = "full", startIndex = 0, steps = null } = {}) {
      const doStart = async () => {
        await tour.start({
          mode, // "home" | "menu" | "full"
          startIndex,
          steps, // ak pošleš vlastné kroky, použijú sa tie
        });
        // otvor vizuálny overlay (GuideOverlay)
        if (window.haTour?.start) {
          window.haTour.start(0);
        } else {
          // fallback: eventom
          try {
            window.dispatchEvent(
              new CustomEvent("ha.tour.start", { detail: { index: 0 } })
            );
          } catch (e) {}
        }
      };

      if (window.__haTourReady) {
        await doStart();
      } else {
        const onReady = async () => {
          window.removeEventListener("ha.tour.ready", onReady);
          await doStart();
        };
        window.addEventListener("ha.tour.ready", onReady, { once: true });
      }
    },

    startGlobalTour() {
      // full = domovská etapa + menubar (presety sú už v tour.js)
      this.runTour({
        mode: "full",
        startIndex: 0,
        // steps: [...tour.presets.defaultHomeSteps(), ...tour.presets.menuSteps()], // ak chceš natvrdo
      });
    },

    /* ---------------- zvyšok tvojich metód bez zmeny ---------------- */
    randomInt(min, max) {
      return Math.floor(Math.random() * (max - min + 1)) + min;
    },
    initNotifyCredit() {
      const saved = Number(localStorage.getItem("ha.notifyCredit.nextAt")) || 0;
      this.notifyNextAt = saved;
      this.notifyMinActiveMs = this.randomInt(10 * 60 * 1000, 15 * 60 * 1000);
      this.notifyActiveMs = 0;
      this.notifyTriggeredThisSession = false;
      this.notifyLastActivityAt = performance.now();
      if (this.notifyTimer) clearInterval(this.notifyTimer);
      this.notifyTimer = setInterval(this.tickNotifyActivity, 15 * 1000);
    },
    markUserActive() {
      this.notifyLastActivityAt = performance.now();
    },
    onVisibilityChange() {
      if (!document.hidden) this.markUserActive();
    },
    tickNotifyActivity() {
      const nowPerf = performance.now();
      const recentlyActive = nowPerf - this.notifyLastActivityAt <= 30 * 1000;
      if (recentlyActive) this.notifyActiveMs += 15 * 1000;
      if (this.notifyTriggeredThisSession) return;
      if (this.notifyActiveMs >= this.notifyMinActiveMs) {
        this.notifyTriggeredThisSession = true;
        this.maybeNotifyCredit();
      }
    },

    async maybeNotifyCredit() {
      try {
        if (!this.$store.state.user?.id) return;
        const now = Date.now();
        if (this.notifyNextAt && now < this.notifyNextAt) return;
        if (this.notifyIsChecking) return;
        this.notifyIsChecking = true;
        const res = await axios.post(
          "https://heligonkovaakademia.sk/api/helitime/notifyCredit.php",
          null,
          { withCredentials: true }
        );
        const ok =
          res?.data?.status === "Request succesfull" &&
          String(res?.data?.data) === "true";
        if (ok) {
          this.$store.commit("SET_GRATULATION", {
            show: true,
            message:
              "Nazbierali ste dostatok bodov! Svoje body si môžete minúť v časti Číselné zápisy po stlačení darčeka.",
          });
          const days = this.randomInt(3, 10);
          const nextAt = now + days * 24 * 60 * 60 * 1000;
          localStorage.setItem("ha.notifyCredit.nextAt", String(nextAt));
          this.notifyNextAt = nextAt;
        } else {
          this.notifyTriggeredThisSession = false;
          this.notifyActiveMs = 0;
          this.notifyMinActiveMs = this.randomInt(
            10 * 60 * 1000,
            15 * 60 * 1000
          );
        }
      } catch {
        this.notifyTriggeredThisSession = false;
      } finally {
        this.notifyIsChecking = false;
      }
    },
    showPrintWarning() {
      this.printWarningVisible = true;
    },
    hidePrintWarning() {
      this.printWarningVisible = false;
    },
    maybeShowPrintWarning() {
      const allowed = !!(
        this.$store &&
        this.$store.state &&
        this.$store.state.user &&
        (this.$store.state.user.edupage_in_calendar ||
          (this.$store.state.user.isAdmin &&
            this.$store.state.userRoles?.roles?.includes("subscription_edit")))
      );
      if (!allowed) this.showPrintWarning();
    },
    _onKeydownPrint(e) {
      const key = String(e.key).toLowerCase();
      const wantsPrint = (e.ctrlKey || e.metaKey) && key === "p";
      const isPrintScreen = String(e.key) === "PrintScreen";
      if (wantsPrint || isPrintScreen) {
        const allowed = !!(
          this.$store &&
          this.$store.state &&
          this.$store.state.user &&
          (this.$store.state.user.edupage_in_calendar ||
            (this.$store.state.user.isAdmin &&
              this.$store.state.userRoles?.roles?.includes(
                "subscription_edit"
              )))
        );
        if (!allowed) {
          e.preventDefault();
          this.showPrintWarning();
        }
      }
    },
    startAdminInterval() {
      if (!this.intervalAdmin) {
        this.intervalAdmin = setInterval(() => {
          this.overenieStavuRoly();
        }, 1000 * 60);
      }
    },
    startUserInterval() {
      if (!this.intervalUser) {
        this.intervalUser = setInterval(() => {
          this.overenieStavuRoly();
        }, 1000 * 60 * 5);
      }
    },
    stopAdminInterval() {
      if (this.intervalAdmin) {
        clearInterval(this.intervalAdmin);
        this.intervalAdmin = null;
      }
    },
    stopUserInterval() {
      if (this.intervalUser) {
        clearInterval(this.intervalUser);
        this.intervalUser = null;
      }
    },
    overenieStavuRoly() {
      const FormData = require("form-data");
      let data = new FormData();
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/user/auth/rolesLoad.php",
        data: data,
      };
      axios.request(config).catch(() => {});
    },
    handleResize() {
      const screenWidth = window.innerWidth;
      document.body.style.overflow = screenWidth > 750 ? "hidden" : "auto";
    },
    async pingOnlineOnce() {
      try {
        await apiFriend.get("/online.php");
      } catch {}
    },
    startGlobalOnlinePing() {
      this.stopGlobalOnlinePing();
      const tick = () => {
        if (document.visibilityState === "visible") this.pingOnlineOnce();
      };
      tick();
      this.globalOnlineTimer = setInterval(tick, 4 * 60 * 1000);
      const onVis = () => {
        if (document.visibilityState === "visible") tick();
      };
      document.addEventListener("visibilitychange", onVis);
      this._onVisHandler = onVis;
    },
    stopGlobalOnlinePing() {
      if (this.globalOnlineTimer) clearInterval(this.globalOnlineTimer);
      this.globalOnlineTimer = null;
      if (this._onVisHandler) {
        document.removeEventListener("visibilitychange", this._onVisHandler);
        this._onVisHandler = null;
      }
    },
  },
  mounted() {
    window.scrollTo(0, 0);

    const codeFromQuery = this.$route.query.pozyvaci_kod;
    if (codeFromQuery) {
      this.$store.commit("SET_INVITE_CODE", codeFromQuery);
    } else if (!this.$store.state.inviteCode) {
      fetch(this.$store.state.api + "/user/info/getInviteCode.php")
        .then((res) => res.json())
        .then((result) => {
          if (result.status === "Request succesfull" && result.data) {
            this.$store.commit("SET_INVITE_CODE", result.data);
          } else {
            this.$store.commit("SET_INVITE_CODE", "neznamy");
          }
        })
        .catch(() => {
          this.$store.commit("SET_INVITE_CODE", "neznamy");
        });
    }

    document.onreadystatechange = () => {
      if (document.readyState === "complete") {
        this.$store.state.isLoaded = true;
        this.isLoaded = true;
        window.haCanPrint = !!(
          this.$store.state.user?.edupage_in_calendar ||
          (this.$store.state.user?.isAdmin &&
            this.$store.state.userRoles?.roles?.includes("subscription_edit"))
        );
      }
    };

    this.$store.dispatch("overeniePrihlasenia");

    let noLogged = 0;
    const intervalId = setInterval(() => {
      if (this.$store.state.prihlasenyStav <= 1) {
        noLogged++;
        if (noLogged >= 5) clearInterval(intervalId);
      } else if (this.$store.state.prihlasenyStav < 5) {
        this.$store.state.prihlasenyStav = 0;
        this.$store.dispatch("overeniePrihlasenia");
      } else {
        clearInterval(intervalId);
      }
    }, 1000);

    window.addEventListener("resize", this.handleResize);
    this.handleResize();

    const isAppleDevice = /iPad|iPhone|iPod/.test(navigator.userAgent);
    const isMacOS = /Mac OS X/.test(navigator.userAgent);
    if (isAppleDevice) document.documentElement.classList.add("apple-device");
    else if (isMacOS) document.documentElement.classList.add("macos-device");
    else document.documentElement.classList.add("other-device");
    if (typeof window.safari !== "undefined")
      document.documentElement.classList.add("safari");

    this.initNotifyCredit();
    window.addEventListener("mousemove", this.markUserActive, {
      passive: true,
    });
    window.addEventListener("keydown", this.markUserActive);
    window.addEventListener("touchstart", this.markUserActive, {
      passive: true,
    });
    document.addEventListener("visibilitychange", this.onVisibilityChange);

    this.startGlobalOnlinePing();

    // Reakcia na tlač / screenshot
    window.addEventListener("beforeprint", this.maybeShowPrintWarning);
    window.addEventListener("afterprint", this.hidePrintWarning);
    window.addEventListener("keydown", this._onKeydownPrint);

    // Voliteľné helpery pre manuálne ovládanie
    window.haShowPrintWarning = () => this.showPrintWarning();
    window.haHidePrintWarning = () => this.hidePrintWarning();
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.handleResize);
    window.removeEventListener("mousemove", this.markUserActive);
    window.removeEventListener("keydown", this.markUserActive);
    window.removeEventListener("touchstart", this.markUserActive);
    document.removeEventListener("visibilitychange", this.onVisibilityChange);
    window.removeEventListener("beforeprint", this.maybeShowPrintWarning);
    window.removeEventListener("afterprint", this.hidePrintWarning);
    window.removeEventListener("keydown", this._onKeydownPrint);
    if (this.notifyTimer) clearInterval(this.notifyTimer);
    this.stopGlobalOnlinePing();
  },
  watch: {
    "$store.state.user.isAdmin"(val) {
      if (val) {
        this.startAdminInterval();
        this.stopUserInterval();
      } else {
        this.stopAdminInterval();
        this.startUserInterval();
      }
    },
    $route() {
      // zavri info panel globálne pri zmene trasy
      this.$store.commit("ui/CLOSE_INFO");
    },
    "$store.state.user"(u) {
      if (!u) {
        // zavri info panel globálne pri odhlásení
        this.$store.commit("ui/CLOSE_INFO");
      }
    },
  },
};
</script>

<style lang="scss">
@import url("https://fonts.googleapis.com/css2?family=Hammersmith+One&display=swap");
@font-face {
  font-family: Adumu;
  src: url(../src/assets/font/Adumu.ttf);
}
@font-face {
  font-family: Bahnshrift;
  src: url(../src/assets/font/BAHNSCHRIFT.TTF);
}
@font-face {
  font-family: Avenir;
  src: url(../src/assets/font/Avenir-Oblique.ttf);
}
@font-face {
  font-family: Calibri;
  src: url(../src/assets/font/calibri.ttf);
}

@import "@/assets/sass/_colors.scss";
@import "@/assets/sass/_defaultStyles.scss";
@import "@/assets/sass/globalStyles.scss";

.container {
  height: 100%;
  gap: 4%;
  display: flex;
  flex-direction: column;
  width: -webkit-fill-available;
  width: -moz-available;
}

@media only screen and (max-width: 1500px) {
  .container {
    gap: 3%;
  }
}
@media only screen and (max-width: 1300px) {
  .container {
    gap: 2%;
  }
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}

html {
  -ms-text-size-adjust: 100%;
  -webkit-text-size-adjust: 100%;
  -webkit-font-smoothing: antialiased;
}

.mobile {
  display: none;
}
.computer {
  display: block;
}

@media screen and (max-width: 750px) {
  .computer {
    display: none !important;
  }
  .mobile {
    display: block !important;
  }
  #app {
    padding: 1vh 0 7em;
    height: 100vh;
    gap: 0;
  }
}

.version {
  user-select: none;
  -webkit-user-select: none;
  -ms-user-select: none;
  z-index: 1000;
  position: absolute;
  bottom: 0.3em;
  font-size: 0.8em;
  right: 1em;
}

@media screen and (max-width: 750px) {
  .version {
    display: none;
  }
}

.admin {
  position: absolute;
  top: 2em;
  right: 2em;
}

#print-warning {
  position: fixed;
  inset: 0;
  background: rgba(30, 30, 30, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 999999;
  font-family: sans-serif;
  animation: fadeIn 0.5s ease-out;
}
#print-warning .warning-box {
  z-index: 9999999;
  background-color: rgba(0, 0, 0, 0.85);
  color: #fff;
  padding: 2rem 3rem;
  border-radius: 12px;
  max-width: 600px;
  text-align: center;
  box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
  font-size: 1.2rem;
  font-weight: bold;
}
#print-warning strong {
  font-size: 1.6rem;
  color: #ffd700;
  text-shadow: 1px 1px 2px black;
}
#print-warning p {
  font-size: 1.1rem;
  text-shadow: 1px 1px 1px black;
  color: white;
}

#global-watermark {
  position: fixed;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%) rotate(-30deg);
  opacity: 0.08;
  font-size: 4rem;
  color: #000;
  z-index: 99999;
  pointer-events: none;
  white-space: nowrap;
  user-select: none;
}

body.admin-unlocked,
body.admin-unlocked * {
  user-select: auto !important;
  -webkit-user-select: auto !important;
  -ms-user-select: auto !important;
}

.guide-fab {
  position: fixed;
  right: 16px;
  bottom: 16px;
  z-index: 4000;
  width: 46px;
  height: 46px;
  border-radius: 14px;
  border: 1px solid #e6ecf2;
  background: #fff;
  box-shadow: 0 10px 24px rgba(0, 0, 0, 0.12);
  font-weight: 900;
  font-size: 20px;
  cursor: pointer;
  transition: transform 0.18s ease, box-shadow 0.18s ease;
}
.guide-fab:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.16);
}
.fab-fade-enter-active,
.fab-fade-leave-active {
  transition: opacity 0.24s cubic-bezier(0.18, 0.72, 0.32, 1),
    transform 0.24s cubic-bezier(0.18, 0.72, 0.32, 1);
}
.fab-fade-enter-from,
.fab-fade-leave-to {
  opacity: 0;
  transform: translate3d(0, 12px, 0) scale(0.9);
}
.fab-fade-enter-to,
.fab-fade-leave-from {
  opacity: 1;
  transform: translate3d(0, 0, 0) scale(1);
}

@media print {
  /* nikdy netlač varovanie, nech je kontrola čisto cez JS */
  #print-warning {
    display: none !important;
  }

  img {
    visibility: visible !important;
  }
  img::before,
  img::after {
    content: none !important;
  }
}
</style>
