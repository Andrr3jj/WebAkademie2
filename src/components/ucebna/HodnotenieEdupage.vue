<template>
  <div class="hodnotenie-edupage">
    <p class="nadpis">Tvoje hodnotenie</p>
    <p class="text">Posledná hodina:</p>
    <div class="stars">
      <template v-if="lastLessonStars !== null">
        <div v-for="n in lastLessonStars" :key="n" class="star">
          <img src="@/assets/images/gallery/hviezda.svg" alt="" />
        </div>
      </template>
      <template v-else>
        <span class="text">Žiadne hodnotenie</span>
      </template>
    </div>
    <div class="text">Celkové hodnotenie</div>
    <p class="hodnotenie Adumu">{{ totalStars }}</p>

    <div class="button" @click="$emit('open-modal')">
      <p class="Adumu">Zobraziť body</p>
    </div>
  </div>
</template>

<script>
export default {
  emits: ["open-modal"],
  data() {
    return {
      lastLessonStars: null,
      totalStars: 0,
      loading: false,
      error: null,
    };
  },
  async mounted() {
    await this.loadStars();
  },
  methods: {
    async loadStars() {
      this.loading = true;
      this.error = null;
      try {
        const userId = this.$store?.state?.user?.id;
        const apiBase = this.$store?.state?.api;
        if (!userId || !apiBase)
          throw new Error("Chýba user.id alebo api base v store");
        const url = `${apiBase}/edupage/loadMyLessonAll.php?id=${userId}`;
        const res = await fetch(url);
        const json = await res.json();
        const lessons = Array.isArray(json?.data) ? json.data : [];

        let totalStars = 0;
        let lastLessonStars = null;

        // Zoradiť podľa id zostupne (najnovšia prvá)
        lessons.sort((a, b) => Number(b.id) - Number(a.id));

        for (const lesson of lessons) {
          const students = JSON.parse(lesson.student);
          for (const s of students) {
            if (String(s.student_id) === String(userId)) {
              totalStars += Number(s.stars || 0);
              if (lastLessonStars === null && s.attendance_status === false) {
                lastLessonStars = s.stars;
              }
            }
          }
        }

        this.lastLessonStars = lastLessonStars;
        this.totalStars = totalStars;
      } catch (e) {
        this.error = e.message || String(e);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.hodnotenie-edupage {
  display: flex;
  gap: 0.8em;
  flex-direction: column;
  background: #f9f186;
  padding: 2em;
  border-radius: 3em;

  filter: drop-shadow(5px 5px 5px rgba(0, 0, 0, 0.2));
  box-shadow: inset 2px 5px 8px rgb(0 0 0 / 13%);
}
.nadpis {
  font-size: 2.2em;
  font-weight: 600;
}

.stars {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  gap: 0.9em;

  .star {
    opacity: 0;
    animation: starFadeIn 0.6s ease forwards;
    transition: opacity 0.2s ease;
  }

  .star img {
    width: 2em;
    transition: transform 0.2s ease;
    pointer-events: auto;
    cursor: pointer;

    &:hover {
      transform: translateY(0) scale(1.1) rotate(10deg);
    }
  }

  .star:nth-child(1) {
    animation-delay: 0s;
  }
  .star:nth-child(2) {
    animation-delay: 0.2s;
  }
  .star:nth-child(3) {
    animation-delay: 0.4s;
  }
  .star:nth-child(4) {
    animation-delay: 0.6s;
  }
  .star:nth-child(5) {
    animation-delay: 0.8s;
  }
}

@keyframes starFadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.text {
  font-size: 1.6em;
}

.hodnotenie {
  font-size: 3em;
  color: #00913f;

  &:hover {
    transform: scale(1.1) skewX(-3deg);
    transition-duration: 0.2s;
  }
}

.button {
  padding: 0.1em 1.3em;

  font-size: 1em;
  width: auto;
  margin: auto;
}

@media screen and (max-width: 1024px) {
  .hodnotenie-edupage {
    margin-inline: auto;
    margin-block: 2em;
  }
}

@media screen and (max-width: 750px) {
  .hodnotenie-edupage {
    width: 80%;
  }
}
</style>
