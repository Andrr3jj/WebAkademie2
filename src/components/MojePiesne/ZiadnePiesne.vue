<template>
  <section :id="$route.name + 'ziadnePiesne'" class="computer mobile">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="line horizontal w-80"></div>

      <p class="nadpis">Zatiaľ tu je ticho, ale nie na dlho!</p>
      <p class="w-70">
        Kým zoznam nie je plný, zakúpte si prvú pieseň a spustite svoju vlastnú
        hudobnú revolúciu. Každý číselný zápis, ktorý si kúpite, pridáva do
        vášho hudobného sveta nový rozmer. Začnite teraz a urobte svoje ticho
        melodickejším!
      </p>

      <router-link to="/ciselne-zapisy"
        ><img
          class="ciselne-zapisy"
          src="@/assets/images/gallery/CiselneZapisyTitle.png"
          alt="Císelné zápisy pre heligonku"
      /></router-link>

      <router-link class="link" to="/ciselne-zapisy">
        <h3>Zakúpiť číselne zápisy</h3>
      </router-link>
    </div>
  </section>
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

.nadpis {
  font-size: 2em;
  font-style: normal;
  font-weight: 600;
  margin: 2em 0 1em;
  padding: 0 5%;
}

section p {
  text-align: center;
  font-size: 1.4em;
  font-weight: 400;
  line-height: 115%;
  margin: auto;
}

.line {
  margin: auto;
}

.ciselne-zapisy {
  width: 11em;
  margin: 2em auto 1em;
  transform: translateX(0.5em);
  transition-duration: 0.3s;

  &:hover {
    transform: translateX(0.5em) scale(1.1);
    transition-duration: 0.2s;
  }
}

h3 {
  text-align: center;
  font-size: 2.2em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.875rem */
  margin: auto;
}

.link:hover h3 {
  text-decoration: underline;
}

.mobile .scroll {
  padding-bottom: 0;
}

@media only screen and (max-width: 750px) {
  .w-70 {
    width: 90% !important;
  }

  .nadpis {
    margin: 1em 0 0.5em;
  }
}
</style>
