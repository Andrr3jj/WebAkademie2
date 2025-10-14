<template>
  <div class="hodnotenie-edupage">
    <p class="nadpis">Platba za výučbu</p>
    <div class="action-months">
      <p class="text">
        {{ monthName }} <span>{{ currentYear }}</span>
      </p>
    </div>
    <div class="info">
      <p class="text">Suma na úhradu:</p>
      <p class="suma Adumu">
        {{ paymentDisplay.price }}
      </p>
    </div>
    <div class="info ma-b">
      <p class="text">Počet lekcií:</p>
      <p class="suma Adumu">{{ paymentDisplay.lessons }}</p>
    </div>
    <div class="info">
      <p class="text">Stav</p>
      <p
        class="text"
        :class="['suma', 'Adumu', 'stav', paymentDisplay.statusColor]"
      >
        {{ paymentDisplay.statusText }}
      </p>
    </div>

    <div class="text-mini">
      Splatnosť do:
      {{ paymentDisplay.dueDate }}
    </div>

    <div class="action">
      <img
        src="@/assets/images/icons/arrow.svg"
        alt=""
        class="arrow left"
        @click="prevMonth"
      />
      <div
        class="button"
        :style="
          !paymentDisplay.canPay
            ? 'opacity:0;z-index:-1;pointer-events:none;'
            : ''
        "
        @click="
          $emit('open-modal', {
            payment: currentPayment,
            monthName,
            currentYear,
            monthNameGenitive,
          })
        "
      >
        <p class="Adumu">Zaplatiť</p>
      </div>
      <img
        src="@/assets/images/icons/arrow.svg"
        alt=""
        class="arrow right"
        @click="nextMonth"
      />
    </div>
  </div>
</template>

<script>
/* eslint-disable */
import PrikazKUrhade from "../Edupage/PrikazKUrhade.vue";
export default {
  emits: ["open-modal"],
  components: { PrikazKUrhade },
  data() {
    return {
      paymentHistory: null,
      currentMonth: new Date().getMonth() + 1,
      currentYear: new Date().getFullYear(),
    };
  },
  computed: {
    monthNameGenitive() {
      const monthsGen = [
        "január",
        "február",
        "marec",
        "apríl",
        "máj",
        "jún",
        "júl",
        "august",
        "september",
        "október",
        "november",
        "december",
      ];
      return monthsGen[this.currentMonth - 1] || this.currentMonth;
    },
    currentPayment() {
      const data = this.paymentHistory?.data;
      if (!data) return null;
      const yearObj = data[this.currentYear];
      if (!yearObj) return null;
      const monthObj = yearObj[this.currentMonth];
      if (!monthObj || (Array.isArray(monthObj) && monthObj.length === 0))
        return null;
      if (Array.isArray(monthObj)) return monthObj[0] || null;
      return monthObj;
    },
    paymentDisplay() {
      const p = this.currentPayment;
      if (!p) {
        return {
          price: "--€",
          lessons: "--",
          statusText: "NEVYTVORENÉ",
          statusColor: "stav-empty",
          dueDate: "N/A",
          canPay: false,
        };
      }
      let statusText = "NEZAPLATENÉ";
      let statusColor = "stav";
      if (!p) {
        statusText = "NEVYTVORENÉ";
        statusColor = "stav-empty";
      }
      let canPay = true;
      if (p.status && p.status.toLowerCase() !== "pending") {
        statusText = "ZAPLATENÉ";
        statusColor = "stav-paid";
        canPay = false;
      }
      if (!p.price) canPay = false;
      let lessonsCount = "--";
      if (p.lesson) {
        try {
          const arr = JSON.parse(p.lesson);
          lessonsCount = Array.isArray(arr) ? arr.length : "--";
        } catch {
          lessonsCount = "--";
        }
      }
      return {
        price: p.price ? p.price + "€" : "--€",
        lessons: lessonsCount,
        statusText,
        statusColor,
        dueDate: p.dueDate ?? "20. " + this.monthName + " " + this.currentYear,
        canPay,
      };
    },
    monthName() {
      const monthsNom = [
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
      return monthsNom[this.currentMonth - 1] || this.currentMonth;
    },
  },
  async mounted() {
    await this.loadPaymentHistory();
  },
  methods: {
    async loadPaymentHistory() {
      try {
        const userId = this.$store?.state?.user?.id;
        const apiBase = this.$store?.state?.api;
        if (!userId || !apiBase) return;
        const url = `${apiBase}/edupage/paymentHistory.php`;
        const res = await fetch(url, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ id: userId }),
        });
        const json = await res.json();
        this.paymentHistory = json;
      } catch (e) {
        this.paymentHistory = { error: String(e) };
      }
    },
    prevMonth() {
      if (this.currentMonth === 1) {
        this.currentMonth = 12;
        this.currentYear--;
      } else {
        this.currentMonth--;
      }
    },
    nextMonth() {
      if (this.currentMonth === 12) {
        this.currentMonth = 1;
        this.currentYear++;
      } else {
        this.currentMonth++;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.stav-paid {
  color: #00913f;
}
.stav {
  color: #f26d24;
}
.stav.stav-empty {
  color: #5f5f5f;
}
.action-months {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1em;
}
.hodnotenie-edupage {
  display: flex;
  gap: 0.3em;
  flex-direction: column;
  background: #f9f186;
  padding: 2em;
  border-radius: 3em;

  filter: drop-shadow(5px 5px 5px rgba(0, 0, 0, 0.2));
  box-shadow: inset 2px 5px 8px rgb(0 0 0 / 13%);
}
.nadpis {
  font-size: 2.2em;
  font-weight: 600;
}

.text {
  font-size: 1.6em;
  margin-bottom: 0.4em;
  text-align: left;
}

span:hover {
  font-weight: 600;
}

.suma {
  font-size: 1.5em;
  color: #00913f !important;

  &:hover {
    transform: scale(1.1) skewX(-3deg);
    transition-duration: 0.2s;
  }
}

.info {
  display: flex;
  align-items: center;
  gap: 1em;
  justify-content: space-between;
}

.button {
  padding: 0.1em 1.3em;

  font-size: 1em;
  width: auto;
  margin: auto;
}

.stav {
  color: #f26d24;
}

.ma-b {
  margin-bottom: 1em;
}

.text-mini {
  font-size: 1.2em;
  margin: auto;
}

.action {
  display: flex;
  margin-top: 0.5em;
}

.arrow {
  width: 2em;
  cursor: pointer;

  &:hover {
    transform: scale(1.1);
    transition-duration: 0.2s;
  }
}

.right {
  transform: rotate(180deg);
  &:hover {
    transform: scale(1.1) rotate(180deg);
    transition-duration: 0.2s;
  }
}

@media screen and (max-width: 1024px) {
  .hodnotenie-edupage {
    margin-inline: auto;
    margin-block: 2em;
  }
}

@media screen and (max-width: 750px) {
  .hodnotenie-edupage {
    width: 80%;
  }
}
</style>
