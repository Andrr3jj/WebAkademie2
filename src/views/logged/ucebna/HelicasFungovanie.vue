<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>
      <div class="line horizontal"></div>
      <UnderText />
      <HowWorks />
      <div class="line horizontal w-90"></div>
      <EarnPoints />
      <div class="line horizontal w-90"></div>
      <BonusTasks />
      <div class="line horizontal w-90"></div>
      <InviteFriends />
      <div class="line horizontal w-90"></div>
      <TrackProgress />
    </div>
  </section>

  <div class="mobile" :id="$route.name">
    <section>
      <div class="scroll">
        <TheHeadline></TheHeadline>
        <HowWorks />
        <div class="line horizontal w-90"></div>
        <EarnPoints />
        <div class="line horizontal w-90"></div>
        <BonusTasks />
        <div class="line horizontal w-90"></div>
        <InviteFriends />
        <div class="line horizontal w-90"></div>
        <TrackProgress />
      </div>
    </section>
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";
import UnderText from "@/components/Helicas/Fungovanie/UnderText.vue";
import HowWorks from "@/components/Helicas/Fungovanie/HowWorks.vue";
import EarnPoints from "@/components/Helicas/Fungovanie/EarnPoints.vue";
import BonusTasks from "@/components/Helicas/Fungovanie/BonusTasks.vue";
import InviteFriends from "@/components/Helicas/Fungovanie/InviteFriends.vue";
import TrackProgress from "@/components/Helicas/Fungovanie/TrackProgress.vue";

export default {
  components: {
    TheHeadline,
    UnderText,
    HowWorks,
    EarnPoints,
    BonusTasks,
    InviteFriends,
    TrackProgress,
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.line {
  margin-top: 2em;
  margin-bottom: 4em;
}
</style>
