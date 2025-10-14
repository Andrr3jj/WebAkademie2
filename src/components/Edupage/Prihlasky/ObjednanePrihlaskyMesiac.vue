<template>
  <div
    v-for="(monthData, index) in rozdeleneObjednavky"
    :key="index"
    class="objednavka-mesiac"
  >
    <div
      v-if="monthData.data.length > 0"
      class="month-header"
      @click="toggleMonth(monthData)"
    >
      <h3 class="month-name">{{ getMonthName(monthData.month) }}</h3>
      <div class="kpi">
        <span class="label">Počet prihlášok:</span>
        <span class="pill">{{ monthData.data.length || 0 }}</span>
      </div>
      <div class="kpi">
        <span class="label">Vybavené prihlášky:</span>
        <span class="pill">
          {{ confirmedCount[monthData.year]?.[monthData.month] || 0 }}
        </span>
      </div>
    </div>

    <div v-if="monthData.isToggled" class="objednavky">
      <JednaObjednanaPrihlaska
        v-for="(order, indexOrder) in monthData.data"
        :key="indexOrder"
        :item="order"
        :users="users"
        :userMap="userMap"
        @updateOrder="$emit('updateOrder', $event)"
        @deleted="onDeletedOrder"
      />
      <div class="line horizontal w-90" style="height: 0.35rem"></div>
    </div>
  </div>
</template>

<script>
import JednaObjednanaPrihlaska from "./JednaObjednanaPrihlaska.vue";

export default {
  components: { JednaObjednanaPrihlaska },
  props: {
    orders: { type: Array, default: () => [] },
    users: { type: Array, default: () => [] },
    userMap: { type: Object, default: () => ({}) },
  },
  data() {
    return { mesiace: [] };
  },
  watch: {
    orders: {
      immediate: true,
      handler(newOrders) {
        this.mesiace = this.rozdelObjednavkyDoMesiacov(newOrders);
      },
    },
  },
  // mounted() tu už netreba – fetch userov si rieši parent
  computed: {
    rozdeleneObjednavky() {
      return this.mesiace;
    },
    confirmedCount() {
      const counts = {};
      this.orders.forEach((objednavka) => {
        if (objednavka.status !== "zaradený") return;
        const ts = Number(objednavka.timestamp_created);
        if (!ts) return;
        const date = new Date(ts * 1000);
        const rok = date.getFullYear();
        const mesiac = date.getMonth();
        if (!counts[rok]) counts[rok] = {};
        if (!counts[rok][mesiac]) counts[rok][mesiac] = 0;
        counts[rok][mesiac]++;
      });
      return counts;
    },
  },
  methods: {
    onDeletedOrder(deletedId) {
      this.mesiace.forEach((mesiac) => {
        mesiac.data = mesiac.data.filter((o) => o.id !== deletedId);
      });
    },
    rozdelObjednavkyDoMesiacov(objednavky) {
      let rozdeleneObjednavky = [];
      objednavky.forEach((objednavka) => {
        const ts = Number(objednavka.timestamp_created);
        if (!ts) return;
        const date = new Date(ts * 1000);
        const rok = date.getFullYear();
        const mesiac = date.getMonth();
        let mesiacovyZaznam = rozdeleneObjednavky.find(
          (z) => z.month === mesiac && z.year === rok
        );
        if (!mesiacovyZaznam) {
          mesiacovyZaznam = {
            data: [],
            isToggled: false,
            month: mesiac,
            year: rok,
          };
          rozdeleneObjednavky.push(mesiacovyZaznam);
        }
        mesiacovyZaznam.data.push({ ...objednavka });
      });
      rozdeleneObjednavky.sort((a, b) => {
        if (a.year !== b.year) return b.year - a.year;
        return b.month - a.month;
      });
      return rozdeleneObjednavky;
    },
    toggleMonth(monthData) {
      monthData.isToggled = !monthData.isToggled;
    },
    getMonthName(i) {
      const months = [
        "Január",
        "Február",
        "Marec",
        "Apríl",
        "Máj",
        "Jún",
        "Júl",
        "August",
        "September",
        "Október",
        "November",
        "December",
      ];
      return months[i];
    },
  },
};
</script>

<style lang="scss" scoped>
.month-header {
  display: flex;
  justify-content: space-between;
  width: 80%;
  max-width: 35em;
  background-color: #fef35a;
  box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
  align-items: center;
  cursor: pointer;
  border-radius: 3em;
  margin: 1.5em auto;
  padding: 0.4em 2.5em;
  h3 {
    font-size: 1.3em;
    letter-spacing: 0.1em;
    margin: auto 0;
  }
  &:hover {
    transform: scale(1.03);
    transition-duration: 0.2s;
  }
}
.objednavka-mesiac {
  width: 100%;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
}
.objednavky {
  display: flex;
  flex-direction: column-reverse;
}

.kpi {
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  gap: 0.5em;
}

.button {
  display: flex;
  width: 10em;
  justify-content: center;
  align-items: center;
  margin: 3em auto 5em;
  font-size: 1em;
}
</style>
