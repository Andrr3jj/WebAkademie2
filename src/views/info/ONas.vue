<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <!-- <p class="popis">
        Objavte, čo nás inšpiruje a robí našu spoločnosť jedinečnou.
      </p> -->

      <div class="line horizontal w-80"></div>

      <div class="obsah">
        <div class="box">
          <div class="box-item left">
            <p class="text">
              Voláme sa <strong>Juraj </strong> a <strong> Andrej</strong> a sme
              učitelia heligónky. Na základe obrovského záujmu sme v roku
              <strong> 2020</strong> založili HELIGÓNKOVÚ AKADÉMIÚ, ktorá je v
              naších končinách tak trochu unikát.
            </p>
            <p class="text">
              Ide o projekt, v ktorom sa snažíme učiť ľudí hrať na heligónke
              rôzne piesne a žánre novým spôsobom výčby. Ak sa cheš aj ty naučiť
              hrať na heligónku tak si na správnom mieste.
              <strong> U nás naučíme hrať každého.</strong>
            </p>
          </div>
          <div class="box-item right">
            <img
              class="uvodny-obrazok"
              src="@/assets/images/gallery/JurajAAndrejONas.png"
              alt="Juraj a Andrej s heligonkami"
            />
          </div>
        </div>
        <h3 class="Bahnshrift">VŠETKO NA JEDNOM MIESTE!</h3>

        <p class="text">
          Sme oddaní tomu, aby sme priniesli radosť z hry každému, bez ohľadu na
          <strong> vek </strong> či <strong> talent </strong>. Naším cieľom je
          vytvárať prostredie, kde sa učenie stáva zážitkom, a to nielen
          prostredníctvom tradičného prezenčného vzdelávania, ale aj inovatívnou
          formou <strong> dištančného vzdelávania. </strong>
        </p>
        <p class="text">
          Preto ako prví na slovensku prinášame
          <strong> ONLINE školu heligónky, </strong> kde máte na jednom mieste
          všetko, čo potrebujete k výučbe, kdekoľvek sa nachádzate. Na našej
          stránke nájdete rôzne piesňe od ľudových až po moderné skladby. Piesne
          sú zapísane už tradičnou formou číselného zápisu. U nás môžte hrať bez
          ohľadu nato, či ste <strong> začiatočník </strong> alebo už
          <strong>pokročilý </strong>heligonkár. Na našej platforme si každý
          nájde to, čo potrebuje pre svoje dalšie napredovanie v hre na
          heligónku.
        </p>

        <p class="text">
          Veríme, že každý je schopný objaviť svoj talent a vášeň
          prostredníctvom nášho prístupu k výučbe.
        </p>

        <h3 class="Bahnshrift">ONLINE NÁUČNÉ KURZY</h3>

        <p class="text">
          S našimi náučnými videami sa môžete naučiť hrať kdekoľvek, v
          ktorejkoľvek časti sveta. Stačí mať pri sebe
          <strong> heligónku, mobil </strong> alebo <strong>počítač</strong> a
          samozrejme, pripojenie na <strong>internet.</strong> Týmto novým
          spôsobom výučby otvárame dvere ku skvelému hudobnému zážitku pre
          každého, kto má chuť objaviť krásu hrania na heligónke.
        </p>
        <p class="text">
          Stači sa iba registrovať na našej stránke a otvorí sa vám možnosť
          začať hrať na heligónku podlľa naších
          <strong> ONLINE náučnych videí.</strong>
        </p>

        <h4 class="Bahnshrift">
          Je to veľmi jednoduché! Tak neváhaj a pridaj sa k nám.
        </h4>

        <p class="text-small">
          Našu tvorbu môžte vidieť na rôznych sociálnych sieťach ako Instagram,
          Facebook, TikTok.
        </p>

        <div class="link">
          <a
            href="https://www.instagram.com/heligonkova_akademia/"
            target="_blank"
          >
            <img src="@/assets/images/icons/instagram.svg" />
          </a>
          <a
            href="https://www.facebook.com/heligonkovaakademia/"
            target="_blank"
          >
            <img src="@/assets/images/icons/facebook.svg" />
          </a>
          <a href="https://www.tiktok.com/@heligonkovaakademia" target="_blank">
            <img src="@/assets/images/icons/tiktok.svg" />
          </a>
        </div>

        <div class="line horizontal more-margin"></div>

        <h4 class="Bahnshrift">
          Naša jednota v tíme je tým, čo robí naše vzdelávanie výnimočným!
        </h4>

        <p class="text center">
          Toto sme my, štyria učitelia, ktorých spojila vášeň pre heligónku.
        </p>
      </div>

      <div class="ucitelia">
        <div class="top riadok">
          <div class="cast">
            <img src="@/assets/images/gallery/JurajONas.png" alt="" />
            <router-link to="/o-nas/juraj-kvocka" class="button">
              <p>Zistiť viac</p>
            </router-link>
          </div>
          <div class="cast">
            <img src="@/assets/images/gallery/AndrejONas.png" alt="" />
            <router-link to="/o-nas/andrej-trnka" class="button">
              <p>Zistiť viac</p>
            </router-link>
          </div>
        </div>
        <div class="bottom riadok">
          <div class="cast">
            <img src="@/assets/images/gallery/LiborONas.png" alt="" />
            <router-link to="/o-nas/libor-hlinik" class="button">
              <p>Zistiť viac</p>
            </router-link>
          </div>
          <div class="cast">
            <p class="text">
              Ak máte zanietenie pre <strong> heligónku</strong> a
              <strong> schopnosti vyučovať, </strong>ste ten správny človek pre
              náš tím. Pripojte sa k nám a pomôžte nám šíriť radosť z hrania na
              heligónke. Spoločne môžeme vytvoriť inšpiratívne učebné prostredie
              pre našich študentov.
            </p>

            <router-link to="/pomoc" class="button">
              <p>Mám záujem</p>
            </router-link>
          </div>
          <div class="cast">
            <img src="@/assets/images/gallery/MatejONas.png" alt="" />
            <router-link to="/o-nas/matej-kondela" class="button">
              <p>Zistiť viac</p>
            </router-link>
          </div>
        </div>
      </div>

      <p class="text center"><strong>Kontakt: </strong> +421 915 371 474</p>
      <p class="text center">
        <strong>Email: </strong> heligonkovaakademia@gmail.com
      </p>
    </div>
  </section>

  <div class="mobile">
    <section>
      <div class="scroll">
        <TheHeadline></TheHeadline>

        <!-- <p class="popis">
        Objavte, čo nás inšpiruje a robí našu spoločnosť jedinečnou.
      </p> -->

        <div class="line horizontal w-80"></div>

        <div class="obsah">
          <div class="box">
            <div class="box-item left">
              <p class="text">
                Voláme sa <strong>Juraj </strong> a <strong> Andrej</strong> a
                sme učitelia heligónky. Na základe obrovského záujmu sme v roku
                <strong> 2020</strong> založili HELIGÓNKOVÚ AKADÉMIÚ, ktorá je v
                naších končinách tak trochu unikát.
              </p>
              <p class="text">
                Ide o projekt, v ktorom sa snažíme učiť ľudí hrať na heligónku
                rôzne piesne a žánre novým spôsobom výčby. Ak sa cheš aj ty
                naučiť hrať na heligónku tak si na správnom mieste.
                <strong> U nás naučíme hrať každého.</strong>
              </p>
            </div>
            <div class="box-item right">
              <img
                class="uvodny-obrazok"
                src="@/assets/images/gallery/JurajAAndrejONas.png"
                alt="Juraj a Andrej s heligonkami"
              />
            </div>
          </div>
          <h3 class="Bahnshrift">VŠETKO NA JEDNOM MIESTE!</h3>

          <p class="text">
            Sme oddaní tomu, aby sme priniesli radosť z hry každému, bez ohľadu
            na
            <strong> vek </strong> či <strong> talent </strong>. Naším cieľom je
            vytvárať prostredie, kde sa učenie stáva zážitkom, a to nielen
            prostredníctvom tradičného prezenčného vzdelávania, ale aj
            inovatívnou formou <strong> dištančného vzdelávania. </strong>
          </p>
          <p class="text">
            Preto ako prví na slovensku prinášame
            <strong> ONLINE školu heligónky, </strong> kde máte na jednom mieste
            všetko, čo potrebujete k výučbe, kdekoľvek sa nachádzate. Na našej
            stránke nájdete rôzne piesňe od ľudových až po moderné skladby.
            Piesne sú zapísane už tradičnou formou číselného zápisu. U nás môžte
            hrať bez ohľadu nato, či ste <strong> začiatočník </strong> alebo už
            <strong>pokročilý </strong>heligonkár. Na našej platforme si každý
            nájde to, čo potrebuje pre svoje dalšie napredovanie v hre na
            heligónku.
          </p>

          <p class="text">
            Veríme, že každý je schopný objaviť svoj talent a vášeň
            prostredníctvom nášho prístupu k výučbe.
          </p>

          <h3 class="Bahnshrift">ONLINE NÁUČNÉ KURZY</h3>

          <p class="text">
            S našimi náučnými videami sa môžete naučiť hrať kdekoľvek, v
            ktorejkoľvek časti sveta. Stačí mať pri sebe
            <strong> heligónku, mobil </strong> alebo <strong>počítač</strong> a
            samozrejme, pripojenie na <strong>internet.</strong> Týmto novým
            spôsobom výučby otvárame dvere ku skvelému hudobnému zážitku pre
            každého, kto má chuť objaviť krásu hrania na heligónke.
          </p>
          <p class="text">
            Stači sa iba registrovať na našej stránke a otvorí sa Vám možnosť
            začať hrať na heligónku podlľa naších
            <strong> ONLINE náučnych videí.</strong>
          </p>

          <h4 class="Bahnshrift">
            Je to veľmi jednoduché! <br />
            Tak neváhaj a pridaj sa k nám.
          </h4>

          <p class="text-small">
            Našu tvorbu môžte vidieť na rôznych sociálnych sieťach ako
            Instagram, Facebook, TikTok.
          </p>

          <div class="link">
            <a
              href="https://www.instagram.com/heligonkova_akademia/"
              target="_blank"
            >
              <img src="@/assets/images/icons/instagram.svg" />
            </a>
            <a
              href="https://www.facebook.com/heligonkovaakademia/"
              target="_blank"
            >
              <img src="@/assets/images/icons/facebook.svg" />
            </a>
            <a
              href="https://www.tiktok.com/@heligonkovaakademia"
              target="_blank"
            >
              <img src="@/assets/images/icons/tiktok.svg" />
            </a>
          </div>

          <div class="line horizontal more-margin"></div>

          <h4 class="Bahnshrift">
            Naša jednota v tíme je tým, čo robí naše vzdelávanie výnimočným!
          </h4>

          <p class="text center">
            Toto sme my, štyria učitelia, ktorých spojila vášeň pre heligónku.
          </p>
        </div>

        <div class="ucitelia">
          <div class="top riadok">
            <div class="cast">
              <img src="@/assets/images/gallery/JurajONas.png" alt="" />
              <router-link to="/o-nas/juraj-kvocka" class="button">
                <p>Zistiť viac</p>
              </router-link>
            </div>
            <div class="cast">
              <img src="@/assets/images/gallery/AndrejONas.png" alt="" />
              <router-link to="/o-nas/andrej-trnka" class="button">
                <p>Zistiť viac</p>
              </router-link>
            </div>
          </div>
          <div class="bottom riadok">
            <div class="cast">
              <img src="@/assets/images/gallery/LiborONas.png" alt="" />
              <router-link to="/o-nas/libor-hlinik" class="button">
                <p>Zistiť viac</p>
              </router-link>
            </div>
            <div class="cast">
              <p class="text">
                Ak máte zanietenie pre <strong> heligónku</strong> a
                <strong> schopnosti vyučovať, </strong>ste ten správny človek
                pre náš tím. Pripojte sa k nám a pomôžte nám šíriť radosť z
                hrania na heligónke. Spoločne môžeme vytvoriť inšpiratívne
                učebné prostredie pre našich študentov.
              </p>

              <router-link to="/pomoc" class="button">
                <p>Mám záujem</p>
              </router-link>
            </div>
            <div class="cast">
              <img src="@/assets/images/gallery/MatejONas.png" alt="" />
              <router-link to="/o-nas/matej-kondela" class="button">
                <p>Zistiť viac</p>
              </router-link>
            </div>
          </div>
        </div>

        <p class="text center"><strong>Kontakt: </strong> +421 915 371 474</p>
        <p class="text center">
          <strong>Email: </strong> heligonkovaakademia@gmail.com
        </p>
      </div>
    </section>
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";

export default {
  mounted() {
    window.scrollTo(0, 0);
  },
  components: { TheHeadline },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.popis {
  font-size: 1.8vw;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.51563rem */
  margin: 0 auto 0.5em;
}

.obsah {
  padding: 0 5%;
}
.line.horizontal {
  width: 100%;
  height: 0.5rem;
  margin-bottom: 2.5em;
}
.uvodny-obrazok {
  filter: drop-shadow(5px 5px 3px rgba(0, 0, 0, 0.35));
  width: 100%;
  max-width: 23em;
  height: auto;
}

.box {
  gap: 3%;
}

.box-item {
  justify-content: center;
}

.text {
  font-size: 1.4em;
  margin: 1em 0;
  text-align: left;
}

.text-small {
  font-size: 1.45em;
}
.center {
  text-align: center;
}

.more-margin {
  margin: 2em 0 3em;
}

h3 {
  font-size: 2.5em;
  font-weight: 800;
}

h4 {
  font-size: 2em;
  font-weight: 600;
}

.left {
  flex: 3;
}

.link {
  display: flex;
  justify-content: center;
  gap: 3%;

  margin: 2rem auto;
}

.link img {
  width: 2.8em;
  height: 2.8em;
  flex-shrink: 0;
}

.link img:hover {
  cursor: pointer;
  transform: scale(1.1);
  transition-duration: 0.2s;
}

.ucitelia {
  display: flex;
  flex-direction: column;
  padding-bottom: 4em;

  img {
    width: 16vw;
    max-width: 25em;
  }

  .button {
    font-size: 1.3em;
    margin-left: -0.2em;
    padding: 0.4em 2em;
  }

  & > div.bottom.riadok > div:nth-child(1) > img {
    width: 15vw;
  }

  // & > div.bottom.riadok > div:nth-child(1) > a {
  //   margin-left: -1em;
  // }

  .bottom .cast:nth-child(2) {
    width: 40%;
    max-width: 23em;
    align-self: end;
  }

  .text {
    font-size: 1.5em;
    margin: 0 0 0.5em;
    text-align: center;
  }
}

div.ucitelia > div.bottom.riadok > div:nth-child(3) > img {
  transform: scale(1.05) translateY(-0.3em);
}

.riadok {
  display: flex;
  justify-content: space-evenly;
}

.top {
  justify-content: center;
  gap: 10%;
}

.cast {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
  gap: 0.5em;
}

@media only screen and (max-width: 1100px) {
  .ucitelia .text {
    font-size: 1.3em;
  }
}
@media only screen and (max-width: 950px) {
  // .ucitelia .text {
  //   font-size: 1.1em;
  // }

  // .ucitelia .button {
  //   font-size: 1.1em;
  // }

  // .ucitelia img {
  //   width: 16vw;
  // }

  .bottom .cast:nth-child(2) {
    order: 3;
    width: 80% !important;
    max-width: unset !important;
    margin: 3em auto;
  }

  .bottom {
    margin-top: 2em;
  }

  .riadok {
    flex-wrap: wrap;
  }
}
@media only screen and (max-width: 750px) {
  .mobile {
    .text {
      font-size: 1.2em;
      text-align: center;
    }

    .uvodny-obrazok {
      width: 60%;
      max-width: 23em;
    }

    h3 {
      font-size: 1.7em;
      margin: 1em 0 1.2em;
    }

    h4 {
      font-size: 4.5vw;
    }

    .link {
      font-size: 3vw;
      margin: 2em auto;
      gap: 5%;
    }

    .cast {
      width: 40%;
    }

    .ucitelia {
      margin-bottom: 0;
      padding-bottom: 0;
    }

    .ucitelia img {
      width: 100%;
      max-width: 18em;
    }

    .ucitelia .button {
      font-size: 0.9em;
      padding: 0.3em 0.9em;
      width: 70%;
      margin: 0 auto;
      justify-content: center;

      p {
        font-size: 3vw;
        margin: 0 auto;
      }
    }

    div.ucitelia > div.bottom.riadok > div:nth-child(3) > img {
      transform: scale(1.1) translateY(-0.9em);
    }
    // div.ucitelia > div.bottom.riadok > div:nth-child(1) > a {
    //   margin-left: 2.3vw;
    // }
    div.ucitelia > div.bottom.riadok > div:nth-child(1) > img {
      width: 30vw;
    }

    .bottom .cast:nth-child(2) .button {
      width: 40%;
    }
  }
  .mobile {
    padding-bottom: 7em;
  }
}

@media only screen and (max-width: 500px) {
  .mobile {
    .text-small {
      font-size: 1.25em;
      margin: 0.3em 0;
    }
    text {
      font-size: 1.2em;
    }

    .ucitelia .button p {
      font-size: 3vw;
    }

    div.ucitelia > div.bottom.riadok > div:nth-child(3) > img {
      transform: scale(1.12) translateY(-0.7em);
    }
    div.ucitelia > div.bottom.riadok > div:nth-child(1) > a {
      margin-left: 1.5vw;
    }
  }
}
</style>
