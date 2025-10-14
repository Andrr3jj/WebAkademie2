<template>
  <div class="edupage-info">
    <p class="main">{{ mainText }}</p>
    <p class="secondary">{{ secondText }}</p>

    <button
      v-if="showPlanButton"
      type="button"
      class="button Adumu"
      @click="$emit('open-modal')"
    >
      {{ texts.button }}
    </button>
  </div>
</template>

<script>
export default {
  name: "EdupageInfo",
  props: {
    /**
     * Keď je true: zobrazíme prázdny stav.
     * Keď je false: zobrazíme plánovací stav (napr. existujú žiaci bez hodín).
     */
    empty: { type: Boolean, default: true },

    /**
     * Počet žiakov bez naplánovaných hodín. Používa sa v texte pre plánovací stav.
     */
    unplannedCount: { type: Number, default: 0 },

    /**
     * Voliteľné texty – umožňuje jednoducho lokalizovať/meniť wording z parent komponentu.
     */
    texts: {
      type: Object,
      default: () => ({
        empty: {
          main: "Tvoj rozvrh hodín je zatiaľ prázdny.",
          secondary:
            "Aby si si mohol naplánovať hodiny, musíš mať najprv pridelených žiakov.",
        },
        plan: {
          main: "V tvojom rozvrhu sa {verb} {countStudents}, ktor{relativePronoun} ešte nem{verbNeg} naplánované hodiny.",
          secondary: "Pridaj im lekcie, aby si mohol začať s výučbou.",
        },
        button: "Naplánovať výučbu",
      }),
    },
  },
  emits: ["open-modal"],
  computed: {
    showPlanButton() {
      return !this.empty && this.unplannedCount > 0;
    },
    mainText() {
      if (this.empty) return this.texts.empty.main;
      const count = this.unplannedCount;
      const verb = count === 1 ? "nachádza" : "nachádzajú";
      const countStudents = this.inflectAssignedStudents(count);
      let relativePronoun, verbNeg;
      if (count === 1) {
        relativePronoun = "ý";
        verbNeg = "á";
      } else {
        relativePronoun = "í";
        verbNeg = "ajú";
      }
      return this.texts.plan.main
        .replace("{verb}", verb)
        .replace("{countStudents}", countStudents)
        .replace("{relativePronoun}", relativePronoun)
        .replace("{verbNeg}", verbNeg);
    },
    secondText() {
      return this.empty
        ? this.texts.empty.secondary
        : this.texts.plan.secondary;
    },
  },
  methods: {
    inflectAssignedStudents(count) {
      // Correct Slovak declension with adjective "pridelený"
      if (count === 1) return `${count} pridelený žiak`;
      if (count >= 2 && count <= 4) return `${count} pridelení žiaci`;
      return `${count} pridelených žiakov`;
    },
  },
};
</script>

<style lang="scss" scoped>
.edupage-info {
  display: flex;
  flex-direction: column;
  gap: 2em;
  margin: 11em 10%;
  max-width: 70em;
}

.main {
  font-weight: 600;
  font-size: 2.1em;
}

.secondary {
  color: #555;
  font-size: 1.4em;
}

.button {
  width: max-content;
  margin: auto;
}
</style>
