<template>
  <div class="headline-admin">
    <div class="row">
      <h1 class="title">{{ title }}</h1>

      <aside class="stats-panel" v-if="stats">
        <h6>Počet žiakov:</h6>
        <div class="stat">
          <p>Celkový počet:</p>
          <span class="badge">{{ stats.total }}</span>
        </div>
        <div class="stat">
          <p>Aktívni žiaci:</p>
          <span class="badge">{{ stats.active }}</span>
        </div>
        <div class="stat">
          <p>Odhlásení žiaci:</p>
          <span class="badge">{{ stats.resigned }}</span>
        </div>
      </aside>
    </div>

    <div class="teachers" v-if="teachers && teachers.length">
      <div class="teacher" v-for="t in teachers" :key="t.name">
        <strong class="name">{{ t.name }}:</strong>
        <span class="count">{{ t.count }}</span>
        <small class="duo-solo">(Duo: {{ t.duo }} / Sólo: {{ t.solo }})</small>
      </div>
    </div>

    <div class="line horizontal w-90"></div>
  </div>
</template>

<script>
export default {
  name: "HeadlineSection",
  props: {
    title: { type: String, required: true },
    teachers: {
      type: Array,
      default: () => [],
    },
    stats: {
      type: Object,
      default: null,
    },
  },
};
</script>

<style lang="scss" scoped>
.headline-admin {
  padding: 1.2em 1.2em 0.6em;
  background: transparent;
}

.row {
  display: grid;
  grid-template-columns: 1fr auto;
  align-items: center;
  column-gap: 1.5em;
  justify-items: center;
  background: transparent !important;
  box-shadow: none !important;
}

.title {
  color: #fef35a;
  text-align: left;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.4vw;
  font-weight: 400;
  letter-spacing: -0.03em;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: #000;
  line-height: 1.05;
  margin: 0;
}

.stats-panel {
  border-radius: 1.2em;
  padding: 0.8em 1em;
  position: absolute;
  right: 5em;
  top: 3em;
}

.stats-panel h6 {
  margin: 0 0 0.4em 0;
  font-size: 1.5em;
  font-weight: 700;
}

.stat {
  display: flex;
  flex-direction: column;
  align-items: center;

  p {
    font-size: 0.938em;
    margin-bottom: 0.5em;
  }
}

.badge {
  display: flex;
  text-align: center;
  border-radius: 9px;
  background: #fef35a;
  box-shadow: 2.5px 2.5px 7.5px rgba(0, 0, 0, 0.5);
  font-size: 0.875em;
  font-family: "Adumu", sans-serif;
  margin-bottom: 0.5em;
  align-self: center;
  justify-content: center;
  padding: 0.1em 1.3em;
}

.teachers {
  display: grid;
  grid-auto-flow: column;
  justify-content: center;
  gap: 2.2em;
  margin-top: 4em;
  margin-bottom: 2em;
}

.teacher {
  text-align: center;
}

.name {
  font-weight: 700;
  margin-right: 0.5em;
  font-size: 1.5em;
}

.count {
  font-weight: 700;
  font-size: 1.5em;
}

.duo-solo {
  display: block;
  margin-top: 0.3em;
  font-size: 0.938em;
  font-weight: lighter;
}

@media (max-width: 1400px) {
  .stats-panel {
    right: 0;
    top: 2em;
  }
}

@media (max-width: 1024px) {
  .row {
    grid-template-columns: 1fr;
    row-gap: 0.8em;
  }
  .title {
    text-align: center;
    font-size: 9vw;
  }
  .stats-panel {
    position: static;
    justify-self: center;
  }
  .teachers {
    grid-auto-flow: row;
    grid-template-columns: 1fr;
    text-align: center;
    justify-items: center;
  }
  .teacher {
    font-size: 1.05em;
  }
  .duo-solo {
    font-size: 0.95em;
  }
}
</style>
