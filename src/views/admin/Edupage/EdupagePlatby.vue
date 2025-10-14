<template>
  <section class="computer zapisy" :id="$route.name">
    <div class="scroll">
      <h1>Platobné príkazy</h1>

      <KalendarDatumHeader
        :showFilter="showFilter"
        :showAllFilter="true"
        :showDays="true"
        :hide-days="true"
        :allowedDaysFull="povoleneDni"
        :allowedTimesByDay="povoleneCasyPodlaDna"
        @day-changed="onDayChanged"
        @date-changed="onDateChanged"
        @month-changed="onMonthChanged"
      />

      <AdminPlatobnePrikazy
        :month="currentMonth"
        :year="currentYear"
        :teacherId="selectedTeacher"
        :day="currentDay"
        :key="
          currentDay +
          '-' +
          currentMonth +
          '-' +
          currentYear +
          '-' +
          selectedTeacher
        "
      />

      <div class="teachers_counts">
        <p
          v-for="teacher in teachers"
          :key="teacher.id"
          :class="{ selected: selectedTeacher === teacher.id }"
          @click="selectTeacher(teacher.id)"
        >
          {{ teacher.name }}: {{ teacher.count }}
        </p>
      </div>
    </div>
  </section>
</template>

<script>
import AdminPlatobnePrikazy from "@/components/Edupage/Platby/AdminPlatobnePrikazy.vue";
import { defineAsyncComponent } from "vue";
import axios from "axios";

const KalendarDatumHeader = defineAsyncComponent(() =>
  import("@/components/Edupage/KalendarDatumHeader.vue")
);

const LS_KEY_DATE = "edp.selectedDateISO";
const LS_KEY_DAY = "edp.currentDay";

export default {
  components: { KalendarDatumHeader, AdminPlatobnePrikazy },
  data() {
    const today = new Date();
    return {
      showFilter: false,
      povoleneDni: [],
      povoleneCasyPodlaDna: {},
      dostupnost: [],
      acLoads: {},
      slotCache: new Map(),
      teacherCountsGlobal: null,
      currentMonth: today.getMonth() + 1,
      currentYear: today.getFullYear(),
      currentDay: localStorage.getItem(LS_KEY_DAY) || "",
      selectedDateISO: localStorage.getItem(LS_KEY_DATE) || null,
      teachers: [
        { id: "59", name: "Juraj Kvočka", count: 0 },
        { id: "54", name: "Andrej Trnka", count: 0 },
        { id: "607", name: "Matej Kondela", count: 0 },
        { id: "53", name: "Ľuboš Kukla", count: 0 },
      ],
      selectedTeacher: "54",
    };
  },
  computed: {
    apiBase() {
      return (this.$store?.state?.api || "").replace(/\/+$/, "") + "/edupage/";
    },
    teacherId() {
      const allowed = [59, 54, 607, 945, 53];
      const id = Number(this.$store?.state?.user?.id || 0);
      return allowed.includes(id) ? id : null;
    },
  },
  methods: {
    selectTeacher(id) {
      this.selectedTeacher = id;
    },
    async safeFetch(url, init = {}, key = null) {
      if (key) {
        this.acLoads[key]?.abort?.();
        this.acLoads[key] = new AbortController();
        init.signal = this.acLoads[key].signal;
      }
      return fetch(url, { credentials: "include", ...init });
    },
    async parseMaybeJSON(res) {
      const t = await res.text();
      try {
        return JSON.parse(t);
      } catch {
        return null;
      }
    },
    slotyStvrtHod(from, to) {
      if (!from || !to) return [];
      const key = from + ">" + to;
      const cached = this.slotCache.get(key);
      if (cached) return cached;
      const [fh, fm] = String(from)
        .split(":")
        .map((n) => parseInt(n, 10));
      const [th, tm] = String(to)
        .split(":")
        .map((n) => parseInt(n, 10));
      if (![fh, fm, th, tm].every(Number.isFinite)) return [];
      const out = [];
      let cur = fh * 60 + fm;
      const end = th * 60 + tm;
      while (cur < end) {
        const h = String(Math.floor(cur / 60)).padStart(2, "0");
        const m = String(cur % 60).padStart(2, "0");
        out.push(`${h}:${m}`);
        cur += 15;
      }
      this.slotCache.set(key, out);
      return out;
    },
    async nacitajDostupnost() {
      if (!this.teacherId) {
        this.dostupnost = [];
        this.povoleneDni = [];
        this.povoleneCasyPodlaDna = {};
        this.showFilter = false;
        return;
      }
      const res = await this.safeFetch(
        this.apiBase + "loadCalendarCustom.php?teacher_id=" + this.teacherId,
        {},
        "avail"
      );
      const out = await this.parseMaybeJSON(res);
      const rows = Array.isArray(out?.data)
        ? out.data
        : Array.isArray(out)
        ? out
        : [];
      const myTid = String(this.teacherId);
      const parsed = [];
      for (const r of rows) {
        const tid = String(r.teacher_id ?? r.teacherId ?? r.teacherID ?? "");
        if (tid !== myTid) continue;
        let d = r?.data;
        if (typeof d === "string") {
          try {
            d = d.trim() ? JSON.parse(d) : null;
          } catch {
            d = null;
          }
        }
        if (!d) continue;
        parsed.push({
          id: Number(r.id || 0) || null,
          den: d.den || d.day || "",
          miesto: d.miesto || d.location || "",
          od: d.od || d.from || "",
          do: d.do || d.to || "",
        });
      }
      this.dostupnost = parsed;
      this.povoleneDni = Array.from(new Set(parsed.map((x) => x.den))).filter(
        Boolean
      );
      const map = {};
      for (const d of this.povoleneDni) map[d] = [];
      for (const rec of parsed) {
        if (!map[rec.den]) map[rec.den] = [];
        map[rec.den].push(...this.slotyStvrtHod(rec.od, rec.do));
      }
      Object.keys(map).forEach(
        (k) => (map[k] = Array.from(new Set(map[k])).sort())
      );
      this.povoleneCasyPodlaDna = map;
      this.showFilter = this.povoleneDni.length > 0;
    },
    onDayChanged(payload) {
      console.log(
        "onDayChanged called with",
        payload,
        "currentDay before:",
        this.currentDay
      );
      if (!payload) return;
      if (typeof payload === "string") {
        this.currentDay = payload;
      } else if (payload instanceof Date) {
        this.currentDay = this.dayNameSkFromDate(payload);
      } else if (payload.dayName) {
        this.currentDay = payload.dayName;
      }
      console.log("currentDay after:", this.currentDay);
      localStorage.setItem(LS_KEY_DAY, this.currentDay || "");
    },
    onDateChanged(iso) {
      if (typeof iso === "string" && /^\d{4}-\d{2}-\d{2}$/.test(iso)) {
        this.selectedDateISO = iso;
        const d = new Date(iso);
        this.currentMonth = d.getMonth() + 1;
        this.currentYear = d.getFullYear();
        localStorage.setItem(LS_KEY_DATE, iso);
      }
    },
    onMonthChanged({ month, year }) {
      this.currentMonth = month;
      this.currentYear = year;
    },
    async loadTeacherCountsOnce() {
      if (this.teacherCountsGlobal) return;
      const apiBase = (this.$store?.state?.api || "").replace(/\/+$/, "");
      const ids = this.teachers.map((t) => Number(t.id));
      const counts = {};

      await Promise.allSettled(
        ids.map(async (id) => {
          try {
            const r = await axios.get(
              `${apiBase}/edupage/countTeacherStudents.php?id=${id}`
            );
            if (r?.data?.status === "Request succesfull") {
              const d = r?.data?.data;
              let total = 0;
              if (d && typeof d === "object") {
                const solo = Number(d.solo) || 0;
                const duo = Number(d.duo) || 0;
                total = solo + duo;
              } else {
                total = Number(d) || 0;
              }
              counts[id] = total;
            } else {
              counts[id] = 0;
            }
          } catch {
            counts[id] = 0;
          }
        })
      );

      this.teacherCountsGlobal = counts;
      // zapíš počty do `teachers`
      this.teachers = this.teachers.map((t) => ({
        ...t,
        count: counts[Number(t.id)] ?? 0,
      }));
    },
  },
  async mounted() {
    await this.nacitajDostupnost();
    await this.loadTeacherCountsOnce();
  },
  watch: {
    "$store.state.user"(v, o) {
      if (v?.id !== o?.id) this.nacitajDostupnost();
    },
  },
};
</script>

<style lang="scss" scoped>
h1 {
  text-align: left;
  font-size: 3.125em;
  font-weight: 400;
  line-height: 115%;
  letter-spacing: 0.25rem;
  z-index: 1;
  margin: 0;
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 3px rgba(0, 0, 0, 0.25);
  font-size: 5vw;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0.1em 0.15em 0.1em;
}

.teachers_counts {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 3em;
  gap: 1em;

  p {
    font-size: 1.3em;
    padding: 0.3em;
    border-radius: 13px;
    cursor: pointer;
    transition: background 0.2s;
  }

  p.selected {
    background-color: #fef35a;
    font-weight: bold;
    border: 2px solid #fef35a;
  }

  p:hover {
    background-color: #fef35a;
  }
}
</style>
