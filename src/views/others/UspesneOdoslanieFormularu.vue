<template>
  <section :id="$route.name" class="computer">
    <the-headline />

    <div class="line horizontal w-80"></div>
    <div class="obsah">
      <p class="nadpis">Ďakujeme za vašu správu!</p>

      <p class="text">
        Vaša otázka bola úspešne odoslaná. Budeme sa snažiť čo najrýchlejšie
        odpovedať na vašu požiadavku. Prosím, buďte trpezliví, a ak máte ďalšie
        otázky alebo potreby, neváhajte nás opäť kontaktovať.
      </p>

      <p class="podtext">S pozdravom, Heligónková Akadémia</p>
    </div>
    <img
      src="@/assets/images/gallery/AndrejSpravaOdoslana.png"
      alt="Andrej odoslaná správa "
    />
  </section>
  <div :id="$route.name" class="mobile">
    <section>
      <the-headline />

      <div class="line horizontal w-80"></div>
      <div class="obsah">
        <p class="nadpis">Ďakujeme za vašu správu!</p>

        <p class="text">
          Vaša otázka bola úspešne odoslaná. Budeme sa snažiť čo najrýchlejšie
          odpovedať na vašu požiadavku. Prosím, buďte trpezliví, a ak máte
          ďalšie otázky alebo potreby, neváhajte nás opäť kontaktovať.
        </p>

        <p class="podtext">S pozdravom, Heligónková Akadémia</p>
      </div>
      <img
        src="@/assets/images/gallery/AndrejSpravaOdoslana.png"
        alt="Andrej odoslaná správa "
      />
    </section>
  </div>
</template>

<script>
import TheHeadline from "../../components/Menu/TheHeadline.vue";
export default {
  mounted() {
    window.scrollTo(0, 0);

    setTimeout(() => {
      this.$router.push("/pomoc");
    }, 10000);
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
  margin: 1.5em auto;
  width: 90%;
  padding: 0 5%;
}

img {
  width: 30vw;
  max-width: 28em;
  min-width: 20em;
  position: absolute;
  left: -3%;
  bottom: 0em;
  filter: drop-shadow(8px 5px 8px rgba(0, 0, 0, 0.35));
}

.nadpis {
  font-size: 2em;
  font-weight: 800;
}

.text {
  font-size: 1.6em;
}

.podtext {
  font-size: 1.6em;
  font-weight: 600;
}

.obsah {
  width: 60%;
  margin: 5em auto;
}

@media only screen and (max-width: 1300px) {
  .obsah {
    width: 75%;
    margin: 8em auto;
  }
}
@media only screen and (max-width: 1100px) {
  .obsah {
    width: 80%;
  }
}

@media screen and (max-width: 750px) {
  img {
    display: none;
  }

  .mobile section p {
    margin: 1.5em auto;
  }

  .obsah {
    width: 90%;
    margin: 3em auto 5em;
  }
}

@media only screen and (max-width: 450px) {
  .obsah {
    font-size: 90%;
  }
}
</style>
