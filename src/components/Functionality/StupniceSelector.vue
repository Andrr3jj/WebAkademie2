<template>
  <div class="info-buttons">
    <div
      v-for="element in stupnice"
      :key="element"
      class="button Adumu"
      :class="{ green: aktualnaStupnica === element }"
      @click="klikolNaStupnicu(element)"
    >
      <p>
        {{
          element === "D" || element === "A"
            ? element + " - Mol"
            : element + " - Dur"
        }}
      </p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    stupnice: Array,
    aktualnaStupnica: String,
  },
  methods: {
    klikolNaStupnicu(element) {
      this.$emit("klikol-na-stupnicu", element);
    },
  },
};
</script>

<style scoped>
.info-buttons {
  display: flex;
  gap: 1em;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
}

.button {
  background: #fef35a;
  font-size: 0.6em;
  margin: 0 0.1em 1em;
}

.button.Adumu {
  padding: 0.5em 1.5em;
  border-radius: 1em;
  cursor: pointer;
  transition: background 0.3s;
}

.button.Adumu.green {
  background: #90ca50;
}

@media print {
  /* Skryť UI výberu stupnice len pri tlači */
  .button:not(.green) {
    display: none !important;
  }
}
</style>
