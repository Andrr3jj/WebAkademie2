<template>
  <div class="calendar">
    <div class="calendar-header">
      <h2 class="monthName">{{ monthName }} {{ currentYear }}</h2>
      <div class="month-navigation">
        <button @click="prevMonth"><p>‹</p></button>
        <button @click="nextMonth"><p>›</p></button>
      </div>
    </div>

    <div class="calendar-grid">
      <div v-for="d in daysOfWeek" :key="d" class="day-name Adumu">{{ d }}</div>

      <div
        v-for="day in daysInMonth"
        :key="day.date"
        class="day"
        :class="getDayClass(day)"
      >
        {{ day.day }}
      </div>
    </div>
  </div>
</template>

<script>
function formatISO(date) {
  const y = date.getFullYear();
  const m = String(date.getMonth() + 1).padStart(2, "0");
  const d = String(date.getDate()).padStart(2, "0");
  return `${y}-${m}-${d}`;
}

function parseStudentArray(studentField) {
  if (!studentField) return [];
  if (Array.isArray(studentField)) return studentField;
  try {
    return JSON.parse(studentField);
  } catch {
    return [];
  }
}

function skDateToISO(skDate) {
  // "02.09.2025" -> "2025-09-02"
  if (!skDate || typeof skDate !== "string") return null;
  const [dd, mm, yyyy] = skDate.split(".");
  if (!dd || !mm || !yyyy) return null;
  return `${yyyy}-${mm.padStart(2, "0")}-${dd.padStart(2, "0")}`;
}

function weekdayISO(date) {
  // 1..7  (1=Mon, 7=Sun)
  const wd = date.getDay();
  return wd === 0 ? 7 : wd;
}

export default {
  data() {
    const today = new Date();
    return {
      currentMonth: today.getMonth(),
      currentYear: today.getFullYear(),
      daysOfWeek: ["Po", "Ut", "St", "Št", "Pi", "So", "Ne"],
      // newly internalized state
      scheduleDay: null, // 1..7
      statusDays: {}, // { 'YYYY-MM-DD': 'cancelled_admin' | 'cancelled_parent' | 'scheduled' | 'absolved' }
      loading: false,
      error: null,
    };
  },
  computed: {
    monthName() {
      return new Date(this.currentYear, this.currentMonth).toLocaleString(
        "default",
        { month: "long" }
      );
    },
    daysInMonth() {
      const days = [];
      const firstDay = new Date(this.currentYear, this.currentMonth, 1);
      const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);

      let start = firstDay.getDay();
      if (start === 0) start = 7;

      for (let i = 1; i < start; i++) {
        days.push({ day: "", date: null });
      }

      for (let d = 1; d <= lastDay.getDate(); d++) {
        const date = new Date(this.currentYear, this.currentMonth, d);
        const iso = formatISO(date);
        const weekday = date.getDay() === 0 ? 7 : date.getDay(); // 1..7 (Po..Ne)
        days.push({ day: d, date: iso, weekday, dateObj: date });
      }
      return days;
    },
    currentWeekRange() {
      // return {start: Date, end: Date} for current week (Mon..Sun)
      const today = new Date();
      const wd = weekdayISO(today); // 1..7
      const start = new Date(today);
      start.setDate(today.getDate() - (wd - 1));
      const end = new Date(start);
      end.setDate(start.getDate() + 6);
      return { start, end };
    },
  },
  mounted() {
    this.initCalendar();
  },
  methods: {
    async initCalendar() {
      this.loading = true;
      this.error = null;
      try {
        const userId =
          this.$store?.state?.user?.id || this.$store?.state?.userId || null;
        const apiBase = this.$store?.state?.api || "";
        if (!userId || !apiBase)
          throw new Error("Chýba userId alebo API base URL v store.");

        const url = `${apiBase}/edupage/loadMyLessonAll.php?id=${userId}`;
        const res = await fetch(url);
        const json = await res.json();
        const items = Array.isArray(json?.data) ? json.data : [];

        let maxId = -1;
        let lastIsoByMaxId = null;

        // 1) Vytvoriť mapu statusov podľa dát z API
        const statusMap = {}; // isoDate -> status-class
        const slots = []; // zoznam (weekday, hourString?) na zistenie patternu

        for (const it of items) {
          const iso = skDateToISO(it.date);

          // track last lesson by numeric max id
          const idNum = Number(it.id);
          if (!Number.isNaN(idNum) && iso) {
            if (idNum > maxId) {
              maxId = idNum;
              lastIsoByMaxId = iso;
            }
          }

          if (!iso) continue;

          // priorita: najprv per-študent (attendance_status), až potom status hodiny
          let computedStatus = null;

          const students = parseStudentArray(it.student);
          console.log("Lesson item", it, "parsed students", students);

          if (students && students.length > 0) {
            const me = students.find(
              (s) => String(s.student_id) === String(userId)
            );
            if (
              me &&
              Object.prototype.hasOwnProperty.call(me, "attendance_status")
            ) {
              const att = me.attendance_status;
              console.log(
                "Checking student",
                userId,
                "attendance_status raw:",
                att
              );
              if (att === false) computedStatus = "absolved"; // absolvoval
              else if (att === true)
                computedStatus = "cancelled_parent"; // neabsolvoval (rodič)
              else if (att === null) computedStatus = "cancelled_admin"; // zrušené učiteľom
            }
          }

          // Ak študent nemá špecifický stav, zober status na úrovni hodiny (zrušené/planned)
          if (computedStatus == null && typeof it.status === "string") {
            const s = it.status;
            if (s === "cancelled_admin") computedStatus = "cancelled_admin";
            else if (s === "cancelled_parent")
              computedStatus = "cancelled_parent";
            else if (s === "planned") computedStatus = "scheduled";
            console.log(
              "Lesson-level fallback (after student-first):",
              s,
              "->",
              computedStatus
            );
          }

          if (computedStatus) {
            const m = new Date(iso).getMonth() + 1; // 1..12
            if (m !== 7 && m !== 8) {
              console.log(
                "Final status applied for",
                iso,
                "=",
                computedStatus,
                "(lesson.status:",
                it.status,
                ")"
              );
              statusMap[iso] = computedStatus;
            }
          }

          // uchovaj slot na zistenie pravidelného dňa
          const d = iso ? new Date(iso) : null;
          if (d) {
            const wd = weekdayISO(d); // 1..7
            slots.push(`${wd}`);
          }
        }

        // 2) Derivovať najčastejší deň v týždni (pattern)
        let scheduleDay = null;
        if (slots.length) {
          const freq = slots.reduce((acc, k) => {
            acc[k] = (acc[k] || 0) + 1;
            return acc;
          }, {});
          scheduleDay = Number(
            Object.keys(freq).sort((a, b) => freq[b] - freq[a])[0]
          );
        }

        // 3) Vygenerovať budúce plánované termíny až do júna (bez 7,8)
        if (scheduleDay) {
          const today = new Date();

          // školský rok: ak aktuálny mesiac >= september (8), končí 30.6. budúci rok, inak tento rok
          const schoolYearEndYear =
            today.getMonth() >= 8
              ? today.getFullYear() + 1
              : today.getFullYear();
          const end = new Date(schoolYearEndYear, 5, 30); // 30. jún

          // základ od ktorého generujeme: deň PO poslednej lekcii podľa najväčšieho ID
          const base = lastIsoByMaxId
            ? new Date(lastIsoByMaxId)
            : new Date(today);

          // nájdi ďalší výskyt požadovaného weekday prísne PO base
          let cursor = new Date(base);
          cursor.setDate(cursor.getDate() + 1);
          while (weekdayISO(cursor) !== scheduleDay) {
            cursor.setDate(cursor.getDate() + 1);
          }

          // nikdy negeneruj do minulosti – posuň na >= dnes
          while (cursor < today) {
            cursor.setDate(cursor.getDate() + 7);
          }

          // iteruj po týždňoch až do konca júna
          while (cursor <= end) {
            const month = cursor.getMonth() + 1; // 1..12
            if (month !== 7 && month !== 8) {
              const iso = formatISO(cursor);
              if (!statusMap[iso]) {
                statusMap[iso] = "scheduled";
              }
            }
            cursor.setDate(cursor.getDate() + 7);
          }
        }

        this.statusDays = statusMap;
        this.scheduleDay = scheduleDay; // môže byť null, vtedy sa len zobrazia statusy z API
      } catch (e) {
        this.error = e.message || String(e);
        // nechaj komponent fungovať aj bez dát
      } finally {
        this.loading = false;
      }
    },
    prevMonth() {
      if (this.currentMonth === 0) {
        this.currentMonth = 11;
        this.currentYear--;
      } else {
        this.currentMonth--;
      }
    },
    nextMonth() {
      if (this.currentMonth === 11) {
        this.currentMonth = 0;
        this.currentYear++;
      } else {
        this.currentMonth++;
      }
    },
    getDayClass(day) {
      if (!day.date) return "empty";
      const classes = [];

      // zobraz len dni, ktoré máme priamo v statusDays (z API alebo umelo vygenerované do budúcnosti)
      const status = this.statusDays[day.date];
      if (status) {
        classes.push(status);
      }

      // zelený background len pre aktuálny deň
      const todayIso = formatISO(new Date());
      if (day.date === todayIso) classes.push("thisWeek");

      return classes.join(" ");
    },
  },
};
</script>

<style lang="scss" scoped>
.calendar {
  background: rgba($color: #fef35a, $alpha: 0.7);
  padding: 2em 1em;
  border-radius: 6em 3em;
  box-shadow: inset 0 4px 4px rgba($color: #000000, $alpha: 0.25),
    (4px 4px 10px rgba($color: #000000, $alpha: 0.25));
  width: 80%;
  font-family: "Bahnschrift", sans-serif;
  box-sizing: border-box;
}
.calendar-header {
  display: flex;
  justify-content: space-around;
  align-items: flex-start;
  padding: 0 1.3em 0 2em;

  .monthName {
    font-family: Bahnschrift, sans-serif;
    font-size: 1.9em;
    font-weight: 700;
    margin: 0 0 0.4em;
  }

  .month-navigation {
    display: flex;
    gap: 1em;

    button {
      background: #00913f;
      border: none;
      border-radius: 99999px;
      padding: 0;
      width: 1.3em;
      box-shadow: (2px 2px 3.5px rgba($color: #000000, $alpha: 0.25));
      p {
        font-size: 1.1em;
        color: #fef35a;
      }

      cursor: pointer;
    }
  }
}
.calendar-grid {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  text-align: center;
  align-items: center;
  justify-items: center;
  row-gap: 0.18em;
}
.day-name {
  font-weight: bold;
  font-size: 1.7em;
}
.day {
  padding: 0.2em;
  border-radius: 0.5em;
  font-weight: 500;
  font-size: 1.7em;
  font-family: "Bahnschirft", sans-serif;
  cursor: pointer;
}
.day.empty {
  background: transparent;
}
.day.thisWeek {
  background: #90ca50;
  border: 8px;
  box-shadow: inset 1px 1px 12.5px rgba(0, 0, 0, 0.25),
    2px 2px 7.5px rgba(0, 0, 0, 0.25);
  width: 70%;
}
.day.scheduled {
  font-weight: bold;
  color: #00913f;
  font-family: "Adumu", sans-serif;
}
.day.absolved {
  font-weight: bold;
  color: #00913f;
  font-family: "Adumu", sans-serif;
}
.day.cancelled_admin {
  font-weight: bold;
  color: #f86e5c !important;
  font-family: "Adumu", sans-serif;
}
.day.cancelled_parent {
  font-weight: bold;
  color: #f4ab2a !important;
  font-family: "Adumu", sans-serif;
}

@media screen and (max-width: 1024px) {
  .calendar {
    width: 50%;
    margin-inline: auto;
    margin-block: 2em;
  }
}

@media screen and (max-width: 820px) {
  .calendar {
    width: 85%;
  }
}
</style>
