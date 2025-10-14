<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>
      <div class="line horizontal w-90"></div>
      <div class="under-headline">
        Tu môžete zadať kód z vašej darčekovej poukážky a automaticky sa vám jej
        hodnota pridá do vášho košíka. Pri nákupe si môžete zvoliť, či chcete
        použiť darčekovú poukážku alebo zaplatiť kartou.
      </div>
      <ZisteniePoukazky />
    </div>
  </section>
  <div class="mobile" :id="$route.name">
    <section>
      <div class="scroll">
        <TheHeadline></TheHeadline>
        <div class="line horizontal w-90"></div>
        <div class="under-headline">
          Tu môžete zadať kód z vašej darčekovej poukážky a automaticky sa vám
          jej hodnota pridá do vášho košíka. Pri nákupe si môžete zvoliť, či
          chcete použiť darčekovú poukážku alebo zaplatiť kartou.
        </div>
        <ZisteniePoukazky />
      </div>
    </section>
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";
import ZisteniePoukazky from "@/components/Darcekove/ZisteniePoukazky.vue";

export default {
  components: {
    TheHeadline,
    ZisteniePoukazky,
  },
};
</script>

<style>
.under-headline {
  font-size: 1.6em;
  color: black;
  line-height: 115%;
  font-family: "Bahnschrift", sans-serif;
  margin: 0 auto;
  margin-top: 2em;
  width: 72%;
}

@media screen and (max-width: 750px) {
  h5 {
    margin: 1em 1.2em 0.5em 1.2em !important;
  }
}
</style>
