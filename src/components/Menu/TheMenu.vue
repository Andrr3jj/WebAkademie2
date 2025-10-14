<template>
  <section class="menu computer">
    <div class="logo">
      <router-link to="/">
        <img
          src="@/assets/images/logo.svg"
          alt="Heligónková Akadémia – škola hry na heligónku online"
        />
      </router-link>
    </div>

    <div class="navigation">
      <div class="top-nav">
        <ul>
          <li>
            <router-link to="/naucne-videa">
              <div class="icon">
                <img
                  src="@/assets/images/icons/naucneVidea.svg"
                  alt="Online náučné videá – hra na heligónku krok za krokom"
                />
              </div>
              Náučné videá
            </router-link>
          </li>

          <li>
            <router-link to="/ciselne-zapisy">
              <div class="icon">
                <img
                  src="@/assets/images/icons/ciselneZapisy.svg"
                  alt="Číselné zápisy a notový materiál pre heligónku"
                />
              </div>
              Číselné zápisy
            </router-link>
          </li>

          <li>
            <router-link to="/spevnik">
              <div class="icon">
                <img
                  src="@/assets/images/icons/textyPiesni.svg"
                  alt="Slovenské ľudové piesne – texty pre heligónku"
                />
              </div>
              Texty piesní
            </router-link>
          </li>

          <li>
            <router-link to="/helifest">
              <div class="icon">
                <img
                  src="@/assets/images/icons/helifest.svg"
                  alt="HeliFest – hudobný festival a stretnutie hráčov na heligónku"
                />
              </div>
              HeliFest
            </router-link>
          </li>

          <li>
            <router-link to="/helishop">
              <div class="icon">
                <img
                  src="@/assets/images/icons/merch.svg"
                  alt="HeliShop – mikiny a doplnky s motívom heligónky"
                />
              </div>
              HeliShop
            </router-link>
          </li>

          <li>
            <router-link to="/o-nas">
              <div class="icon">
                <img
                  src="@/assets/images/icons/oNas.svg"
                  alt="O nás – Učitelia a príbeh Heligónkovej Akadémie"
                />
              </div>
              O nás
            </router-link>
          </li>
        </ul>
      </div>

      <div class="bot-nav">
        <ul>
          <li>
            <router-link to="/kosik">
              <div class="icon">
                <img
                  src="@/assets/images/icons/kosik.svg"
                  alt="Košík – objednávka kurzov a produktov"
                />
              </div>
              Košík
            </router-link>

            <p
              v-if="Object.keys(this.$store.state.userCart).length != 0"
              class="cart-number"
            >
              {{ getTotalCartItem() }}
            </p>
          </li>

          <li>
            <router-link to="/pomoc">
              <div class="icon">
                <img
                  src="@/assets/images/icons/pomoc.svg"
                  alt="Pomoc a podpora – Heligónková Akadémia"
                />
              </div>
              Pomoc
            </router-link>
          </li>

          <!-- ZOZNAM PIESNÍ -->
          <router-link
            v-if="$store.state.user != null"
            to="/ucebna/moje-piesne"
            class="login button prihlaseny menu-link-with-badge"
            @click="markSongsSeen"
          >
            <img
              src="@/assets/images/icons/zoznam.svg"
              alt="Zoznam piesní na heligónku – vaše zakúpené číselné zápisy a noty"
            />
            <router-link to="/ucebna/moje-piesne">Zoznam piesní</router-link>

            <span v-if="hasNewSongsBadge" class="ha-new-badge">Nové</span>
          </router-link>

          <!-- ONLINE VÝUČBA -->
          <router-link
            v-if="$store.state.user != null"
            to="/ucebna/moje-kurzy"
            class="login button prihlaseny"
            :class="$store.state.user.isSubscribed ? 'green' : ''"
          >
            <img
              src="@/assets/images/icons/online.svg"
              alt="Online výučba heligónky – videolekcie a kurzy krok za krokom"
            />
            <router-link to="/ucebna/moje-kurzy">Online výučba</router-link>
          </router-link>
        </ul>
      </div>

      <router-link
        v-if="$store.state.user == null"
        to="/prihlasenie"
        class="login button"
      >
        <img
          @click="this.odhlasenie()"
          src="@/assets/images/icons/prihlasenie.svg"
          alt=""
        />
        <router-link to="/prihlasenie">Prihlásiť sa</router-link>
      </router-link>

      <div class="moj-ucet">
        <router-link
          v-if="$store.state.user != null"
          to="/ucebna"
          class="login no-padding button"
        >
          <img :src="profilePhoto" alt="Profilová fotka" class="profile-icon" />
          <p>Moja učebňa</p>
          <a class="logout">
            <img
              @click="this.odhlasenie()"
              src="@/assets/images/icons/odhlasenie.svg"
              alt="odhlasenie"
            />
          </a>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script>
import defaultAvatar from "@/assets/images/icons/prihlasenie.svg";

export default {
  data() {
    return {
      hasNewSongsBadge: false,
      unregAfterEach: null, // (bez podčiarkovníka) odregistrujeme router hook pri unmount
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
    userKey() {
      const u = this.$store.state.user;
      if (!u) return "guest";
      return (
        (u.id != null && String(u.id)) ||
        (u.email && String(u.email).toLowerCase()) ||
        "guest"
      );
    },
  },

  mounted() {
    this.safeEvaluateBadge();

    // auto-zhasnutie po vstupe do /ucebna/moje-piesne
    if (this.$router?.afterEach) {
      this.unregAfterEach = this.$router.afterEach((to) => {
        if (to?.path?.startsWith?.("/ucebna/moje-piesne")) {
          this.markSongsSeen();
        }
      });
    }
  },

  beforeUnmount() {
    if (this.unregAfterEach) {
      try {
        this.unregAfterEach();
      } catch (e) {
        /* no-op */
      }
    }
  },

  watch: {
    "$store.state.user"() {
      this.safeEvaluateBadge();
    },
    "$store.state.user.ownedNotes.length"() {
      this.safeEvaluateBadge();
    },
  },

  methods: {
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

    getTotalCartItem() {
      return this.$store.state.userCart.reduce(
        (sum, item) => sum + item.count,
        0
      );
    },

    // ———— logika „Nové“ ————
    getOwnedCount() {
      const list = this.$store.state.user?.ownedNotes || [];
      return Array.isArray(list) ? list.length : 0;
    },
    readLastSeen() {
      try {
        return Number(
          localStorage.getItem(`ha_songs_last_seen_${this.userKey}`) || "0"
        );
      } catch (e) {
        /* no-op */ return 0;
      }
    },
    writeLastSeen(count) {
      try {
        localStorage.setItem(
          `ha_songs_last_seen_${this.userKey}`,
          String(count)
        );
      } catch (e) {
        /* no-op */
      }
    },
    setAlert(on) {
      this.hasNewSongsBadge = !!on;
      try {
        localStorage.setItem(`ha_songs_alert_${this.userKey}`, on ? "1" : "0");
      } catch (e) {
        /* no-op */
      }
    },

    evaluateBadge() {
      if (!this.$store.state.user) {
        this.setAlert(false);
        return;
      }

      const current = this.getOwnedCount();
      const lastSeen = this.readLastSeen();

      if (!lastSeen && current >= 0) {
        this.writeLastSeen(current);
        this.setAlert(false);
        return;
      }
      this.setAlert(current > lastSeen);
    },

    safeEvaluateBadge() {
      this.evaluateBadge();

      if (this.$store.state.user && this.getOwnedCount() === 0) {
        setTimeout(this.evaluateBadge, 300);
        setTimeout(this.evaluateBadge, 900);
      }
    },

    // klik / vstup do „moje-piesne“
    markSongsSeen() {
      if (!this.$store.state.user) return;
      const current = this.getOwnedCount();
      this.writeLastSeen(current);
      this.setAlert(false);
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.button.login.no-padding {
  padding: 0;
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
  height: 100%;
  width: 27vw;
  max-width: 20em;
  min-width: 17em;
  position: relative;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
}

.logo {
  height: 30%;
}

.navigation {
  height: 100%;
  padding-bottom: 5%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: space-around;
}

.bot-nav {
  margin: auto auto 5%;
  width: 100%;
}
.top-nav {
  width: 100%;
}

.logo img {
  width: 83%;
  margin: 1vw auto auto;
  filter: drop-shadow(3px 3px 3px rgba(0, 0, 0, 0.3));
}

ul {
  display: flex;
  flex-direction: column;
  align-items: center;
  a.login {
    font-size: 120%;
  }
}

.cart-number {
  background: #fef35a;
  position: absolute;
  border-radius: 20rem;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 0.55em;
  box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.35);
  top: -0.3em;
  left: 1.15em;
  padding: 0.2em 0.5em 0.1em;
}

.navigation > div.bot-nav > ul > a:nth-child(4) {
  margin-top: 0.4em;
}

.navigation li {
  padding: 1.5% 2.5% 1.5% 5%;
  margin: 1% auto;
  display: flex;
  align-items: center;
  gap: 6%;
  position: relative;
  font-size: 1.5625em;
  font-style: normal;
  font-weight: 700;
  letter-spacing: 0.03em;
  width: 8.5em;

  &:hover {
    background: lighten($lt-gr-clr, 20%);
    transition-duration: 0.2s;
    border-radius: 0.625em;

    img {
      filter: brightness(0);
    }
  }

  img {
    width: 5em;
  }
  a {
    color: #000;
  }

  & > a {
    display: flex;
    gap: 6%;
    justify-content: flex-start;
    align-items: center;
  }
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
  padding: 0.4em 0.8em;
  img {
    width: 1.4em;
  }
}

.prihlaseny {
  padding: 0.4em 1.5em;
}

.login.no-padding {
  img {
    width: 1em;
    padding: 0.6em 0 0.6em 0.8em;
  }
  p {
    padding: 0.6em 0.4em 0.6em 0;
  }
}

li:has(.router-link-active.router-link-exact-active) {
  background: $lt-gr-clr;
  border-radius: 0.625em;
  img {
    filter: brightness(0);
  }
}

section {
  padding: 0;
}

.moj-ucet .login .logout {
  height: 100%;
  fill: #fef35a;
  background: url("@/assets/images/icons/elipsaOdhlasenie.svg");
  padding-right: 0.6em;
  background-position: center;
  background-size: cover;
  margin-bottom: 0 !important;
  transition-duration: 0.2s;
}
.moj-ucet .login .logout:hover {
  transform: scale(1.1);
}
.moj-ucet .login .logout img {
  width: 0.95em;
  margin-right: 0;
}
.moj-ucet p {
  font-size: 0.8em;
}

@media only screen and (max-width: 1500px) {
  .moj-ucet {
    font-size: 90%;
  }
}
@media only screen and (max-height: 700px) {
  .logo img {
    width: 70%;
  }
}

/* ——— vizuál „Nové“ prilepený k žltému buttonu ——— */
.menu-link-with-badge {
  position: relative; /* referenčný box pre badge */
}
.ha-new-badge {
  font-family: "Bahnschrift", sans-serif;
  position: absolute;
  top: -1em;
  right: -0.1em;
  padding: 0.18em 0.65em 0.12em;
  border-radius: 1em;
  background: #ff9900; /* oranžové pozadie */
  color: #ffffff; /* biely text */
  font-weight: 700;
  font-size: 0.9em;
  letter-spacing: 0.02em;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
  outline: 2px solid rgba(255, 255, 255, 0.6);
  outline-offset: -2px;
}
</style>
