<template>
  <div
    class="audio-wrapper"
    @mouseover="hover = true"
    @mouseleave="onMouseLeave"
  >
    <img
      :src="
        isPlaying
          ? require('@/assets/images/icons/pause.svg')
          : require('@/assets/images/icons/play.svg')
      "
      @click.stop="togglePlay"
      alt="Prehrať/Zastaviť"
    />
    <div class="timeline" :class="{ expanded: hover }">
      <input
        type="range"
        min="0"
        :max="duration"
        step="0.1"
        v-model="currentTime"
        @input="onSeek"
      />
      <p class="time">{{ remainingTime }}</p>
    </div>

    <slot />
    <audio
      ref="audio"
      :src="src"
      @timeupdate="updateTime"
      @loadedmetadata="loadMeta"
      @ended="onEnded"
    ></audio>
  </div>
</template>

<script>
export default {
  props: {
    src: String,
    id: Number,
    aktualnePrehravaneId: Number,
  },
  data() {
    return {
      isPlaying: false,
      duration: 0,
      currentTime: 0,
      hover: false,
    };
  },
  computed: {
    remainingTime() {
      const remaining = this.duration - this.currentTime;
      const minutes = Math.floor(remaining / 60);
      const seconds = Math.floor(remaining % 60)
        .toString()
        .padStart(2, "0");
      return `–${minutes}:${seconds}`;
    },
  },
  methods: {
    pause() {
      const audio = this.$refs.audio;
      if (audio) {
        audio.pause();
        this.isPlaying = false;
        this.hover = false;
      }
    },
    async togglePlay() {
      await this.$nextTick();
      const audio = this.$refs.audio;
      if (audio && typeof audio.play === "function") {
        if (this.isPlaying) {
          audio.pause();
        } else {
          audio.play();
        }

        this.isPlaying = !this.isPlaying;
        this.$emit("prehravanie");
        this.$emit("update-id", this.id);
      }
    },
    onMouseLeave() {
      if (!this.isPlaying) {
        this.hover = false;
      }
    },
    onEnded() {
      this.isPlaying = false;
      this.hover = false;
    },
    updateTime() {
      const audio = this.$refs.audio;
      if (audio && audio.currentTime != null) {
        this.currentTime = audio.currentTime;
      }
    },
    loadMeta() {
      this.duration = this.$refs.audio.duration;
    },
    onSeek() {
      const audio = this.$refs.audio;
      if (audio) {
        audio.currentTime = this.currentTime;
      }
    },
  },
  watch: {
    aktualnePrehravaneId(noveId) {
      if (noveId !== this.id && this.isPlaying) {
        this.pause();
      }
    },
  },
  beforeUnmount() {
    const audio = this.$refs.audio;
    if (audio) {
      audio.pause();
      audio.src = "";
      audio.removeAttribute("src");
      audio.load();
    }
  },
};
</script>

<style scoped lang="scss">
.audio-wrapper {
  display: flex;
  align-items: center;
  position: relative;
}

.audio-wrapper img {
  background-color: #fef35a;
  box-shadow: 0.1em 0.1em 1em #00000040;
  border-radius: 2rem;
  padding: 0.5em 1em;
  margin: 0 1em;
  width: 1.2em;
  z-index: 3;
  cursor: pointer;
}

.timeline {
  position: absolute;
  top: 50%;
  left: 3em;
  transform: translateY(-50%);
  overflow: hidden;
  width: 0;
  height: 0;
  padding: 0;
  background-color: transparent;
  border-radius: 0;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  z-index: 2;

  input[type="range"] {
    width: 100%;
    -webkit-appearance: none;
    height: 0.1em;
    background: linear-gradient(to right, #00b300, #a4e065);
    border-radius: 10px;
    outline: none;
    padding: 0.15em 0em;
    cursor: pointer;

    &::-webkit-slider-thumb {
      -webkit-appearance: none;
      appearance: none;
      height: 0.6em;
      width: 0.6em;
      border-radius: 50%;
      background: black;
      cursor: pointer;
      border: none;
    }

    &::-moz-range-thumb {
      height: 0.6em;
      width: 0.6em;
      border-radius: 50%;
      background: black;
      cursor: pointer;
      border: none;
    }
  }
}

.timeline.expanded {
  width: 15em;
  height: 1.5em;
  padding: 0.1em 1em 0.1em 1.5em;
  background-color: #fef35a;
  border-radius: 2rem;
}

.time {
  font-size: 0.9em;
  text-decoration: none !important;
  width: 4em;
}

/* Position slotted text exactly below the controls */
:deep(.audio-text) {
  position: absolute;
  top: calc(100% + 0.5em);
  left: 50%;
  transform: translateX(-50%);
  margin: 0;
  white-space: nowrap;
}
</style>
