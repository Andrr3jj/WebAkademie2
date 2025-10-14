<template>
  <div class="spevnik">
    <p class="nadpis">Spevník piesní</p>
    <select v-model="vybraneId" @change="vyberCvicenie">
      <option
        v-for="cvicenie in cvicenia"
        :key="cvicenie.id"
        :value="cvicenie.id"
      >
        {{ cvicenie.nazov }}
      </option>
    </select>

    <div class="text" v-if="aktualneCvicenie">
      <p>{{ aktualneCvicenie.text }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "SpevnikPiesni",
  props: {
    cvicenia: {
      type: Array,
      required: true,
    },
    aktualneId: {
      type: [String, Number],
      required: true,
    },
  },
  data() {
    return {
      vybraneId: this.aktualneId,
    };
  },
  computed: {
    aktualneCvicenie() {
      return this.cvicenia.find((c) => c.id === Number(this.vybraneId));
    },
  },
  methods: {
    vyberCvicenie() {
      this.$emit("update-id", this.vybraneId);
    },
    isVideo(link) {
      return link.endsWith(".mp4");
    },
  },
  watch: {
    aktualneId(newId) {
      this.vybraneId = newId;
    },
  },
};
</script>

<style lang="scss" scoped>
.spevnik {
  margin: auto;
  max-width: 90%;

  .nadpis {
    font-size: 2.1em;
    text-align: center;
    font-weight: 500;
    margin: 1em auto 0.5em;
  }

  select {
    appearance: none; /* zruší default šípku pre Chrome/Safari */
    -moz-appearance: none; /* pre Firefox */
    -webkit-appearance: none; /* pre staršie Safari */
    background-image: url("@/assets/images/icons/rozbalenie.png"); /* sem tvoj obrázok */
    background-repeat: no-repeat;
    background-position: right 1rem center; /* zarovnanie šípky doprava */
    background-size: 0.8em; /* veľkosť šípky */

    padding: 0.2em 2em 0.2em 1.2em;
    border: none;
    font-size: 1.2em;

    background-color: #fef35a;
    box-shadow: 0.1em 0.1em 1em #00000040;
    border-radius: 2rem;
    min-height: 2em;
    margin: 1em auto 3em;

    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;

    transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;

    &:hover {
      background-color: #ffeb3b;
      transform: translateY(-3px);
      cursor: pointer;
      box-shadow: 0.1em 0.1em 0.5em #00000040;
    }

    &:active {
      transform: translateY(1px);
      background-color: #90ca50; // Farba pri kliknutí
      filter: brightness(0.95);
    }
  }

  select:focus {
    outline: none;
    box-shadow: none;
  }

  .text {
    margin-top: 1.2em;
    white-space: break-spaces;

    p {
      text-align: center;
      font-size: 1.2em;
    }
  }
}

@media only screen and (max-width: 750px) {
  select {
    width: auto;
    max-width: inherit;
    font-size: 1em;
  }

  .mobile .spevnik {
    .text p {
      font-size: 1.1em;
    }

    .nadpis {
      font-size: 2em;
    }
  }
}
</style>
