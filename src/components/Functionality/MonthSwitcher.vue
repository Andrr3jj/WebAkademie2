<template>
  <div class="month-switcher">
    <div class="label">Predané:</div>
    <div class="controls">
      <button @click="changeMonth(-1)">◀</button>
      <small>({{ formattedDate }})</small>
      <button @click="changeMonth(1)">▶</button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    sortKey: String,
    sortDirection: String,
    month: Number,
    year: Number,
  },
  data() {
    return {
      currentDate: this.createDateFromProps(),
    };
  },
  computed: {
    formattedDate() {
      const month = String(this.currentDate.getMonth() + 1).padStart(2, "0");
      const year = this.currentDate.getFullYear();
      return `${month}.${year}`;
    },
    soldMonthValue() {
      const month = String(this.currentDate.getMonth() + 1).padStart(2, "0");
      const year = this.currentDate.getFullYear();
      return `${year}-${month}`;
    },
  },
  watch: {
    month(newVal) {
      if (newVal && this.currentDate.getMonth() + 1 !== newVal) {
        this.currentDate = new Date(
          this.year || new Date().getFullYear(),
          newVal - 1
        );
      }
    },
    year(newVal) {
      if (newVal && this.currentDate.getFullYear() !== newVal) {
        this.currentDate = new Date(newVal, (this.month || 1) - 1);
      }
    },
  },
  mounted() {
    // Ak boli poslané props, nastav ich, inak nastav na dnes
    this.currentDate = this.createDateFromProps();
    this.emitMonthYear();
  },
  methods: {
    createDateFromProps() {
      // Ak rodič posiela mesiac/rok, nastav podľa toho, inak current month
      const y = this.year || new Date().getFullYear();
      const m = this.month ? this.month - 1 : new Date().getMonth();
      return new Date(y, m);
    },
    emitMonthYear() {
      this.$emit("update:month", this.currentDate.getMonth() + 1);
      this.$emit("update:year", this.currentDate.getFullYear());
    },
    changeMonth(delta) {
      const newDate = new Date(this.currentDate);
      newDate.setMonth(newDate.getMonth() + delta);
      this.currentDate = newDate;
      this.emitMonthYear();
      this.$emit("update:sort", {
        column: "soldMonth",
        value: this.soldMonthValue,
      });
    },
  },
};
</script>

<style scoped>
.month-switcher {
  display: flex;
  flex-direction: column;
  align-items: center;
}
.controls {
  display: flex;
  align-items: center;
  gap: 0.5em;
}
button {
  background: none;
  border: none;
  font-size: 1.2em;
  cursor: pointer;
}
</style>
