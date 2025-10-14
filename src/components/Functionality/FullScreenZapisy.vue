<template>
  <div v-if="visible" class="overlay" @click="hideOverlay">
    <div class="image-container">
      <div
        v-if="this.images.length > 2"
        class="arrow left-arrow"
        @click="prevImage"
        @click.stop
      >
        &#10094;
      </div>
      <img
        v-for="(image, index) in visibleImages"
        :key="index"
        :src="image"
        class="image"
      />
      <div
        v-if="this.images.length > 2"
        class="arrow right-arrow"
        @click="nextImage"
        @click.stop
      >
        &#10095;
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    images: {
      type: Array,
      required: true,
    },
    visible: {
      type: Boolean,
      required: true,
    },
  },
  data() {
    return {
      currentIndex: 0,
    };
  },
  computed: {
    visibleImages() {
      if (this.images.length == 1) {
        return [this.images[this.currentIndex]];
      }
      return [
        this.images[this.currentIndex],
        this.images[(this.currentIndex + 1) % this.images.length],
      ];
    },
  },
  methods: {
    hideOverlay() {
      this.$emit("close");
    },
    prevImage() {
      this.currentIndex =
        (this.currentIndex - 1 + this.images.length) % this.images.length;
    },
    nextImage() {
      this.currentIndex = (this.currentIndex + 1) % this.images.length;
    },
  },
};
</script>

<style scoped>
.overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(128, 128, 128, 0.8);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.image-container {
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  gap: 2%;
  width: 97%;
}

.image {
  max-width: 45%;
  max-height: 95vh;
  margin: 0 5px;
  border-radius: 1rem;
  pointer-events: none; /* Zabráni klikaniu na obrázky */
}

.arrow {
  font-size: 2rem;
  cursor: pointer;
  user-select: none;
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  color: white;
}

.left-arrow {
  left: 10px;
}

.right-arrow {
  right: 10px;
}
</style>
