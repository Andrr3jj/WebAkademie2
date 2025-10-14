<template>
  <video ref="videoPlayer" controls controlsList="nodownload">
    <source :src="videoSrc" type="video/mp4" />
    Váš prehliadač nepodporuje prehrávanie videa, prosím skúste použiť Chrome.
  </video>
</template>

<script>
export default {
  props: {
    video: {
      type: String,
      default: "",
    },
    videoId: {
      type: [String, Number],
      required: true,
    },
  },
  data() {
    return {
      odoslane: false,
    };
  },
  computed: {
    videoSrc() {
      return this.video && this.video.trim() !== ""
        ? this.video
        : "https://heligonkovaakademia.sk/data/free/fasiangy.mp4";
    },
  },
  watch: {
    videoSrc() {
      this.reloadVideo();
    },
  },
  methods: {
    reloadVideo() {
      const video = this.$refs.videoPlayer;
      if (video) video.load();
    },
  },
  mounted() {
    this.reloadVideo();
  },
};
</script>

<style lang="scss" scoped>
video {
  width: 100%;
  height: auto;
  object-fit: cover;
  border-radius: 1.5rem;
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
  outline: 0.3em solid #fef35a;
  filter: drop-shadow(0.2px 0.2px 10px rgba(0, 0, 0, 0.3));
}

@media only screen and (max-width: 1300px) {
  video {
    width: 90%;
  }
}
</style>
