<template>
  <div class="calendar-wrapper">
    <div class="months" ref="months">
      <div
        v-for="(month, index) in monthsOfYear"
        :key="`${month.name}-${month.year}`"
        class="month"
        :class="{ selected: isCurrentMonth(month, index) }"
        @click="scrollMonthToCenter(index)"
      >
        {{ month.name }}
      </div>
    </div>
    <div class="days" ref="days" v-if="showDays && !hideDays">
      <div
        v-for="(day, index) in displayedWorkingDays"
        :key="day.date"
        class="day"
        @click="scrollDayToCenter(index)"
      >
        <p :class="{ active: isCurrentDay(index) }">
          {{ day.dayNumber }}
        </p>
        <span>{{ day.dayName }}</span>
      </div>
    </div>
  </div>
  <div class="line horizontal"></div>
  <div v-if="showFilter" class="types">
    <div class="type">
      <p
        v-for="fullName in filteredDaysOfWeekFull"
        :key="fullName"
        class="button"
        :class="{ active: isTypeActive(fullName) }"
        @click="
          setTypDayFilter(fullName);
          $emit('day-changed', fullName);
        "
      >
        {{ fullName }}
      </p>
    </div>
  </div>
</template>

<script>
export default {
  emits: ["day-changed", "date-changed", "month-changed"],
  props: {
    showFilter: { type: Boolean, default: () => true },
    showAllFilter: { type: Boolean, default: () => false },
    showDays: { type: Boolean, default: () => true },
    allowedDaysFull: { type: Array, default: () => [] },
    allowedTimesByDay: { type: Object, default: () => ({}) },
    hideDays: { type: Boolean, default: false },
  },
  inject: ["getAllowedDaysFull"],
  data() {
    return {
      daysOfWeek: ["Po", "Ut", "Str", "Št", "Pi"],
      daysOfWeekFull: ["Pondelok", "Utorok", "Streda", "Štvrtok", "Piatok"],
      selectedTypDays: [],
      currentYear: new Date().getFullYear(),
      visibleYears: [],
      monthNames: [
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
      ],
      selectedMonthIndex: null,
      selectedDayIndex: null,
      displayedWorkingDays: [],
      calendarMap: {},
      calendarData: {},
      selectedDay: null,
      selectedFullDate: "",
      today: new Date(),
      centeringLock: false,
      generateMonthData(year, month) {
        const dayNames = ["Ne", "Po", "Ut", "Str", "Št", "Pi", "So"];
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const days = [];
        for (let day = 1; day <= daysInMonth; day++) {
          const dateObj = new Date(year, month, day);
          days.push({
            dayNumber: day,
            dayName: dayNames[dateObj.getDay()],
            month,
            year,
            date: `${year}-${String(month + 1).padStart(2, "0")}-${String(
              day
            ).padStart(2, "0")}`,
          });
        }
        return days;
      },
    };
  },
  computed: {
    allowedDaysFullRaw() {
      if (this.allowedDaysFull && this.allowedDaysFull.length)
        return this.allowedDaysFull;
      const fn = this.getAllowedDaysFull;
      const arr = typeof fn === "function" ? fn() : [];
      return Array.isArray(arr) ? arr : [];
    },
    hasCalendar() {
      return this.allowedDaysFullRaw.length > 0;
    },
    allowedDaysFullEffective() {
      return this.hasCalendar ? this.allowedDaysFullRaw : this.daysOfWeekFull;
    },
    allowedDaysAbbrev() {
      const map = {
        Pondelok: "Po",
        Utorok: "Ut",
        Streda: "Str",
        Štvrtok: "Št",
        Piatok: "Pi",
      };
      return this.allowedDaysFullEffective.map((n) => map[n] || n);
    },
    filteredDaysOfWeekFull() {
      if (this.showAllFilter) {
        return this.daysOfWeekFull;
      } else {
        return this.daysOfWeekFull.filter((d) =>
          this.allowedDaysFullEffective.includes(d)
        );
      }
    },
    monthsOfYear() {
      const months = this.visibleYears.flatMap((year) =>
        this.monthNames.map((name, index) => ({ fullName: name, index, year }))
      );
      return months.map((m, i, arr) => {
        const isNewYear = i === 0 || m.year !== arr[i - 1].year;
        return {
          ...m,
          name: isNewYear
            ? `${m.fullName} ${String(m.year).slice(-2)}`
            : m.fullName,
        };
      });
    },
  },
  methods: {
    generateWorkingDaysForMonth(year, month) {
      const daysOfWeek = ["Po", "Ut", "Str", "Št", "Pi"];
      const daysInMonth = new Date(year, month + 1, 0).getDate();
      const working = [];
      for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const dayIndex = date.getDay();
        if (dayIndex >= 1 && dayIndex <= 5) {
          working.push({
            dayName: daysOfWeek[dayIndex - 1],
            dayNumber: day,
            year,
            month,
            date: `${year}-${String(month + 1).padStart(2, "0")}-${String(
              day
            ).padStart(2, "0")}`,
          });
        }
      }
      return working;
    },
    setTypDayFilter(fullName) {
      const isSame =
        this.selectedTypDays.length === 1 &&
        this.selectedTypDays[0] === fullName;
      this.selectedTypDays = isSame ? [] : [fullName];
      this.updateDisplayedWorkingDays();
      const idx = this.showAllFilter
        ? this.applySelectIndex(3, false)
        : this.pickTodayOrClosestInCurrentMonth();
      if (idx !== -1) this.applySelectIndex(idx, true);
      console.log("idx :>> ", idx);
    },
    isTypeActive(fullName) {
      return (
        this.selectedTypDays.length === 1 &&
        this.selectedTypDays[0] === fullName
      );
    },
    isCurrentMonth(_, index) {
      return index === this.selectedMonthIndex;
    },
    isCurrentDay(index) {
      return index === this.selectedDayIndex;
    },
    ensureTypeDefaultsFromAllowed() {
      const src = this.allowedDaysFullEffective;
      this.selectedTypDays = this.selectedTypDays.filter((d) =>
        src.includes(d)
      );
    },
    fullToAbbrev(fullName) {
      const map = {
        Pondelok: "Po",
        Utorok: "Ut",
        Streda: "Str",
        Štvrtok: "Št",
        Piatok: "Pi",
      };
      return map[fullName] || fullName;
    },
    updateDisplayedWorkingDays() {
      const allowed = this.allowedDaysAbbrev;
      const filterByType = this.selectedTypDays.length
        ? this.selectedTypDays.map(this.fullToAbbrev)
        : allowed;
      const monthDays = (() => {
        if (
          this.selectedMonthIndex !== null &&
          this.selectedMonthIndex !== -1
        ) {
          const m = this.monthsOfYear[this.selectedMonthIndex];
          return this.calendarMap?.[m.year]?.[m.index] || [];
        }
        return Object.values(this.calendarMap)
          .flatMap((y) => Object.values(y))
          .flat();
      })();
      this.displayedWorkingDays = monthDays
        .filter((d) => allowed.includes(d.dayName))
        .filter((d) => filterByType.includes(d.dayName));
    },
    pickTodayOrClosestInCurrentMonth() {
      if (!this.displayedWorkingDays.length) return -1;
      const today = new Date();
      const y = today.getFullYear(),
        m = today.getMonth(),
        d = today.getDate();
      const same = this.displayedWorkingDays.findIndex(
        (x) => x.year === y && x.month === m && x.dayNumber === d
      );
      if (same !== -1) return same;
      let bestIdx = -1,
        bestDiff = Infinity;
      for (let i = 0; i < this.displayedWorkingDays.length; i++) {
        const x = this.displayedWorkingDays[i];
        if (x.year !== y || x.month !== m) continue;
        const diff = Math.abs(x.dayNumber - d);
        if (diff < bestDiff) {
          bestDiff = diff;
          bestIdx = i;
        }
      }
      return bestIdx === -1 ? 0 : bestIdx;
    },
    scrollMonthToCenter(index) {
      if (this.centeringLock) return;
      const monthsContainer = this.$refs.months;
      if (!monthsContainer) return;
      const monthElements = monthsContainer.querySelectorAll(".month");
      if (!monthElements[index]) return;

      this.centeringLock = true;
      const element = monthElements[index];
      const containerRect = monthsContainer.getBoundingClientRect();
      const elementRect = element.getBoundingClientRect();
      const offset =
        elementRect.left -
        containerRect.left -
        containerRect.width / 2 +
        elementRect.width / 2;
      monthsContainer.scrollLeft += offset;

      this.selectedMonthIndex = index;
      const selMonth = this.monthsOfYear[index];
      const allMonthDays =
        this.calendarMap?.[selMonth.year]?.[selMonth.index] || [];
      const allowed = this.allowedDaysAbbrev;
      const filterByType = this.selectedTypDays.length
        ? this.selectedTypDays.map(this.fullToAbbrev)
        : allowed;
      this.displayedWorkingDays = allMonthDays
        .filter((d) => allowed.includes(d.dayName))
        .filter((d) => filterByType.includes(d.dayName));

      // Emit event for month change
      this.$emit("month-changed", {
        month: selMonth.index + 1,
        year: selMonth.year,
      });

      const nextIdx = this.pickTodayOrClosestInCurrentMonth();
      this.applySelectIndex(
        nextIdx !== -1 ? nextIdx : this.displayedWorkingDays.length ? 0 : -1,
        !this.hideDays
      );

      this.$nextTick(() => {
        this.centeringLock = false;
      });
    },
    scrollDayToCenter(index) {
      // Emit selection immediately so parent always receives events
      this.applySelectIndex(index, false);

      if (this.hideDays) return;
      const daysContainer = this.$refs.days;
      if (!daysContainer) return;

      const dayElements = daysContainer.querySelectorAll(".day");
      if (!dayElements[index]) return;

      const element = dayElements[index];
      const containerRect = daysContainer.getBoundingClientRect();
      const elementRect = element.getBoundingClientRect();
      const offset =
        elementRect.left -
        containerRect.left -
        containerRect.width / 2 +
        elementRect.width / 2;

      daysContainer.scrollLeft += offset;
    },
    applySelectIndex(index, centerAfter) {
      this.selectedDayIndex = index;
      const d = index !== -1 ? this.displayedWorkingDays[index] : null;
      console.log("d :>> ", d);
      if (!d) return;

      this.selectedDay = d;
      this.selectedFullDate = `${d.dayName}, ${d.dayNumber}. ${
        this.monthNames[d.month]
      } ${d.year}`;

      const fullMap = {
        Ne: "Nedeľa",
        Po: "Pondelok",
        Ut: "Utorok",
        Str: "Streda",
        Št: "Štvrtok",
        Pi: "Piatok",
        So: "Sobota",
      };
      console.log("som pri emitoch :>> ");
      this.$emit("day-changed", fullMap[d.dayName] || d.dayName);
      this.$emit("date-changed", d.date);

      console.log("d.date, full :>> ", d.date, fullMap[d.dayName], d.dayNamel);
      if (!this.hideDays && centerAfter) {
        this.$nextTick(() => this.scrollDayToCenter(index));
      }
    },
    initializeCalendarMap() {
      const years = [
        this.currentYear - 1,
        this.currentYear,
        this.currentYear + 1,
      ];
      years.forEach((year) => {
        this.calendarMap[year] = {};
        for (let month = 0; month <= 11; month++) {
          this.calendarMap[year][month] = this.generateWorkingDaysForMonth(
            year,
            month
          );
        }
      });
    },
  },
  mounted() {
    const today = new Date();
    this.today = today;
    const year = today.getFullYear();
    const month = today.getMonth();
    const day = today.getDate();

    if (!this.calendarData[year]) this.calendarData[year] = {};
    if (!this.calendarData[year][month]) {
      this.calendarData[year][month] = this.generateMonthData(year, month);
    }

    this.visibleYears = [
      this.currentYear - 1,
      this.currentYear,
      this.currentYear + 1,
    ];
    this.initializeCalendarMap();
    this.ensureTypeDefaultsFromAllowed();

    this.selectedMonthIndex = this.monthsOfYear.findIndex(
      (m) => m.index === month && m.year === year
    );
    if (this.selectedMonthIndex === -1) this.selectedMonthIndex = 0;

    this.updateDisplayedWorkingDays();

    const idxToday = this.displayedWorkingDays.findIndex(
      (d) => d.dayNumber === day && d.month === month && d.year === year
    );
    const idx =
      idxToday !== -1 ? idxToday : this.pickTodayOrClosestInCurrentMonth();

    this.applySelectIndex(
      idx !== -1 ? idx : this.displayedWorkingDays.length ? 0 : -1,
      false
    );

    this.$nextTick(() => {
      requestAnimationFrame(() => {
        if (this.selectedMonthIndex !== -1 && this.$refs.months) {
          this.scrollMonthToCenter(this.selectedMonthIndex);
        }
        if (!this.hideDays && this.selectedDayIndex !== -1 && this.$refs.days) {
          this.scrollDayToCenter(this.selectedDayIndex);
        }
      });
    });
  },
  watch: {
    allowedDaysFullRaw() {
      this.ensureTypeDefaultsFromAllowed();
      this.updateDisplayedWorkingDays();
      const idx = this.pickTodayOrClosestInCurrentMonth();
      this.applySelectIndex(
        idx !== -1 ? idx : this.displayedWorkingDays.length ? 0 : -1,
        false
      );
    },
    hideDays() {
      this.$nextTick(() => {
        if (!this.hideDays && this.selectedDayIndex !== -1 && this.$refs.days) {
          this.scrollDayToCenter(this.selectedDayIndex);
        }
      });
    },
  },
};
</script>

<style scoped lang="scss">
.calendar-wrapper {
  width: 80%;
  max-width: 40em;
  margin: auto;
  position: relative;
  &::before,
  &::after {
    content: "";
    position: absolute;
    top: 0;
    width: 25%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
  }
  &::before {
    left: 0;
    background: linear-gradient(to right, #ededed 0%, transparent 100%);
  }
  &::after {
    right: 0;
    background: linear-gradient(to left, #ededed 0%, transparent 100%);
  }
}
.months {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 1em;
  padding: 1.2em 10%;
  width: 100%;
  overflow-x: scroll;
  box-sizing: border-box;
}
.month {
  font-size: 1.4em;
  cursor: pointer;
  transition-duration: 0.2s;
  white-space: nowrap;
  scroll-snap-align: center;
}
.month.selected {
  font-weight: 900;
  font-size: 1.6em;
}
.days {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 1em;
  padding: 1.2em 10%;
  width: 100%;
  overflow-x: scroll;
  box-sizing: border-box;
  scroll-behavior: smooth;
  scroll-snap-type: x mandatory;
}
.day {
  font-size: 1.7em;
  cursor: pointer;
  transition-duration: 0.2s;
  white-space: nowrap;
  scroll-snap-align: center;
  display: flex;
  flex-direction: column;
  gap: 0.3em;
  p {
    border-radius: 0.2em;
    background: #d9d9d9;
    padding: 0.05em 0.4em 0;
  }
  p.active {
    background: #90ca50;
    border: 2px solid #00913f;
  }
  span {
    font-size: 0.5em;
    font-weight: 300;
  }
}
.types .button.active {
  background-color: #00913f;
}
.button {
  font-size: 1em;
}
.type {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1.5em;
}
.line {
  margin-bottom: -1.2em;
  margin-top: 1em;
  height: 0.3rem;
}
</style>
