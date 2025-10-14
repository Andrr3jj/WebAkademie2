<template>
  <div
    class="player-card"
    :class="['rank-' + poradie, { 'is-helibody': heliBody }]"
  >
    <div class="avatar-wrapper">
      <img
        v-if="avatarSrc"
        :src="avatarSrc"
        :alt="`Avatar ${meno}`"
        class="avatar"
      />
      <div class="badge-text">{{ poradie }}.</div>
      <div class="stars-wrapper">
        <StarRating :stars="stars" :filledOnly="true" />
      </div>
    </div>

    <p class="meno">{{ meno }}</p>

    <p v-if="!heliBody" class="cas">{{ cas }}</p>
    <p v-else class="body">{{ bodyText }}</p>
  </div>
</template>

<script>
import StarRating from "@/components/Helicas/Leaderboard/StarRating.vue";

export default {
  name: "LeaderboardPlayerCard",
  components: { StarRating },
  props: {
    poradie: { type: Number, required: true },
    avatar: { type: String, default: "" },
    meno: { type: String, required: true },
    cas: { type: String, default: "" },
    stars: { type: Number, default: 0 },
    heliBody: { type: Boolean, default: false },
    body: { type: [Number, String], default: 0 },
  },
  computed: {
    avatarSrc() {
      if (!this.avatar) return null;
      return this.stripWWW(this.avatar);
    },
    bodyText() {
      const n = this.body ?? 0;
      return typeof n === "number" ? n.toString() : n;
    },
  },
  methods: {
    stripWWW(url) {
      try {
        const u = new URL(url);
        if (u.hostname.startsWith("www.")) u.hostname = u.hostname.slice(4);
        return u.toString();
      } catch {
        return url.replace(/^https?:\/\/www\./, (m) => m.replace("www.", ""));
      }
    },
  },
};
</script>

<style scoped lang="scss">
.player-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  transform-origin: center;
  width: fit-content;
  position: relative;
}

.avatar-wrapper {
  position: relative;
  width: 7.5em;
  height: 7.5em;
}

.avatar {
  width: 100%;
  height: 100%;
  border-radius: 1.6em;
  object-fit: cover;
  border: 2px solid #fef35a;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.25);
}

.badge-text {
  position: absolute;
  top: -0.8em;
  left: 54%;
  transform: translateX(-50%);
  font-family: "Adumu", sans-serif;
  font-size: 3.2em;
  color: #90ca50;
  -webkit-text-stroke: 2px #fef35a;
  text-stroke: 2px #fef35a;
  line-height: 1;
  font-weight: 400;
}

.stars-wrapper {
  position: absolute;
  bottom: -1.1em;
  left: 54%;
  transform: translateX(-50%);
  font-size: 0.8em;
}

.meno {
  font-size: 1.75em;
  margin-top: 0.7em;
  text-align: center;
}

.cas {
  font-size: 1em;
  color: black;
  margin: 0;
}

.body {
  margin: 0.1em 0 0;
  font-size: 2.25em;
  font-weight: 800;
  color: #00913f;
  line-height: 1;
  font-family: "Adumu", sans-serif;
}

.rank-1 {
  transform: scale(1.2);
  z-index: 2;
}

.rank-2,
.rank-3 {
  transform: scale(0.94) translateY(2.4em);
  z-index: 1;
}

@media screen and (max-width: 767px) {
  .stars-wrapper {
    font-size: 0.8em;
  }
  .rank-1 {
    margin-top: 2em;
  }
}
</style>
