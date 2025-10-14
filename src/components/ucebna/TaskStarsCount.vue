<template>
  <div class="task-stars-local-wrapper">
    <TaskCount :count="player.splnene ?? 0" />
    <StarRating
      :stars="player.stars"
      :dni="player.dni"
      :hodiny="player.hodiny"
    />
  </div>
</template>

<script>
import TaskCount from "../Helicas/Leaderboard/TaskCount.vue";
import StarRating from "../Helicas/Leaderboard/StarRating.vue";

export default {
  name: "TaskStarsCount",
  components: {
    TaskCount,
    StarRating,
  },
  props: {
    player: {
      type: Object,
      default: () => ({
        ulohy: 0,
        splnene: 0, // <--- Pridaj ak používaš splnene!
        stars: 0,
        dni: 0,
        hodiny: 0,
      }),
    },
  },
};
</script>

<style scoped>
.task-stars-local-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5em;
  margin-top: 0.5em;
  flex-direction: column;
}

:deep(.hviezda) {
  width: 2em;
}

:deep(.task-wrapper) {
  max-width: 8em;
  width: 7em !important;
}

:deep(.task-number) {
  font-size: 2.5em;
}

@media (max-width: 750px) {
  :deep(.task-wrapper) {
    width: 5em !important;
  }
}
</style>
