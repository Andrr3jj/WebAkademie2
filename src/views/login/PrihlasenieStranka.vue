<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="box">
        <div class="box-item">
          <p>Chcete ovládať umenie heligónky a hrať svoje obľúbené melódie?</p>
          <p>
            <strong>
              Registrujte sa ešte dnes a otvorte si bránu k hudobnému
              dobrodružstvu!</strong
            >
          </p>

          <div class="button green">
            <router-link to="/registracia">Registrovať</router-link>
          </div>
        </div>

        <div class="line"></div>

        <TheLogin />
      </div>
    </div>
    <img
      class="absolute-img"
      src="@/assets/images/gallery/JurajPrihlasenie.png"
      alt=""
    />
  </section>
  <div class="mobile" :id="$route.name">
    <div class="scroll">
      <section>
        <TheHeadline></TheHeadline>

        <div class="box">
          <div class="box-item">
            <p>
              Chcete ovládať umenie heligónky a hrať svoje obľúbené melódie?
            </p>
            <p>
              <strong>
                Registrujte sa ešte dnes a otvorte si bránu k hudobnému
                dobrodružstvu!</strong
              >
            </p>

            <div class="button green">
              <router-link to="/registracia">Registrovať</router-link>
            </div>
          </div>
        </div>
        <img
          class="absolute-img"
          src="@/assets/images/gallery/JurajPrihlasenie.png"
          alt=""
        />
      </section>

      <section>
        <div class="box">
          <TheLogin />
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import TheLogin from "@/components/Login/TheLogin.vue";
import TheHeadline from "@/components/Menu/TheHeadline.vue";

export default {
  mounted() {
    window.scrollTo(0, 0);
  },
  components: { TheHeadline, TheLogin },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

p {
  margin: 0.6em 10% 0.6em auto;
  padding-left: 5%;
  text-align: right;
  font-size: 1.6em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.15625rem */
  width: 85%;

  &:has(strong) {
    width: 70%;
  }
}

.underline {
  font-size: 1.2em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 1.79688rem */
  letter-spacing: 0.12em;
  text-decoration-line: underline;
  text-align: center;
  margin: 1em;
  transition-duration: 0.5s;

  &:hover {
    letter-spacing: 0.3em;
    transition-duration: 0.3s;
  }
}
h4,
h3 {
  font-size: 2em;
  text-align: center;

  line-height: 115%; /* 2.51563rem */
  letter-spacing: 0.05em;
}

.scroll {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}

.button {
  padding: 0.5em 0.9em 0.5em;
  margin: 0 auto;
}

.box {
  margin: auto;
}

.box-item {
  padding: 3em 0;
}

.button.green {
  margin: 1.8em 18% auto auto;
}

.absolute-img {
  position: absolute;
  bottom: -1.35%;
  left: -2%;
  width: 16vw;
  max-width: 18em;
  filter: drop-shadow(10px 5px 10px rgba(0, 0, 0, 0.35));
}

.mobile .absolute-img {
  max-width: 20em;
}

.mobile section:nth-child(1) > .box > .box-item {
  align-items: flex-end;

  .button {
    margin: 1.8em 10% 1em auto;
    font-size: 4vw;
  }
}

.mobile section p {
  font-size: 4.7vw;
}

.mobile p:has(strong) {
  width: 73vw;
  margin-top: 1em;
}

@media only screen and (max-width: 1500px) {
  .absolute-img {
    display: none;
  }
}

@media only screen and (max-width: 1000px) {
  .box {
    gap: 0;
  }

  p {
    margin: 0.1em 2em 0.5em;
    padding-left: 0;
    text-align: center;
  }
}

@media screen and (max-width: 750px) {
  .absolute-img {
    left: -4.57%;
    width: 35vw;
    max-width: 13em;
    display: block;
  }
  .mobile {
    padding-bottom: 5em;
  }
  .underline {
    font-size: 1.5em;
    font-style: normal;
    font-weight: 400;
    line-height: 115%; /* 1.79688rem */
    letter-spacing: 0.12em;
    text-decoration-line: underline;
    text-align: center;
    margin: 1em;
    transition-duration: 0.5s;

    &:hover {
      letter-spacing: 0.3em;
      transition-duration: 0.3s;
    }
  }

  .box {
    margin: 2em auto;
  }

  p {
    text-align: right;
  }
}
</style>
