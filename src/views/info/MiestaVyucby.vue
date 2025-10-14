<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="line horizontal"></div>

      <div class="box">
        <div class="box-item left">
          <img src="@/assets/images/gallery/mapa.png" alt="" />
        </div>
        <div class="box-item right">
          <p class="nadpis-vyucba">Naše hudobné učebne v teréne:</p>
          <p class="kraj">Žilinský kraj</p>

          <ul>
            <div class="list-items">
              <li>Hlboké nad Váhom</li>
              <li>Kamenná Poruba</li>
              <li>Lietavská svinna</li>
              <li>Zákamenné</li>
              <li>Nededza</li>
            </div>
            <div class="list-items">
              <li>Krasňany</li>
              <li>Skalité</li>
              <li>Rajec</li>
              <li>Varín</li>
              <li>Belá</li>
            </div>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <div class="mobile" :id="$route.name">
    <div class="scroll">
      <section>
        <TheHeadline></TheHeadline>

        <div class="line horizontal"></div>

        <div class="box">
          <div class="box-item left">
            <img src="@/assets/images/gallery/mapa.png" alt="" />
          </div>
          <div class="box-item right">
            <p class="nadpis-vyucba">Naše hudobné učebne v teréne:</p>
            <p class="kraj">Žilinský kraj</p>

            <ul>
              <div class="list-items">
                <li>Hlboké nad Váhom</li>
                <li>Kamenná Poruba</li>
                <li>Lietavská svinna</li>
                <li>Zákamenné</li>
                <li>Nededza</li>
              </div>
              <div class="list-items">
                <li>Krasňany</li>
                <li>Skalité</li>
                <li>Rajec</li>
                <li>Varín</li>
                <li>Belá</li>
              </div>
            </ul>
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";

export default {
  mounted() {
    window.scrollTo(0, 0);
  },
  components: { TheHeadline },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.scroll .line {
  width: 80%;
  margin: auto;
}

ul {
  display: flex;
  justify-content: center;
  gap: 5%;
  width: 100%;
}

li {
  display: flex;
  align-items: center;
  justify-content: flex-start;

  font-size: 2em;
  font-style: normal;
  font-weight: 500;
  line-height: 115%; /* 2.51563rem */
}

ul li::before {
  content: "";
  display: inline-block;
  width: 0.6em; /* Adjust the width as needed */
  height: 0.6em; /* Set the height to match the width for a perfect circle */
  background-color: #8ec84e;
  border: 1px solid #fff;
  border-radius: 50%; /* Creates a circle */
  margin-right: 0.5em; /* Adjust the spacing as needed */
}

.nadpis-vyucba {
  text-align: center;

  font-size: 2.5em;
  font-style: normal;
  font-weight: 600;
  line-height: 115%; /* 2.875rem */
}

.kraj {
  font-size: 2.5em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.875rem */
  margin: 0.7em 0 0.4em;
}

.left img {
  width: 41vh;
  max-width: 28em;
}

.box-item {
  justify-content: center;
}

.right {
  flex: 2;
}

.box {
  margin: 4em auto;
  padding: 0 5%;
}

@media screen and (max-width: 1500px) {
  .right {
    font-size: 0.85vw;
  }
}
@media screen and (max-width: 1300px) {
  .left img {
    width: 25vw;
  }
}

@media screen and (max-width: 1000px) {
  .right {
    font-size: unset;
  }

  .box {
    flex-direction: column-reverse;
  }

  .left img {
    width: 70%;
    margin-top: 2em;
  }
}
@media screen and (max-width: 700px) {
  .nadpis-vyucba {
    font-size: 5.4vw;
    margin: unset;
  }
  .mobile {
    padding-bottom: 5em;
  }
}
@media screen and (max-width: 500px) {
  li {
    font-size: 4.7vw;
    font-weight: 500;
  }
}
</style>
