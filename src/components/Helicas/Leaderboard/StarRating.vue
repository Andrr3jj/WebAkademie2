<template>
  <div class="stars">
    <img
      v-for="i in stars"
      :key="i"
      :src="plna"
      alt="Hviezda"
      class="hviezda"
      :title="getStarTitle(i)"
    />
  </div>
</template>

<script>
export default {
  name: "StarRating",
  props: {
    stars: {
      type: Number,
      default: 0,
      validator: (value) => value >= 0 && value <= 5,
    },
    dni: Number,
    hodiny: Number,
  },
  data() {
    return {
      plna: require("@/assets/images/gallery/hviezda.png"),
    };
  },
  methods: {
    getStarTitle(index) {
      if (index === 1) return "Hviezda za 8 hodín hrania";
      if (index === 2) return "Hviezda za 1 deň hrania";
      if (index === 3) return "Hviezda za 2 dní hrania";
      if (index === 4) return "Hviezda za 5 dní hrania";
      if (index === 5) return "Hviezda za 10 dní hrania";

      return "Hviezda";
    },
  },
};
</script>

<style scoped>
.stars {
  display: flex;
  justify-content: flex-start;
  flex-direction: row-reverse;
  align-items: center;
  gap: 0.1em;
}

.hviezda {
  width: 3em;
  height: auto;
  filter: drop-shadow(1px 1px 2px rgba(0, 0, 0, 0.25));
}
</style>
