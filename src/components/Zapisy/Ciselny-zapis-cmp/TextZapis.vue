<template>
  <transition name="info-box">
    <div class="song-info-box">
      <div class="text scroll">
        <p class="text-piesne">Text piesne:</p>

        <p v-if="lyrics">
          {{ lyrics.replaceAll("//n", "\n") }}
        </p>
      </div>
    </div>
  </transition>
</template>

<script>
export default {
  name: "TextZapis",
  props: {
    lyrics: {
      type: String,
      default: "",
    },
  },
};
</script>

<style lang="scss" scoped>
.text p {
  white-space: break-spaces;
}

.text-piesne {
  text-align: center;
  font-size: 1.875em;
  font-style: normal;
  font-weight: 800;
  margin: 1.1em auto 0.7em;
}

.text p:not(.text-piesne) {
  margin: 1em auto;
}

.text {
  padding-bottom: 3em;
  max-height: 50vh;
}

.song-info-box {
  position: absolute;
  bottom: 0.9em;
  left: 50%;
  transform: translateX(-50%);
  background: #ededed;
  border: 4px solid #fef35a;
  border-radius: 1.5rem;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
  padding: 1.5em;
  width: 90%;
  z-index: 49;
  box-sizing: border-box;
}

.song-info-box {
  transform-origin: bottom center;
}

.info-box-enter-active,
.info-box-leave-active {
  transition: all 0.3s ease;
}

.info-box-enter-from,
.info-box-leave-to {
  opacity: 0;
  transform: scale(0.8) translateY(-20px);
}

.info-box-enter-to,
.info-box-leave-from {
  opacity: 1;
  transform: scale(1) translateY(0);
}
</style>
