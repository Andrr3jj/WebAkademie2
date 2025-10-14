<template>
  <div class="formular">
    <h5>Pridať číselný zápis</h5>

    <div class="text">Vyber zápis:</div>
    <div class="chips">
      <span
        v-for="song in selected"
        :key="song?.id || song?.name || song"
        :class="['chip', { 'chip--locked': !!song?.locked }]"
        @dblclick.prevent="onChipDblclick(song)"
        :title="
          song?.locked
            ? 'Priradené z poznámky – nemožno odstrániť'
            : 'Dvojklik odstráni'
        "
      >
        {{ song?.name || song }}
      </span>
    </div>
    <div class="pesnicky-wrapper">
      <div class="pesnicka" v-for="(select, i) in selects" :key="i">
        <select v-model="selects[i]" @change="onSelectChange(i)">
          <option disabled value="">Vyber zápis…</option>
          <option v-for="opt in optionsFor(i)" :key="opt.id" :value="opt">
            {{ opt.name }} - {{ opt.id }} ({{ opt.price }}€)
          </option>
        </select>
      </div>
    </div>

    <div class="button Adumu" @click="pridatVybrane">Pridať</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      allSongs: [],
      selected: [],
      selects: [null],
    };
  },
  props: {
    summary: {
      type: Object,
      default: () => ({ id: null, students: [], lessonId: null }),
    },
    activeStudentId: {
      type: [String, Number],
      default: null,
    },
    activeStudentIndex: {
      type: Number,
      default: 0,
    },
    selectedDate: {
      type: String, // očakávame formát ako v DB, napr. "02.09.2025"
      default: "",
    },
  },
  computed: {
    effectiveDate() {
      // priorita: prop -> summary.date -> lessonMeta.date
      return (
        this.selectedDate || this.summary.date || this.lessonMeta.date || ""
      );
    },
    activeUserIds() {
      // Zober všetkých študentov zo summary, môžu byť 1 alebo 2 (alebo viac v budúcnosti)
      const students = Array.isArray(this.summary?.students)
        ? this.summary.students
        : [];

      // poskladaj unikátne IDčka zo summary
      const idsFromSummary = students
        .map((s) => (s && s.id != null ? String(s.id) : null))
        .filter((v) => v);

      // ak príde explicitný activeStudentId, tiež ho zahrň (zachovanie kompatibility)
      const explicit =
        this.activeStudentId !== null &&
        this.activeStudentId !== undefined &&
        this.activeStudentId !== ""
          ? [String(this.activeStudentId)]
          : [];

      // unikátna množina
      const uniq = Array.from(new Set([...idsFromSummary, ...explicit]));
      return uniq;
    },
  },
  mounted() {
    if (this.activeUserIds.length) {
      this.nacitajNevlastneneZapisyPreUzivatelov(this.activeUserIds);
    }
    this.loadAssignedFromNote();
    console.log(
      "this.summary :>> ",
      this.summary,
      "activeUserIds:",
      this.activeUserIds
    );
  },
  watch: {
    summary: {
      handler() {
        if (this.activeUserIds.length) {
          this.nacitajNevlastneneZapisyPreUzivatelov(this.activeUserIds);
          this.loadAssignedFromNote();
        }
      },
      deep: true,
    },
    activeStudentId() {
      if (this.activeUserIds.length) {
        this.nacitajNevlastneneZapisyPreUzivatelov(this.activeUserIds);
        this.loadAssignedFromNote();
      }
    },
    activeStudentIndex() {
      if (this.activeUserIds.length) {
        this.nacitajNevlastneneZapisyPreUzivatelov(this.activeUserIds);
        this.loadAssignedFromNote();
      }
    },
  },
  methods: {
    onChipDblclick(song) {
      if (song && song.locked) return; // locked z poznámky – ignoruj
      this.removeSong(song);
    },

    async loadAssignedFromNote() {
      try {
        console.log("[NOTE→CHIPS] start loadAssignedFromNote");
        console.log("[NOTE→CHIPS] summary.id (calendarId):", this.summary?.id);
        console.log("[NOTE→CHIPS] summary.students:", this.summary?.students);
        const calendarId = this.summary?.id;
        if (!calendarId) return;
        const baseUrl = `${
          this.$store.state.api
        }/edupage/loadLesson.php?calendar_id=${encodeURIComponent(calendarId)}`;
        const url = this.effectiveDate
          ? `${baseUrl}&date=${encodeURIComponent(this.effectiveDate)}`
          : baseUrl;
        console.log(
          "[NOTE→CHIPS] GET",
          url,
          "with date =",
          this.effectiveDate || "(none)"
        );
        const res = await fetch(url, { credentials: "include" });
        const json = await res.json().catch(() => ({}));
        console.log("[NOTE→CHIPS] fetch ok?", res.ok, "status:", res.status);
        console.log(
          "[NOTE→CHIPS] raw response json keys:",
          Object.keys(json || {})
        );
        const lessons = Array.isArray(json?.data) ? json.data : [];
        console.log("[NOTE→CHIPS] lessons count:", lessons.length);
        if (!lessons.length)
          console.warn(
            "[NOTE→CHIPS] No lessons returned for calendar",
            this.summary?.id
          );
        if (!lessons.length) return;

        // vyber najnovšiu lekciu (podľa id)
        const main = lessons.reduce(
          (acc, cur) => (!acc || +cur.id > +acc.id ? cur : acc),
          null
        );
        console.log(
          "[NOTE→CHIPS] chosen main lesson by max id:",
          main
            ? {
                id: main.id,
                date: main.date,
                hour: main.hour,
                status: main.status,
              }
            : null
        );
        if (!main) {
          console.warn("[NOTE→CHIPS] No main lesson could be chosen");
          return;
        }

        let students = [];
        try {
          students = JSON.parse(main.student || "[]");
        } catch {
          students = [];
        }
        console.log("[NOTE→CHIPS] parsed students length:", students.length);
        if (students.length)
          console.log("[NOTE→CHIPS] sample student[0]:", students[0]);

        // zober iba tých študentov, ktorí sú v tomto komponente aktívni (summary.students)
        const allowedIds = new Set(
          (Array.isArray(this.summary?.students)
            ? this.summary.students
            : []
          ).map((s) => String(s.id))
        );
        console.log(
          "[NOTE→CHIPS] allowedIds from summary:",
          Array.from(allowedIds)
        );
        // nahrad v loadAssignedFromNote idRegex
        const idRegex =
          /(Pridané\s+noty\s+s\s+id|Add\s+notes\s+id)\s*[:=]\s*(\d+)/gi;
        const idsToFetch = new Set();

        for (const s of students) {
          const sid = s?.student_id ?? s?.id;
          if (!sid || !allowedIds.has(String(sid))) continue;
          const note = String(s?.note || "");
          let m;
          while ((m = idRegex.exec(note)) !== null) {
            const num = parseInt(m[2], 10);
            if (!Number.isNaN(num)) idsToFetch.add(num);
          }
        }
        console.log(
          "[NOTE→CHIPS] extracted product IDs from notes:",
          Array.from(idsToFetch)
        );
        if (!idsToFetch.size)
          console.warn(
            "[NOTE→CHIPS] No product IDs found in notes for current students"
          );
        if (!idsToFetch.size) return;

        // načítaj názvy/ceny cez existujúcu metódu nacitajNazovProduktu
        const metas = await Promise.all(
          Array.from(idsToFetch).map(async (id) => {
            const d = await this.nacitajNazovProduktu(id);
            return {
              id,
              name: d.name || `Zápis #${id}`,
              price:
                typeof d.price === "number"
                  ? d.price
                  : d.price
                  ? Number(d.price)
                  : null,
            };
          })
        );
        console.log("[NOTE→CHIPS] loaded product metas:", metas);

        // pridaj do selected ako locked, ale neduplikuj podľa id
        const already = new Set(this.selected.map((s) => s && s.id));
        for (const m of metas) {
          if (!already.has(m.id)) {
            console.log("[NOTE→CHIPS] push locked chip", m);
            this.selected.push({
              id: m.id,
              name: m.name,
              price: m.price ?? null,
              locked: true,
            });
          }
        }
        // uprac selects, aby sa už vybrané neponúkali
        console.log("[NOTE→CHIPS] selected after merge:", this.selected);
        if (typeof this.normalizeSelects === "function")
          this.normalizeSelects();
      } catch (e) {
        // swallow – nech to nezastaví formulár
        console.warn("loadAssignedFromNote error", e);
      }
    },

    async nacitajNevlastneneZapisyPreUzivatelov(userIds) {
      // userIds: pole stringov/čísel — zmiešame (union) zápisy, ktoré aspoň jeden z nich NEVLASTNÍ
      this.pouzivatelNevlastenene = [];

      try {
        // 1) stiahni IDčka pre každého používateľa paralelne
        const lists = await Promise.all(
          userIds.map((uid) => this.getNotOwnedIdsFor(uid))
        );

        // 2) urob zjednotenú množinu IDčiek (union)
        const unionIds = Array.from(new Set(lists.flat()));

        // 3) vytvor základné objekty
        this.pouzivatelNevlastenene = unionIds.map((id) => ({
          id,
          name: "",
          price: null,
        }));

        // 4) paralelne doplň názvy a ceny
        await Promise.all(
          this.pouzivatelNevlastenene.map(async (item) => {
            const details = await this.nacitajNazovProduktu(item.id);
            item.name = details.name || "";
            item.price =
              typeof details.price === "number"
                ? details.price
                : details.price
                ? Number(details.price)
                : null;
          })
        );

        // 5) zotried podľa mena (case-insensitive)
        this.pouzivatelNevlastenene.sort((a, b) =>
          a.name.toLowerCase().localeCompare(b.name.toLowerCase())
        );

        // 6) naplň allSongs
        this.allSongs = this.pouzivatelNevlastenene;
        if (typeof this.normalizeSelects === "function") {
          this.normalizeSelects();
        }
      } catch (e) {
        console.log(e);
      }
    },

    async getNotOwnedIdsFor(userId) {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      const config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/user/stats/getZapisNotOwned.php?user_id=" +
          encodeURIComponent(userId),
        data,
      };

      try {
        const response = await axios.request(config);
        const ids = Array.isArray(response?.data?.data)
          ? response.data.data
          : [];
        return ids.map((id) => parseInt(id, 10)).filter((n) => !isNaN(n));
      } catch (error) {
        console.log(error);
        return [];
      }
    },

    async nacitajNazovProduktu(id) {
      const axios = require("axios");

      const config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
        headers: {},
      };

      try {
        const response = await axios.request(config);
        const data = response?.data?.data || {};
        const name = data.name || "";
        // Podpora pre rôzne názvy polí z API: price alebo cena
        const rawPrice = data.price !== undefined ? data.price : data.cena;
        const price =
          rawPrice !== undefined && rawPrice !== null && rawPrice !== ""
            ? Number(rawPrice)
            : null;
        return { name, price };
      } catch (error) {
        console.log(error);
        return { name: "", price: null };
      }
    },
    optionsFor(index) {
      const current = this.selects[index];
      const selectedIds = new Set(this.selected.map((s) => s && s.id));

      const remaining = this.allSongs
        .filter((s) => !selectedIds.has(s.id))
        .sort((a, b) =>
          a.name.toLowerCase().localeCompare(b.name.toLowerCase())
        );

      // Ak aktuálna hodnota nie je v remaining (napr. už vybraná), zobraz ju navrchu, aby select nezhodil hodnotu
      if (current && !remaining.some((s) => s.id === current.id)) {
        return [current, ...remaining];
      }
      return remaining;
    },
    onSelectChange(index) {
      const val = this.selects[index];
      if (!val || typeof val !== "object") return;
      const exists = this.selected.some((s) => s.id === val.id);
      if (!exists) {
        this.selected.push({ id: val.id, name: val.name, price: val.price });
      }
      this.normalizeSelects();
    },
    removeSong(song) {
      const id = song && song.id ? song.id : null;
      if (id === null) return;
      this.selected = this.selected.filter((s) => s.id !== id);
      this.selects = this.selects.map((v) => (v && v.id === id ? null : v));
      this.normalizeSelects();
    },
    normalizeSelects() {
      const nonEmpty = this.selects.filter((v) => v && typeof v === "object");
      const selectedIds = new Set(this.selected.map((s) => s.id));
      const remainingCount = this.allSongs.filter(
        (s) => !selectedIds.has(s.id)
      ).length;
      this.selects = [...nonEmpty, remainingCount > 0 ? null : null];
      if (remainingCount === 0) {
        this.selects = [null];
      }
    },
    async fetchSongs() {
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/zapisy/list.php"
        );
        if (!res.ok) throw new Error("Network response was not ok");
        const data = await res.json();
        if (Array.isArray(data)) {
          this.allSongs = data.map((item) => ({
            id: item.id,
            name: item.name,
            price:
              item.price !== undefined
                ? Number(item.price)
                : item.cena !== undefined
                ? Number(item.cena)
                : null,
          }));
        } else if (Array.isArray(data.songs)) {
          this.allSongs = data.songs.map((item) => ({
            id: item.id,
            name: item.name,
            price:
              item.price !== undefined
                ? Number(item.price)
                : item.cena !== undefined
                ? Number(item.cena)
                : null,
          }));
        } else {
          console.warn("Unexpected API format", data);
        }
        if (typeof this.normalizeSelects === "function") {
          this.normalizeSelects();
        }
      } catch (err) {
        console.error("Chyba pri načítaní zoznamu zápisov:", err);
      }
    },
    async submit() {
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/zapisy/add.php",
          {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ songs: this.selected }),
          }
        );
        const data = await res.json();
        console.log("Odozva servera:", data);
      } catch (err) {
        console.error("Chyba pri odosielaní zápisov:", err);
      }
    },
    async pridatVybrane() {
      try {
        // 1) Over vstupy
        const userIds = Array.isArray(this.activeUserIds)
          ? this.activeUserIds
          : [];
        if (!userIds.length) {
          console.warn("Žiadny aktívny používateľ – nič sa neposiela.");
          return;
        }
        const selectedObjs = Array.isArray(this.selected)
          ? this.selected.filter(
              (o) => o && typeof o === "object" && o.id != null && !o.locked
            )
          : [];
        console.log(
          "[ADD→OWNED] selected total:",
          Array.isArray(this.selected) ? this.selected.length : 0,
          "| locked skipped:",
          Array.isArray(this.selected)
            ? this.selected.filter((x) => x && x.locked).length
            : 0,
          "| to-send:",
          selectedObjs.length
        );
        if (!selectedObjs.length) {
          console.warn("Nie sú vybrané žiadne piesne – nič sa neposiela.");
          return;
        }

        // 2) Zober priamo ID produktov
        const selectedIds = selectedObjs.map((s) => s.id);

        // unikátne ID produktov a používateľov
        const productIds = Array.from(new Set(selectedIds));
        const uniqUsers = Array.from(new Set(userIds));

        const lessonId =
          this.summary && this.summary.lessonId !== undefined
            ? this.summary.lessonId
            : null;

        // 3) Priprav a odošli všetky requesty paralelne
        const base = this.$store.state.api + "/edupage/addToOwnedAtLesson.php";
        const mkUrl = (uid, pid) => {
          const params = new URLSearchParams({
            user_id: String(uid),
            product_id: String(pid),
          });
          if (
            lessonId !== null &&
            lessonId !== undefined &&
            String(lessonId) !== ""
          ) {
            params.append("lesson_id", String(lessonId));
          }
          return `${base}?${params.toString()}`;
        };

        const requests = [];
        for (const uid of uniqUsers) {
          for (const pid of productIds) {
            requests.push(
              fetch(mkUrl(uid, pid), { credentials: "include" })
                .then((r) => r.json())
                .then((j) => ({
                  uid,
                  pid,
                  ok: j?.status === "Request succesfull",
                  resp: j,
                }))
                .catch((e) => ({ uid, pid, ok: false, error: String(e) }))
            );
          }
        }

        const results = await Promise.allSettled(requests);
        const flattened = results.map((r) =>
          r.status === "fulfilled" ? r.value : r.reason
        );
        const okCount = flattened.filter((x) => x && x.ok).length;
        const failCount = flattened.length - okCount;

        console.log("Pridanie do Owned hotové", {
          total: flattened.length,
          okCount,
          failCount,
          detail: flattened,
        });

        // 4) UI odozva – jednoduchý this.$store.state.SnackBarTextconsole; prípadne neskôr toast
        if (failCount === 0) {
          this.$store.state.SnackBarText =
            "✅ Zápisy boli úspešne priradené všetkým zvoleným žiakom.";
        } else if (okCount > 0) {
          this.$store.state.SnackBarText = `⚠️ Čiastočný úspech: ${okCount} OK, ${failCount} zlyhalo. Skontroluj konzolu.`;
        } else {
          this.$store.state.SnackBarText =
            "❌ Nepodarilo sa pridať žiadny zápis. Skontroluj prihlásenie/roly alebo API.";
        }
      } catch (err) {
        console.error("Chyba pri hromadnom prideľovaní zápisov:", err);
        this.$store.state.SnackBarText =
          "❌ Nastala neočakávaná chyba pri odosielaní požiadaviek.";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
h5 {
  font-size: 3.3em;
  margin: 0.5em auto;
}

.text {
  font-size: 1.5em;
  margin: 0.6em auto 0;
}

.pesnicka {
  margin: 0;
  select {
    margin: auto;
  }
}

.pesnicky-wrapper {
  display: flex;
  flex-direction: column;
  gap: 0.8em;
  margin: 0.5em auto 2em;
}

.chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5em;
  margin: 0.5em 0 1em;
  justify-content: center;
  cursor: pointer;
}
.chip {
  background: #d9d9d9;
  border-radius: 999px;
  padding: 0.2em 0.8em;
  box-shadow: 0.1em 0.1em 0.6em #00000033;
  user-select: none;
}

.formular {
  max-width: 60em;

  margin: auto;
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

.button {
  width: fit-content;
  margin: auto;
}

.chip--locked {
  opacity: 0.8;
  pointer-events: none; // nedovoľ odstránenie
  background: #aad985;
}
</style>
