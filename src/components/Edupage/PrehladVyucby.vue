<template>
  <div class="prehlad-vyucby">
    <p class="nadpis">Tvoj prehľad výučby</p>
    <div class="info">
      <div class="left">
        <div class="icon">
          <img src="@/assets/images/vyzvy/7-dnova-vyzva.png" alt="" />
        </div>
        <div class="wrap">
          <p class="maly-nadpis">Učivo na poslednej hodine:</p>
          <p class="text"><span>Hodina:</span> {{ lastLessonDate || "--" }}</p>
          <p class="text"><span>Pieseň:</span> {{ lastLessonSong || "--" }}</p>
          <p class="text">
            <span>Poznámka:</span> {{ lastLessonNote || "--" }}
          </p>
        </div>
      </div>
      <div class="line"></div>
      <div class="right">
        <div class="stat">
          <p class="maly-nadpis">Počet absolvovaných lekcií:</p>
          <p class="value">{{ absolvedCount }}</p>
        </div>
        <div class="stat">
          <p class="maly-nadpis">Počet ospravedlnených lekcií:</p>
          <p class="value">{{ excusedCount }}</p>
        </div>
        <div class="stat">
          <p class="maly-nadpis">Počet získaných piesní:</p>
          <p class="value">{{ songCount }}</p>
        </div>
      </div>
    </div>
    <div class="table">
      <div class="header">
        <p>Dátum lekcie</p>
        <p>Pieseň / Učivo</p>
        <p>Poznámka</p>
        <p>Hodnotenie</p>
      </div>
      <div class="body">
        <div v-for="lesson in lessons" :key="lesson.id" class="row-table">
          <div class="item">
            <p>{{ lesson.date || "--" }}</p>
          </div>
          <div class="item">
            <p>{{ lesson.song || "--" }}</p>
          </div>
          <div class="item">
            <p>{{ lesson.note || "--" }}</p>
          </div>
          <div class="item">
            <div class="stars">
              <div v-for="n in lesson.stars" :key="n" class="star">
                <img src="@/assets/images/gallery/hviezda.svg" alt="" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      lessons: [],
      lastLessonDate: null,
      lastLessonSong: null,
      lastLessonNote: null,
      absolvedCount: 0,
      excusedCount: 0,
      songCount: 0,
      songNameCache: {},
    };
  },
  async mounted() {
    await this.loadLessons();
  },
  methods: {
    // Normalize values like false/0/"0"/"false" to a single falsey check
    isAttendanceFalse(value) {
      return (
        value === false || value === 0 || value === "0" || value === "false"
      );
    },
    async fetchSongName(id) {
      if (this.songNameCache[id]) return this.songNameCache[id];
      try {
        const res = await fetch(
          `https://heligonkovaakademia.sk/api/product/loadLimited.php/?id=${id}`
        );
        const json = await res.json();
        const name = json?.data?.name || `id:${id}`;
        this.songNameCache[id] = name;
        return name;
      } catch {
        return `id:${id}`;
      }
    },
    async parseNotes(notes) {
      // Extrahuje všetky id z "Add notes id:XXX"
      const regex = /Add notes id:(\d+)/g;
      const ids = [];
      let match;
      while ((match = regex.exec(notes))) {
        ids.push(match[1]);
      }
      // Načíta názvy piesní
      const names = await Promise.all(ids.map((id) => this.fetchSongName(id)));
      return names.join(", ");
    },
    async loadLessons() {
      const userId = this.$store?.state?.user?.id;
      const apiBase = this.$store?.state?.api;
      if (!userId || !apiBase) return;
      const url = `${apiBase}/edupage/loadMyLessonAll.php?id=${userId}`;
      const res = await fetch(url);
      const json = await res.json();
      const items = Array.isArray(json?.data) ? json.data : [];

      let lessons = [];
      let absolvedCount = 0;
      let excusedCount = 0;
      let songSet = new Set();
      let lastLesson = null;

      // Zoradiť podľa id zostupne (najnovšia prvá)
      items.sort((a, b) => Number(b.id) - Number(a.id));

      for (const it of items) {
        const students = JSON.parse(it.student);
        const me = students.find(
          (s) => String(s.student_id) === String(userId)
        );
        const showRow =
          me &&
          this.isAttendanceFalse(me.attendance_status) &&
          String(it.status) !== "planned";
        // Získaj piesne (ak existujú)
        if (it.song) songSet.add(it.song);

        // Počet absolvovaných/ospravedlnených
        if (me) {
          if (me.attendance_status === false) absolvedCount++;
          if (me.attendance_status === true) excusedCount++;
        }

        // Poznámka z me.info
        let info = me?.info || "";

        // Získaj názvy piesní z note
        let songNames = "";
        if (me?.note) {
          songNames = await this.parseNotes(me.note);
        }

        if (showRow) {
          lessons.push({
            id: it.id,
            date: it.date,
            song: songNames,
            note: info,
            stars: me ? Number(me.stars || 0) : 0,
          });
        }

        // Posledná absolvovaná hodina
        if (!lastLesson && showRow) {
          lastLesson = { date: it.date, song: songNames, note: info };
        }
      }

      this.lessons = lessons;
      this.absolvedCount = absolvedCount;
      this.excusedCount = excusedCount;
      this.songCount = songSet.size;
      this.lastLessonDate = lastLesson?.date || null;
      this.lastLessonSong = lastLesson?.song || null;
      this.lastLessonNote = lastLesson?.note || null;
    },
  },
};
</script>

<style lang="scss" scoped>
.nadpis {
  font-size: 2.8em;
  font-weight: 500;
  margin-bottom: 0.8em;
}

.info {
  display: flex;
  justify-content: space-evenly;
  margin-bottom: 2.5em;
}

.left,
.right {
  display: flex;
  gap: 0.4em;
  width: 40%;
}

.value {
  font-size: 1.4em;
  font-weight: 100;
}

.left {
  gap: 1.3em;
}

.line {
  width: 0.4rem;
}

.right {
  justify-content: center;
}

.icon {
  display: flex;
  align-items: center;
  justify-content: center;

  img {
    width: 5em;
  }
}

.maly-nadpis {
  font-size: 1.5em;
  margin-bottom: 0.3em;
  text-align: left;
}

span {
  font-weight: bold;
  margin-right: 0.3em;
}

.text {
  text-align: left;
  max-width: 24em;
}

.stat {
  display: flex;
  justify-content: space-between;
  width: 100%;
  gap: 1em;
}

.header {
  box-shadow: inset 2px 5px 4px rgba(0, 0, 0, 0.13);
  background: #fef35a;
  padding: 0.4em;
  border-radius: 0.8em;
  margin-bottom: 0.8em;
}

.header,
.body {
  display: flex;
  align-items: center;
  justify-content: center;
}

.body {
  flex-direction: column;
}

.row-table {
  width: 100%;
  display: flex;
  padding: 0.4em;
  box-sizing: border-box;
  align-items: center;
  border-bottom: 0.18em solid #e4e4e4;
  position: relative;

  &::before,
  &::after {
    content: "";
    position: absolute;
    bottom: 0;
    transform: translateY(58%);
    border-top: 6px solid transparent;
    border-bottom: 6px solid transparent;
  }

  /* Šípka naľavo */
  &::before {
    right: 0;
    border-right: 10px solid #e4e4e4; // farba ako border
  }

  /* Šípka napravo */
  &::after {
    left: 0;
    border-left: 10px solid #e4e4e4; // farba ako border
  }
}
.header p,
.body .row-table .item {
  padding: 0 0.4em;
  font-size: 1.3em;
  box-sizing: border-box;
}

.body .row-table .item p {
  font-size: 0.8em;
}

.header p:nth-child(1),
.body .row-table .item:nth-child(1) {
  width: 15%;
  text-align: left;

  p {
    text-align: left;
  }
}
.header p:nth-child(2),
.body .row-table .item:nth-child(2) {
  width: 30%;
}
.header p:nth-child(3),
.body .row-table .item:nth-child(3) {
  width: 40%;
}
.header p:nth-child(4),
.body .row-table .item:nth-child(4) {
  width: 15%;
  text-align: right;
}

.stars {
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
  align-items: center;
  gap: 0.3em;

  .star {
    opacity: 0;
    animation: starFadeIn 0.6s ease forwards;
    transition: opacity 0.2s ease;
  }

  .star img {
    width: 1em;
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

@media only screen and (max-width: 850px) {
  .row-table {
    display: flex;
    flex-direction: column;
    align-items: center;
    /* align-content: center; */
    justify-content: center;

    .item {
      width: 90% !important;
    }
  }

  .body .row-table .item:nth-child(1) p {
    font-weight: bolder;
    font-size: 0.9em;
  }
  .body .row-table .item:nth-child(1) p,
  .body .row-table .item:nth-child(4) p {
    text-align: center;
  }

  .body .row-table .item {
    margin: 0.5em auto;
  }

  .stars {
    justify-content: center;
  }

  /* Skryje všetky P */
  .header p {
    display: none;
  }

  /* Na header ako celok pridá náhradný názov */
  .header::before {
    content: "Všetky lekcie";
    display: block;
    font-size: 1.3em;
    font-weight: bold;
    text-align: center;
    padding: 0.4em 0;
  }

  .info {
    flex-direction: column;
    gap: 1em;
  }

  .icon img {
    width: 4em;
  }

  .maly-nadpis {
    font-size: 1.3em;
  }

  .nadpis {
    font-size: 2.6em;
  }

  .left,
  .right {
    width: 100%;

    margin: auto;
    display: flex;
    justify-content: center;
  }

  .stat {
    max-width: 30em;
  }
}

@media only screen and (max-width: 450px) {
  .left {
    flex-direction: column;
  }

  .nadpis {
    font-size: 1.8em;
    margin-bottom: 1em;
  }

  .value {
    font-size: 1.3em;
  }

  .stat .maly-nadpis {
    font-size: 1.1em;
  }
}
</style>
