<template>
  <div class="task-wrapper">
    <img :src="taskIcon" alt="Úloha" class="task-icon" />
    <span class="task-number">{{ count }}</span>
  </div>
</template>

<script>
export default {
  name: "TaskCount",
  props: {
    count: {
      type: Number,
      default: 0,
    },
  },
  data() {
    return {
      taskIcon: require("@/assets/images/icons/task.svg"),
    };
  },
};
</script>

<style scoped>
.task-wrapper {
  position: relative;
  width: 30px;
  height: auto;
}

.task-icon {
  width: 100%;
  height: auto;
  display: block;
}

.task-number {
  position: absolute;
  top: 80%;
  left: 50%;
  transform: translate(-50%, -50%);
  color: #00913f; /* tmavozelená výplň */
  font-size: 1.8em;
  font-weight: 900;
  font-family: "Bahnshrift", sans-serif;
  -webkit-text-stroke: 1px #90ca50; /* svetlý zelený okraj */
  text-stroke: 1px #90ca50;
  pointer-events: none;
}
</style>
