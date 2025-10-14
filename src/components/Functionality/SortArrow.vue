<template>
  <span class="sort-arrow" @click="toggleSort">
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="0.7em"
      height="0.7em"
      viewBox="0 0 14 12"
      class="triangle-icon"
      :class="rotationClass"
    >
      <path
        d="M13.7814 2.28223C14.0658 1.81973 14.0752 1.23848 13.8095 0.766602C13.5439 0.294727 13.0439 0.000976574 12.5002 0.000976581L1.50015 0.000976612C0.956406 0.000976612 0.454801 0.294727 0.190776 0.766602C-0.0748491 1.23848 0.0623493 1.81973 0.218901 2.28223L5.7189 11.2822C5.99786 11.7917 6.47515 12.0015 7.00015 12.001C7.52515 12.001 8.0064 11.7291 8.2814 11.2822L13.7814 2.28223Z"
        fill="black"
      />
    </svg>
  </span>
</template>

<script>
export default {
  props: {
    column: String,
    sortKey: String,
    sortDirection: String,
  },
  computed: {
    rotationClass() {
      if (this.sortKey !== this.column) return "neutral";
      return this.sortDirection === "asc" ? "rotate" : "";
    },
  },
  methods: {
    toggleSort() {
      let newDirection = "asc";
      if (this.sortKey === this.column && this.sortDirection === "asc")
        newDirection = "desc";
      else if (this.sortKey === this.column && this.sortDirection === "desc")
        newDirection = "";
      this.$emit("update:sort", {
        key: newDirection ? this.column : "",
        direction: newDirection,
      });
    },
  },
};
</script>

<style scoped>
.sort-arrow {
  display: inline-flex;
  align-items: center;
  margin-left: 2px;
  cursor: pointer;
  vertical-align: middle;
}

.triangle-icon {
  height: 0.7em;
  width: 0.7em;
  vertical-align: middle;
  transition: transform 0.3s ease;
}

.rotate {
  transform: rotate(180deg);
}

.neutral {
  opacity: 0.3;
}
</style>
