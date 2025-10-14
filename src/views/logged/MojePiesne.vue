<template>
  <!-- <ziadne-piesne v-if="$store.state.user == null" /> -->

  <MojeZakupenePiesne />
</template>

<script>
// import ZiadnePiesne from "../../components/MojePiesne/ZiadnePiesne.vue";
import MojeZakupenePiesne from "../../components/MojePiesne/MojeZakupenePiesne.vue";

export default {
  components: { MojeZakupenePiesne },
};
</script>

<style lang="scss" scoped></style>
