<template>
  <div class="formular">
    <h5>{{ headline }}</h5>
    <p class="text">Vyber deň a čas hodiny.</p>
    <div class="pridanie">
      <div class="boxik" style="width: 100%">
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
      class="button Adumu danger"
      @click="vymazatEdit"
      :aria-busy="loading"
      style="margin-top: 1em"
    >
      {{ loading ? "Mažem..." : "Vymazať" }}
    </div>
    <p v-if="error" class="text small" style="color: #c0392b">{{ error }}</p>
    <p v-if="message" class="text small" style="color: #2e7d32">
      {{ message }}
    </p>
  </div>
</template>

<script>
export default {
  name: "UpravaHodiny",
  emits: ["updated", "deleted"],
  props: {
    calendarId: { type: [String, Number], required: true },
    dayName: { type: String, default: "" },
    hour: { type: String, default: "" },
    formOfStudy: { type: String, default: "" },
    studentId: { type: [String, Number], default: null },
    duoStudentId: { type: [String, Number], default: null },
  },
  data() {
    return {
      loading: false,
      message: "",
      error: "",
      dostupnost: [],
      obsadene: {},
      selectedEditDay: "",
      selectedEditTime: "",
      existujucaForma: "solo",
      slotKrokMin: 15,
      existujuciStudenti: [],
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
    dniPoPi() {
      const poPi = ["Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok"];
      const dostupne = Array.from(new Set(this.dostupnost.map((x) => x.den)));
      const prienik = poPi.filter((d) => dostupne.includes(d));
      return prienik.length ? prienik : poPi;
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
        if (String(item.id) === String(this.calendarId)) continue;
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
    headline() {
      return "Upraviť hodinu";
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
            let d = r.data || "{}";
            if (typeof d === "string") {
              try {
                d = JSON.parse(d);
              } catch {
                d = {};
              }
            }
            return { den: d.den || "", od: d.od || "", do: d.do || "" };
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
          { credentials: "include" }
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
          const day = this.intToDay(r.availability_day ?? r.day ?? r.den ?? "");
          const hour = this.normalizeHour(
            r.availability_hour || r.hour || r.cas || ""
          );
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
    async nacitajDetailHodiny(id) {
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
        if (Array.isArray(stRaw))
          ids = stRaw.map((x) => String(x?.student_id ?? x));
        this.existujuciStudenti = ids;
        this.existujucaForma = form;
        this.selectedEditDay = this.intToDay(dayRaw);
        this.selectedEditTime = this.normalizeHour(hourRaw);
      } catch {
        // ignore
      }
    },
    async overKolizie(den, casNum, excludeId = null, duoFlag = null) {
      await this.nacitajObsadene();
      const zoznam = this.obsadene[den] || [];
      const novyStart = this.toMin(this.normalizeHour(casNum));
      const isDuo = duoFlag != null ? duoFlag : this.existujucaForma === "duo";
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
    async callEditCalendarUpdate(payload) {
      const body = new URLSearchParams();
      body.append("id", String(payload.id));
      if (payload.formOfStudy) body.append("formOfStudy", payload.formOfStudy);
      if (payload.availability_day != null)
        body.append("availability_day", String(payload.availability_day));
      if (payload.availability_hour)
        body.append("availability_hour", payload.availability_hour);
      if (Array.isArray(payload.student))
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

      const statusStr = String((out && out.status) || txt).toLowerCase();
      const ok =
        res.ok &&
        (out === true ||
          out?.ok === true ||
          out?.success === true ||
          statusStr.includes("succes"));

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

      const statusStr = String((out && out.status) || txt).toLowerCase();
      const ok =
        res.ok &&
        (out === true ||
          out?.ok === true ||
          out?.success === true ||
          statusStr.includes("succes"));

      if (!ok)
        throw new Error(out?.message || out?.error || "Mazanie zlyhalo.");
      return true;
    },
    nastavDefaultCas() {
      const moznosti = this.casoveMoznostiEdit;
      if (!moznosti.length) {
        this.selectedEditTime = "";
        return;
      }
      if (this.selectedEditTime && moznosti.includes(this.selectedEditTime))
        return;
      if (this.selectedEditTime) {
        const pref = this.toMin(this.selectedEditTime);
        let best = moznosti[0],
          diff = Math.abs(this.toMin(best) - pref);
        for (const t of moznosti) {
          const d = Math.abs(this.toMin(t) - pref);
          if (d < diff) {
            best = t;
            diff = d;
          }
        }
        this.selectedEditTime = best;
        return;
      }
      this.selectedEditTime = moznosti[0];
    },
    async ulozitEdit() {
      this.error = "";
      this.message = "";
      if (!this.calendarId) {
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
        this.calendarId,
        this.existujucaForma === "duo"
      );
      if (kol) {
        this.error = kol;
        return;
      }
      this.loading = true;
      try {
        await this.callEditCalendarUpdate({
          id: this.calendarId,
          formOfStudy: this.existujucaForma,
          availability_day: this.dayToInt(this.selectedEditDay),
          availability_hour: this.hourToIntHHMM(this.selectedEditTime),
          student: this.existujuciStudenti,
        });
        await this.nacitajObsadene();
        this.message = "Hodina bola upravená.";
        this.$emit("updated", {
          id: this.calendarId,
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
      if (!this.calendarId) {
        this.error = "Chýba ID hodiny.";
        return;
      }
      this.loading = true;
      try {
        await this.callEditCalendarDelete(this.calendarId);
        this.message = "Hodina bola vymazaná.";
        this.$emit("deleted", { id: this.calendarId });
      } catch (e) {
        this.error = e?.message || "Mazanie zlyhalo.";
      } finally {
        this.loading = false;
      }
    },
  },
  async mounted() {
    await Promise.all([this.nacitajDostupnost(), this.nacitajObsadene()]);
    if (this.formOfStudy)
      this.existujucaForma =
        this.formOfStudy.toLowerCase() === "duo" ? "duo" : "solo";
    if (this.dayName) this.selectedEditDay = this.dayName;
    if (this.hour) this.selectedEditTime = this.normalizeHour(this.hour);
    const ids = [];
    if (this.studentId != null && String(this.studentId).trim() !== "")
      ids.push(String(this.studentId));
    if (
      this.existujucaForma === "duo" &&
      this.duoStudentId != null &&
      String(this.duoStudentId).trim() !== ""
    )
      ids.push(String(this.duoStudentId));
    if (ids.length) this.existujuciStudenti = ids;
    if (!this.existujuciStudenti.length)
      await this.nacitajDetailHodiny(this.calendarId);
    if (!this.selectedEditDay || !this.selectedEditTime)
      await this.nacitajDetailHodiny(this.calendarId);
    this.nastavDefaultCas();
  },
};
</script>

<style scoped lang="scss">
.formular {
  padding: 2.2em 2.6em;
  margin: auto;
}

h5 {
  margin: 0 0 0.15em;
  font-size: clamp(2em, 3.2vw, 3.2em);
  text-align: center;
  line-height: 1.1;
}

.text {
  text-align: center;
  font-size: clamp(1em, 1.1vw + 0.8em, 1.25em);
  margin: 0.35em auto 1.8em;
}
.text.small {
  font-size: 1em;
}

.pridanie {
  margin: 1.8em 0 2.2em;
  display: flex;
  justify-content: center;
}
.boxik {
  width: 100%;
}
.nadpis {
  font-size: 1.15em;
  margin: 0 0 0.8em;
  opacity: 0.8;
  text-align: left;
}

.ziak {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1em 1.2em;
  align-items: center;
}

select {
  appearance: none;
  -moz-appearance: none;
  -webkit-appearance: none;
  background-image: url("@/assets/images/icons/rozbalenie.png");
  background-repeat: no-repeat;
  background-position: right 0.9em center;
  background-size: 0.8em;
  width: 100%;
  min-height: 2.6em;
  padding: 0.55em 2.2em 0.55em 1em;
  border: 0;
  border-radius: 0.9em;
  font-size: 1em;
  background-color: #90ca50;
  box-shadow: 0 6px 16px #00000024, inset 0 2px 4px #0000001f;
  transition: transform 0.15s ease, box-shadow 0.2s ease, filter 0.2s ease;
}
select:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 20px #00000024, inset 0 2px 4px #0000001f;
}
select:active {
  transform: translateY(0);
  filter: brightness(0.96);
}
select:focus {
  outline: none;
  box-shadow: 0 0 0 3px #fef35a66, 0 10px 20px #00000024,
    inset 0 2px 4px #0000001f;
}

.button {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 0.5em;
  padding: 0.8em 1.4em;
  border-radius: 1em;
  letter-spacing: 0.04em;
  font-weight: 800;
  font-size: 1em;
  margin: 0.2em auto;
  box-shadow: 0 8px 18px #00000020, inset 0 2px 4px #ffffff66;
  transition: transform 0.15s ease, box-shadow 0.2s ease, filter 0.2s ease;
}
.button:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 24px #00000026, inset 0 2px 4px #ffffff66;
}
.button:active {
  transform: translateY(0);
  filter: brightness(0.97);
}

.button.danger {
  background: #f86e5c;
}

[aria-busy="true"] {
  opacity: 0.7;
  pointer-events: none;
}

@media (max-width: 720px) {
  .formular {
    padding: 1.6em 1.4em;
  }
  .ziak {
    grid-template-columns: 1fr;
  }
  h5 {
    font-size: 2em;
  }
}
</style>
