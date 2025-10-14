<template>
  <section class="computer" v-if="!isMobile" :id="$route.name">
    <div class="scroll">
      <HeadlineSection
        title="Online prihlášky"
        :teachers="teachers"
        :stats="stats"
      />
      <ObjednavkyPrihlaskyMesiac
        :orders="orders"
        :users="users"
        :userMap="userMap"
        @updateOrder="updateOrder"
        @deleted="onDeletedOrder"
      />
      <button
        v-if="hasMore && !loading"
        class="button"
        @click="
          async () => {
            await loadNextMonthsChunk();
            applyData();
          }
        "
      >
        Načítať ďalšie mesiace
      </button>
      <div v-else-if="loading">Načítavam…</div>
    </div>
  </section>
  <div v-else class="mobile" :id="$route.name">
    <section>
      <div class="scroll">
        <HeadlineSection
          title="Online prihlášky"
          :teachers="teachers"
          :stats="stats"
        />
        <ObjednavkyPrihlaskyMesiac
          :orders="orders"
          :users="users"
          :userMap="userMap"
          @updateOrder="updateOrder"
          @deleted="onDeletedOrder"
        />
        <button
          v-if="hasMore && !loading"
          class="button"
          @click="
            async () => {
              await loadNextMonthsChunk();
              applyData();
            }
          "
        >
          Načítať ďalšie mesiace
        </button>
        <div v-else-if="loading">Načítavam…</div>
      </div>
    </section>
  </div>
</template>

<script>
import axios from "axios";
import HeadlineSection from "@/components/Edupage/Prihlasky/HeadlineSection.vue";
import ObjednavkyPrihlaskyMesiac from "@/components/Edupage/Prihlasky/ObjednanePrihlaskyMesiac.vue";

function ymKey(y, m) {
  return `${y}-${String(m).padStart(2, "0")}`;
}
function stepBack(y, m, n = 1) {
  let total = y * 12 + (m - 1) - n;
  const ny = Math.floor(total / 12);
  const nm = (total % 12) + 1;
  return [ny, nm];
}

export default {
  name: "AdminOnlinePrihlaskyView",
  components: { HeadlineSection, ObjednavkyPrihlaskyMesiac },
  data() {
    const now = new Date();
    return {
      orders: [],
      teachers: [
        { id: 59, name: "Juraj Kvočka", count: 0, duo: 0, solo: 0 },
        { id: 54, name: "Andrej Trnka", count: 0, duo: 0, solo: 0 },
        { id: 607, name: "Matej Kondela", count: 0, duo: 0, solo: 0 },
      ],
      stats: { total: 0, active: 0, resigned: 0 },
      users: [],
      userMap: {},
      isMobile:
        typeof window !== "undefined" ? window.innerWidth <= 768 : false,

      selectedYear:
        Number(new URLSearchParams(location.search).get("year")) ||
        now.getFullYear(),
      selectedMonth:
        Number(new URLSearchParams(location.search).get("month")) ||
        now.getMonth() + 1,

      ordersByPeriod: {},
      teachersByPeriod: {},
      statsByPeriod: {},
      loading: false,
      error: null,

      chunkSize: 5,
      maxMonthsBack: 60,
      nextCursorYear: now.getFullYear(),
      nextCursorMonth: now.getMonth() + 1,
      fetchedKeys: new Set(),
      hasMore: true,

      includeAllMonths: true,

      teacherCountsGlobal: null, // { [teacherId]: number } — načíta sa raz
    };
  },

  async mounted() {
    await this.loadUsers();
    await this.loadTeacherCountsOnce(); // 1× globálne
    await this.loadNextMonthsChunk(); // mesačné dáta
    this.applyData();
    window.addEventListener("popstate", this.onPopstate);
  },

  beforeUnmount() {
    window.removeEventListener("popstate", this.onPopstate);
  },

  methods: {
    key(year, month) {
      return ymKey(year, month);
    },

    pushQuery(year, month) {
      const url = new URL(location.href);
      url.searchParams.set("year", year);
      url.searchParams.set("month", month);
      history.replaceState({}, "", url.toString());
    },

    onPopstate() {
      const y = Number(new URLSearchParams(location.search).get("year"));
      const m = Number(new URLSearchParams(location.search).get("month"));
      if (y && m) {
        this.selectedYear = y;
        this.selectedMonth = m;
        this.applyData();
      }
    },

    async setPeriod(year, month) {
      this.selectedYear = Number(year);
      this.selectedMonth = Number(month);
      const k = this.key(year, month);
      if (!this.ordersByPeriod[k]) {
        await this.refreshPeriodData(year, month);
      }
      this.applyData();
      this.pushQuery(this.selectedYear, this.selectedMonth);
    },

    async loadNextMonthsChunk() {
      if (this.loading || !this.hasMore) return;
      this.loading = true;
      this.error = null;

      const tasks = [];
      let y = this.nextCursorYear;
      let m = this.nextCursorMonth;
      let added = 0;
      let scanned = 0;

      while (added < this.chunkSize && scanned < this.maxMonthsBack) {
        const k = ymKey(y, m);
        if (!this.fetchedKeys.has(k)) {
          tasks.push({ y, m, k });
          this.fetchedKeys.add(k);
          added++;
        }
        [y, m] = stepBack(y, m, 1);
        scanned++;
      }

      if (!tasks.length) {
        this.hasMore = false;
        this.loading = false;
        return;
      }

      try {
        const stats = await this.fetchStats();
        const batch = 5;
        for (let i = 0; i < tasks.length; i += batch) {
          const slice = tasks.slice(i, i + batch);
          await Promise.all(
            slice.map(async ({ y, m, k }) => {
              const orders = await this.fetchOrders(y, m);
              this.ordersByPeriod[k] = orders;
              this.statsByPeriod[k] = stats;
              // žiadne API — len spočítaj solo/duo a count zober globálne
              this.recalculateTeachersForPeriod(y, m);
            })
          );
        }
        const last = tasks[tasks.length - 1];
        [this.nextCursorYear, this.nextCursorMonth] = stepBack(
          last.y,
          last.m,
          1
        );
        const totalFetched = this.fetchedKeys.size;
        if (totalFetched >= this.maxMonthsBack) this.hasMore = false;
      } catch (e) {
        this.error = e;
      } finally {
        this.loading = false;
      }
    },

    async fetchOrders(year, month) {
      const r = await axios.get(
        `${this.$store.state.api}/edupage/loadAll.php?year=${year}&month=${month}`
      );
      return r?.data?.status === "Request succesfull" &&
        Array.isArray(r?.data?.data)
        ? r.data.data
        : [];
    },

    async fetchStats() {
      const r = await axios.get(
        `${this.$store.state.api}/edupage/countRegistrations.php`
      );
      if (r?.data?.status === "Request succesfull" && r.data.data) {
        const d = r.data.data;
        return {
          total: d.count_all ?? 0,
          active: d["count_zaradený"] ?? d["count_zaradeny"] ?? 0,
          resigned: d["count_odhlásený"] ?? d["count_odhlaseny"] ?? 0,
        };
      }
      return { total: 0, active: 0, resigned: 0 };
    },

    async loadUsers() {
      try {
        const res = await axios.get(
          `${this.$store.state.api}/user/stats/getUsers.php`
        );
        if (
          res.data?.status === "Request succesfull" &&
          Array.isArray(res.data.data?.users)
        ) {
          this.users = res.data.data.users;
          this.userMap = Object.fromEntries(this.users.map((u) => [u.id, u]));
        } else {
          this.users = [];
          this.userMap = {};
        }
      } catch {
        this.users = [];
        this.userMap = {};
      }
    },

    async loadTeacherCountsOnce() {
      if (this.teacherCountsGlobal) return;
      const baseIds = [59, 54, 607];
      const counts = {};
      await Promise.allSettled(
        baseIds.map(async (id) => {
          try {
            const r = await axios.get(
              `${this.$store.state.api}/edupage/countTeacherStudents.php?id=${id}`
            );
            if (r?.data?.status === "Request succesfull") {
              const d = r?.data?.data;
              if (d && typeof d === "object") {
                const solo = Number(d.solo) || 0;
                const duo = Number(d.duo) || 0;
                counts[id] = { total: solo + duo, solo, duo };
              } else {
                const total = Number(d) || 0;
                counts[id] = { total, solo: null, duo: null };
              }
            } else {
              counts[id] = { total: 0, solo: 0, duo: 0 };
            }
          } catch {
            counts[id] = { total: 0, solo: 0, duo: 0 };
          }
        })
      );
      this.teacherCountsGlobal = counts;
    },

    recalculateTeachersForPeriod(year, month) {
      const k = this.key(year, month);
      // const orders = this.ordersByPeriod[k] || [];
      const counts = this.teacherCountsGlobal || {};
      const base = [
        { id: 59, name: "Juraj Kvočka" },
        { id: 54, name: "Andrej Trnka" },
        { id: 607, name: "Matej Kondela" },
      ];
      this.teachersByPeriod[k] = base.map((t) => {
        // const assigned = orders.filter(
        //   (o) =>
        //     String(o.teacher_id) === String(t.id) && o.status === "zaradený"
        // );
        // const solo = assigned.filter(
        //   (o) => (o.formOfStudy || "").toLowerCase() === "solo"
        // ).length;
        // const duo = assigned.filter(
        //   (o) => (o.formOfStudy || "").toLowerCase() === "duo"
        // ).length;
        return {
          id: t.id,
          name: t.name,
          count:
            counts[t.id] && typeof counts[t.id] === "object"
              ? counts[t.id].total ?? 0
              : Number(counts[t.id]) || 0,
          solo: counts[t.id].solo,
          duo: counts[t.id].duo,
        };
      });
    },

    applyData() {
      if (this.includeAllMonths) {
        this.orders = Object.values(this.ordersByPeriod).flat();
      } else {
        const k = this.key(this.selectedYear, this.selectedMonth);
        this.orders = this.ordersByPeriod[k] || [];
      }
      const k = this.key(this.selectedYear, this.selectedMonth);
      this.teachers = this.teachersByPeriod[k] || this.teachers;
      this.stats = this.statsByPeriod[k] || {
        total: 0,
        active: 0,
        resigned: 0,
      };
    },

    async onDeletedOrder(deletedId) {
      for (const key of Object.keys(this.ordersByPeriod)) {
        this.ordersByPeriod[key] = (this.ordersByPeriod[key] || []).filter(
          (o) => o.id !== deletedId
        );
      }
      this.recalculateTeachersForPeriod(this.selectedYear, this.selectedMonth);
      this.applyData();
    },
  },
};
</script>

<style lang="scss" scoped>
.button {
  margin-top: 2em;
  margin-inline: auto;
  font-size: 1.2em;
}
</style>
