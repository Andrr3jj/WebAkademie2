<template>
  <div class="wrapper">
    <div class="player">
      <div class="player__top">
        <div class="player-controls">
          <div class="player-controls__item -xl js-play" @click="play">
            <img
              v-if="isTimerPlaying"
              src="@/assets/images/icons/pause.svg"
              alt=""
              class="icon"
            />
            <img
              v-else
              src="@/assets/images/icons/play.svg"
              class="icon"
              alt=""
            />
          </div>
        </div>
      </div>
      <div class="progress" ref="progress">
        <div class="progress__bar" @click="clickProgress">
          <div class="progress__current" :style="{ width: barWidth }"></div>
          <div class="progress__dot" :style="{ left: barWidth }">
            <img src="@/assets/images/icons/bodkaPrehravac.svg" />
          </div>
        </div>
      </div>
      <div class="progress__time">{{ currentTime }} - {{ duration }}</div>
      <div v-cloak></div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["audioUrl"],
  data() {
    return {
      barWidth: null,
      duration: null,
      currentTime: null,
      isTimerPlaying: false,
      tracks: [
        {
          url: this.audioUrl,
        },
      ],
    };
  },
  methods: {
    play() {
      if (this.audio.paused) {
        this.audio.play();
        this.isTimerPlaying = true;
      } else {
        this.audio.pause();
        this.isTimerPlaying = false;
      }
    },
    generateTime() {
      let width = (100 / this.audio.duration) * this.audio.currentTime;
      this.barWidth = width + "%";
      this.circleLeft = width + "%";
      let durmin = Math.floor(this.audio.duration / 60);
      let dursec = Math.floor(this.audio.duration - durmin * 60);
      let curmin = Math.floor(this.audio.currentTime / 60);
      let cursec = Math.floor(this.audio.currentTime - curmin * 60);
      if (durmin < 10) {
        durmin = "0" + durmin;
      }
      if (dursec < 10) {
        dursec = "0" + dursec;
      }
      if (curmin < 10) {
        curmin = "0" + curmin;
      }
      if (cursec < 10) {
        cursec = "0" + cursec;
      }
      this.duration = durmin + ":" + dursec;
      this.currentTime = curmin + ":" + cursec;
    },
    updateBar(x) {
      let progress = this.$refs.progress;
      let maxduration = this.audio.duration;
      let position;

      if (window.innerWidth <= 1100) {
        position = x - window.innerWidth / 50 - progress.offsetLeft;
      } else if (window.innerWidth <= 1600) {
        /* eslint-disable-next-line */
        position =
          x -
          window.innerWidth / 2 +
          window.innerWidth / 9 -
          progress.offsetLeft;
      } else if (window.innerWidth > 1600 && window.innerWidth < 1700) {
        /* eslint-disable-next-line */
        position =
          x -
          window.innerWidth / 2 +
          window.innerWidth / 9.2 -
          progress.offsetLeft;
      } else {
        /* eslint-disable-next-line */
        position =
          x -
          window.innerWidth / 2 +
          window.innerWidth / 10.2 -
          progress.offsetLeft;
      }

      let percentage = (100 * position) / progress.offsetWidth;
      if (percentage > 100) {
        percentage = 100;
      }
      if (percentage < 0) {
        percentage = 0;
      }
      this.barWidth = percentage + "%";
      this.circleLeft = percentage + "%";
      this.audio.currentTime = (maxduration * percentage) / 100;
      this.audio.play();
    },
    clickProgress(e) {
      this.isTimerPlaying = true;
      this.audio.pause();
      this.updateBar(e.pageX);
    },
  },
  created() {
    let vm = this;
    this.currentTrack = this.tracks[0];
    this.audio = new Audio();
    this.audio.src = this.currentTrack.url;
    this.audio.ontimeupdate = function () {
      vm.generateTime();
    };
    this.audio.onloadedmetadata = function () {
      vm.generateTime();
    };
    this.audio.onended = function () {
      this.isTimerPlaying = false;
    };

    // this is optional (for preload covers)
    for (let index = 0; index < this.tracks.length; index++) {
      const element = this.tracks[index];
      let link = document.createElement("link");
      link.rel = "prefetch";
      link.href = element.cover;
      link.as = "image";
      document.head.appendChild(link);
    }
  },
  beforeUnmount() {
    // Zastav přehrávání hudby při opuštění komponenty
    if (this.audio) {
      this.audio.pause();
    }
  },
};
</script>

<style lang="scss" scoped>
.wrapper {
  background: #fef35a;
  box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.35);
  display: flex;
  justify-content: center;
  width: 20em;
  border-radius: 1.6rem;
  margin: 1em auto 0.5em;
}

.player {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 92%;
  margin: auto;
  height: 3.3em;
}
.icon {
  display: inline-block;
  width: 2.4em;
  height: 2.4em;
  margin-right: 1em;

  stroke-width: 0;
  stroke: currentColor;
  fill: currentColor;
  cursor: pointer;
  transition-duration: 0.2s;
  &:hover {
    cursor: pointer;
    transform: scale(1.1);
  }
}

[v-cloak] {
  display: none;
}
[v-cloak] > * {
  display: none;
}

.player-controls__item {
  display: flex;
  filter: drop-shadow(4px 4px 6px rgba(0, 0, 0, 0.25));
}
.progress {
  width: 100%;
  margin: auto;
  user-select: none;
  position: relative;
  &__top {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
  }

  &__duration {
    color: #71829e;
    font-weight: 700;
    font-size: 20px;
    opacity: 0.5;
  }
  &__time {
    margin-top: 2px;
    color: #71829e;
    font-weight: 700;
    font-size: 0.8em;
    opacity: 0.7;
    position: absolute;
    left: 50%; //48%
    transform: translate(-50%, -50%);
    //top: 120%;
    bottom: -6%;
  }
  &__current {
    background-color: darken(#8ec84e, 5%);
    height: inherit;
    width: 0%;
    border-radius: 10px;
  }

  &__dot {
    position: absolute;
    left: 82.2204%;
    top: 0;
    transform: translate(-50%, 0px);

    & img {
      width: 1.1em;
      position: relative;
      z-index: 5;
      filter: drop-shadow(3px 3px 5px rgba(0, 0, 0, 0.25));
      margin-top: 0.15em;
    }
  }
}
.progress__bar {
  height: 6px;
  width: 100%;
  cursor: pointer;
  background-color: #000;
  display: inline-block;
  border-radius: 10px;
}

.album-info {
  color: #71829e;
  flex: 1;
  padding-right: 60px;
  user-select: none;

  @media screen and (max-width: 576px), (max-height: 500px) {
    padding-right: 30px;
  }

  &__name {
    font-size: 20px;
    font-weight: bold;
    margin-bottom: 12px;
    line-height: 1.3em;
    @media screen and (max-width: 576px), (max-height: 500px) {
      font-size: 18px;
      margin-bottom: 9px;
    }
  }
  &__track {
    font-weight: 400;
    font-size: 20px;
    opacity: 0.7;
    line-height: 1.3em;
    min-height: 52px;
    @media screen and (max-width: 576px), (max-height: 500px) {
      font-size: 18px;
      min-height: 50px;
    }
  }
}

.github-btn {
  position: absolute;
  right: 40px;
  bottom: 50px;
  text-decoration: none;
  padding: 15px 25px;
  border-radius: 4px;
  box-shadow: 0px 4px 30px -6px rgba(36, 52, 70, 0.65);
  background: #24292e;
  color: #fff;
  font-weight: bold;
  letter-spacing: 1px;
  font-size: 16px;
  transition: all 0.3s ease-in-out;

  @media screen and (min-width: 500px) {
    &:hover {
      transform: scale(1.1);
      box-shadow: 0px 17px 20px -6px rgba(36, 52, 70, 0.36);
    }
  }

  @media screen and (max-width: 700px) {
    position: relative;
    bottom: auto;
    right: auto;
    margin-top: 20px;

    &:active {
      transform: scale(1.1);
      box-shadow: 0px 17px 20px -6px rgba(36, 52, 70, 0.36);
    }
  }
}

//scale out

.scale-out-enter-active {
  transition: all 0.35s ease-in-out;
}
.scale-out-leave-active {
  transition: all 0.35s ease-in-out;
}
.scale-out-enter {
  transform: scale(0.55);
  pointer-events: none;
  opacity: 0;
}
.scale-out-leave-to {
  transform: scale(1.2);
  pointer-events: none;
  opacity: 0;
}

//scale in

.scale-in-enter-active {
  transition: all 0.35s ease-in-out;
}
.scale-in-leave-active {
  transition: all 0.35s ease-in-out;
}
.scale-in-enter {
  transform: scale(1.2);
  pointer-events: none;
  opacity: 0;
}
.scale-in-leave-to {
  transform: scale(0.55);
  pointer-events: none;
  opacity: 0;
}
</style>
