<template>
  <div class="formular">
    <h5>Naplánuj si svoje výučbové dni</h5>

    <p class="text">
      Označ, v ktoré dni budeš učiť, kde budeš učiť a v akom čase.
    </p>

    <div class="rozvrh">
      <div class="den" v-for="(day, index) in days" :key="index">
        <input type="checkbox" v-model="day.enabled" />
        <p class="day">{{ day.name }}</p>

        <select :disabled="!day.enabled" v-model="day.location">
          <option v-for="loc in locations" :key="loc" :value="loc">
            {{ loc }}
          </option>
        </select>

        <select :disabled="!day.enabled" v-model="day.start">
          <option value="" disabled>Od:</option>
          <option v-for="time in allTimes" :key="time" :value="time">
            {{ time }}
          </option>
        </select>

        <select :disabled="!day.start" v-model="day.end">
          <option value="" disabled>Do:</option>
          <option v-for="time in endOptions(day)" :key="time" :value="time">
            {{ time }}
          </option>
        </select>
      </div>
    </div>

    <p class="text smaller">
      Tvoj rozvrh ovplyvňuje, či ťa môžu noví žiaci kontaktovať.
    </p>
    <p class="text small">
      Na základe tebou nastavených výučbových dní, miest a časov bude systém
      vedieť, kedy si dostupný.
    </p>

    <button
      class="button"
      :class="{ disabled: !canSave }"
      :disabled="!canSave"
      @click="saveDays"
    >
      {{ loading ? "Ukladám..." : "Uložiť" }}
    </button>
  </div>
</template>

<script>
export default {
  name: "CasyUcenia",
  props: { closeOnSave: { type: Boolean, default: true } },
  data() {
    return {
      loading: false,
      message: "",
      error: "",
      lastSavedSig: null,
      days: [
        {
          name: "Pondelok",
          enabled: false,
          location: "Kamenná Poruba",
          start: "",
          end: "",
          primaryId: null,
          primarySnapshot: null,
          extraIds: [],
          extraSnapshots: {},
        },
        {
          name: "Utorok",
          enabled: false,
          location: "Kamenná Poruba",
          start: "",
          end: "",
          primaryId: null,
          primarySnapshot: null,
          extraIds: [],
          extraSnapshots: {},
        },
        {
          name: "Streda",
          enabled: false,
          location: "Kamenná Poruba",
          start: "",
          end: "",
          primaryId: null,
          primarySnapshot: null,
          extraIds: [],
          extraSnapshots: {},
        },
        {
          name: "Štvrtok",
          enabled: false,
          location: "Kamenná Poruba",
          start: "",
          end: "",
          primaryId: null,
          primarySnapshot: null,
          extraIds: [],
          extraSnapshots: {},
        },
        {
          name: "Piatok",
          enabled: false,
          location: "Kamenná Poruba",
          start: "",
          end: "",
          primaryId: null,
          primarySnapshot: null,
          extraIds: [],
          extraSnapshots: {},
        },
      ],
      locations: [
        "Hlboké nad Váhom",
        "Kamenná Poruba",
        "Lietavská Svinná",
        "Zákamenné",
        "Krasňany",
        "Skalité",
        "Rajec",
        "Varín",
        "Nededza",
        "Belá",
      ],
    };
  },
  computed: {
    allTimes() {
      const t = [];
      for (let h = 0; h < 24; h++)
        for (let m = 0; m < 60; m += 15)
          t.push(`${String(h).padStart(2, "0")}:${String(m).padStart(2, "0")}`);
      return t;
    },
    apiBase() {
      return (this.$store?.state?.api || "").replace(/\/+$/, "") + "/edupage/";
    },
    teacherId() {
      const s = Number(this.$store?.state?.user?.id);
      if (Number.isFinite(s) && s > 0) return Math.trunc(s);
      const q = Number(new URLSearchParams(location.search).get("teacher_id"));
      if (Number.isFinite(q) && q > 0) return Math.trunc(q);
      return null;
    },
    sigData() {
      return this.days.map((d) => ({
        name: d.name,
        enabled: !!d.enabled,
        location: d.location || "",
        start: d.start || "",
        end: d.end || "",
      }));
    },
    currentSig() {
      return JSON.stringify(this.sigData);
    },
    hasChanges() {
      return this.currentSig !== this.lastSavedSig;
    },
    canSave() {
      return !!this.teacherId && !this.loading && this.hasChanges;
    },
  },
  methods: {
    endOptions(day) {
      if (!day.start) return [];
      const i = this.allTimes.indexOf(day.start);
      return i === -1 ? [] : this.allTimes.slice(i + 1);
    },
    makeDataObj(day) {
      return {
        den: day.name,
        miesto: day.location || "",
        od: day.start || "",
        do: day.end || "",
      };
    },
    sameData(a, b) {
      return JSON.stringify(a) === JSON.stringify(b);
    },
    looseField(str, names) {
      for (const n of names) {
        let m = str.match(new RegExp(`"(?:${n})"\\s*:\\s*"([^"]*)"`, "i"));
        if (m && m[1] != null) return m[1];
        m = str.match(new RegExp(`'(?:${n})'\\s*:\\s*'([^']*)'`, "i"));
        if (m && m[1] != null) return m[1];
        m = str.match(new RegExp(`\\b(?:${n})\\b\\s*:\\s*"([^"]*)"`, "i"));
        if (m && m[1] != null) return m[1];
        m = str.match(new RegExp(`\\b(?:${n})\\b\\s*:\\s*'([^']*)'`, "i"));
        if (m && m[1] != null) return m[1];
      }
      return "";
    },
    rowToSnapshot(row) {
      let payload = row.data ?? row.DATA ?? "{}";
      for (let i = 0; i < 2 && typeof payload === "string"; i++) {
        try {
          payload = JSON.parse(payload);
        } catch {
          break;
        }
      }
      if (payload && typeof payload === "object") {
        return {
          den: (payload.den || payload.day || "").toString().trim(),
          miesto: (payload.miesto || payload.location || "").toString().trim(),
          od: (payload.od || payload.start || "").toString().trim(),
          do: (payload.do || payload.end || "").toString().trim(),
        };
      }
      const s = String(row.data || row.DATA || "");
      return {
        den: this.looseField(s, ["den", "day"]).trim(),
        miesto: this.looseField(s, ["miesto", "location"]).trim(),
        od: this.looseField(s, ["od", "start"]).trim(),
        do: this.looseField(s, ["do", "end"]).trim(),
      };
    },
    async fetchCalendarRows() {
      const qs = new URLSearchParams();
      if (Number.isFinite(this.teacherId) && this.teacherId > 0) {
        qs.set("teacher_id", String(this.teacherId));
      }
      const url =
        this.apiBase +
        "loadCalendarCustom.php" +
        (qs.toString() ? "?" + qs.toString() : "");
      const res = await fetch(url, { credentials: "include" });
      const txt = await res.text();
      let out;
      try {
        out = JSON.parse(txt);
      } catch {
        out = null;
      }
      return Array.isArray(out?.data)
        ? out.data
        : Array.isArray(out)
        ? out
        : [];
    },
    applyAvailabilityRows(rows) {
      for (const row of rows) {
        const id = Number(row.id || row.ID || row.Id || 0) || null;
        const snapshot = this.rowToSnapshot(row);
        const den = snapshot.den || "";
        const d = this.days.find(
          (x) => x.name.toLowerCase() === den.toLowerCase()
        );
        if (!d || !id) continue;
        if (d.primaryId == null) {
          d.primaryId = id;
          d.primarySnapshot = snapshot;
          d.enabled = true;
          d.location = snapshot.miesto;
          d.start = snapshot.od;
          d.end = snapshot.do;
        } else {
          if (!d.extraIds.includes(id)) d.extraIds.push(id);
          d.extraSnapshots[id] = snapshot;
        }
      }
    },
    setSavedSignature() {
      this.lastSavedSig = this.currentSig;
    },
    extractId(res) {
      return (
        res?.id ||
        res?.data?.id ||
        res?.inserted_id ||
        res?.new_id ||
        res?.DATA?.id ||
        null
      );
    },
    dayIdIndex(rows) {
      const idx = {};
      for (const row of rows) {
        const id = Number(row.id || row.ID || row.Id || 0) || null;
        if (!id) continue;
        const s = this.rowToSnapshot(row);
        const den = (s.den || "").toLowerCase();
        if (!den) continue;
        if (!idx[den] || id > idx[den]) idx[den] = id;
      }
      return idx;
    },
    findExactId(rows, data) {
      const target = {
        den: (data.den || "").trim(),
        miesto: (data.miesto || "").trim(),
        od: (data.od || "").trim(),
        do: (data.do || "").trim(),
      };
      for (const row of rows) {
        const id = Number(row.id || row.ID || row.Id || 0) || null;
        if (!id) continue;
        const s = this.rowToSnapshot(row);
        if (
          (s.den || "").trim() === target.den &&
          (s.miesto || "").trim() === target.miesto &&
          (s.od || "").trim() === target.od &&
          (s.do || "").trim() === target.do
        )
          return id;
      }
      return null;
    },

    async createCalendarEntry(data) {
      const body = new URLSearchParams();
      const tid = Number.isFinite(this.teacherId)
        ? Math.trunc(this.teacherId)
        : 0;
      body.append("teacher_id", String(tid));
      body.append("data", JSON.stringify(data));

      const res = await fetch(`${this.apiBase}editCalendarCustom.php`, {
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
      if (!res.ok || out?.error || out?.status === "Request failed") {
        throw new Error(out?.message || out?.error || `HTTP ${res.status}`);
      }
      let newId =
        out?.id || out?.data?.id || out?.inserted_id || out?.new_id || null;
      if (!newId) {
        const rows = await this.fetchCalendarRows();
        newId =
          this.findExactId(rows, data) ||
          this.dayIdIndex(rows)[(data.den || "").toLowerCase()] ||
          null;
      }
      return { id: newId };
    },

    async updateCalendarEntry(id, data) {
      const tid = Number.isFinite(this.teacherId)
        ? Math.trunc(this.teacherId)
        : 0;
      const url = `${
        this.apiBase
      }editCalendarCustom.php?id=${encodeURIComponent(String(id))}`;

      const body = new URLSearchParams();
      body.append("id", String(id)); // aj v POST
      body.append("teacher_id", String(tid));
      body.append("data", JSON.stringify(data));

      const res = await fetch(url, {
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
      if (!res.ok || out?.error || out?.status === "Request failed") {
        throw new Error(out?.message || out?.error || `HTTP ${res.status}`);
      }
      return out;
    },

    async deleteCalendarEntry(id) {
      const tid = Number.isFinite(this.teacherId)
        ? Math.trunc(this.teacherId)
        : 0;
      const url = `${
        this.apiBase
      }editCalendarCustom.php?id=${encodeURIComponent(String(id))}`;

      const body = new URLSearchParams();
      body.append("id", String(id));
      body.append("teacher_id", String(tid));
      body.append("delete", "true");

      const res = await fetch(url, {
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
      if (!res.ok || out?.error || out?.status === "Request failed") {
        throw new Error(out?.message || out?.error || `HTTP ${res.status}`);
      }
      return out;
    },

    async saveDays() {
      this.error = "";
      if (!this.teacherId) {
        this.error = "Nemáš oprávnenie ukladať rozvrh.";
        return;
      }
      const invalid = this.days.find((d) => d.enabled && !d.start);
      if (invalid) {
        this.error = `Nastav čas Od pre deň ${invalid.name}.`;
        return;
      }
      this.loading = true;
      try {
        const serverRows = await this.fetchCalendarRows();
        const byDay = this.dayIdIndex(serverRows);
        const ops = [];
        for (const d of this.days) {
          const dataObj = this.makeDataObj(d);
          if (!d.enabled) {
            if (d.primaryId != null)
              ops.push(
                this.deleteCalendarEntry(d.primaryId).then(() => ({
                  type: "delPrimary",
                  id: d.primaryId,
                  day: d,
                }))
              );
            for (const id of d.extraIds)
              ops.push(
                this.deleteCalendarEntry(id).then(() => ({
                  type: "delExtra",
                  id,
                  day: d,
                }))
              );
            continue;
          }
          if (d.primaryId == null && byDay[d.name.toLowerCase()])
            d.primaryId = byDay[d.name.toLowerCase()];
          if (d.primaryId == null) {
            ops.push(
              this.createCalendarEntry(dataObj).then((res) => ({
                type: "createPrimary",
                res,
                day: d,
              }))
            );
          } else {
            const prev = d.primarySnapshot;
            if (!prev || !this.sameData(prev, dataObj)) {
              ops.push(
                this.updateCalendarEntry(d.primaryId, dataObj).then(() => ({
                  type: "updatePrimary",
                  id: d.primaryId,
                  day: d,
                }))
              );
            }
          }
        }
        const settled = await Promise.allSettled(ops);
        for (const r of settled) {
          if (r.status !== "fulfilled") continue;
          const { type, res, id, day } = r.value;
          if (type === "createPrimary") {
            let newId =
              res?.id || res?.data?.id || res?.inserted_id || res?.new_id;
            if (!newId) {
              const rows2 = await this.fetchCalendarRows();
              newId =
                this.findExactId(rows2, this.makeDataObj(day)) ||
                this.dayIdIndex(rows2)[day.name.toLowerCase()] ||
                null;
            }
            if (newId) {
              day.primaryId = newId;
              day.primarySnapshot = { ...this.makeDataObj(day) };
            }
          } else if (type === "updatePrimary") {
            day.primarySnapshot = { ...this.makeDataObj(day) };
          } else if (type === "delPrimary") {
            day.primaryId = null;
            day.primarySnapshot = null;
          } else if (type === "delExtra") {
            day.extraIds = day.extraIds.filter((x) => x !== id);
            delete day.extraSnapshots[id];
          }
        }
        this.setSavedSignature();
        if (this.closeOnSave) this.$emit("close");
      } catch (e) {
        this.error = e?.message || "Ukladanie zlyhalo.";
      } finally {
        this.loading = false;
      }
    },
  },
  watch: {
    teacherId: {
      immediate: true,
      async handler(val) {
        if (Number.isFinite(val) && val > 0) {
          this.days.forEach((d) => {
            d.primaryId = null;
            d.primarySnapshot = null;
            d.enabled = false;
            d.start = "";
            d.end = "";
          });
          const rows = await this.fetchCalendarRows();
          this.applyAvailabilityRows(rows);
          this.setSavedSignature();
        }
      },
    },
  },
  async mounted() {
    await this.loadDays();
    this.setSavedSignature();
  },
};
</script>

<style lang="scss" scoped>
h5 {
  font-size: 3.3em;
  margin: 0.5em auto;
}

// Custom circular checkboxes for .den only
.den input[type="checkbox"] {
  font-size: 1em;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
  width: 1.5em;
  height: 1.5em;
  border-radius: 50%;
  border: 0.18em solid #41a143;
  box-shadow: 0 0 0 0.2em #ffffff inset, 0 0.08em 0.25em #41a14333;
  background: #fff;
  transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
  position: relative;
  cursor: pointer;
  outline: none;
  margin-right: 1em;
  vertical-align: middle;
  display: inline-block;
}

.den input[type="checkbox"]:hover,
.den input[type="checkbox"]:focus {
  border-color: #318e36;
  box-shadow: 0 0 0 0.2em #e8f5e9 inset, 0 0.12em 0.4em #41a14355;
}

.den input[type="checkbox"]:checked {
  background: #41a143;
  border-color: #41a143;
  box-shadow: 0 0 0 0.2em #fffbe6 inset, 0 0.12em 0.4em #41a14355;
}

.den input[type="checkbox"]:checked::before {
  content: "";
  display: block;
  position: absolute;
  top: 0.32em;
  left: 0.32em;
  width: 0.86em;
  height: 0.86em;
  border-radius: 50%;
  z-index: -1;
  background: radial-gradient(circle, #41a143 70%, #ffe600 100%);
  box-shadow: 0 0 0 0.06em #41a14355;
}

.formular {
  max-width: 60em;

  margin: auto;
}

.text {
  font-size: 1.9em;
  margin: 1em auto;

  &.smaller {
    font-size: 1.6em;
  }

  &.small {
    font-size: 1em;
  }
}

input {
  width: 1em;
}

.den {
  display: flex;
  justify-content: space-between;
  align-items: center;
  max-width: 50em;
  margin: auto;
  border-bottom: 0.15em solid rgba(0, 0, 0, 0.1803921569);
  padding: 0.5em 2em;
}

.day {
  font-size: 1.8em;
  width: 6em;
  text-align: left;
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

select:focus {
  outline: none;
  box-shadow: none;
}

.button {
  width: fit-content;
  margin: auto;
}
.button.disabled,
.button:disabled {
  opacity: 0.5;
  pointer-events: none;
}
</style>
