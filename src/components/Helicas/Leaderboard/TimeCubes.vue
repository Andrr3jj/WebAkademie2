<template>
  <div class="time-cubes">
    <div class="cube yellow">
      <p>{{ dni }}</p>
    </div>
    <div class="cube lite-green">
      <p>{{ hodiny }}</p>
    </div>
    <div class="cube green">
      <p>{{ minuty }}</p>
    </div>
  </div>
</template>

<script>
export default {
  name: "TimeCubes",
  props: {
    dni: { type: Number, default: 0 },
    hodiny: { type: Number, default: 0 },
    minuty: { type: Number, default: 0 },
  },
};
</script>

<style scoped lang="scss">
.time-cubes {
  display: flex;
  gap: 0.3em;
  justify-content: center;
  align-items: center;
}

.cube {
  width: 1.8em;
  height: 1.8em;
  padding: 0.4em;
  border: 0.15em solid #fef35a;
  background: #fef35a;
  border-radius: 0.4rem;
  box-shadow: 2.5px 2.5px 5px rgba(0, 0, 0, 0.35),
    inset 2px 2px 10px rgba(0, 0, 0, 0.25);
  display: flex;
  align-items: center;
  justify-content: center;

  p {
    font-family: Adumu, Bahnshrift, sans-serif;
    font-weight: 400;
    font-size: 1.1em;
    letter-spacing: 0.05em;
    margin: 0;
    color: black;
  }
}

.lite-green {
  background: #8ec84e;
}
.green {
  background: #00913f;
}
</style>
