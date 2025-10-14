<template>
  <div class="header">
    <h1>Hraj, uč sa a vyhraj!</h1>
    <h5>Zbieraj hviezdičky, získavaj body a vyhraj super ceny!</h5>
    <div class="line horizontal"></div>
  </div>
</template>

<style lang="scss" scoped>
h1 {
  text-align: left;
  font-size: 3.125em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%;
  /* 3.59375rem */
  letter-spacing: 0.25em;
  z-index: 1;
  margin: 0;
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 3px rgba(0, 0, 0, 0.25);
  font-size: 5vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0.1em 0.15em 0.1em;
  margin: 0;
}

h5 {
  color: #001;
  font-size: 2.3vw;
  font-weight: 550;
  line-height: 115%;
  margin: 0.6em 2em 0.5em;
}

@media screen and (max-width: 750px) {
  h5 {
    margin: 1em auto 0.7em !important;
  }
}
</style>
