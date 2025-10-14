<template>
  <section class="computer zapisy" :id="$route.name + '1'">
    <div class="scroll">
      <div class="left">
        <p class="nadpis">Odomkni si bonusový obsah ku knihe</p>
        <p class="text">
          Aby si získal prístup k náučným videám, zvukovým nahrávkam a všetkým
          piesňam z knihy vo svojom online zozname, zadaj kód, ktorý nájdeš vo
          vnútri knižky.
        </p>
        <ul>
          <li>Prihlás sa alebo si vytvor účet</li>
          <li>Zadaj svoj jedinečný kód</li>
          <li>Kód je možné použiť iba raz</li>
        </ul>
        <img src="@/assets/images/gallery/kniha-vzor.png" alt="Vzor knižky" />
      </div>

      <div class="line"></div>

      <div class="right video">
        <the-login v-if="$store.state.user == null" />
        <aktivuj-kod-skrateny v-else />
      </div>
    </div>
  </section>

  <div class="mobile" :id="$route.name + 'zapisimobil'">
    <div class="scroll">
      <section>
        <div class="left">
          <p class="nadpis">Odomkni si bonusový obsah ku knihe</p>
          <p class="text">
            Aby si získal prístup k náučným videám, zvukovým nahrávkam a všetkým
            piesňam z knihy vo svojom online zozname, zadaj kód, ktorý nájdeš vo
            vnútri knižky.
          </p>
          <ul>
            <li>Prihlás sa alebo si vytvor účet</li>
            <li>Zadaj svoj jedinečný kód</li>
            <li>Kód je možné použiť iba raz</li>
          </ul>
        </div>

        <div ref="mySection" class="right video">
          <the-login v-if="$store.state.user == null" />
          <aktivuj-kod-skrateny v-else />
        </div>
      </section>
    </div>
  </div>
</template>

<script>
import AktivujKodSkrateny from "../Darcekove/AktivujKodSkrateny.vue";
import TheLogin from "../Login/TheLogin.vue";
export default {
  components: { TheLogin, AktivujKodSkrateny },
  mounted() {
    const scrollElement = document.querySelector(".computer .scroll"); // Nájdenie elementu
    if (scrollElement) {
      scrollElement.addEventListener("scroll", this.handleScroll);
      console.log("scrollEmelent :>> ", scrollElement);
    }
  },

  beforeUnmount() {
    const scrollElement = document.querySelector(".scroll");
    if (scrollElement) {
      scrollElement.removeEventListener("scroll", this.handleScroll);
    }
  },

  methods: {
    handleScroll(event) {
      this.$emit("handleScroll", event); // vyšle event hore do rodiča
      console.log("vyšielam event :>> ", event);
    },
  },

  data() {
    return {
      aktualnePrehravaneId: null,
      data: [],
    };
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

h5 {
  color: #001;
  font-size: 2.2vw;
  margin: auto;
  margin-bottom: 0;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;

  &.slim {
    font-weight: 300;
    font-size: 1.7vw;
    margin: 0 auto 0.4em;
  }
}

.podnadpis.podpis {
  font-size: 1.4vw;
  font-weight: 600;
  margin: 0.4em auto 0em;
}

.podnadpis {
  color: #001;
  font-size: 1.2vw;
  font-style: normal;
  font-weight: 400;
  line-height: 115%;
  margin: auto;
  width: 95%;
}

.fade-slide-enter-active,
.fade-slide-leave-active {
  transition: all 0.3s ease;
  max-height: 1000px;
}

.fade-slide-enter-from,
.fade-slide-leave-to {
  opacity: 0;
  max-height: 0;
}
.fade-slide-enter-to,
.fade-slide-leave-from {
  opacity: 1;
  max-height: 1000px;
}

.image-zapis {
  height: 100%;
  margin-right: 1.5em;
  margin-left: -1.5em;
}

.vyber {
  height: auto;
  min-height: 22em;
  max-height: 24em;
  padding: 2% 4%;
  z-index: 1;

  margin-right: 20%;
  padding-right: 18%;
}

.vyber {
  transition: opacity 0.5s ease, transform 0.5s ease; /* Definovanie plynulosti */
}

.vyber {
  opacity: 1;
  transform: translateY(0);
  height: auto; /* Pre normálnu výšku pred animovaním */
  transition: opacity 0.5s ease-out, transform 0.5s ease-out,
    height 0.5s ease-out, min-height 0.5s ease-out;
}

.vyber.hidden {
  opacity: 0;
  transform: translateY(-100%); /* Posun k hornej hranici pre skrytie */
  height: 0;
  min-height: 0;
  transition-duration: 0.5s;
}

.vyber.hidden h1,
.vyber.hidden > div {
  transform: scaleY(0); /* Zmenšovanie na vertikálnu nulu */
  transition: transform 0.3s ease-out; /* Plynulý prechod na vertikálne zmenšenie */
}

.vyber.hidden.display-none {
  display: none; /* Nastavenie display: none po dokončení animácie */
}

.computer.vyber {
  display: flex;
  width: 100%;
  gap: 0%;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
}

.control {
  display: flex;
  align-items: flex-start;
  justify-content: space-around;
  flex-direction: column;
  gap: 2%;
  width: 100%;
  height: auto;

  position: relative;
  height: 100%;
}

h5,
.podnadpis,
.categories {
  margin: 0 auto;
}

//optimalizácia pre Mobil
.computer {
  display: block;
}

.zapisy {
  height: 100%; //70vh
  display: inline-table;
  padding: 0.5em;
}

/* Keď je .vyber skrytý, posunieme .zapisy hore */
.zapisy {
  transition: transform 0.5s ease-in-out;
}

.zapisy {
  overflow: hidden;
  display: flex;
  flex-direction: column;

  .scroll {
    width: 100%;
  }
}

.rotated {
  transform: rotate(90deg);
}

.mobile,
.tablet {
  display: none;
  padding-bottom: 0;
}

#vnorenyScroll {
  padding-bottom: 0;
}

.image {
  position: absolute;
  right: 0;
  bottom: 5%;
  top: -5%;
  transform: scale(1.1);
  margin-right: -3em;
}

.image img {
  position: unset;
  height: -webkit-fill-available;
  width: auto;
}

//css pre prvy diel spec
.obsah-cast {
  width: 100%;
}

.cast {
  display: flex;
  flex-direction: column;
  width: 100%;
  margin: 1em 0;
}

.cast:last-of-type {
  padding-bottom: 5em;
}

.nadpis-cast {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 2em;

  p {
    font-size: 2.1em;
    text-align: left;
    font-weight: 500;
  }

  img {
    width: 1.4em;
    height: auto;
  }

  &:hover {
    cursor: pointer;
    transform: scale(1.01) translateX(10px);
    transition-duration: 0.2s;
  }
}

.text {
  font-size: 1.4em;
  text-align: left;
  margin: 0em 0 1.5em;
}

.nadpis {
  font-size: 2.2em;
  font-weight: 700;
  margin: 0.8em 0;
  text-align: left;
}

.line {
  height: 80%;
  margin: auto;
}

ul {
  list-style: none;
  padding: 0;
  margin: 0;

  li {
    position: relative;
    padding-left: 1.5em;
    margin-bottom: 0.7em;
    font-weight: bold;
    text-align: left;
    font-size: 1.4em;

    &::before {
      content: "";
      position: absolute;
      left: 0;
      top: 0.25em;
      width: 0.6em;
      height: 0.6em;
      background-color: #90ca50; // zelená bodka
      border-radius: 50%;
    }
  }
}

.scroll {
  width: 100%;
  box-sizing: border-box;

  display: flex;
  justify-content: space-between;
  gap: 2%;
  padding: 1em;
}

.left {
  width: 53%;
  position: relative;
  box-sizing: border-box;

  display: flex;
  align-items: flex-start !important;
  padding: 1em 1em 1em 3em;
}

img {
  position: fixed;
  left: 0;
  bottom: 0;
  width: 30em;
  transform: translate(-3em, 7em);
  transition: transform 0.4s;

  &:hover {
    transform: translate(0, 0);
  }
}

.right {
  width: 45%;
  margin: auto;
  height: max-content;
}

.mobile {
  input {
    font-size: 1.3em;
    font-style: normal;
    font-weight: 400;
    line-height: 137.53%; /* 1.11744rem */
    letter-spacing: 0.04063rem;
  }
  .vyber {
    height: auto;
    font-size: 100%;
    min-height: 11em;
    align-items: center;

    display: flex;
    flex-direction: row;
    gap: 0%;
  }

  div.vyber {
    margin-bottom: 0;
    padding-bottom: 0;
  }

  section.vyber {
    flex-direction: column;
  }

  h1 {
    text-align: center;
    font-size: 5.5vw;
    font-style: normal;
    font-weight: 400;
    line-height: 115%; /* 1.725rem */
    letter-spacing: 0.045rem;
    margin-top: 0.1em;
  }

  .control {
    padding: 0 5% 3%;
  }

  .search {
    width: 100%;
    max-width: unset;
  }

  .layouts {
    margin: 0 0 0.5em 0;
    width: 15%;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100%;

    & > img {
      width: 3em;
    }
  }

  .image-zapis {
    margin-right: 0;
  }

  .button {
    padding: 0.4em 2em;
  }

  .button p {
    font-size: 1.2em;
  }

  .control {
    flex-direction: column;
    height: auto;
  }

  .categories {
    flex-wrap: wrap;
    gap: 3%;
    font-size: 95% !important;
    // justify-content: space-evenly;
  }

  h5 {
    font-size: 2.8vw;
    margin: 0.2em auto;
  }

  .podnadpis {
    font-size: 2vw;
    margin: 0.8em auto 0;
  }

  .image-zapis {
    max-width: 10em;
    height: auto;
  }

  .left {
    padding: 1em;
    margin-bottom: 2em;
    width: 90%;
  }
}

@media screen and (max-width: 750px) {
  .mobile {
    display: block;
  }

  .computer {
    display: none !important;
  }

  .mobile > .scroll#vnorenyScroll {
    padding-bottom: 10em !important;
  }

  .vyber {
    max-height: unset;
  }

  .left {
    margin: 0 5%;
    width: 90%;
  }
}

@media only screen and (max-width: 1650px) {
  .vyber {
    font-size: 90%;
  }

  .control {
    gap: 1%;
  }
}

@media only screen and (max-width: 1500px) {
  .vyber {
    font-size: 80%;
  }

  .control {
    gap: 0%;
  }

  .vyber {
    height: 40vh;
  }

  .button {
    margin: 0.9em 0;
  }
}

@media only screen and (max-width: 1500px) {
  .vyber {
    font-size: 75%;
  }

  .container {
    gap: 4%;
  }

  .control {
    gap: 0%;
  }

  .vyber {
    height: 35vh;
  }

  .button {
    white-space: nowrap;
    margin: 0.6em 0;
  }

  .search {
    margin: 0em 0 0.5em;
  }
}

@media only screen and (max-width: 1300px) {
  .scroll {
    display: flex;
    flex-direction: column;
    padding: 0;
  }

  .left {
    width: 100%;
    max-width: 35em;
    margin: auto;
  }

  .cast:last-of-type {
    padding-bottom: 2em;
    align-items: center;
    .obsah-cast {
      display: flex;
      justify-content: center;
      margin-right: 1em;
    }
  }

  .nadpis-cast {
    margin: auto;
  }

  .right {
    width: 100%;
  }

  .zapisy {
    margin: 0;
  }
}

@media only screen and (max-width: 1100px) {
  .vyber {
    font-size: 70%;
  }

  .noty {
    right: 0%;
  }
}

@media only screen and (max-width: 1050px) {
  .image {
    display: none;
  }

  .vyber {
    height: 23vh;
    padding-right: 2%;
    margin-bottom: 1.5em;
  }

  .mobile .vyber {
    margin-right: 0;
  }

  .control {
    box-sizing: border-box;
  }
}

@media only screen and (max-width: 1000px) {
  #Helishop {
    display: none;
  }
  #Helishopmobil {
    display: flex;
  }

  #Helishopzapisimobil {
    display: none;
  }

  #Helishoptablet {
    display: inline-table;
  }

  #Helishopzápisy > div.scroll {
    padding-bottom: 0;
  }

  .search {
    max-width: 35em;
    width: 80%;
  }
}

@media only screen and (max-width: 850px) {
  .mobile .button p {
    font-size: 1em;
  }

  .mobile .button {
    width: 6.5em;
  }
}

@media only screen and (min-height: 1200px) {
  .image {
    display: none;
  }

  .computer.vyber {
    padding-right: 6%;
  }
}
@media only screen and (max-width: 750px) {
  .mobile h1 {
    font-size: 9vw !important;
    margin-top: 0;
    text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.25);
  }

  .mobile {
    .podnadpis {
      margin: 0.5em auto;
      font-size: 3.3vw;
    }
  }

  .mobile h5 {
    font-size: 4.3vw;
  }

  .mobile {
    padding-bottom: 2em;
  }
  // #Číselné\ zápisy > div.scroll {
  //   padding-bottom: 10em;
  // }

  #Helishopmobil {
    display: block;
  }

  #Helishopzapisimobil {
    display: block;
  }

  #Helishoptablet {
    display: none;
  }

  .categories .buttons .button {
    width: 6.8em;
  }

  .mobile > .scroll {
    padding-bottom: 5em !important;
  }

  .mobile {
    section .button {
      margin: 0.7em auto 0;
      width: 4.7em;

      p {
        margin: unset;
      }
    }
  }
}
@media only screen and (max-width: 600px) {
  .mobile .layouts {
    & > img {
      width: 3.2em;
    }
  }
}
@media only screen and (max-width: 500px) {
  .categories .buttons .button {
    width: 6em;
  }
  .mobile {
    section .button {
      margin: 0.6em auto 0;
      padding: 0.3em 1.3em;

      p {
        font-size: 0.8em;
      }
    }
    .categories-search {
      font-size: 2.5vw;
    }

    .search input {
      padding: 0.3em 0.5em;
      font-size: 0.9em;
    }

    .layouts > img {
      font-size: 2.5vw;
    }

    .search-nadpis {
      font-size: 5vw;
      margin: 0.3em auto 0.5em;
    }
  }
}

@media only screen and (max-width: 430px) {
  .mobile {
    section .button {
      margin: 0.6em auto 0;
      padding: 0.1em 0.7em;

      p {
        font-size: 0.6em;
      }
    }
  }
}
@media only screen and (max-width: 350px) {
  .categories .buttons .button {
    width: 5em;
  }

  .mobile {
    section .button {
      margin: 0.6em auto 0;
      padding: 0.1em 0.6em;

      p {
        font-size: 0.5em;
      }
    }
  }
}
</style>
