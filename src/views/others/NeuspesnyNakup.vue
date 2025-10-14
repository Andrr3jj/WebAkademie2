<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <the-headline />

      <div class="line horizontal w-80"></div>

      <p>
        Upozorňujeme, že vaša platba kartou nebola úspešná a objednávka nebola
        dokončená. Prosím, overte si zadané údaje a skúste platbu opäť. V
        prípade pokračujúcich problémov nás neváhajte kontaktovať.
      </p>

      <img
        src="@/assets/images/gallery/neuspesnyNakup.png"
        alt="Úspešný nákup "
      />
    </div>
  </section>

  <div class="mobile">
    <div class="scroll">
      <section :id="$route.name">
        <the-headline />

        <div class="line horizontal w-80"></div>

        <p>
          Upozorňujeme, že vaša platba kartou nebola úspešná a objednávka nebola
          dokončená. Prosím, overte si zadané údaje a skúste platbu opäť. V
          prípade pokračujúcich problémov nás neváhajte kontaktovať.
        </p>

        <img
          src="@/assets/images/gallery/neuspesnyNakup.png"
          alt="Úspešný nákup"
        />
      </section>
    </div>
  </div>
</template>

<script>
import TheHeadline from "../../components/Menu/TheHeadline.vue";
export default {
  mounted() {
    window.scrollTo(0, 0);
  },
  components: {
    TheHeadline,
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

p {
  color: #000;
  text-align: center;
  font-size: 1.875em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.15625rem */
  margin: 1em auto;
  width: 90%;
  padding: 0 5%;
}

img {
  width: 30vw;
  max-width: 23em;
  min-width: 20em;
}

@media only screen and (max-width: 1180px) {
  p {
    margin: 3em 0;
  }
}

@media only screen and (max-width: 1000px) {
  p {
    margin: 2em 0;
  }
}

.mobile section p {
  margin: 1em auto;
}

@media only screen and (max-width: 450px) {
  .mobile img {
    width: 70%;
    max-width: unset;
    min-width: unset;
  }
}
</style>
