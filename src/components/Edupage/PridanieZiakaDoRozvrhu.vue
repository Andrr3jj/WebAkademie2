<template>
  <div class="formular" v-if="!edit">
    <h5>Pridať žiaka do rozvrhu</h5>
    <p class="text">
      Vyber konkrétneho žiaka, ktorého chceš zaradiť do výučby, a nastav čas,
      kedy bude mať svoju lekciu.
    </p>
    <div class="pridanie">
      <div class="boxik forma">
        <p class="nadpis">Zvoľ formu výučby:</p>
        <div class="vyucba solo">
          <div @click="dual = false" :class="{ active: !dual }" class="button">
            <p>Solo</p>
          </div>
          <p class="popis">Lekcia s jedným žiakom</p>
        </div>
        <div class="vyucba duo">
          <div @click="dual = true" :class="{ active: dual }" class="button">
            <p>Dual</p>
          </div>
          <p class="popis">Lekcia s dvoma žiakmi</p>
        </div>
      </div>
      <div class="line"></div>
      <div class="boxik">
        <p class="nadpis">Vyber žiaka a zvol čas výučby:</p>
        <div class="ziak">
          <select name="Žiak" id="vyberZiaka" v-model="ziak1Id">
            <option value="" disabled>Vyber žiaka</option>
            <option v-for="s in students" :key="s.id" :value="s.id">
              {{ s.name }}
            </option>
          </select>
          <select name="cas" id="odCas" v-model="zvolenyCas">
            <option value="" disabled>Vyber čas</option>
            <option v-for="t in casoveMoznosti" :key="t" :value="t">
              {{ t }}
            </option>
          </select>
        </div>
        <div v-if="dual" class="ziak">
          <select name="Žiak" id="vyberZiaka2" v-model="ziak2Id">
            <option value="" disabled>Vyber druhého žiaka</option>
            <option v-for="s in studentsDruhePole" :key="s.id" :value="s.id">
              {{ s.name }}
            </option>
          </select>
          <select name="cas" id="odCas2" v-model="zvolenyCas">
            <option value="" disabled>Vyber čas</option>
            <option v-for="t in casoveMoznosti" :key="t" :value="t">
              {{ t }}
            </option>
          </select>
        </div>
      </div>
    </div>
    <div class="button Adumu" @click="odoslat" :aria-busy="loading">
      {{ loading ? "Pridávam..." : "Pridať" }}
    </div>
    <p v-if="error" class="text small" style="color: #c0392b">{{ error }}</p>
    <p v-if="message" class="text small" style="color: #2e7d32">
      {{ message }}
    </p>
  </div>

  <div class="formular" v-else>
    <h5>{{ editHeadlineText }}</h5>
    <p class="text">Vyber deň a čas hodiny.</p>
    <div class="pridanie">
      <div class="boxik" style="width: 100%">
        <p class="nadpis">Deň a čas:</p>
        <div class="ziak">
          <select v-model="selectedEditDay">
            <option value="" disabled>Vyber deň</option>
            <option v-for="d in dniPoPi" :key="d" :value="d">{{ d }}</option>
          </select>
          <select v-model="selectedEditTime">
            <option value="" disabled>Vyber čas</option>
            <option v-for="t in casoveMoznostiEdit" :key="t" :value="t">
              {{ t }}
            </option>
          </select>
        </div>
      </div>
    </div>
    <div class="button Adumu" @click="ulozitEdit" :aria-busy="loading">
      {{ loading ? "Ukladám..." : "Uložiť zmeny" }}
    </div>
    <div
      class="button Adumu"
      style="margin-top: 1em; background: #f86e5c"
      @click="vymazatEdit"
      :aria-busy="loading"
    >
      {{ loading ? "Mažem..." : "Vymazať" }}
    </div>
    <p v-if="error" class="text small" style="color: #c0392b">{{ error }}</p>
    <p v-if="message" class="text small" style="color: #2e7d32">
      {{ message }}
    </p>
  </div>

  <div v-if="showPlaceWarning" class="modal-overlay">
    <div class="modal">
      <h5>Pozor na mesto výučby</h5>
      <p v-html="placeWarningText.replace(/\n/g, '<br>')"></p>
      <div class="modal-actions">
        <button @click="showPlaceWarning = false">Zrušiť</button>
        <button @click="potvrdPlaceWarning">Pokračovať</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "PridanieZiakaDoRozvrhu",
  props: {
    predvolenyDen: { type: String, default: "" },
    predvolenyCas: { type: String, default: "" },
    poslatDvakratPriDuo: { type: Boolean, default: false },
    edit: { type: Boolean, default: false },
    editId: { type: [String, Number], default: null },
    calendarId: { type: [String, Number], default: null },
    dayName: { type: String, default: "" },
    calendarDayName: { type: String, default: "" },
    hour: { type: String, default: "" },
    calendarHour: { type: String, default: "" },
    formOfStudy: { type: String, default: "" },
  },
  data() {
    return {
      dual: false,
      loading: false,
      message: "",
      error: "",
      dostupnost: [],
      obsadene: {},
      students: [],
      ziak1Id: "",
      ziak2Id: "",
      zvolenyCas: "",
      slotKrokMin: 15,
      selectedEditDay: "",
      selectedEditTime: "",
      existujuciStudenti: [],
      existujucaForma: "solo",
      editNames: [],
      showPlaceWarning: false,
      placeWarningText: "",
      continueCtx: null,
    };
  },
  computed: {
    resolvedEditId() {
      return this.editId != null ? this.editId : this.calendarId;
    },
    incomingDay() {
      return this.dayName || this.calendarDayName || "";
    },
    incomingHour() {
      return this.hour || this.calendarHour || "";
    },
    incomingForm() {
      return (this.formOfStudy || "").toLowerCase();
    },
    apiBase() {
      return (this.$store?.state?.api || "").replace(/\/+$/, "") + "/edupage/";
    },
    apiUser() {
      return (this.$store?.state?.api || "").replace(/\/+$/, "") + "/user/";
    },
    apiInfo() {
      return (
        (this.$store?.state?.api || "").replace(/\/+$/, "") + "/user/info/"
      );
    },
    teacherId() {
      const allowed = [59, 54, 607, 945, 53];
      const id = Number(this.$store?.state?.user?.id || 0);
      return allowed.includes(id) ? id : null;
    },
    denNaOdoslanie() {
      if (this.edit) return this.selectedEditDay;
      if (this.predvolenyDen) return this.predvolenyDen;
      const dni = [
        "Nedeľa",
        "Pondelok",
        "Utorok",
        "Streda",
        "Štvrtok",
        "Piatok",
        "Sobota",
      ];
      return dni[new Date().getDay()];
    },
    casoveMoznosti() {
      const den = this.denNaOdoslanie;
      if (!den) return [];
      const bloky = this.dostupnost.filter((b) => b.den === den);
      if (!bloky.length) return [];
      const trvanie = this.dual ? 45 : 30;
      const set = new Set();
      for (const b of bloky) {
        for (const t of this.genSlotsFit(b.od, b.do, this.slotKrokMin, trvanie))
          set.add(t);
      }
      const allSlots = Array.from(set).sort();
      const obs = this.obsadene[den] || [];
      const blocked = [];
      for (const item of obs) {
        const startMin = this.toMin(item.time);
        const dur = item.formOfStudy === "duo" ? 45 : 30;
        blocked.push([startMin, startMin + dur]);
      }
      return allSlots.filter((slot) => {
        const s = this.toMin(slot);
        const e = s + trvanie;
        return !blocked.some(([bs, be]) => s < be && bs < e);
      });
    },
    casoveMoznostiEdit() {
      const den = this.selectedEditDay;
      if (!den) return [];
      const bloky = this.dostupnost.filter((b) => b.den === den);
      if (!bloky.length) return [];
      const trvanie = this.existujucaForma === "duo" ? 45 : 30;
      const set = new Set();
      for (const b of bloky) {
        for (const t of this.genSlotsFit(b.od, b.do, this.slotKrokMin, trvanie))
          set.add(t);
      }
      const allSlots = Array.from(set).sort();
      const obs = this.obsadene[den] || [];
      const blocked = [];
      for (const item of obs) {
        if (String(item.id) === String(this.resolvedEditId)) continue;
        const startMin = this.toMin(item.time);
        const dur = item.formOfStudy === "duo" ? 45 : 30;
        blocked.push([startMin, startMin + dur]);
      }
      return allSlots.filter((slot) => {
        const s = this.toMin(slot);
        const e = s + trvanie;
        return !blocked.some(([bs, be]) => s < be && bs < e);
      });
    },
    studentsDruhePole() {
      return this.students.filter((s) => String(s.id) !== String(this.ziak1Id));
    },
    dniPoPi() {
      const poPi = ["Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok"];
      const dostupne = Array.from(new Set(this.dostupnost.map((x) => x.den)));
      const prienik = poPi.filter((d) => dostupne.includes(d));
      return prienik.length ? prienik : poPi;
    },
    editHeadlineText() {
      const n = this.editNames.filter(Boolean);
      if (!n.length) return "Zmeňte hodinu";
      if (n.length > 1) return "Zmeňte hodinu týmto žiakom " + n.join(" a ");
      return "Zmeňte hodinu tomuto žiakovi " + n[0];
    },
  },
  methods: {
    dayToInt(den) {
      const map = {
        Pondelok: 0,
        Utorok: 1,
        Streda: 2,
        Štvrtok: 3,
        Piatok: 4,
        Sobota: 5,
        Nedeľa: 6,
      };
      return typeof den === "number" ? den : map[den] ?? den;
    },
    intToDay(v) {
      const arr = [
        "Pondelok",
        "Utorok",
        "Streda",
        "Štvrtok",
        "Piatok",
        "Sobota",
        "Nedeľa",
      ];
      const n = Number(v);
      return Number.isFinite(n) ? arr[n] ?? String(v) : String(v);
    },
    normalizeHour(v) {
      const s = String(v ?? "");
      if (/^\d{1,4}$/.test(s)) {
        const n = parseInt(s, 10);
        const h = Math.floor(n / 100);
        const m = n % 100;
        return String(h).padStart(2, "0") + ":" + String(m).padStart(2, "0");
      }
      if (/^\d{2}:\d{2}$/.test(s)) return s;
      return s;
    },
    hourToIntHHMM(hhmm) {
      const [h, m] = String(hhmm || "00:00")
        .split(":")
        .map((x) => parseInt(x, 10) || 0);
      return String(h).padStart(2, "0") + String(m).padStart(2, "0");
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
    ceilToStep(min, step) {
      return Math.ceil(min / step) * step;
    },
    genSlotsFit(od, doo, step, durationMin) {
      let s = this.toMin(od);
      const e = this.toMin(doo);
      s = this.ceilToStep(s, step);
      const out = [];
      for (let t = s; t + durationMin <= e; t += step) out.push(this.toHHMM(t));
      return out;
    },
    chooseNearest(preferred, options) {
      if (!options.length) return "";
      const pref = this.toMin(preferred);
      let best = options[0];
      let bestDiff = Math.abs(this.toMin(best) - pref);
      for (const opt of options) {
        const d = Math.abs(this.toMin(opt) - pref);
        if (d < bestDiff) {
          best = opt;
          bestDiff = d;
        }
      }
      return best;
    },
    async nacitajDostupnost() {
      if (!this.teacherId) return;
      const url =
        this.apiBase + "loadCalendarCustom.php?teacher_id=" + this.teacherId;
      try {
        const res = await fetch(url, { credentials: "include" });
        const txt = await res.text();
        let out;
        try {
          out = JSON.parse(txt);
        } catch {
          out = null;
        }
        const rows = out?.data && Array.isArray(out.data) ? out.data : [];
        this.dostupnost = rows
          .map((r) => {
            let payload = r.data || "{}";
            if (typeof payload === "string") {
              try {
                payload = JSON.parse(payload);
              } catch {
                payload = {};
              }
              if (typeof payload === "string") {
                try {
                  payload = JSON.parse(payload);
                } catch {
                  payload = {};
                }
              }
            }
            return {
              den: payload.den || "",
              od: payload.od || "",
              do: payload.do || "",
              mesto:
                payload.miesto ||
                payload.placeOfStudy ||
                payload.city ||
                payload.location ||
                "",
            };
          })
          .filter((x) => x.den && x.od && x.do);
      } catch {
        this.dostupnost = [];
      }
    },
    async nacitajObsadene() {
      if (!this.teacherId) return;
      try {
        const res = await fetch(
          this.apiBase + "loadCalendar.php?teacher_id=" + this.teacherId,
          {
            credentials: "include",
          }
        );
        const txt = await res.text();
        let out;
        try {
          out = JSON.parse(txt);
        } catch {
          out = null;
        }
        const arr = out?.data && Array.isArray(out.data) ? out.data : [];
        const map = {};
        for (const r of arr) {
          const rawDay = r.availability_day ?? r.day ?? r.den ?? "";
          const day = this.intToDay(rawDay);
          const hourRaw = r.availability_hour || r.hour || r.cas || "";
          const hour = this.normalizeHour(hourRaw);
          const id = Number(r.id || r.ID || 0) || null;
          const form =
            (r.formOfStudy || "").toLowerCase() === "duo" ? "duo" : "solo";
          if (!day || !hour || !id) continue;
          if (!map[day]) map[day] = [];
          map[day].push({ id, time: hour, formOfStudy: form });
        }
        this.obsadene = map;
      } catch {
        this.obsadene = {};
      }
    },
    safeJsonParse(txt) {
      try {
        return JSON.parse(txt);
      } catch {
        // ignore
      }
      const start = txt.indexOf("{");
      const end = txt.lastIndexOf("}");
      if (start !== -1 && end !== -1 && end > start) {
        try {
          return JSON.parse(txt.slice(start, end + 1));
        } catch {
          return null;
        }
      }
      return null;
    },
    async nacitajZiakov() {
      try {
        const res = await fetch(this.apiBase + "loadStudentsUnassigned.php", {
          credentials: "include",
        });
        const txt = await res.text();
        const out = this.safeJsonParse(txt);
        let rows = Array.isArray(out?.data) ? out.data : [];
        const myTid = String(
          this.teacherId || this.$store?.state?.user?.id || ""
        );
        if (myTid) rows = rows.filter((s) => String(s.teacher_id) === myTid);
        const pickBetter = (a, b) => {
          const ta = String(a.approximateTimeOfStudy || "").trim() !== "";
          const tb = String(b.approximateTimeOfStudy || "").trim() !== "";
          if (ta !== tb) return ta ? a : b;
          const ia = Number(a.timestamp_included || 0);
          const ib = Number(b.timestamp_included || 0);
          if (ia !== ib) return ia > ib ? a : b;
          const ca = Number(a.timestamp_created || 0);
          const cb = Number(b.timestamp_created || 0);
          if (ca !== cb) return ca > cb ? a : b;
          const ida = Number(a.id || 0);
          const idb = Number(b.id || 0);
          return ida >= idb ? a : b;
        };
        const byStudent = new Map();
        for (const r of rows) {
          const sid = String(r.student_id || r.id);
          if (!byStudent.has(sid)) byStudent.set(sid, r);
          else byStudent.set(sid, pickBetter(byStudent.get(sid), r));
        }
        const toMinutes = (hhmm) => {
          const norm = this.normalizeHour(hhmm || "");
          if (!/^\d{2}:\d{2}$/.test(norm)) return Number.POSITIVE_INFINITY;
          const [h, m] = norm.split(":").map((n) => parseInt(n, 10) || 0);
          return h * 60 + m;
        };
        const sorted = Array.from(byStudent.values()).sort((a, b) => {
          const placeA = (a.placeOfStudy || "").trim();
          const placeB = (b.placeOfStudy || "").trim();
          const cmpPlace = placeA.localeCompare(placeB, "sk", {
            sensitivity: "base",
          });
          if (cmpPlace !== 0) return cmpPlace;
          return (
            toMinutes(a.approximateTimeOfStudy) -
            toMinutes(b.approximateTimeOfStudy)
          );
        });
        this.students = sorted.map((d) => {
          const meno = [d.name, d.surname].filter(Boolean).join(" ").trim();
          const label =
            (meno || d.fullname || String(d.student_id || d.id)) +
            " - " +
            (d.formOfStudy || "") +
            " - " +
            (d.placeOfStudy || "") +
            (d.approximateTimeOfStudy
              ? ` (${this.normalizeHour(d.approximateTimeOfStudy)})`
              : "");
          return {
            id: String(d.student_id || d.id),
            name: label,
            preferredTime: this.normalizeHour(d.approximateTimeOfStudy || ""),
            placeOfStudy: d.placeOfStudy || "",
          };
        });
      } catch {
        this.students = [];
      }
    },
    getTeacherPlaceFor(dayName, hhmm) {
      const blocks = this.dostupnost.filter((b) => b.den === dayName);
      if (!blocks.length) return "";
      const want = this.toMin(this.normalizeHour(hhmm));
      for (const b of blocks) {
        const s = this.toMin(b.od);
        const e = this.toMin(b.do);
        if (want >= s && want < e) return (b.mesto || "").trim();
      }
      let best = null;
      let bestDiff = Infinity;
      for (const b of blocks) {
        const s = this.toMin(b.od);
        const e = this.toMin(b.do);
        const diff = want < s ? s - want : want > e ? want - e : 0;
        if (diff < bestDiff) {
          bestDiff = diff;
          best = b;
        }
      }
      if (best && best.mesto) return String(best.mesto).trim();
      const counts = new Map();
      for (const b of blocks) {
        const m = String(b.mesto || "").trim();
        if (!m) continue;
        counts.set(m, (counts.get(m) || 0) + 1);
      }
      if (counts.size) {
        return Array.from(counts.entries()).sort((a, b) => b[1] - a[1])[0][0];
      }
      return "";
    },
    async potvrdPlaceWarning() {
      this.showPlaceWarning = false;
      await this.odoslatPokracovanie();
    },
    makePlaceWarning({ teacherPlace, students }) {
      const lines = [];
      for (const s of students) {
        if (!s) continue;
        const baseName = s.name.replace(/\s+-\s+.*/, "");
        const zvolil = (s.placeOfStudy || "").trim() || "—";
        if (
          teacherPlace &&
          zvolil &&
          zvolil.toLowerCase() !== teacherPlace.toLowerCase()
        ) {
          lines.push(
            `• ${baseName}: prihláška = "${zvolil}", dávate = "${teacherPlace}"`
          );
        }
      }
      if (!lines.length) return "";
      return [
        "Pozor! Mesto sa nezhoduje:",
        ...lines,
        "",
        "Chceš pokračovať a poslať do emailu mesto podľa rozvrhu učiteľa?",
      ].join("\n");
    },
    validuj() {
      if (!this.teacherId) {
        this.error = "Nemáš oprávnenie.";
        return false;
      }
      if (!this.ziak1Id) {
        this.error = "Vyber žiaka.";
        return false;
      }
      if (this.dual && !this.ziak2Id) {
        this.error = "Vyber druhého žiaka pre Duo.";
        return false;
      }
      if (
        this.dual &&
        this.ziak1Id &&
        this.ziak2Id &&
        String(this.ziak1Id) === String(this.ziak2Id)
      ) {
        this.error = "Nemôžeš vybrať toho istého žiaka dvakrát.";
        return false;
      }
      if (!this.zvolenyCas) {
        this.error = "Vyber čas.";
        return false;
      }
      if (!this.casoveMoznosti.includes(this.zvolenyCas)) {
        this.error = "Čas nie je v tvojich dostupných hodinách.";
        return false;
      }
      return true;
    },
    nastavDefaultCas() {
      if (this.edit) {
        if (!this.selectedEditDay)
          this.selectedEditDay = this.dniPoPi[0] || "Pondelok";
        const moznosti = this.casoveMoznostiEdit;
        if (!moznosti.length) {
          this.selectedEditTime = "";
          return;
        }
        if (this.selectedEditTime && moznosti.includes(this.selectedEditTime))
          return;
        if (this.selectedEditTime) {
          this.selectedEditTime = this.chooseNearest(
            this.selectedEditTime,
            moznosti
          );
          return;
        }
        this.selectedEditTime = moznosti[0];
        return;
      }
      if (!this.casoveMoznosti.length) {
        this.zvolenyCas = "";
        return;
      }
      const ziaci = [];
      if (this.ziak1Id) {
        const z1 = this.students.find(
          (s) => String(s.id) === String(this.ziak1Id)
        );
        if (z1) ziaci.push(z1);
      }
      if (this.dual && this.ziak2Id) {
        const z2 = this.students.find(
          (s) => String(s.id) === String(this.ziak2Id)
        );
        if (z2) ziaci.push(z2);
      }
      if (!this.dual && ziaci[0]?.preferredTime) {
        const norm = this.normalizeHour(ziaci[0].preferredTime);
        if (this.casoveMoznosti.includes(norm)) {
          this.zvolenyCas = norm;
          return;
        }
        this.zvolenyCas = this.chooseNearest(norm, this.casoveMoznosti);
        return;
      }
      if (this.dual && ziaci.length === 2) {
        const norm1 = ziaci[0].preferredTime
          ? this.normalizeHour(ziaci[0].preferredTime)
          : null;
        const norm2 = ziaci[1].preferredTime
          ? this.normalizeHour(ziaci[1].preferredTime)
          : null;
        if (norm1 && norm2) {
          if (norm1 === norm2 && this.casoveMoznosti.includes(norm1)) {
            this.zvolenyCas = norm1;
            return;
          }
          const nearest1 = norm1
            ? this.chooseNearest(norm1, this.casoveMoznosti)
            : null;
          const nearest2 = norm2
            ? this.chooseNearest(norm2, this.casoveMoznosti)
            : null;
          if (nearest1 && nearest1 === nearest2) this.zvolenyCas = nearest1;
          else this.zvolenyCas = this.casoveMoznosti[0];
          return;
        }
      }
      if (
        this.predvolenyCas &&
        this.casoveMoznosti.includes(this.predvolenyCas)
      ) {
        this.zvolenyCas = this.predvolenyCas;
        return;
      }
      if (this.predvolenyCas) {
        this.zvolenyCas = this.chooseNearest(
          this.predvolenyCas,
          this.casoveMoznosti
        );
        return;
      }
      this.zvolenyCas = this.casoveMoznosti[0];
    },
    async overKolizie(den, casNum, excludeId = null, duoFlag = null) {
      await this.nacitajObsadene();
      const zoznam = this.obsadene[den] || [];
      const novyStart = this.toMin(this.normalizeHour(casNum));
      const isDuo = duoFlag != null ? duoFlag : this.dual;
      const novyTrvanie = isDuo ? 45 : 30;
      const novyEnd = novyStart + novyTrvanie;
      for (const exist of zoznam) {
        if (excludeId && String(exist.id) === String(excludeId)) continue;
        const existStart = this.toMin(exist.time);
        const existTrvanie = exist.formOfStudy === "duo" ? 45 : 30;
        const existEnd = existStart + existTrvanie;
        if (novyStart < existEnd && existStart < novyEnd)
          return `Kolízia: ${exist.time} (${exist.formOfStudy})`;
      }
      return null;
    },
    async callCreateCalendar(formOfStudy, day, hour, ...studentIds) {
      const body = new URLSearchParams();
      body.append("formOfStudy", formOfStudy);
      body.append("availability_day", String(this.dayToInt(day)));
      body.append("availability_hour", this.hourToIntHHMM(hour));
      const validIds = studentIds.filter((id) => id && String(id) !== "null");
      if (validIds.length) body.append("student", JSON.stringify(validIds));
      const res = await fetch(this.apiBase + "createCalendar.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
        },
        body,
        credentials: "include",
      });
      const txt = await res.text();
      let out;
      try {
        out = JSON.parse(txt);
      } catch {
        out = { ok: res.ok, raw: txt };
      }
      if (!res.ok || out?.status === "Request failed" || out?.error) {
        throw new Error(
          out?.message || out?.data || out?.error || `HTTP ${res.status}`
        );
      }
      return out;
    },
    async callEditCalendarUpdate(payload) {
      const body = new URLSearchParams();
      body.append("id", String(payload.id));
      if (payload.formOfStudy) body.append("formOfStudy", payload.formOfStudy);
      if (payload.availability_day != null)
        body.append("availability_day", String(payload.availability_day));
      if (payload.availability_hour)
        body.append("availability_hour", payload.availability_hour);
      if (payload.student && Array.isArray(payload.student))
        body.append("student", JSON.stringify(payload.student));
      const res = await fetch(this.apiBase + "editCalendar.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
        },
        body,
        credentials: "include",
      });
      const txt = await res.text();
      let out;
      try {
        out = JSON.parse(txt);
      } catch {
        out = null;
      }
      const ok =
        res.ok &&
        (out === true ||
          out?.ok === true ||
          out?.success === true ||
          /^(ok|true|1)$/i.test(String(out?.status || "")) ||
          /^(ok|true|1)$/i.test(txt.trim()));
      if (!ok)
        throw new Error(out?.message || out?.error || "Uloženie zlyhalo.");
      return true;
    },
    async callEditCalendarDelete(id) {
      const body = new URLSearchParams();
      body.append("id", String(id));
      body.append("delete", "true");
      const res = await fetch(this.apiBase + "editCalendar.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
        },
        body,
        credentials: "include",
      });
      const txt = await res.text();
      let out;
      try {
        out = JSON.parse(txt);
      } catch {
        out = null;
      }
      const ok =
        res.ok &&
        (out === true ||
          out?.ok === true ||
          out?.success === true ||
          /^(ok|true|1)$/i.test(String(out?.status || "")) ||
          /^(ok|true|1)$/i.test(txt.trim()));
      if (!ok)
        throw new Error(out?.message || out?.error || "Mazanie zlyhalo.");
      return true;
    },
    async sendStudentEmail(studentId, placeOverride) {
      if (!studentId) return { ok: false };
      const body = new URLSearchParams();
      body.append("student_id", studentId);
      if (placeOverride && String(placeOverride).trim()) {
        body.append("placeOfStudy", String(placeOverride).trim());
      }
      const res = await fetch(this.apiBase + "calendarSendEmailStudent.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
        },
        body,
        credentials: "include",
      });
      const txt = await res.text();
      let out;
      try {
        out = JSON.parse(txt);
      } catch {
        out = null;
      }
      const ok =
        res.ok &&
        (!out ||
          out?.status === "Request succesfull" ||
          out?.status === "Request fullfiled");
      return { ok, raw: out || txt };
    },
    async odoslat() {
      this.error = "";
      this.message = "";
      if (!this.validuj()) return;

      const den = this.denNaOdoslanie;
      const cas = this.zvolenyCas;

      const kol = await this.overKolizie(den, cas, null, this.dual);
      if (kol) {
        this.error = kol;
        return;
      }

      const teacherPlace = this.getTeacherPlaceFor(den, cas);
      const z1 = this.students.find(
        (s) => String(s.id) === String(this.ziak1Id)
      );
      const z2 =
        this.dual && this.ziak2Id
          ? this.students.find((s) => String(s.id) === String(this.ziak2Id))
          : null;

      if (teacherPlace) {
        const warn = this.makePlaceWarning({
          teacherPlace,
          students: [z1, z2].filter(Boolean),
        });
        if (warn) {
          this.placeWarningText = warn;
          this.continueCtx = {
            den,
            cas,
            teacherPlace,
            ids: [this.ziak1Id, this.dual ? this.ziak2Id : null].filter(
              Boolean
            ),
            form: this.dual ? "duo" : "solo",
          };
          this.showPlaceWarning = true;
          return;
        }
      }

      await this.vykonajVytvorenie(
        den,
        cas,
        this.dual ? "duo" : "solo",
        teacherPlace,
        [this.ziak1Id, this.dual ? this.ziak2Id : null]
      );
    },
    async odoslatPokracovanie() {
      if (!this.continueCtx) return;
      const ctx = this.continueCtx;
      this.continueCtx = null;
      await this.vykonajVytvorenie(
        ctx.den,
        ctx.cas,
        ctx.form,
        ctx.teacherPlace,
        ctx.ids
      );
    },
    async vykonajVytvorenie(den, cas, forma, teacherPlace, ids) {
      this.loading = true;
      this.error = "";
      this.message = "";
      try {
        // Vždy vyčisti zoznam ID (môže obsahovať null pri SOLO)
        const validIds = (Array.isArray(ids) ? ids : []).filter(
          (id) => id && String(id) !== "null"
        );

        if (forma === "duo" && this.poslatDvakratPriDuo) {
          await Promise.all([
            this.callCreateCalendar("duo", den, cas, ...validIds),
            this.callCreateCalendar("duo", den, cas, ...validIds),
          ]);
        } else {
          await this.callCreateCalendar(forma, den, cas, ...validIds);
        }

        // Po vytvorení: poslať emaily iba pre validné ID
        const emailResults = [];
        for (const sid of validIds) {
          const r = await this.sendStudentEmail(sid, teacherPlace);
          emailResults.push(r.ok);
        }

        await this.nacitajObsadene();
        this.nastavDefaultCas();

        const emailOk =
          emailResults.length === 0 || emailResults.every(Boolean);
        this.message = emailOk
          ? "Hodina bola pridaná a email odoslaný."
          : "Hodina bola pridaná. Email sa nepodarilo odoslať všetkým.";
        this.$emit("created");
      } catch (e) {
        this.error = e?.message || "Nepodarilo sa pridať hodinu.";
      } finally {
        this.loading = false;
      }
    },
    async nacitajDetailHodiny(id) {
      if (!id) return;
      try {
        const url =
          this.apiBase + "loadCalendar.php?id=" + encodeURIComponent(id);
        const res = await fetch(url, { credentials: "include" });
        const txt = await res.text();
        let out;
        try {
          out = JSON.parse(txt);
        } catch {
          out = null;
        }
        const arr = Array.isArray(out?.data)
          ? out.data
          : Array.isArray(out)
          ? out
          : [];
        const r = arr[0] || null;
        if (!r) return;
        const dayRaw = r.availability_day ?? r.day ?? r.den ?? "";
        const hourRaw = r.availability_hour ?? r.hour ?? r.cas ?? "";
        const form =
          (r.formOfStudy || "").toLowerCase() === "duo" ? "duo" : "solo";
        let ids = [];
        let stRaw = r.student ?? r.students ?? "";
        if (typeof stRaw === "string" && stRaw.trim()) {
          try {
            stRaw = JSON.parse(stRaw);
          } catch {
            stRaw = [];
          }
        }
        if (Array.isArray(stRaw)) {
          ids = stRaw.map((x) => String(x?.student_id ?? x));
        }
        this.existujuciStudenti = ids;
        this.existujucaForma = form;
        this.selectedEditDay = this.intToDay(dayRaw);
        this.selectedEditTime = this.normalizeHour(hourRaw);
        await this.nacitajMena(ids);
      } catch {
        // ignore
      }
    },
    async nacitajMena(ids) {
      const out = [];
      for (const sid of ids) {
        const n = await this.fetchStudentName(sid);
        out.push(n || "ID " + sid);
      }
      this.editNames = out;
    },
    async fetchStudentName(id) {
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
        const full = [first, last].filter(Boolean).join(" ").trim();
        return full || "ID " + id;
      } catch {
        return "ID " + id;
      }
    },
    async ulozitEdit() {
      this.error = "";
      this.message = "";
      if (!this.resolvedEditId) {
        this.error = "Chýba ID hodiny.";
        return;
      }
      if (!this.selectedEditDay) {
        this.error = "Vyber deň.";
        return;
      }
      if (!this.selectedEditTime) {
        this.error = "Vyber čas.";
        return;
      }
      const kol = await this.overKolizie(
        this.selectedEditDay,
        this.selectedEditTime,
        this.resolvedEditId,
        this.existujucaForma === "duo"
      );
      if (kol) {
        this.error = kol;
        return;
      }
      this.loading = true;
      try {
        await this.callEditCalendarUpdate({
          id: this.resolvedEditId,
          formOfStudy: this.existujucaForma,
          availability_day: this.dayToInt(this.selectedEditDay),
          availability_hour: this.hourToIntHHMM(this.selectedEditTime),
          student: this.existujuciStudenti,
        });
        await this.nacitajObsadene();
        this.message = "Hodina bola upravená.";
        this.$emit("updated", {
          id: this.resolvedEditId,
          day: this.selectedEditDay,
          time: this.selectedEditTime,
        });
      } catch (e) {
        this.error = e?.message || "Uloženie zlyhalo.";
      } finally {
        this.loading = false;
      }
    },
    async vymazatEdit() {
      this.error = "";
      this.message = "";
      if (!this.resolvedEditId) {
        this.error = "Chýba ID hodiny.";
        return;
      }
      this.loading = true;
      try {
        await this.callEditCalendarDelete(this.resolvedEditId);
        this.message = "Hodina bola vymazaná.";
        this.$emit("deleted", { id: this.resolvedEditId });
      } catch (e) {
        this.error = e?.message || "Mazanie zlyhalo.";
      } finally {
        this.loading = false;
      }
    },
  },
  async mounted() {
    if (this.edit) {
      await Promise.all([this.nacitajDostupnost(), this.nacitajObsadene()]);
      if (this.resolvedEditId) {
        await this.nacitajDetailHodiny(this.resolvedEditId);
      } else {
        if (this.incomingForm)
          this.existujucaForma = this.incomingForm === "duo" ? "duo" : "solo";
        if (this.incomingDay) this.selectedEditDay = this.incomingDay;
        if (this.incomingHour)
          this.selectedEditTime = this.normalizeHour(this.incomingHour);
      }
      this.nastavDefaultCas();
    } else {
      if (this.incomingForm) this.dual = this.incomingForm === "duo";
      await Promise.all([
        this.nacitajZiakov(),
        this.nacitajDostupnost(),
        this.nacitajObsadene(),
      ]);
      this.nastavDefaultCas();
    }
  },
  watch: {
    dual() {
      this.nastavDefaultCas();
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
.text.smaller {
  font-size: 1.6em;
}
.text.small {
  font-size: 1em;
}
select {
  appearance: none;
  -moz-appearance: none;
  -webkit-appearance: none;
  background-image: url("@/assets/images/icons/rozbalenie.png");
  background-repeat: no-repeat;
  background-position: right 1rem center;
  background-size: 0.8em;
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
}
select:hover {
  background-color: #90ca50;
  transform: translateY(-3px);
  cursor: pointer;
  box-shadow: 0.1em 0.1em 0.5em #00000040;
}
select:active {
  transform: translateY(1px);
  background-color: #ffeb3b;
  filter: brightness(0.95);
}
.active {
  background: #90ca50;
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
.vyucba {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 2em;
}
.solo {
  margin-bottom: 1em;
}
.button {
  width: fit-content;
  margin: auto;
}
.vyucba .button {
  margin: 0;
  border-radius: 0.7em;
  font-size: 1.1em;
  width: fit-content;
  margin: auto;
}
.duo .button {
  margin: 0;
}
.ziak {
  display: flex;
  gap: 1em;
  justify-content: space-between;
}
#vyberZiaka {
  margin-bottom: 1.5em;
  flex: 0.8;
}
#odCas {
  flex: 0.2;
  height: max-content;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
}
.modal {
  background: #fff;
  border-radius: 1em;
  padding: 2em;
  max-width: 500px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.4);
  text-align: center;
}
.modal-actions {
  margin-top: 1.5em;
  display: flex;
  justify-content: space-around;
}
.modal-actions button {
  padding: 0.6em 1.2em;
  border: none;
  border-radius: 0.5em;
  font-size: 1em;
  cursor: pointer;
}
.modal-actions button:first-child {
  background: #f86e5c;
}
.modal-actions button:last-child {
  background-color: #ffeb3b;
}
</style>
