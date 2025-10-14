<template>
  <!-- chyba treba dorobiť ešte aj dátum -->
  <div class="text-center font-sans">
    <p>Dnes o 16:00 spúšťame náš nový HeliShop!</p>
    <div class="text-6xl font-bold Bahnschrift">{{ formattedTime }}</div>
    <p>Sleduj odpočítavanie a buď pri tom ako prvý</p>
  </div>
</template>

<script>
export default {
  data() {
    return {
      intervalId: null,
      remainingTime: 0,
      cielovyCas: null,
      spustene: false,
    };
  },
  computed: {
    formattedTime() {
      if (this.remainingTime <= 0) {
        return "00:00:00";
      }
      const hodiny = String(
        Math.floor((this.remainingTime / (1000 * 60 * 60)) % 24)
      ).padStart(2, "0");
      const minuty = String(
        Math.floor((this.remainingTime / (1000 * 60)) % 60)
      ).padStart(2, "0");
      const sekundy = String(
        Math.floor((this.remainingTime / 1000) % 60)
      ).padStart(2, "0");
      return `${hodiny}:${minuty}:${sekundy}`;
    },
  },
  methods: {
    spustiHeliShop() {
      if (this.spustene) return;
      this.spustene = true;
      this.$emit("spusti-helishop"); // 👈 emit do rodiča
    },
    aktualizujOdpocet() {
      const teraz = new Date();
      this.remainingTime = this.cielovyCas - teraz;

      if (this.remainingTime <= 0) {
        this.remainingTime = 0;
        clearInterval(this.intervalId);
        this.spustiHeliShop();
      }
    },
  },
  mounted() {
    const dnes = new Date();
    this.cielovyCas = new Date(
      dnes.getFullYear(),
      dnes.getMonth(),
      dnes.getDate(),
      16,
      0,
      0
    ); // 16:00 dnes

    const teraz = new Date();
    this.remainingTime = this.cielovyCas - teraz;

    if (this.remainingTime <= 0) {
      this.spustiHeliShop();
    } else {
      this.aktualizujOdpocet();
      this.intervalId = setInterval(this.aktualizujOdpocet, 1000);
    }
  },
  beforeUnmount() {
    if (this.intervalId) {
      clearInterval(this.intervalId);
    }
  },
};
</script>

<style lang="scss" scoped>
.text-6xl {
  font-size: 6em;
  font-weight: 700;
  margin: 0.1em auto;
}

p {
  margin: 1em auto;
  font-size: 1.6em;
}

.text-center {
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
</style>
