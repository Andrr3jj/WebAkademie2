<template>
  <div class="bonus-tasks-wrapper">
    <div class="bonus-tasks-content">
      <div class="left-side">
        <h3>Bonusové úlohy – získaj extra body!</h3>
        <p>
          Okrem klasického učenia môžeš body zbierať aj plnením úloh a výziev:
        </p>
        <div class="bonus-tasks-list">
          <ol>
            <li>Zbieraj čas výučby.</li>
            <li>Vlastni číslené zápisy.</li>
            <li>Získavaj skúsenosti.</li>
          </ol>
          <button @click="presmerujNaUlohy" class="button">
            Bonusové úlohy
          </button>
        </div>
      </div>
      <div class="right-side">
        <img
          src="@/assets/images/icons/bonus-task-icon.png"
          alt="Bonusové úlohy ilustrácia"
        />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "BonusTasks",
  methods: {
    presmerujNaUlohy() {
      this.$router.push("/ucebna/ulohy");
    },
  },
};
</script>

<style scoped lang="scss">
@import "@/assets/sass/_colors.scss";

.bonus-tasks-wrapper {
  display: flex;
  margin-top: 1em;
  margin-left: 8em;

  .bonus-tasks-content {
    display: flex;
    align-items: center;
    width: 100%;
    font-size: 80%;
    .left-side {
      width: 70%;
      h3 {
        font-family: "Bahnschrift", sans-serif;
        font-size: 2.5em;
        font-weight: bold;
        margin-bottom: 0.5em;
        text-align: left;
      }

      p {
        font-family: "Bahnschrift", sans-serif;
        font-size: 2em;
        margin-bottom: 1em;
        text-align: left;
        width: 70%;
      }

      .bonus-tasks-list {
        display: flex;
        align-items: center;
      }

      ol {
        font-family: "Bahnschrift", sans-serif;
        font-size: 1.3em;
        padding: 0;
        padding-left: 1em;

        li {
          padding-left: 0.5em;
          margin-bottom: 0.5em;
          text-align: left;
        }
      }
      .button {
        margin-left: 5em;
        padding-top: 0.5em;
        padding-bottom: 0.5em;
        font-family: "Adumu", sans-serif;
        color: black;
      }
    }

    .right-side {
      flex-shrink: 0;
      display: flex;
      align-items: center;
      justify-content: center;

      img {
        width: 15em;
      }
    }
  }
}

@media screen and (max-width: 1280px) {
  .button {
    margin-left: 1em;
  }
}

@media (max-width: 900px) {
  .bonus-tasks-wrapper {
    margin-left: 0;
  }
  .bonus-tasks-content {
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 2em;

    .left-side p {
      width: 100% !important;
      font-size: 1.5em;
    }

    .left-side {
      width: 100% !important;
      display: flex;
      flex-direction: column;
      align-items: center;

      h3,
      p,
      ol li {
        text-align: center !important;
      }
      .bonus-tasks-list {
        flex-direction: column;
        gap: 1em;

        ol {
          padding-left: 0;
        }

        .button {
          margin-left: 0;
        }
      }
    }

    .right-side {
      margin-top: 1em;

      img {
        width: 11em;
      }
    }
  }
}
</style>
