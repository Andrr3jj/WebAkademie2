<template>
  <button class="akcia-btn" @click="togglePlay">
    <span class="ikona" v-html="isPlaying ? pauseIcon : playIcon"></span>
  </button>
</template>

<script>
export default {
  name: "GreenMusicPlayer",
  props: {
    audioUrl: {
      type: String,
      required: true,
    },
  },
  data() {
    return {
      audio: null,
      isPlaying: false,
    };
  },
  computed: {
    playIcon() {
      return `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none"><g filter="url(#filter0_i_3011_1185)"><path d="M0 10C0 7.34784 1.05357 4.8043 2.92893 2.92893C4.8043 1.05357 7.34784 0 10 0C12.6522 0 15.1957 1.05357 17.0711 2.92893C18.9464 4.8043 20 7.34784 20 10C20 12.6522 18.9464 15.1957 17.0711 17.0711C15.1957 18.9464 12.6522 20 10 20C7.34784 20 4.8043 18.9464 2.92893 17.0711C1.05357 15.1957 0 12.6522 0 10ZM7.35547 5.74609C7.05859 5.91016 6.875 6.22656 6.875 6.5625V13.4375C6.875 13.7773 7.05859 14.0898 7.35547 14.2539C7.65234 14.418 8.01172 14.4141 8.30469 14.2344L13.9297 10.7969C14.207 10.625 14.3789 10.3242 14.3789 9.99609C14.3789 9.66797 14.207 9.36719 13.9297 9.19531L8.30469 5.75781C8.01562 5.58203 7.65234 5.57422 7.35547 5.73828V5.74609Z" fill="#90CA50"/></g><defs><filter id="filter0_i_3011_1185" x="0" y="0" width="20" height="24" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB"><feFlood flood-opacity="0" result="BackgroundImageFix"/><feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix" result="shape"/><feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/><feOffset dy="4"/><feGaussianBlur stdDeviation="2"/><feComposite in2="hardAlpha" operator="arithmetic" k2="-1" k3="1"/><feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/><feBlend mode="normal" in2="shape" result="effect1_innerShadow_3011_1185"/></filter></defs></svg>`;
    },
    pauseIcon() {
      return `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M15.6 16.8H13.2V7.2H15.6M10.8 16.8H8.4V7.2H10.8M12 0C10.4241 0 8.86371 0.310389 7.4078 0.913446C5.95189 1.5165 4.62902 2.40042 3.51472 3.51472C1.26428 5.76516 0 8.8174 0 12C0 15.1826 1.26428 18.2348 3.51472 20.4853C4.62902 21.5996 5.95189 22.4835 7.4078 23.0866C8.86371 23.6896 10.4241 24 12 24C15.1826 24 18.2348 22.7357 20.4853 20.4853C22.7357 18.2348 24 15.1826 24 12C24 10.4241 23.6896 8.86371 23.0866 7.4078C22.4835 5.95189 21.5996 4.62902 20.4853 3.51472C19.371 2.40042 18.0481 1.5165 16.5922 0.913446C15.1363 0.310389 13.5759 0 12 0Z" fill="#90CA50"/></svg>`;
    },
  },
  watch: {
    audioUrl: {
      immediate: true,
      handler(newUrl) {
        if (!newUrl) return;
        if (this.audio) {
          this.audio.pause();
          this.audio = null;
        }
        this.audio = new Audio(newUrl);
        this.audio.ontimeupdate = () => {
          this.generateTime();
        };
        this.audio.onloadedmetadata = () => {
          this.generateTime();
        };
        this.audio.onended = () => {
          this.isTimerPlaying = false;
        };
      },
    },
  },
  methods: {
    togglePlay() {
      if (!this.audio) return;

      if (this.audio.paused) {
        this.audio.play();
        this.isPlaying = true;
      } else {
        this.audio.pause();
        this.isPlaying = false;
      }
    },
  },
  beforeUnmount() {
    if (this.audio) {
      this.audio.pause();
      this.audio = null;
    }
  },
};
</script>

<style scoped>
.akcia-btn {
  display: flex;
  align-items: center;
  gap: 0.5em;
  background-color: rgba(254, 243, 90, 0.7);
  border: none;
  border-radius: 17px;
  padding: 0.6em 1.2em;
  font-weight: bold;
  font-size: 0.95em;
  cursor: pointer;
  transition: background 0.2s ease, transform 0.2s ease;
  box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.15);
}

.akcia-btn:hover {
  background-color: rgba(254, 243, 90, 0.85);
  transform: translateY(-2px) scale(1.03);
}

.akcia-btn:active {
  transform: scale(0.96);
  box-shadow: none;
}

.ikona {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 21px;
}
</style>
