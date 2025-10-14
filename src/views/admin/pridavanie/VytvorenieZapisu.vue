<template>
  <section
    ref="htmlContent"
    @contextmenu.prevent="checkContextMenu($event)"
    :id="$route.name"
  >
    <form class="scroll" @submit.prevent="zapisanieZapisu">
      <transition v-if="showModal" name="modal-fade">
        <div class="modal-overlay">
          <div class="modal-content">
            <h2>Zadaj údaje</h2>
            <div>
              <!-- <label for="lines">Počet riadkov:</label>
              <input
                type="number"
                id="lines"
                v-model="zapisyData.riadky"
                required
                min="1"
                max="4"
              /><br /> -->

              <label for="beats">Počet dôb:</label>
              <input
                type="number"
                id="beats"
                v-model="zapisyData.doby"
                required
                min="3"
                max="4"
              /><br />

              <label v-if="zapisyData.doby == 3" for="lines"
                >Počet taktov v časti</label
              >
              <input
                v-if="zapisyData.doby == 3"
                type="number"
                id="lines"
                v-model="displayedPocetTaktov"
                required
                min="4"
                max="5"
              /><br />

              <a @click="submitForm()" type="button" class="submit-btn button">
                <p>Potvrdiť</p>
              </a>
            </div>
          </div>
        </div>
      </transition>

      <!-- Hlavný obsah -->
      <div v-show="!showModal" class="main-content">
        <div class="information">
          <input
            ref="nazov"
            type="text"
            name="nazov-piesne"
            :placeholder="zapisyData.nazov ? zapisyData.nazov : 'Názov'"
            class="title"
            v-model="zapisyData.nazov"
          />

          <input
            ref="stupnica"
            type="text"
            name="stupnica-piesne"
            :placeholder="
              zapisyData.stupnica ? zapisyData.stupnica : 'Stupnica'
            "
            class="stupnica"
            v-model="zapisyData.stupnica"
          />
          <input
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
            v-for="(cast, castIndex) in dataNoty.casty"
            :key="castIndex"
            :class="[
              'cast',
              {
                'cast-margin':
                  pocetCasti == 4
                    ? castIndex % 4 === 0 && castIndex >= 4
                    : false,
              },
              {
                'cast-margin2':
                  pocetCasti == 5
                    ? castIndex % 5 === 0 && castIndex >= 5
                    : false,
              },
            ]"
            @contextmenu.prevent="showContextMenuCast($event, castIndex)"
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
                          (cast.takty[taktIndex + 1].isGhost || false) ===
                            true),
                      'i-am-not-here-smile': takt.isGhost === true,
                    },
                  ]"
                  @contextmenu.prevent="showContextMenuTakt($event, takt)"
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
                    @contextmenu.prevent="showContextMenuDoba($event, doba)"
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
                              riadok.hodnota_cisla[0].cela_gulicka[0].cislo !=
                                '',
                            yellow:
                              doba.smer_vlavo &&
                              riadok.hodnota_cisla[0].cela_gulicka[0].cislo !=
                                '',
                          }"
                          @dblclick="
                            riadok.hodnota_cisla[0].cela_gulicka[0].cislo == ''
                              ? (riadok.hodnota_cisla[0].cela_gulicka[0].cislo =
                                  '0')
                              : (riadok.hodnota_cisla[0].cela_gulicka[0].cislo =
                                  '')
                          "
                        >
                          <!-- Prechádzanie cez hodnota_cisla v každom riadku -->
                          <div
                            v-for="(cisloObj, cisloIndex) in riadok
                              .hodnota_cisla[0].cela_gulicka"
                            :key="cisloIndex"
                            @contextmenu.prevent="
                              showContextMenuRiadok($event, riadok, riadokIndex)
                            "
                            @keyup.enter="addInput(riadok.hodnota_cisla[0])"
                            @keydown.shift.backspace="
                              deleteInput(riadok.hodnota_cisla[0], cisloIndex)
                            "
                            :class="getInputClass(riadok.hodnota_cisla[0])"
                          >
                            <!-- Prepojenie inputu s hodnotou cisla -->
                            <input
                              v-model="cisloObj.cislo"
                              type="text"
                              class="doba-cislo"
                              :placeholder="
                                cisloObj.cislo
                                  ? cisloObj.cislo
                                  : dotSee
                                  ? '·'
                                  : ''
                              "
                              @keydown="moveToNextInput($event, '.doba-cislo')"
                              :style="{
                                opacity:
                                  cisloObj.cislo != 0 || cisloObj.cislo == ''
                                    ? 1
                                    : 0.001,
                              }"
                            />

                            <!-- Prepojenie inputu s hodnotou indexu -->
                            <input
                              v-model="cisloObj.index"
                              type="text"
                              class="doba-index doba-index-tabs"
                              :placeholder="
                                cisloObj.index
                                  ? cisloObj.index
                                  : dotSee
                                  ? '·'
                                  : ''
                              "
                              @keydown="
                                moveToNextInput($event, '.doba-index-tabs')
                              "
                              v-if="cisloObj.index != '0'"
                            />
                          </div>
                          <div
                            v-if="riadok.drzanie > 1"
                            class="drzanie"
                            :class="[
                              Number(zapisyData.doby) == 3
                                ? 'doby-na-takt3'
                                : '',
                            ]"
                          >
                            <img
                              src="https://heligonkovaakademia.sk/data/images/generovanieZapisov/2-doby.svg"
                              v-if="riadok.drzanie == 2"
                              alt="Držanie na dve doby"
                              class="doby2"
                              :class="{
                                'doby2-koniec':
                                  dobaIndex === takt.doby.length - 1,
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
                              class="doby2-rozdelene inverz"
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
                          v-if="
                            doba.opakovanie_koniec || doba.opakovanie_zaciatok
                          "
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
                            (cast.takty[taktIndex + 1].isGhost || false) ===
                              true &&
                            dobaIndex == takt.doby.length - 1)
                        "
                      />
                    </div>

                    <!-- Ostatné inputy pre slabiku, basu, smer atď. -->
                    <!-- <div class="slabika-text">
                      <input
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
                        v-model="doba.smer"
                        type="text"
                        class="doba-smer"
                        :placeholder="doba.smer ? doba.smer : dotSee ? '·' : ''"
                        @input="checkSmer(doba)"
                        @dblclick="changeColor(doba)"
                        @keydown="
                          handleKeyDown($event, doba);
                          moveToNextInput($event, '.doba-smer');
                        "
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
                        @contextmenu.prevent="showContextMenuBass($event, doba)"
                      >
                        <input
                          v-model="doba.basa"
                          type="text"
                          class="doba-basa"
                          :placeholder="
                            doba.basa ? doba.basa : dotSee ? '·' : ''
                          "
                          :class="{
                            'small-g': doba.basa && doba.basa.includes('g'),
                          }"
                          :style="{
                            opacity: parseFloat(doba.basa) !== 0 ? 1 : 0.001,
                          }"
                          @dblclick="
                            doba.basa == ''
                              ? (doba.basa = '0')
                              : (doba.basa = '')
                          "
                          @keydown="moveToNextInput($event, '.doba-basa')"
                        />
                        <!-- Prepojenie inputu s hodnotou indexu -->
                        <input
                          v-model="doba.basa_index"
                          type="text"
                          class="doba-index basa-index"
                          :placeholder="
                            doba.basa_index
                              ? doba.basa_index
                              : dotSee
                              ? '·'
                              : ''
                          "
                          @keydown="moveToNextInput($event, '.basa-index')"
                          v-if="doba.basa_index != '0'"
                        />
                        <div
                          class="opakovanie"
                          v-if="
                            doba.opakovanie_koniec || doba.opakovanie_zaciatok
                          "
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
                              'doba3-basy-off2':
                                dobaIndex === takt.doby.length - 2,
                              'doba3-basy-off3':
                                dobaIndex === takt.doby.length - 1,
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
                              'doba4-basy-off2':
                                dobaIndex === takt.doby.length - 2,
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
                            class="doby5-basy test"
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
                            (cast.takty[taktIndex + 1].isGhost || false) ===
                              true &&
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
                      @dblclick="changeColor(doba)"
                    ></div>
                  </div>
                </div>
              </div>

              <div class="popis-cast">
                <div
                  v-for="(popis, popisIndex) in cast.popis"
                  :key="popisIndex"
                  class="popis"
                  @dblclick="
                    changeColorPopis(cast.popis_farba, popisIndex, castIndex)
                  "
                  :class="[
                    cast.popis_farba[popisIndex] == 1 ? 'slabo-siva' : '',
                    cast.popis_farba[popisIndex] == 2 ? 'stredne-siva' : '',
                    cast.popis_farba[popisIndex] == 3 ? 'silno-siva' : '',
                    cast?.popis_class?.[popisIndex] || '',
                  ]"
                >
                  <input
                    type="text"
                    v-model="cast.popis[popisIndex]"
                    :placeholder="
                      cast.popis[popisIndex]
                        ? cast.popis[popisIndex]
                        : dotSee
                        ? '·   ·   ·'
                        : ''
                    "
                    class="popis-input"
                    @keydown="moveToNextInput($event, '.popis-input')"
                  />
                </div>
              </div>
            </div>

            <!-- <div v-if="(castIndex + 1) % 4 === 0" class="number-page">
              <p>{{ Math.floor(castIndex / 4) + 1 }}.</p>
            </div> -->
          </div>
        </div>
        <div :style="{ paddingTop: paddingTop + 'px' }" class="information">
          <textarea
            name="text-piesne"
            ref="text"
            v-model="zapisyData.text"
            :placeholder="
              zapisyData.text ? zapisyData.text : dotSee ? 'Žiadny text' : ''
            "
            @input="adjustTextareaPadding"
          ></textarea>
          <input
            v-if="dotSee"
            ref="nazov"
            type="text"
            name="nazov-piesne"
            class="title"
            v-model="zapisyData.pata"
            :placeholder="
              zapisyData.pata ? zapisyData.pata : 'Heligónková Akadémia'
            "
          />
        </div>

        <!-- <div
          class="number-page numer-page-bottom"
          @click="
            pageNumberOffset++;
            updatePageNumber();
          "
        >
          {{ pageNumber }}.
        </div> -->

        <div v-if="dotSee" class="akcie-container">
          <div class="akcie">
            <div v-if="!upravovanieZapisu" class="button">
              <p @click="ulozitZapis()">Uložiť číselný zápis</p>
            </div>
            <div
              :class="{
                'not-have-permission':
                  !this.$store.state.userRoles?.roles?.includes('record_edit'),
              }"
              v-if="upravovanieZapisu"
              class="button"
            >
              <p @click="upravitZapis()">Upraviť číselný zápis</p>
            </div>
            <div
              :class="{
                'not-have-permission':
                  !this.$store.state.userRoles?.roles?.includes(
                    'record_delete'
                  ),
              }"
              v-if="upravovanieZapisu"
              class="button red-button"
              @click="vymazanieZapisu(this.$route.query.id, 0)"
            >
              <p>Skry</p>
            </div>
            <div
              :class="{
                'not-have-permission':
                  !this.$store.state.userRoles?.roles?.includes(
                    'record_delete'
                  ),
              }"
              v-if="upravovanieZapisu"
              class="button red-button"
              @click="vymazanieZapisu(this.$route.query.id, 1)"
            >
              <p>Vymaž</p>
            </div>
          </div>
          <div class="akcie">
            <div
              :style="{
                zIndex: generatingText !== 'Generovať pdf' ? -1 : 1,
              }"
              class="button"
            >
              <p
                @click="generatePDF(4)"
                :style="{
                  cursor:
                    generatingText !== 'Generovať pdf'
                      ? 'not-allowed'
                      : 'pointer',
                }"
              >
                {{ generatingText }} (4)
              </p>
            </div>
            <div
              :style="{
                zIndex: generatingText !== 'Generovať pdf' ? -1 : 1,
              }"
              class="button"
            >
              <p
                @click="generatePDF(5)"
                :style="{
                  cursor:
                    generatingText !== 'Generovať pdf'
                      ? 'not-allowed'
                      : 'pointer',
                }"
              >
                {{ generatingText }} (5)
              </p>
            </div>
          </div>
          <div class="button button-under">
            <p v-if="!strukturaVlozenie" @click="spravaStruktury">
              Zobraziť štruktúru
            </p>
            <p v-else @click="spravaStruktury">Skryť štruktúru</p>
          </div>

          <textarea
            v-if="strukturaVlozenie"
            name="struktura"
            class="struktura-textarea"
            id="struktura"
            cols="30"
            rows="10"
            v-model="strukturaVlozenieText"
          ></textarea>

          <div
            v-if="strukturaVlozenie"
            @click="kopirovanieStruktury"
            class="button"
          >
            <p>Skopírovať štruktúru</p>
          </div>
          <div
            v-if="strukturaVlozenie"
            @click="vlozenieStruktury"
            class="button"
          >
            <p>Vložiť štruktúru</p>
          </div>
          <button
            type="button"
            v-if="zapisyData.id && zapisyData.nazov"
            @click="openAddToFolderDialog"
            @keydown.enter.prevent
            class="button"
          >
            Pridať do priečinka
          </button>
          <AddToFolderDialog
            v-if="showAddToFolderModal"
            :item="addToFolderItem"
            :folders="addToFolderFolders"
            @close="showAddToFolderModal = false"
            @confirm="handleAddToFolderConfirm"
            @done="onAddToFolderDone"
          />
        </div>
      </div>
    </form>

    <!-- Vlastné kontextové menu -->
    <transition name="radial-overlay-fade">
      <div
        v-if="contextMenuVisible"
        class="radial-context-menu-backdrop"
        @mousedown="hideContextMenu"
      >
        <div
          class="radial-context-menu"
          :style="{ top: contextMenuY + 'px', left: contextMenuX + 'px' }"
        >
          <div
            class="radial-overlay"
            :style="{ width: radialSize + 'px', height: radialSize + 'px' }"
            @mousedown.stop
            @click.stop
          >
            <svg
              class="radial-menu"
              :viewBox="radialViewBox"
              role="menu"
              xmlns="http://www.w3.org/2000/svg"
            >
              <g
                v-for="(item, index) in visibleRadialItems"
                :key="item.id || index"
                class="radial-sector"
                :class="{
                  selected: radialSelection && radialSelection.id === item.id,
                }"
                role="menuitem"
                tabindex="0"
                @click.stop="handleRadialSectorClick(item)"
                @keydown.enter.prevent="handleRadialSectorClick(item)"
                @keydown.space.prevent="handleRadialSectorClick(item)"
                @mouseenter="handleRadialSectorHover(item)"
                @mouseleave="clearRadialHover"
              >
                <path :d="radialSectorPath(index)" />
                <g class="radial-icon" :transform="radialIconTransform(index)">
                  <text
                    v-if="showRadialInnerText(item)"
                    text-anchor="middle"
                    dominant-baseline="middle"
                  >
                    {{ getRadialInnerText(item) }}
                  </text>
                </g>
                <g
                  class="radial-label"
                  :transform="radialLabelTransform(index)"
                >
                  <foreignObject
                    v-if="radialItemHasIcon(item) && radialPath.length === 0"
                    x="-36"
                    y="-36"
                    width="72"
                    height="72"
                  >
                    <div
                      class="radial-label-icon"
                      xmlns="http://www.w3.org/1999/xhtml"
                      v-html="item.icon"
                    ></div>
                  </foreignObject>
                  <text v-else text-anchor="middle" dominant-baseline="middle">
                    {{ getRadialItemLabel(item) }}
                  </text>
                </g>
              </g>

              <g
                class="radial-center"
                role="button"
                tabindex="0"
                @click.stop="handleRadialCenterClick"
                @keydown.enter.prevent="handleRadialCenterClick"
                @keydown.space.prevent="handleRadialCenterClick"
              >
                <circle
                  :cx="radialCenter"
                  :cy="radialCenter"
                  :r="radialInnerRadius - 15"
                />
                <text
                  :x="radialCenter"
                  :y="radialCenter"
                  text-anchor="middle"
                  dominant-baseline="middle"
                >
                  {{ radialCenterLabel }}
                </text>
              </g>
            </svg>
          </div>
        </div>
      </div>
    </transition>
  </section>
</template>

<script>
import AddToFolderDialog from "@/components/Zapisy/AddToFolderDialog.vue";
import axios from "axios";

const TWO_PI = Math.PI * 2;

function polarToCartesian(cx, cy, radius, angle) {
  return {
    x: +(cx + radius * Math.cos(angle)).toFixed(3),
    y: +(cy + radius * Math.sin(angle)).toFixed(3),
  };
}

function describeRingSegment(
  cx,
  cy,
  innerRadius,
  outerRadius,
  startAngle,
  endAngle
) {
  const outerStart = polarToCartesian(cx, cy, outerRadius, startAngle);
  const outerEnd = polarToCartesian(cx, cy, outerRadius, endAngle);
  const innerEnd = polarToCartesian(cx, cy, innerRadius, endAngle);
  const innerStart = polarToCartesian(cx, cy, innerRadius, startAngle);
  const largeArcFlag = endAngle - startAngle > Math.PI ? 1 : 0;

  return [
    `M ${outerStart.x} ${outerStart.y}`,
    `A ${outerRadius} ${outerRadius} 0 ${largeArcFlag} 1 ${outerEnd.x} ${outerEnd.y}`,
    `L ${innerEnd.x} ${innerEnd.y}`,
    `A ${innerRadius} ${innerRadius} 0 ${largeArcFlag} 0 ${innerStart.x} ${innerStart.y}`,
    "Z",
  ].join(" ");
}

export default {
  components: { AddToFolderDialog },
  mounted() {
    window.scrollTo(0, 0);

    const fontAwesomeId = "font-awesome-cdn";
    if (!document.getElementById(fontAwesomeId)) {
      const link = document.createElement("link");
      link.id = fontAwesomeId;
      link.rel = "stylesheet";
      link.href =
        "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css";
      link.integrity =
        "sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==";
      link.crossOrigin = "anonymous";
      link.referrerPolicy = "no-referrer";
      document.head.appendChild(link);
    }

    if (typeof this.$route.query.id !== "undefined") {
      this.upravovanieZapisu = true;

      this.nacitajDataZapisu(this.$route.query.id);
    }

    let textarea = document.querySelector("textarea");

    // Nastav udalosť pre vstup (písanie alebo vloženie textu)
    textarea.addEventListener("input", () => {
      this.autoResize();
    });

    // Pridaj listener na resize okna
    window.addEventListener("resize", () => {
      this.autoResize(); // Znova uprav výšku pri zmene veľkosti okna
    });

    // Pridanie event listenera na klávesnicu
    window.addEventListener("keydown", this.handleKeyboardShortcuts);

    // Prvotné nastavenie výšky na základe počiatočného obsahu

    if (this.upravovanieZapisu) {
      this.showModal = false;
      setTimeout(() => {
        this.nacitajVysky();
      }, 500);
    } else {
      this.addPart();
    }
  },
  beforeUnmount() {
    // Odstránenie event listenera pri zničení komponentu
    window.removeEventListener("keydown", this.handleKeyboardShortcuts);
  },
  data() {
    return {
      zapisyData: {
        riadky: 2,
        doby: 4,
      },
      // Celkový počet dôb, ktorý chceme rozdeliť
      upravovanieZapisu: typeof this.$route.query.id !== "undefined",
      showModal: true,
      contextMenuVisible: false,
      contextMenuX: 0,
      contextMenuY: 0,
      radialPath: [],
      radialSelection: null,
      radialHoveredItem: null,
      radialSize: 420,
      radialInnerRadius: 95,
      radialOuterRadius: 190,
      selectedRiadok: null,
      selectedRiadokIndex: 0,
      selectedBass: null,
      selectedCast: null,
      selectedDoba: null,
      selectedTakt: null,

      dotSee: true,
      pageNumber: 1,
      pageNumberOffset: 0,
      a4Height: 0,
      countdownInterval: null,
      generatingText: "Generovať pdf",
      pocetCasti: 4,

      dataNoty: {
        pocetTaktov: 15,
        casty: [],
      },

      strukturaVlozenie: false,
      strukturaVlozenieText: "",
      folders: [],
      showAddToFolderModal: false,
      addToFolderItem: null,
      addToFolderFolders: [],
    };
  },
  methods: {
    async openAddToFolderDialog() {
      const allFolderNames = await this.fetchFolders(); // napr. ["Polka", "Valčík", ...]
      const id = this.zapisyData.id;

      // Pre každý priečinok zisťujeme, či obsahuje danú pesničku
      const folderStates = await Promise.all(
        allFolderNames.map(async (name) => {
          try {
            const res = await axios.get(
              this.$store.state.api + "/folder/list.php",
              {
                params: { name },
              }
            );

            const items = Array.isArray(res.data?.data) ? res.data.data : [];
            const isInFolder = items.includes(String(id)); // ✅ opravené porovnanie

            return { name, checked: isInFolder };
          } catch (e) {
            return { name, checked: false };
          }
        })
      );

      this.addToFolderItem = {
        id,
        nazov: this.zapisyData.nazov,
      };

      this.addToFolderFolders = folderStates;
      this.showAddToFolderModal = true;
    },

    async fetchFolders() {
      try {
        const res = await axios.get(this.$store.state.api + "/folder/list.php");
        return Array.isArray(res.data.data) ? res.data.data : [];
      } catch (e) {
        return [];
      }
    },
    handleAddToFolderConfirm() {
      this.showAddToFolderModal = false;
      this.$store.state.SnackBarText = "Zápis pridaný do priečinkov!";
    },
    formatDobaLabel(count) {
      if (count === 1) {
        return "dobu";
      }
      if (count >= 2 && count <= 4) {
        return "doby";
      }
      return "dôb";
    },
    handleRadialSectorHover(item) {
      this.radialHoveredItem = item;
    },
    clearRadialHover() {
      this.radialHoveredItem = null;
    },
    handleRadialSectorClick(item) {
      this.clearRadialHover();
      if (item.items && item.items.length) {
        this.radialPath = [...this.radialPath, item];
        return;
      }

      this.radialSelection = item;
      this.triggerRadialAction(item.action);
    },
    handleRadialCenterClick() {
      this.clearRadialHover();
      if (this.radialPath.length) {
        this.radialPath = this.radialPath.slice(0, -1);
        return;
      }

      this.hideContextMenu();
    },
    getRadialItemLabel(item) {
      if (!item) {
        return "";
      }

      const candidates = [item.title, item.innerLabel];
      for (const candidate of candidates) {
        if (typeof candidate === "string" && candidate.trim()) {
          return candidate.trim();
        }
      }

      if (typeof item.icon === "string") {
        const iconTrimmed = item.icon.trim();
        if (iconTrimmed && !iconTrimmed.includes("<")) {
          return iconTrimmed;
        }
      }

      if (typeof item.id === "string" && item.id.trim()) {
        return item.id
          .trim()
          .replace(/[-_]+/g, " ")
          .replace(/\s+/g, " ")
          .replace(/\b\w/g, (char) => char.toUpperCase());
      }

      return "";
    },
    radialItemHasIcon(item) {
      return !!item && typeof item.icon === "string" && item.icon.includes("<");
    },
    showRadialInnerText(item) {
      if (!item) {
        return false;
      }

      if (typeof item.innerLabel === "string" && item.innerLabel.trim()) {
        return true;
      }

      return !(this.radialItemHasIcon(item) && this.radialPath.length === 0);
    },
    getRadialInnerText(item) {
      if (!item) {
        return "";
      }

      if (typeof item.innerLabel === "string" && item.innerLabel.trim()) {
        return item.innerLabel.trim();
      }

      if (this.radialItemHasIcon(item) && this.radialPath.length === 0) {
        return "";
      }

      return this.getRadialItemLabel(item);
    },
    radialSectorPath(index) {
      const start = -Math.PI / 2 + this.radialAngleStep * index;
      const end = start + this.radialAngleStep;
      return describeRingSegment(
        this.radialCenter,
        this.radialCenter,
        this.radialInnerRadius,
        this.radialOuterRadius,
        start,
        end
      );
    },
    radialIconTransform(index) {
      const angle =
        -Math.PI / 2 + this.radialAngleStep * index + this.radialAngleStep / 2;
      const radius = (this.radialInnerRadius + this.radialOuterRadius) / 2;
      const { x, y } = polarToCartesian(
        this.radialCenter,
        this.radialCenter,
        radius,
        angle
      );
      return `translate(${x}, ${y})`;
    },
    radialLabelTransform(index) {
      const angle =
        -Math.PI / 2 + this.radialAngleStep * index + this.radialAngleStep / 2;
      const radius = (this.radialInnerRadius + this.radialOuterRadius) / 2;
      const { x, y } = polarToCartesian(
        this.radialCenter,
        this.radialCenter,
        radius,
        angle
      );
      return `translate(${x}, ${y})`;
    },
    triggerRadialAction(action) {
      if (typeof action === "function") {
        action();
      }
      this.hideContextMenu();
    },
    handleKeyboardShortcuts(event) {
      const activeElement = document.activeElement;
      const isInputField =
        activeElement.tagName === "INPUT" ||
        activeElement.tagName === "TEXTAREA" ||
        activeElement.isContentEditable;

      // Ak je aktívny textový vstup, neblokuj natívne skratky
      if (isInputField) {
        return;
      }

      const isCopyTakt =
        (event.ctrlKey || event.metaKey) &&
        event.key.toLowerCase() === "c" &&
        !event.shiftKey;
      const isPasteTakt =
        (event.ctrlKey || event.metaKey) &&
        event.key.toLowerCase() === "v" &&
        !event.shiftKey;

      const isCopyCast =
        (event.ctrlKey || event.metaKey) &&
        event.key.toLowerCase() === "c" &&
        event.shiftKey;
      const isPasteCast =
        (event.ctrlKey || event.metaKey) &&
        event.key.toLowerCase() === "v" &&
        event.shiftKey;

      if (isCopyTakt) {
        event.preventDefault();
        this.copyTakt();
      }

      if (isPasteTakt) {
        event.preventDefault();
        this.pasteTakt();
      }

      if (isCopyCast) {
        event.preventDefault();
        this.copyCast();
      }

      if (isPasteCast) {
        event.preventDefault();
        this.pasteCast();
      }
    },
    vlozenieStruktury() {
      try {
        // Pri každej zmene textu aktualizuj objekt
        this.dataNoty = JSON.parse(this.strukturaVlozenieText);
        this.$store.state.SnackBarText = "Štruktúra bola úspešne vložená";
      } catch (e) {
        this.$store.state.SnackBarText = "Neplatný formát štruktúry";
      }
      this.spravaStruktury();
    },
    kopirovanieStruktury() {
      this.generujStrukturu();
      // Použitie Clipboard API
      navigator.clipboard
        .writeText(this.strukturaVlozenieText)
        .then(() => {
          this.$store.state.SnackBarText =
            "Štruktúra bola skopirovaná do schránky";
        })
        .catch(() => {
          this.$store.state.SnackBarText = "Nepodarilo sa skopírovať štruktúru";
        });
    },
    spravaStruktury() {
      if (this.strukturaVlozenie == true) {
        this.strukturaVlozenie = false;
      } else {
        this.strukturaVlozenie = true;
      }

      this.generujStrukturu();
    },
    generujStrukturu() {
      this.strukturaVlozenieText = JSON.stringify(this.dataNoty, null);
    },
    changeValue(cislo) {
      console.log("changeValue :>> ", cislo);
      if (cislo == "") {
        cislo = "0";
      } else if (cislo == "0") {
        cislo = "";
      }
    },
    nacitajVysky() {
      setTimeout(() => {
        this.autoResize();
        this.adjustTextareaPadding();
      }, 300);
    },
    autoResize() {
      let textarea = document.querySelector("textarea");

      textarea.style.height = "auto"; // Reset height na 'auto', aby sa správne vypočítala nová výška
      textarea.style.height = textarea.scrollHeight + 10 + "px"; // Nastav výšku podľa scrollHeight
    },

    updatePageNumber() {
      let dlzka = this.dataNoty.casty.length;
      this.pageNumber =
        Math.ceil(dlzka / this.pocetCasti) + this.pageNumberOffset;
    },
    updateAllRiadky() {
      this.dataNoty.casty.forEach((cast) => {
        cast.takty.forEach((takt) => {
          takt.doby.forEach((doba) => {
            doba.riadky = this.createRiadky(this.zapisyData.riadky); // Aktualizuje počet riadkov
          });
        });
      });
    },
    adjustTextareaPadding() {
      var value = 1123 - 325; //251
      var extraPadding = 0;
      this.a4Height = value;
      const textarea = this.$refs.text;
      const textareaHeight = textarea.scrollHeight * 1.2;

      // Základný padding-top na zarovnanie na spodok A4
      let basePadding = this.a4Height - textareaHeight;

      // Zvyšné časti po delení počtu častí 4 (počítame z `generatedPartsCount`)
      const remainingParts = this.dataNoty.casty.length % this.pocetCasti;

      if (this.dataNoty.casty.length % this.pocetCasti == 0) {
        extraPadding = 160; //160
      }

      // Extra padding podľa počtu zvyšných častí
      var castPadding = remainingParts * 235; //230

      if (remainingParts == 1) {
        castPadding = 330; //330
      } else if (remainingParts == 2) {
        castPadding = 213 * 2;
      } else if (remainingParts == 3) {
        castPadding = 227 * 3;
      }

      // Nastaví výsledný padding-top
      this.paddingTop = basePadding - castPadding + extraPadding;

      if (this.paddingTop < 25) {
        this.paddingTop = this.paddingTop + value;
      }
    },
    pridajVsetkyDoby() {
      this.dataNoty.casty.forEach((cast) => {
        // Prispôsobí počet taktov v každom `cast`
        this.adjustTaktyCount(cast.takty, this.zapisyData.doby);

        // Prispôsobí počet `doby` v rámci každého taktu
        cast.takty.forEach((takt) => {
          this.adjustDobyCount(takt.doby, this.zapisyData.doby);
        });
      });
    },
    adjustTaktyCount(taktyArray, targetCount) {
      this.vypocitajPocetTaktov();
      // Vypočíta požadovaný počet taktov na základe celkového počtu dôb
      const targetTaktyCount = Math.floor(
        this.dataNoty.pocetTaktov / targetCount
      );

      // Pridá nové takty, ak ich je menej ako požadovaný počet
      while (taktyArray.length < targetTaktyCount) {
        taktyArray.push({ doby: [], isGhost: false });
      }

      // Odstráni nadbytočné takty, ak ich je viac ako požadovaný počet
      while (taktyArray.length > targetTaktyCount) {
        taktyArray.pop();
      }
    },
    vypocitajPocetTaktov() {
      if (this.zapisyData.doby == 4) {
        this.dataNoty.pocetTaktov = 16;
      } else if (this.zapisyData.doby == 2) {
        this.dataNoty.pocetTaktov = 14;
      } else if (this.zapisyData.doby == 5) {
        this.dataNoty.pocetTaktov = 15;
      } else if (this.zapisyData.doby == 6) {
        this.dataNoty.pocetTaktov = 18;
      }
    },
    adjustDobyCount(dobyArray, targetCount) {
      // Pridá nové `doby`, ak ich je menej ako požadovaný počet na takt
      while (dobyArray.length < targetCount) {
        dobyArray.push(this.createDoba());
      }

      // Odstráni nadbytočné `doby`, ak ich je viac ako požadovaný počet na takt
      while (dobyArray.length > targetCount) {
        dobyArray.pop();
      }
    },
    createDoba() {
      return {
        riadky: this.createRiadky(this.zapisyData.riadky),
        opakovanie_zaciatok: false, //značí opakovanie doby na začiatku
        opakovanie_koniec: false, //značí opakovanie doby na konci
        text: "", //jedna slabika textu na dobe
        basa: "", //jeden basový zápis
        basa_drzanie: "", //jeden basový zápis
        basa_index: "0",
        smer_pravo: false, //smer mechu zatváranie
        smer_vlavo: false, //smer mechu otváranie
        farba: 0, //0 = biela, 1= slabo sivá, 2= silno sivá
        isGhost: false,
      };
    },
    createRiadky(count) {
      // Vytvára pole `riadky` s počtom objektov `count`
      return Array.from({ length: count }, () => ({
        drzanie: 1,
        hodnota_cisla: [
          {
            cela_gulicka: [
              {
                cislo: "",
                index: "0",
              },
            ],
            poddoba: [],
          },
        ],
      }));
    },
    nacitajDataZapisu(id) {
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
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request succesfull") {
            this.zapisyData.doby = response.data.data.pocet_dob;
            this.zapisyData.riadky = response.data.data.pocet_riadkov;

            this.zapisyData.id = response.data.data.id;

            this.zapisyData.nazov = response.data.data.nazov;
            this.zapisyData.stupnica = response.data.data.stupnica;
            this.zapisyData.autor = response.data.data.autor;
            this.zapisyData.text = response.data.data.text_piesne;
            this.zapisyData.pata = response.data.data.pata;

            this.zapisyData.pocet_casti = response.data.data.pocet_casti;
            this.zapisyData.productID = response.data.data.product_id;

            console.log("this.dataNoty :>> ", this.dataNoty);
            this.dataNoty = JSON.parse(response.data.data.data);
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    generatePDF(casti) {
      // if (typeof this.$route.query.id === "undefined") {
      //   this.$store.SnackBarText =
      //     "Pre generovanie zápisu je potrebné ho uložiť";
      //   return;
      // }
      this.pocetCasti = casti;
      this.dotSet(false);
      this.nacitajVysky();
      setTimeout(async () => {
        // Zmeň setTimeout na asynchrónnu funkciu
        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();

        var htmlContent = this.$refs.htmlContent.innerHTML;

        const url =
          "https://heligonkovaakademia.sk/data/styles/generovanieZapisov.css";

        // Funkcia na načítanie CSS ako reťazec
        async function loadCSS(url) {
          const response = await fetch(url);
          if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
          }
          return await response.text();
        }

        try {
          const styles = await loadCSS(url); // Počkaj na načítanie CSS

          // Vytvor HTML s vnorenými štýlmi a Vue dátami
          const htmlString = `
          
        
      <!DOCTYPE html>
      <html lang="en">
      <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Document</title>
          <style>
                ${styles}
          </style>
      </head>
      <body>
    
        <div>
          <section>${htmlContent}</section>
        </div>
        </body>
      </html>
      `;

          // Pridaj HTML do FormData
          data.append("input", htmlString);
          data.append("id", Number(this.zapisyData.id));

          console.log("htmlString :>> ", htmlString);

          let config = {
            method: "post",
            maxBodyLength: Infinity,
            url: this.$store.state.api + "/noty/html2pdf/html_input2pdf.php",
            data: data,
          };

          axios
            .request(config)
            .then((response) => {
              console.log(JSON.stringify(response.data));
            })
            .catch((error) => {
              console.log(error);
            });
        } catch (error) {
          console.error("Error loading CSS:", error);
        }
      }, 300); // Tieto časy môžeš upraviť podľa potreby

      setTimeout(() => {
        this.dotSet(true);
        this.startGenerating();
      }, 600);
    },
    generateSequence(length) {
      const sequence = [5, 3]; // Prvý prvok je pevne nastavený na 30 sekúnd
      for (let i = 2; i < length; i++) {
        const previousValue = sequence[i - 1];
        sequence.push(previousValue * 1.02); // Každý ďalší prvok sa zväčší o 20%
      }
      return sequence;
    },
    startGenerating() {
      // Vygeneruj sekvenciu a priraď ju do this.sequence
      this.sequence = this.generateSequence(this.dataNoty.casty.length);

      // Spočítaj sumu všetkých prvkov v sequence, zaokrúhli na celé číslo a nastav na remainingTime
      let remainingTime = Math.round(
        this.sequence.reduce((sum, value) => sum + value, 0)
      );

      let timeoutTime = remainingTime;

      // Nastaví text na začiatku
      this.generatingText = `Zostáva ${remainingTime} sekúnd`;

      // Nastaví interval pre každú sekundu
      this.countdownInterval = setInterval(() => {
        remainingTime--;

        if (remainingTime > 1) {
          this.generatingText = `Zostáva ${remainingTime} sekúnd`;
        } else if (remainingTime === 1) {
          this.generatingText = "Zostáva 1 sekunda";
        } else {
          this.generatingText = "Vygenerované";
          clearInterval(this.countdownInterval); // Zastaví odpočítavanie
        }
      }, 1000); // Aktualizuje každú sekundu

      setTimeout(() => {
        this.generatingText = "Generovať pdf";
        setTimeout(() => {
          window.open(
            "https://heligonkovaakademia.sk/api/noty/load_pdf.php?id=" +
              Number(this.zapisyData.id),
            "_blank"
          ); // "_blank" otvorí URL na novej karte alebo v novom okne
        }, 300);
      }, (timeoutTime + 1) * 1000);
    },

    upravitZapis() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      // Pridaj dáta do FormData
      data.append("id", Number(this.zapisyData.id));
      data.append("data", JSON.stringify(this.dataNoty));
      data.append("nazov", this.zapisyData.nazov || "Číselný zápis");
      data.append("autor", this.zapisyData.autor || "Neznámy autor");
      data.append("stupnica", this.zapisyData.stupnica || "C dur");
      data.append("pocet_casti", this.dataNoty.casty.length);
      data.append("pocet_dob", this.zapisyData.doby);
      data.append("pocet_riadkov", this.zapisyData.riadky);
      data.append("text_piesne", this.zapisyData.text || "");
      data.append("pata", this.zapisyData.pata || "Heligónková akadémia");
      data.append("product_id", this.zapisyData.productID || 0);

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/update.php",
        // headers: {
        //   "Content-Type": "multipart/form-data",
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request fullfiled") {
            this.$store.state.SnackBarText =
              "Číselný zápis bol úspešne uložený";
            this.$router.go(-1);
          } else {
            this.$store.state.SnackBarText =
              "Nepodarilo sa uložiť číslený zápis";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText = "Nepodarilo sa uložiť číslený zápis";
        });
    },
    ulozitZapis() {
      // this.zapisyData.pocet_casti = this.dataNoty.casty.length;
      console.log("Dlžká častí :>> ", this.dataNoty.casty.length);
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      // Pridaj dáta do FormData
      data.append("data", JSON.stringify(this.dataNoty));
      data.append("nazov", this.zapisyData.nazov || "Číselný zápis");
      data.append("autor", this.zapisyData.autor || "Neznámy autor");
      data.append("stupnica", this.zapisyData.stupnica || "C dur");
      data.append("pocet_casti", this.dataNoty.casty.length);
      data.append("pocet_dob", this.zapisyData.doby);
      data.append("pocet_riadkov", this.zapisyData.riadky);
      data.append("text_piesne", this.zapisyData.text || "");
      data.append("pata", this.zapisyData.pata || "Heligónková akadémia");

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/create.php",
        // headers: {
        //   "Content-Type": "multipart/form-data",
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request fullfiled") {
            this.$store.state.SnackBarText =
              "Číselný zápis bol úspešne vytvorený";
            this.$router.go(-1);
          } else {
            this.$store.state.SnackBarText =
              "Nepodarilo sa vytvoriť číslený zápis";
          }
        })
        .catch((error) => {
          console.log(error);
          this.$store.state.SnackBarText =
            "Nepodarilo sa vytvoriť číslený zápis";
        });
    },
    vymazanieZapisu(id, parameterArg) {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      let parameter = "";

      if (parameterArg == 1) {
        parameter = "&permanent=true";
      }

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/delete.php?id=" + id + parameter,
        // headers: {
        //   ...data.getHeaders()
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request fullfiled") {
            this.$store.state.SnackBarText = "Vymazanie zápisu bolo úspešné";

            this.$router.go(-1);
          } else {
            this.$store.state.SnackBarText = "Nepodarilo sa vymazať zápis";
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    submitForm() {
      this.showModal = false;
      this.pridajVsetkyDoby();
      this.nacitajVysky();
    },
    addInput(doba) {
      // Ak je počet riadkov menší ako 4, pridaj nový
      if (doba.cela_gulicka.length < 4) {
        if (doba.cela_gulicka[0].index == "0") {
          doba.cela_gulicka.push({
            cislo: "", // Prázdna hodnota pre nové číslo
            index: "0", // Predvolená hodnota pre index (môžeš zmeniť podľa potreby)
          });
        } else {
          doba.cela_gulicka.push({
            cislo: "", // Prázdna hodnota pre nové číslo
            index: "", // Predvolená hodnota pre index (môžeš zmeniť podľa potreby)
          });
        }
      } else {
        this.$store.state.SnackBarText =
          "Nieje možné pridať viacej ako 4 riadky";
      }
      console.log(doba);
    },
    deleteInput(doba, riadokIndex) {
      // Skontroluj, či je index platný a či existujú riadky v cela_gulicka
      if (doba.cela_gulicka.length > 1) {
        // Odstráň konkrétny riadok podľa indexu
        doba.cela_gulicka.splice(riadokIndex, 1);
      } else {
        // Ak zostal len jeden riadok, zobraz upozornenie
        this.$store.state.SnackBarText =
          "Nie je možné odstrániť posledný riadok.";
      }
      console.log(doba);
    },
    getInputClass(doba) {
      const inputCount = doba.cela_gulicka.length;
      if (inputCount === 1) return "jedna-cisla";
      if (inputCount === 2) return "dva-cisla";
      if (inputCount === 3) return "tri-cisla";
      if (inputCount === 4) return "styri-cisla";
      return "";
    },
    checkContextMenu(event) {
      // Skontroluje, či element nemá class 'riadok'
      if (!event.target.closest(".riadok")) {
        this.showContextMenu(event); // Ak element nemá class 'riadok', zobrazí kontextové menu
      }
    },
    // Zobrazí menu na mieste kliknutia
    showContextMenu(event) {
      this.selectedRiadok = null;
      this.selectedRiadokIndex = 0;

      this.contextMenuX = event.pageX;
      this.contextMenuY = event.pageY;
      this.radialPath = [];
      this.radialSelection = null;
      this.clearRadialHover();
      this.contextMenuVisible = true;

      // Skryť menu, keď klikneš mimo neho
      document.addEventListener("click", this.hideContextMenu);
    },
    showContextMenuRiadok(event, riadok, riadokIndex) {
      this.contextMenuX = event.pageX;
      this.contextMenuY = event.pageY;
      this.radialPath = [];
      this.radialSelection = null;
      this.clearRadialHover();
      this.contextMenuVisible = true;
      if (riadok != undefined) {
        this.selectedRiadok = riadok; // Ukladá riadok správne
        this.selectedRiadokIndex = riadokIndex;
      } else {
        this.selectedRiadok = null;
        this.selectedRiadokIndex = 0;
      }

      console.log(
        "idem urobiť showcontextmenu :>> ",
        this.selectedRiadok,
        riadok
      );

      // Zabezpečiť, aby kliknutie mimo kontextového menu skrylo menu iba v prípade, že sa nekoná interakcia s ním
      setTimeout(() => {
        document.addEventListener("click", this.hideContextMenu);
      }, 0);
    },
    showContextMenuBass(event, riadok) {
      this.contextMenuX = event.pageX;
      this.contextMenuY = event.pageY;
      this.radialPath = [];
      this.radialSelection = null;
      this.clearRadialHover();
      this.contextMenuVisible = true;
      if (riadok != undefined) {
        this.selectedBass = riadok;
      } else {
        this.selectedBass = null;
      }

      // Zabezpečiť, aby kliknutie mimo kontextového menu skrylo menu iba v prípade, že sa nekoná interakcia s ním
      setTimeout(() => {
        document.addEventListener("click", this.hideContextMenu);
      }, 0);
    },
    showContextMenuDoba(event, doba) {
      this.contextMenuX = event.pageX;
      this.contextMenuY = event.pageY;
      this.radialPath = [];
      this.radialSelection = null;
      this.clearRadialHover();
      this.contextMenuVisible = true;
      if (doba != undefined) {
        this.selectedDoba = doba; // Ukladá doba správne
      } else {
        this.selectedDoba = null;
      }

      console.log("idem urobiť selected doba :>> ", this.selectedDoba, doba);

      // Zabezpečiť, aby kliknutie mimo kontextového menu skrylo menu iba v prípade, že sa nekoná interakcia s ním
      setTimeout(() => {
        document.addEventListener("click", this.hideContextMenu);
      }, 0);
    },
    showContextMenuTakt(event, takt) {
      this.contextMenuX = event.pageX;
      this.contextMenuY = event.pageY;
      this.radialPath = [];
      this.radialSelection = null;
      this.clearRadialHover();
      this.contextMenuVisible = true;
      if (takt != undefined) {
        this.selectedTakt = takt; // Ukladá doba správne
      } else {
        this.selectedTakt = null;
      }

      console.log("taaaaaaaaaaaaaak :>> ", this.selectedTakt, takt);

      // Zabezpečiť, aby kliknutie mimo kontextového menu skrylo menu iba v prípade, že sa nekoná interakcia s ním
      setTimeout(() => {
        document.addEventListener("click", this.hideContextMenu);
      }, 0);
    },
    showContextMenuCast(event, cast) {
      this.contextMenuX = event.pageX;
      this.contextMenuY = event.pageY;
      this.radialPath = [];
      this.radialSelection = null;
      this.clearRadialHover();
      this.contextMenuVisible = true;
      if (cast != undefined) {
        this.selectedCast = cast;
      } else {
        this.selectedCast = null;
      }

      console.log("idem urobiť cast :>> ", cast, this.selectedCast);

      // Zabezpečiť, aby kliknutie mimo kontextového menu skrylo menu iba v prípade, že sa nekoná interakcia s ním
      setTimeout(() => {
        document.addEventListener("click", this.hideContextMenu);
      }, 0);
    },
    hideContextMenu() {
      // Ak kliknutie nebolo na element s triedou "radial-context-menu", skryje kontextové menu
      this.contextMenuVisible = false;
      this.selectedRiadok = null;
      this.selectedRiadokIndex = 0;
      this.selectedBass = null;
      this.selectedCast = null;
      this.selectedDoba = null;
      this.selectedPopis = null;
      this.selectedTakt = null;
      this.radialPath = [];
      this.radialSelection = null;
      this.clearRadialHover();

      // Odstránenie event listeneru, aby sa nepridával znova a znova
      document.removeEventListener("click", this.hideContextMenu);
    },
    dotSet(value) {
      this.dotSee = value;
    },
    handleKeyDown(event, doba) {
      if (event.key === "ArrowRight") {
        event.preventDefault(); // Zabraňte predvolenému správaniu
        doba.smer = "";
        const cursorPosition = event.target.selectionStart; // Získajte pozíciu kurzora

        // Uistite sa, že 'doba.smer' je definovaný a nie undefined
        if (typeof doba.smer === "string") {
          // Pridajte '>' na pozíciu kurzora
          doba.smer =
            doba.smer.slice(0, cursorPosition) +
            ">" +
            doba.smer.slice(cursorPosition);

          // Nastavte kurzor za pridaný znak
          this.$nextTick(() => {
            event.target.selectionStart = event.target.selectionEnd =
              cursorPosition + 1;
          });

          this.checkSmer(doba);
        }
      } else if (event.key === "ArrowLeft") {
        event.preventDefault(); // Zabraňte predvolenému správaniu
        doba.smer = "";
        const cursorPosition = event.target.selectionStart; // Získajte pozíciu kurzora

        // Uistite sa, že 'doba.smer' je definovaný a nie undefined
        if (typeof doba.smer === "string") {
          // Pridajte '<' na pozíciu kurzora
          doba.smer =
            doba.smer.slice(0, cursorPosition) +
            "<" +
            doba.smer.slice(cursorPosition);

          // Nastavte kurzor za pridaný znak
          this.$nextTick(() => {
            event.target.selectionStart = event.target.selectionEnd =
              cursorPosition + 1;
          });
        }

        this.checkSmer(doba);
      }
    },

    addHoldingBass(drzanieValue) {
      if (this.selectedBass) {
        this.selectedBass.basa_drzanie = drzanieValue; // Priraď držanie pre basu
        let sideLeft = false;

        if (drzanieValue < 100) {
          drzanieValue = Math.abs(drzanieValue) / (drzanieValue > 10 ? 20 : 1);
        }

        if (drzanieValue >= 100) {
          drzanieValue = drzanieValue / 200;
          sideLeft = true;
        }

        this.contextMenuVisible = false; // Skryť kontextové menu

        for (let cast of this.dataNoty.casty) {
          if (Array.isArray(cast.takty)) {
            let foundTakt = null;
            let foundDoba = null;

            for (let takt of cast.takty) {
              for (let doba of takt.doby) {
                if (doba === this.selectedBass) {
                  foundTakt = takt;
                  foundDoba = doba;
                  break;
                }
              }
              if (foundTakt && foundDoba) break;
            }

            if (foundTakt && foundDoba) {
              let currentDobaIndex = foundTakt.doby.indexOf(foundDoba);

              if (!sideLeft) {
                let dobyToNull = drzanieValue - 1;
                let currentCastIndex = cast.takty.indexOf(foundTakt);
                let dobaIndex = currentDobaIndex + 1;

                while (dobyToNull > 0 && currentCastIndex < cast.takty.length) {
                  const takt = cast.takty[currentCastIndex];

                  while (dobaIndex < takt.doby.length && dobyToNull > 0) {
                    takt.doby[dobaIndex].basa = "0";
                    dobaIndex++;
                    dobyToNull--;
                  }

                  // Move to next takt and reset doba index
                  currentCastIndex++;
                  dobaIndex = 0;
                }
              } else if (sideLeft) {
                let prevTaktDoby = drzanieValue - 1;
                for (
                  let taktIndex = cast.takty.indexOf(foundTakt);
                  taktIndex >= 0 && prevTaktDoby > 0;
                  taktIndex--
                ) {
                  let takt = cast.takty[taktIndex];
                  for (
                    let i = takt.doby.length - 1;
                    i >= 0 && prevTaktDoby > 0;
                    i--
                  ) {
                    if (takt.doby[i]) {
                      takt.doby[i].basa = "0";
                      prevTaktDoby--;
                    }
                  }
                }
              }
            }
          } else {
            console.error("cast.takty nie je platné pole.");
          }
        }

        this.selectedBass = null; // Vymaž referenciu na vybranú basu
      }
    },
    addHolding(drzanieValue) {
      if (this.selectedRiadok) {
        this.selectedRiadok.drzanie = drzanieValue; // Priraď držanie
        let sideLeft = false;

        if (drzanieValue < 190) {
          drzanieValue = Math.abs(drzanieValue) / (drzanieValue > 12 ? 20 : 1);
        }

        if (drzanieValue >= 190) {
          drzanieValue = drzanieValue / 200;
          sideLeft = true;
        }

        this.contextMenuVisible = false; // Skryť kontextové menu

        for (let cast of this.dataNoty.casty) {
          if (Array.isArray(cast.takty)) {
            let foundTakt = null;
            let foundDoba = null;

            for (let takt of cast.takty) {
              for (let doba of takt.doby) {
                if (doba.riadky.includes(this.selectedRiadok)) {
                  foundTakt = takt;
                  foundDoba = doba;
                  break;
                }
              }
              if (foundTakt && foundDoba) break;
            }

            if (foundTakt && foundDoba) {
              let currentDobaIndex = foundTakt.doby.indexOf(foundDoba);

              if (!sideLeft) {
                this.moveRight(
                  cast,
                  foundTakt,
                  foundDoba,
                  currentDobaIndex,
                  drzanieValue
                );
              } else if (sideLeft) {
                this.moveLeft(
                  cast,
                  foundTakt,
                  foundDoba,
                  currentDobaIndex,
                  Math.abs(drzanieValue)
                );
              }
            }
          } else {
            console.error("cast.takty nie je platné pole.");
          }
        }

        this.selectedRiadok = null; // Vymaž referenciu na vybraný riadok
        this.selectedRiadokIndex = 0;
      }
    },

    moveRight(cast, foundTakt, foundDoba, currentDobaIndex, drzanieValue) {
      let pocetTaktovToSet = currentDobaIndex + drzanieValue;
      let nextTaktDoby = drzanieValue - 1;
      let nextTaktDoby2 = nextTaktDoby;

      for (let i = currentDobaIndex + 1; i < pocetTaktovToSet; i++) {
        if (foundTakt.doby[i]) {
          const prvyRiadok = foundTakt.doby[i].riadky[this.selectedRiadokIndex];
          if (prvyRiadok) {
            prvyRiadok.hodnota_cisla.forEach((hodnota) => {
              hodnota.cela_gulicka.forEach((gulicka) => {
                gulicka.cislo = "0";
              });
            });
            nextTaktDoby--;
            nextTaktDoby2--;
          }
        }
        if (i >= foundTakt.doby.length) {
          const nextTaktIndex = cast.takty.indexOf(foundTakt) + 1;
          if (cast.takty[nextTaktIndex]) {
            const nextTakt = cast.takty[nextTaktIndex];
            for (let i = 0; i < nextTaktDoby; i++) {
              const prvyRiadokNextTakt =
                nextTakt.doby[i]?.riadky[this.selectedRiadokIndex];
              if (prvyRiadokNextTakt) {
                prvyRiadokNextTakt.hodnota_cisla.forEach((hodnota) => {
                  hodnota.cela_gulicka.forEach((gulicka) => {
                    gulicka.cislo = "0";
                  });
                });
                nextTaktDoby2--;
              }
            }
          }
        }
        if (i >= foundTakt.doby.length) {
          const nextTaktIndex2 = cast.takty.indexOf(foundTakt) + 2;
          if (cast.takty[nextTaktIndex2]) {
            const nextTakt = cast.takty[nextTaktIndex2];
            for (let i = 0; i < nextTaktDoby2; i++) {
              const prvyRiadokNextTakt2 =
                nextTakt.doby[i]?.riadky[this.selectedRiadokIndex];
              if (prvyRiadokNextTakt2) {
                prvyRiadokNextTakt2.hodnota_cisla.forEach((hodnota) => {
                  hodnota.cela_gulicka.forEach((gulicka) => {
                    gulicka.cislo = "0";
                  });
                });
              }
            }
          }
        }
      }
    },

    moveLeft(cast, foundTakt, foundDoba, currentDobaIndex, drzanieValue) {
      let prevTaktDoby = drzanieValue - 1; // Počet dôb, ktoré sa majú nastaviť na 0

      for (
        let taktIndex = cast.takty.indexOf(foundTakt);
        taktIndex >= 0 && prevTaktDoby > 0;
        taktIndex--
      ) {
        const takt = cast.takty[taktIndex];
        for (
          let i =
            taktIndex === cast.takty.indexOf(foundTakt)
              ? currentDobaIndex - 1
              : takt.doby.length - 1;
          i >= 0 && prevTaktDoby > 0;
          i--
        ) {
          if (takt.doby[i]) {
            const prvyRiadok = takt.doby[i].riadky[this.selectedRiadokIndex];
            if (prvyRiadok) {
              prvyRiadok.hodnota_cisla.forEach((hodnota) => {
                hodnota.cela_gulicka.forEach((gulicka) => {
                  gulicka.cislo = "0"; // Nastav cislo na 0
                });
              });
              prevTaktDoby--; // Zníž počet zostávajúcich dôb na nastavenie
            }
          }
        }
      }
    },
    addPart() {
      this.vypocitajPocetTaktov();
      // Vypočíta počet taktov na základe `pocetTaktov` a `targetCount`
      const targetTaktyCount = Math.floor(
        this.dataNoty.pocetTaktov / this.zapisyData.doby
      );

      // Vytvorí novú časť s prázdnym poľom `takty`
      const newCast = {
        takty: [],
        popis: ["", ""], // Text medzi slohami, opakovaniami
        popis_farba: [0, 0],
        popis_class: [0, 0],
      };

      // Pridá takty do novej časti podľa `targetTaktyCount`
      for (let i = 0; i < targetTaktyCount; i++) {
        const takt = {
          doby: [],
          isGhost: false,
        };

        // Pridá `doby` do každého taktu na základe `targetCount`
        for (let j = 0; j < this.zapisyData.doby; j++) {
          takt.doby.push(this.createDoba(this.zapisyData.doby));
        }

        // Pridá takt do takty
        newCast.takty.push(takt);
      }

      // Pridá novú časť do `casty`
      this.dataNoty.casty.push(newCast);

      // Skryje kontextové menu a upraví padding pre textarea (ak sú tieto funkcie potrebné)
      this.hideContextMenu();
      this.adjustTextareaPadding();
    },
    addOpakovanie(poloha) {
      console.log("poloha :>> ", poloha);
      if (poloha == "zaciatok") {
        this.selectedDoba.opakovanie_zaciatok = true;
        this.selectedDoba.opakovanie_koniec = false;
      } else if (poloha == "koniec") {
        this.selectedDoba.opakovanie_zaciatok = false;
        this.selectedDoba.opakovanie_koniec = true;
      } else {
        this.selectedDoba.opakovanie_zaciatok = false;
        this.selectedDoba.opakovanie_koniec = false;
      }
    },
    addPopis(pocet, pocet2, pocet3, classPopis, smer) {
      // pocet3 je pre špecialné nepravidelné prípady napríklad na umiestnenie taktov 1 2 a 1
      // Inicializuje `popis`, `popis_farba`, a `popis_class` pre vybranú časť
      this.dataNoty.casty[this.selectedCast].popis = [];
      this.dataNoty.casty[this.selectedCast].popis_farba = [];
      this.dataNoty.casty[this.selectedCast].popis_class = [];

      if (smer) {
        // Pridá prvky bez špeciálnej triedy pre `pocet`
        for (let i = 0; i < pocet; i++) {
          this.dataNoty.casty[this.selectedCast].popis.push(""); // Pridá prázdny text
          this.dataNoty.casty[this.selectedCast].popis_farba.push(0); // Farba bez špeciálnej triedy
          this.dataNoty.casty[this.selectedCast].popis_class.push(""); // Bez triedy pre prvý cyklus
        }

        // Pridá prvky so špeciálnou triedou pre `pocet2`
        for (let i = 0; i < pocet2; i++) {
          this.dataNoty.casty[this.selectedCast].popis.push(""); // Pridá prázdny text
          this.dataNoty.casty[this.selectedCast].popis_farba.push(0); // Farba pre tento cyklus
          this.dataNoty.casty[this.selectedCast].popis_class.push(classPopis); // Trieda `popis-2` pre druhý cyklus
        }

        // Pridá prvky so špeciálnou triedou pre `pocet2`
        for (let i = 0; i < pocet3; i++) {
          this.dataNoty.casty[this.selectedCast].popis.push(""); // Pridá prázdny text
          this.dataNoty.casty[this.selectedCast].popis_farba.push(0); // Farba pre tento cyklus
          this.dataNoty.casty[this.selectedCast].popis_class.push(); // Trieda `popis-2` pre druhý cyklus
        }
      } else {
        // Pridá prvky so špeciálnou triedou pre `pocet2`
        for (let i = 0; i < pocet2; i++) {
          this.dataNoty.casty[this.selectedCast].popis.push(""); // Pridá prázdny text
          this.dataNoty.casty[this.selectedCast].popis_farba.push(0); // Farba pre tento cyklus
          this.dataNoty.casty[this.selectedCast].popis_class.push(classPopis); // Trieda `popis-2` pre druhý cyklus
        }
        // Pridá prvky bez špeciálnej triedy pre `pocet`
        for (let i = 0; i < pocet; i++) {
          this.dataNoty.casty[this.selectedCast].popis.push(""); // Pridá prázdny text
          this.dataNoty.casty[this.selectedCast].popis_farba.push(0); // Farba bez špeciálnej triedy
          this.dataNoty.casty[this.selectedCast].popis_class.push(""); // Bez triedy pre prvý cyklus
        }
      }
    },
    removePart() {
      this.dataNoty.casty.splice(this.selectedCast, 1);
      this.hideContextMenu();
      this.adjustTextareaPadding();
    },
    checkSmer(doba) {
      // Ak 'smer' obsahuje znak '>', nastaví sa smer doprava
      if (doba.smer.trim() === ">") {
        doba.smer_pravo = true;
        doba.smer_vlavo = false;
      }
      // Ak 'smer' obsahuje znak '<', nastaví sa smer doľava
      else if (doba.smer.trim() === "<") {
        doba.smer_pravo = false;
        doba.smer_vlavo = true;
      }
      // Ak 'smer' obsahuje iné hodnoty alebo neplatné znaky, zresetuje smer
      else {
        doba.smer_pravo = false;
        doba.smer_vlavo = false;
      }
    },
    copyTakt() {
      if (!this.selectedTakt) {
        this.$store.state.SnackBarText = "Žiadny takt na kopírovanie!";
        return;
      }

      try {
        // Konvertujeme objekt taktu na JSON
        const taktString = JSON.stringify(this.selectedTakt);

        // Skopírujeme do schránky
        navigator.clipboard
          .writeText(taktString)
          .then(() => {
            this.$store.state.SnackBarText =
              "Takt bol skopírovaný do schránky!";
          })
          .catch((err) => {
            console.error("Chyba pri kopírovaní: ", err);
            this.$store.state.SnackBarText = "Nepodarilo sa skopírovať takt!";
          });
      } catch (error) {
        console.error("Chyba pri serializácii taktu: ", error);
        this.$store.state.SnackBarText = "Chyba pri spracovaní taktu!";
      }
    },
    copyCast() {
      var selectedCast = this.selectedCast;
      if (selectedCast === null || selectedCast === undefined) {
        this.$store.state.SnackBarText = "Žiadna časť na kopírovanie!";
        return;
      }

      try {
        const cast = this.dataNoty.casty[selectedCast]; // Získame časť podľa indexu
        if (!cast) {
          this.$store.state.SnackBarText = "Neplatný index časti!";
          return;
        }

        const castString = JSON.stringify(cast);

        navigator.clipboard.writeText(castString).then(() => {
          this.$store.state.SnackBarText = "Časť bola skopírovaná do schránky!";
        });
      } catch (error) {
        console.error("Chyba pri kopírovaní časti: ", error);
        this.$store.state.SnackBarText = "Nepodarilo sa skopírovať časť!";
      }
    },
    async pasteTakt() {
      const selectedTakt = this.selectedTakt;
      try {
        // Načítanie JSON stringu zo schránky
        const taktString = await navigator.clipboard.readText();

        // Konverzia JSON stringu na objekt
        const parsedTakt = JSON.parse(taktString);

        // Overíme, či načítané dáta sú platný takt
        if (!parsedTakt || !Array.isArray(parsedTakt.doby)) {
          this.$store.state.SnackBarText = "Neplatné dáta v schránke!";
          return;
        }

        // Nájdeme index vybraného taktu v hlavnej štruktúre
        let taktIndex = null;
        let castIndex = null;

        // Prehľadáme všetky časti a takty
        for (let i = 0; i < this.dataNoty.casty.length; i++) {
          for (let j = 0; j < this.dataNoty.casty[i].takty.length; j++) {
            console.log(
              "this.dataNoty.casty[i].takty[i], selectedTakt :>> ",
              this.dataNoty.casty[i].takty[i],
              selectedTakt
            );
            if (this.dataNoty.casty[i].takty[j] === selectedTakt) {
              castIndex = i;
              taktIndex = j;
              break;
            }
          }
          if (taktIndex !== null) break; // Ak sme našli, ukončíme cyklus
        }

        // Ak sa `selectedTakt` nenašiel, zobrazíme chybu
        if (taktIndex === null || castIndex === null) {
          this.$store.state.SnackBarText =
            "Nepodarilo sa nájsť takt v štruktúre!";
          return;
        }

        // Nahradíme vybraný takt skopírovanými dátami
        this.dataNoty.casty[castIndex].takty[taktIndex] = parsedTakt;

        this.$store.state.SnackBarText = "Takt bol úspešne nahradený!";

        // Prípadne môžeš zavolať `$forceUpdate()`, ak sa zmena ihneď nezobrazí
        this.$forceUpdate();
      } catch (error) {
        console.error("Chyba pri prilepení taktu: ", error);
        this.$store.state.SnackBarText = "Nepodarilo sa prilepiť takt!";
      }
    },
    async pasteCast() {
      var selectedCast = this.selectedCast;
      if (selectedCast === null || selectedCast === undefined) {
        this.$store.state.SnackBarText = "Žiadna časť na prilepenie!";
        return;
      }

      try {
        const castString = await navigator.clipboard.readText();
        const parsedCast = JSON.parse(castString);

        if (!parsedCast || !Array.isArray(parsedCast.takty)) {
          this.$store.state.SnackBarText = "Neplatné dáta v schránke!";
          return;
        }

        if (!this.dataNoty.casty[selectedCast]) {
          this.$store.state.SnackBarText = "Neplatný index časti!";
          return;
        }

        // Nahradíme vybranú časť
        this.dataNoty.casty[selectedCast] = parsedCast;

        this.$store.state.SnackBarText = "Časť bola úspešne nahradená!";
        this.$forceUpdate();
      } catch (error) {
        console.error("Chyba pri prilepení časti: ", error);
        this.$store.state.SnackBarText = "Nepodarilo sa prilepiť časť!";
      }
    },
    setIndex(cislo, miesto) {
      if (miesto == "takt") {
        // Predpokladám, že máte nejakú referenciu na vybraný takt
        const selectedTakt = this.selectedTakt; // Získajte aktuálne vybraný takt

        if (selectedTakt && Array.isArray(selectedTakt.doby)) {
          // Prejdite všetky doby v aktuálne vybranom takte
          selectedTakt.doby.forEach((doba) => {
            // Prejdite všetky riadky v aktuálnej dobe
            doba.riadky.forEach((riadok) => {
              // Prejdite hodnoty čísel v aktuálnom riadku
              riadok.hodnota_cisla.forEach((hodnota) => {
                // Prejdite všetky cela_gulicka a nastavte index na 0
                hodnota.cela_gulicka.forEach((gulicka) => {
                  gulicka.index = "" + cislo; // Nastavte index na 0
                });
              });
            });
            doba.basa_index = "" + cislo;
          });
        } else {
          this.$store.state.SnackBarText =
            "Nedajú sa pridať indexy do vybraného taktu";
        }
      } else {
        // Predpokladám, že všetky časti sú uložené v this.cast
        if (Array.isArray(this.dataNoty.casty)) {
          this.dataNoty.casty.forEach((cast) => {
            if (Array.isArray(cast.takty)) {
              cast.takty.forEach((takt) => {
                if (Array.isArray(takt.doby)) {
                  takt.doby.forEach((doba) => {
                    doba.riadky.forEach((riadok) => {
                      riadok.hodnota_cisla.forEach((hodnota) => {
                        hodnota.cela_gulicka.forEach((gulicka) => {
                          if (
                            cislo == "" &&
                            (gulicka.index == 0 ||
                              gulicka.index == "" ||
                              gulicka.index == "0")
                          ) {
                            gulicka.index = "" + cislo; // Nastavenie indexu
                          }
                        });
                      });
                    });
                    if (
                      cislo == "" &&
                      (doba.basa_index == 0 ||
                        doba.basa_index == "" ||
                        doba.basa_index == "0")
                    ) {
                      doba.basa_index = "" + cislo;
                    }
                  });
                }
              });
            }
          });
        } else {
          this.$store.state.SnackBarText =
            "Nepodarilo sa nájsť štruktúru častí skladby";
        }
      }
    },
    setGhost(iSeeYou, what) {
      var selectedTakt = this.selectedTakt; // Získajte aktuálne vybraný takt
      var selectedDoba = this.selectedDoba;
      if (what === "takt") {
        if (iSeeYou === "hide") {
          selectedTakt.isGhost = true;
        } else {
          selectedTakt.isGhost = false;
        }
      } else {
        if (iSeeYou === "hide") {
          selectedDoba.isGhost = true;
        } else {
          selectedDoba.isGhost = false;
        }
      }
    },
    changeColor(doba) {
      var farba = doba.farba;
      if (farba == 0 || farba == 1) {
        farba++;
      } else {
        farba = 0;
      }

      doba.farba = farba;
    },
    changeColorPopis(popis, popisIndex, castIndex) {
      var farba = popis[popisIndex];
      if (farba == 0 || farba == 1 || farba == 2) {
        farba++;
      } else {
        farba = 0;
      }

      this.dataNoty.casty[castIndex].popis_farba[popisIndex] = farba;
    },
    moveToNextInput(event, className) {
      const inputs = document.querySelectorAll(className);
      const currentInputIndex = Array.prototype.indexOf.call(
        inputs,
        event.target
      );

      console.log("Current Index:", currentInputIndex);
      console.log("Total Inputs:", inputs.length);

      if (
        (event.key === "ArrowDown" ||
          (event.key === "Tab" && !event.shiftKey)) &&
        currentInputIndex !== -1 &&
        currentInputIndex < inputs.length - 1
      ) {
        event.preventDefault(); // Zastaví predvolené správanie klávesov
        inputs[currentInputIndex + 1].focus();
      } else if (
        event.key === "ArrowUp" ||
        (event.key === "Tab" && event.shiftKey && currentInputIndex > 0)
      ) {
        event.preventDefault(); // Zastaví predvolené správanie klávesov

        const prevInput = inputs[currentInputIndex - 1];
        prevInput.focus();

        const valueLength = prevInput.value.length;
        prevInput.setSelectionRange(valueLength, valueLength);
      }
    },
  },
  watch: {
    "dataNoty.casty": {
      handler() {
        this.updatePageNumber(); // Spustí sa pri každej zmene `dataNoty.casty`
      },
      deep: true, // Sleduje aj vnútorné zmeny
    },
    contextMenuVisible(value) {
      if (!value) {
        this.radialPath = [];
        this.radialSelection = null;
        this.radialHoveredItem = null;
      }
    },
    // "zapisyData.riadky": function () {
    //   this.updateAllRiadky(); // Aktualizuje všetky riadky pri zmene počtu riadkov
    // },
  },
  computed: {
    radialRootItems() {
      const items = [];

      const makeAction = (id, title, action, innerLabel, icon = null) => ({
        id,
        title,
        innerLabel,
        action,
        icon,
      });

      if (!this.selectedRiadok) {
        items.push(
          makeAction(
            "Pridať-časť",
            "",
            () => this.addPart(),
            "",
            '<i class="fa-solid fa-plus"></i>'
          )
        );

        if (this.selectedCast) {
          items.push(
            makeAction(
              "Vymazať-časť",
              "",
              () => this.removePart(),
              "",
              '<i class="fa-solid fa-minus"></i>'
            )
          );
        }
      }

      if (this.selectedRiadok) {
        const baseHoldings = Array.from({ length: 12 }, (_, idx) => {
          const value = idx + 1;
          return makeAction(
            `holding-${value}`,
            `Držanie ${value} ${this.formatDobaLabel(value)}`,
            () => this.addHolding(value),
            `${value}`
          );
        });

        const extendedHoldings = [
          { value: 20, label: "", inner: "1" },
          { value: 40, label: "", inner: "2" },
          { value: 60, label: "", inner: "3" },
          { value: 80, label: "", inner: "4" },
          { value: 100, label: "", inner: "5" },
          { value: 120, label: "", inner: "6" },
          { value: 140, label: "", inner: "7" },
          { value: 200, label: "", inner: "-1" },
          { value: 400, label: "", inner: "-2" },
          { value: 600, label: "", inner: "-3" },
          { value: 800, label: "", inner: "-4" },
          { value: 1000, label: "", inner: "-5" },
          { value: 1200, label: "", inner: "-6" },
          { value: 1400, label: "", inner: "-7" },
        ].map((option, index) =>
          makeAction(
            `holding-extended-${index}`,
            option.label,
            () => this.addHolding(option.value),
            option.inner
          )
        );

        items.push({
          id: "holding-full",
          title: "Držanie tónu",
          icon: '<i class="fa-solid fa-l fa-rotate-90"></i>',
          items: baseHoldings,
        });
        items.push({
          id: "holding-extended",
          title: "Držanie tónu neskončené",
          icon: '<i class="fa-solid fa-l fa-rotate-90"></i><i class="fa-solid fa-l fa-flip-vertical fa-rotate-by" style="--fa-rotate-angle: -90deg;"></i>',
          items: extendedHoldings,
        });
      }

      if (this.selectedBass) {
        const baseBass = Array.from({ length: 8 }, (_, idx) => {
          const value = idx + 1;
          return makeAction(
            `bass-holding-${value}`,
            `Držanie ${value} ${this.formatDobaLabel(value)}`,
            () => this.addHoldingBass(value),
            `${value}`
          );
        });

        const extendedBass = [
          { value: 20, label: "", inner: "2" },
          { value: 30, label: "", inner: "3" },
          { value: 40, label: "", inner: "4" },
          { value: 50, label: "", inner: "5" },
          { value: 100, label: "", inner: "-1" },
          { value: 200, label: "", inner: "-2" },
          { value: 300, label: "", inner: "-3" },
          { value: 400, label: "", inner: "-4" },
        ].map((option, index) =>
          makeAction(
            `bass-holding-extended-${index}`,
            option.label,
            () => this.addHoldingBass(option.value),
            option.inner
          )
        );

        items.push({
          id: "bass-holding",
          icon: '<i class="fa-solid fa-l fa-rotate-270"></i>',
          items: baseBass,
        });

        items.push({
          id: "bass-holding-extended",
          title: "Držanie neskončené basy",
          innerLabel: "Basy ∞",
          items: extendedBass,
        });
      }

      if (this.selectedDoba) {
        items.push({
          id: "repeat",
          title: "Refrén",
          icon: '<i class="fa-solid fa-repeat"></i>',
          items: [
            makeAction(
              "repeat-start",
              "Opakovať začiatok",
              () => this.addOpakovanie("zaciatok"),
              "[:"
            ),
            makeAction(
              "repeat-end",
              "Opakovať koniec",
              () => this.addOpakovanie("koniec"),
              ":]"
            ),
            makeAction(
              "repeat-reset",
              "Zrušiť opakovanie",
              () => this.addOpakovanie("zrusit"),
              "Zrušiť"
            ),
          ],
        });
      }

      if (
        (this.selectedCast || this.selectedCast === 0) &&
        this.zapisyData.doby == 4
      ) {
        const popisItems = [
          { args: [1, 0, 0, "", true], label: "|----|" },
          { args: [2, 0, 0, "", true], label: "|--|--|" },
          { args: [2, 1, 0, "popis-2", true], label: "|-|-|--|" },
          { args: [2, 1, 0, "popis-2", false], label: "|--|-|-|" },
          { args: [1, 1, 1, "popis-2", true], label: "|-|--|-|" },
          { args: [4, 0, 0, "", true], label: "|-|-|-|" },
        ].map((option, index) =>
          makeAction(
            `popis-4-${index}`,
            option.label,
            () => this.addPopis(...option.args),
            null
          )
        );

        items.push({
          id: "popis-4",
          title: "Popis",
          icon: '<i class="fa-solid fa-pen-to-square"></i>',
          items: popisItems,
        });
      }

      if (
        (this.selectedCast || this.selectedCast === 0) &&
        this.zapisyData.doby == 3
      ) {
        const popisItems = [
          { args: [1, 0, 0, "", true], label: "|-----|" },
          { args: [1, 2, 0, "popis-2", true], label: "|-|--|--|" },
          { args: [1, 2, 0, "popis-2", false], label: "|--|--|-|" },
          { args: [2, 1, 0, "popis-3", true], label: "|-|-|---|" },
          { args: [2, 1, 0, "popis-3", false], label: "|---|-|-|" },
          { args: [5, 0, 0, "", true], label: "|-|-|-|-|-|" },
        ].map((option, index) =>
          makeAction(
            `popis-3-${index}`,
            option.label,
            () => this.addPopis(...option.args),
            option.label.split(" ")[1]
          )
        );

        items.push({
          id: "popis-3",
          icon: "Popisy",
          innerLabel: "Popisy",
          items: popisItems,
        });
      }

      if (this.selectedTakt) {
        items.push({
          id: "visibility",
          title: "Zobrazenie",
          icon: '<i class="fa-solid fa-eye"></i>',
          items: [
            makeAction(
              "hide-takt",
              "Skryť takt",
              () => this.setGhost("hide", "takt"),
              "Skryť"
            ),
            makeAction(
              "show-takt",
              "Zobraziť takt",
              () => this.setGhost("show", "takt"),
              "Zobraziť"
            ),
            makeAction(
              "hide-doba",
              "Skryť dobu",
              () => this.setGhost("hide", "doba"),
              "Skryť"
            ),
            makeAction(
              "show-doba",
              "Zobraziť dobu",
              () => this.setGhost("show", "doba"),
              "Zobraziť"
            ),
          ],
        });

        items.push({
          id: "copy",
          title: "Kopírovať",
          icon: '<i class="fa-solid fa-copy"></i>',
          items: [
            makeAction(
              "copy-takt",
              "Skopírovať takt",
              () => this.copyTakt(),
              "Kopírovať"
            ),
            makeAction(
              "paste-takt",
              "Vložiť takt",
              () => this.pasteTakt(),
              "Vložiť"
            ),
            makeAction(
              "copy-cast",
              "Skopírovať časť",
              () => this.copyCast(),
              "Kopírovať"
            ),
            makeAction(
              "paste-cast",
              "Vložiť časť",
              () => this.pasteCast(),
              "Vložiť"
            ),
          ],
        });
      }

      if (!this.selectedRiadok) {
        const indexItems = [];

        if (this.selectedTakt) {
          indexItems.push(
            makeAction(
              "index-add-takt",
              "Pridať indexy",
              () => this.setIndex("", "takt"),
              "Pridať"
            )
          );
          indexItems.push(
            makeAction(
              "index-remove-takt",
              "Vymazať indexy",
              () => this.setIndex(0, "takt"),
              "Vymazať"
            )
          );
        }

        indexItems.push(
          makeAction(
            "index-add-cast",
            "Pridať všetky indexy",
            () => this.setIndex("", "cast"),
            "Pridať"
          )
        );
        indexItems.push(
          makeAction(
            "index-remove-cast",
            "Vymazať všetky indexy",
            () => this.setIndex(0, "cast"),
            "Vymazať"
          )
        );

        if (indexItems.length) {
          items.push({
            id: "indexy",
            icon: '<i class="fa-solid fa-hands"></i>',
            items: indexItems,
          });
        }
      }

      return items;
    },
    visibleRadialItems() {
      if (!this.radialPath.length) {
        return this.radialRootItems;
      }

      const last = this.radialPath[this.radialPath.length - 1];
      return Array.isArray(last.items) ? last.items : [];
    },
    radialCenter() {
      return this.radialSize / 2;
    },
    radialViewBox() {
      return `0 0 ${this.radialSize} ${this.radialSize}`;
    },
    radialAngleStep() {
      return this.visibleRadialItems.length
        ? TWO_PI / this.visibleRadialItems.length
        : TWO_PI;
    },
    radialCenterLabel() {
      const hoverLabel = this.getRadialItemLabel(this.radialHoveredItem);
      if (hoverLabel) {
        return hoverLabel;
      }

      return this.radialPath.length ? "Späť" : "Zavrieť";
    },
    displayedPocetTaktov: {
      get() {
        // Zobrazená hodnota, vydelená tromi
        return Math.floor(this.dataNoty.pocetTaktov / 3);
      },
      set(value) {
        // Uloží nezmenenú hodnotu do `dataNoty.pocetTaktov`
        this.dataNoty.pocetTaktov = value * 3;
      },
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/zapis.scss";

@media (max-height: 900px), screen and (max-width: 1300px) {
  .main-content {
    font-size: 111%;
  }
}

@media (max-height: 850px), screen and (max-width: 1180px) {
  .main-content {
    font-size: 124%;
  }
}

section {
  background: #ededed;
  box-sizing: border-box;
  width: inherit;
  height: 100%;
  position: relative;
  overflow: visible;
}

.scroll {
  width: 100%;
  max-width: unset;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  gap: 2%;
}

.modal-overlay {
  width: 100%;
  display: flex;
  align-items: center;
  font-size: 90%;
  justify-content: center;

  h2 {
    font-size: 3em;
    letter-spacing: 0.3em;
  }

  label {
    font-size: 1.8em;
  }

  input {
    width: 2em;
    margin: 0.2em 1em;
    height: auto;
    font-size: 2.2em;
    border: 1px solid #dedede;
    border-radius: 1rem;
  }

  a {
    width: max-content;
    margin: auto;
  }
}

.akcie-container {
  margin-bottom: 2em;
}

.akcie-container .button {
  width: max-content;
  margin: 1em 1em 0;
}

.akcie-container > .button {
  margin-left: auto;
  margin-right: auto;
  position: relative;
}

.button-under {
  background: no-repeat;
  box-shadow: none;
  font-size: 1em;
  text-decoration: underline;
}

.akcie {
  display: flex;
  flex-direction: row;
  gap: 0;
  justify-content: center;
}

.struktura-textarea {
  width: 100%;
  text-align: left;
  max-width: 90%;
  border: 0.1em solid #c6c6c6;
  border-radius: 1em;
  padding: 1em;
  height: 20em;
}

.radial-overlay-fade-enter-active,
.radial-overlay-fade-leave-active {
  transition: opacity 0.18s ease;
}

.radial-overlay-fade-enter-from,
.radial-overlay-fade-leave-to {
  opacity: 0;
}

.radial-context-menu-backdrop {
  position: fixed;
  inset: 0;
  z-index: 9998;
  pointer-events: auto;
}

.radial-context-menu {
  position: absolute;
  transform: translate(-50%, -50%);
  z-index: 9999;
  pointer-events: none;

  .radial-overlay {
    pointer-events: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(
      circle at center,
      rgba(0, 0, 0, 0.08),
      transparent 75%
    );
    border-radius: 1.25rem;
    padding: 1.25rem;
  }

  .radial-menu {
    width: 70%;
    height: 70%;
  }

  .radial-sector path {
    fill: rgba(94, 94, 95, 0.75);
    transition: fill 0.15s ease;
    stroke: rgba(255, 255, 255, 0.6);
    stroke-width: 2px;
    stroke-linejoin: round;
  }

  .radial-sector:focus {
    outline: none;
  }

  .radial-sector:focus path {
    outline: none;
  }

  .radial-sector:hover path,
  .radial-sector:focus-visible path {
    fill: #90ca50;
    outline: none;
  }

  .radial-sector.selected path {
    fill: rgba(50, 205, 50, 0.85);
  }

  .radial-icon text {
    fill: #ffffff;
    font-size: 1.5rem;
    font-weight: 600;
  }

  .radial-label {
    pointer-events: none;
  }

  .radial-label text {
    fill: #ffffff;
    font-size: 0rem;
    font-weight: 600;
    letter-spacing: 0.02em;
  }

  .radial-label-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: #ffffff;
    font-size: 2.2rem;
    pointer-events: none;

    svg {
      width: 100%;
      height: 100%;
    }
  }

  .radial-center circle {
    fill: rgba(26, 27, 31, 0.85);
    transition: fill 0.15s ease;
  }

  .radial-center text {
    fill: #ffffff;
    font-size: 0.9rem;
    font-weight: 600;
    letter-spacing: 0.03em;
  }

  .radial-center:focus {
    outline: none;
  }

  .radial-center:focus circle {
    outline: none;
  }

  .radial-center:hover circle,
  .radial-center:focus-visible circle {
    fill: #00913f;
    outline: none;
  }
}
</style>
