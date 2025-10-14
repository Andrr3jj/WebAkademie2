<template>
  <div class="version-show">
    <span>Verzia: {{ version }}</span>
  </div>
</template>

<script>
import pkg from "@/../package.json";

export default {
  name: "VersionShow",
  data() {
    return {
      version: pkg.version,
    };
  },
};
</script>

<style lang="scss" scoped>
.version-show {
  font-size: 1.1em;
  color: #9abe4a;
}
</style>
