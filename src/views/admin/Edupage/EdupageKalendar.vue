<template>
  <section
    :id="$route.name"
    @click="closeContextMenu"
    @contextmenu.prevent="onSectionContext($event)"
    tabindex="0"
  >
    <div class="scroll">
      <h1 v-once>Rozvrh hodín</h1>

      <KalendarDatumHeader
        :showFilter="showFilter"
        :showDays="true"
        :allowedDaysFull="povoleneDni"
        :allowedTimesByDay="povoleneCasyPodlaDna"
        @show-modal="openModal"
        @day-changed="onDayChanged"
        @date-changed="onDateChanged"
      />

      <EdupageInfo
        v-if="headerReady && !showFilter"
        :empty="studentsWithoutLessons === 0"
        :unplannedCount="studentsWithoutLessons"
        :hasCalendar="hasCalendar"
        @open-modal="openModal"
      />

      <keep-alive>
        <kalendar-flow
          ref="flow"
          v-if="flowReady && showFilter"
          :key="currentDay + ':' + selectedDateDMY"
          :predvolenyDen="currentDay"
          :selectedDate="selectedDateDMY"
          @show-modal="openModal"
          @open-summary="openSummaryModal"
          @open-add-zapis="openAddCiselnyZapis"
          @open-context="openContextMenu"
        />
      </keep-alive>
    </div>

    <img
      class="set"
      @click="openModal({ component: CasyUcenia })"
      src="@/assets/images/icons/settingEdupage.svg"
      alt=""
    />
    <button
      class="button Adumu"
      :aria-busy="createBusy"
      @click="createLessonsNextWeek"
    >
      {{ createBusy ? "Vytváram…" : "Vytvoriť lekcie" }}
    </button>
    <p v-if="createErr" class="text small" style="color: #c0392b">
      {{ createErr }}
    </p>
    <p v-if="createMsg" class="text small" style="color: #2e7d32">
      {{ createMsg }}
    </p>

    <teleport to="body">
      <transition name="ctx">
        <div
          v-if="contextMenu.visible"
          ref="ctxMenu"
          class="context-menu"
          :style="{
            transform: `translate3d(${contextMenu.x}px, ${contextMenu.y}px, 0)`,
          }"
          @click.stop
        >
          <button
            v-if="contextMenu.mode === 'section'"
            class="ctx-item"
            @click="onAddStudent"
          >
            Pridať žiaka
          </button>

          <button
            v-else
            class="ctx-item danger"
            :disabled="contextMenu.busy || !contextMenu.target?.id"
            @click="deleteSelected"
          >
            {{ contextMenu.busy ? "Mažem…" : "Vymazať hodinu" }}
          </button>

          <p v-if="contextMenu.error" class="ctx-err">
            {{ contextMenu.error }}
          </p>
        </div>
      </transition>
    </teleport>
  </section>

  <ModalSkeleton
    v-if="modalOpen"
    :content-component="modalComponent"
    :content-props="modalProps"
    @close="handleModalClose"
  />
</template>

<script>
import { defineAsyncComponent } from "vue";
import ModalSkeleton from "../../../components/Spevnik/modal/ModalSkeleton.vue";

const KalendarDatumHeader = defineAsyncComponent(() =>
  import("@/components/Edupage/KalendarDatumHeader.vue")
);
const KalendarFlow = defineAsyncComponent(() =>
  import("../../../components/Edupage/KalendarFlow.vue")
);
const CasyUcenia = defineAsyncComponent(() =>
  import("@/components/Edupage/CasyUcenia.vue")
);
const EdupageInfo = defineAsyncComponent(() =>
  import("@/components/Edupage/EdupageInfo.vue")
);
const PridanieZiakaDoRozvrhu = defineAsyncComponent(() =>
  import("@/components/Edupage/PridanieZiakaDoRozvrhu.vue")
);
const ZhrnutieLekcie = defineAsyncComponent(() =>
  import("@/components/Edupage/ZhrnutieLekcie.vue")
);
const PridatCiselnyZapis = defineAsyncComponent(() =>
  import("@/components/Edupage/PridatCiselnyZapis.vue")
);
const UpravaHodiny = defineAsyncComponent(() =>
  import("@/components/Edupage/UpravaHodiny.vue")
);

const LS_KEY_DATE = "edp.selectedDateISO";
const LS_KEY_DAY = "edp.currentDay";
const LS_KEY_AVAIL = "edp.cache.availability.v1";
const LS_KEY_HOURS = "edp.cache.hasHours.v1";
const LS_KEY_UNPLAN = "edp.cache.unplanned.v1";

export default {
  components: {
    KalendarDatumHeader,
    KalendarFlow,
    ModalSkeleton,
    // eslint-disable-next-line vue/no-unused-components
    CasyUcenia,
    EdupageInfo,
    // eslint-disable-next-line vue/no-unused-components
    PridanieZiakaDoRozvrhu,
    // eslint-disable-next-line vue/no-unused-components
    ZhrnutieLekcie,
    // eslint-disable-next-line vue/no-unused-components
    PridatCiselnyZapis,
    // eslint-disable-next-line vue/no-unused-components
    UpravaHodiny,
  },
  data() {
    const fromLsDate = localStorage.getItem(LS_KEY_DATE) || "";
    const fromLsDay = localStorage.getItem(LS_KEY_DAY) || "";
    let cached = null;
    try {
      cached = JSON.parse(localStorage.getItem(LS_KEY_AVAIL) || "null");
    } catch {
      cached = null;
    }
    return {
      modalOpen: false,
      modalComponent: CasyUcenia,
      modalProps: {},
      showFilter: !!(cached && cached.povoleneDni?.length),
      flowReady: true,
      headerReady: true,
      studentsWithoutLessons: Number(localStorage.getItem(LS_KEY_UNPLAN) || 0),
      contextMenu: {
        visible: false,
        x: 0,
        y: 0,
        mode: "section",
        target: null,
        busy: false,
        error: "",
      },
      dostupnost: cached?.dostupnost || [],
      povoleneDni: cached?.povoleneDni || [],
      povoleneCasyPodlaDna: cached?.povoleneCasyPodlaDna || {},
      hasAnyHours: JSON.parse(localStorage.getItem(LS_KEY_HOURS) || "false"),
      currentDay: fromLsDay,
      selectedDateISO: fromLsDate,
      createBusy: false,
      createMsg: "",
      createErr: "",
      acLoads: {},
      flowReloadScheduled: false,
      slotCache: new Map(),
    };
  },
  provide() {
    const self = this;
    return {
      getEdupageAvailability: () => self.dostupnost,
      getAllowedDaysFull: () => self.povoleneDni,
      getAllowedTimesByDay: () => self.povoleneCasyPodlaDna,
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
    hasCalendar() {
      return this.dostupnost.length > 0;
    },
    selectedDateDMY() {
      return this.normalizeToDMY(this.selectedDateISO || "");
    },
    editRequest() {
      return this.$store.state.ui?.editRequest || null;
    },
  },
  methods: {
    scheduleFlowReload() {
      if (this.flowReloadScheduled) return;
      this.flowReloadScheduled = true;
      queueMicrotask(() => {
        this.flowReloadScheduled = false;
        this.$refs.flow?.nacitajHodiny?.();
      });
    },
    async safeFetch(url, init = {}, key = null) {
      if (key) {
        this.acLoads[key]?.abort?.();
        this.acLoads[key] = new AbortController();
        init.signal = this.acLoads[key].signal;
      }
      return fetch(url, init);
    },
    async parseMaybeJSON(res) {
      const text = await res.text();
      const ct = res.headers.get("content-type") || "";
      if (ct.includes("application/json")) {
        try {
          return JSON.parse(text);
        } catch {
          return null;
        }
      }
      try {
        return JSON.parse(text);
      } catch {
        return null;
      }
    },
    toDMY(d) {
      if (!(d instanceof Date)) return "";
      const dd = String(d.getDate()).padStart(2, "0");
      const mm = String(d.getMonth() + 1).padStart(2, "0");
      const yyyy = d.getFullYear();
      return `${dd}.${mm}.${yyyy}`;
    },
    normalizeToDMY(v) {
      if (!v) return "";
      if (typeof v === "string" && /^\d{2}\.\d{2}\.\d{4}$/.test(v)) return v;
      if (typeof v === "string" && /^\d{4}-\d{2}-\d{2}$/.test(v)) {
        const [y, m, d] = v.split("-").map((n) => parseInt(n, 10));
        return this.toDMY(new Date(y, m - 1, d));
      }
      if (v instanceof Date) return this.toDMY(v);
      if (typeof v === "string") {
        const t = Date.parse(v);
        if (!isNaN(t)) return this.toDMY(new Date(t));
      }
      return "";
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
        { credentials: "include" },
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
          const s = d.trim();
          if (!s) d = null;
          else {
            try {
              d = JSON.parse(s);
            } catch {
              d = null;
            }
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
      Object.keys(map).forEach((k) => {
        map[k] = Array.from(new Set(map[k])).sort();
      });
      this.povoleneCasyPodlaDna = map;
      if (!this.currentDay) {
        const today = this.todayName();
        this.currentDay = this.povoleneDni.includes(today)
          ? today
          : this.povoleneDni[0] || "";
      }
      this.showFilter = this.hasCalendar && this.povoleneDni.length > 0;
      localStorage.setItem(
        LS_KEY_AVAIL,
        JSON.stringify({
          dostupnost: this.dostupnost,
          povoleneDni: this.povoleneDni,
          povoleneCasyPodlaDna: this.povoleneCasyPodlaDna,
        })
      );
      this.scheduleFlowReload();
    },
    async createLessonsNextWeek() {
      if (this.createBusy) return;
      this.createBusy = true;
      this.createErr = "";
      this.createMsg = "";
      try {
        const res = await this.safeFetch(this.apiBase + "createLesson.php", {
          method: "POST",
          credentials: "include",
        });

        // skús prečítať JSON, ale ak nič neprišlo a res.ok je true, ber to ako OK
        let out = null;
        try {
          out = await this.parseMaybeJSON(res);
        } catch {
          out = null;
        }

        const txtOk =
          res.ok &&
          (out === true ||
            out?.ok === true ||
            out?.success === true ||
            out?.status === "Request succesfull" ||
            /^(ok|true|1)$/i.test(String(out?.status || "")) ||
            /request\s+succes+full/i.test(String(out?.status || "")) ||
            out === null); // prázdne telo alebo známy OK status a 200 OK

        if (!txtOk)
          throw new Error(out?.message || out?.error || "Vytvorenie zlyhalo.");

        this.createMsg = "Lekcie na nadchádzajúci týždeň boli vytvorené.";
        this.$store.state.SnackBarText = this.createMsg;
        this.scheduleFlowReload();
      } catch (e) {
        this.createErr = e?.message || "Vytvorenie zlyhalo.";
        this.$store.state.SnackBarText = this.createErr;
      } finally {
        this.createBusy = false;
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
    async fetchHoursCount() {
      if (!this.teacherId) {
        this.hasAnyHours = false;
        localStorage.setItem(LS_KEY_HOURS, "false");
        return;
      }
      const qs = new URLSearchParams();
      qs.append("teacher_id", String(this.teacherId));
      const res = await this.safeFetch(
        this.apiBase + "loadCalendar.php?" + qs,
        { credentials: "include" },
        "hours"
      );
      const out = await this.parseMaybeJSON(res);
      const rows = Array.isArray(out?.data)
        ? out.data
        : Array.isArray(out)
        ? out
        : [];
      this.hasAnyHours = rows.length > 0;
      localStorage.setItem(LS_KEY_HOURS, JSON.stringify(this.hasAnyHours));
    },
    onDayChanged(payload) {
      if (!payload) return;
      if (typeof payload === "string") {
        this.currentDay = payload;
      } else if (payload instanceof Date) {
        this.currentDay = this.dayNameSkFromDate(payload);
      } else if (payload.dayName) {
        this.currentDay = payload.dayName;
      }
      localStorage.setItem(LS_KEY_DAY, this.currentDay || "");
      this.scheduleFlowReload();
    },
    onDateChanged(iso) {
      if (typeof iso === "string" && /^\d{4}-\d{2}-\d{2}$/.test(iso)) {
        this.selectedDateISO = iso;
        localStorage.setItem(LS_KEY_DATE, iso);
        this.scheduleFlowReload();
      }
    },
    dayNameSkFromDate(d) {
      const dni = [
        "Nedeľa",
        "Pondelok",
        "Utorok",
        "Streda",
        "Štvrtok",
        "Piatok",
        "Sobota",
      ];
      return dni[d.getDay()];
    },
    todayName() {
      return this.dayNameSkFromDate(new Date());
    },
    openModal(payload = {}) {
      const { component, props } = payload;
      this.modalComponent = component || CasyUcenia;
      this.modalProps = {
        ...(props || {}),
        availability: this.dostupnost,
        allowedDaysFull: this.povoleneDni,
        allowedTimesByDay: this.povoleneCasyPodlaDna,
        teacherId: this.teacherId,
        closeOnSave: true,
      };
      this.modalOpen = true;
    },
    async handleModalClose() {
      this.modalOpen = false;
      const keepDate = this.selectedDateISO;
      const keepDay = this.currentDay;
      await Promise.all([
        this.nacitajDostupnost(),
        this.fetchHoursCount(),
        this.fetchStudentsUnassignedCount(),
      ]);
      if (keepDate) {
        this.selectedDateISO = keepDate;
        localStorage.setItem(LS_KEY_DATE, keepDate);
      }
      if (keepDay) {
        this.currentDay = keepDay;
        localStorage.setItem(LS_KEY_DAY, keepDay);
      }
      this.showFilter = this.hasCalendar && this.povoleneDni.length > 0;
      this.scheduleFlowReload();
    },
    openSummaryModal(summaryData) {
      const selectedDate = this.selectedDateDMY;
      this.openModal({
        component: ZhrnutieLekcie,
        props: { summary: summaryData, selectedDate },
      });
    },
    openAddCiselnyZapis(summaryData) {
      const selectedDate = this.selectedDateDMY;
      this.openModal({
        component: PridatCiselnyZapis,
        props: { summary: summaryData, selectedDate },
      });
    },
    chooseInitialTime(day) {
      const options = this.povoleneCasyPodlaDna?.[day] || [];
      if (!options.length) return "";
      const now = new Date();
      const pref =
        String(now.getHours()).padStart(2, "0") +
        ":" +
        String(now.getMinutes()).padStart(2, "0");
      const toMin = (v) => {
        const [h, m] = v.split(":").map((n) => parseInt(n, 10));
        return h * 60 + m;
      };
      let best = options[0],
        bestDiff = Math.abs(toMin(best) - toMin(pref));
      for (const t of options) {
        const d = Math.abs(toMin(t) - toMin(pref));
        if (d < bestDiff) {
          best = t;
          bestDiff = d;
        }
      }
      return best;
    },
    async fetchStudentsUnassignedCount() {
      try {
        const url =
          this.apiBase +
          "loadStudentsUnassigned.php" +
          (this.teacherId ? `?teacher_id=${this.teacherId}` : "");
        const res = await this.safeFetch(
          url,
          { credentials: "include" },
          "unplan"
        );
        const out = await this.parseMaybeJSON(res);
        const arr = Array.isArray(out?.data)
          ? out.data
          : Array.isArray(out)
          ? out
          : [];
        const ids = arr.filter((x) => x && x !== "null" && x !== "");
        this.studentsWithoutLessons = ids.length;
        localStorage.setItem(
          LS_KEY_UNPLAN,
          String(this.studentsWithoutLessons)
        );
      } catch {
        this.studentsWithoutLessons = 0;
        localStorage.setItem(LS_KEY_UNPLAN, "0");
      }
    },
    onAddStudent() {
      this.closeContextMenu();
      const d =
        this.currentDay && this.povoleneDni.includes(this.currentDay)
          ? this.currentDay
          : this.povoleneDni.includes(this.todayName())
          ? this.todayName()
          : this.povoleneDni[0] || "";
      const t = this.chooseInitialTime(d);
      this.openModal({
        component: PridanieZiakaDoRozvrhu,
        props: { predvolenyDen: d, predvolenyCas: t },
      });
    },
    openContextMenu(e, z = null) {
      if (z) {
        this.contextMenu.mode = "lecture";
        this.contextMenu.target = z;
      } else {
        this.contextMenu.mode = "section";
        this.contextMenu.target = null;
      }
      this.contextMenu.error = "";
      this.contextMenu.busy = false;
      this.contextMenu.visible = true;
      this.$nextTick(() => {
        const el = this.$refs.ctxMenu;
        if (!el) return;
        const margin = 8;
        const rect = el.getBoundingClientRect();
        const vw = window.innerWidth;
        const vh = window.innerHeight;
        let x = e.clientX,
          y = e.clientY;
        if (x + rect.width + margin > vw) x = vw - rect.width - margin;
        if (y + rect.height + margin > vh) y = vh - rect.height - margin;
        if (x < margin) x = margin;
        if (y < margin) y = margin;
        this.contextMenu.x = x;
        this.contextMenu.y = y;
      });
    },
    closeContextMenu() {
      if (this.contextMenu.visible) this.contextMenu.visible = false;
    },
    onSectionContext(e) {
      if (e.target.closest(".lecture")) return;
      if (e.target.closest(".context-menu")) return;
      this.openContextMenu(e, null);
    },
    async deleteSelected() {
      const tgt = this.contextMenu.target;
      if (!tgt || !tgt.id) return;
      this.contextMenu.busy = true;
      this.contextMenu.error = "";
      try {
        const body = new URLSearchParams();
        body.append("id", String(tgt.id));
        body.append("delete", "true");
        const res = await this.safeFetch(this.apiBase + "editCalendar.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
          },
          body,
          credentials: "include",
        });
        const out = await this.parseMaybeJSON(res);
        const ok =
          res.ok &&
          (out?.status === "Request succesfull" ||
            out?.ok === true ||
            out?.success === true ||
            /^(ok|true|1)$/i.test(String(out?.status || "")));
        if (!ok)
          throw new Error(out?.message || out?.error || "Mazanie zlyhalo.");
        this.closeContextMenu();
        this.$store.state.SnackBarText = "Hodina bola vymazaná.";
        this.scheduleFlowReload();
      } catch (e) {
        this.contextMenu.error = e?.message || "Mazanie zlyhalo.";
        this.$store.state.SnackBarText = this.contextMenu.error;
      } finally {
        this.contextMenu.busy = false;
      }
    },
    onEditRequest(eOrPayload) {
      const p =
        eOrPayload && eOrPayload.detail ? eOrPayload.detail : eOrPayload || {};
      const day = p.dayName || this.currentDay || "";
      const hour = p.hour || this.chooseInitialTime(day) || "";
      this.openModal({
        component: PridanieZiakaDoRozvrhu,
        props: {
          edit: true,
          calendarId: p.calendarId || null,
          studentId: p.studentId || null,
          duoStudentId: p.duoStudentId || null,
          studentName: p.studentName || "",
          formOfStudy: p.formOfStudy || "",
          predvolenyDen: day,
          predvolenyCas: hour,
        },
      });
      this.openModal({
        component: UpravaHodiny,
        props: {
          calendarId: p.calendarId,
          dayName: p.dayName,
          hour: p.hour,
          formOfStudy: p.formOfStudy,
          studentId: p.studentId,
          duoStudentId: p.duoStudentId,
        },
      });
    },
  },
  async mounted() {
    if (!this.selectedDateISO) {
      const todayISO = new Date().toISOString().slice(0, 10);
      this.selectedDateISO = todayISO;
      localStorage.setItem(LS_KEY_DATE, todayISO);
    }
    if (!this.currentDay) {
      this.currentDay = this.todayName();
      localStorage.setItem(LS_KEY_DAY, this.currentDay);
    }
    this.showFilter = this.povoleneDni.length > 0;
    if (this.teacherId) {
      Promise.all([
        this.nacitajDostupnost(),
        this.fetchHoursCount(),
        this.fetchStudentsUnassignedCount(),
      ]).then(() => {
        this.showFilter = this.hasCalendar && this.povoleneDni.length > 0;
        this.scheduleFlowReload();
      });
    } else {
      this.showFilter = false;
    }
    this._ctxKeyHandler = (e) => {
      if (e.key === "Escape") this.closeContextMenu();
    };
    this._ctxOutsideHandler = (e) => {
      const el = this.$refs.ctxMenu;
      if (!this.contextMenu.visible) return;
      if (el && !el.contains(e.target)) this.closeContextMenu();
    };
    this._ctxResizeHandler = () => this.closeContextMenu();
    this._editHandler = (e) => this.onEditRequest(e);
    window.addEventListener("keydown", this._ctxKeyHandler);
    window.addEventListener("mousedown", this._ctxOutsideHandler, true);
    window.addEventListener("scroll", this._ctxResizeHandler, true);
    window.addEventListener("resize", this._ctxResizeHandler);
    window.addEventListener("ui:edit-request", this._editHandler);
  },
  watch: {
    "$store.state.user"(v, o) {
      if (v?.id && v?.id !== o?.id) {
        this.nacitajDostupnost().then(() => {
          this.showFilter = this.hasCalendar && this.povoleneDni.length > 0;
          this.scheduleFlowReload();
        });
        this.fetchHoursCount();
        this.fetchStudentsUnassignedCount();
      }
    },
    editRequest(req) {
      if (!req) return;
      this.onEditRequest(req);
      this.$nextTick(() => this.$store.commit("ui/CLEAR_EDIT"));
    },
  },
  unmounted() {
    window.removeEventListener("keydown", this._ctxKeyHandler);
    window.removeEventListener("mousedown", this._ctxOutsideHandler, true);
    window.removeEventListener("scroll", this._ctxResizeHandler, true);
    window.removeEventListener("resize", this._ctxResizeHandler);
    window.removeEventListener("ui:edit-request", this._editHandler);
  },
};
</script>

<style scoped lang="scss">
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
.button {
  position: absolute;
  bottom: 3%;
  left: 3%;
  font-size: 1em;
}
.set {
  position: absolute;
  top: 5%;
  left: 4%;
  transform: translate(-50%, -50%);
  width: 2em;
}
.set:hover {
  transform: translate(-50%, -50%) scale(1.1);
  cursor: pointer;
  transition-duration: 0.2s;
}
.context-menu {
  position: fixed;
  left: 0;
  top: 0;
  z-index: 9999;
  min-width: 150px;
  border-radius: 0.5rem;
  background: #ffffff;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18), 0 2px 8px rgba(0, 0, 0, 0.12);
  border: 1px solid rgba(0, 0, 0, 0.06);
  transform-origin: top left;
  will-change: transform, opacity;
}
.ctx-item {
  width: 100%;
  text-align: left;
  padding: 0.6rem 0.75rem;
  border: 0;
  background: transparent;
  border-radius: 0.375rem;
  cursor: pointer;
  font-weight: 600;
  line-height: 1.25;
}
.ctx-item:hover {
  background: #f5f5f5;
}
.ctx-err {
  color: #c62828;
  font-size: 0.85rem;
  margin: 0.25rem 0.5rem 0;
}
.context-menu .danger {
  color: #c62828;
}
.ctx-enter-active,
.ctx-leave-active {
  transition: opacity 120ms ease, transform 140ms cubic-bezier(0.2, 0.8, 0.2, 1);
}
.ctx-enter-from,
.ctx-leave-to {
  opacity: 0;
  transform: scale(0.96) translateY(-2px);
}
</style>
