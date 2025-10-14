<template>
  <div class="formular">
    <h5>Zhrnutie dnešnej {{ dual ? "duo" : "sólo" }} lekcie</h5>

    <div v-if="dual" class="dual">
      <div class="student-switch">
        <button
          type="button"
          class="option"
          :class="{ active: activeStudent === 1 }"
          @click="setActive(1)"
        >
          {{ name }}
        </button>
        <button
          type="button"
          class="option"
          :class="{ active: activeStudent === 2 }"
          @click="setActive(2)"
        >
          {{ name2 }}
        </button>
      </div>
    </div>
    <p v-else class="meno">{{ activeStudentName }}</p>

    <p class="text">
      Vyplň, ako prebehla dnešná lekcia. Zaznač dochádzku, hodnotenie a pridaj
      piesne alebo poznámku.
    </p>

    <div class="pridanie">
      <div class="boxik forma">
        <p class="nadpis">Dochádzka žiaka:</p>

        <div class="dochadzka">
          <div
            @click="dochadzka = false"
            :class="{ active: dochadzka === false }"
            class="button"
          >
            <p>Absolvoval</p>
          </div>
        </div>
        <div class="dochadzka">
          <div
            @click="dochadzka = true"
            :class="{ active: dochadzka === true }"
            class="button"
          >
            <p>Neabsolvoval</p>
          </div>
        </div>
        <div class="dochadzka ucitel">
          <div
            @click="dochadzka = null"
            :class="{ active: dochadzka === null }"
          >
            <p>
              {{
                dochadzka === null
                  ? "Hodina zrušená učiteľom"
                  : "Zrušiť žiakovi lekciu"
              }}
            </p>
          </div>
        </div>
      </div>
      <div class="boxik">
        <p class="nadpis">Poznámka k hodine:</p>

        <textarea
          name="poznámka"
          id="poznamka"
          rows="7"
          v-model="noteText"
          :placeholder="`Poznámka pre ${activeStudentName}`"
        ></textarea>
      </div>
    </div>

    <div class="stars" role="radiogroup" aria-label="Hodnotenie hviezdičkami">
      <button
        v-for="i in 5"
        :key="i"
        type="button"
        class="star-btn"
        :aria-checked="rating >= i"
        role="radio"
        @click="setRating(i)"
        @dblclick.stop.prevent="clearRating()"
        :title="'Klik pre ' + i + '★, dvojklik vymaže'"
      >
        <img
          :src="rating >= i ? filledStar : emptyStar"
          :alt="`${i} hviezdička`"
        />
      </button>
    </div>
    <div class="button Adumu" @click="saveLesson">Zapísať</div>
    <p v-if="saveMessage" class="save-message">{{ saveMessage }}</p>
  </div>
</template>

<script>
import axios from "axios";
import filledStar from "@/assets/images/gallery/hviezda.svg";
import emptyStar from "@/assets/images/gallery/hviezda-prazdna.png";

export default {
  props: {
    summary: {
      type: Object,
      default: () => ({ id: null, students: [] }),
    },
    selectedDate: {
      type: String, // očakávame formát ako v DB, napr. "02.09.2025"
      default: "",
    },
  },
  data() {
    return {
      activeStudent: 1,
      filledStar,
      emptyStar,

      lessonId: null,
      lessonMeta: {},
      studentMap: {},
      saveMessage: "",
    };
  },
  computed: {
    effectiveDate() {
      // priorita: prop -> summary.date -> lessonMeta.date
      return (
        this.selectedDate || this.summary.date || this.lessonMeta.date || ""
      );
    },
    dual() {
      return this.summary.students.length === 2;
    },
    name() {
      return this.summary.students[0]?.name || "Žiak 1";
    },
    name2() {
      return this.summary.students[1]?.name || "Žiak 2";
    },
    activeStudentId() {
      return this.summary.students[this.activeStudent - 1]?.id ?? null;
    },
    activeStudentName() {
      return this.dual
        ? this.activeStudent === 1
          ? this.name
          : this.name2
        : this.name;
    },
    noteText: {
      get() {
        return this.studentMap[this.activeStudentId]?.info ?? "";
      },
      set(v) {
        if (!this.activeStudentId) return;
        this.ensureStudent(this.activeStudentId);
        this.studentMap[this.activeStudentId].info = v;
      },
    },
    rating: {
      get() {
        return this.studentMap[this.activeStudentId]?.stars ?? 0;
      },
      set(v) {
        if (!this.activeStudentId) return;
        this.ensureStudent(this.activeStudentId);
        this.studentMap[this.activeStudentId].stars = v;
      },
    },
    dochadzka: {
      get() {
        return this.studentMap[this.activeStudentId]?.attendance_status ?? null;
      },
      set(v) {
        if (!this.activeStudentId) return;
        this.ensureStudent(this.activeStudentId);
        this.studentMap[this.activeStudentId].attendance_status = v;
      },
    },
  },
  methods: {
    setActive(student) {
      this.activeStudent = student;
    },
    ensureStudent(studentId) {
      if (!this.studentMap[studentId]) {
        this.$set(this.studentMap, studentId, {
          student_id: studentId,
          stars: 0,
          note: "",
          info: "",
          attendance_status: null,
          played: 0,
        });
      }
    },
    async fetchLesson() {
      try {
        const baseUrl = `${this.$store.state.api}/edupage/loadLesson.php?calendar_id=${this.summary.id}`;
        const url = this.effectiveDate
          ? `${baseUrl}&date=${encodeURIComponent(this.effectiveDate)}`
          : baseUrl;
        const { data } = await axios.get(url);
        const lessons = Array.isArray(data?.data) ? data.data : [];

        const main = lessons.reduce((acc, cur) => {
          if (!acc) return cur;
          return +cur.id > +acc.id ? cur : acc;
        }, null);

        if (!main) {
          this.lessonId = null;
          this.lessonMeta = {
            calendar_id: this.summary.id,
            teacher_id: this.summary.teacher_id || "",
            date: this.effectiveDate || "",
            hour: this.summary.hour || "",
            status: this.summary.status || "planned",
          };
          const map = {};
          for (const st of this.summary.students) {
            map[st.id] = {
              student_id: st.id,
              stars: 0,
              note: "",
              info: "",
              attendance_status: null,
              played: 0,
            };
          }
          this.studentMap = map;
          return;
        }

        this.lessonId = +main.id;
        this.lessonMeta = {
          calendar_id: main.calendar_id,
          teacher_id: main.teacher_id,
          date: main.date,
          hour: main.hour,
          status: main.status,
        };

        let students = [];
        try {
          students = JSON.parse(main.student || "[]");
        } catch {
          students = [];
        }

        const map = {};
        for (const s of students) {
          if (!s || !s.student_id) continue;
          map[s.student_id] = {
            student_id: +s.student_id,
            stars: +s.stars || 0,
            note: s.note || "",
            info: s.info || "",
            attendance_status: s.attendance_status ?? null,
            played: +s.played || 0,
          };
        }
        for (const st of this.summary.students) {
          if (!map[st.id]) {
            map[st.id] = {
              student_id: st.id,
              stars: 0,
              note: "",
              info: "",
              attendance_status: null,
              played: 0,
            };
          }
        }
        this.studentMap = map;
      } catch (err) {
        console.error("Chyba pri fetchLesson:", err);
        this.lessonId = null;
        this.lessonMeta = {
          calendar_id: this.summary.id,
          teacher_id: this.summary.teacher_id || "",
          date: this.effectiveDate || "",
          hour: this.summary.hour || "",
          status: this.summary.status || "planned",
        };
        const map = {};
        for (const st of this.summary.students) {
          map[st.id] = {
            student_id: st.id,
            stars: 0,
            note: "",
            info: "",
            attendance_status: null,
            played: 0,
          };
        }
        this.studentMap = map;
      }
    },
    setRating(val) {
      this.rating = val;
    },
    clearRating() {
      this.rating = 0;
    },
    async saveLesson() {
      const studentsPayload = Object.values(this.studentMap).map((s) => ({
        student_id: s.student_id,
        stars: s.stars || 0,
        note: s.note || "",
        info: s.info || "",
        attendance_status: s.attendance_status ?? null,
        played: s.played || 0,
      }));

      // Urči status podľa dochádzky aktívneho študenta
      const active = this.studentMap[this.activeStudentId] || null;
      let computedStatus =
        this.lessonMeta.status || this.summary.status || "planned";
      if (active) {
        if (active.attendance_status === false)
          computedStatus = "absolved"; // absolvoval
        else if (active.attendance_status === true)
          computedStatus = "cancelled_parent"; // neabsolvoval
        else if (active.attendance_status === null)
          computedStatus = "cancelled_admin"; // zrušené učiteľom
      }

      const form = new URLSearchParams();
      form.append("id", String(this.lessonId));
      form.append("calendar_id", String(this.lessonMeta.calendar_id ?? ""));
      form.append(
        "teacher_id",
        String(this.lessonMeta.teacher_id || this.summary.teacher_id || "")
      );
      form.append("student", JSON.stringify(studentsPayload));
      form.append("date", String(this.effectiveDate || ""));
      form.append(
        "hour",
        String(this.lessonMeta.hour || this.summary.hour || "")
      );
      form.append("status", computedStatus);

      try {
        const url = `${this.$store.state.api.replace(
          /\/+$/,
          ""
        )}/edupage/editLesson.php`;
        const response = await axios.post(url, form, {
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
        });
        if (response.data?.status === "Request succesfull") {
          this.saveMessage = "Lekcia bola úspešne hodnotená.";
        } else {
          this.saveMessage = "Nepodarilo sa uložiť lekciu.";
        }
      } catch (err) {
        console.error(err);
        this.saveMessage = "Chyba pri ukladaní lekcie.";
      }
    },
  },
  watch: {
    summary: {
      immediate: true,
      deep: true,
      async handler(newVal) {
        if (newVal?.students?.length) {
          this.activeStudent = 1;
          await this.fetchLesson();
        }
      },
    },
  },
};
</script>

<style lang="scss" scoped>
h5 {
  font-size: 3.3em;
  margin: 0.5em auto;
}

.formular {
  max-width: 60em;

  margin: auto;
}

.text {
  font-size: 1.9em;
  margin: 1em auto;
}

.meno {
  font-size: 2.4em;
  font-weight: 400;
}

select {
  appearance: none; /* zruší default šípku pre Chrome/Safari */
  -moz-appearance: none; /* pre Firefox */
  -webkit-appearance: none; /* pre staršie Safari */
  background-image: url("@/assets/images/icons/rozbalenie.png"); /* sem tvoj obrázok */
  background-repeat: no-repeat;
  background-position: right 1rem center; /* zarovnanie šípky doprava */
  background-size: 0.8em; /* veľkosť šípky */

  padding: 0.2em 2em 0.2em 1.2em;
  border: none;
  font-size: 1em;

  background-color: #90ca50;
  box-shadow: 0.1em 0.1em 1em #00000040;
  border-radius: 1em;
  min-height: 2em;

  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;

  transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;

  &:hover {
    background-color: #90ca50;
    transform: translateY(-3px);
    cursor: pointer;
    box-shadow: 0.1em 0.1em 0.5em #00000040;
  }

  &:active {
    transform: translateY(1px);
    background-color: #ffeb3b; // Farba pri kliknutí
    filter: brightness(0.95);
  }
}

.active {
  background: #90ca50;
}

.ucitel > div {
  margin: auto;
}

.ucitel .active {
  background: transparent;
  font-weight: 900;
  text-decoration: underline;
}

select:focus {
  outline: none;
  box-shadow: none;
}

.pridanie {
  display: flex;
  justify-content: space-around;
  margin: 4em 0.3em;
}

.nadpis {
  font-size: 1.8em;
  text-align: left;
  margin: 0.5em 0;
}

.dochadzka {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 2em;
  cursor: pointer;

  &:hover {
    text-decoration: underline;
  }
}

.button {
  width: fit-content;
  margin: auto;
}

.dochadzka .button {
  margin: 0;
  border-radius: 0.7em;
  font-size: 1.1em;
  width: fit-content;
  margin: auto;

  width: 9em;
  margin-bottom: 1em;

  p {
    margin: auto;
  }
}
.ziak {
  display: flex;
  gap: 1em;
  justify-content: space-between;
}

textarea {
  border-radius: 1.0625em;
  background: #90ca50;
  box-shadow: -7px 5px 15px 0px rgba(0, 0, 0, 0.25) inset,
    0px 4px 4px 0px rgba(0, 0, 0, 0.25);
  padding: 0.65em 5%;
  margin: 0.3em auto 1.3em;
  width: 100%;
  font-size: 1em;
}

.dual p {
  background: #fef35a;
  padding: 1em 1.5em;
}

.student-switch {
  display: flex;
  gap: 1em;
  justify-content: center;
  margin: 0.5em 0 1em;
}
.student-switch .option {
  background: #d9d9d9;
  border: none;
  border-radius: 0.7em;
  padding: 0.4em 1em;
  font-size: 1.2em;
  cursor: pointer;
  box-shadow: 0.1em 0.1em 1em #00000040;
  transition: background-color 0.2s, transform 0.2s, box-shadow 0.2s;
}
.student-switch .option:hover {
  transform: translateY(-2px);
}
.student-switch .option.active {
  background: #90ca50;
  font-weight: 700;
}

.stars {
  display: flex;
  align-items: center;
  gap: 0.5em;
  margin: 1em 0 2em;
  justify-content: center;
}
.star-btn {
  padding: 0;
  margin: 0;
  background: transparent;
  border: none;
  cursor: pointer;
  line-height: 0;
}
.star-btn:focus-visible {
  outline: 2px solid #90ca50;
  outline-offset: 2px;
  border-radius: 4px;
}
.stars img {
  width: 3em;
  height: 3em;
  display: block;
}
.clear-rating {
  margin-left: 0.8em;
  background: #d9d9d9;
  border: none;
  border-radius: 0.5em;
  padding: 0.2em 0.6em;
  cursor: pointer;
  box-shadow: 0.1em 0.1em 0.6em #00000033;
}
.clear-rating:hover {
  transform: translateY(-1px);
}
.save-message {
  margin-top: 1em;
  font-size: 1.2em;
  font-weight: 600;
  color: #00913f;
  text-align: center;
}
</style>
