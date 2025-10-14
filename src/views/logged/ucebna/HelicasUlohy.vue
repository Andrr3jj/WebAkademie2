<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <the-ulohy></the-ulohy>
    </div>
  </section>

  <div class="mobile">
    <div class="scroll">
      <section :id="$route.name">
        <the-ulohy></the-ulohy>
      </section>
    </div>
  </div>
</template>

<script>
import TheUlohy from "@/components/Helicas/TheUlohy.vue";
export default {
  mounted() {
    window.scrollTo(0, 0);
  },
  components: {
    TheUlohy,
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";
</style>
