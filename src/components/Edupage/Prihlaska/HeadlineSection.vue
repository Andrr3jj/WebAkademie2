<template>
  <div>
    <h1>{{ title }}</h1>
    <h5>{{ subtitle }}</h5>
    <p>{{ text }}</p>
  </div>
</template>

<script>
export default {
  name: "HeadlineSection",
  props: {
    title: {
      type: String,
      required: true,
    },
    subtitle: {
      type: String,
      default: "",
    },
    text: {
      type: String,
      default: "",
    },
  },
};
</script>

<style lang="scss" scoped>
div {
  font-size: 90%;
  display: flex;
  flex-direction: column;
  gap: 1em;
}
h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.75vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0;
}

h5 {
  text-align: center;
  font-size: 2.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

p {
  text-align: center;
  font-size: 2.18em;
  font-style: normal;
  line-height: 104%;
  color: #000;
  margin-bottom: 0.5em;
  font-family: "Bahnschrift", sans-serif;
}

@media screen and (max-width: 750px) {
  .mobile section h1 {
    font-size: 11vw !important;
  }
  .mobile section p {
    font-size: 3.2vw !important;
  }
}
</style>
