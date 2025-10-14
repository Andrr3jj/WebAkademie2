<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <the-headline />

      <div class="line horizontal w-80"></div>

      <p class="text">
        Ospravedlňujeme sa, ale stránka, ktorú hľadáte, nie je k dispozícii.
      </p>

      <p class="text">
        Možno ste zadali nesprávnu URL adresu, alebo stránka mohla byť presunutá
        alebo odstránená. <br />Prosím, skontrolujte si adresu a skúste to
        znova.
      </p>

      <p class="text underline">
        Ak si myslíte, že ide o chybu, kontaktujte nás pomocou údajov uvedených
        na našej hlavnej stránke.
      </p>

      <p class="big">Ďakujeme za pochopenie.</p>

      <div class="button">
        <router-link to="/">Prejsť na stránku</router-link>
      </div>
    </div>
  </section>

  <div class="mobile">
    <div class="scroll">
      <section :id="$route.name">
        <the-headline />

        <div class="line horizontal w-80"></div>

        <p class="text">
          Ospravedlňujeme sa, ale stránka, ktorú hľadáte, nie je k dispozícii.
        </p>

        <p class="text">
          Možno ste zadali nesprávnu URL adresu, alebo stránka mohla byť
          presunutá alebo odstránená. Prosím, skontrolujte si adresu a skúste to
          znova.
        </p>

        <p class="text underline">
          Ak si myslíte, že ide o chybu, kontaktujte nás pomocou údajov
          uvedených na našej hlavnej stránke.
        </p>

        <p class="big">Ďakujeme za pochopenie.</p>

        <div class="button">
          <router-link to="/">Prejsť na stránku</router-link>
        </div>
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

.text {
  text-align: center;

  font-size: 1.5em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.15625rem */
  margin: 1.5em auto;
  width: 90%;
  padding: 0 5%;
}

.underline {
  text-decoration-line: underline;
}

.big {
  color: #000;
  text-align: center;
  font-size: 2em;
  font-style: normal;
  font-weight: 600;
  line-height: 115%; /* 2.875rem */
  margin-top: 1em;
}

.button {
  color: #000;
  text-align: center;
  font-size: 1.3rem;
  font-style: normal;
  font-weight: 400;
  line-height: 90%; /* 1.94063rem */
  width: max-content;
  margin: 2em auto;
}

img {
  width: 30vw;
  max-width: 23em;
  min-width: 20em;
}

.mobile section .button {
  margin: 2em auto;
}

.mobile section .button {
  margin: 1em auto 1em auto;

  a {
    font-size: 1em;
    margin: 0;
  }
}

.mobile .text {
  font-size: 1.55em;
}

.mobile .big {
  font-size: 1.9em;
}

.mobile .button {
  font-size: 1.3em;
}

@media only screen and (max-width: 1180px) {
  .text {
    margin: 1.5em auto !important;
  }
}
@media only screen and (max-width: 450px) {
  .mobile .big {
    font-size: 6vw;
  }

  .mobile .text {
    font-size: 1.4em;
  }
}
</style>
