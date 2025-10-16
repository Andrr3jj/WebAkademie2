<template>
  <section class="computer" :id="$route.name">
    <div class="left">
      <TheHeadline />
      <div class="buttons-group">
        <router-link to="/zistit-viac" class="login button sm-br">
          <router-link
            to="/zistit-viac"
            alt="Zistiť viac o Heligónkovej Akadémii a našich kurzoch heligónky"
            >Zistiť viac</router-link
          >
        </router-link>
        <router-link to="/zistit-viac" class="login button sm-br">
          <router-link
            to="/ucebna/zadarmo-video"
            alt="Vyskúšajte zdarma ukážkové náučné video na heligónku – krok po kroku"
            >Vyskúšať zadarmo</router-link
          >
        </router-link>
      </div>
    </div>
    <div class="right">
      <img
        src="@/assets/images/gallery/JurajAAndrejVelkeHlavy.png"
        alt="Učitelia Heligónkovej Akadémie s heligónkami – online kurzy a náučné videá"
      />
      <button
        v-if="isGuideButtonVisible"
        type="button"
        class="login button sm-br guide-tour-button"
        @click="startHomeTour"
        aria-label="Spustiť krátky návod"
      >
        <span aria-hidden="true" class="guide-tour-button__icon">?</span>
      </button>
    </div>
  </section>

  <section :id="$route.name" class="second computer">
    <routerLink to="naucne-videa" class="box-main">
      <img
        src="@/assets/images/gallery/NaucneVideaTitle.png"
        alt="Náučné videá na heligónku – výučba krok za krokom"
      />
      <p>Náučné videá</p>
    </routerLink>
    <routerLink to="ciselne-zapisy" class="box-main">
      <img
        src="@/assets/images/gallery/CiselneZapisyTitle.png"
        alt="Číselné zápisy a noty pre heligónku – jednoduché návody na hranie"
      />
      <p>Číselné zápisy</p>
    </routerLink>
    <routerLink to="helishop" class="box-main">
      <img
        src="@/assets/images/gallery/MerchTitle.png"
        alt="HeliShop – oblečenie a doplnky pre heligónku"
      />
      <p>HeliShop</p>
    </routerLink>
    <routerLink to="helifest" class="box-main">
      <img
        src="@/assets/images/gallery/HelifestTitle.png"
        alt="HeliFest – festival a podujatia pre hráčov na heligónku"
      />
      <p>HeliFest</p>
    </routerLink>
  </section>

  <div class="mobile" :id="$route.name">
    <div class="scroll">
      <section>
        <TheHeadline />
        <div class="left">
          <router-link to="/zistit-viac" class="login button sm-br">
            <router-link to="/zistit-viac">Zistiť viac</router-link>
          </router-link>
          <router-link to="/zistit-viac" class="login button sm-br">
            <router-link
              to="/ucebna/zadarmo-video"
              alt="Vyskúšajte zdarma ukážkové náučkové video na heligónku – krok po kroku"
              >Vyskúšať zadarmo</router-link
            >
          </router-link>
        </div>
        <div class="right">
          <img
            src="@/assets/images/gallery/JurajAAndrejVelkeHlavy.png"
            alt="Heligonkáry Juraj a Andrej zo zväčšenými hlavami"
          />
          <button
            v-if="isGuideButtonVisible"
            type="button"
            class="login button sm-br guide-tour-button"
            @click="startHomeTour"
            aria-label="Spustiť krátky návod"
          >
            <span aria-hidden="true" class="guide-tour-button__icon">?</span>
          </button>
        </div>
      </section>

      <section :id="$route.name" class="second mobile">
        <routerLink to="naucne-videa" class="box-main">
          <img src="@/assets/images/gallery/NaucneVideaTitle.png" alt="" />
          <p>Náučné videá</p>
        </routerLink>
        <routerLink to="ciselne-zapisy" class="box-main">
          <img src="@/assets/images/gallery/CiselneZapisyTitle.png" alt="" />
          <p>Číselné zápisy</p>
        </routerLink>
        <routerLink to="helishop" class="box-main">
          <img src="@/assets/images/gallery/MerchTitle.png" alt="" />
          <p>HeliShop</p>
        </routerLink>
      </section>

      <section :id="$route.name" class="mobile-newslist">
        <div class="news">
          <p class="news-intro">
            Pravidelne pridávame nové náučné videá, číselné zápisy a ďalšie
            zaujímavosti. Sledujte nás, aby vám nič neuniklo.
          </p>
        </div>
        <NewsList />
      </section>

      <!-- BOOK blok (len mobil) -->
      <section :id="$route.name" class="mobile-frstbook">
        <div class="book-card" ref="bookSection">
          <!-- riadok: obálka vľavo + text vpravo -->
          <div class="book-row">
            <img
              class="book-cover"
              src="@/assets/images/gallery/KnihaJedna.png"
              alt="Kniha Nauč sa hrať na heligónke jednoducho, rýchlo a efektívne – učebnica s číselnými zápismi a tipmi ako hrať na heligónke"
            />
            <div class="book-text">
              <h2 class="book-title">
                Naša prvá kniha: Nauč sa hrať na heligónke jednoducho, rýchlo a
                efektívne
              </h2>
              <p class="book-lead">
                Objavte jedinečnú učebnicu, ktorá vás prevedie od prvých tónov
                až po obľúbené piesne doma aj na cestách.
              </p>
            </div>
          </div>

          <!-- rozbalovací text (animovaný) -->
          <transition name="fade-slide">
            <div v-if="showMoreBook" class="book-more-content">
              <p>
                Po rokoch zbierania skúseností a vďaka obrovskému záujmu sme sa
                rozhodli pripraviť prvý diel náučnej knihy Heligónková akadémia.
                Naším cieľom bolo vytvoriť sprievodcu, ktorý vás naučí hrať na
                heligónke od úplných základov až po pokročilé skladby –
                jednoducho, zrozumiteľne a zábavne.
              </p>

              <div class="book-points-wrapper">
                <p class="points-title"><strong>V knihe nájdete:</strong></p>
                <ul class="book-points">
                  <li>Prehľadné číselné zápisy k obľúbeným piesňam</li>
                  <li>Tipy, triky a postupy od skúsených učiteľov</li>
                  <li>Metodiku krok za krokom, od basov po melódiu</li>
                  <li>
                    Cvičenia s náučnými videami, ktoré nájdete na našej stránke
                  </li>
                </ul>
              </div>

              <p>
                Každý výtlačok je jedinečný – obsahuje svoj unikátny kód. Po
                jeho zadaní na našej stránke sa vám všetky piesne z knihy
                pridajú do vášho osobného online zoznamu piesní. Tak budete mať
                svoje zápisy vždy k dispozícii, aj bez knihy po ruke – či už ste
                doma, na ceste, alebo na vystúpení.
              </p>

              <p>
                Pri tvorbe sme dbali na každý detail – od grafického spracovania
                až po logickú postupnosť lekcií – aby bolo učenie rýchle,
                zábavné a motivujúce. Veríme, že práve vďaka tejto knihe
                objavíte nielen krásu heligónky, ale aj radosť z hrania.
              </p>
            </div>
          </transition>

          <!-- CTA pod rozbaleným textom -->
          <div class="book-ctas">
            <button
              type="button"
              class="button book-more"
              @click="toggleBook"
              :aria-expanded="showMoreBook"
            >
              {{ showMoreBook ? "Zobraziť menej" : "Zobraziť viac" }}
            </button>
            <router-link to="/helishop" class="button book-buy">
              Zakúpiť knihu
            </router-link>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, nextTick, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useStore } from "vuex";

import TheHeadline from "@/components/Menu/TheHeadline.vue";
import NewsList from "@/components/ucebna/NewsList.vue";
import { tour } from "@/components/Navigacia/tour/tour";

export default {
  name: "DomovStranka",
  components: { TheHeadline, NewsList },

  metaInfo() {
    return {
      title: "Moderný spôsob učenia sa hry na heligónku 🎶",
      meta: [
        {
          name: "description",
          content:
            "Online kurzy, číselné zápisy a výučbové materiály pre začiatočníkov aj pokročilých.",
        },
        { property: "og:url", content: "https://heligonkovaakademia.sk" },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Moderný spôsob učenia sa hry na heligónku 🎶",
        },
        {
          property: "og:description",
          content:
            "Online kurzy, číselné zápisy a výučbové materiály pre začiatočníkov aj pokročilých.",
        },
        {
          property: "og:image",
          content:
            "https://opengraph.b-cdn.net/production/images/d86c7af9-25eb-492e-b01e-7aec94925f79.png?token=Tn2HJOo9yWewlnuQB6Vhpmm6EQI5Hg_kNyN3XNtizyw&height=630&width=1199&expires=33277902256",
        },
        { name: "twitter:card", content: "summary_large_image" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        { property: "twitter:url", content: "https://heligonkovaakademia.sk" },
        {
          name: "twitter:title",
          content: "Moderný spôsob učenia sa hry na heligónku 🎶",
        },
        {
          name: "twitter:description",
          content:
            "Online kurzy, číselné zápisy a výučbové materiály pre začiatočníkov aj pokročilých.",
        },
        {
          name: "twitter:image",
          content:
            "https://opengraph.b-cdn.net/production/images/d86c7af9-25eb-492e-b01e-7aec94925f79.png?token=Tn2HJOo9yWewlnuQB6Vhpmm6EQI5Hg_kNyN3XNtizyw&height=630&width=1199&expires=33277902256",
        },
      ],
    };
  },

  setup() {
    const route = useRoute();
    const router = useRouter();
    const store = useStore();

    const showMoreBook = ref(false);
    const bookSection = ref(null);

    const toggleBook = async () => {
      const opening = !showMoreBook.value;
      showMoreBook.value = opening;

      if (opening) {
        await nextTick();
        setTimeout(() => {
          if (bookSection.value) {
            bookSection.value.scrollIntoView({
              behavior: "smooth",
              block: "end",
            });
          }
        }, 50);
      }
    };

    onMounted(() => {
      window.scrollTo(0, 0);
      checkId();
    });

    const startHomeTour = async () => {
      const doStart = async () => {
        await tour.start({
          mode: "full",
          startIndex: 0,
        });

        if (window.haTour?.start) {
          window.haTour.start(0);
        } else {
          try {
            window.dispatchEvent(
              new CustomEvent("ha.tour.start", { detail: { index: 0 } })
            );
          } catch (e) {
            console.error("Failed to dispatch tour start event", e);
          }
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
    };

    const isGuideButtonVisible = computed(() => !tour.state.open);

    function checkId() {
      const id = route.query.id;
      if (!id) return;

      setTimeout(() => {
        verifyId(id, "/subscription/paymentStatusSubscription.php/?id=");
      }, 500);

      setTimeout(() => {
        verifyId(id, "/cart/paymentStatus.php/?id=");
      }, 300);
    }

    function verifyId(id, path) {
      const axios = require("axios");
      axios
        .get(store.state.api + path + id)
        .then((response) => {
          const data = response.data?.data;
          if (!data) return;

          if (data.includes("PAID")) {
            router.push("/uspesny-nakup");
            store.dispatch("pouzivatelVlastneneProdukty");
            store.dispatch("pouzivatelAktivnePredplatne");
          } else if (!data.includes("gopay_id not found")) {
            router.push("/neuspesny-nakup");
          }
        })
        .catch((err) => console.error(err));
    }

    return {
      showMoreBook,
      toggleBook,
      bookSection,
      startHomeTour,
      isGuideButtonVisible,
    };
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

/* existujúci štýl – nemením štruktúru */
.right img {
  position: absolute;
  bottom: 0;
  right: -3vw;
  width: auto;
  height: 36vw;
  max-height: 35em;
  filter: drop-shadow(10px 2px 4px rgba(0, 0, 0, 0.5));
}

h5 {
  margin: 2em 2em !important;
}

:deep(h5.no-default) {
  font-size: 1.6vw;
  margin: 0.8em 2em 0.6em !important;
  font-weight: 550;
}

.right {
  position: relative;
  width: 40%;
}
.left {
  width: 90%;
}

.guide-tour-button {
  position: absolute;
  right: 1.5rem;
  bottom: 1.5rem;
  justify-content: center;
  padding: 0.35em 1.25em;
  font-size: 1.35em;
  line-height: 1;
  z-index: 2;
}

.guide-tour-button__icon {
  display: block;
  font-family: Adumu;
  font-size: 1em;
  letter-spacing: 0;
  line-height: 1;
}

section {
  display: flex;
  flex-direction: row;
  align-items: stretch;
  height: auto;
  padding: 0;
  z-index: 5;
}

.box-main {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  gap: 4%;
  transition-duration: 0.3s;

  img {
    width: 14em;
    height: 14em;
    object-fit: contain;
    z-index: 1;
    filter: drop-shadow(4px 4px 4px rgba(0, 0, 0, 0.25));
  }
  &:not(.not-this):nth-child(2) > img {
    transform: scale(1.1) translate(0.9em, -0.5em);
  }
  &:hover {
    transform: scale(1.15);
    transition-duration: 0.1s;
  }
}

.second {
  justify-content: space-around;
  height: 6em;
  margin-top: auto;
  padding: 0.5em;
  p {
    color: #000;
    text-align: center;
    font-size: 1.65em;
    font-style: normal;
    font-weight: 600;
    line-height: 130%;
    letter-spacing: 0.04em;
  }
}

.button {
  margin-bottom: 2%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.buttons-group {
  font-size: 0.7vw;
  text-align: center;
  display: flex;
  justify-content: center;
  gap: 2em;
  margin: 1.5em 0 2em;
  width: auto;
}

.botton {
  padding: 0.5em 1.1em 0.5em;
  border-radius: 0.8625em;
}

@media screen and (max-width: 1600px) {
  .left {
    width: 70%;
  }
}
@media screen and (max-width: 1030px), (max-height: 730px) {
  body {
    font-size: 70%;
  }
}
@media screen and (max-width: 920px), (max-height: 640px) {
  body {
    font-size: 60%;
  }
}
@media screen and (max-width: 1000px) {
  .box-item:first-child {
    margin-bottom: 2em;
  }
}

@media screen and (max-width: 768px) {
  .right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
  }

  .guide-tour-button {
    position: static;
    margin-top: 1.25rem;
    align-self: flex-end;
  }
}

@media screen and (max-width: 950px) {
  .second {
    font-size: 70%;
  }
  .buttons-group {
    display: flex;
    flex-direction: column;
    gap: 1em;
    font-size: 0.7em;
    margin: 2.5em auto;
  }
  :deep(h5.no-default) {
    font-size: 2.2vw;
    margin: 1em 1em !important;
  }
  :deep(h1.no-default) {
    font-size: 6.5vw;
    padding: 0.5em 0 0;
  }
}

.mobile {
  display: none;
}
.computer {
  display: flex;
}

/* === JEDEN zjednotený blok pre max-width: 750px === */
@media screen and (max-width: 750px) {
  .mobile {
    display: flex !important;
  }
  .computer {
    display: none;
  }

  .left {
    width: 60%;
    padding: 2% 2%;
    margin-bottom: 2em;

    p {
      color: #575757;
      text-align: left;
      font-style: normal;
      font-size: 4vw;
      font-weight: 600;
      line-height: 130%;
      width: 70%;
      margin: 1.5em 2.5; /* pôvodné tvoje */
    }
  }

  /* rozloženie sekcií tak ako doteraz */
  section:not(.mobile) {
    flex-direction: column;
  }

  section.mobile {
    font-size: 1.8vw; /* z 450-verzie */
    margin: 8em auto 0; /* z 450-verzie */
    a {
      width: 50%;
    }
  }

  /* obrázok vpravo – nech sa správa ako v 450-verzii */
  .right {
    width: 99%;
  }
  .right img {
    width: 47vw; /* z 450-verzie (odstránená pevná height) */
    height: auto;
    filter: drop-shadow(5px 2px 3px rgba(0, 0, 0, 0.5));
  }

  /* veľkosť tlačidiel a medzery ako v 450-verzii */
  .button {
    font-size: 3vw;
  } /* z 450-verzie */
  .mobile section .button {
    margin: 0.5em;
  } /* z 450-verzie */

  /* sekcia s boxami */
  .second {
    font-size: 100%;
    height: 19vw; /* z 450-verzie – nech boxy prečnievajú rovnako */
  }

  /* popisy pod obrázkami v boxoch – zachovaj tesné medzery */
  .mobile section p {
    font-size: 3.2vw;
  } /* z 450-verzie */

  /* nadpisy */
  :deep(h1.no-default) {
    padding: 0.25em 0 0;
  } /* rovnaké v oboch */
  :deep(h5.no-default) {
    font-size: 4vw; /* z prvého 750-bloku (väčšie H5) */
    margin: 0.7em 1em 0 !important; /* tesnejšie okraje z 450-verzie */
  }

  /* newslist */
  .mobile-newslist {
    margin: 2em auto 0;
  }
  .mobile-newslist .news-intro {
    font-size: 2.6vw;
    color: #575757;
    font-weight: 400;
    line-height: 130%;
    text-align: center;
    margin: 1.5em auto 0;
    width: 85%;
  }
}

/* --- Book block (mobil) --- */
.mobile-frstbook {
  margin: 2.2em auto 0;
  width: 100%;
  display: flex;
  justify-content: center;

  .book-card {
    width: 92%;
    padding: 1.5em 1.5em 1em;
  }

  .book-row {
    display: grid;
    grid-template-columns: auto 1fr;
    align-items: center;
    column-gap: 0.8em;
  }

  .book-cover {
    width: 22vw;
    max-width: 120px;
    height: auto;
    object-fit: contain;
    border-radius: 0.6em;
    filter: drop-shadow(2px 2px 3px rgba(0, 0, 0, 0.25));
  }

  .book-text {
    display: flex;
    flex-direction: column;
    gap: 0.35em;
  }

  .book-title {
    font-family: "Bahnschrift", sans-serif;
    font-size: 3.8vw;
    line-height: 1.2;
    font-weight: 400;
    margin: 0;
    color: #000;
    text-align: left;
  }
  .book-lead {
    color: #575757;
    font-size: 3.2vw;
    line-height: 1.35;
    margin: 0;
    text-align: left;
  }

  /* rozbalený text pod riadkom */
  .book-more-content {
    text-align: center;

    p {
      color: #575757;
      font-size: 3.2vw;
      line-height: 1.4;
      margin: 0.7em auto;
      width: 90%;
    }

    .book-points-wrapper {
      text-align: left;
      margin: 0.8em auto;
      width: 85%;
    }

    .points-title {
      margin-bottom: 0.4em;
      font-weight: bold;
      color: #575757;
    }

    .book-points {
      margin: 0;
      padding-left: 1.2em;
      li {
        margin-bottom: 0.4em;
        list-style-type: disc;
        color: #575757;
        font-size: 3.2vw;
        line-height: 1.4;
      }
    }
  }

  /* CTA sú POD rozbaleným textom */
  .book-ctas {
    display: flex;
    gap: 0.6em;
    margin-top: 0.5em;
    align-items: center;
    justify-content: center;

    .button {
      font-size: 2.5vw;
      font-family: "Adumu";
      padding: 0.55em 1em;
      border-radius: 1.8em;
      box-shadow: 3px 3px 3px 0 rgba(0, 0, 0, 0.25);
      border: none;
      cursor: pointer;
      transition: transform 0.15s ease;
      white-space: nowrap;
      -webkit-text-fill-color: #000;
      &:active {
        transform: scale(0.98);
      }
    }
    .book-buy {
      background: #fef35a;
      color: #000;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
    }
  }
}

/* rovnaká animácia ako NewsList */
.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.35s ease;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(-8px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

/* drobný tuning pre užšie mobily */
@media (max-width: 380px) {
  .mobile-frstbook .book-ctas .button {
    font-size: 3.8vw;
  }
}
</style>
