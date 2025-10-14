<template>
  <div class="news-list">
    <h2>Novinky pridané na stránke:</h2>

    <div class="entries-wrapper" ref="entries">
      <transition-group name="fade-slide" tag="div">
        <div v-for="item in displayedNovinky" :key="item.id" class="news-entry">
          <p class="date">
            {{ formatDate(item.timestamp) }}
            <span class="type">{{ typText(item.details.typ) }}</span>
          </p>
          <p class="title">{{ cleanText(item.name) }}</p>
        </div>
      </transition-group>

      <img
        v-if="!isDesktop"
        class="news-toggle"
        :class="{ expanded: newsExpanded }"
        :src="
          newsExpanded
            ? require('@/assets/images/icons/rollDown.svg')
            : require('@/assets/images/icons/rollDown.svg')
        "
        :alt="
          newsExpanded ? 'Zobraziť menej noviniek' : 'Zobraziť viac noviniek'
        "
        role="button"
        tabindex="0"
        @click="toggleSmooth"
        @keydown.enter.space.prevent="toggleSmooth"
      />
    </div>
  </div>
</template>

<script>
export default {
  name: "NewsList",
  data() {
    return {
      novinky: [],
      fallbackNovinky: [
        {
          id: 1,
          timestamp: "2025-07-25 12:00:00",
          name: "Testovacia skladba č. 1",
          details: { typ: "Zapis" },
        },
        {
          id: 2,
          timestamp: "2025-07-24 10:00:00",
          name: "Video – Úvod do Heligónky",
          details: { typ: "Video" },
        },
        {
          id: 3,
          timestamp: "2025-07-23 09:00:00",
          name: "Záznam z koncertu – Liptov",
          details: { typ: "Zapis" },
        },
        {
          id: 4,
          timestamp: "2025-07-22 08:00:00",
          name: "Pridaná nová funkcia",
          details: { typ: "System" },
        },
        {
          id: 5,
          timestamp: "2025-07-21 08:00:00",
          name: "Ešte jedna testovacia novinka",
          details: { typ: "Zapis" },
        },
      ],
      visibleCount: 3,
      increment: 3,
      initialVisible: 3,
      isDesktop: false,
      newsExpanded: false,
      transitionMs: 350,
    };
  },
  computed: {
    displayedNovinky() {
      return this.isDesktop
        ? this.novinky
        : this.novinky.slice(0, this.visibleCount);
    },
    hasMore() {
      return this.visibleCount < this.novinky.length;
    },
    canCollapse() {
      return this.visibleCount > this.initialVisible;
    },
  },
  mounted() {
    this.checkIfDesktop();
    window.addEventListener("resize", this.checkIfDesktop);

    fetch("/api/product/getLatest.php")
      .then((res) => res.json())
      .then((data) => {
        if (Array.isArray(data.data) && data.data.length > 0) {
          this.novinky = data.data.map((item) => ({
            ...item,
            details: JSON.parse(item.details || "{}"),
          }));
        } else {
          this.novinky = this.fallbackNovinky;
        }
      })
      .catch(() => {
        this.novinky = this.fallbackNovinky;
      });
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.checkIfDesktop);
  },
  methods: {
    checkIfDesktop() {
      this.isDesktop = window.matchMedia("(min-width:1025px)").matches;
    },
    formatDate(ts) {
      const [date] = ts.split(" ");
      return date.split("-").reverse().join(".");
    },
    typText(typ) {
      switch (typ) {
        case "Zapis":
          return "bol pridaný zápis";
        case "Video":
          return "bolo pridané náučné video";
        case "System":
          return "bola pridaná systémová novinka";
        default:
          return "bola pridaná novinka";
      }
    },
    cleanText(text) {
      return text?.replace(/[\u200B-\u200D\uFEFF]/g, "").trim() || "";
    },

    toggleSmooth() {
      const wrapper = this.$refs.entries;
      if (!wrapper) return;

      const startHeight = wrapper.getBoundingClientRect().height;
      const expanding = this.hasMore;

      if (expanding) {
        const oldCount = this.visibleCount;
        this.visibleCount = Math.min(
          this.visibleCount + this.increment,
          this.novinky.length
        );
        this.newsExpanded = this.visibleCount >= this.novinky.length;

        this.$nextTick(() => {
          const newlyAdded = wrapper.querySelectorAll(".news-entry")[oldCount];
          if (newlyAdded) {
            newlyAdded.scrollIntoView({
              behavior: "smooth",
              block: "start",
            });
          }
        });
      } else if (this.canCollapse) {
        wrapper.style.height = `${startHeight}px`;
        wrapper.style.overflow = "hidden";
        requestAnimationFrame(() => {
          this.visibleCount = this.initialVisible;
          this.newsExpanded = false;
          this.$nextTick(() => {
            const targetHeight = wrapper.scrollHeight;
            wrapper.style.transition = `height ${this.transitionMs}ms ease`;
            wrapper.style.height = `${targetHeight}px`;
            setTimeout(() => {
              wrapper.style.height = "";
              wrapper.style.transition = "";
              wrapper.style.overflow = "";
            }, this.transitionMs);
          });
        });
      }
    },
  },
};
</script>

<style scoped lang="scss">
.news-list {
  margin: 0 0.5em 1em;
  display: flex;
  flex-direction: column;
  align-items: center;

  h2 {
    font-size: 2em;
    font-weight: 40;
    font-family: "Bahnschrift", sans-serif;
    margin: 0.5em 0 0.2em;
    text-align: center;
  }

  .entries-wrapper {
    position: relative;
    width: 100%;
    display: flex;
    flex-direction: column;
  }

  .news-entry {
    margin-bottom: 0.6em;
    text-align: center;

    .date {
      font-size: 1em;
      color: #00000070;
      margin: 0.3em 0 0;
    }
    .type {
      margin-left: 0.4em;
    }
    .title {
      font-size: 1.1em;
      font-weight: 400;
    }
  }

  .news-toggle {
    position: absolute;
    right: 1em;
    bottom: 0.4em;
    width: 7.9vw;
    height: 7.9vw;
    cursor: pointer;
    transition: transform 0.35s ease;
    user-select: none;
    filter: drop-shadow(2px 2px 3px rgba(0, 0, 0, 0.25));
  }
  .news-toggle.expanded {
    transform: rotate(180deg);
  }
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.35s ease;
}
.fade-slide-enter-from {
  opacity: 0;
  transform: translateY(-8px);
}
.fade-slide-leave-to {
  opacity: 0;
  transform: translateY(-8px);
}

@media screen and (min-width: 1025px) {
  .entries-wrapper {
    max-height: 22em;
    overflow-y: auto;
    padding-right: 0.3em;
  }
}

@media screen and (max-width: 750px) {
  .news-list {
    h2 {
      font-size: 4vw;
      font-weight: 400;
      text-align: center;
    }
    .news-entry {
      .date {
        font-size: 1.3em; /* bez !important */
      }
      .title {
        font-size: 1.5em;
        font-weight: 400;
      }
    }
  }
}
@media screen and (max-width: 450px) {
  .news-list {
    h2 {
      font-size: 4.5vw !important; /* ak chceš presne kopírovať z NewsListu */
      font-weight: 400 !important;
      text-align: center;
    }
    .news-entry {
      .date {
        font-size: 3.2vw;
      }
      .title {
        font-weight: 500;
        font-size: 3.6vw;
      }
    }
  }
}
</style>
