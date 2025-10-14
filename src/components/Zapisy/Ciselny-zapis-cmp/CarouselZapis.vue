<template>
  <div class="carousel">
    <button class="arrow left" @click="scroll('left')">
      <img src="@/assets/images/gallery/vlavo.png" alt="Posuň do ľava" />
    </button>

    <div ref="container" class="carousel-container">
      <div class="carousel-track">
        <JedenZapis
          v-for="produktId in produktyID"
          :key="produktId"
          :produktyID="produktId"
          :produktyData="produktyData[produktId]"
          :flexCenter="true"
        />
      </div>
    </div>

    <button class="arrow right" @click="scroll('right')">
      <img src="@/assets/images/gallery/vpravo.png" alt="Posuň do prava" />
    </button>
  </div>
</template>

<script>
import JedenZapis from "../JedenZapis.vue";
export default {
  name: "CarouselZapis",
  components: { JedenZapis },
  props: {
    produktyID: {
      type: Array,
      default: () => [],
    },
    produktyData: {
      type: Object,
      default: () => ({}),
    },
  },
  methods: {
    scroll(direction) {
      const container = this.$refs.container;
      const amount = container.clientWidth * 0.8;
      container.scrollBy({
        left: direction === "left" ? -amount : amount,
        behavior: "smooth",
      });
    },
  },
};
</script>

<style scoped lang="scss">
.carousel {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1em;
  position: relative;
}

.carousel-container {
  width: 90%;
}

.carousel-track {
  font-size: 60%;
  box-sizing: border-box;
}

img {
  width: 2.5em;
}

.arrow {
  position: absolute;
  top: 50%;

  z-index: 2;

  &.left {
    left: 1%;
    transform: translate(-50%, -50%);
  }

  &.right {
    right: 1%;
    transform: translate(+50%, -50%);
  }
}

.arrow {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 0;
  flex-shrink: 0;
}

.carousel-container {
  overflow: hidden;
  flex: 0.95;
  margin: auto;
}

.carousel-container::-webkit-scrollbar {
  display: none;
}

.carousel-track {
  display: flex;
  gap: 4em;
  transition: transform 0.5s ease;
}
</style>
