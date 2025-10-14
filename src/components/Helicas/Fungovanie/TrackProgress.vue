<template>
  <div class="howToWork">
    <div>
      <h3>Sleduj svoj progres a zbieraj odmeny!</h3>
      <p>
        V sekcii HeliČas nájdeš všetky svoje nazbierané body, splnené úlohy a
        rebríček najaktívnejších študentov. Nečakaj a pridaj sa k tým
        najvytrvalejším! Každá minúta učenia ťa posúva bližšie k odmenám!
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: "TrackProgress",
};
</script>

<style scoped lang="scss">
@import "@/assets/sass/_colors.scss";

.howToWork {
  font-size: 80%;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: start;
  padding-top: 2em;
  h3 {
    font-family: "Bahnschrift", sans-serif;
    font-size: 2.5em;
    margin-bottom: 1em;
  }
  p {
    font-family: "Bahnschrift", sans-serif;
    font-size: 1.8em;
    margin-bottom: 1em;
    width: 77%;
    text-align: center;
    margin-left: auto;
    margin-right: auto;
  }
}

@media screen and (max-width: 900px) {
  .howToWork {
    p {
      width: 100%;
    }
  }
}
</style>
