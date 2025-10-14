<template>
  <div
    v-for="zapisyData in getFilteredZapisy()"
    :key="zapisyData.id"
    class="main-content"
  >
    <div :class="{ hide: sliceStart != 0 }" class="information">
      <p
        v-if="this.$route.path.includes('ucebna')"
        @keydown.prevent
        @paste.prevent
        ref="nazov"
        type="text"
        name="nazov-piesne"
        class="title"
      >
        {{ zapisyData.nazov ? zapisyData.nazov : "Názov" }}
      </p>
      <input
        v-else
        @keydown.prevent
        @paste.prevent
        ref="nazov"
        type="text"
        name="nazov-piesne"
        :placeholder="zapisyData.nazov ? zapisyData.nazov : 'Názov'"
        class="title"
        v-model="zapisyData.nazov"
      />

      <input
        v-if="!this.$route.path.includes('ucebna')"
        @keydown.prevent
        @paste.prevent
        ref="stupnica"
        type="text"
        name="stupnica-piesne"
        :placeholder="zapisyData.stupnica ? zapisyData.stupnica : 'Stupnica'"
        class="stupnica"
        v-model="zapisyData.stupnica"
      />
      <div class="stupnica buttons" v-else>
        <StupniceSelector
          :stupnice="vlastnenyProduktData?.stupnice || []"
          :aktualnaStupnica="stupnica"
          :nacitaneNoty="nacitaneNoty"
          :ciselneZapisy="prepojeneZapisyID"
          @klikol-na-stupnicu="onStupnicaSelected"
        />
      </div>
      <input
        @keydown.prevent
        @paste.prevent
        ref="autor"
        type="text"
        name="autor-piesne"
        :placeholder="zapisyData.autor ? zapisyData.autor : 'Autor'"
        class="autor"
        v-model="zapisyData.autor"
      />
    </div>
    <div class="template">
      <div
        v-for="(cast, castIndex) in getVisibleCasty(zapisyData)"
        :key="castIndex"
        :class="[
          'cast',
          {
            'cast-margin':
              pocetCasti == 4 ? castIndex % 4 === 0 && castIndex >= 4 : false,
          },
          {
            'cast-margin2':
              pocetCasti == 5 ? castIndex % 5 === 0 && castIndex >= 5 : false,
          },
        ]"
      >
        <div class="zapis-cast">
          <div class="takty">
            <div
              v-for="(takt, taktIndex) in cast.takty"
              :key="taktIndex"
              class="takt"
              :class="[
                {
                  'takt-prva': taktIndex === 0,
                  'takt-posledna':
                    taktIndex === cast.takty.length - 1 ||
                    (taktIndex < cast.takty.length - 1 &&
                      (cast.takty[taktIndex + 1].isGhost || false) === true),
                  'i-am-not-here-smile': takt.isGhost === true,
                },
              ]"
            >
              <div
                v-for="(doba, dobaIndex) in takt.doby"
                :key="dobaIndex"
                :class="[
                  'doba',
                  {
                    'doba-prva': dobaIndex === 0,
                    'doba-posledna': dobaIndex === takt.doby.length - 1,
                    'i-am-not-here-smile': doba.isGhost === true,
                  },
                ]"
              >
                <div class="riadky">
                  <img
                    src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/Zaciatok - noty.svg"
                    alt="Začiatok nôt polka"
                    class="start-riadky"
                    v-if="
                      zapisyData.doby == 4
                        ? (taktIndex === 0 && dobaIndex === 0) ||
                          (taktIndex > 0 &&
                            cast.takty[taktIndex - 1].isGhost === true &&
                            dobaIndex === 0)
                        : false
                    "
                  />
                  <img
                    src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/Zaciatok - noty2.svg"
                    alt="Začiatok nôt valčík"
                    v-if="
                      zapisyData.doby == 3
                        ? (taktIndex === 0 && dobaIndex === 0) ||
                          (taktIndex > 0 &&
                            cast.takty[taktIndex - 1].isGhost === true &&
                            dobaIndex === 0)
                        : false
                    "
                    class="start-riadky"
                  />
                  <div class="riadky-overflow">
                    <div
                      class="riadok"
                      v-for="(riadok, riadokIndex) in doba.riadky"
                      :key="riadokIndex"
                      :class="{
                        green:
                          doba.smer_pravo &&
                          riadok.hodnota_cisla[0].cela_gulicka[0].cislo != '',
                        yellow:
                          doba.smer_vlavo &&
                          riadok.hodnota_cisla[0].cela_gulicka[0].cislo != '',
                      }"
                    >
                      <!-- Prechádzanie cez hodnota_cisla v každom riadku -->
                      <div
                        v-for="(cisloObj, cisloIndex) in riadok.hodnota_cisla[0]
                          .cela_gulicka"
                        :key="cisloIndex"
                        :class="getInputClass(riadok.hodnota_cisla[0])"
                      >
                        <!-- Prepojenie inputu s hodnotou cisla -->
                        <input
                          @keydown.prevent
                          @paste.prevent
                          v-model="cisloObj.cislo"
                          type="text"
                          class="doba-cislo"
                          :placeholder="
                            cisloObj.cislo ? cisloObj.cislo : dotSee ? '·' : ''
                          "
                          :style="{
                            opacity:
                              cisloObj.cislo != 0 || cisloObj.cislo == ''
                                ? 1
                                : 0.001,
                          }"
                        />

                        <!-- Prepojenie inputu s hodnotou indexu -->
                        <input
                          @keydown.prevent
                          @paste.prevent
                          v-model="cisloObj.index"
                          type="text"
                          class="doba-index"
                          :placeholder="
                            cisloObj.index ? cisloObj.index : dotSee ? '·' : ''
                          "
                          v-if="cisloObj.index != '0'"
                        />
                      </div>
                      <div
                        v-if="riadok.drzanie > 1"
                        class="drzanie"
                        :class="[
                          Number(zapisyData.doby) == 3 ? 'doby-na-takt3' : '',
                        ]"
                      >
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/2-doby.svg"
                          v-if="riadok.drzanie == 2"
                          alt="Držanie na dve doby"
                          class="doby2"
                          :class="{
                            'doby2-koniec': dobaIndex === takt.doby.length - 1,
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/3-doby.svg"
                          v-if="riadok.drzanie == 3"
                          alt="Držanie na tri doby"
                          class="doby3"
                          :class="{
                            'doby3-koniec':
                              dobaIndex === takt.doby.length - 1 ||
                              dobaIndex === takt.doby.length - 2,
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/4-doby.svg"
                          v-if="riadok.drzanie == 4"
                          alt="Držanie na štyri doby"
                          class="doby4"
                          :class="{
                            'doby4-koniec':
                              dobaIndex === takt.doby.length - 1 ||
                              dobaIndex === takt.doby.length - 2 ||
                              dobaIndex === takt.doby.length - 3,
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/5-dob.svg"
                          v-if="riadok.drzanie == 5"
                          alt="Držanie na päť doby"
                          class="doby5"
                          :class="{
                            'doby5-koniec':
                              Number(zapisyData.doby) === 3
                                ? dobaIndex === takt.doby.length - 1
                                : false,
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/6-dob.svg"
                          v-if="riadok.drzanie == 6"
                          alt="Držanie na šesť doby"
                          class="doby6"
                          :class="{
                            'doby6-koniec':
                              Number(zapisyData.doby) === 4
                                ? dobaIndex === takt.doby.length - 1
                                : Number(zapisyData.doby) === 3 &&
                                  (dobaIndex === takt.doby.length - 2 ||
                                    dobaIndex === takt.doby.length - 1),
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/7-dob.svg"
                          v-if="riadok.drzanie == 7"
                          alt="Držanie na sedem doby"
                          class="doby7"
                          :class="{
                            'doby7-koniec':
                              dobaIndex === takt.doby.length - 1 ||
                              dobaIndex === takt.doby.length - 2,
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/8-dob.svg"
                          v-if="riadok.drzanie == 8"
                          alt="Držanie na osem doby"
                          class="doby8"
                          :class="{
                            'doby8-koniec':
                              Number(zapisyData.doby) === 4
                                ? dobaIndex === takt.doby.length - 1 ||
                                  dobaIndex === takt.doby.length - 2 ||
                                  dobaIndex === takt.doby.length - 3
                                : Number(zapisyData.doby) === 3 &&
                                  dobaIndex === takt.doby.length - 1,
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/9-dob.svg"
                          v-if="riadok.drzanie == 9"
                          alt="Držanie na deväť doby"
                          class="doby9"
                          :class="{
                            'doby9-koniec':
                              Number(zapisyData.doby) === 3
                                ? dobaIndex === takt.doby.length - 1 ||
                                  dobaIndex === takt.doby.length - 2
                                : false,
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/10-dob.svg"
                          v-if="riadok.drzanie == 10"
                          alt="Držanie na desať doby"
                          class="doby10"
                          :class="{
                            'doby10-koniec':
                              Number(zapisyData.doby) === 4
                                ? dobaIndex === takt.doby.length - 1
                                : Number(zapisyData.doby) === 3 &&
                                  (dobaIndex === takt.doby.length - 2 ||
                                    dobaIndex === takt.doby.length - 1),
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/11-dob.svg"
                          v-if="riadok.drzanie == 11"
                          alt="Držanie na jedenásť dôb"
                          class="doby11"
                          :class="{
                            'doby11-koniec':
                              Number(zapisyData.doby) == 4
                                ? dobaIndex === takt.doby.length - 1 ||
                                  dobaIndex === takt.doby.length - 2
                                : dobaIndex === takt.doby.length - 1,
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/12-dob.svg"
                          v-if="riadok.drzanie == 12"
                          alt="Držanie na dvanásť dôb"
                          class="doby12"
                          :class="{
                            'doby12-koniec':
                              (Number(zapisyData.doby) === 4 &&
                                (Number(dobaIndex) ===
                                  Number(takt.doby.length) - 1 ||
                                  Number(dobaIndex) ===
                                    Number(takt.doby.length) - 2 ||
                                  Number(dobaIndex) ===
                                    Number(takt.doby.length) - 3)) ||
                              (Number(zapisyData.doby) === 3 &&
                                Number(dobaIndex) ===
                                  Number(takt.doby.length) - 1),
                          }"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/2-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 20"
                          alt="Držanie na 2 doby"
                          class="doby2-rozdelene"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/4-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 40"
                          alt="Držanie na 4 doby"
                          class="doby4-rozdelene"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/6-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 60"
                          alt="Držanie na 6 doby"
                          class="doby6-rozdelene"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/8-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 80"
                          alt="Držanie na 8 doby"
                          class="doby8-rozdelene"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/10-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 100"
                          alt="Držanie na 10 doby"
                          class="doby10-rozdelene"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/12-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 120"
                          alt="Držanie na 12 doby"
                          class="doby12-rozdelene"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/14-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 140"
                          alt="Držanie na 14 doby"
                          class="doby14-rozdelene"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/2-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 200"
                          alt="Držanie na 2 doby"
                          class="doby2-rozdelene"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/4-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 400"
                          alt="Držanie na 4 doby"
                          class="doby4-rozdelene inverz"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/6-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 600"
                          alt="Držanie na 6 doby"
                          class="doby6-rozdelene inverz"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/8-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 800"
                          alt="Držanie na 8 doby"
                          class="doby8-rozdelene inverz"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/10-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 1000"
                          alt="Držanie na 10 doby"
                          class="doby10-rozdelene inverz"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/12-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 1200"
                          alt="Držanie na 12 doby"
                          class="doby12-rozdelene inverz"
                        />
                        <img
                          src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/14-dob-rozdelene.svg"
                          v-if="riadok.drzanie == 1400"
                          alt="Držanie na 14 doby"
                          class="doby14-rozdelene inverz"
                        />
                      </div>
                    </div>

                    <div
                      class="opakovanie"
                      v-if="doba.opakovanie_koniec || doba.opakovanie_zaciatok"
                      :class="[
                        {
                          'opakovanie-zaciatok': doba.opakovanie_zaciatok,
                          'opakovanie-koniec': doba.opakovanie_koniec,
                        },
                      ]"
                    >
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/Refren - VLAVO.svg?v=2"
                        alt="opakovanie začiatok"
                        v-if="doba.opakovanie_zaciatok"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/Refren - VPRAVO.svg?v=2"
                        alt="opakovanie koniec"
                        v-if="doba.opakovanie_koniec"
                      />
                    </div>
                  </div>
                  <img
                    src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/Koniec noty.svg"
                    alt="Koniec nôt"
                    class="end-riadky"
                    v-if="
                      (taktIndex == cast.takty.length - 1 &&
                        dobaIndex == takt.doby.length - 1) ||
                      (taktIndex < cast.takty.length - 1 &&
                        (cast.takty[taktIndex + 1].isGhost || false) === true &&
                        dobaIndex == takt.doby.length - 1)
                    "
                  />
                </div>

                <!-- Ostatné inputy pre slabiku, basu, smer atď. -->
                <!-- <div class="slabika-text">
                      <input
                      @keydown.prevent @paste.prevent
                        v-model="doba.text"
                        type="text"
                        class="doba-text"
                        :placeholder="doba.text ? doba.text : dotSee ? '·' : ''"
                        @keydown="moveToNextInput($event, '.doba-text')"
                      />
                    </div> -->

                <!-- Zobrazenie smeru podľa výsledku z kontroly -->
                <div
                  :class="[
                    {
                      'slabo-siva': doba.farba == 1,
                      'stredne-siva': doba.farba == 2,
                      'silno-siva': doba.farba == 3,
                    },
                  ]"
                  class="smer"
                >
                  <input
                    @keydown.prevent
                    @paste.prevent
                    v-model="doba.smer"
                    type="text"
                    class="doba-smer"
                    :placeholder="doba.smer ? doba.smer : dotSee ? '·' : ''"
                  />
                </div>
                <div class="basy">
                  <img
                    src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/Zaciatok - Basy - noty.svg"
                    alt="Začiatok bás"
                    class="start-basy"
                    v-if="
                      (taktIndex === 0 && dobaIndex === 0) ||
                      (taktIndex > 0 &&
                        cast.takty[taktIndex - 1].isGhost === true &&
                        dobaIndex === 0)
                    "
                  />

                  <div
                    class="basa"
                    :class="{
                      green: doba.smer_pravo && doba.basa.trim(),
                      yellow: doba.smer_vlavo && doba.basa.trim(),
                    }"
                  >
                    <input
                      @keydown.prevent
                      @paste.prevent
                      v-model="doba.basa"
                      type="text"
                      class="doba-basa"
                      :placeholder="doba.basa ? doba.basa : dotSee ? '·' : ''"
                      :class="{
                        'small-g': doba.basa && doba.basa.includes('g'),
                      }"
                      :style="{
                        opacity: parseFloat(doba.basa) !== 0 ? 1 : 0.001,
                      }"
                    />
                    <!-- Prepojenie inputu s hodnotou indexu -->
                    <input
                      @keydown.prevent
                      @paste.prevent
                      v-model="doba.basa_index"
                      type="text"
                      class="doba-index basa-index"
                      :placeholder="
                        doba.basa_index ? doba.basa_index : dotSee ? '·' : ''
                      "
                      v-if="doba.basa_index != '0'"
                    />
                    <div
                      class="opakovanie"
                      v-if="doba.opakovanie_koniec || doba.opakovanie_zaciatok"
                      :class="[
                        {
                          'opakovanie-zaciatok': doba.opakovanie_zaciatok,
                          'opakovanie-koniec': doba.opakovanie_koniec,
                        },
                      ]"
                    >
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/Refren - Basy - VLAVO.svg?v=2"
                        alt="opakovanie začiatok"
                        v-if="doba.opakovanie_zaciatok"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/Refren - Basy - VPRAVO.svg?v=2"
                        alt="opakovanie koniec"
                        v-if="doba.opakovanie_koniec"
                      />
                    </div>
                    <div v-if="doba?.basa_drzanie > 1" class="drzanie-basa">
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/2-dob-basy.svg"
                        v-if="doba?.basa_drzanie == 2"
                        alt="Držanie na 2 doby"
                        class="doby2-basy"
                        :class="[
                          {
                            'doba2-basy-off':
                              dobaIndex === takt.doby.length - 2,
                            'doba2-basy-off3':
                              dobaIndex === takt.doby.length - 1,
                            'doba2-basy-off2': dobaIndex === 0,
                          },
                        ]"
                      />

                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/3-dob-basy.svg"
                        v-if="doba?.basa_drzanie == 3"
                        alt="Držanie na 3 doby"
                        class="doby3-basy"
                        :class="{
                          'doba3-basy-off':
                            zapisyData.doby === 4
                              ? dobaIndex === takt.doby.length - 3
                              : false,
                          'doba3-basy-off2': dobaIndex === takt.doby.length - 2,
                          'doba3-basy-off3': dobaIndex === takt.doby.length - 1,
                        }"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/4-dob-basy.svg"
                        v-if="doba?.basa_drzanie == 4"
                        alt="Držanie na 4 doby"
                        class="doby4-basy"
                        :class="{
                          'doba4-basy-off':
                            zapisyData.doby === 4
                              ? dobaIndex === takt.doby.length - 3
                              : false,
                          'doba4-basy-off1':
                            zapisyData.doby === 3
                              ? dobaIndex === takt.doby.length - 1
                              : false,
                          'doba4-basy-off2': dobaIndex === takt.doby.length - 2,
                          'doba4-basy-off3':
                            zapisyData.doby === 4
                              ? dobaIndex === takt.doby.length - 1
                              : false,
                          'doba4-basy-off4':
                            zapisyData.doby === 3 ? dobaIndex === 0 : false,
                        }"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/5-dob-basy.svg"
                        v-if="doba?.basa_drzanie == 5"
                        alt="Držanie na 5 doby"
                        class="doby5-basy"
                        :class="{
                          'doba5-basy-off2':
                            (zapisyData.doby == 4 &&
                              [
                                takt.doby.length - 3,
                                takt.doby.length - 2,
                              ].includes(dobaIndex)) ||
                            (zapisyData.doby == 3 && dobaIndex === 1),
                          'doba5-basy-off1':
                            zapisyData.doby == 3 ? dobaIndex == 0 : false,

                          'doba5-basy-off3':
                            zapisyData.doby == 3 ? dobaIndex == 2 : false,
                        }"
                      />

                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/6-dob-basy.svg"
                        v-if="doba?.basa_drzanie == 6"
                        alt="Držanie na 6 doby"
                        class="doby6-basy"
                        :class="{
                          'doba6-basy-off':
                            zapisyData.doby == 4 ? dobaIndex == 1 : false,
                          'doba6-basy-off1':
                            zapisyData.doby == 4 ? dobaIndex == 2 : false,
                          'doba6-basy-off2':
                            zapisyData.doby == 4 ? dobaIndex == 3 : false,
                          'doba6-basy-off3':
                            zapisyData.doby == 3 ? dobaIndex == 1 : false,
                          'doba6-basy-off4':
                            zapisyData.doby == 3 ? dobaIndex == 2 : false,
                        }"
                      />

                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/7-dob-basy.svg"
                        v-if="doba?.basa_drzanie == 7"
                        alt="Držanie na 7 doby"
                        class="doby7-basy"
                        :class="{
                          'doba7-basy-off-val':
                            zapisyData.doby == 3 ? dobaIndex == 0 : false,
                          'doba7-basy-off':
                            zapisyData.doby == 4 ? dobaIndex == 1 : false,
                          'doba7-basy-off1':
                            zapisyData.doby == 4 ? dobaIndex == 2 : false,
                          'doba7-basy-off2':
                            zapisyData.doby == 4 ? dobaIndex == 3 : false,
                          'doba7-basy-off3':
                            zapisyData.doby == 3 ? dobaIndex == 1 : false,
                          'doba7-basy-off4':
                            zapisyData.doby == 3 ? dobaIndex == 2 : false,
                        }"
                      />

                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/8-dob-basy.svg"
                        v-if="doba?.basa_drzanie == 8"
                        alt="Držanie na 8 doby"
                        class="doby8-basy"
                        :class="{
                          'doba8-basy-off-val':
                            zapisyData.doby == 3 ? dobaIndex == 0 : false,
                          'doba8-basy-off':
                            zapisyData.doby == 4 ? dobaIndex == 1 : false,
                          'doba8-basy-off1':
                            zapisyData.doby == 4 ? dobaIndex == 2 : false,
                          'doba8-basy-off2':
                            zapisyData.doby == 4 ? dobaIndex == 3 : false,
                          'doba8-basy-off3':
                            zapisyData.doby == 3 ? dobaIndex == 1 : false,
                          'doba8-basy-off4':
                            zapisyData.doby == 3 ? dobaIndex == 2 : false,
                        }"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/2-doby-basy-zac.svg"
                        v-if="doba?.basa_drzanie == 20"
                        alt="Držanie na 2 doby"
                        class="doba2-basy-rozdelene"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/3-doby-basy-zac.svg"
                        v-if="doba?.basa_drzanie == 30"
                        alt="Držanie na 3 doby"
                        class="doba3-basy-rozdelene"
                        :class="{
                          'doba3-basy-rozdelene2':
                            zapisyData.doby === 3 ? true : false,
                        }"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/4-doby-basy-zac.svg"
                        v-if="doba?.basa_drzanie == 40"
                        alt="Držanie na 4 doby"
                        class="doba4-basy-rozdelene"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/5-doby-basy-zac.svg"
                        v-if="doba?.basa_drzanie == 50"
                        alt="Držanie na 5 doby"
                        class="doba5-basy-rozdelene"
                        :class="{
                          'doba5-basy-rozdelene2':
                            zapisyData.doby === 3 ? true : false,
                        }"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/1-doby-basy-kon.svg"
                        v-if="doba?.basa_drzanie == 100"
                        alt="Držanie na 1 doby"
                        class="doba1-basy-rozdelene-kon ukoncenie"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/2-doby-basy-kon.svg"
                        v-if="doba?.basa_drzanie == 200"
                        alt="Držanie na 2 doby"
                        class="doba2-basy-rozdelene-kon ukoncenie"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/3-doby-basy-kon.svg"
                        v-if="doba?.basa_drzanie == 300"
                        alt="Držanie na 3 doby"
                        class="doba3-basy-rozdelene-kon ukoncenie"
                      />
                      <img
                        src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/4-doby-basy-kon.svg"
                        v-if="doba?.basa_drzanie == 400"
                        alt="Držanie na 4 doby"
                        class="doba4-basy-rozdelene-kon ukoncenie"
                      />
                    </div>
                  </div>

                  <img
                    src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/Koniec - Basy - Noty.svg"
                    alt="Koniec bás"
                    class="end-basy"
                    v-if="
                      (taktIndex == cast.takty.length - 1 &&
                        dobaIndex == takt.doby.length - 1) ||
                      (taktIndex < cast.takty.length - 1 &&
                        (cast.takty[taktIndex + 1].isGhost || false) === true &&
                        dobaIndex == takt.doby.length - 1)
                    "
                  />
                </div>

                <div
                  :class="[
                    {
                      'slabo-siva': doba.farba == 1,
                      'stredne-siva': doba.farba == 2,
                      'silno-siva': doba.farba == 3,
                    },
                  ]"
                  class="popis"
                ></div>
              </div>
            </div>
          </div>

          <div class="popis-cast">
            <div
              v-for="(popis, popisIndex) in cast.popis"
              :key="popisIndex"
              class="popis"
              :class="[
                cast.popis_farba[popisIndex] == 1 ? 'slabo-siva' : '',
                cast.popis_farba[popisIndex] == 2 ? 'stredne-siva' : '',
                cast.popis_farba[popisIndex] == 3 ? 'silno-siva' : '',
                cast?.popis_class?.[popisIndex] || '',
              ]"
            >
              <input
                @keydown.prevent
                @paste.prevent
                type="text"
                v-model="cast.popis[popisIndex]"
                :placeholder="
                  cast.popis[popisIndex]
                    ? cast.popis[popisIndex]
                    : dotSee
                    ? '·   ·   ·'
                    : ''
                "
              />
            </div>
          </div>
        </div>

        <!-- <div v-if="(castIndex + 1) % 4 === 0" class="number-page">
              <p>{{ Math.floor(castIndex / 4) + 1 }}.</p>
            </div> -->
      </div>
    </div>
    <!-- <div :style="{ paddingTop: paddingTop + 'px' }" class="information">
      <textarea
        name="text-piesne"
        ref="text"
        v-model="zapisyData.text"
        :placeholder="
          zapisyData.text ? zapisyData.text : dotSee ? 'Žiadny text' : ''
        "
      ></textarea> 
      <input
        @keydown.prevent
        @paste.prevent
        ref="nazov"
        type="text"
        name="nazov-piesne"
        class="title"
        v-model="zapisyData.pata"
        :placeholder="
          zapisyData.pata ? zapisyData.pata : 'Heligónková Akadémia'
        "
      />
    </div> -->
  </div>
</template>

<script>
import StupniceSelector from "@/components/Functionality/StupniceSelector.vue";

export default {
  components: {
    StupniceSelector,
  },
  data() {
    return {
      dotSee: false,
      zapisy: [],
    };
  },
  mounted() {
    this.preventSelect = (e) => e.preventDefault();
    document.addEventListener("selectstart", this.preventSelect);

    // for (let i = 0; i < this.prepojeneZapisyID.length; i++) {
    //   this.nacitajData(this.prepojeneZapisyID[i]);
    //   console.log("nacitavam data pre i :>> ", this.prepojeneZapisyID[i]);
    // }

    this.$nextTick(() => {
      this.$emit("ready");
    });
  },

  unmounted() {
    document.removeEventListener("selectstart", this.preventSelect);
  },
  methods: {
    getVisibleCasty(zapisyData) {
      // 1) bezpečne dostaneme pole casty
      const casty = zapisyData?.dataNoty?.casty || [];
      // 2) spoľahlivo vypočítame start / end
      const start = Number.isInteger(this.sliceStart) ? this.sliceStart : 0;
      const end =
        Number.isInteger(this.sliceEnd) && this.sliceEnd >= 0
          ? this.sliceEnd + 1
          : casty.length;
      // 3) vrátime slice
      return casty.slice(start, end);
    },
    onStupnicaSelected(element) {
      this.$emit("update-stupnica", element);
    },
    // Filtrovanie podľa stupnice
    getFilteredZapisy() {
      return this.zapisy.filter(
        (zapis) =>
          zapis.stupnica.charAt(0).toUpperCase() ===
          this.stupnica.charAt(0).toUpperCase()
      );
    },
    getInputClass(doba) {
      const inputCount = doba.cela_gulicka.length;
      if (inputCount === 1) return "jedna-cisla";
      if (inputCount === 2) return "dva-cisla";
      if (inputCount === 3) return "tri-cisla";
      if (inputCount === 4) return "styri-cisla";
      return "";
    },
    nacitajData(id) {
      console.log("[Editor] nacitajData pre ID", id);
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/load.php?id=" + id,
        // headers: {
        //   ...data.getHeaders()
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log("[Editor] odpoveď pre", id, response.data);

          if (response.data.status == "Request succesfull") {
            var zapisStahovanie = {};
            zapisStahovanie.doby = response.data.data.pocet_dob;
            zapisStahovanie.riadky = response.data.data.pocet_riadkov;

            zapisStahovanie.id = response.data.data.id;

            zapisStahovanie.nazov = response.data.data.nazov;
            zapisStahovanie.stupnica = response.data.data.stupnica;
            zapisStahovanie.autor = response.data.data.autor;
            zapisStahovanie.text = response.data.data.text_piesne;
            zapisStahovanie.pata = response.data.data.pata;

            zapisStahovanie.pocet_casti = response.data.data.pocet_casti;

            zapisStahovanie.dataNoty = JSON.parse(response.data.data.data);

            this.zapisy.push(zapisStahovanie);
            console.log("this.zapisy :>> ", this.zapisy);
            this.$emit("loading-changed", false); // Pošle event do parenta
          }
        })
        .catch((err) => {
          console.error("[Editor] chyba pri načítaní", id, err);
        });
    },
  },
  watch: {
    prepojeneZapisyID: {
      immediate: true, // spustí sa hneď pri prvom priradení aj potom
      handler(ids) {
        this.zapisy = []; // vyčistíme staré
        ids.forEach((id) => this.nacitajData(id));
      },
    },
    zapisy: {
      deep: true,
      handler() {
        // Spočítame celkový počet castí len z filtrovaných zápisov
        const filtered = this.getFilteredZapisy();
        const total = filtered.reduce(
          (sum, zapis) => sum + (zapis.dataNoty?.casty?.length || 0),
          0
        );
        // Pošleme event rodičovi
        this.$emit("total-casts", total);
      },
    },
    stupnica: {
      handler() {
        // Keď sa zmení stupnica, prepočítame znovu
        const filtered = this.getFilteredZapisy();
        const total = filtered.reduce(
          (sum, zapis) => sum + (zapis.dataNoty?.casty?.length || 0),
          0
        );
        this.$emit("total-casts", total);
      },
    },
  },
  props: {
    nacitaneNoty: {
      type: Boolean,
      default: false,
    },
    stupnica: {
      type: String, // Ak očakávaš reťazec
      required: true, // Ak je to potrebné
      default: "", // Ak chceš nastaviť predvolenú hodnotu (prázdny reťazec)
    },
    prepojeneZapisyID: {
      type: Array,
      required: true, // Ak je to potrebné
      default: () => [], // Predvolená hodnota pre pole (prázdne pole)
    },
    vlastnenyProduktData: {
      type: Object,
      required: true,
    },
    // Add sliceStart and sliceEnd props
    sliceStart: {
      type: Number,
      default: 0,
    },
    sliceEnd: {
      type: Number,
      default: null,
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/zapis.scss";

input,
select,
textarea {
  pointer-events: none;
}

.main-content {
  user-select: none;
  font-size: 0.56vw;
  // z-index: -1;
  position: relative;
  margin-bottom: 3em;
}

.hide {
  opacity: 0;
  z-index: -1;
  pointer-events: none;
}

.title {
  font-size: 2.5em;
}

@media only screen and (max-width: 1100px) {
  .main-content {
    font-size: 1.1vw;
  }
}
@media only screen and (max-width: 1000px) {
  .main-content {
    font-size: 1vw;
  }
}
@media only screen and (max-width: 750px) {
  .main-content {
    font-size: 1.5vw;
  }
}
</style>
