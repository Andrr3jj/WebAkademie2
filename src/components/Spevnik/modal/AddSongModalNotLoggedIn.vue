<template>
  <div>
    <h2>Aby si mohol pridať text piesní, najprv sa prihlás.</h2>

    <div class="modal-desc">
      <p>
        Nemáš ešte účet? Nevadí! Rýchlo sa zaregistruj a zdieľaj svoju hudbu s
        ostatnými.
      </p>
    </div>
    <div class="notlogin-grid">
      <img
        class="modal-absolute-img"
        src="@/assets/images/gallery/JurajPrihlasenie.png"
        alt="heligonkár"
      />

      <!-- Vľavo: benefit + obrázok -->
      <div class="notlogin-left">
        <div class="box">
          <div class="box-item" style="align-items: flex-end !important">
            <p>Prečo by ste sa mali zaregistrovať?</p>
            <p class="strong">
              🎵 Pestrosť pesničiek pre všetky vkusy od ľudoviek po moderné hity
            </p>
            <p class="strong">
              ✨ Interaktívne lekcie od skúsených učiteľov heligónky
            </p>
            <p class="strong">💡 Osvojte si techniky a tipy od profesionálov</p>
            <p class="strong" style="width: 50%">
              🎁 Možnosť zakúpiť si noty a nahrávky priamo od nás
            </p>
            <div class="button green">
              <router-link to="/registracia">Registrovať</router-link>
            </div>
          </div>
        </div>
      </div>

      <div class="line"></div>

      <!-- Vpravo: login -->
      <div class="notlogin-right">
        <div class="login-form-box">
          <TheLogin />
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import TheLogin from "./../../Login/TheLogin.vue";
export default {
  components: {
    TheLogin,
  },
};
</script>

<style lang="scss" scoped>
h2 {
  text-align: center;
  font-size: 2.3em;
  font-weight: 700;
  margin: 0.3em auto 0.7em;
  font-family: "Bahnschrift", sans-serif;
}

.modal-desc {
  text-align: center;
  font-size: 1.25em;
  margin-bottom: 1.5em;
  font-family: "Bahnschrift", sans-serif;
}

.modal-absolute-img {
  position: absolute;
  bottom: -1em;
  left: -3.5%;
  width: auto;
  height: 80%;
  max-height: 95%;
  object-fit: contain;
  filter: drop-shadow(10px 5px 10px rgba(0, 0, 0, 0.35));
  z-index: 0;
  pointer-events: none;
}

.notlogin-grid {
  display: flex;
  gap: 1.4em;
  justify-content: space-between;
  align-items: stretch;
  position: relative;
  border-radius: 32px;
  width: 100%;
  flex-direction: column;
  margin: 2em 0 0em;
}

@media (min-width: 901px) {
  .notlogin-grid {
    flex-direction: row;
  }
}

.notlogin-left {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  min-width: 220px;

  .box-item {
    padding: 1em 0;
    justify-content: space-between;
    gap: 2em;

    p {
      text-align: right !important;
    }
  }

  .box {
    gap: 2%;
  }

  .button {
    font-size: 1em;
    align-self: center;
    margin-left: 8em;
  }

  .strong {
    font-weight: 600;
    width: 60%;
    font-size: 16px;
  }

  .box-item p:first-child {
    font-size: 20px;
    width: 95%;
  }

  .mobile .box-item p:first-child {
    font-size: 5vw !important;
    text-align: center;
  }

  .box-item p:last-child {
    width: 50% !important;
  }
}

.notlogin-right {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;

  .login-form-box {
    width: 100%;
    max-width: 320px;
    background: none;
    border-radius: 13px;
    padding: 0;

    h3 {
      text-align: center;
      font-size: 1.2em;
      font-weight: 700;
      letter-spacing: 0.03em;
      font-family: "Bahnschrift", sans-serif;
      margin-bottom: 0.5em;
      margin-top: 0.15em;
    }
  }
}

@media (max-width: 900px) {
  .notlogin-grid {
    flex-direction: column;
    align-items: center;
    gap: 0.3em;
  }

  .modal-absolute-img {
    display: none !important;
  }

  .notlogin-left,
  .notlogin-right {
    align-items: center !important;
    justify-content: center;
    width: 100% !important;
    min-width: 0 !important;
  }

  .notlogin-left .box-item,
  .notlogin-left .box {
    align-items: center !important;
    justify-content: center !important;
    text-align: center !important;
  }

  .notlogin-left .box-item p,
  .notlogin-left .strong,
  .notlogin-left .box-item p:first-child {
    text-align: center !important;
    width: 100% !important;
    margin: 0 auto !important;
  }

  .notlogin-left .button {
    align-self: center !important;
    margin-left: 0 !important;
    margin-top: 1.1em;
    margin-bottom: 0.2em;
  }

  .login-form-box {
    margin: 0 auto;
    max-width: 350px;
  }

  .modal-absolute-img {
    left: 50% !important;
    transform: translateX(-50%);
    width: 82px !important;
    height: auto !important;
    max-height: 100px !important;
    top: -38px;
    bottom: unset;
    z-index: 1;
    display: block;
  }
}
</style>
