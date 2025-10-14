<template>
  <div><router-link to="/admin/">Administračný systém</router-link></div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
a {
  background: #88ca4d;
  padding: 0.7em 1em;
  border-radius: 0.8rem;
  z-index: 2;
  position: relative;

  &:hover {
    cursor: pointer;
    transition-duration: 0.2s;
    padding: 0.9em 1em;
  }
}

@media only screen and (max-width: 750px) {
  a {
    display: none;
  }
}
</style>
