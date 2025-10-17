<template>
  <!-- Základné nadpisy podľa názvu routy -->
  <h1
    v-if="
      $route.name != 'Moje piesne' &&
      $route.name != 'Heligonková Akadémia' &&
      $route.name != 'Heličas úlohy' &&
      $route.name != 'Heličas fungovanie' &&
      $route.name != 'Zisti hodnotu poukážky'
    "
  >
    Heligónková Akadémia
  </h1>

  <h1 v-if="$route.name == 'Heligonková Akadémia'" class="no-default">
    {{ $t("home.hero.title") }}
  </h1>
  <h1 v-if="$route.name == 'Heličas úlohy'" class="no-default">
    HeliČas úlohy
  </h1>
  <h1 v-if="$route.name == 'Moje piesne'">Zoznam piesní</h1>
  <h1 v-if="$route.name == 'Heličas fungovanie'" class="no-default">
    Systém Heli Čas
  </h1>
  <h1 v-if="$route.name == 'Zisti hodnotu poukážky'" class="no-default">
    Darčekové poukážky
  </h1>

  <!-- Podnadpis -->
  <h5 v-if="$route.name == 'Heligonková Akadémia'" class="podnadpis no-default">
    {{ $t("home.hero.subtitle") }}
  </h5>
  <h5 v-else class="podnadpis">
    {{ podnadpis }}
  </h5>

  <!-- Popis zobrazíme iba ak je povolený -->
  <p v-if="showBody && popis" class="popis">
    {{ popis }}
  </p>
</template>

<script>
export default {
  props: {
    showBody: {
      type: Boolean,
      default: true, // určuje, či sa má zobraziť <p class="popis">
    },
  },
  data() {
    return {
      podnadpis:
        "Nauč sa hrať na heligónke s online kurzami od základov po obľúbené piesne, vlastným tempom z pohodlia domova.",
      popis:
        "Objav čaro heligónky s našimi kurzami heligónky online, náučnými videami a číselnými zápismi. Pre začiatočníkov aj pokročilých pesničky krok za krokom, od melódie cez mech až po basy. Jednoducho, zábavne a kedykoľvek, keď máš čas.",
    };
  },
  mounted() {
    switch (this.$route.name) {
      case "Heligonková Akadémia":
        this.podnadpis =
          "Nauč sa hrať na heligónke s online kurzami od základov po obľúbené piesne, vlastným tempom z pohodlia domova.";
        this.popis =
          "Objav čaro heligónky s našimi kurzami heligónky online, náučnými videami a číselnými zápismi. Pre začiatočníkov aj pokročilých pesničky krok za krokom, od melódie cez mech až po basy. Jednoducho, zábavne a kedykoľvek, keď máš čas.";
        if (!this.showBody) this.popis = null;
        break;

      case "O nás":
        this.podnadpis = "Vaše vzdelávacie úspechy sú našim cieľom!";
        this.popis = null;
        break;

      case "Zabudnuté heslo":
        this.podnadpis = "Zabudnuté prihlasovacie údaje";
        this.popis = null;
        break;

      case "Prihlásenie":
      case "Registrácia":
        this.podnadpis = "Otvor si dvere k hudobnému svetu s heligónkou!";
        this.popis = null;
        break;

      case "Pomoc":
        this.podnadpis = "Potrebujete pomoc alebo máte otázky?";
        this.popis = null;
        break;

      case "Košík":
        this.podnadpis =
          "Váš nákupný košík je krokom bližšie k odhaleniu vzdelávacieho a hudobného pokladu";
        this.popis = null;
        break;

      case "Pokladňa":
        this.podnadpis = "Platby a záverečné kroky vašej objednávky";
        this.popis = null;
        break;

      case "Časté otázky":
        this.podnadpis = "Objavte odpovede na časté otázky";
        this.popis = null;
        break;

      case "Miesta výučby":
        this.podnadpis =
          "Prezenčné lekcie s našimi učiteľmi vo Vašej blízkosti";
        this.popis = null;
        break;

      case "Moje piesne":
        this.podnadpis = "Piesne na učenie dostupné po zakúpení!";
        this.popis = null;
        break;

      case "Vytvorenie hesla":
        this.podnadpis = "Vytvorte si svoje nové heslo";
        this.popis = null;
        break;

      case "Pripravujeme":
        this.podnadpis = "Práve sa pripravuje";
        this.popis = null;
        break;

      case "NotFound":
        this.podnadpis = "Oops! Niečo sa pokazilo.";
        this.popis = null;
        break;

      case "Zabudnuté heslo úspešné":
        this.podnadpis = "Obnoviť heslo úspešné";
        this.popis = null;
        break;

      case "Potrebné prihlásenie":
        this.podnadpis =
          "Obsah tejto stránky je prístupný len prihláseným užívateľom";
        this.popis = null;
        break;

      case "Úspešný nákup":
        this.podnadpis = "Ďakujeme za váš nákup!";
        this.popis = null;
        break;

      case "Neúspešný nákup":
        this.podnadpis = "Platba bola zamietnutá";
        this.popis = null;
        break;

      case "Úspešné odoslanie formuláru":
        this.podnadpis = "Správa bolo odoslaná";
        this.popis = null;
        break;

      case "Náučné videá":
        this.podnadpis = "Začnite hrať na heligónke pomocou náučných videí";
        this.popis = null;
        break;

      case "Heličas úlohy":
        this.podnadpis = "Splňte si ich a získajte bonusové body!";
        this.popis = null;
        break;

      case "Heličas fungovanie":
        this.podnadpis = "Všetko, čo potrebuješ vedieť!";
        this.popis = null;
        break;

      case "Zisti hodnotu poukážky":
        this.podnadpis = "Zadajte svoj kód a využite svoju príležitosť naplno!";
        this.popis = null;
        break;

      default:
        break;
    }
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

h1,
h5,
p {
  text-align: center;
}

h1 {
  color: #fef35a;
  text-shadow: 5px 5px 7px rgba(0, 0, 0, 0.25);
  font-size: 6.1vw;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.03em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0.1em 0.05em 0.1em;
  margin: 0;
}

h5 {
  color: #001;
  font-size: 2.3vw;
  font-weight: 550;
  line-height: 115%;
  margin: 0.6em 2em 0.5em;
}

p {
  color: #575757;
  font-size: 1.2vw;
  font-weight: 400;
  line-height: 130%;
  margin: 0.2em 0.5em;
  width: 90%;
}

@media screen and (max-width: 950px) {
  p {
    width: 80%;
    font-size: 2vw;
  }
}

@media only screen and (max-width: 750px) {
  h1:not(.no-default) {
    font-size: 12vw;
  }

  h5:not(.no-default) {
    margin: 0em 1.2em 0.5em 1.2em;
    font-size: 4vw;
  }
  p {
    width: 46%;
    margin: 1em 3em 0;
    text-align: left;
    font-size: 2.6vw;
  }
}

@media screen and (max-width: 450px) {
  .mobile section p {
    font-size: 3.2vw;
    text-align: left;
    margin: 1em 5em 0 1.5em;
    width: 57%;
  }
}
</style>
