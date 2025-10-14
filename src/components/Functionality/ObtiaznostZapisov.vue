<template>
  <img
    v-if="produktObtiaznost == 1 || produktObtiaznost == null"
    src="@/assets/images/obtiaznost/obtiaznost1.png"
    alt="Obtiažnosť veľmi ľahká"
  />
  <img
    v-if="produktObtiaznost == 2"
    src="@/assets/images/obtiaznost/obtiaznost2.png"
    alt="Obtiažnosť ľahká"
  />
  <img
    v-if="produktObtiaznost == 3"
    src="@/assets/images/obtiaznost/obtiaznost3.png"
    alt="Obtiažnosť Stredne ťažká"
  />
  <img
    v-if="produktObtiaznost == 4"
    src="@/assets/images/obtiaznost/obtiaznost4.png"
    alt="Obtiažnosť ťažká"
  />
  <img
    v-if="produktObtiaznost == 5"
    src="@/assets/images/obtiaznost/obtiaznost5.png"
    alt="Obtiažnosť veľmi ťazká"
  />
  <img
    v-if="produktObtiaznost == 6"
    src="@/assets/images/obtiaznost/obtiaznost6.png"
    alt="Obtiažnosť expert"
  />
</template>

<script>
export default {
  props: {
    produktObtiaznost: Number,
  },
};
</script>

<style lang="scss" scoped>
img {
  width: 90%;
  z-index: 1;
  position: relative;
}
</style>
