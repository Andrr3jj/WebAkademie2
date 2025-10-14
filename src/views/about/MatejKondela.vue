<template>
  <section class="computer">
    <div class="scroll">
      <h1>Matej Kondela</h1>

      <div class="line horizontal w-80"></div>

      <div class="box">
        <div class="box-item">
          <img
            src="@/assets/images/gallery/MatejKondela.png"
            alt="Matej Kondela"
          />
        </div>
        <div class="box-item">
          <div class="text">
            <p>
              Volám sa <strong> Matej Kondela. </strong> Pochádzam z obce
              Zákamenné na Orave. Hre na hudobné nástroje sa venujem už 14
              rokov. S toho päť rokov hrám na heligónke. Tento nástroj ma
              nesmierne baví a preto som sa rozhodol moje skúsenosti zdielať s
              ostatnými, pričom výučbe sa venujem jeden rok. <br /><br />
              Vo voľnom čase chodievám hrávať oslavy, svadby, plesy a mnoho
              daľšieho.
            </p>
          </div>

          <div class="text miesta-nadpis">
            <p>Miesta kde vyučujem:</p>
          </div>

          <div class="text miesta-uci">
            <p>Zakamenné</p>
          </div>
        </div>
      </div>
    </div>

    <img
      class="ovladanie"
      id="sipkaLava"
      src="@/assets/images/gallery/sipka.svg"
      alt="Posun doľava"
      @click="$router.push('/o-nas/libor-hlinik')"
    />
    <img
      class="ovladanie"
      id="sipkaPrava"
      src="@/assets/images/gallery/sipka.svg"
      alt="Posun doprava"
      @click="$router.push('/o-nas/andrej-trnka')"
    />
  </section>

  <div class="mobile">
    <section>
      <div class="scroll">
        <h1>Matej Kondela</h1>

        <div class="line horizontal w-80"></div>

        <div class="box">
          <div class="box-item">
            <img
              src="@/assets/images/gallery/MatejKondela.png"
              alt="Matej Kondela"
            />
          </div>
          <div class="box-item">
            <div class="text">
              <p>
                Volám sa <strong> Matej Kondela. </strong> Pochádzam z obce
                Zákamenné na Orave. Hre na hudobné nástroje sa venujem už 14
                rokov. S toho päť rokov hrám na heligónke. Tento nástroj ma
                nesmierne baví a preto som sa rozhodol moje skúsenosti zdielať s
                ostatnými, pričom výučbe sa venujem jeden rok. <br /><br />
                Vo voľnom čase chodievám hrávať oslavy, svadby, plesy a mnoho
                daľšieho.
              </p>
            </div>

            <div class="text miesta-nadpis">
              <p>Miesta kde vyučujem:</p>
            </div>

            <div class="text miesta-uci">
              <p>Zakamenné</p>
            </div>
          </div>
        </div>
      </div>

      <img
        class="ovladanie"
        id="sipkaLava"
        src="@/assets/images/gallery/sipka.svg"
        alt="Posun doľava"
        @click="$router.push('/o-nas/libor-hlinik')"
      />
      <img
        class="ovladanie"
        id="sipkaPrava"
        src="@/assets/images/gallery/sipka.svg"
        alt="Posun doprava"
        @click="$router.push('/o-nas/andrej-trnka')"
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
  max-width: 24em;
  transform: scale(1.3);
  padding: 14% 5% 0;
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

  .box-item img {
    margin: 0 auto 3em;
  }
}
@media only screen and (max-width: 750px) {
  .mobile section {
    h1 {
      font-size: 9vw !important;
    }

    .box-item img {
      width: 68%;
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
