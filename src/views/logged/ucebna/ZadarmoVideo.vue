<template>
  <div class="content computer">
    <section class="first" :id="$route.name">
      <video
        class="lesson-video"
        data-tour="lesson-video"
        controls
        controlsList="nodownload"
      >
        <source
          src="https://heligonkovaakademia.sk/data/free/fasiangy.mp4"
          type="video/mp4"
        />
        Váš prehliadač nepodporuje prehrávanie videa, prosím skúste použiť
        chrome.
      </video>

      <div class="obsah scroll">
        <p class="nadpis-aktualnej Adumu">Názov piesne:</p>
        <p class="nazov-aktualnej">Fašiangy, túrice</p>

        <div class="line horizontal w-70"></div>

        <div class="text-aktualny">
          <p>
            V tomto videu ti vysvetlíme ako sa naučíš hrať prvú najjednoduchšiu
            pesničku a to Fašiangy Turíce. Pozorne postupuj podľa video návodu,
            ak by si mal s čímkoľvek problém, vráť sa a pozri si danú časť
            znova. Opakovane prehravanie a zdokonaľovanie sa v danej časti ta
            privedie k úspechu.
          </p>
        </div>

        <div v-if="showText" class="text-aktualny">
          <p class="center">
            1. Fašiangy, Turíce, Veľká noc ide, <br />
            kto nemá kožuška zima mu bude. <br />
            <br />
            2. Ja nemám, ja nemám, len sa tak trasiem,<br />
            dajte mi slaninky, nech sa popasiem.<br />
            <br />
            3. Fašiangy, fašiangy, fašiangové časy,<br />
            jedni pijú, druhí jedia za stolom klobásy.<br />
            <br />
            4. Tuto nám nedali, tuto nám dajú,<br />
            tu koňa zabili, tu rebrá majú.<br />
            <br />
            5. A my žiaci, neboráci, nemáme čo jesti,<br />
            musíme sa z domu, do domu po dedine pliesti.<br />
          </p>
        </div>

        <div class="buttons">
          <div
            class="button cursor-disable"
            data-tour="mark-complete"
            role="button"
            aria-label="Označiť ako zvládnuté"
          >
            <p>Označiť ako zvládnute</p>
          </div>
          <div
            @click="toggleText()"
            v-if="!showText"
            class="button"
            data-tour="lyrics-toggle"
            role="button"
            aria-label="Text piesne"
          >
            <p>Text piesne</p>
          </div>
          <div
            @click="toggleText()"
            v-else
            class="button"
            data-tour="lyrics-toggle"
            role="button"
            aria-label="Skryť text"
          >
            <p>Skryť text</p>
          </div>
        </div>
      </div>
    </section>

    <section class="second" :id="$route.name">
      <div class="scroll">
        <div class="song">
          <h1>Online výučba</h1>
          <h5>Začiatočník</h5>

          <div class="line horizontal w-80"></div>

          <div class="dificulty">
            <img
              src="@/assets/images/obtiaznost/obtiaznostZac.png"
              alt="Začiatočník"
            />
          </div>

          <div>
            <p class="info-next">Nasleduje pesnička</p>
          </div>
        </div>

        <div class="next zoznam blur">
          <div class="item">
            <img src="@/assets/images/gallery/defaultKurz.png" alt="" />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#02</p>
              </div>
              <p class="info-text">Mamko moja mne se čigaň lubi</p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">15:34</p>
            </div>
          </div>
        </div>

        <div class="line horizontal w-80"></div>

        <div class="zoznam blur">
          <div class="item">
            <img src="@/assets/images/gallery/defaultKurz.png" alt="" />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#02</p>
              </div>
              <p class="info-text">Mamko moja mne se čigaň lubi</p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">15:34</p>
            </div>
          </div>

          <div class="item">
            <img src="@/assets/images/gallery/defaultKurz.png" alt="" />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#02</p>
              </div>
              <p class="info-text">Mamko moja mne se čigaň lubi</p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">15:34</p>
            </div>
          </div>
          <div class="item">
            <img src="@/assets/images/gallery/defaultKurz.png" alt="" />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#02</p>
              </div>
              <p class="info-text">Mamko moja mne se čigaň lubi</p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">15:34</p>
            </div>
          </div>
        </div>

        <img
          src="@/assets/images/icons/rozsirit.png"
          alt="Načitať viacej"
          class="more cursor-disable"
        />
      </div>
    </section>
  </div>

  <div class="content tablet">
    <section class="second" :id="$route.name">
      <div class="scroll">
        <div class="song">
          <h1>Online výučba</h1>
          <h5>Začiatočník</h5>

          <div class="line horizontal w-80"></div>

          <div class="dificulty">
            <img
              src="@/assets/images/obtiaznost/obtiaznostZac.png"
              alt="Začiatočník"
            />
          </div>
        </div>

        <video
          class="lesson-video"
          data-tour="lesson-video"
          controls
          controlsList="nodownload"
        >
          <source
            src="https://heligonkovaakademia.sk/data/free/fasiangy.mp4"
            type="video/mp4"
          />
          Váš prehliadač nepodporuje prehrávanie videa, prosím skúste použiť
          chrome.
        </video>

        <div class="obsah">
          <p class="nadpis-aktualnej Adumu">Názov piesne:</p>
          <p class="nazov-aktualnej">Fašiangy, túrice</p>

          <div class="line horizontal w-70"></div>

          <div class="text-aktualny">
            <p>
              V tomto videu ti vysvetlíme ako sa naučíš hrať prvú
              najjednoduchšiu pesničku a to Fašiangy Turíce. Pozorne postupuj
              podľa video návodu, ak by si mal s čímkoľvek problém, vráť sa a
              pozri si danú časť znova. Opakovane prehravanie a zdokonaľovanie
              sa v danej časti ta privedie k úspechu.
            </p>
          </div>

          <div v-if="showText" class="text-aktualny">
            <p>
              2. V polnoci, v tichom háji, vtáčky prekrásne spievajú, /:Ježiška
              malého v jasličkách tíško uspávajú:/ 3. Vôl, osol zohrievajú to
              malé Božské Dieťatko, /:do jasieľ na neho dýchajú, spí sa mu
              presladko:/ 4. Mária ho kolembá a svätý Jozef mu spieva,
              /:pastieri na fujarách hrajú aj sa mu klaňajú:/
            </p>
          </div>

          <div class="buttons">
            <div
              class="button cursor-disable"
              data-tour="mark-complete"
              role="button"
              aria-label="Označiť ako zvládnuté"
            >
              <p>Označiť ako zvládnute</p>
            </div>
            <div
              @click="toggleText()"
              v-if="!showText"
              class="button"
              data-tour="lyrics-toggle"
              role="button"
              aria-label="Text piesne"
            >
              <p>Text piesne</p>
            </div>
            <div
              @click="toggleText()"
              v-else
              class="button"
              data-tour="lyrics-toggle"
              role="button"
              aria-label="Skryť text"
            >
              <p>Skryť text</p>
            </div>
          </div>
        </div>

        <div>
          <p class="info-next">Nasleduje pesnička</p>
        </div>

        <div class="next zoznam blur">
          <div class="item">
            <img src="@/assets/images/gallery/defaultKurz.png" alt="" />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#02</p>
              </div>
              <p class="info-text">Mamko moja mne se čigaň lubi</p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">15:34</p>
            </div>
          </div>
        </div>

        <div class="line horizontal w-80"></div>

        <div class="zoznam blur">
          <div class="item">
            <img src="@/assets/images/gallery/defaultKurz.png" alt="" />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#02</p>
              </div>
              <p class="info-text">Mamko moja mne se čigaň lubi</p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">15:34</p>
            </div>
          </div>

          <div class="item">
            <img src="@/assets/images/gallery/defaultKurz.png" alt="" />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#02</p>
              </div>
              <p class="info-text">Mamko moja mne se čigaň lubi</p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">15:34</p>
            </div>
          </div>
          <div class="item">
            <img src="@/assets/images/gallery/defaultKurz.png" alt="" />
            <div class="info">
              <div class="nadpis">
                <p class="Adumu">Názov piesne:</p>
                <p class="Adumu">#02</p>
              </div>
              <p class="info-text">Mamko moja mne se čigaň lubi</p>
              <div class="nadpis">
                <p class="Adumu">Dĺžka videa:</p>
              </div>
              <p class="info-text">15:34</p>
            </div>
          </div>
        </div>

        <img
          src="@/assets/images/icons/rozsirit.png"
          alt="Načitať viacej"
          class="more cursor-disable"
        />
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      popisOtvor: false,
      noty: false, //toto odstranim ked to bduem prepajat
      showText: false,
    };
  },
  mounted() {
    window.scrollTo(0, 0);
    // Nájde všetky elementy s triedami .blur a .cursor-disable
    const elements = document.querySelectorAll(".blur, .cursor-disable");

    // Pridá event listener na kliknutie pre každý z týchto elementov
    elements.forEach((element) => {
      element.addEventListener("click", () => {
        this.$store.state.SnackBarText =
          "Tieto funkcie sú dostupné len so zakúpením predplatním";
        // Sem môžete pridať ďalšiu funkcionalitu, ktorá sa má vykonať po kliknutí
      });
    });
  },
  methods: {
    toggleText() {
      if (this.showText == true) {
        this.showText = false;
      } else {
        this.showText = true;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";
section .scroll {
  height: 94%;
  margin: 3% 0.1em 3% 0;
}

section .scroll.obsah {
  padding: 0 5% 1em;
}

section {
  padding: 1%;
}

.blur {
  filter: blur(5px);
}

.cursor-disable {
  cursor: not-allowed !important;
}

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 3vw;
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

  font-size: 1.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%; /* 2.51563rem */
  margin: 0.6em auto;
}

section .scroll {
  height: 98%;
}

.text-piesne {
  text-align: center;
  font-size: 1.875em;
  font-style: normal;
  font-weight: 800;
  margin: 1.1em auto 0.7em;
}

.info-text {
  text-align: center;

  font-size: 1.5625em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%; /* 1.79688rem */
  margin-top: 0.3em;
}

.nadpis-aktualnej {
  font-size: 2.2em;
}

.nazov-aktualnej {
  font-size: 1.8em;
}

.text-aktualny p {
  text-align: left;
  font-size: 1.1em;
}

.text-aktualny .center {
  text-align: center;
}

.text-aktualny {
  margin: 1em 0 3em;
}

.first .line {
  margin: 0.4em auto 2em;
}

.strong {
  color: #000;
  text-align: center;
  font-size: 1.5625em;
  font-style: normal;
  font-weight: 600;
  line-height: 115%; /* 1.79688rem */
}

.content {
  height: 100%;
  gap: 3.5%;
  display: flex;
  flex-direction: row;
  width: 100%;
  width: -webkit-fill-available;
}

.first {
  width: 65%;
  display: flex;
  flex-direction: column;
}

.second {
  width: 35%;
}

.green {
  background: #8ec84e;
}

.text p:not(.text-piesne) {
  margin: 1em auto;
}

.text {
  padding-bottom: 5em;
}

.first {
  padding: 1.85%;
}

.ma-b {
  margin-bottom: 0.5em;
}

.jednaStranka {
  display: none;
}

.tablet {
  display: none;
}
.second {
  background-color: #ededed;
}

.dificulty {
  transform: rotate(180deg) translateY(1.3em);
  z-index: -1;
  position: relative;

  img {
    width: 4.8em;
  }
}

.info-next {
  margin-bottom: 1em;
  margin-top: -0.5em;
  font-size: 1.5em;
}

.more {
  margin: 1em auto 0;
  width: 2em;
  transition-duration: 0.2s;
  cursor: pointer;

  &:hover {
    transform: scale(1.1);
  }
}

.buttons {
  display: flex;
  justify-content: space-around;

  .button {
    font-size: 1.2em;
  }
}

//videa

video {
  width: 100%;
  height: auto;
  object-fit: cover;
  border-radius: 1.5rem;
  border-bottom-left-radius: 1rem;
  border-bottom-right-radius: 1rem;
  outline: 0.3em solid #fef35a;
  filter: drop-shadow(0.2px 0.2px 10px rgba(0, 0, 0, 0.3));
}

//zoznam videa
.item {
  display: flex;
  flex-direction: row;
  gap: 2%;
  font-size: 0.95vw;
  justify-content: center;
  margin: 1em 0;

  .info {
    align-items: flex-start;
  }

  .info-text {
    font-size: 1em;
    text-align: left;
  }

  img {
    border: 0.18rem solid #fef35a;
    border-radius: 0.7rem;
    box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.35);
    width: 35%;
    height: auto;
    object-fit: cover;
    margin: auto 0.1em;
  }

  .nadpis {
    justify-content: space-between;
    display: flex;

    p {
      font-size: 1.1em;
      letter-spacing: 1px;
    }
  }

  .nazov {
    margin-bottom: 0.2em;
  }
}

@media only screen and (max-width: 1100px) {
  .tablet section {
    width: 100%;

    .w-70 {
      margin: 1em auto;
    }

    .buttons .button {
      font-size: 0.9em;
    }

    .text-aktualny p {
      text-align: center;
    }

    .obsah {
      margin-bottom: 3em;
    }

    .item .nadpis p {
      font-size: 2.1em;
    }

    .item img {
      border-radius: 1.2rem;
    }

    .item .info-text {
      font-size: 2em;
    }

    .item {
      margin: 3em 0;
    }

    .scroll {
      margin: 2% 0;
      padding: 0 3%;
      height: 96%;
    }
  }

  .second {
    font-size: 2vw;
  }

  .popis {
    right: 8vw;
    left: unset;
  }

  .computer {
    display: none;
  }

  h1 {
    font-size: 8.2vw;
  }

  .tablet {
    display: flex;

    .zapis {
      max-height: 80vh;
    }
  }

  .images {
    padding-bottom: 2em;
  }
}

@media only screen and (max-width: 750px) {
  .second {
    font-size: 2.3vw;
  }

  .tablet {
    height: auto;
    align-items: flex-start;

    video {
      border-radius: 1rem;
    }

    section .item .nadpis p {
      font-size: 3em;
    }

    .item .info {
      width: 100%;
    }

    section .item .info-text {
      font-size: 2.9em;
    }

    .obsah > p,
    .text-aktualny p {
      margin: 0.3rem 1.2em;
    }
    .buttons {
      flex-direction: column-reverse;
      align-items: center;

      .button {
        font-size: 1.2em;
        margin: 0.5em auto;
      }
    }
  }

  section {
    margin-bottom: 10em;
    height: auto;
  }

  h1 {
    font-size: 12vw;
  }

  .add-naucene {
    left: 44%;
    bottom: 1em;
    font-size: 140%;
  }

  .add-favorite {
    bottom: 0.9em;
    left: 72.2%;
    img {
      width: 3.7em;
    }
  }

  .text {
    padding-bottom: 2em;
  }

  .ovladanie {
    width: 11vw;
  }

  .images {
    padding-bottom: 2em;
  }
}

@media only screen and (max-width: 500px) {
  .second {
    font-size: 3.3vw;
  }

  h1 {
    font-size: 12vw;
  }

  .add-naucene {
    left: 44%;

    bottom: 0.7em;
    font-size: 110%;
  }

  .add-favorite {
    bottom: 0.4em;
    left: 71%;
    img {
      width: 3em;
    }
  }

  .text {
    padding-bottom: 2em;
  }

  .ovladanie {
    width: 11vw;
  }

  .images {
    padding-bottom: 2em;
  }
}
@media only screen and (max-width: 400px) {
  .add-naucene p {
    font-size: 0.8em;
  }
}
</style>
