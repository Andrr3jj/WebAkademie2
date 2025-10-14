<template>
  <div class="favorites-toggle">
    <div class="info">
      <img src="@/assets/images/gallery/heligonka.png" alt="ikona heligónky" />
      <div>
        <span class="span_text"
          >Ikonka heligónky pri názve piesne označuje:</span
        >
        <p>
          Že k tejto piesni máme pripravený číselný zápis pre hru na heligónke.
        </p>
        <p>Ak pri piesni ikonu nevidíte, zápis zatiaľ k dispozícii nie je.</p>
      </div>
    </div>

    <div class="actions">
      <div class="action">
        <p>Poznáš pieseň, ktorú tu ešte nemáme?</p>
        <button class="button" @click="$emit('add-song')">
          <p>Chcem pridať pieseň</p>
          <span class="inline-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="17"
              height="17"
              viewBox="0 0 17 17"
              fill="none"
            >
              <path
                d="M16.5717 0.210951C16.8406 0.410148 17 0.728865 17 1.06418V3.45455V12.2193C17 13.6867 15.5723 14.8752 13.8125 14.8752C12.0527 14.8752 10.625 13.6867 10.625 12.2193C10.625 10.7518 12.0527 9.56328 13.8125 9.56328C14.1844 9.56328 14.543 9.6164 14.875 9.716V4.88214L6.375 7.43187V14.344C6.375 15.8115 4.94727 17 3.1875 17C1.42773 17 0 15.8115 0 14.344C0 12.8766 1.42773 11.6881 3.1875 11.6881C3.55938 11.6881 3.91797 11.7412 4.25 11.8408V6.64172V4.25134C4.25 3.78323 4.55879 3.36823 5.00703 3.23212L15.632 0.0449524C15.9541 -0.0513265 16.3027 0.00843285 16.5717 0.210951Z"
                fill="black"
              />
            </svg>
          </span>
        </button>
      </div>
      <div class="action">
        <p>Výber tvojích obľubených piesní</p>
        <button
          class="button favorites"
          :class="{ active: showingOnlyFavorites }"
          @click="$emit('toggle-favorites')"
        >
          <p>Obľúbené</p>
          <span class="inline-icon">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="18"
              height="16"
              viewBox="0 0 18 16"
              fill="none"
            >
              <path
                d="M1.67344 9.43701L8.02617 15.6017C8.28984 15.8575 8.63789 16 9 16C9.36211 16 9.71016 15.8575 9.97383 15.6017L16.3266 9.43701C17.3953 8.40286 18 6.95213 18 5.43563V5.22368C18 2.66938 16.2246 0.491461 13.8023 0.0712247C12.1992 -0.206497 10.568 0.337983 9.42188 1.52926L9 1.96777L8.57812 1.52926C7.43203 0.337983 5.80078 -0.206497 4.19766 0.0712247C1.77539 0.491461 0 2.66938 0 5.22368V5.43563C0 6.95213 0.604687 8.40286 1.67344 9.43701Z"
                fill="black"
              />
            </svg>
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "FavoritesToggle",
  props: {
    showingOnlyFavorites: {
      type: Boolean,
      default: false,
    },
  },
};
</script>

<style scoped lang="scss">
.favorites-toggle {
  display: flex;
  flex-direction: row;
  justify-content: space-around;
  margin-top: 2em;
  margin-bottom: 2em;
  font-size: 95%;
}

.info {
  display: flex;
  gap: 1em;
  width: 47%;
  align-items: center;

  div {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
  }

  img {
    max-width: 10em;
    min-width: 5em;
    height: auto;
    width: 6em;
  }

  .span_text {
    font-size: 1.25em;
    text-align: left;
  }

  p {
    width: 100%;
    font-size: 1em;
    text-align: left;
  }
}

.actions {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: row;
  gap: 2em;

  p {
    font-size: 1em;
  }

  .action {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .button {
    font-size: 1.25em;
    font-family: "Adumu", sans-serif;
    letter-spacing: normal;
  }

  .inline-icon {
    padding-left: 0.2em;
  }
}

@media screen and (max-width: 1220px) {
  .favorites-toggle {
    flex-direction: column;
    align-items: center;
    justify-content: center;
  }

  .info {
    width: 100%;
    margin-bottom: 2.5em;
    justify-content: center;
  }
}

@media screen and (max-width: 1020px) {
  .favorites-toggle {
    font-size: 85%;
  }
}

@media screen and (max-width: 550px) {
  .info {
    justify-content: center;

    div {
      width: 50%;
    }
  }

  .actions {
    flex-direction: column;
  }
}

@media screen and (max-width: 450px) {
  .mobile section p {
    font-size: 0.9375em;
  }
}
</style>
