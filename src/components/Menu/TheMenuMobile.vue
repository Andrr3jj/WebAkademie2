<template>
  <!-- rozbalené menu (dropdown) -->
  <div v-if="menuOpenValue" class="navigation-more">
    <ul>
      <li
        @click="menuOpenValue = false"
        data-tour-id="home-menu-mobile-lessons"
      >
        <div class="icon">
          <img src="@/assets/images/icons/naucneVidea.svg" alt="" />
        </div>
        <router-link to="/naucne-videa">Náučné videá</router-link>
      </li>

      <li @click="menuOpenValue = false" data-tour-id="home-menu-mobile-texty">
        <div class="icon">
          <img src="@/assets/images/icons/textyPiesni.svg" alt="" />
        </div>
        <router-link to="/spevnik">Texty piesní</router-link>
      </li>

      <li
        @click="menuOpenValue = false"
        data-tour-id="home-menu-mobile-helifest"
      >
        <div class="icon">
          <img src="@/assets/images/icons/helifest.svg" alt="" />
        </div>
        <router-link to="/helifest">HeliFest</router-link>
      </li>

      <li @click="menuOpenValue = false" data-tour-id="home-menu-mobile-shop">
        <div class="icon">
          <img src="@/assets/images/icons/merch.svg" alt="" />
        </div>
        <router-link to="/helishop">HeliShop</router-link>
      </li>

      <li @click="menuOpenValue = false" data-tour-id="home-menu-mobile-about">
        <div class="icon">
          <img src="@/assets/images/icons/oNas.svg" alt="" />
        </div>
        <router-link to="/onas">O nás</router-link>
      </li>

      <li @click="menuOpenValue = false" data-tour-id="home-menu-mobile-help">
        <div class="icon">
          <img src="@/assets/images/icons/pomoc.svg" alt="" />
        </div>
        <router-link to="/pomoc">Pomoc</router-link>
      </li>

      <!-- ZOZNAM PIESNÍ v dropdown-e + odznak Nové -->
      <router-link
        v-if="$store.state.user != null"
        @click="
          menuOpenValue = false;
          markSongsSeen();
        "
        to="/ucebna/moje-piesne"
        class="button songs-btn"
      >
        <img src="@/assets/images/icons/zoznam.svg" alt="" />
        <router-link to="/ucebna/moje-piesne">Zoznam piesní</router-link>

        <!-- oranžový odznak NOVÉ prichytený k žltému buttonu -->
        <span v-if="songsAlert" class="badge-new badge-dropdown">Nové</span>
      </router-link>

      <router-link
        v-if="$store.state.user != null"
        @click="menuOpenValue = false"
        to="/ucebna/moje-kurzy"
        class="button"
        :class="$store.state.user.isSubscribed ? 'green' : ''"
      >
        <img src="@/assets/images/icons/online.svg" alt="" />
        <router-link to="/ucebna/moje-kurzy">Online výučba</router-link>
      </router-link>
    </ul>
  </div>

  <!-- spodná lišta -->
  <section class="menu mobile">
    <div class="navigation">
      <div class="top-nav">
        <ul>
          <!-- Košík -->
          <li
            @click="menuOpenValue = false"
            class="cart-li"
            data-tour-id="home-menu-mobile-cart"
          >
            <router-link to="/kosik">
              <div class="icon">
                <img src="@/assets/images/icons/kosik.svg" alt="Košík" />
              </div>
            </router-link>
            <p v-if="cartCount > 0" class="cart-number">{{ cartCount }}</p>
          </li>

          <!-- Admin alebo Číselné zápisy -->
          <li
            @click="menuOpenValue = false"
            v-if="$store.state.user?.isAdmin"
            data-tour-id="home-menu-mobile-admin"
          >
            <router-link to="/admin">
              <div class="icon">
                <img
                  src="@/assets/images/icons/asSystem.svg"
                  alt="Admin systém"
                />
              </div>
            </router-link>
          </li>
          <li
            @click="menuOpenValue = false"
            v-else
            data-tour-id="home-menu-mobile-scores"
          >
            <router-link to="/ciselne-zapisy">
              <div class="icon">
                <img
                  src="@/assets/images/icons/ciselneZapisy.svg"
                  alt="Číselné zápisy"
                />
              </div>
            </router-link>
          </li>

          <!-- Menu toggle -->
          <li data-tour-id="home-menu-mobile-toggle">
            <div @click="menuOpen" v-show="menuOpenValue" class="icon">
              <img
                src="@/assets/images/icons/menuOpen.svg"
                alt="Menu otvorené"
              />
            </div>
            <div @click="menuOpen" v-show="!menuOpenValue" class="icon">
              <img
                src="@/assets/images/icons/menuClosed.svg"
                alt="Menu zatvorené"
              />
            </div>
          </li>

          <!-- Moje piesne / Spevník v spodnej lište -->
          <li @click="menuOpenValue = false" class="songs-li">
            <router-link v-if="$store.state.user == null" to="/spevnik">
              <div class="icon">
                <img
                  src="@/assets/images/icons/textyPiesni.svg"
                  alt="Spevník"
                />
              </div>
            </router-link>

            <router-link v-else to="/ucebna/moje-piesne" @click="markSongsSeen">
              <div class="icon songs-icon">
                <img
                  src="@/assets/images/icons/zoznamGreen.svg"
                  alt="Moje piesne"
                />
                <!-- odznak NOVÉ pod ikonou v lište (ako na fotke) -->
                <span v-if="songsAlert" class="badge-new badge-under"
                  >Nové</span
                >
              </div>
            </router-link>
          </li>

          <!-- Domov -->
          <li @click="menuOpenValue = false">
            <router-link to="/">
              <div class="icon">
                <img src="@/assets/images/icons/domov.svg" alt="Domov" />
              </div>
            </router-link>
          </li>
        </ul>
      </div>

      <!-- tlačidlo prihlásiť / môj účet -->
      <div
        @click="menuOpenValue = false"
        v-if="$store.state.user == null"
        class="login button"
      >
        <img src="@/assets/images/icons/prihlasenie.svg" alt="" />
        <router-link to="/prihlasenie">Prihlásiť sa</router-link>
      </div>

      <router-link
        v-else
        @click="menuOpenValue = false"
        to="/ucebna"
        class="login no-padding button"
      >
        <img :src="profilePhoto" alt="Profilová fotka" class="profile-icon" />
        <p>Môj účet</p>
        <a class="logout">
          <img
            @click="odhlasenie"
            src="@/assets/images/icons/odhlasenie.svg"
            alt="odhlasenie"
          />
        </a>
      </router-link>
    </div>
  </section>
</template>

<script>
import defaultAvatar from "@/assets/images/icons/prihlasenie.svg";

export default {
  name: "TheMenuMobile",

  data() {
    return {
      menuOpenValue: false,

      // Lokálny stav, či je aktívny alert z localStorage
      alertFromLS: false,

      // timer na pravidelné načítanie storage (ak sa zmení v inej záložke)
      songsTimer: null,
    };
  },

  computed: {
    profilePhotoRaw() {
      return this.$store.state.user?.profilePhotoUrl || "";
    },
    profilePhoto() {
      if (!this.profilePhotoRaw) return defaultAvatar;
      return this.stripWWW(this.profilePhotoRaw);
    },

    cartCount() {
      const cart = this.$store.state.userCart;
      if (!Array.isArray(cart)) return 0;
      return cart.reduce((sum, it) => sum + (Number(it.count) || 0), 0);
    },

    // unikátny kľúč pre storage podľa prihláseného používateľa
    userKey() {
      const u = this.$store.state.user;
      if (!u) return "guest";
      return (
        (u.id != null && String(u.id)) ||
        (u.email && String(u.email).toLowerCase()) ||
        "guest"
      );
    },

    // hlavnú vlajku čítame z Vuexu (ak existuje) alebo z localStorage
    songsAlert() {
      const vuexFlag =
        this.$store.getters?.["ui/songListAlert"] ??
        this.$store.state?.ui?.songListAlert;
      return Boolean(vuexFlag) || this.alertFromLS;
    },
  },

  mounted() {
    // inicializuj alert zo storage
    this.readAlert();

    // priebežný refresh (kvôli zmenám z iných tabov či po asynchrónnom nabehnutí ownedNotes)
    this.songsTimer = setInterval(this.readAlert, 800);

    // keď sa storage zmení v inej záložke okna
    window.addEventListener("storage", this.readAlert);
  },

  beforeUnmount() {
    clearInterval(this.songsTimer);
    window.removeEventListener("storage", this.readAlert);
  },

  methods: {
    menuOpen() {
      this.menuOpenValue = !this.menuOpenValue;
    },

    stripWWW(url) {
      if (!url) return url;
      try {
        const u = new URL(url);
        if (u.hostname.startsWith("www.")) u.hostname = u.hostname.slice(4);
        return u.toString();
      } catch (e) {
        return url.replace(/^https?:\/\/www\./, (m) => m.replace("www.", ""));
      }
    },

    odhlasenie() {
      this.$store.dispatch("odhlasenie");
    },

    /* ===================== NOVÉ: alert „Nové“ ===================== */

    // načítaj stav z localStorage
    readAlert() {
      try {
        const v = localStorage.getItem(`ha_songs_alert_${this.userKey}`);
        this.alertFromLS = v === "1";
      } catch (e) {
        // storage nemusí byť dostupný (Safari Private mode ap.)
        this.alertFromLS = false;
        void 0;
      }
    },

    // zapíš do localStorage
    setAlert(on) {
      try {
        localStorage.setItem(`ha_songs_alert_${this.userKey}`, on ? "1" : "0");
        this.alertFromLS = !!on;
      } catch (e) {
        void 0;
      }
    },

    // po kliknutí na „Zoznam piesní“: zhasni alert a nastav baseline na aktuálny počet
    markSongsSeen() {
      this.setAlert(false);
      try {
        const count = Array.isArray(this.$store.state.user?.ownedNotes)
          ? this.$store.state.user.ownedNotes.length
          : 0;
        localStorage.setItem(
          `ha_songs_last_seen_${this.userKey}`,
          String(count)
        );
      } catch (e) {
        void 0;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

/* ——— pôvodné štýly (ponechané) ——— */
.button.login.no-padding {
  padding: 0;
  width: auto;
}
.profile-icon {
  border-top-left-radius: 50% !important;
  border-top-right-radius: 50% !important;
  border-bottom-left-radius: 50% !important;
  border-bottom-right-radius: 50% !important;
  width: 1.5em !important;
  height: 1.5em !important;
  padding: 0.5em !important;
  margin-right: 0 !important;
  object-fit: cover;
  filter: drop-shadow(3px 3px 3px rgba(0, 0, 0, 0.3));
}
.menu {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100vw;
  height: 5em;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}
.top-nav {
  width: 100%;
  ul {
    width: 100%;
    display: flex;
    justify-content: space-around;
    font-size: 70%;
    li {
      margin: 0.6em auto;
      padding: 0.3em 5%;
      display: flex;
      align-items: flex-end;
      justify-content: center;
      font-size: 1.5625em;
      font-style: normal;
      letter-spacing: 0.03em;
      cursor: pointer;
      width: auto;
      filter: drop-shadow(1px 1px 1px rgba(0, 0, 0, 0.1));
      a {
        display: flex;
        flex-direction: column;
        align-items: center;
        font-weight: 400;
        font-family: Adumu;
        font-size: 0.9em;
        letter-spacing: 0.07em;
      }
      img {
        width: auto;
        height: 6vw;
        margin-top: 1.5vw;
      }
    }
  }
}
.bot-nav {
  margin: auto auto 5%;
}
.navigation-more,
.menu {
  z-index: 10;
}
.logo img {
  width: 75%;
  margin: 5% auto auto 12.5%;
}
.icon {
  width: 10%;
  display: flex;
  align-items: center;
  justify-content: center;
  img {
    height: 100%;
    width: 1em;
  }
}
.login {
  font-size: clamp(1em, 4.5vw, 1.5em);
  padding: 0.6em 1em;
  position: fixed;
  bottom: 3.45rem;
  left: 50%;
  transform: translateX(-50%);
  width: 8.5em;
  img {
    width: 1em;
  }
  &:hover {
    transform: translateX(-50%) scale(1.1);
  }
  &:has(a.router-link-active.router-link-exact-active) {
    display: none;
  }
}
.login.no-padding img {
  width: 1em;
  padding: 0.8em 0.3em 0.8em 0.8em;
}
.login.no-padding p {
  padding: 0.4em 0.4em 0.4em 0;
}
li:has(.router-link-active.router-link-exact-active) {
  background: $lt-gr-clr;
  border-radius: 0.625em;
  padding-bottom: 0.6em;
  img {
    filter: brightness(0);
  }
}
section {
  padding: 0;
}
.top-nav > ul > li:nth-child(2) > div > img {
  height: 1.3em;
  margin-top: auto;
  margin-bottom: 0.4em;
}

/* menu dropdown */
.navigation-more {
  display: flex;
  justify-content: center;
  align-items: center;
  position: fixed;
  bottom: 7em;
  width: 90%;
  height: auto;
  background: #ededed;
  outline: 0.2em solid #fef35a;
  filter: drop-shadow(5px 5px 30px rgba(0, 0, 0, 0.5));
  border-radius: 1em;
  box-sizing: border-box;
  padding: 1.5em;
  li {
    padding: 0.3em 0.8em;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    color: #000;
    font-size: 1.05rem;
    font-style: normal;
    letter-spacing: 0.03em;
    width: auto;
    gap: 5%;
    min-width: 10em;
    a {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.3em;
      font-weight: 700;
    }
    img {
      width: auto;
      height: 1em;
    }
  }
  ul .button {
    padding: 0.4em 1.2em;
    margin: 0.7em auto;
    font-size: 100%;
    width: 75%;
    justify-content: center;
    box-shadow: 2.5px 2.5px 7.5px rgba(0, 0, 0, 0.25);
  }
}

.login .logout {
  height: 100%;
  fill: #fef35a;
  filter: drop-shadow(-4px 0 5px rgba(0, 0, 0, 0.15));
  background: url("@/assets/images/icons/elipsaOdhlasenie.svg");
  padding-right: 0.6em;
  background-position: center;
  background-size: cover;
  margin-bottom: 0 !important;
  transition-duration: 0.2s;
}
.logout {
  display: flex;
  align-items: center;
  height: 2.4em;
  justify-content: center;
}
.login .logout:hover {
  transform: scale(1.1);
}
.login .logout img {
  width: 0.95em;
  margin-right: 0;
}

/* badge pre košík (pôvodné) */
.top-nav ul .cart-li {
  position: relative;
}
.top-nav ul .cart-li .cart-number {
  position: absolute;
  top: 0.5em;
  right: 45%;
  transform: translate(50%, -35%);
  background: #fef35a;
  color: #000;
  border-radius: 100vmax;
  padding: 0.1em 0.5em;
  font-size: clamp(10px, 2.8vw, 15px);
  line-height: 1.4;
  box-shadow: 0 2px 2px rgba(0, 0, 0, 0.35);
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* ——— ORANŽOVÝ ODZNAK „Nové“ (vizuál ako na desktope) ——— */
.badge-new {
  font-family: "Bahnschrift", sans-serif;
  background: #ff9900;
  color: #ffffff;
  font-weight: 700;
  border-radius: 999px;
  padding: 0.1em 0.7em;
  font-size: clamp(9px, 2vw, 14px);
  letter-spacing: 0.02em;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
  outline: 2px solid rgba(255, 255, 255, 0.6);
  white-space: nowrap;
}

/* pod ikonou v spodnej lište (ako na tvojej fotke) */
.songs-li .songs-icon {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.badge-under {
  position: absolute;
  bottom: -0.4em; /* tesne pod ikonou */
}

/* v rozbalenom dropdown-e, prichyť vpravo hore žltého tlačidla */
.songs-btn {
  position: relative;
}
.badge-dropdown {
  position: absolute;
  top: -0.7em;
  right: 1.6em;
}
</style>
