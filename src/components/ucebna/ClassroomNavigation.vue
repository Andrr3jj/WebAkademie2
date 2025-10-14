<template>
  <div class="bottom-nav-wrapper">
    <img src="@/assets/images/gallery/AndrejUcebna.png" class="hero-image" />

    <div class="bottom-bar">
      <router-link to="/ucebna/hodnota-poukazky" class="poukazky-link">
        Darčekové poukážky</router-link
      >

      <a
        @click="
          $store.state.user.isSubscribed
            ? $router.push('/ucebna/clenstvo-zakupene')
            : $router.push({
                path: 'ucebna/clenstvo',
                query: { data: 'predplatne' },
              })
        "
      >
        Predplatné
      </a>

      <router-link to="/ucebna/helicas">HeliČas</router-link>
      <router-link to="/knizky/prvy-diel">Kniha</router-link>
    </div>
    <div class="settings-icon" @click="$router.push('/ucebna/nastavenia-uctu')">
      <img src="@/assets/images/icons/nastavenia.svg" alt="ikonka nastavení" />
    </div>
  </div>
</template>

<script>
export default {};
</script>

<style lang="scss" scoped>
.bottom-nav-wrapper {
  position: sticky;
  bottom: 0;
  width: 100%;
  display: flex;
  align-items: flex-end;
  z-index: 100;

  .hero-image {
    position: absolute;
    width: 26vw;
    max-width: 27em;
    left: -3vw;
    z-index: 2;
    bottom: -0.5em;
    pointer-events: none;
  }

  .bottom-bar {
    background: #fef35a;
    width: 80%;
    margin: 0 auto;
    border-radius: 2rem;
    padding: 0.6em 2em;
    display: flex;
    justify-content: space-evenly;
    align-items: center;
    filter: drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.35));
    position: relative;
    z-index: 1;

    a,
    router-link {
      font-size: 1.4em;
      font-weight: 800;
      text-decoration: none;
      color: black;
      transition: transform 0.2s;

      &:hover {
        transform: scale(1.1);
        cursor: pointer;
      }
    }
    .poukazky-link {
      margin-left: 20%;
    }
  }

  .settings-icon {
    cursor: pointer;
    display: flex;
    align-items: center;
    margin-right: 1em;

    img {
      width: 3em;
      height: 3em;
      filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
    }

    &:hover {
      transform: scale(1.1);
    }
  }

  @media screen and (max-width: 1024px) {
    .bottom-bar {
      a,
      router-link {
        font-size: 1em;
      }
      .poukazky-link {
        margin-left: 25%;
      }
    }
    .settings-icon {
      img {
        width: 2.5em;
        height: 2.5em;
        margin-left: 0.5em;
      }
    }
  }

  @media screen and (max-width: 900px) {
    .bottom-bar {
      .poukazky-link {
        margin-left: 27%;
      }
    }
  }

  @media (max-width: 750px) {
    flex-direction: column;
    align-items: center;

    .hero-image {
      position: static;
      width: 40vw;
      max-width: 200px;
      margin-top: 1em;
    }

    .bottom-bar {
      width: 90%;
      padding: 1em 1.5em;

      a,
      router-link {
        font-size: 1.1em;
      }
    }

    .poukazky-link {
      margin-left: 0 !important;
    }

    .settings-icon {
      margin: 1em 0 0;
      img {
        width: 2.2em;
        height: 2.2em;
      }
    }
  }
}
</style>
