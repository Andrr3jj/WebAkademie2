<template>
  <section class="computer">
    <div class="scroll">
      <h1>Juraj Kvočka</h1>

      <div class="line horizontal w-80"></div>

      <div class="box">
        <div class="box-item">
          <img
            src="@/assets/images/gallery/JurajKvocka.png"
            alt="Juraj Kvočka"
          />
        </div>
        <div class="box-item">
          <div class="text">
            <p>
              Volám sa <strong> Juraj Kvočka.</strong> Hre na heligónku sa
              venujem od roku 2007, keď som ako 11 ročný začal chodiť k
              Martinovi Čerňanskému, ktorý ma naučil na heligónku naozaj veľa za
              čo som mu nesmierne vďačný. Odvtedy mi heligónka prirástla k srdcu
              tak, že sa jej venujem dodnes. <br />
              Počas môjho hrania na heligónke som sa zúčastnil rôznych podujatí
              na Slovensku v Česku, Poľsku,Nemecku, Chorvátsku. Momentálne sa už
              9 rokov naplno venujem výučbe na tento prekrásny nástroj. Za tie
              roky som odučil už cca 200 žiakov z rôzných kútov Slovenska a
              Čiech. <br />
              Aktuálne som aj členom kapely Partia od Žiliny, s ktorou hráme
              žáner pop-folk. Na heligónke sa môžu hrať naozaj rôzne piesne
              preto verím, že každý, kto sa jej venuje alebo ju len rád počúva v
              nej nájdepotešenie, radosť a úsmev.
            </p>
          </div>

          <div class="text miesta-nadpis">
            <p>Miesta kde vyučujem:</p>
          </div>

          <div class="text miesta-uci">
            <p>Krasňany, Rajec, Skalité, Nededza, Belá</p>
          </div>

          <div class="text miesta-nadpis">
            <p>Kontakt:</p>
          </div>

          <div class="text miesta-uci">
            <p>+421 944 086 4704</p>
          </div>
        </div>
      </div>
    </div>

    <img
      class="ovladanie"
      id="sipkaLava"
      src="@/assets/images/gallery/sipka.svg"
      alt="Posun doľava"
      @click="$router.push('/o-nas/andrej-trnka')"
    />
    <img
      class="ovladanie"
      id="sipkaPrava"
      src="@/assets/images/gallery/sipka.svg"
      alt="Posun doprava"
      @click="$router.push('/o-nas/libor-hlinik')"
    />
  </section>

  <div class="mobile">
    <section>
      <div class="scroll">
        <h1>Juraj Kvočka</h1>

        <div class="line horizontal w-80"></div>

        <div class="box">
          <div class="box-item">
            <img
              src="@/assets/images/gallery/JurajKvocka.png"
              alt="Juraj Kvočka"
            />
          </div>
          <div class="box-item">
            <div class="text">
              <p>
                Volám sa <strong> Juraj Kvočka.</strong> Hre na heligónku sa
                venujem od roku 2007, keď som ako 11 ročný začal chodiť k
                Martinovi Čerňanskému, ktorý ma naučil na heligónku naozaj veľa
                za čo som mu nesmierne vďačný. Odvtedy mi heligónka prirástla k
                srdcu tak, že sa jej venujem dodnes. <br />
                Počas môjho hrania na heligónke som sa zúčastnil rôznych
                podujatí na Slovensku v Česku, Poľsku,Nemecku, Chorvátsku.
                Momentálne sa už 9 rokov naplno venujem výučbe na tento
                prekrásny nástroj. Za tie roky som odučil už cca 200 žiakov z
                rôzných kútov Slovenska a Čiech. <br />
                Aktuálne som aj členom kapely Partia od Žiliny, s ktorou hráme
                žáner pop-folk. Na heligónke sa môžu hrať naozaj rôzne piesne
                preto verím, že každý, kto sa jej venuje alebo ju len rád počúva
                v nej nájdepotešenie, radosť a úsmev.
              </p>
            </div>

            <div class="text miesta-nadpis">
              <p>Miesta kde vyučujem:</p>
            </div>

            <div class="text miesta-uci">
              <p>Krasňany, Rajec, Skalité, Nededza, Belá</p>
            </div>

            <div class="text miesta-nadpis">
              <p>Kontakt:</p>
            </div>

            <div class="text miesta-uci">
              <p>+421 944 086 470</p>
            </div>
          </div>
        </div>
      </div>

      <img
        class="ovladanie"
        id="sipkaLava"
        src="@/assets/images/gallery/sipka.svg"
        alt="Posun doľava"
        @click="$router.push('/o-nas/andrej-trnka')"
      />
      <img
        class="ovladanie"
        id="sipkaPrava"
        src="@/assets/images/gallery/sipka.svg"
        alt="Posun doprava"
        @click="$router.push('/o-nas/libor-hlinik')"
      />
    </section>
  </div>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);
  },
};
</script>

<style lang="scss" scoped>
h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.5vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0 0 0.1em;
}

.text {
  margin: 0 auto 0.8em 0;
}

.text:first-child {
  margin: 0 auto 5em 0;
}

.text p {
  font-size: 1.45em;
  text-align: left;
  line-height: 1.3em;
}

.miesta-nadpis p {
  font-size: 1.65em;
  font-weight: 600;
}
.miesta-uci p {
  font-size: 1.6em;
  font-weight: 400;
}

.box {
  padding: 5%;
}

.box-item:first-child {
  flex: 1.5;
}
.box-item:last-child {
  flex: 2;
}

.box-item img {
  width: 100%;
  filter: drop-shadow(5px 5px 4px rgba(0, 0, 0, 0.35));
  max-width: 22.55em;
  padding: 5% 5% 0;
}

.ovladanie {
  position: absolute;
  bottom: 1em;
  width: 4em;
  cursor: pointer;
  transition-duration: 0.2s;
}

#sipkaLava {
  left: 1em;

  transform: scaleX(-1);

  &:hover {
    transform: scaleX(-1) scale(1.2) rotate(-20deg);
  }
}
#sipkaPrava {
  right: 1em;
  &:hover {
    transform: scale(1.2) rotate(-20deg);
  }
}

@media only screen and (max-width: 1000px) {
  .box {
    padding: 0 5%;
  }

  .text:first-child {
    margin: 2em auto 3em 0;
  }

  h1 {
    font-size: 7.5vw;
  }

  .miesta-uci {
    margin-bottom: 5em;
  }
}
@media only screen and (max-width: 750px) {
  .mobile section {
    h1 {
      font-size: 9vw !important;
    }

    .box-item img {
      width: 66%;
    }

    .box {
      margin: auto;
      margin-bottom: 5em;
    }

    .text p {
      font-size: 1.2em;
      line-height: 1.25em;
    }

    .miesta-nadpis p {
      font-size: 1.55em;
    }
    .miesta-uci p {
      font-size: 1.35em;
    }
    .miesta-uci {
      margin-bottom: 0;
    }
  }
  .mobile {
    padding-bottom: 7em;
  }
}
</style>
