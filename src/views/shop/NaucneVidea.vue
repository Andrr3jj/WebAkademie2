<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <the-headline />

      <div class="line horizontal w-80"></div>

      <p class="text center w-80 m0">
        Predstavujeme Vám naše <b> revolučné náučne videá.</b> Jedná sa online
        výučbu hry na heligónku, kde sa učíte hrať s ľahkosťou a zábavou! Naše
        kurzy ponúkajú jedinečný spôsob učenia, ktorý zjednodušuje proces
        osvojovania si hry na heligónku a umožňuje Vám dosiahnuť hudobné úspechy
        rýchlo a efektívne.
      </p>

      <div class="jedinecna w-90">
        <img
          src="@/assets/images/gallery/JurajNaucneVidea.png"
          alt="Juraj Kvočka hrá na heligonke"
        />
        <p class="big-text bold" data-tour="naucne-heading">
          JEDINEČNÁ METÓDA UČENIA
        </p>
      </div>

      <div class="text center w-75">
        Naša jedinečná metóda učenia sa zameriava na podrobné rozoberanie každej
        piesne, krok za krokom, aby ste sa mohli stať skutočnými odborníkmi v
        hre na heligónku. S našimi podrobnými videami môžete cítiť pôvab
        hudobného sveta nielen v melodickej časti, ale aj v basových pasážach a
        postupoch.
      </div>

      <div class="obsah">
        <ul>
          <li class="text bold">DETAILNÝ ROZBOR KAŽDEJ PIESNE</li>
          <p class="text">
            Pri každej pesničke sa venujeme jej komplexnému rozboru, začíname
            základnými melódiami a postupujeme až k pokročilým technikám. Naše
            videá Vám umožňujú sledovať každý krok procesu učenia, čo Vám dáva
            jasný obraz toho, ako dosiahnuť skutočný hudobný úspech.
          </p>
          <li class="text bold">UČENIE SA Z RÔZNYCH UHLOV</li>
          <p class="text">
            V našich videách sa nezameriavame len na melodickú časť piesne.
            Okrem toho sa venujeme aj basovým pasážam a harmonickým postupom, čo
            Vám dáva komplexný pohľad na hudobnú štruktúru. To Vám umožňuje
            porozumieť piesni ako celku a lepšie ju interpretovať.
          </p>
        </ul>
        <img src="@/assets/images/gallery/hmatnik.png" alt="Hmatník" />
      </div>
      <div class="obsah">
        <ul>
          <li class="text bold">OPAKOVANIE A ZÍSKAVANIE ISTOTY</li>
          <p class="text">
            Jedným z kľúčových prvkov našej metódy je opakovanie. Naše videá Vám
            umožňujú sledovať každú lekciu neobmedzene, čo Vám dáva možnosť
            pustiť si rôzne pasáže viackrát, zastaviť ich a vrátiť sa k nim
            podľa svojej potreby. To Vám umožňuje osobný rast a rozvoj vo Vašom
            vlastnom tempe. Taktiaž týmto spôsobom získavate istotu vo svojich
            schopnostiach a pripravujete sa na ďalšie hudobné výzvy.
          </p>
        </ul>
      </div>
      <div class="obsah">
        <ul>
          <li class="text bold flexibilita">FLEXIBILITA</li>
          <p class="text">
            Nakoniec, naša metóda učenia je navrhnutá tak, aby bola flexibilná a
            prístupná pre každého. S našimi online kurzami sa môžete učiť
            kedykoľvek a kdekoľvek, čo Vám umožňuje prispôsobiť si učenie sa
            podľa Vášho vlastného harmonogramu a tempa.
          </p>
        </ul>
        <img
          src="@/assets/images/gallery/AndrejNaucneVidea.png"
          alt="Andrej hrajúci na heligonkew"
          class="andrej"
        />
      </div>

      <div class="big-text bold center predplatne">
        PREDPLATNÉ A JEHO VÝHODY
      </div>

      <p class="text semi-bold center">
        Získajte prístup ku všetkým našim online náučným videám zakúpením
        predplatného.
      </p>

      <p class="text center w-80">
        S predplatným budete mať neobmedzený prístup ku všetkým našim
        vzdelávacím videám počas jeho platnosti. Okrem toho, Vás zakúpenie
        predplatného oprávňuje vstúpiť do uzavretej skupiny na Facebooku, kde
        môžete zdieľať svoje úspechy, otázky a postupy s ostatnými študentmi. V
        tejto skupine taktiež komunikujeme so študentmi a pridávame rôzne
        dodatočné veci, ktoré Vám pomôžu pri Vašej hudobnej výuke.
      </p>

      <div class="buttons">
        <router-link
          to="/ucebna/clenstvo"
          class="button"
          data-tour="cta-subscription"
        >
          <p>Zakúpiť predplatné</p>
        </router-link>
        <router-link
          to="/ucebna/zadarmo-video"
          class="button"
          data-tour="cta-free"
        >
          <p>Pozrieť zadarmo</p>
        </router-link>
      </div>
    </div>
  </section>

  <div class="mobile">
    <section>
      <div class="scroll">
        <the-headline />

        <div class="line horizontal w-80"></div>

        <p class="text center w-80 m0">
          Predstavujeme Vám naše <b> revolučné náučne videá.</b> Jedná sa online
          výučbu hry na heligónku, kde sa učíte hrať s ľahkosťou a zábavou! Naše
          kurzy ponúkajú jedinečný spôsob učenia, ktorý zjednodušuje proces
          osvojovania si hry na heligónku a umožňuje vám dosiahnuť hudobné
          úspechy rýchlo a efektívne.
        </p>

        <div class="jedinecna w-90">
          <img
            src="@/assets/images/gallery/JurajNaucneVidea.png"
            alt="Juraj Kvočka hrá na heligonke"
          />
          <p class="big-text bold" data-tour="naucne-heading">
            JEDINEČNÁ METÓDA UČENIA
          </p>
        </div>

        <div class="text center w-75">
          Naša jedinečná metóda učenia sa zameriava na podrobné rozoberanie
          každej piesne, krok za krokom, aby ste sa mohli stať skutočnými
          odborníkmi v hre na heligónku. S našimi podrobnými videami môžete
          cítiť pôvab hudobného sveta nielen v melodickej časti, ale aj v
          basových pasážach a postupoch.
        </div>

        <div class="obsah">
          <ul>
            <li class="text bold">DETAILNÝ ROZBOR KAŽDEJ PIESNE</li>
            <p class="text">
              Pri každej pesničke sa venujeme jej komplexnému rozboru, začíname
              základnými melódiami a postupujeme až k pokročilým technikám. Naše
              videá Vám umožňujú sledovať každý krok procesu učenia, čo Vám dáva
              jasný obraz toho, ako dosiahnuť skutočný hudobný úspech.
            </p>
            <li class="text bold">UČENIE SA Z RÔZNYCH UHLOV</li>
            <p class="text">
              V našich videách sa nezameriavame len na melodickú časť piesne.
              Okrem toho sa venujeme aj basovým pasážam a harmonickým postupom,
              čo Vám dáva komplexný pohľad na hudobnú štruktúru. To Vám umožňuje
              porozumieť piesni ako celku a lepšie ju interpretovať.
            </p>
          </ul>
          <img src="@/assets/images/gallery/hmatnik.png" alt="Hmatník" />
        </div>
        <div class="obsah">
          <ul>
            <li class="text bold">OPAKOVANIE A ZÍSKAVANIE ISTOTY</li>
            <p class="text">
              Jedným z kľúčových prvkov našej metódy je opakovanie. Naše videá
              Vám umožňujú sledovať každú lekciu neobmedzene, čo Vám dáva
              možnosť pustiť si rôzne pasáže viackrát, zastaviť ich a vrátiť sa
              k nim podľa svojej potreby. To Vám umožňuje osobný rast a rozvoj
              vo Vašom vlastnom tempe. Taktiaž týmto spôsobom získavate istotu
              vo svojich schopnostiach a pripravujete sa na ďalšie hudobné
              výzvy.
            </p>
          </ul>
        </div>
        <div class="obsah">
          <ul>
            <li class="text bold flexibilita">FLEXIBILITA</li>
            <p class="text">
              Nakoniec, naša metóda učenia je navrhnutá tak, aby bola flexibilná
              a prístupná pre každého. S našimi online kurzami sa môžete učiť
              kedykoľvek a kdekoľvek, čo Vám umožňuje prispôsobiť si učenie sa
              podľa Vášho vlastného harmonogramu a tempa.
            </p>
          </ul>
          <img
            src="@/assets/images/gallery/AndrejNaucneVidea.png"
            alt="Andrej hrajúci na heligonkew"
            class="andrej"
          />
        </div>

        <div class="big-text bold center predplatne">
          PREDPLATNÉ A JEHO VÝHODY
        </div>

        <p class="text semi-bold center">
          Získajte prístup ku všetkým našim online náučným videám zakúpením
          predplatného.
        </p>

        <p class="text center w-80">
          S predplatným budete mať neobmedzený prístup ku všetkým našim
          vzdelávacím videám počas jeho platnosti. Okrem toho Vás zakúpenie
          predplatného oprávňuje vstúpiť do uzavretej skupiny na Facebooku, kde
          môžete zdieľať svoje úspechy, otázky a postupy s ostatnými študentmi.
          V tejto skupine taktiež komunikujeme so študentmi a pridávame rôzne
          dodatočné veci, ktoré Vám pomôžu pri Vašej hudobnej výuke.
        </p>

        <div class="buttons" data-tour="cta-buttons">
          <router-link
            to="/ucebna/clenstvo"
            class="button"
            data-tour="cta-subscription"
          >
            <p>Zakúpiť predplatné</p>
          </router-link>
          <router-link
            to="/ucebna/zadarmo-video"
            class="button"
            data-tour="cta-free"
          >
            <p>Pozrieť zadarmo</p>
          </router-link>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { onMounted } from "vue";
import TheHeadline from "@/components/Menu/TheHeadline.vue";

onMounted(() => {
  window.scrollTo(0, 0);
});
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.text {
  font-size: 1.4em;
  margin-top: 1em;
  margin-bottom: 1em;
  text-align: left;
}

.semi-bold {
  font-weight: 650;
  width: 60%;
  margin: 1em auto;
}

.predplatne {
  margin-top: 2em;
}

.center {
  text-align: center;
}
.big-text {
  font-size: 2.5em;
}

.line {
  margin-bottom: 3.5em;
}
.bold {
  font-weight: 800;
}

/* Pridané vlastné štýly pre bodky v zoznamoch */
ul {
  list-style-type: disc; /* Bodky */
}

li {
  margin-left: 0.9em; /* Vzdialenosť medzi bodkou a textom */
}

.m0 {
  margin-bottom: 0;
}

.obsah {
  width: 85%;
  margin-left: 10%;
  display: flex;
  align-items: center;
  gap: 5%;

  img {
    width: 14em;
  }
}

.jedinecna {
  display: flex;
  align-items: center;

  img {
    width: 30vw;
    max-width: 20em;
    filter: drop-shadow(5px 5px 3px rgba(0, 0, 0, 0.35));
  }

  p {
    width: auto;
    margin-right: auto;
    margin-left: 8%;
  }
}

.andrej {
  width: 30vw;
  max-width: 15em;
  transform: scale(1.3);
  filter: drop-shadow(5px 5px 3px rgba(0, 0, 0, 0.35));
}

.buttons {
  gap: 3%;
  margin: 3em 0 3em;
  display: flex;
  align-items: center;
  justify-content: space-evenly;

  .button {
    padding: 0.8em 2em;
    font-size: 1.1em;
  }
}

@media only screen and (max-width: 1000px) {
  .text {
    font-size: 1.4em;
  }

  .big-text {
    font-size: 1.8em;
  }
}

.mobile {
  .w-80:not(.line),
  .w-70,
  .w-75 {
    width: 100% !important;
    padding: 0 7%;
    box-sizing: border-box;
  }
  .jedinecna {
    flex-direction: column;

    img {
      width: 80%;
    }

    p {
      margin-right: auto;
      margin-left: auto;
      margin-top: 1em;
    }
  }

  .obsah {
    width: 100%;
    margin-left: auto;
    margin-right: auto;
    box-sizing: border-box;
    padding: 0 5%;
    flex-direction: column;

    img {
      max-width: 14em;
      width: 80%;
    }

    .andrej {
      transform: none;
      max-width: 20em;
    }

    .predplatne {
      margin-top: 1em;
    }
  }

  .semi-bold {
    width: 80%;
  }

  .buttons {
    margin-bottom: 0;
  }

  .scroll {
    margin-bottom: 0;
  }

  .predplatne {
    margin-top: 1em;
  }

  // section {
  //   margin-bottom: 0;
  // }
}
@media only screen and (max-width: 750px) {
  .mobile {
    padding-bottom: 7em;
  }
}
@media only screen and (max-width: 500px) {
  .mobile {
    .buttons {
      flex-direction: column;
      margin-top: 0;
    }

    .text {
      font-size: 1.2em;
    }

    .big-text {
      font-size: 1.4em;
    }

    .buttons .button {
      font-size: 1em;
    }
  }
}
</style>
