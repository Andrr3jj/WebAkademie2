<template>
  <div class="calendar-flow" @click="closeMenu">
    <div class="time">
      <p v-for="t in times" :key="t.from">{{ t.from }}</p>
    </div>

    <div class="flow">
      <div
        v-for="z in zaznamy"
        :key="`${z.id}-${z.cas}-${(z.studentIds || []).join('_')}`"
        class="lecture"
        :class="[z.stavClass, { noActions: !z.hasLesson }]"
        :style="[
          {
            marginTop: z.offsetPx + 'px',
            marginBottom: '0px',
            height: z.heightPx + 'px',
            boxSizing: 'border-box',
          },
          z.bg ? { background: z.bg } : {},
        ]"
        @click.stop="openInfoMenu(z)"
        @contextmenu.stop.prevent="(e) => openMenu(e, z)"
      >
        <div class="profile">
          <img :src="z.students[0]?.photo || defaultAvatar" alt="Profil" />
          <img
            v-if="z.isDuo"
            :src="z.students[1]?.photo || defaultAvatar"
            alt="Profil"
          />
        </div>

        <div class="info">
          <p class="name">
            {{ z.students[0]?.name || "Neznámy žiak" }}
            <span v-if="z.isDuo && z.students[1]"
              >s {{ z.students[1]?.name }}</span
            >
          </p>
          <p class="start">
            Začiatok hodiny: &nbsp; {{ z.cas }} - {{ z.koniec }}
          </p>
        </div>

        <div class="action">
          <p class="button Adumu" @click.stop="openSummary(z)">
            Zapísať lekciu
          </p>
          <div
            class="addZapis button"
            role="button"
            tabindex="0"
            @click.stop="openAddZapis(z)"
          >
            <img src="@/assets/images/icons/addZapis.svg" alt="" />
          </div>
        </div>
      </div>
      <div v-if="showNowIndicator" class="now-indicator" :style="nowStyle">
        <span class="label">{{ currentTime }}</span>
        <span class="dot" aria-hidden="true"></span>
        <span class="line" aria-hidden="true"></span>
      </div>
    </div>

    <div
      v-if="menu.show && menu.target"
      class="ctx"
      :style="{ left: menu.x + 'px', top: menu.y + 'px' }"
      @click.stop
    >
      <button
        class="ctx-item danger"
        :disabled="menu.busy || !menu.target.id"
        @click="deleteSelected"
      >
        {{ menu.busy ? "Mazem…" : "Zmazať hodinu" }}
      </button>
      <p v-if="menu.error" class="ctx-err">{{ menu.error }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "KalendarFlow",
  emits: ["open-summary", "open-add-zapis", "open-context"],
  inject: ["getAllowedTimesByDay"],
  props: {
    predvolenyDen: { type: String, default: "" },
    intervalMinutes: { type: Number, default: 30 },
    rowHeight: { type: Number, default: 40 },
    selectedDate: { type: String, default: "" },
  },
  data() {
    return {
      currentTime: new Date().toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      }),
      currentTimeInterval: null,
      hodiny: [],
      lessonsByCalendar: {},
      measuredRowHeight: null,
      defaultAvatar: require("@/assets/images/vyzvy/prva-lekcia.png"),
      menu: { show: false, x: 0, y: 0, target: null, busy: false, error: "" },
    };
  },
  computed: {
    apiRoot() {
      return (this.$store?.state?.api || "").replace(/\/+$/, "");
    },
    apiEdupage() {
      return this.apiRoot + "/edupage/";
    },
    apiInfo() {
      return this.apiRoot + "/user/info/";
    },
    todayDMY() {
      const t = new Date();
      const dd = String(t.getDate()).padStart(2, "0");
      const mm = String(t.getMonth() + 1).padStart(2, "0");
      const yyyy = t.getFullYear();
      return `${dd}.${mm}.${yyyy}`;
    },
    teacherId() {
      const allowed = [59, 54, 607, 945, 53];
      const id = Number(this.$store?.state?.user?.id || 0);
      return allowed.includes(id) ? id : null;
    },
    isPastSelected() {
      if (!this.selectedDate) return false;
      const sel = this.parseDMY(this.selectedDate);
      if (!sel) return false;
      const today = new Date();
      sel.setHours(0, 0, 0, 0);
      today.setHours(0, 0, 0, 0);
      return sel.getTime() < today.getTime();
    },
    denNaZobrazenie() {
      if (this.predvolenyDen) return this.predvolenyDen;
      const dni = [
        "Pondelok",
        "Utorok",
        "Streda",
        "Štvrtok",
        "Piatok",
        "Sobota",
        "Nedeľa",
      ];
      return dni[new Date().getDay()];
    },
    rowHeightEffective() {
      return Number(this.measuredRowHeight || this.rowHeight);
    },
    allowedSlots() {
      const map = this.getAllowedTimesByDay?.() || {};
      const arr = Array.isArray(map[this.denNaZobrazenie])
        ? map[this.denNaZobrazenie]
        : [];
      return [...new Set(arr)].sort();
    },
    lessonsRaw() {
      const denMap = {
        0: "Pondelok",
        1: "Utorok",
        2: "Streda",
        3: "Štvrtok",
        4: "Piatok",
        5: "Sobota",
        6: "Nedeľa",
      };
      const nowMin = this.toMin(this.nowHHMM());
      return this.hodiny
        .filter((h) => denMap[String(h.den)] === this.denNaZobrazenie)
        .map((h) => {
          const start = this.formatHour(h.cas);
          const lesson = this.lessonsByCalendar[String(h.id)] || null;
          let lessonStudents = [];
          if (lesson && typeof lesson.student === "string") {
            try {
              lessonStudents = JSON.parse(lesson.student || "[]");
            } catch {
              lessonStudents = [];
            }
          }
          const lessonHour =
            lesson && lesson.hour ? this.formatHour(String(lesson.hour)) : null;
          const startEff = lessonHour || start;
          const endStr =
            h.koniec ||
            h.to ||
            h.end ||
            h.hour_to ||
            h.availability_hour_to ||
            "";
          const isDuo = (h.forma || "").toLowerCase() === "duo";
          let dur = Number(h.trvanieMin || 0);
          const end = endStr ? this.formatHour(String(endStr)) : "";
          if (!dur && end)
            dur = Math.max(1, this.toMin(end) - this.toMin(startEff));
          if (!dur) dur = isDuo ? 45 : 30;
          const finalEnd = end || this.toHHMM(this.toMin(startEff) + dur);
          const startMin = this.toMin(startEff);
          const status = String(
            (lesson && lesson.status) || h.status || h.stav || ""
          ).toLowerCase();
          let stavClass = this.statusToClass(status);
          if (nowMin >= startMin && nowMin < startMin + dur) {
            stavClass = "prebiehajuca";
          }
          let bg = null;
          if (isDuo && lesson) {
            const sid1 = String((h.studentIds && h.studentIds[0]) || "");
            const sid2 = String((h.studentIds && h.studentIds[1]) || "");
            const s1 = lessonStudents.find(
              (s) => String(s.student_id) === sid1
            );
            const s2 = lessonStudents.find(
              (s) => String(s.student_id) === sid2
            );
            const st1 =
              this.attendanceToStatus(s1 ? s1.attendance_status : undefined) ||
              lesson.status ||
              "planned";
            const st2 =
              this.attendanceToStatus(s2 ? s2.attendance_status : undefined) ||
              lesson.status ||
              "planned";
            const cls1 = this.statusToClass(st1);
            const cls2 = this.statusToClass(st2);
            const c1 = this.statusClassToColor(cls1);
            const c2 = this.statusClassToColor(cls2);
            bg = `linear-gradient(90deg, ${c1} 0 50%, ${c2} 50% 100%)`;
          } else if (!isDuo && lesson && lessonStudents.length) {
            const sid = String((h.studentIds && h.studentIds[0]) || "");
            const s = lessonStudents.find((x) => String(x.student_id) === sid);
            if (s) {
              const st = this.attendanceToStatus(s.attendance_status);
              stavClass = this.statusToClass(st);
            }
          }
          const hasLesson = !!lesson;
          return {
            id: h.id,
            students: h.students || [],
            studentIds: Array.isArray(h.studentIds) ? h.studentIds : [],
            isDuo,
            cas: startEff,
            koniec: finalEnd,
            stavClass: stavClass,
            bg,
            durMin: dur,
            startMin: startMin,
            hasLesson,
            lessonId: lesson ? Number(lesson.id) : null,
          };
        })
        .sort((a, b) => a.startMin - b.startMin);
    },
    layoutStartMin() {
      const step = this.intervalMinutes;
      const c = [];
      if (this.allowedSlots.length) c.push(this.toMin(this.allowedSlots[0]));
      if (this.lessonsRaw.length) c.push(this.lessonsRaw[0].startMin);
      const raw = c.length ? Math.min(...c) : 8 * 60;
      return Math.floor(raw / step) * step;
    },
    layoutEndMin() {
      const step = this.intervalMinutes;
      const c = [];
      if (this.allowedSlots.length) {
        const last = this.allowedSlots[this.allowedSlots.length - 1];
        c.push(this.toMin(last) + step);
      }
      if (this.lessonsRaw.length)
        c.push(Math.max(...this.lessonsRaw.map((l) => l.startMin + l.durMin)));
      const raw = c.length ? Math.max(...c) : this.layoutStartMin + 60;
      return Math.ceil(raw / step) * step;
    },
    times() {
      const out = [];
      for (
        let m = this.layoutStartMin;
        m <= this.layoutEndMin;
        m += this.intervalMinutes
      ) {
        out.push({
          from: this.toHHMM(m),
          to: this.toHHMM(m + this.intervalMinutes),
        });
      }
      return out;
    },
    zaznamy() {
      if (!this.times.length) return [];
      const baseMin = this.toMin(this.times[0].from);
      const step = this.intervalMinutes;
      const pxPerStep = this.rowHeightEffective;
      let cursor = baseMin;
      const list = [];
      for (const l of this.lessonsRaw) {
        const gapUnits = (l.startMin - cursor) / step;
        const durUnits = l.durMin / step;
        list.push({
          ...l,
          offsetPx: gapUnits * pxPerStep,
          heightPx: durUnits * pxPerStep,
        });
        cursor = l.startMin + l.durMin;
      }
      return list;
    },
    minutesFromStart() {
      return this.toMin(this.nowHHMM()) - this.layoutStartMin;
    },
    totalMinutes() {
      return Math.max(1, this.layoutEndMin - this.layoutStartMin);
    },
    nowPercent() {
      const pct = (this.minutesFromStart / this.totalMinutes) * 100;
      return Math.max(0, Math.min(100, pct));
    },
    nowOffsetPx() {
      const minutes = Math.max(
        0,
        Math.min(
          this.totalMinutes,
          this.toMin(this.nowHHMM()) - this.layoutStartMin
        )
      );
      const step = this.intervalMinutes;
      const pxPerStep = this.rowHeightEffective;
      return (minutes / step) * pxPerStep;
    },
    showNowIndicator() {
      const now = this.nowHHMM();
      const start = this.toHHMM(this.layoutStartMin);
      const end = this.toHHMM(this.layoutEndMin);
      return now >= start && now <= end;
    },
    nowStyle() {
      return { top: this.nowOffsetPx + "px" };
    },
  },
  methods: {
    parseDMY(s) {
      const m = String(s || "").match(/^(\d{2})\.(\d{2})\.(\d{4})$/);
      if (!m) return null;
      const dd = parseInt(m[1], 10);
      const mm = parseInt(m[2], 10) - 1;
      const yy = parseInt(m[3], 10);
      const d = new Date(yy, mm, dd);
      return isNaN(d.getTime()) ? null : d;
    },
    statusToClass(status) {
      const s = String(status || "").toLowerCase();
      if (s === "absolved") return "dokoncena";
      if (s === "cancelled_admin") return "zrusenaAdminom";
      if (s === "cancelled_parent") return "zrusenaRodicom";
      if (s === "in_progress" || s === "running" || s === "ongoing")
        return "prebiehajuca";
      return "naplanovana";
    },
    attendanceToStatus(att) {
      if (att === false) return "absolved";
      if (att === true) return "cancelled_parent";
      if (att === null) return "cancelled_admin";
      return "planned";
    },
    statusClassToColor(cls) {
      switch (cls) {
        case "dokoncena":
          return "#90ca50";
        case "zrusenaAdminom":
          return "#f86e5cc1";
        case "zrusenaRodicom":
          return "#f7b21ec0";
        case "prebiehajuca":
          return "#00913f";
        default:
          return "#d9d9d9";
      }
    },
    async loadPastLessons(dateDMY) {
      this.hodiny = [];
      this.lessonsByCalendar = {};
      const url = `${this.apiEdupage}loadLesson.php?date=${encodeURIComponent(
        dateDMY
      )}`;
      const res = await fetch(url, { credentials: "include" });
      const txt = await res.text();
      let out;
      try {
        out = JSON.parse(txt);
      } catch {
        out = null;
      }
      const rows = Array.isArray(out?.data) ? out.data : [];
      const filtered = this.teacherId
        ? rows.filter(
            (r) =>
              String(r.teacher_id || r.teacherId || "") ===
              String(this.teacherId)
          )
        : rows;
      const parsed = [];
      const lessonsMap = {};
      const dsel = this.parseDMY(dateDMY) || new Date();
      const jsDay = dsel.getDay();
      const mon0 = (jsDay + 6) % 7;
      for (const r of filtered) {
        let studentsArr = [];
        try {
          const v = typeof r.student === "string" ? r.student : "[]";
          studentsArr = JSON.parse(v || "[]");
        } catch {
          studentsArr = [];
        }
        const studentIds = studentsArr
          .map((s) => String(s.student_id))
          .filter(Boolean);
        const isDuo = studentIds.length === 2;
        const hourStr = String(r.hour || "").trim();
        const cas = this.formatHour(hourStr);
        const koniec = "";
        const trvanieMin = isDuo ? 45 : 30;
        const status = String(r.status || "").toLowerCase();
        parsed.push({
          id: Number(r.calendar_id || r.id || 0) || null,
          teacher_id: Number(r.teacher_id || 0) || null,
          forma: isDuo ? "duo" : "solo",
          den: String(mon0),
          cas,
          koniec,
          trvanieMin,
          status,
          students: [],
          studentIds,
        });
        const key = String(r.calendar_id);
        lessonsMap[key] = r;
      }
      const cache = new Map();
      for (const h of parsed) {
        for (const sid of h.studentIds) {
          if (!cache.has(sid)) cache.set(sid, await this.fetchStudentName(sid));
          const base = cache.get(sid) || {
            name: `ID ${sid}`,
            photo: this.defaultAvatar,
          };
          h.students.push({ ...base });
        }
      }
      this.hodiny = parsed;
      this.lessonsByCalendar = lessonsMap;
    },
    async loadLessonsForCurrentDate() {
      const date = this.selectedDate || this.todayDMY;
      const ids = [
        ...new Set(
          (this.hodiny || []).map((h) => h.id).filter((x) => x != null)
        ),
      ];
      const map = {};
      for (const cid of ids) {
        try {
          const url = `${
            this.apiEdupage
          }loadLesson.php?calendar_id=${encodeURIComponent(
            cid
          )}&date=${encodeURIComponent(date)}`;
          const res = await fetch(url, { credentials: "include" });
          const txt = await res.text();
          let out;
          try {
            out = JSON.parse(txt);
          } catch {
            out = null;
          }
          const arr = Array.isArray(out?.data) ? out.data : [];
          if (arr.length) {
            const lesson = arr.reduce((a, b) => (+b.id > +a.id ? b : a));
            map[String(cid)] = lesson;
          }
        } catch {
          // ignore
        }
      }
      this.lessonsByCalendar = map;
    },
    openSummary(z) {
      const students = z.students.map((s, index) => ({
        id: z.studentIds[index],
        name: s?.name || `ID ${z.studentIds[index]}`,
        photo: s?.photo || this.defaultAvatar,
      }));
      this.$emit("open-summary", {
        id: z.id,
        students: students,
        date: this.selectedDate || "",
        hour: z.cas ? String(z.cas).replace(":", "") : "",
      });
    },
    openAddZapis(z) {
      const students = z.students.map((s, index) => ({
        id: z.studentIds[index],
        name: s?.name || `ID ${z.studentIds[index]}`,
        photo: s?.photo || this.defaultAvatar,
      }));
      this.$emit("open-add-zapis", {
        id: z.id,
        students: students,
        lessonId: z.lessonId,
      });
    },
    openMenu(e, z) {
      this.$emit("open-context", e, z);
    },
    measureRowHeight() {
      this.$nextTick(() => {
        const els = this.$el?.querySelectorAll(".time p");
        if (els && els.length >= 2) {
          const a = els[0].getBoundingClientRect();
          const b = els[1].getBoundingClientRect();
          const h = b.top - a.top;
          if (h > 0 && Number.isFinite(h)) this.measuredRowHeight = h;
        }
      });
    },
    toMin(v) {
      const [h, m] = String(v || "00:00")
        .split(":")
        .map((n) => parseInt(n, 10));
      return (h || 0) * 60 + (m || 0);
    },
    toHHMM(min) {
      const h = Math.floor(min / 60);
      const m = min % 60;
      return String(h).padStart(2, "0") + ":" + String(m).padStart(2, "0");
    },
    nowHHMM() {
      const d = new Date();
      return (
        String(d.getHours()).padStart(2, "0") +
        ":" +
        String(d.getMinutes()).padStart(2, "0")
      );
    },
    formatHour(s) {
      const v = String(s || "").trim();
      if (!v) return "00:00";
      if (/^\d{4}$/.test(v)) return `${v.slice(0, 2)}:${v.slice(2)}`;
      if (/^\d{3}$/.test(v)) return `0${v[0]}:${v.slice(1)}`;
      if (/^\d{2}:\d{2}$/.test(v)) return v;
      if (/^\d{1,2}$/.test(v)) return `00:${v.padStart(2, "0")}`;
      return "00:00";
    },
    async fetchStudentName(id) {
      const fallback = { name: `ID ${id}`, photo: this.defaultAvatar };
      try {
        const res = await fetch(
          this.apiInfo +
            "getAllInformationAboutUser.php?id=" +
            encodeURIComponent(id),
          { credentials: "include" }
        );
        const txt = await res.text();
        let out;
        try {
          out = JSON.parse(txt);
        } catch {
          out = null;
        }
        const u = out?.data || out || {};
        const first = String(u.firstname ?? u.name ?? "").trim();
        const last = String(u.lastname ?? u.surname ?? "").trim();
        const full = [first, last].filter(Boolean).join(" ");
        let rawPhoto = String(u.profilePhotoUrl || "").trim();
        if (rawPhoto === "null" || rawPhoto === "undefined") rawPhoto = "";
        const stripWWW = (s) =>
          String(s)
            .replace(/^(https?:\/\/)www\./i, "$1")
            .replace(/^www\./i, "");
        const normalize = (url) => {
          if (!url) return "";
          try {
            const abs = new URL(stripWWW(url), window.location.origin);
            if (abs.hostname.startsWith("www.")) {
              abs.hostname = abs.hostname.slice(4);
            }
            return abs.toString();
          } catch {
            return stripWWW(url);
          }
        };
        const photo = normalize(rawPhoto) || this.defaultAvatar;
        return { name: full || `ID ${id}`, photo };
      } catch {
        return fallback;
      }
    },
    parseStudentIds(raw) {
      if (Array.isArray(raw)) {
        const ids = raw
          .map((x) => {
            if (x == null) return "";
            if (typeof x === "object")
              return String(x.student_id ?? x.id ?? "").trim();
            return String(x).trim();
          })
          .filter(Boolean);
        return [...new Set(ids)];
      }
      const s = String(raw ?? "").trim();
      if (!s) return [];
      try {
        const arr = JSON.parse(s);
        if (Array.isArray(arr)) {
          const ids = arr
            .map((x) => {
              if (x == null) return "";
              if (typeof x === "object")
                return String(x.student_id ?? x.id ?? "").trim();
              return String(x).trim();
            })
            .filter(Boolean);
          return [...new Set(ids)];
        }
      } catch {
        // ignore
      }
      if (/^\d+(?:\s*,\s*\d+)*$/.test(s))
        return [
          ...new Set(
            s
              .split(",")
              .map((x) => x.trim())
              .filter(Boolean)
          ),
        ];
      return s ? [s] : [];
    },
    async nacitajHodiny({ id = null } = {}) {
      this.hodiny = [];
      this.lessonsByCalendar = {};
      if (this.isPastSelected && this.selectedDate) {
        await this.loadPastLessons(this.selectedDate);
        this.$nextTick(this.measureRowHeight);
        return;
      }
      const qs = new URLSearchParams();
      if (id != null) qs.append("id", String(id));
      if (this.teacherId) qs.append("teacher_id", String(this.teacherId));
      const url =
        this.apiEdupage +
        "loadCalendar.php" +
        (qs.toString() ? "?" + qs.toString() : "");
      const res = await fetch(url, { credentials: "include" });
      const txt = await res.text();
      let out;
      try {
        out = JSON.parse(txt);
      } catch {
        out = null;
      }
      if (!res.ok || !out || out?.status === "Request failed")
        throw new Error(out?.data || out?.message || `HTTP ${res.status}`);
      const rows = Array.isArray(out?.data)
        ? out.data
        : Array.isArray(out)
        ? out
        : [];
      const parsed = [];
      for (const r of rows) {
        const studentField =
          r.student ??
          r.students ??
          r.student_id ??
          r.studentId ??
          r.studentIds ??
          "";
        const studentIds = this.parseStudentIds(studentField);
        const forma =
          (r.formOfStudy || "").toLowerCase() === "duo" ? "duo" : "solo";
        const startStr =
          r.availability_hour ??
          r.hour ??
          r.from ??
          r.zaciatok ??
          r.start ??
          "";
        const endStr =
          r.availability_hour_to ??
          r.hour_to ??
          r.to ??
          r.koniec ??
          r.end ??
          "";
        parsed.push({
          id: Number(r.id || r.ID || 0) || null,
          teacher_id: Number(r.teacher_id || r.teacherId || 0) || null,
          forma,
          den: String(r.availability_day ?? r.day ?? ""),
          cas: this.formatHour(String(startStr)),
          koniec: endStr ? this.formatHour(String(endStr)) : "",
          trvanieMin: Number(r.duration || 0) || 0,
          status: String(
            r.status || r.stav || r.state || r.lesson_status || ""
          ).toLowerCase(),
          students: [],
          studentIds,
        });
      }
      const cache = new Map();
      for (const h of parsed) {
        for (const sid of h.studentIds) {
          if (!cache.has(sid)) cache.set(sid, await this.fetchStudentName(sid));
          const base = cache.get(sid) || {
            name: `ID ${sid}`,
            photo: this.defaultAvatar,
          };
          h.students.push({ ...base });
        }
      }
      this.hodiny = parsed.filter(
        (h) => !this.teacherId || h.teacher_id === this.teacherId
      );
      await this.loadLessonsForCurrentDate();
      this.$nextTick(this.measureRowHeight);
    },
    async dispatchAny(action, payload) {
      try {
        return await this.$store.dispatch(
          "edupage/learning/" + action,
          payload
        );
      } catch {
        try {
          return await this.$store.dispatch("learning/" + action, payload);
        } catch {
          return null;
        }
      }
    },
    commitAny(mutation, payload) {
      try {
        this.$store.commit("edupage/learning/" + mutation, payload);
      } catch {
        try {
          this.$store.commit("learning/" + mutation, payload);
        } catch {
          // ignore
        }
      }
    },
    async fetchAdvancedDirect(id) {
      const sid = String(id);
      const url = `${
        this.apiRoot
      }/edupage/loadStudentAdvanced.php?id=${encodeURIComponent(sid)}`;
      const res = await fetch(url, { credentials: "include" });
      const txt = await res.text();
      let out;
      try {
        out = JSON.parse(txt);
      } catch {
        out = null;
      }
      const d = out?.data || {};
      const toNum = (v) => {
        if (v === null || v === undefined) return 0;
        const n = Number(String(v).replace(",", "."));
        return Number.isFinite(n) ? n : 0;
      };
      const stripWWW = (u) => {
        const s = String(u || "").trim();
        if (!s) return "";
        return s.replace(/^(https?:\/\/)www\./i, "$1").replace(/^www\./i, "");
      };
      const full = [String(d.name || "").trim(), String(d.surname || "").trim()]
        .filter(Boolean)
        .join(" ")
        .trim();
      const student = {
        id: sid,
        name: full || `ID ${sid}`,
        email: d.email || "",
        photo: stripWWW(d.profilePhotoUrl || ""),
        completed: toNum(d.absolvedLessonsCount),
        missed: toNum(d.canceledLessonsCount),
        tasks: toNum(d.achievementsFulfiledCount),
        star: toNum(d.starsCount),
        rank: toNum(d.rank ?? 0),
        time: {
          total: toNum(d.helitime),
          today: toNum(d.helitime_today ?? 0),
          week: toNum(d.helitime_week ?? 0),
        },
      };
      this.commitAny("UPSERT_STUDENT", student);
      return student;
    },
    openInfoMenu(z) {
      const ids = Array.isArray(z?.studentIds)
        ? z.studentIds.filter((x) => x != null).map(String)
        : [];

      this.selectLesson(ids).then(() => {
        this.$store.commit("ui/OPEN_INFO", {
          id: z.id,
          studentIds: ids,
          hour: z.cas,
          dayName: this.denNaZobrazenie,
          dateDMY: this.selectedDate || this.todayDMY,
          formOfStudy: z.isDuo ? "duo" : "solo",
        });
      });
    },
    closeMenu() {
      // zavri context, zatvor info (globálne)
      this.menu.show = false;
      this.menu.target = null;
      this.menu.error = "";
      this.$store.commit("ui/CLOSE_INFO");
    },
    async selectLesson(ids) {
      if (!Array.isArray(ids) || !ids.length) return;
      try {
        await Promise.all(
          ids.map((sid) =>
            this.$store.dispatch("edupage/learning/fetchStudentAdvanced", sid)
          )
        );
        this.$store.commit("edupage/learning/SET_ACTIVE", ids[0]);
      } catch {
        try {
          await Promise.all(
            ids.map((sid) =>
              this.$store.dispatch("learning/fetchStudentAdvanced", sid)
            )
          );
          this.$store.commit("learning/SET_ACTIVE", ids[0]);
        } catch {
          await Promise.all(ids.map((sid) => this.fetchAdvancedDirect(sid)));
          this.commitAny("SET_ACTIVE", ids[0]);
        }
      }
    },
  },
  async mounted() {
    this.currentTimeInterval = setInterval(() => {
      this.currentTime = new Date().toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
      });
    }, 1000);
    await this.nacitajHodiny();
    window.addEventListener("resize", this.measureRowHeight);
    this.$nextTick(this.measureRowHeight);
  },
  watch: {
    times() {
      this.$nextTick(this.measureRowHeight);
    },
    denNaZobrazenie() {
      this.$nextTick(this.measureRowHeight);
    },
    selectedDate() {
      this.nacitajHodiny();
    },
    predvolenyDen() {
      this.$nextTick(this.measureRowHeight);
    },
  },
  beforeUnmount() {
    if (this.currentTimeInterval) {
      clearInterval(this.currentTimeInterval);
      this.currentTimeInterval = null;
    }
    window.removeEventListener("resize", this.measureRowHeight);
  },
};
</script>

<style lang="scss" scoped>
.lecture.dokoncena {
  background-color: #90ca50;
  &:hover {
    background-color: #7cad42;
  }
}
.lecture.naplanovana {
  background-color: #d9d9d9;
  &:hover {
    background-color: #bfbfbf;
  }
}
.lecture.prebiehajuca {
  background-color: #00913f;
  &:hover {
    background-color: #007d36;
  }
}
.lecture.zrusenaAdminom {
  background-color: #f86e5cc1;
  &:hover {
    background-color: #e95f4d;
  }
}
.lecture.zrusenaRodicom {
  background-color: #f7b21ec0;
  &:hover {
    background-color: #dea31b;
  }
}
.calendar-flow {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  justify-content: center;
  width: 90%;
  max-width: 70em;
  margin: 3em auto;
}
.lecture {
  display: flex;
  align-items: center;
  justify-content: space-around;
  gap: 1em;
  max-width: 50em;
  border-radius: 0.7em;
  padding: 0.6em 1.2em;
  margin: 0 auto 0.2em;
  border-bottom: 2px solid #ededed;
  &:hover {
    transition-duration: 0.2s;
    transform: scale(1.01);
  }
}
.info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  width: 60%;
}
.profile {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}
.profile img {
  width: 1.9em;
  border-radius: 0.7em;
  border: 0.13em solid #fef35a;
  box-shadow: 5px 5px 7px 2px rgba(0, 0, 0, 0.3);
  &:nth-child(2) {
    margin-top: 0.27em;
  }
}
.name {
  font-size: 1.2em;
  font-weight: 500;
  text-align: left;
}
.start {
  font-size: 0.9em;
  font-weight: 300;
  opacity: 0.5;
}
.action,
.addZapis {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  gap: 0.8em;
}
.action {
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.2s;
}
.lecture:hover .action {
  opacity: 1;
  pointer-events: auto;
}
.lecture.noActions .action {
  opacity: 0 !important;
  pointer-events: none !important;
}
.lecture.noActions:hover .action {
  opacity: 0 !important;
}
.button {
  font-size: 0.6em;
  border-radius: 0.8em;
  img {
    width: 1.4em;
    margin: 0;
  }
}
.time {
  flex: 0.1;
  opacity: 0.5;
  margin-top: -0.8em;
  p {
    font-size: 1.2em;
    margin-bottom: 2.05em;
  }
}
.flow {
  flex: 0.8;
  position: relative;
}
.calendar-flow {
  position: relative;
  --time-col-width: 6.5rem;
}
.now-indicator {
  position: absolute;
  left: -15%;
  right: 0;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  pointer-events: none;
  width: 130%;
  z-index: -1;
}
.now-indicator .label {
  width: var(--time-col-width);
  text-align: right;
  font-weight: 700;
  font-size: 1.1em;
  opacity: 0.6;
  margin-right: 0.8rem;
  margin-left: calc(-1 * var(--time-col-width));
}
.now-indicator .dot {
  width: 14px;
  height: 14px;
  border-radius: 999px;
  background: #00913f;
}
.now-indicator .line {
  height: 3px;
  border-radius: 6px;
  background: #00913f;
  flex: 1;
  margin-left: -0.2rem;
}
.button {
  letter-spacing: 0.04rem;
}
.ctx {
  position: fixed;
  z-index: 1000;
  min-width: 180px;
  background: #ffffff;
  border: 1px solid #e3e3e3;
  border-radius: 10px;
  box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
  padding: 0.4rem;
}
.ctx-item {
  width: 100%;
  text-align: left;
  background: transparent;
  border: 0;
  padding: 0.55rem 0.7rem;
  border-radius: 8px;
  font-size: 0.95rem;
  cursor: pointer;
}
.ctx-item:hover {
  background: #f4f4f4;
}
.ctx-item.danger {
  color: #c62828;
}
.ctx-item.danger:hover {
  background: #fdecec;
}
.ctx-sep {
  margin: 0.25rem 0;
  border: 0;
  border-top: 1px solid #eee;
}
.ctx-err {
  color: #c62828;
  font-size: 0.85rem;
  margin: 0.25rem 0.5rem 0;
}
</style>
