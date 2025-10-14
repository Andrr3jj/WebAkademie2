<template>
  <div class="earn-points">
    <div class="left-side">
      <h3>Ako využiť nazbierané body?</h3>
      <p>
        Nazbierané body môžeš využiť na číselné zápisy piesní. Keď dosiahneš
        dostatočný počet bodov, môžeš ich vymeniť za svoje obľúbené zápisy úplne
        zadarmo!
      </p>
    </div>

    <div class="right-side">
      <h3>Ako na to?</h3>
      <ol>
        <li>Prejdi do sekcie <strong>Číselné zápisy</strong></li>
        <li>
          Klikni na možnosť "
          <img src="@/assets/images/icons/present.png" alt="" /> " ceny zápisov
          sa ti automaticky zobrazia v bodoch namiesto eur
        </li>
        <li>Vyzdvihni si pieseň a potvrď nákup za body</li>
      </ol>
      <button @click="$router.push('/ciselne-zapisy')" class="button">
        Číselné zápisy
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "EarnPoints",
};
</script>

<style scoped lang="scss">
.earn-points {
  display: flex;
  justify-content: space-between;
  gap: 7em;
  margin-top: 1em;
  margin-left: 8em;

  .left-side,
  .right-side {
    flex: 1 1 48%;
    font-family: "Bahnschrift", sans-serif;
    text-align: left;
    font-size: 80%;
  }

  .left-side {
    h3 {
      font-size: 2.6em;
    }
    p {
      width: 81%;
      line-height: 1.4;
    }
  }

  .right-side {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    font-size: 80%;
    img {
      width: 1.5em;
      position: relative;
      top: 0.5em;
    }
    h3 {
      margin-top: 1.18em;
      margin-bottom: -0.3em;
    }
  }

  h3 {
    font-size: 2.5em;
    margin-bottom: 0.8em;
    font-family: "Bahnschrift", sans-serif;
    text-align: left;
    width: 100%;
  }

  p,
  ol {
    font-size: 1.8em;
    text-align: left;
  }

  ol {
    padding-left: 1.2em;

    li {
      margin-bottom: 0.2em;
      text-align: left;
      font-size: 0.9em;
    }
    li:nth-child(1) {
      margin-bottom: 0;
    }
  }
  .button {
    font-family: "Adumu", sans-serif;
    color: black;
  }
}

@media screen and (max-width: 1280px) {
  .earn-points {
    gap: 1em;
  }
}

@media (max-width: 900px) {
  .earn-points {
    flex-direction: column;
    margin-left: 1.5em;
    margin-right: 1.5em;
    gap: 3em;

    .left-side,
    .right-side {
      text-align: center;
      align-items: center;

      h3,
      p,
      ol {
        text-align: center !important;
      }

      p {
        width: 100%;
      }

      .button {
        align-self: center;
      }
    }

    .right-side {
      img {
        top: 0.2em;
      }
    }
  }
}
</style>
