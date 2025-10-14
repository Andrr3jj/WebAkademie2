<template>
  <div class="category-filters">
    <button
      v-for="category in categories"
      :key="category"
      class="button"
      :class="{ active: category === selected }"
      @click="
        $emit(
          'select-category',
          category === selected ? null : categoryMap[category] || category
        )
      "
    >
      <p>{{ category }}</p>
    </button>
  </div>
</template>

<script>
export default {
  name: "CategoryFilters",
  props: {
    selected: {
      type: String,
      default: null,
    },
  },
  data() {
    return {
      categories: ["Polka", "Valčík", "České", "Terchovské", "Moderné"],
      categoryMap: {
        Polka: "Polka",
        Valčík: "Valčík",
        České: "Česká",
        Terchovské: "Terchovská",
        Moderné: "Moderná",
      },
    };
  },
};
</script>

<style scoped lang="scss">
.category-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 1em;
  justify-content: center;
  margin: 1.5em auto 0.5em auto;
  padding: 0 1em;
  max-width: 100%;

  .button {
    font-family: "Adumu", sans-serif;
    font-size: 1.2em;
    width: 10.375em;
    justify-content: center;
    transition: all 0.18s;

    &.active {
      background-color: #90ca50;
    }
  }
}

@media (max-width: 600px) {
  .category-filters {
    justify-content: center;
    overflow-x: auto;
    padding-bottom: 0.5em;
    gap: 0.2em;

    .button {
      font-size: 1em !important;
      min-width: unset;
      width: 8.5em !important;
      padding: 0.33em 0.96em !important;
      border-radius: 0.8em;
      white-space: nowrap;
      flex-shrink: 0;
      margin: 0 0.13em 0.33em 0.13em;
    }
  }

  .mobile section p {
    font-size: 3.4vw;
  }

  .button.clear {
    font-size: 16px !important;
    min-width: 5.3em;
    padding: 0.23em 0.95em !important;
    margin-top: 0.45em;
  }
}
</style>
