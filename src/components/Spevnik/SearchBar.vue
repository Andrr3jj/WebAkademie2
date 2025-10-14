<template>
  <div class="search">
    <div class="search-bar">
      <img src="@/assets/images/icons/hladanie.svg" alt="Vyhľadávanie" />
      <input
        type="text"
        :value="modelValue"
        placeholder="Vyhľadať pieseň"
        @input="$emit('update:modelValue', $event.target.value)"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: "SearchBar",
  props: {
    modelValue: {
      type: String,
      default: "",
    },
  },
};
</script>

<style scoped lang="scss">
.search {
  width: 100%;
  display: flex;
  justify-content: center;
  margin-bottom: 2em;
}
.search-bar {
  border-radius: 1.5625rem;
  border: 6px solid #fef35a;
  background: rgba(233, 233, 233, 0.93);
  box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.35);

  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0 0.5em;
  margin: 1em 0;

  width: 40em;
  font-size: 0.8em;

  img {
    width: 2em;
  }

  input {
    width: 100%;
    background: transparent;
    border: none;
    outline: none;

    font-size: 1.25em;
    font-style: normal;
    font-weight: 400;
    line-height: 137.53%;
    letter-spacing: 0.0625rem;

    margin-bottom: -0.2em;
  }
}

@media (max-width: 600px) {
  .search {
    width: 90%;
    padding: 0 1em;
    bottom: 0.5em;
  }
}
</style>
