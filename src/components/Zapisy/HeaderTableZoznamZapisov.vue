<template>
  <div class="zapisy-table">
    <div class="table-top">
      <p class="Adumu">Názov piesní:</p>
      <p class="Bahnschrift">Určené pre:</p>
      <p class="Bahnschrift">Stupnica:</p>
      <p class="Bahnschrift">Obtiažnosť:</p>
      <p
        class="Bahnschrift cena"
        :style="{
          opacity: isUcebna ? 1 : 0,
          transition: 'opacity 0.3s ease-in-out',
        }"
      >
        Cena:
      </p>
      <p class="Bahnschrift info">Info:</p>
      <p class="Adumu">{{ isUcebna ? "Zakúpiť" : "Začať" }}</p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    layouts: {
      type: Boolean,
      default: () => true,
    },
  },
  data() {
    return {
      isUcebna: !window.location.pathname.includes("ucebna"),
    };
  },
  watch: {
    // Reaguje na zmenu cesty, ak používaš Vue Router
    $route(to) {
      this.isUcebna = !to.path.includes("ucebna");
    },
  },
  mounted() {
    // Pri prvom načítaní nastaví hodnotu podľa URL
    window.addEventListener("popstate", this.updateUcebna);
  },
  beforeUnmount() {
    // Odstráni event listener, aby nebolo zbytočné počúvanie
    window.removeEventListener("popstate", this.updateUcebna);
  },
  methods: {
    updateUcebna() {
      this.isUcebna = !window.location.pathname.includes("ucebna");
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";
// sekcia zapisy

.table-top {
  border-radius: 2.1875em;
  background: #fef35a;
  box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: row;
  align-items: flex-end;
  font-size: 1.1em;
  font-style: normal;
  font-weight: 400;
  letter-spacing: 0.1rem;
  padding: 0.4em 1em;
  border: none;
  justify-content: space-around;
  box-sizing: border-box;
  white-space: nowrap;
  z-index: 5;
}

.info {
  display: none;
}

.table-top p:nth-child(1) {
  width: 34%;
  text-align: left;
}
.table-top p:nth-child(2),
.table-top p:nth-child(3),
.table-top p:nth-child(4) {
  width: 13%;
}
.table-top p:nth-child(5) {
  width: 8%;
  text-align: right;
}

.table-top p:nth-child(7) {
  width: 19%;
}

.Bahnschrift {
  font-weight: 800;
  letter-spacing: 0.02em;
}

.Adumu {
  font-size: 110%;
}

@media only screen and (max-width: 1000px) {
  .table-top p:nth-child(1) {
    width: 60%;
  }
  .table-top p:nth-child(2),
  .table-top p:nth-child(3),
  .table-top p:nth-child(4),
  .table-top p:nth-child(5) {
    display: none;
  }

  .table-top .info {
    display: block;
    text-align: right;
  }
  .table-top p:nth-child(6) {
    width: 15%;
  }

  .table-top p:nth-child(7) {
    width: 25%;
  }

  .table-top {
    gap: 3%;
  }

  .mobile section p {
    font-size: 1em;
  }
}

@media only screen and (max-width: 650px) {
  .table-top p:nth-child(1) {
    width: 45%;
  }

  .table-top p:nth-child(7) {
    width: 40%;
  }
}
</style>
