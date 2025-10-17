<template>
  <!-- DESKTOP: bez zmeny -->
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <h1>{{ $t("more.title") }}</h1>
      <h5 v-html="$t('more.lead')"></h5>

      <div class="line horizontal w-80"></div>
      <p class="text center w-85">
        Vitaj medzi milovníkmi heligónky! Objav radosť z hry na heligónke z
        pohodlia svojho domova tempom, ktoré ti dokonale vyhovuje. S našimi
        online náučnými videami a prehľadnými číselnými zápismi ťa prevedieme od
        úplných základov, cez melodickú časť, prácu s mechom, používanie basov a
        ich variácií, až po hranie tvojich obľúbených piesní.
      </p>
      <p class="text center w-85">
        Či si úplný začiatočník, alebo skúsený hráč, u nás nájdeš to, čo ťa
        posunie ďalej. Pridaj sa k našej hudobnej rodine ešte dnes uč sa
        jednoducho, hraj s radosťou a užívaj si hudbu kdekoľvek si!
      </p>

      <p class="big-text bold">
        Online výučba heligónky prostredníctvom <br />
        náučných videí kdekoľvek a kedykoľvek
      </p>

      <p class="text center w-80">
        Učiť sa hrať na heligónke ešte nikdy nebolo jednoduchšie. Vďaka našim
        online náučným videám sa môžeš zdokonaľovať z pohodlia domova bez
        nutnosti cestovať za učiteľom. Stačí si pustiť video a môžeš cvičiť
        ráno, večer, cez víkend… jednoducho vtedy, keď ti to vyhovuje.
      </p>

      <p class="text center w-80">
        Tento flexibilný a moderný spôsob výučby ti umožní učiť sa vlastným
        <br />
        tempom a opakovať si lekcie koľkokrát len chceš.
      </p>
      <!-- ONLINE: dynamický pomer bez orezu -->
      <figure
        class="m-img-wrap carousel online"
        :style="{ '--online-ar': onlineRatio }"
      >
        <transition name="xfade-zoom">
          <img
            class="slide-img"
            :key="onlineSlides[currentOnline].src"
            :src="onlineSlides[currentOnline].src"
            :alt="onlineSlides[currentOnline].alt"
            @load="onOnlineLoad"
            loading="lazy"
          />
        </transition>
      </figure>

      <div class="buttons">
        <routerLink to="/naucne-videa" class="button">
          <p>Naúčné videá</p>
        </routerLink>
      </div>

      <p class="big-text bold">
        Číselné zápisy pre hru na heligónke <br />Tvoje piesne vždy poruke
      </p>

      <p class="text center w-80">
        Okrem videí ti prinášame aj špeciálne číselné zápisy pre heligónku,
        vďaka ktorým sa naučíš zahrať pesničky rýchlo a bez zbytočného hľadania.
        Naša zbierka obsahuje jedinečné piesne, ktoré hráme v Heligónkovej
        akadémii a ktoré nájdeš priamo v našich online materiáloch.
      </p>

      <p class="text center w-80">
        Číselné zápisy nie sú len pre skúsených hráčov skvele poslúžia aj
        začiatočníkom, ktorí chcú rýchlo rozšíriť svoj repertoár a vyskúšať
        niečo nové. Stačí mať po ruke počítač alebo mobil a tvoja hudobná
        knižnica je vždy pripravená, nech si doma, na chate alebo na vystúpení.
      </p>
      <p class="text center w-85">
        Chceš objaviť nové piesne a posunúť svoje hranie na ďalší level?
        <br />Pozri si našu kolekciu číselných zápisov a začni hrať už dnes!
      </p>
      <!-- ČÍSELNÉ ZÁPISY: vyšší rámik, „plnší“ cover -->
      <figure
        class="m-img-wrap carousel ciselne"
        :style="{ '--ciselne-ar': ciselneRatio }"
      >
        <transition name="xfade-zoom">
          <img
            class="slide-img"
            :key="ciselneSlides[currentCiselne].src"
            :src="ciselneSlides[currentCiselne].src"
            :alt="ciselneSlides[currentCiselne].alt"
            @load="onCiselneLoad"
            loading="eager"
          />
        </transition>
      </figure>
      <!-- KOTVA pre plynulé rolovanie k rozbalenému obsahu -->
      <div ref="benefitsAnchor" class="reveal-anchor"></div>

      <!-- Rozbaľovací text s animáciou -->
      <transition name="reveal">
        <div v-show="showMore" class="reveal-panel">
          <!-- Body s podnadpisom + odsekom -->
          <!-- Hlavný nadpis sekcie – rovnaký vzhľad ako pri "Číselné zápisy..." -->
          <p class="big-text bold benefits-heading">
            Výhody našich online číselných zápisov
          </p>
          <div class="numeric-benefits">
            <div class="benefit">
              <h4 class="benefit-title">Okamžité prehratie pesničky</h4>
              <p class="text center w-80">
                Pri každom zápise si môžeš hneď vypočuť, ako skladba znie na
                heligónke a to ešte predtým, než ju otvoríš. Stačí kliknúť na
                ikonu prehrávania v zozname čiselných zapisoch piesní alebo
                priamo v otvorenom zápise a máš jasnú predstavu, do čoho ideš.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Dvojité zápisy</h4>
              <p class="text center w-80">
                Ak pesnička obsahuje viac stupníc, jednoducho si medzi nimi
                prepneš. Naše zápisy pripravujeme primárne pre ladenie C-F (pri
                trojradových heligónkach G-C-F), no nie je problém zahrať si ich
                aj na iných ladeniach. Takto si môžeš vybrať verziu, ktorá ti
                sadne najviac.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Text piesne na dosah</h4>
              <p class="text center w-80">
                Tak ako v našom online spevníku, aj pri každom zápise máš hneď
                po ruke text piesne. Môžeš sa tak učiť melódiu spolu so slovami
                a mať kompletný zážitok z hrania aj spievania.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Obľúbené piesne</h4>
              <p class="text center w-80">
                Každú skladbu si vieš pridať do obľúbených, aby si sa k nej
                rýchlo vrátil. Ideálne, keď chceš mať po ruke svoje „srdcovky“
                alebo si zostaviť vlastný zoznam na cvičenie.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Označenie zvládnutých skladieb</h4>
              <p class="text center w-80">
                Priamo v zápise si označíš, ktoré piesne už máš zvládnuté a
                ktoré ešte potrebuješ dotrénovať. Takto máš vždy prehľad o
                svojom pokroku a vieš, na čom zapracovať.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Vždy po ruke</h4>
              <p class="text center w-80">
                Či máš mobil, tablet alebo počítač tvoja hudobná knižnica je
                stále s tebou. Učiť sa môžeš doma, na chate, na dovolenke alebo
                aj tesne pred vystúpením.
              </p>
            </div>
          </div>
        </div>
      </transition>
      <!-- DVE CTA vedľa seba -->
      <div class="buttons">
        <div class="cta-row">
          <button type="button" class="button" @click="toggleMore">
            <span v-if="!showMore">Zobraziť viac</span>
            <span v-else>Zobraziť menej</span>
          </button>

          <routerLink to="/ciselne-zapisy" class="button">
            Číselné zápisy
          </routerLink>
        </div>
      </div>
      <p class="big-text bold">HeliČas - Hraj, uč sa a získavaj odmeny</p>

      <p class="text center w-80">
        Učenie na heligónke u nás nie je len o hraní je to aj o zábave, výzvach
        a odmenách. <br />
        Vďaka nášmu HeliČas systému získavaš body za každú minútu, ktorú stráviš
        <br />
        hraním a cvičením na našej stránke. <br />
        <span class="text center time">1 minúta hrania = 1 bod </span>
      </p>
      <!-- Rozbalený obsah – totočne ako pri Číselných zápisoch -->
      <transition name="reveal">
        <div v-show="showChase" class="reveal-panel">
          <p class="text center w-80">
            A to nie je všetko. Body môžeš zbierať aj plnením rôznych úloh od
            jednoduchých výziev až po poriadne hudobné misie. Čím viac hráš a
            trénuješ, tým viac bodov nazbieraš.
          </p>
          <!-- Slider s obrázkami (fade, autoplay, swipe ako inde) -->
          <figure class="m-img-wrap carousel helichase">
            <transition name="edge-swipe">
              <img
                class="slide-img"
                :key="helichaseSlides[currentHelichase].src"
                :src="helichaseSlides[currentHelichase].src"
                :alt="helichaseSlides[currentHelichase].alt"
                loading="lazy"
              />
            </transition>
          </figure>
          <p class="text center w-80">
            Nazbierané body si potom môžeš vymeniť za číselné zápisy a tak si
            rozšíriť svoj repertoár bez toho, aby si si ich musel kupovať. Takto
            nielenže napreduješ vo svojej hre, ale ešte k tomu dostávaš odmeny
            za svoju snahu.
          </p>

          <p class="text center w-80">
            HeliČas ti zároveň ukáže tvoje štatistiky učenia koľko dní, hodín a
            minút si sa venoval hre na heligónke. A ak chceš, môžeš sa porovnať
            aj s ostatnými hráčmi a zistiť, kto je v našej hudobnej komunite
            najaktívnejší.
          </p>

          <p class="text center last-text">
            Tak poď na to uč sa, hraj a nech body pribúdajú!
          </p>
        </div>
      </transition>

      <!-- CTA RAD (TOTOČNE ako pri Číselných zápisoch) – pod rozbaleným obsahom -->

      <div class="buttons">
        <button type="button" class="button cta-btn" @click="toggleChase">
          <span v-if="!showChase">Zobraziť viac</span>
          <span v-else>Zobraziť menej</span>
        </button>
      </div>
      <div class="line horizontal w-80"></div>
      <div class="registracia-grid">
        <!-- Ľavý stĺpec – fotka -->
        <div class="registracia-image">
          <figure class="m-img-wrap carousel">
            <transition name="xfade-zoom">
              <img
                class="slide-img"
                src="@/assets/images/gallery/JurajAAndrejZistitViac.png"
                alt="Učitelia heligónky Juraj a Andrej - Heligónková Akadémia"
                loading="lazy"
              />
            </transition>
          </figure>
        </div>

        <!-- Pravý stĺpec – text + CTA -->
        <div class="registracia-text">
          <p class="big-text bold">Začni hrať na heligónke s nami už dnes!</p>

          <p class="text">
            Zaregistruj sa a získaj prístup k desiatkam náučných videí, číselným
            zápisom a odmeňovaciemu systému s bodmi za učenie. Klikni na
            „Zaregistrovať sa“ a poď si s nami zahrať svoje prvé piesne!
          </p>

          <routerLink to="/registracia" class="button cta-btn">
            Zaregistrovať sa
          </routerLink>
        </div>
      </div>
    </div>
  </section>

  <!-- MOBILE: rozdelené na panely; každý panel sa prispôsobí obsahu -->
  <div class="mobile zistit-viac">
    <!-- 1) H1 + H5 -->
    <section class="m-panel">
      <div class="scroll m-tight">
        <h1>{{ $t("more.title") }}</h1>
        <h5 v-html="$t('more.lead')"></h5>
        <div class="line horizontal w-80 m-line"></div>
        <p class="text center w-85">
          Vitaj medzi milovníkmi heligónky! Objav radosť z hry na heligónke z
          pohodlia svojho domova tempom, ktoré ti dokonale vyhovuje. S našimi
          online náučnými videami a prehľadnými číselnými zápismi ťa prevedieme
          od úplných základov, cez melodickú časť, prácu s mechom, používanie
          basov a ich variácií, až po hranie tvojich obľúbených piesní.
        </p>
        <p class="text center w-85 second85">
          Či si úplný začiatočník, alebo skúsený hráč, u nás nájdeš to, čo ťa
          posunie ďalej. Pridaj sa k našej hudobnej rodine ešte dnes uč sa
          jednoducho, hraj s radosťou a užívaj si hudbu kdekoľvek si!
        </p>
      </div>
    </section>

    <!-- 3) Online výučba -->
    <section class="m-panel">
      <div class="scroll m-tight">
        <p class="big-text bold">
          Online výučba heligónky prostredníctvom náučných videí kdekoľvek a
          kedykoľvek
        </p>
        <p class="text center w-80">
          Učiť sa hrať na heligónke ešte nikdy nebolo jednoduchšie. Vďaka našim
          online náučným videám sa môžeš zdokonaľovať z pohodlia domova bez
          nutnosti cestovať za učiteľom. Stačí si pustiť video a môžeš cvičiť
          ráno, večer, cez víkend… jednoducho vtedy, keď ti to vyhovuje.
        </p>
        <p class="text center w-80">
          Tento flexibilný a moderný spôsob výučby ti umožní učiť sa vlastným
          tempom a opakovať si lekcie koľkokrát len chceš.
        </p>

        <!-- ONLINE: dynamický pomer bez orezu -->
        <figure
          class="m-img-wrap carousel online"
          :style="{ '--online-ar': onlineRatio }"
        >
          <transition name="xfade-zoom">
            <img
              class="slide-img"
              :key="onlineSlides[currentOnline].src"
              :src="onlineSlides[currentOnline].src"
              :alt="onlineSlides[currentOnline].alt"
              @load="onOnlineLoad"
              loading="lazy"
            />
          </transition>
        </figure>

        <routerLink to="/naucne-videa" class="button cta-over-img">
          Naučné videa
        </routerLink>
      </div>
    </section>

    <!-- 4) Číselné zápisy -->
    <section class="m-panel">
      <div class="scroll m-tight">
        <p class="big-text bold">
          Číselné zápisy pre hru na heligónke <br />tvoje piesne vždy poruke
        </p>
        <p class="text center w-80">
          Okrem videí ti prinášame aj špeciálne číselné zápisy pre heligónku,
          vďaka ktorým sa naučíš zahrať pesničky rýchlo a bez zbytočného
          hľadania. Naša zbierka obsahuje jedinečné piesne, ktoré hráme v
          Heligónkovej akadémii a ktoré nájdeš priamo v našich online
          materiáloch.
        </p>

        <p class="text center w-80">
          Číselné zápisy nie sú len pre skúsených hráčov skvele poslúžia aj
          začiatočníkom, ktorí chcú rýchlo rozšíriť svoj repertoár a vyskúšať
          niečo nové. Stačí mať po ruke počítač alebo mobil a tvoja hudobná
          knižnica je vždy pripravená, nech si doma, na chate alebo na
          vystúpení.
        </p>
        <p class="text center w-80">
          Chceš objaviť nové piesne a posunúť svoje hranie na ďalší level? Pozri
          si našu kolekciu číselných zápisov a začni hrať už dnes!
        </p>
      </div>

      <!-- ČÍSELNÉ ZÁPISY: vyšší rámik, „plnší“ cover -->
      <figure
        class="m-img-wrap carousel ciselne"
        :style="{ '--ciselne-ar': ciselneRatio }"
      >
        <transition name="xfade-zoom">
          <img
            class="slide-img"
            :key="ciselneSlides[currentCiselne].src"
            :src="ciselneSlides[currentCiselne].src"
            :alt="ciselneSlides[currentCiselne].alt"
            @load="onCiselneLoad"
            loading="eager"
          />
        </transition>
      </figure>

      <!-- KOTVA pre plynulé rolovanie k rozbalenému obsahu -->
      <div ref="benefitsAnchor" class="reveal-anchor"></div>

      <!-- Rozbaľovací text s animáciou -->
      <transition name="reveal">
        <div v-show="showMore" class="reveal-panel">
          <!-- Body s podnadpisom + odsekom -->
          <!-- Hlavný nadpis sekcie – rovnaký vzhľad ako pri "Číselné zápisy..." -->
          <p class="big-text bold benefits-heading">
            Výhody našich online číselných zápisov
          </p>
          <div class="numeric-benefits">
            <div class="benefit">
              <h4 class="benefit-title">Okamžité prehratie pesničky</h4>
              <p class="text center w-80 benefit-desc">
                Pri každom zápise si môžeš hneď vypočuť, ako skladba znie na
                heligónke a to ešte predtým, než ju otvoríš. Stačí kliknúť na
                ikonu prehrávania v zozname čiselných zapisoch piesní alebo
                priamo v otvorenom zápise a máš jasnú predstavu, do čoho ideš.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Dvojité zápisy</h4>
              <p class="text center w-80 benefit-desc">
                Ak pesnička obsahuje viac stupníc, jednoducho si medzi nimi
                prepneš. Naše zápisy pripravujeme primárne pre ladenie C-F (pri
                trojradových heligónkach G-C-F), no nie je problém zahrať si ich
                aj na iných ladeniach. Takto si môžeš vybrať verziu, ktorá ti
                sadne najviac.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Text piesne na dosah</h4>
              <p class="text center w-80 benefit-desc">
                Tak ako v našom online spevníku, aj pri každom zápise máš hneď
                po ruke text piesne. Môžeš sa tak učiť melódiu spolu so slovami
                a mať kompletný zážitok z hrania aj spievania.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Obľúbené piesne</h4>
              <p class="text center w-80 benefit-desc">
                Každú skladbu si vieš pridať do obľúbených, aby si sa k nej
                rýchlo vrátil. Ideálne, keď chceš mať po ruke svoje „srdcovky“
                alebo si zostaviť vlastný zoznam na cvičenie.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Označenie zvládnutých skladieb</h4>
              <p class="text center w-80 benefit-desc">
                Priamo v zápise si označíš, ktoré piesne už máš zvládnuté a
                ktoré ešte potrebuješ dotrénovať. Takto máš vždy prehľad o
                svojom pokroku a vieš, na čom zapracovať.
              </p>
            </div>

            <div class="benefit">
              <h4 class="benefit-title">Vždy po ruke</h4>
              <p class="text center w-80 benefit-desc">
                Či máš mobil, tablet alebo počítač tvoja hudobná knižnica je
                stále s tebou. Učiť sa môžeš doma, na chate, na dovolenke alebo
                aj tesne pred vystúpením.
              </p>
            </div>
          </div>
        </div>
      </transition>

      <!-- DVE CTA vedľa seba -->
      <div class="cta-row">
        <button type="button" class="button cta-btn" @click="toggleMore">
          <span v-if="!showMore">Zobraziť viac</span>
          <span v-else>Zobraziť menej</span>
        </button>

        <routerLink to="/ciselne-zapisy" class="button cta-btn">
          Číselné zápisy
        </routerLink>
      </div>
    </section>

    <!-- 5) HeliChase – hraj, uč sa a získavaj odmeny -->
    <section class="m-panel">
      <div class="scroll m-tight">
        <p class="big-text bold">HeliČas - Hraj, uč sa a získavaj odmeny</p>

        <p class="text center w-80">
          Učenie na heligónke u nás nie je len o hraní je to aj o zábave,
          výzvach a odmenách. Vďaka nášmu HeliČas systému získavaš body za každú
          minútu, ktorú stráviš hraním a cvičením na našej stránke. <br />
          <span class="text center time">1 minúta hrania = 1 bod </span>
        </p>
      </div>

      <!-- KOTVA: rovnaké miesto ako pri Číselných zápisoch -->
      <div ref="chaseAnchor" class="reveal-anchor"></div>

      <!-- Rozbalený obsah – totočne ako pri Číselných zápisoch -->
      <transition name="reveal">
        <div v-show="showChase" class="reveal-panel">
          <p class="text center w-80">
            A to nie je všetko. Body môžeš zbierať aj plnením rôznych úloh od
            jednoduchých výziev až po poriadne hudobné misie. Čím viac hráš a
            trénuješ, tým viac bodov nazbieraš.
          </p>
          <!-- Slider s obrázkami (fade, autoplay, swipe ako inde) -->
          <figure class="m-img-wrap carousel helichase">
            <transition name="edge-swipe">
              <img
                class="slide-img"
                :key="helichaseSlides[currentHelichase].src"
                :src="helichaseSlides[currentHelichase].src"
                :alt="helichaseSlides[currentHelichase].alt"
                loading="lazy"
              />
            </transition>
          </figure>
          <p class="text center w-80">
            Nazbierané body si potom môžeš vymeniť za číselné zápisy a tak si
            rozšíriť svoj repertoár bez toho, aby si si ich musel kupovať. Takto
            nielenže napreduješ vo svojej hre, ale ešte k tomu dostávaš odmeny
            za svoju snahu.
          </p>

          <p class="text center w-80">
            HeliČas ti zároveň ukáže tvoje štatistiky učenia koľko dní, hodín a
            minút si sa venoval hre na heligónke. A ak chceš, môžeš sa porovnať
            aj s ostatnými hráčmi a zistiť, kto je v našej hudobnej komunite
            najaktívnejší.
          </p>

          <p class="text center last-text">
            Tak poď na to uč sa, hraj a nech body pribúdajú!
          </p>
        </div>
      </transition>

      <!-- CTA RAD (TOTOČNE ako pri Číselných zápisoch) – pod rozbaleným obsahom -->
      <div class="cta-row">
        <button type="button" class="button cta-btn" @click="toggleChase">
          <span v-if="!showChase">Zobraziť viac</span>
          <span v-else>Zobraziť menej</span>
        </button>
      </div>
    </section>

    <!-- 6) Registrácia + obrázok -->

    <section class="m-panel">
      <div class="scroll m-tight">
        <p class="big-text bold">Začni hrať na heligónke s nami už dnes!</p>

        <p class="text center w-80">
          Zaregistruj sa a získaj prístup k desiatkam náučných videí, číselným
          zápisom a odmeňovaciemu systému s bodmi za učenie. Klikni na
          „Zaregistrovať sa“ a poď si s nami zahrať svoje prvé piesne!
        </p>
      </div>

      <!-- Obrázok (rovnaký „card“ kontajner/štýl ako inde) -->
      <!-- Registrácia: obrázok s tlačidlom priamo na ňom -->
      <div class="cta-on-image">
        <figure class="m-img-wrap carousel">
          <transition name="xfade-zoom">
            <img
              class="slide-img"
              src="@/assets/images/gallery/JurajAAndrejZistitViac.png"
              alt="Učitelia heligónky Juraj a Andrej - Heligónková Akadémia"
              loading="lazy"
            />
          </transition>
        </figure>
        <div class="buttons">
          <div class="cta-image">
            <routerLink to="/registracia" class="button cta-over-img">
              Zaregistrovať sa
            </routerLink>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { ref, onMounted, onBeforeUnmount, nextTick } from "vue";

export default {
  name: "ZistitViac",

  mounted() {
    window.scrollTo(0, 0);
  },

  setup() {
    /* =========================
       Slider #1 – NÁUČNÉ VIDEÁ
       ========================= */
    const onlineSlides = ref([
      {
        src: require("@/assets/images/gallery/NaucneVideo1.png"),
        alt: "Ukážka náučných videí – lekcia 1",
      },
      {
        src: require("@/assets/images/gallery/NaucneVideo2.png"),
        alt: "Ukážka náučných videí – lekcia 2",
      },
      {
        src: require("@/assets/images/gallery/NaucneVideo3.png"),
        alt: "Ukážka náučných videí – detail prstov",
      },
    ]);
    const currentOnline = ref(0);

    // dynamický pomer pre Naučné videá (žiadny orez)
    const onlineRatio = ref("16 / 9");
    const onOnlineLoad = (e) => {
      const img = e?.target;
      if (!img) return;
      const { naturalWidth: w, naturalHeight: h } = img;
      if (w && h) onlineRatio.value = `${w} / ${h}`;
    };

    /* ==========================================
       Slider #2 – ČÍSELNÉ ZÁPISY
       ========================================== */
    const ciselneSlides = ref([
      {
        src: require("@/assets/images/gallery/CiselnyZapis1.png"),
        alt: "Číselné zápisy – ukážka strany 1",
      },
      {
        src: require("@/assets/images/gallery/CiselnyZapis2.png"),
        alt: "Číselné zápisy – ukážka strany 2",
      },
      {
        src: require("@/assets/images/gallery/CiselnyZapis3.png"),
        alt: "Číselné zápisy – ukážka strany 3",
      },
    ]);
    const currentCiselne = ref(0);

    /* =========================
       HeliChase – slider + toggle
       ========================= */
    const helichaseSlides = ref([
      // nahraď svojimi HeliChase obrázkami, keď ich budeš mať
      {
        src: require("@/assets/images/gallery/Uloha1.png"),
        alt: "HeliChase ukážka 1",
      },
      {
        src: require("@/assets/images/gallery/Uloha2.png"),
        alt: "HeliChase ukážka 2",
      },
      {
        src: require("@/assets/images/gallery/Uloha3.png"),
        alt: "HeliChase ukážka 3",
      },
    ]);
    const currentHelichase = ref(0);
    const showChase = ref(false);
    const chaseAnchor = ref(null);

    /* ===== Autoplay / timery (všetky slidery) ===== */
    const onlineIntervalMs = 7000;
    const ciselneIntervalMs = 7000;
    const helichaseIntervalMs = 7000;

    let timerOnline = null;
    let timerCiselne = null;
    let timerHelichase = null;

    const paused = ref(false);

    const nextOnline = () => {
      currentOnline.value =
        (currentOnline.value + 1) % onlineSlides.value.length;
    };
    const nextCiselne = () => {
      currentCiselne.value =
        (currentCiselne.value + 1) % ciselneSlides.value.length;
    };
    const nextHelichase = () => {
      currentHelichase.value =
        (currentHelichase.value + 1) % helichaseSlides.value.length;
    };

    const start = () => {
      stop();
      if (onlineSlides.value.length > 1) {
        timerOnline = setInterval(() => {
          if (!paused.value) nextOnline();
        }, onlineIntervalMs);
      }
      if (ciselneSlides.value.length > 1) {
        timerCiselne = setInterval(() => {
          if (!paused.value) nextCiselne();
        }, ciselneIntervalMs);
      }
      if (helichaseSlides.value.length > 1) {
        timerHelichase = setInterval(() => {
          if (!paused.value) nextHelichase();
        }, helichaseIntervalMs);
      }
    };

    const stop = () => {
      if (timerOnline) {
        clearInterval(timerOnline);
        timerOnline = null;
      }
      if (timerCiselne) {
        clearInterval(timerCiselne);
        timerCiselne = null;
      }
      if (timerHelichase) {
        clearInterval(timerHelichase);
        timerHelichase = null;
      }
    };

    const onVisibilityChange = () => {
      paused.value = document.hidden || false;
    };

    onMounted(() => {
      start();
      document.addEventListener("visibilitychange", onVisibilityChange);
    });

    onBeforeUnmount(() => {
      stop();
      document.removeEventListener("visibilitychange", onVisibilityChange);
    });

    /* „Zobraziť viac“ – Číselné zápisy */
    const showMore = ref(false);
    const benefitsAnchor = ref(null);
    const toggleMore = async () => {
      const opening = !showMore.value;
      showMore.value = opening;
      if (opening) {
        await nextTick();
        if (benefitsAnchor.value) {
          benefitsAnchor.value.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      }
    };
    // ČÍSELNÉ ZÁPISY – dynamický pomer bez orezu
    const ciselneRatio = ref("4 / 3");
    const onCiselneLoad = (e) => {
      const img = e?.target;
      if (!img) return;
      const { naturalWidth: w, naturalHeight: h } = img;
      if (w && h) ciselneRatio.value = `${w} / ${h}`;
    };

    /* „Zobraziť viac“ – HeliChase (smooth scroll na kotvu) */
    const toggleChase = async () => {
      const opening = !showChase.value;
      showChase.value = opening;
      if (opening) {
        await nextTick();
        if (chaseAnchor.value) {
          chaseAnchor.value.scrollIntoView({
            behavior: "smooth",
            block: "start",
          });
        }
      }
    };

    return {
      // slider: náučné videá
      onlineSlides,
      currentOnline,
      onlineRatio,
      onOnlineLoad,

      // slider: číselné zápisy
      ciselneSlides,
      currentCiselne,
      ciselneRatio,
      onCiselneLoad,

      // reveal + scroll – číselné zápisy
      showMore,
      toggleMore,
      benefitsAnchor,

      // HeliChase
      helichaseSlides,
      currentHelichase,
      showChase,
      toggleChase,
      chaseAnchor,
    };
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

/* ===== HEADINGS (ponechané) ===== */
h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.2vw;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.03em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0 0 0.2em;
}
h5 {
  text-align: center;
  font-size: 1.6vw;
  font-weight: 550;
  line-height: 115%;
  margin: 0.3em auto 0.7em;
}

:deep(.desktop-break) {
  display: inline;
}

@media screen and (max-width: 750px) {
  :deep(.desktop-break) {
    display: none;
  }
}

/* =========================================================
   DESKTOP (typografia + layout) – PONECHANÉ
   ========================================================= */
.computer {
  /* šírky + radius na jednom mieste */
  --w-online: clamp(420px, 36vw, 640px);
  --w-ciselne: clamp(400px, 32vw, 580px);
  --w-helichase: clamp(420px, 36vw, 640px);
  --w-registracia: clamp(420px, 36vw, 600px);
  --radius: 1.5em;

  .w-80 {
    width: 80%;
    margin-left: auto;
    margin-right: auto;
  }
  .w-85 {
    width: 85%;
    margin-left: auto;
    margin-right: auto;
  }

  .text {
    font-size: 1.2vw;
    line-height: 1.5;
    letter-spacing: 0.01em;
    margin: 0.9em auto;
    max-width: 77ch;
    text-align: center;
  }
  .big-text {
    font-size: 1.5vw;
    line-height: 1.35;
    margin: 1.2em auto 0.6em;
    text-align: center;
  }
  .bold {
    font-weight: 550;
  }
  .semi-bold {
    font-weight: 700;
    margin: 1em auto;
    max-width: 70ch;
    text-align: center;
  }
  .line {
    width: 80%;
    margin: 0.8em auto 2.2em;
  }
  .benefit-title {
    font-size: 1.3vw;
  }

  .buttons {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    margin: 1.2em 0 2.2em;
  }
  .buttons .button p {
    margin: 0;
  }
  .cta-row {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.9rem;
    margin: 1.1em auto 2em;
  }

  /* Reveal animácie – ponechané */
  .reveal-panel {
    overflow: hidden;
    width: 100%;
    margin: 0.2em auto 0.8em;
    will-change: max-height, opacity, transform;
  }
  .reveal-enter-from,
  .reveal-leave-to {
    opacity: 0;
    transform: translateY(-2px);
    max-height: 0;
  }
  .reveal-enter-active,
  .reveal-leave-active {
    transition: opacity 320ms ease, transform 320ms ease, max-height 380ms ease;
  }
  .reveal-enter-to,
  .reveal-leave-from {
    opacity: 1;
    transform: translateY(0);
    max-height: 1200px;
  }
  .reveal-anchor {
    scroll-margin-top: 72px;
  }

  .time {
    display: block;
    margin: 0.35em auto 0;
    font-weight: 700;
    font-size: clamp(18px, 1.6vw, 28px);
    line-height: 1.15;
    text-align: center;
    white-space: nowrap;
    letter-spacing: 0.01em;
  }
  .last-text {
    font-weight: 550;
    margin-bottom: 0;
    text-align: center;
  }

  @media (max-width: 1200px) {
    .text {
      font-size: 1.3vw;
    }
    .big-text {
      font-size: 1.7vw;
    }
  }
  @media (max-width: 1000px) {
    .text {
      font-size: 1.4vw;
    }
    .big-text {
      font-size: 1.9vw;
    }
  }

  /* vypnuté vnútorné scrollovanie panelu */
  .computer .scroll {
    overflow: visible !important;
    height: auto !important;
    min-height: 0 !important;
    padding: 0;
  }
}

/* =========================================================
   DESKTOP – OBRÁZKY (ZJEDNOTENÉ, rovnaká logika ako na mobile)
   ========================================================= */
.computer {
  /* jednotný wrapper pre všetky carousel obrázky */
  .m-img-wrap.carousel {
    position: relative;
    display: block;
    width: auto;
    max-width: 100%;
    margin: 1.2em auto;
    border-radius: var(--radius);
    overflow: hidden;
    line-height: 0;
  }

  /* samotná fotka – vždy zdedí radius a vyplní wrapper */
  .m-img-wrap.carousel .slide-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border-radius: inherit; /* <<< kľúčové, nech radius platí aj na fotke */
    object-position: center;
    backface-visibility: hidden;
    transform: translateZ(0);
  }

  /* Naučné videá: dynamický pomer z :style="--online-ar", bez orezu */
  .m-img-wrap.carousel.online {
    width: var(--w-online);
    aspect-ratio: var(--online-ar, 16 / 9);
  }
  .m-img-wrap.carousel.online .slide-img {
    object-fit: contain;
  }

  /* Číselné zápisy: dynamický pomer z :style="--ciselne-ar", plnší „cover“ */
  .m-img-wrap.carousel.ciselne {
    width: var(--w-ciselne);
    aspect-ratio: var(--ciselne-ar, 4 / 3);
  }
  .m-img-wrap.carousel.ciselne .slide-img {
    object-fit: cover;
  }

  /* HeliČas: pevný pomer podľa Figmy, bez orezu */
  .m-img-wrap.carousel.helichase {
    width: var(--w-helichase);
    aspect-ratio: 666 / 251;
  }
  .m-img-wrap.carousel.helichase .slide-img {
    object-fit: contain;
  }

  /* Registrácia: prirodzený pomer, obraz „v toku“, ale s radiusom */
  .cta-on-image .m-img-wrap.carousel {
    width: var(--w-registracia);
    aspect-ratio: auto; /* nechá prirodzený pomer */
    background: transparent;
  }
  .cta-on-image .m-img-wrap.carousel .slide-img {
    position: static; /* nech sa počíta prirodzená výška */
    display: block;
    width: 100%;
    height: auto;
    border-radius: var(--radius);
    object-fit: contain;
    object-position: center;
  }

  /* jemný fade bez zoomu */
  .xfade-zoom-enter-active,
  .xfade-zoom-leave-active {
    position: absolute;
    inset: 0;
    transition: opacity 480ms ease;
  }
  .xfade-zoom-enter-from,
  .xfade-zoom-leave-to {
    opacity: 0;
  }
}

/* Jednotný look tlačidiel na desktop */
.computer .button,
.computer .cta-btn,
.computer .cta-over-img {
  font-family: "Adumu", system-ui, -apple-system, sans-serif;
  font-size: 1.05rem;
  padding: 0.55em 1.1em;
  border-radius: 1.8em;
}
/* ===== Registrácia – 2 stĺpce (desktop) + foto na spodku panela ===== */
.computer .registracia-grid {
  display: grid;
  grid-template-columns: 1fr 1fr; /* foto | text */
  gap: clamp(6px, 0vw, 40px);
  align-items: end; /* spodná hrana stĺpcov */
  max-width: 1200px;
  margin: 0 auto;
}

.computer .registracia-image {
  display: flex;
  align-items: flex-end; /* prilepí obsah dole */
  justify-content: center;
  align-self: end; /* pre istotu aj na položke */
}
.computer .registracia-image .m-img-wrap.carousel {
  width: 100%;
  max-width: 520px;
  margin: 0; /* žiadna medzera pod fotkou */
  aspect-ratio: auto;
}
.computer .registracia-image .m-img-wrap.carousel .slide-img {
  position: static;
  display: block;
  width: 100%;
  height: auto;
  object-fit: contain;
  object-position: bottom center; /* vizuálne dolu */
  border-radius: var(--radius);
}

/* Textový stĺpec (vpravo) – centrovaný obsah aj tlačidlo */
.computer .registracia-text {
  display: flex;
  flex-direction: column;
  justify-content: center; /* vertikálny stred */
  align-items: center; /* horizontálny stred */
  text-align: center;
  margin-bottom: 2vw;
}
.computer .registracia-text .text {
  max-width: 58ch;
  margin: 0.8em 0 1.2em;
}
/* väčšie, zelené CTA */
.computer .registracia-text .button {
  background: #8ec84e;
  color: #000 !important;
  -webkit-text-fill-color: #000;
  border: none;
  padding: 0.85em 1.75em;
  font-size: clamp(16px, 1.15vw, 20px);
  border-radius: 2em;
  box-shadow: 0 6px 14px rgba(0, 0, 0, 0.12);
}

/* ===== Tablet & down – pod 1100 px: text hore, fotka dole, CTA na fotke ===== */
@media (max-width: 1100px) {
  h1 {
    font-size: 6vw;
    margin: 0 8vw;
  }
  h5 {
    font-size: 2.2vw;
    margin: 0.8em 6vw;
  }
  .computer .registracia-grid {
    grid-template-columns: 1fr;
    row-gap: 1.2em;
    position: relative; /* kotva pre absolútne CTA */
  }

  /* poradie položiek: text prvý, fotka druhá */
  .computer .registracia-text {
    order: 1;
  }
  .computer .registracia-image {
    order: 2;
    position: relative;
    padding-bottom: 3.6em; /* rezerva pre preložené tlačidlo */
  }

  /* presunieme EXISTUJÚCE tlačidlo vizuálne na fotku */
  .computer .registracia-text .button {
    position: absolute;
    left: 50%;
    bottom: clamp(12px, 3.6vw, 27px);
    transform: translateX(-50%);
    z-index: 2;
    /* na menšej šírke môže byť o chlp väčšie */
    font-size: clamp(15px, 2vw, 27px);
    padding: 0.9em 1.9em;
  }
}

/* jemné zmenšenie fotky ešte nižšie */
@media (max-width: 900px) {
  .computer .registracia-image .m-img-wrap.carousel {
    max-width: 460px;
  }
}

/* ===== iOS/WebKit – potlač modrých tlačidiel/odkazov ===== */
.button,
.button:link,
.button:visited,
.cta-btn,
.cta-over-img {
  color: #000 !important;
  -webkit-text-fill-color: #000;
  text-decoration: none !important;
  appearance: none;
  -webkit-appearance: none;
  -webkit-tap-highlight-color: transparent;
}
/* =========================================================
   MOBIL – PONECHANÉ (vizuál ostáva rovnaký)
   ========================================================= */
.mobile {
  .w-80:not(.line),
  .w-90,
  .w-85,
  .w-75,
  .w-70,
  .w-50 {
    width: 100% !important;
    padding: 0 6vw;
    box-sizing: border-box;
  }
  .w-50 {
    width: 70% !important;
  }

  .buttons {
    margin-bottom: 0;
  }
  .buttons .button {
    margin: 0 auto 1em;
  }

  .scroll {
    margin-bottom: 0;
    height: auto;
    min-height: unset;
  }
  section {
    margin-bottom: 0;
  }
  section h1 {
    font-size: 11vw !important;
  }
  section h5 {
    margin: 0.5em 6vw 0.7em;
  }
  .computer .text {
    font-size: 2vw;
  }
}

/* Panely (mobile) */
.m-panel {
  margin-top: 2.2em;
  padding: 0;
  height: auto;
  min-height: unset;
  overflow: hidden;
}
.m-panel:first-of-type {
  margin-top: 0;
}
.m-panel:not(:first-of-type) .big-text.bold {
  margin: 1.5em 6vw 0.5em !important;
}
.m-tight {
  padding-top: 0.6em;
  padding-bottom: 0.6em;
}
.m-line {
  margin: 0.8em auto 2em !important;
}

/* ===== max-width: 750px ===== */
@media only screen and (max-width: 750px) {
  .mobile {
    padding-bottom: 7em;
  }

  .m-tight > *:first-child {
    margin-top: 0 !important;
  }
  .m-tight > *:last-child {
    margin-bottom: 0 !important;
  }

  .line {
    box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.25);
  }
  .m-line {
    margin: 2.8vw auto 4vw !important;
  }

  .big-text {
    font-size: 4vw;
  }
  .text {
    font-size: 2.6vw;
  }
  h5 {
    font-size: 4vw !important;
  }
  .w-85 {
    width: 81% !important;
  }

  .second85 {
    width: auto !important;
    margin-left: auto !important;
    margin-right: auto !important;
    padding-left: 10vw !important;
    padding-right: 10vw !important;
    box-sizing: border-box;
    margin-top: 0.8em !important;
    margin-bottom: 1.5vw !important;
  }
  .center {
    padding-bottom: 1em;
  }

  .mobile .m-panel:first-of-type .w-85 {
    margin-bottom: 1.5em !important;
  }

  .mobile .m-panel > .scroll,
  .m-panel > .scroll {
    overflow: visible !important;
    height: auto !important;
    min-height: 0 !important;
    padding: 0 !important;
    border-radius: 0 !important;
    scrollbar-gutter: auto !important;
  }
  .m-panel {
    overflow: hidden !important;
  }

  /* Obrázky – jednotný „card“ kontajner (mobile) */
  .m-img-wrap {
    position: relative;
    display: block;
    width: calc(100% - 10vw);
    margin: 0.8em auto 0;
    border-radius: 1em;
    overflow: hidden;
    line-height: 0;
    background: #efefef;
  }

  /* ONLINE (mobile) – bez orezu, dynamický pomer */
  .m-img-wrap.carousel.online {
    aspect-ratio: var(--online-ar, 16 / 9);
  }
  .m-img-wrap.carousel.online .slide-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
    object-position: center;
    backface-visibility: hidden;
    transform: translateZ(0);
  }

  /* ČÍSELNÉ ZÁPISY (mobile) – plnší cover + vyšší rámik, dynamický pomer */
  .m-img-wrap.carousel.ciselne {
    aspect-ratio: var(--ciselne-ar, 4 / 3);
    border-radius: 1.5em;
  }
  .m-img-wrap.carousel.ciselne .slide-img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    backface-visibility: hidden;
    transform: translateZ(0);
    border-radius: inherit;
  }

  /* prechody (ponechané) */
  .xfade-zoom-enter-active,
  .xfade-zoom-leave-active {
    position: absolute;
    inset: 0;
    transition: opacity 1800ms cubic-bezier(0.22, 0.61, 0.36, 1),
      transform 1800ms cubic-bezier(0.22, 0.61, 0.36, 1);
    will-change: opacity, transform;
  }
  .xfade-zoom-enter-from {
    opacity: 0;
    transform: scale(1.02);
  }
  .xfade-zoom-leave-to {
    opacity: 0;
    transform: scale(0.985);
  }
  .xfade-zoom-leave-active {
    transition-delay: 60ms;
  }

  /* CTA vzhľad – ponechané */
  .zistit-viac {
    .button,
    .cta-btn,
    .cta-over-img {
      font-family: "Adumu", system-ui, -apple-system, sans-serif;
      font-size: 2.5vw;
      padding: 0.55em 1em;
      border-radius: 1.8em;
      box-shadow: 3px 3px 3px rgba(0, 0, 0, 0.25);
      border: none;
      cursor: pointer;
      transition: transform 0.15s ease;
      white-space: nowrap;
      -webkit-text-fill-color: #000;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: #fef35a;
      color: #000;
      text-decoration: none;
    }
    .button:active,
    .cta-btn:active,
    .cta-over-img:active {
      transform: scale(0.98);
    }
    .cta-over-img {
      position: static !important;
      margin: 2em auto 1.4em !important;
    }
    .cta-row {
      display: flex;
      flex-wrap: nowrap;
      justify-content: center;
      align-items: center;
      gap: clamp(0.3em, 2vw, 0.6em);
      width: calc(100% - 10vw);
      margin: 0 auto;
    }
    .cta-row .button,
    .cta-row .cta-btn {
      min-width: 0;
      padding: clamp(0.45em, 1.8vw, 0.55em) clamp(0.8em, 3vw, 1em);
      font-size: 2.5vw;
    }
    section .button {
      margin: 1em !important;
    }
  }

  /* Reveal panel (mobile) */
  .reveal-panel {
    overflow: hidden;
    width: 100%;
    margin: 0 auto 0.8em;
  }
  .reveal-enter-from,
  .reveal-leave-to {
    opacity: 0;
    transform: translateY(-4px);
    max-height: 0;
  }
  .reveal-enter-active,
  .reveal-leave-active {
    transition: opacity 380ms ease, transform 380ms ease, max-height 420ms ease;
  }
  .reveal-enter-to,
  .reveal-leave-from {
    max-height: 1200px;
  }
}

/* Sekcia výhod – ponechané */
.benefits-heading {
  text-align: center;
  margin: 1.2em 0 0.6em;
}
.numeric-benefits {
  width: 100%;
  box-sizing: border-box;
  padding: 0 2vw;
  margin: 0 auto;
}
.benefit {
  margin: 0.9em 0;
}
.benefit-title {
  margin: 0 0 0.25em;
  text-align: center;
  font-family: Bahnshrift;
  font-weight: 550;
  font-size: 3vw;
  line-height: 1.25;
  color: #000;
}
.benefit-desc {
  margin-top: 0.2em !important;
}
.reveal-anchor {
  scroll-margin-top: 72px;
}
.m-panel > .scroll::-webkit-scrollbar {
  display: none !important;
}

/* Registrácia – mobile (ponechané) */
.zistit-viac .m-panel .cta-on-image {
  position: relative;
  width: calc(100% - 10vw);
  margin: 0.8em auto 0;
  padding-bottom: 0;
}
.zistit-viac .m-panel .cta-on-image .m-img-wrap.carousel {
  aspect-ratio: auto !important;
  margin: 0 !important;
}
.zistit-viac .m-panel .cta-on-image .m-img-wrap.carousel .slide-img {
  position: static !important;
  display: block;
  width: 100% !important;
  height: auto !important;
  object-fit: contain !important;
  object-position: center !important;
}
.zistit-viac .m-panel .cta-on-image .cta-image {
  position: absolute;
  left: 0;
  right: 0;
  bottom: 0;
  display: flex;
  justify-content: center;
  pointer-events: none;
}
.zistit-viac .m-panel .cta-on-image .cta-image .cta-over-img {
  position: relative !important;
  bottom: clamp(3.6em, 2.6vw, 1.2em) !important;
  margin: 0 !important;
  pointer-events: auto;
}
.zistit-viac .m-panel .cta-on-image .m-img-wrap {
  width: 100% !important;
  margin: 0 auto !important;
  display: block;
}
.zistit-viac .m-panel .cta-on-image .m-img-wrap .slide-img {
  width: 100% !important;
  height: auto !important;
  object-fit: contain !important;
  object-position: center !important;
}
.cta-image .button {
  font-size: 2.5vw;
  background: #8ec84e;
}

/* ===== max-width: 450px ===== */
@media only screen and (max-width: 450px) {
  .mobile .buttons {
    flex-direction: column;
    margin-top: 0;
  }
  .mobile .text {
    padding: 0 7%;
    font-size: 3.2vw;
  }
  .nadpis {
    font-size: 1.4em;
    padding: 0 4%;
  }
  .big-text {
    font-size: 4.2vw;
  }

  .m-img-wrap img {
    margin-top: 0 !important;
    width: 100% !important;
  }
  img {
    margin-top: 1em;
    width: 80%;
  }

  .buttons .button {
    font-size: 1em;
  }
  .center {
    padding-bottom: 1em;
  }

  .cta-over-img {
    bottom: 1em;
    font-size: clamp(12px, 3.6vw, 16px);
    padding: clamp(7px, 1.6vw, 10px) clamp(14px, 3.6vw, 20px);
  }
  .cta-image .button {
    font-size: 3vw;
    background: #8ec84e;
  }
  .zistit-viac .m-panel .cta-on-image {
    width: calc(100% - 8vw);
  }
}

/* drobné úpravy */
@media (max-width: 380px) {
  .reveal-anchor {
    scroll-margin-top: 64px;
  }
}

/* HeliChase – mobile */
@media only screen and (max-width: 750px) {
  .m-img-wrap.carousel.helichase {
    width: calc(100% - 10vw);
    margin: 0.8em auto 0;
    border-radius: 1em;
    background: #efefef;
    position: relative;
    overflow: hidden;
    aspect-ratio: 666 / 251;
    --hc-scale: 0.8;
  }
  .m-img-wrap.carousel.helichase .slide-img {
    position: absolute;
    inset: 0;
    margin: auto;
    width: auto;
    height: 100%;
    max-width: calc(var(--hc-scale) * 100%);
    max-height: 100%;
    object-fit: contain;
    object-position: center;
    backface-visibility: hidden;
    transform: translateZ(0);
  }
  .edge-swipe-enter-active,
  .edge-swipe-leave-active {
    transition: opacity 700ms cubic-bezier(0.22, 0.61, 0.36, 1);
  }
  .edge-swipe-enter-from {
    opacity: 0;
  }
  .edge-swipe-enter-to {
    opacity: 1;
  }
  .edge-swipe-leave-from {
    opacity: 1;
  }
  .edge-swipe-leave-to {
    opacity: 0;
  }
}

/* Text „1 minúta…“ – ponechané */
.time {
  display: block;
  margin: 0.35em auto 0;
  font-weight: 700;
  font-size: clamp(18px, 3.5vw, 28px);
  line-height: 1.15;
  text-align: center;
  white-space: nowrap;
  letter-spacing: 0.01em;
  padding-bottom: 0 !important;
}
@media (max-width: 450px) {
  .time {
    font-size: 4vw !important;
  }
}
.last-text {
  font-size: 3vw;
  margin-bottom: 0;
  font-weight: 550;
  padding: 0;
}
/* ---------- TYPO – rovnaká logika ako na domovskej stránke ---------- */
/* DESKTOP ≥1100px */
.computer h5 {
  /* tvoje 1.6vw, ale nikdy nie menej ako 18px ani viac ako 32px */
  font-size: clamp(18px, 1.6vw, 32px);
  line-height: 1.15;
}
.computer .text {
  /* tvoje 1.2vw, poistka 15–24px */
  font-size: clamp(15px, 1.2vw, 24px);
  line-height: 1.5;
}

/* TABLET 750–1099px */
@media (max-width: 1099px) {
  .computer h5 {
    /* tvoje 2.2vw, poistka 18–30px */
    font-size: clamp(18px, 2.2vw, 30px);
  }
  .computer .text {
    /* tvoje 2vw, poistka 15–22px */
    font-size: clamp(15px, 2vw, 22px);
  }
}

/* MOBIL ≤749px */
@media (max-width: 749px) {
  .computer h5 {
    /* tvoje 4vw, poistka 18–28px */
    font-size: clamp(18px, 4vw, 28px);
  }
  .computer .text {
    /* tvoje 2.6vw, poistka 14–20px */
    font-size: clamp(14px, 2.6vw, 20px);
  }
}
@media (max-width: 1050px) {
  .computer .big-text {
    font-size: clamp(18px, 2.5vw, 28px);
  }
}

/* CTA nech sa tiež správa rovnako ako inde */
.computer .button,
.computer .cta-btn,
.computer .cta-over-img {
  /* mierne fluid – sadne k textom vo všetkých breakpointoch */
  font-size: clamp(14px, 1.1vw, 18px);
  padding: clamp(10px, 0.7vw, 14px) clamp(16px, 1.2vw, 26px);
}
</style>
