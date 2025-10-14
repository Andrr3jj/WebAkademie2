<template>
  <div v-if="show" class="modal-backdrop" @click.self="closeModal">
    <div class="modal-content">
      <h2>🎉 Gratulujeme! 🎉</h2>
      <p>
        {{ text }}
      </p>
    </div>
    <canvas ref="confettiCanvas" class="confetti-canvas"></canvas>
  </div>
</template>

<script>
import confetti from "canvas-confetti";

export default {
  name: "ConfettiModal",
  data() {
    return {
      show: true,
    };
  },
  mounted() {
    this.launchConfetti();
    setTimeout(() => {
      document.addEventListener("click", this.closeModal);
    }, 500);
  },
  beforeUnmount() {
    document.removeEventListener("click", this.closeModal);
  },
  methods: {
    closeModal() {
      this.show = false;
      this.$store.commit("SET_GRATULATION", false);
      document.removeEventListener("click", this.closeModal);
    },
    launchConfetti() {
      const canvas = this.$refs.confettiCanvas;
      const myConfetti = confetti.create(canvas, { resize: true });
      myConfetti({
        particleCount: 150,
        angle: 90,
        spread: 80,
        origin: { y: 1 }, // spodná časť obrazovky
      });
    },
  },
  props: {
    text: {
      type: String,
      default: "",
    },
  },
};
</script>

<style scoped>
h2 {
  letter-spacing: 0.1em;
}
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  padding: 2em;
  border-radius: 1em;
  text-align: center;
  max-width: 70%;
  font-size: 1.2em;
  font-weight: bold;
  color: #222;
  position: relative;
  z-index: 10;

  background: #ededed;
  outline: 0.2em solid #fef35a;
  filter: drop-shadow(0.2px 0.2px 10px rgba(0, 0, 0, 0.3));
  box-shadow: inset 0 0 0.3em 2px #fef35a, 5px 5px 20px 5px rgba(0, 0, 0, 0.25);
  border-radius: 2rem;
  padding: 2em;
  box-sizing: border-box;
}

.confetti-canvas {
  position: fixed;
  top: 0;
  left: 0;
  pointer-events: none;
  width: 100vw;
  height: 100vh;
  z-index: 5;
}

@media only screen and (max-width: 400px) {
  .modal-backdrop {
    font-size: 2.7vw;
  }
}
</style>
