<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="box">
        <div class="box-item">
          <p>Prečo by ste sa mali zaregistrovať?</p>
          <p class="strong">
            🎵 Pestrost pesničiek pre všetky vkusy od ludoviek po moderné hity
          </p>
          <p class="strong">
            ✨ Interaktívne lekcie od skúsených učiteľov heligónky
          </p>
          <p class="strong">💡 Osvojte si techniky a tipy od profesionálov</p>
          <p class="strong">
            🎁 Možnosť zakúpiť si noty a nahrávky priamo od nás
          </p>
        </div>

        <div class="line"></div>

        <div class="box-item">
          <h3>REGISTROVAŤ SA</h3>
          <RegisterForm version="desktop" />
        </div>
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
        <TheHeadline />
        <div class="box">
          <div class="box-item">
            <p>Prečo by ste sa mali zaregistrovať?</p>
            <p class="strong">
              🎵 Pestrosť pesničiek pre všetky vkusy od ľudoviek po moderné hity
            </p>
            <p class="strong">
              ✨ Interaktívne lekcie od skúsených učiteľov heligónky
            </p>
            <p class="strong">💡 Osvojte si techniky a tipy od profesionálov</p>
            <p class="strong">
              🎁 Možnosť zakúpiť si noty a nahrávky priamo od nás
            </p>
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
          <div class="box-item">
            <h3>REGISTROVAŤ SA</h3>
            <RegisterForm version="mobil" />
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";
import RegisterForm from "@/components/Registration/TheRegistration.vue";

export default {
  components: {
    TheHeadline,
    RegisterForm,
  },
  mounted() {
    window.scrollTo(0, 0);
  },
};
</script>

<style scoped lang="scss">
@import "@/assets/sass/_colors.scss";

p {
  margin: 0.6em 5% 0.6em auto;
  text-align: right;
  font-size: 1.46vw;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 2.15625rem */
  width: 75%;

  &:has(strong) {
    width: 60%;
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
  margin: 0.5em auto;
}

.button {
  padding: 0.5em 0.9em 0.5em;
  margin: 0 auto;
}

.box-item {
  padding: 1em 0;
  justify-content: space-between;
}

.box {
  gap: 3%;
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

.strong {
  font-weight: 600;
}

.box-item p:first-child {
  font-size: 1.7vw; //1.8em
  width: 95%;
}

.mobile .box-item p:first-child {
  font-size: 5vw !important;
  text-align: center;
}

.box-item p:last-child {
  width: 60%;
}
</style>
