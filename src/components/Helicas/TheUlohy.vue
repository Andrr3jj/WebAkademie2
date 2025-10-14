<template>
  <the-headline />

  <div class="line horizontal"></div>

  <p class="text">
    Učenie sa na heligónke je odmena sama o sebe, ale teraz zaň dostaneš aj
    extra body! Plň úlohy, sleduj svoj progres a zbieraj bonusové odmeny.
  </p>

  <sekcia-vyzvi></sekcia-vyzvi>

  <p class="nadpis">
    Dokončil si všetky úlohy? Neboj sa, pripravujeme ďalšie výzvy!
  </p>
  <p class="text">
    Ak si už splnil všetky aktuálne úlohy, tvoja cesta tu nekončí! Heličas je o
    neustálom napredovaní a my pre teba pripravujeme ďalšie výzvy, vďaka ktorým
    môžeš pokračovať v zbieraní bodov a získavaní odmien.
  </p>
  <div class="box-item">
    <div class="texty">
      <p class="nadpis-text">Čo môžeš robiť ďalej?</p>
      <p class="text-sml">
        Pokračuj v učení a zbieraj body za čas strávený na stránke. Sleduj
        novinky - čoskoro pribudnú nové úlohy a odmeny! Inšpiruj sa v rebríčku
        TOP hráčov a posúvaj sa vyššie.
      </p>
    </div>
    <div class="image">
      <img src="@/assets/images/gallery/hodiny.png" alt="Čas" />
    </div>
  </div>
</template>

<script>
import TheHeadline from "../Menu/TheHeadline.vue";
import SekciaVyzvi from "./SekciaVyzvi.vue";
export default {
  mounted() {
    window.scrollTo(0, 0);
  },
  components: {
    TheHeadline,
    SekciaVyzvi,
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.text {
  font-size: 1.7em;
  margin: 0.8em auto;
  width: 80%;
}

.nadpis {
  font-size: 2.2em;
  margin: 1.8em 0.3em 0.7em;
  font-weight: 700;
}

.box-item {
  display: flex;
  flex-direction: row;
  align-items: center;
}

.texty {
  flex: 70;
}

.image {
  flex: 30;
  padding: 1em;
  box-sizing: border-box;
}

.image img {
  width: 10em;
}

.box-item {
  width: 80%;
  margin: auto;
}

.nadpis-text {
  font-size: 2em;
  font-weight: 700;
  text-align: left;
  margin: 0.4em auto 1em;
}

.text-sml {
  font-size: 1.4em;
  text-align: left;
}

@media only screen and (max-width: 1200px) {
  .text {
    font-size: 1.4em;
  }
}
@media only screen and (max-width: 750px) {
  .text {
    width: 90%;
  }

  .box-item {
    display: flex;
    flex-direction: column;
    width: 90%;
  }

  .nadpis {
    font-size: 1.8em;
  }

  .text {
    font-size: 1.2em;
  }
  .nadpis-text {
    font-size: 1.8em;
  }

  .text-sml {
    font-size: 1.2em;
  }
}
</style>
