<template>
  <div class="lesson-start">
    <div class="lesson-info">
      <div class="lesson-text">
        <h3>Lekcia začína:</h3>
      </div>
      <div
        class="lesson-time"
        :style="
          selected &&
          (selected.computedStatus === 'cancelled_admin' ||
            selected.computedStatus === 'cancelled_parent')
            ? { color: '#F4AB2A' }
            : {}
        "
      >
        {{ displayTime }}
      </div>
    </div>
    <div class="lesson-footer">
      <p class="note">
        Nemôžeš sa zúčastniť lekcie?<br />Dajte nám vedieť vopred
      </p>
      <button v-if="showActionButton" class="button" @click="onActionClick">
        <p>{{ actionLabel }}</p>
      </button>
    </div>
  </div>
</template>

<script>
function skDateToISO(skDate) {
  if (!skDate || typeof skDate !== "string") return null;
  const [dd, mm, yyyy] = skDate.split(".");
  if (!dd || !mm || !yyyy) return null;
  return `${yyyy}-${mm.padStart(2, "0")}-${dd.padStart(2, "0")}`;
}
function formatHour(hhmm) {
  if (!hhmm) return "--:--";
  const s = String(hhmm);
  const h = s.slice(0, -2) || "0";
  const m = s.slice(-2);
  return `${String(Number(h)).padStart(2, "0")}:${String(Number(m)).padStart(
    2,
    "0"
  )}`;
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
export default {
  name: "LessonStart",
  data() {
    return {
      loading: false,
      error: null,
      selected: null, // { id, dateISO, hour, computedStatus, predicted }
      lastLessonHour: null, // string like '1300' from the most recent past lesson
    };
  },
  computed: {
    displayTime() {
      if (!this.selected) return "--:--";
      return formatHour(this.selected.hour || this.lastLessonHour);
    },
    showActionButton() {
      // tlačidlo sa zobrazuje len pre REALNU nasledujúcu lekciu (nie predikciu)
      if (!this.selected || this.selected.predicted) return false;
      // tlačidlo sa nezobrazí ak je lekcia zrušená (už je zrušená)
      if (
        this.selected.computedStatus === "cancelled_admin" ||
        this.selected.computedStatus === "cancelled_parent"
      ) {
        return false;
      }
      return true;
    },
    actionLabel() {
      if (!this.selected || this.selected.predicted) return "";
      return "Zrušiť";
    },
  },
  mounted() {
    this.load();
  },
  methods: {
    async load() {
      this.loading = true;
      this.error = null;
      try {
        const userId =
          this.$store?.state?.user?.id || this.$store?.state?.userId;
        const apiBase = this.$store?.state?.api;
        if (!userId || !apiBase)
          throw new Error("Chýba user.id alebo api base v store");
        const url = `${apiBase}/edupage/loadMyLessonAll.php?id=${userId}`;
        const res = await fetch(url);
        const json = await res.json();
        const items = Array.isArray(json?.data) ? json.data : [];

        // prepočítaj computedStatus per študent (true/false/null) s fallbackom na lesson.status
        const now = new Date();
        const enriched = items
          .map((it) => {
            const iso = skDateToISO(it.date);
            const dateObj = iso ? new Date(iso) : null;
            let status = null;
            const students = parseStudentArray(it.student);
            const me = students.find(
              (s) => String(s.student_id) === String(userId)
            );
            if (
              me &&
              Object.prototype.hasOwnProperty.call(me, "attendance_status")
            ) {
              const att = me.attendance_status; // true/false/null
              if (att === false) status = "absolved";
              else if (att === true) status = "cancelled_parent";
              else if (att === null) status = "cancelled_admin";
            }
            if (!status && typeof it.status === "string") {
              if (it.status === "cancelled_admin") status = "cancelled_admin";
              else if (it.status === "cancelled_parent")
                status = "cancelled_parent";
              else if (it.status === "planned") status = "scheduled";
            }
            return {
              ...it,
              dateISO: iso,
              dateObj,
              computedStatus: status || null,
            };
          })
          .filter((x) => !!x.dateObj);

        // zoradenie podľa dátumu a id (ako sekundárny kľúč)
        enriched.sort(
          (a, b) => a.dateObj - b.dateObj || Number(a.id) - Number(b.id)
        );

        // nájdi najnovšiu minulosť (pre predikciu času) a najbližšiu budúcnosť
        let lastPast = null;
        let nextFuture = null;
        for (const it of enriched) {
          if (it.dateObj < now) lastPast = it;
          else {
            nextFuture = it;
            break;
          }
        }

        this.lastLessonHour = lastPast?.hour || null;

        if (nextFuture) {
          // ak je v júli/auguste, ignoruj (nemá sa zobrazovať)
          const m = nextFuture.dateObj.getMonth() + 1;
          if (m === 7 || m === 8) {
            this.selected = lastPast
              ? {
                  id: null,
                  dateISO: null,
                  hour: lastPast.hour,
                  computedStatus: null,
                  predicted: true,
                }
              : null;
          } else {
            this.selected = {
              id: nextFuture.id,
              dateISO: nextFuture.dateISO,
              hour: nextFuture.hour,
              computedStatus: nextFuture.computedStatus,
              predicted: false,
            };
          }
        } else if (lastPast) {
          // žiadna budúca lekcia – ukáž pravdepodobný čas podľa poslednej
          this.selected = {
            id: null,
            dateISO: null,
            hour: lastPast.hour,
            computedStatus: null,
            predicted: true,
          };
        } else {
          this.selected = null;
        }
      } catch (e) {
        this.error = e.message || String(e);
      } finally {
        this.loading = false;
      }
    },
    async onActionClick() {
      if (!this.selected || this.selected.predicted) return;
      const apiBase = this.$store?.state?.api;
      if (!apiBase || !this.selected.id) return;

      try {
        const url = `${apiBase}/edupage/cancelLesson.php?id=${this.selected.id}`;
        await fetch(url, { method: "GET" });
        await this.load();
      } catch (e) {
        console.error("Lesson toggle error", e);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.lesson-start {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  background: #f9f186;
  padding: 2em 3em;
  border-radius: 3em;
  filter: drop-shadow(5px 5px 5px rgba(0, 0, 0, 0.2));
  box-shadow: inset 2px 5px 8px rgb(0 0 0 / 13%);
  width: 80%;
  box-sizing: border-box;
}

.lesson-info {
  display: flex;
  align-items: center;
  gap: 1.5em;
  justify-content: space-between;
}

.lesson-text h3 {
  font-size: 1.5em;
  font-weight: 700;
  margin: 0;
  font-family: "Bahnschrift", sans-serif;
  text-align: left;
}

.lesson-time {
  font-size: 2em;
  font-weight: 700;
  color: #00913f;
  font-family: "Adumu", sans-serif;
}

.lesson-footer {
  display: flex;
  gap: 1em;
  justify-content: space-between;
}

.lesson-footer .note {
  font-size: 1em;
  text-align: left;
}

.button {
  font-size: 0.9em;
  font-family: "Adumu", sans-serif;
}

@media screen and (max-width: 1024px) {
  .lesson-start {
    width: 40%;
    margin-inline: auto;
  }
}

@media screen and (max-width: 820px) {
  .lesson-start {
    width: 75%;
  }
}
</style>
