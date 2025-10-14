<template>
  <div class="header-section">
    <h1 class="Bahnschrift">Vitaj v spevníku Heligónkovej Akadémie!</h1>
    <h5 class="Bahnschrift">
      Tu nájdeš texty piesní, ktoré spievame, hráme a milujeme.
    </h5>
  </div>
</template>

<script>
export default {
  name: "HeaderSection",
};
</script>

<style scoped lang="scss">
.header-section {
  text-align: center;
  margin: 2em 0;

  h1 {
    text-align: center;
    font-size: 2.8em; // pôvodne 45px
    font-style: normal;
    font-weight: bold;
    line-height: 1.15;
    margin: 0 0 0.5em 0;
    font-family: "Bahnschrift", sans-serif;
  }

  h5 {
    text-align: center;
    font-size: 2.2em; // pôvodne 35px
    font-weight: 400;
    font-style: normal;
    line-height: 1.15;
    margin: 0 0 0.3em 0;
    font-family: "Bahnschrift", sans-serif;
  }
}

// TABLET
@media (max-width: 900px) {
  .mobile section h1 {
    font-size: 2.4em !important; // cca 32px
    text-shadow: none !important;
  }

  h5 {
    font-size: 1.5em !important; // cca 24px
  }
}

// MOBILE
@media (max-width: 600px) {
  .mobile section h1 {
    font-size: 2.2em !important; // cca 22px
    margin-bottom: 0.32em !important;
  }

  .mobile section h5 {
    font-size: 1.2em !important; // cca 20px
  }
}
</style>
