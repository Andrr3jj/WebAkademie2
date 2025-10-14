<template>
  <div class="undertext">
    <p class="text">
      Chceš sa naučiť hrať na heligónke, zlepšovať sa a zároveň získavať odmeny?
      Heličas je jedinečný systém, ktorý odmeňuje tvoj čas strávený učením!
    </p>
  </div>
</template>

<script>
export default {
  name: "UnderText",
};
</script>

<style scoped lang="scss">
.undertext {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-top: 3em;

  .text {
    font-family: "Bahnschrift", sans-serif;
    font-size: 2em;
    text-align: center;
    width: 90%;
  }
}
</style>
