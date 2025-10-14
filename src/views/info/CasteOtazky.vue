<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="questions">
        <div @click="ChangeOpen(0)" class="question">
          <img
            v-if="Open[0]"
            src="@/assets/images/icons/minus.svg"
            alt="Zminimalizuj kar-ty"
            :class="{ 'more-margin': Open[0] }"
          />
          <img
            v-else
            src="@/assets/images/icons/plus.svg"
            alt="Roztvor karty"
          />
          <div class="line"></div>
          <div class="text">
            <h5>Od akého veku sa môže začat výučba na heligónke?</h5>
            <p v-if="Open[0]">
              Výučba na heligónke začína od <strong>5 </strong> až
              <strong> 6 </strong> rokov, kedy je dieťa schopné sústrediť sa a
              má základné čítateľské schopnosti, čo umožňuje efektívnejší
              začiatok hudobného vzdelávania.
            </p>
          </div>
        </div>
        <div @click="ChangeOpen(1)" class="question">
          <img
            v-if="Open[1]"
            src="@/assets/images/icons/minus.svg"
            alt="Zminimalizuj karty"
            :class="{ 'more-margin': Open[1] }"
          />
          <img
            v-else
            src="@/assets/images/icons/plus.svg"
            alt="Roztvor karty"
          />
          <div class="line"></div>
          <div class="text">
            <h5>Akú heligónku vybrať ak by som chcel/a začať s hraním ?</h5>
            <p v-if="Open[1]">
              Ak začínate s hraním na heligónke, odporúčame pre mladšie deti vo
              veku <strong>5 </strong> až <strong> 6 </strong> rokov
              značky<strong> Delicia Popular </strong> alebo<strong>
                Hohner Erica </strong
              >, ktoré sú špeciálne navrhnuté pre začiatočníkov v tomto vekovom
              rozpätí. Pre starších žiakov od<strong> 9 </strong> až
              <strong> 10 </strong> rokov a dospelých odporúčame zvážiť
              heligónku značky <strong>Hohner Club II </strong>.
            </p>
          </div>
        </div>
        <div @click="ChangeOpen(2)" class="question">
          <img
            v-if="Open[2]"
            src="@/assets/images/icons/minus.svg"
            alt="Zminimalizuj karty"
            :class="{ 'more-margin': Open[2] }"
          />
          <img
            v-else
            src="@/assets/images/icons/plus.svg"
            alt="Roztvor karty"
          />
          <div class="line"></div>
          <div class="text">
            <h5>Ako začať s hraním na heligónke, ak som úplný začiatočník?</h5>
            <p v-if="Open[2]">
              Pre úplných začiatočníkov je prvým krokom správna voľba heligonky,
              ktorá bude vyhovovať Vašim požiadavkám. Ďalej Vám odporúčame
              využiť naše vzdelávacie videá, ktoré sú navrhnuté tak, aby vás
              sprevádzali od základov a postupne Vás zaviedli do sveta hry na
              heligónke. S naším prístupom sa nemusíte zaoberať notovým zápisom.
              Namiesto toho využívame videá a jednoduchý číselný zápis, čo Vám
              rýchlo a efektívne umožní začať s hraním na heligónke.
            </p>
          </div>
        </div>
        <div @click="ChangeOpen(3)" class="question">
          <img
            v-if="Open[3]"
            src="@/assets/images/icons/minus.svg"
            alt="Zminimalizuj karty"
            :class="{ 'more-margin': Open[3] }"
          />
          <img
            v-else
            src="@/assets/images/icons/plus.svg"
            alt="Roztvor karty"
          />
          <div class="line"></div>
          <div class="text">
            <h5>
              Má heligónka nejaké špecifické technické vlastnosti, ktoré by som
              mal/a poznať pred nákupom nástroja?
            </h5>
            <p v-if="Open[3]">
              Heligónka je špeciálny nástroj s rôznymi vlastnosťami
              ovplyvňujúcimi zvuk. Na Slovensku a v Česku sú obľúbené ladenia CF
              alebo GCF pre trojradové heligónky. Pri kúpe dávajte pozor na stav
              mechu, no údržba je obvykle jednoduchá. Je vhodné mať heligónku so
              stojakom, čo nie len uľahčuje hru, ale aj prispieva k celkovému
              pohodliu pri hrani. Predtým, než sa rozhodnete, je dobré spoznať
              rôzne ladenia a možnosti prispôsobenia podľa Vašich preferencií.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="mobile" :id="$route.name">
    <div class="scroll">
      <section>
        <TheHeadline></TheHeadline>

        <div class="questions">
          <div @click="ChangeOpen(0)" class="question">
            <img
              v-if="Open[0]"
              src="@/assets/images/icons/minus.svg"
              alt="Zminimalizuj kar-ty"
              :class="{ 'more-margin': Open[0] }"
            />
            <img
              v-else
              src="@/assets/images/icons/plus.svg"
              alt="Roztvor karty"
            />
            <div class="line"></div>
            <div class="text">
              <h5>Od akého veku sa môže začat výučba na heligónke?</h5>
              <p v-if="Open[0]">
                Výučba na heligónke začína od <strong>5 </strong> až
                <strong> 6 </strong> rokov, kedy je dieťa schopné sústrediť sa a
                má základné čítateľské schopnosti, čo umožňuje efektívnejší
                začiatok hudobného vzdelávania.
              </p>
            </div>
          </div>
          <div @click="ChangeOpen(1)" class="question">
            <img
              v-if="Open[1]"
              src="@/assets/images/icons/minus.svg"
              alt="Zminimalizuj karty"
              :class="{ 'more-margin': Open[1] }"
            />
            <img
              v-else
              src="@/assets/images/icons/plus.svg"
              alt="Roztvor karty"
            />
            <div class="line"></div>
            <div class="text">
              <h5>Akú heligónku vybrať ak by som chcel/a začať s hraním ?</h5>
              <p v-if="Open[1]">
                Ak začínate s hraním na heligónke, odporúčame pre mladšie deti
                vo veku <strong>5 </strong> až <strong> 6 </strong> rokov
                značky<strong> Delicia Popular </strong> alebo<strong>
                  Hohner Erica </strong
                >, ktoré sú špeciálne navrhnuté pre začiatočníkov v tomto
                vekovom rozpätí. Pre starších žiakov od<strong> 9 </strong> až
                <strong> 10 </strong> rokov a dospelých odporúčame zvážiť
                heligónku značky <strong>Hohner Club II </strong>.
              </p>
            </div>
          </div>
          <div @click="ChangeOpen(2)" class="question">
            <img
              v-if="Open[2]"
              src="@/assets/images/icons/minus.svg"
              alt="Zminimalizuj karty"
              :class="{ 'more-margin': Open[2] }"
            />
            <img
              v-else
              src="@/assets/images/icons/plus.svg"
              alt="Roztvor karty"
            />
            <div class="line"></div>
            <div class="text">
              <h5>
                Ako začať s hraním na heligónke, ak som úplný začiatočník?
              </h5>
              <p v-if="Open[2]">
                Pre úplných začiatočníkov je prvým krokom správna voľba
                heligonky, ktorá bude vyhovovať vašim požiadavkám. Ďalej Vám
                odporúčame využiť naše vzdelávacie videá, ktoré sú navrhnuté
                tak, aby Vás sprevádzali od základov a postupne Vás zaviedli do
                sveta hry na heligónke. S naším prístupom sa nemusíte zaoberať
                notovým zápisom. Namiesto toho využívame videá a jednoduchý
                číselný zápis, čo Vám rýchlo a efektívne umožní začať s hraním
                na heligónke.
              </p>
            </div>
          </div>
          <div @click="ChangeOpen(3)" class="question">
            <img
              v-if="Open[3]"
              src="@/assets/images/icons/minus.svg"
              alt="Zminimalizuj karty"
              :class="{ 'more-margin': Open[3] }"
            />
            <img
              v-else
              src="@/assets/images/icons/plus.svg"
              alt="Roztvor karty"
            />
            <div class="line"></div>
            <div class="text">
              <h5>
                Má heligónka nejaké špecifické technické vlastnosti, ktoré by
                som mal/a poznať pred nákupom nástroja?
              </h5>
              <p v-if="Open[3]">
                Heligonka je špeciálny nástroj s rôznymi vlastnosťami
                ovplyvňujúcimi zvuk. Na Slovensku a v Česku sú obľúbené ladenia
                CF alebo GCF pre trojradové heligonky. Pri kúpe dávajte pozor na
                stav mechu, no údržba je obvykle jednoduchá. Je vhodné mať
                heligónku so stojakom, čo nie len uľahčuje hru, ale aj prispieva
                k celkovému pohodliu pri hraní. Predtým, než sa rozhodnete, je
                dobré spoznať rôzne ladenia a možnosti prispôsobenia podľa
                Vašich preferencií.
              </p>
            </div>
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
  data() {
    return {
      Open: [],
    };
  },
  methods: {
    ChangeOpen(number) {
      if (this.Open[number] == true) {
        this.Open[number] = false;
      } else {
        this.Open[number] = true;
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.line {
  width: 0.3rem;
  height: auto;

  border-radius: 0.34375rem;
  border: 0px solid #000;
  background: #fef35a;
  box-shadow: 5px 5px 15px 0px rgba(0, 0, 0, 0.25);
}
.question {
  display: flex;
  flex-direction: row;
  align-items: stretch;
  justify-content: center;
  padding: 1em;
  cursor: pointer;

  img {
    width: 3em;
    height: auto;
    padding: 1em 1.5em 1em 1em;
    margin: 1em auto auto;
    filter: drop-shadow(3px 3px 3px rgba(0, 0, 0, 0.25));
  }

  .more-margin {
    padding: 1.5em 1.5em 1em 1em;
  }

  .text {
    flex: 9;
    padding: 0 1em 0 2em;
  }

  h5 {
    font-size: 2em;
    font-style: normal;
    font-weight: 400;
    line-height: 115%;
    margin: 1em 0;
    text-align: left;
  }

  p {
    font-size: 1.6em;
    font-style: normal;
    font-weight: 300;
    line-height: 115%; /* 2.15625rem */
    text-align: left;
  }
}

.mobile {
  h5 {
    font-size: 1.5em;
  }

  .text p {
    margin-left: 0;
    font-size: 1.4em !important;
  }
}

@media only screen and (max-width: 1250px) {
  .questions {
    font-size: 90%;
  }
}

@media only screen and (max-width: 450px) {
  .mobile section h5 {
    font-size: 4.5vw !important;
  }

  .question text {
    padding: 0 1em 0 1em;
  }

  .question img,
  .question .more-margin {
    padding: 0.5em 1.5em 0.5em 0.5em;
    font-size: 80%;
  }
}
</style>
