<template>
  <div class="how-to-work">
    <div class="text-column">
      <h3>Ako funguje Heličas?</h3>
      <p>
        Každá minúta strávená učením sa počíta! Keď si na stránke a venuješ sa
        výučbe – či už pozeráš videá, trénuješ číselné zápisy automaticky
        získavaš body.
      </p>
      <ul>
        <li>Za každú hodinu učenia dostaneš 4 body.</li>
        <li>Svoj progres môžeš sledovať v sekcii Heličas.</li>
        <li>Každá aktivita na stránke ti pomáha zbierať ďalšie body.</li>
      </ul>
    </div>
    <div class="image-column">
      <img
        src="@/assets/images/gallery/ZeleneHodiny.png"
        alt="Veľké zelené hodiny"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: "HowWorks",
};
</script>

<style scoped lang="scss">
.how-to-work {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 4em;
  margin-left: 8em;
  margin-right: 3em;
  gap: 2em;
  flex-wrap: wrap;

  .text-column {
    flex: 1 1 60%;
    max-width: 50em;
    font-size: 80%;

    h3 {
      font-family: "Bahnschrift", sans-serif;
      font-size: 2.5em;
      margin-bottom: 0.7em;
      text-align: left;
    }

    p {
      font-family: "Bahnschrift", sans-serif;
      font-size: 2em;
      margin-bottom: 1.2em;
      text-align: left;
    }

    ul {
      font-family: "Bahnschrift", sans-serif;
      list-style: disc;
      padding-left: 1.2em;
      margin-top: 1em;

      li {
        margin-bottom: 0.5em;
        text-align: left;
        font-weight: bold;
        font-size: 2em;
      }
    }
  }

  .image-column {
    flex: 1 1 30%;
    display: flex;
    justify-content: center;

    img {
      width: 290px;
      height: auto;
    }
  }
}

/* 📱 Responzívne pre menšie zariadenia */
@media (max-width: 900px) {
  .how-to-work {
    flex-direction: column;
    align-items: center;
    margin: 3em 1.5em;

    .text-column {
      text-align: center;

      h3,
      p,
      li {
        text-align: center;
      }

      p {
        font-size: 1.6em;
      }

      li {
        font-size: 1.6em;
      }
    }

    .image-column {
      margin-top: 2em;

      img {
        width: 150px;
      }
    }
  }
}
</style>
