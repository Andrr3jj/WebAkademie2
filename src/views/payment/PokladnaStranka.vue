<template>
  <section class="computer" :id="$route.name">
    <div class="scroll">
      <TheHeadline></TheHeadline>

      <div class="line horizontal"></div>

      <div class="box">
        <div class="box-item left">
          <div class="user-info-box">
            <div class="user-info-head">
              <p class="nazov">Moje informácie</p>
              <img
                @click="editUser(true)"
                v-if="!editUserValue"
                src="@/assets/images/icons/upravit.svg"
                alt="upraviť"
                class="click"
              />
            </div>
            <div class="user-info">
              <p class="nazov-piesne">
                {{ $store.state.user.name + " " + $store.state.user.surname }}
              </p>
              <p class="nazov-piesne">{{ $store.state.user.email }}</p>
              <p v-if="!editUserValue" class="nazov-piesne">
                {{
                  $store.state.user.phone == "" ||
                  $store.state.user.phone == null
                    ? "Žiadne tel. číslo"
                    : $store.state.user.phone
                }}
              </p>
              <p v-if="!editUserValue" class="nazov-piesne">
                {{
                  $store.state.user.dateOfBirth != null
                    ? $store.state.user.dateOfBirth.split("-")[2] +
                      "." +
                      $store.state.user.dateOfBirth.split("-")[1] +
                      ". " +
                      $store.state.user.dateOfBirth.split("-")[0]
                    : "Neznámy"
                }}
              </p>
            </div>
            <form @submit.prevent="upravenieUser" v-if="editUserValue">
              <label for="meno">Meno <span>*</span></label>
              <div class="row">
                <div><img src="@/assets/images/icons/profil.svg" /></div>
                <input
                  @input="userValueChange('meno')"
                  @change="userValueChange('meno')"
                  type="text"
                  name="meno"
                  id=""
                  v-model="$store.state.user.name"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="userValue.meno"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!userValue.meno"
                  />
                </div>
              </div>

              <label for="priezvisko">Priezvisko <span>*</span></label>
              <div class="row">
                <div><img src="@/assets/images/icons/profil.svg" /></div>
                <input
                  @input="userValueChange('priezvisko')"
                  @change="userValueChange('priezvisko')"
                  type="text"
                  name="priezvisko"
                  id=""
                  v-model="$store.state.user.surname"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="userValue.priezvisko"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!userValue.priezvisko"
                  />
                </div>
              </div>

              <label for="phone">Mobil <span>*</span></label>
              <div class="row">
                <div><img src="@/assets/images/icons/profil.svg" /></div>
                <input
                  @input="userValueChange('phone')"
                  @change="userValueChange('phone')"
                  type="text"
                  name="phone"
                  id=""
                  v-model="$store.state.user.phone"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="userValue.phone"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!userValue.phone"
                  />
                </div>
              </div>
              <p class="under">+421 900 000 000</p>

              <label for="date">Dátum narodenia <span>*</span></label>
              <div class="row last-row">
                <div>
                  <img src="@/assets/images/icons/datumNarodenia.svg" />
                </div>
                <input
                  @input="userValueChange('datum')"
                  @change="userValueChange('datum')"
                  type="date"
                  name="date"
                  id=""
                  v-model="$store.state.user.dateOfBirth"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="userValue.datum"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!userValue.datum"
                  />
                </div>
              </div>
              <p class="under">DD.MM.RRRR</p>

              <button class="button" type="submit">
                <a>Uložiť</a>
              </button>
              <div @click="editUser(false)" class="zrusit click">
                <img src="@/assets/images/icons/zrusit.svg" alt="" />
                <p>Zrušiť</p>
              </div>
            </form>
          </div>

          <div class="line horizontal"></div>

          <div class="user-info-box">
            <div class="user-info-head">
              <p class="nazov">
                {{
                  editAddressDeliveryValue == true
                    ? "Fakturačná adresa"
                    : "Fakturačná/Dodacia adresa"
                }}
              </p>
              <img
                @click="editAddress(true)"
                v-if="!editAddressValue"
                src="@/assets/images/icons/upravit.svg"
                alt=""
                class="click"
              />
            </div>
            <div class="user-info">
              <p class="nazov-piesne">
                {{ $store.state.user.name + " " + $store.state.user.surname }}
              </p>
              <p class="nazov-piesne">
                {{
                  $store.state.user.billingAddressStreet == undefined ||
                  $store.state.user.billingAddressHouseNumber == undefined ||
                  $store.state.user.billingAddressStreet == null ||
                  $store.state.user.billingAddressHouseNumber == null
                    ? "Žiadna ulica"
                    : $store.state.user.billingAddressStreet +
                      " " +
                      $store.state.user.billingAddressHouseNumber
                }}
              </p>
              <p v-if="!editAddressValue" class="nazov-piesne">
                {{
                  $store.state.user.billingAddressPostcode == undefined ||
                  $store.state.user.billingAddressCity == undefined ||
                  $store.state.user.billingAddressPostcode == null ||
                  $store.state.user.billingAddressCity == null
                    ? "Žiadna adresa"
                    : $store.state.user.billingAddressPostcode +
                      " " +
                      $store.state.user.billingAddressCity
                }}
              </p>
              <p v-if="!editAddressValue" class="nazov-piesne">
                {{
                  $store.state.user.billingAddressState || "Slovenská republika"
                }}
              </p>
            </div>

            <div v-if="editAddressDeliveryValue" class="user-info-head">
              <p class="nazov">
                {{ editAddressDeliveryValue == true ? "Dodacia adresa" : "" }}
              </p>
            </div>
            <div v-if="editAddressDeliveryValue" class="user-info">
              <p class="nazov-piesne">
                {{
                  $store.state.user.deliveryAddressStreet == undefined ||
                  $store.state.user.deliveryAddressHouseNumber == undefined ||
                  $store.state.user.deliveryAddressStreet == null ||
                  $store.state.user.deliveryAddressHouseNumber == null
                    ? "Žiadna ulica"
                    : $store.state.user.deliveryAddressStreet +
                      " " +
                      $store.state.user.deliveryAddressHouseNumber
                }}
              </p>
              <p v-if="!editAddressValue" class="nazov-piesne">
                {{
                  $store.state.user.deliveryAddressPostcode == undefined ||
                  $store.state.user.deliveryAddressCity == undefined ||
                  $store.state.user.deliveryAddressPostcode == null ||
                  $store.state.user.deliveryAddressCity == null
                    ? "Žiadna adresa"
                    : $store.state.user.deliveryAddressPostcode +
                      " " +
                      $store.state.user.deliveryAddressCity
                }}
              </p>
              <p v-if="!editAddressValue" class="nazov-piesne">
                {{
                  $store.state.user.deliveryAddressState ||
                  "Slovenská republika"
                }}
              </p>
            </div>

            <form @submit.prevent="upravenieAddress" v-if="editAddressValue">
              <label for="adresa">Ulica <span>*</span></label>
              <div class="row last-row">
                <div><img src="@/assets/images/icons/adresa.svg" /></div>
                <input
                  @input="addressValueChange()"
                  @change="addressValueChange()"
                  type="text"
                  name="ulica"
                  id=""
                  v-model="$store.state.user.billingAddressStreet"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="addressValue.ulica"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!addressValue.ulica"
                  />
                </div>
              </div>

              <label for="adresa">Čislo domu <span>*</span></label>
              <div class="row last-row">
                <div><img src="@/assets/images/icons/adresa.svg" /></div>
                <input
                  @input="addressValueChange()"
                  @change="addressValueChange()"
                  type="text"
                  name="cisloDomu"
                  id=""
                  v-model="$store.state.user.billingAddressHouseNumber"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="addressValue.cisloDomu"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!addressValue.cisloDomu"
                  />
                </div>
              </div>

              <label for="priezvisko">Obec/mesto <span>*</span></label>
              <div class="row last-row">
                <div><img src="@/assets/images/icons/mesto.svg" /></div>
                <input
                  @input="addressValueChange()"
                  @change="addressValueChange()"
                  type="text"
                  name="mesto"
                  id=""
                  v-model="$store.state.user.billingAddressCity"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="addressValue.mesto"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!addressValue.mesto"
                  />
                </div>
              </div>
              <p class="under">Mesto, lokalita</p>

              <label for="date">PSČ <span>*</span></label>
              <div class="row last-row">
                <div>
                  <img src="@/assets/images/icons/adresa.svg" />
                </div>
                <input
                  @input="addressValueChange()"
                  @change="addressValueChange()"
                  type="text"
                  name="psc"
                  id=""
                  v-model="$store.state.user.billingAddressPostcode"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="addressValue.psc"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!addressValue.psc"
                  />
                </div>
              </div>
              <p class="under">Zadajte svoje PSČ. Napr. 013 03</p>

              <label for="billingAddressState">Krajina <span>*</span></label>
              <div class="row last-row">
                <div><img src="@/assets/images/icons/state.png" /></div>
                <select
                  @change="addressValueChange()"
                  name="billingAddressState"
                  id="billingAddressState"
                  v-model="$store.state.user.billingAddressState"
                >
                  <option disabled value="">-- Vyberte krajinu --</option>
                  <option value="Slovenská republika">
                    Slovenská republika
                  </option>
                  <option value="Česká republika">Česká republika</option>
                  <option value="Poľská republika">Poľská republika</option>
                </select>
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="addressValue.krajina"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!addressValue.krajina"
                  />
                </div>
              </div>

              <label
                v-if="this.sposobyDopravy.length != 0"
                for="editAddressDeliveryValue"
                class="checkbox"
              >
                Doručiť na iné miesto
                <input
                  type="checkbox"
                  name="editAddressDeliveryValue"
                  id="editAddressDeliveryValue"
                  checked="checked"
                  v-model="editAddressDeliveryValue"
                />
                <span class="checkmark"></span>
              </label>

              <p
                v-if="editAddressDeliveryValue"
                class="nazov"
                style="margin-top: 1em"
              >
                Dodacia adresa
              </p>

              <div class="delivery-form" v-if="editAddressDeliveryValue">
                <label for="adresa">Ulica <span>*</span></label>
                <div class="row last-row">
                  <div><img src="@/assets/images/icons/adresa.svg" /></div>
                  <input
                    @input="addressValueChange()"
                    @change="addressValueChange()"
                    type="text"
                    name="ulicaDelivery"
                    id=""
                    v-model="$store.state.user.deliveryAddressStreet"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressDeliveryValue.ulica"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressDeliveryValue.ulica"
                    />
                  </div>
                </div>

                <label for="adresa">Čislo domu <span>*</span></label>
                <div class="row last-row">
                  <div><img src="@/assets/images/icons/adresa.svg" /></div>
                  <input
                    @input="addressValueChange()"
                    @change="addressValueChange()"
                    type="text"
                    name="cisloDomuDelivery"
                    id=""
                    v-model="$store.state.user.deliveryAddressHouseNumber"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressDeliveryValue.cisloDomu"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressDeliveryValue.cisloDomu"
                    />
                  </div>
                </div>

                <label for="priezvisko">Obec/mesto <span>*</span></label>
                <div class="row last-row">
                  <div><img src="@/assets/images/icons/mesto.svg" /></div>
                  <input
                    @input="addressValueChange()"
                    @change="addressValueChange()"
                    type="text"
                    name="mestoDelivery"
                    id=""
                    v-model="$store.state.user.deliveryAddressCity"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressDeliveryValue.mesto"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressDeliveryValue.mesto"
                    />
                  </div>
                </div>
                <p class="under">Mesto, lokalita</p>

                <label for="date">PSČ <span>*</span></label>
                <div class="row last-row">
                  <div>
                    <img src="@/assets/images/icons/adresa.svg" />
                  </div>
                  <input
                    @input="addressValueChange()"
                    @change="addressValueChange()"
                    type="text"
                    name="pscDelivery"
                    id=""
                    v-model="$store.state.user.deliveryAddressPostcode"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressDeliveryValue.psc"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressDeliveryValue.psc"
                    />
                  </div>
                </div>
                <p class="under">Zadajte svoje PSČ. Napr. 013 03</p>

                <label for="deliveryAddressState">Krajina <span>*</span></label>
                <div class="row last-row">
                  <div><img src="@/assets/images/icons/state.png" /></div>
                  <select
                    @change="addressValueChange()"
                    name="deliveryAddressState"
                    id="deliveryAddressState"
                    v-model="$store.state.user.deliveryAddressState"
                  >
                    <option disabled value="">-- Vyberte krajinu --</option>
                    <option value="Slovenská republika">
                      Slovenská republika
                    </option>
                    <option value="Česká republika">Česká republika</option>
                    <option value="Poľská republika">Poľská republika</option>
                  </select>
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressDeliveryValue.krajina"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressDeliveryValue.krajina"
                    />
                  </div>
                </div>
              </div>

              <button class="button" type="submit">
                <a>Uložiť</a>
              </button>
              <div @click="editAddress(false)" class="zrusit click">
                <img src="@/assets/images/icons/zrusit.svg" alt="" />
                <p>Zrušiť</p>
              </div>
            </form>
          </div>

          <div class="line horizontal"></div>

          <h4 class="m-right podrobnosti-objednavky">
            PODROBNOSTI O OBJEDNÁVKE
          </h4>
          <ul
            v-if="
              Object.keys(this.produktyData).length ==
              this.$store.state.userCart.length
            "
            class="cart"
          >
            <kosik-produkt
              v-for="item in $store.state.userCart"
              :key="item.id"
              :notes="{ id: item.id, count: item.count }"
              :produktyData="produktyData[item.id_delete]"
              :umiestnenie="'pokladna'"
            ></kosik-produkt>
          </ul>

          <div class="line horizontal"></div>

          <div class="gift_card">
            <h4 class="m-right">PLATBA</h4>
            <p class="platba-ako m-right">Ako chcete zaplatiť?</p>
            <div
              class="a_gift-cart"
              @click="zobrazPoukazkuInput = !zobrazPoukazkuInput"
            >
              <p>Pridať darčekové karty</p>
              <img src="@/assets/images/icons/darcek.svg" alt="" />
            </div>
            <div v-if="zobrazPoukazkuInput" class="voucher-input-wrap">
              <VoucherInput
                v-model="kodDarcekovejPoukazky"
                placeholder="Zadajte kód darčekovej poukážky"
                @submit.stop.prevent="pridajDarcekovuPoukazku"
              />
              <button
                @click.stop.prevent="pridajDarcekovuPoukazku"
                class="button"
              >
                Pridať
              </button>
            </div>
          </div>

          <div class="debet-cart">
            <div class="line"></div>
            <p>Karta</p>
            <div class="prijimame">
              <img src="@/assets/images/platby/platby1.png" alt="Maestro" />
              <img src="@/assets/images/platby/platby2.png" alt="Mastercadt" />
              <img src="@/assets/images/platby/platby3.png" alt="VISA" />
            </div>
          </div>
        </div>
        <div class="box-item right">
          <h4>ZHRNUTIE OBJEDNÁVKY</h4>

          <div class="zlavy">
            <p>Zľavy</p>
            <p
              v-if="!chcemZadatKupon && !uznanyKupon.stav"
              @click="chcemZadatKupon = true"
              class="uplatnenie-zlavy"
            >
              Uplatniť zľavu
            </p>
            <p
              v-if="chcemZadatKupon"
              @click="chcemZadatKupon = false"
              class="uplatnenie-zlavy"
            >
              Nechcem zľavu
            </p>
            <p
              v-if="uznanyKupon.stav"
              @click="zmazanieKuponu()"
              class="uplatnenie-zlavy"
            >
              Aktívny kupón
            </p>
          </div>

          <!-- Zľavy (kupóny aj poukážky) - jedna sekcia -->
          <div class="zlavy-list">
            <!-- Kupón -->
            <div v-if="uznanyKupon.stav" class="zlava-wrap">
              <div class="zlava-type-label">Kupón</div>
              <div
                class="uznany-kupon flex-row"
                @click="zmazanieKuponu()"
                style="cursor: pointer"
                title="Vymazať kupón"
              >
                <span class="kupon-kod">
                  {{ uznanyKupon.nazov }} ({{ uznanyKupon.hodnota }}%)
                </span>
                <span class="zlava-suma">-{{ produktyCenaCouponLess }}€</span>
              </div>
            </div>

            <!-- Input na kupón (nerobím úpravy, len roztiahnuť na šírku) -->
            <form v-if="chcemZadatKupon" @submit.prevent="odoslanieKuponu()">
              <div class="zlava-type-label">Kupón</div>
              <div class="row full-width">
                <input
                  type="text"
                  placeholder="Zľavový kód"
                  name="code"
                  id="code"
                  @input="kuponZmena()"
                  @change="kuponZmena()"
                  v-model="kuponKod"
                  class="full-width"
                />
              </div>
            </form>

            <!-- Darčeková poukážka -->
            <div
              v-for="(poukazka, i) in giftCardsInCart"
              :key="poukazka.code"
              class="zlava-wrap"
            >
              <div class="zlava-type-label">Darčeková poukážka</div>
              <div
                class="uznany-kupon flex-row one-gift-card"
                @click="odstranDarcekovuPoukazku(poukazka.code)"
                style="cursor: pointer"
                title="Vymazať poukážku"
              >
                <span class="kupon-kod">
                  🎁 {{ poukazka.code }}
                  <span v-if="poukazka.value">
                    ({{ Number(poukazka.value).toFixed(2).replace(".", ",") }}
                    €)
                  </span>
                </span>
                <span class="zlava-suma">{{ giftCardDiscounts[i] }}€</span>
              </div>
            </div>
          </div>
          <p class="small ta-left">
            Pokračovaním vyjadrujete súhlas so
            <a
              class="a"
              target="_blank"
              href="https://heligonkovaakademia.sk/obchodne-podmienky"
            >
              všeobecnými zmluvnými podmienkami spoločnosti
            </a>
          </p>
          <p class="small ta-left">
            Vaše osobné údaje budeme spracúvať v súlade s
            <a
              class="a"
              target="_blank"
              href="https://heligonkovaakademia.sk/obchodne-podmienky"
            >
              Vyhlásením o ochrane osobných údajov spoločnosti</a
            >
          </p>
          <div class="line horizontal"></div>

          <div v-if="sposobyDopravy.length != 0" class="sposob">
            <div class="suma">
              <p><b>Spôsob dopravy</b></p>
              <p></p>
            </div>
            <div class="size-options">
              <label v-for="item in sposobyDopravy" :key="item">
                <input
                  type="radio"
                  :value="item.id"
                  v-model="sposobDopravy"
                  @change="nastavSposobDopravy(item.id)"
                />
                <span
                  >{{ item.name }} ( +
                  {{
                    (item.price / 100)
                      .toFixed(2)
                      .toString()
                      .replaceAll(".", ",")
                  }}
                  € )</span
                >
              </label>
            </div>
          </div>
          <div class="line horizontal" v-if="sposobyDopravy.length != 0"></div>

          <div class="suma">
            <p><b>Celkom</b></p>
            <p>{{ produktyCena }}€</p>
          </div>
          <div class="prijimame">
            <p>Prijímame</p>
            <img src="@/assets/images/platby/platby1.png" alt="Maestro" />
            <img src="@/assets/images/platby/platby2.png" alt="Mastercadt" />
            <img src="@/assets/images/platby/platby3.png" alt="VISA" />
          </div>

          <div @click="odoslaniePlatby()" class="button green">
            <p>Dokončiť nákup</p>
          </div>

          <p class="small">Odoslať objednávku s povinnosťou platby</p>
        </div>
      </div>
    </div>
  </section>

  <div class="mobile" :id="$route.name">
    <section>
      <div class="scroll">
        <TheHeadline></TheHeadline>

        <div class="line horizontal"></div>

        <div class="box">
          <div class="box-item left">
            <div class="user-info-box">
              <div class="user-info-head">
                <p class="nazov">Moje informácie</p>
                <img
                  @click="editUser(true)"
                  v-if="!editUserValue"
                  src="@/assets/images/icons/upravit.svg"
                  alt="upraviť"
                  class="click"
                />
              </div>
              <div class="user-info">
                <p class="nazov-piesne">
                  {{ $store.state.user.name + " " + $store.state.user.surname }}
                </p>
                <p class="nazov-piesne">{{ $store.state.user.email }}</p>
                <p v-if="!editUserValue" class="nazov-piesne">
                  {{
                    $store.state.user.phone == "" ||
                    $store.state.user.phone == null
                      ? "Žiadne tel. číslo"
                      : $store.state.user.phone
                  }}
                </p>
                <p v-if="!editUserValue" class="nazov-piesne">
                  {{
                    $store.state.user.dateOfBirth != null
                      ? $store.state.user.dateOfBirth.split("-")[2] +
                        "." +
                        $store.state.user.dateOfBirth.split("-")[1] +
                        ". " +
                        $store.state.user.dateOfBirth.split("-")[0]
                      : "Neznámy"
                  }}
                </p>
              </div>
              <form @submit.prevent="upravenieUser" v-if="editUserValue">
                <label for="meno">Meno <span>*</span></label>
                <div class="row">
                  <div><img src="@/assets/images/icons/profil.svg" /></div>
                  <input
                    @input="userValueChange('meno')"
                    @change="userValueChange('meno')"
                    type="text"
                    name="meno"
                    id=""
                    v-model="$store.state.user.name"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="userValue.meno"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!userValue.meno"
                    />
                  </div>
                </div>

                <label for="priezvisko">Priezvisko <span>*</span></label>
                <div class="row">
                  <div><img src="@/assets/images/icons/profil.svg" /></div>
                  <input
                    @input="userValueChange('priezvisko')"
                    @change="userValueChange('priezvisko')"
                    type="text"
                    name="priezvisko"
                    id=""
                    v-model="$store.state.user.surname"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="userValue.priezvisko"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!userValue.priezvisko"
                    />
                  </div>
                </div>

                <label for="phone">Mobil <span>*</span></label>
                <div class="row">
                  <div><img src="@/assets/images/icons/profil.svg" /></div>
                  <input
                    @input="userValueChange('phone')"
                    @change="userValueChange('phone')"
                    type="text"
                    name="phone"
                    id=""
                    v-model="$store.state.user.phone"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="userValue.phone"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!userValue.phone"
                    />
                  </div>
                </div>
                <p class="under">+421 900 000 000</p>

                <label for="date">Dátum narodenia <span>*</span></label>
                <div class="row last-row">
                  <div>
                    <img src="@/assets/images/icons/datumNarodenia.svg" />
                  </div>
                  <input
                    @input="userValueChange('datum')"
                    @change="userValueChange('datum')"
                    type="date"
                    name="date"
                    id=""
                    v-model="$store.state.user.dateOfBirth"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="userValue.datum"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!userValue.datum"
                    />
                  </div>
                </div>
                <p class="under">DD.MM.RRRR</p>

                <button class="button" type="submit">
                  <a>Uložiť</a>
                </button>
                <div @click="editUser(false)" class="zrusit click">
                  <img src="@/assets/images/icons/zrusit.svg" alt="" />
                  <p>Zrušiť</p>
                </div>
              </form>
            </div>

            <div class="line horizontal"></div>

            <div class="user-info-box">
              <div class="user-info-head">
                <p class="nazov">
                  {{
                    editAddressDeliveryValue == true
                      ? "Fakturačná adresa"
                      : "Fakturačná/Dodacia adresa"
                  }}
                </p>
                <img
                  @click="editAddress(true)"
                  v-if="!editAddressValue"
                  src="@/assets/images/icons/upravit.svg"
                  alt=""
                  class="click"
                />
              </div>
              <div class="user-info">
                <p class="nazov-piesne">
                  {{ $store.state.user.name + " " + $store.state.user.surname }}
                </p>
                <p class="nazov-piesne">
                  {{
                    $store.state.user.billingAddressStreet == undefined ||
                    $store.state.user.billingAddressHouseNumber == undefined ||
                    $store.state.user.billingAddressStreet == null ||
                    $store.state.user.billingAddressHouseNumber == null
                      ? "Žiadna ulica"
                      : $store.state.user.billingAddressStreet +
                        " " +
                        $store.state.user.billingAddressHouseNumber
                  }}
                </p>
                <p v-if="!editAddressValue" class="nazov-piesne">
                  {{
                    $store.state.user.billingAddressPostcode == undefined ||
                    $store.state.user.billingAddressCity == undefined ||
                    $store.state.user.billingAddressPostcode == null ||
                    $store.state.user.billingAddressCity == null
                      ? "Žiadna adresa"
                      : $store.state.user.billingAddressPostcode +
                        " " +
                        $store.state.user.billingAddressCity
                  }}
                </p>
                <p v-if="!editAddressValue" class="nazov-piesne">
                  {{
                    $store.state.user.billingAddressState ||
                    "Slovenská republika"
                  }}
                </p>
              </div>

              <div v-if="editAddressDeliveryValue" class="user-info-head">
                <p class="nazov">
                  {{ editAddressDeliveryValue == true ? "Dodacia adresa" : "" }}
                </p>
              </div>
              <div v-if="editAddressDeliveryValue" class="user-info">
                <p class="nazov-piesne">
                  {{
                    $store.state.user.deliveryAddressStreet == undefined ||
                    $store.state.user.deliveryAddressHouseNumber == undefined ||
                    $store.state.user.deliveryAddressStreet == null ||
                    $store.state.user.deliveryAddressHouseNumber == null
                      ? "Žiadna ulica"
                      : $store.state.user.deliveryAddressStreet +
                        " " +
                        $store.state.user.deliveryAddressHouseNumber
                  }}
                </p>
                <p v-if="!editAddressValue" class="nazov-piesne">
                  {{
                    $store.state.user.deliveryAddressPostcode == undefined ||
                    $store.state.user.deliveryAddressCity == undefined ||
                    $store.state.user.deliveryAddressPostcode == null ||
                    $store.state.user.deliveryAddressCity == null
                      ? "Žiadna adresa"
                      : $store.state.user.deliveryAddressPostcode +
                        " " +
                        $store.state.user.deliveryAddressCity
                  }}
                </p>
                <p v-if="!editAddressValue" class="nazov-piesne">
                  {{
                    $store.state.user.deliveryAddressState ||
                    "Slovenská republika"
                  }}
                </p>
              </div>
              <form @submit.prevent="upravenieAddress" v-if="editAddressValue">
                <label for="adresa">Ulica <span>*</span></label>
                <div class="row last-row">
                  <div><img src="@/assets/images/icons/adresa.svg" /></div>
                  <input
                    @input="addressValueChange()"
                    @change="addressValueChange()"
                    type="text"
                    name="ulica"
                    id=""
                    v-model="$store.state.user.billingAddressStreet"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressValue.ulica"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressValue.ulica"
                    />
                  </div>
                </div>

                <label for="adresa">Čislo domu <span>*</span></label>
                <div class="row last-row">
                  <div><img src="@/assets/images/icons/adresa.svg" /></div>
                  <input
                    @input="addressValueChange()"
                    @change="addressValueChange()"
                    type="text"
                    name="cisloDomu"
                    id=""
                    v-model="$store.state.user.billingAddressHouseNumber"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressValue.cisloDomu"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressValue.cisloDomu"
                    />
                  </div>
                </div>

                <label for="priezvisko">Obec/mesto <span>*</span></label>
                <div class="row last-row">
                  <div><img src="@/assets/images/icons/mesto.svg" /></div>
                  <input
                    @input="addressValueChange()"
                    @change="addressValueChange()"
                    type="text"
                    name="mesto"
                    id=""
                    v-model="$store.state.user.billingAddressCity"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressValue.mesto"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressValue.mesto"
                    />
                  </div>
                </div>
                <p class="under">Mesto, lokalita</p>

                <label for="date">PSČ <span>*</span></label>
                <div class="row last-row">
                  <div>
                    <img src="@/assets/images/icons/adresa.svg" />
                  </div>
                  <input
                    @input="addressValueChange()"
                    @change="addressValueChange()"
                    type="text"
                    name="psc"
                    id=""
                    v-model="$store.state.user.billingAddressPostcode"
                  />
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressValue.psc"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressValue.psc"
                    />
                  </div>
                </div>
                <p class="under">Zadajte svoje PSČ. Napr. 013 03</p>

                <label for="billingAddressState">Krajina <span>*</span></label>
                <div class="row last-row">
                  <div><img src="@/assets/images/icons/state.png" /></div>
                  <select
                    @change="addressValueChange()"
                    name="billingAddressState"
                    id="billingAddressState"
                    v-model="$store.state.user.billingAddressState"
                  >
                    <option disabled value="">-- Vyberte krajinu --</option>
                    <option value="Slovenská republika">
                      Slovenská republika
                    </option>
                    <option value="Česká republika">Česká republika</option>
                    <option value="Poľská republika">Poľská republika</option>
                  </select>
                  <div class="verify">
                    <img
                      src="@/assets/images/icons/spravne.svg"
                      alt="Overenie správne"
                      v-if="addressValue.krajina"
                    />
                    <img
                      src="@/assets/images/icons/nespravne.svg"
                      alt="Overenie nesprávne"
                      v-if="!addressValue.krajina"
                    />
                  </div>
                </div>

                <label
                  v-if="this.sposobyDopravy.length != 0"
                  for="editAddressDeliveryValue"
                  class="checkbox"
                >
                  Doručiť na iné miesto
                  <input
                    type="checkbox"
                    name="editAddressDeliveryValue"
                    id="editAddressDeliveryValue"
                    checked="checked"
                    v-model="editAddressDeliveryValue"
                  />
                  <span class="checkmark"></span>
                </label>

                <p v-if="editAddressDeliveryValue" class="nazov">
                  Dodacia adresa
                </p>

                <div class="delivery-form" v-if="editAddressDeliveryValue">
                  <label for="adresa">Ulica <span>*</span></label>
                  <div class="row last-row">
                    <div><img src="@/assets/images/icons/adresa.svg" /></div>
                    <input
                      @input="addressValueChange()"
                      @change="addressValueChange()"
                      type="text"
                      name="ulicaDelivery"
                      id=""
                      v-model="$store.state.user.deliveryAddressStreet"
                    />
                    <div class="verify">
                      <img
                        src="@/assets/images/icons/spravne.svg"
                        alt="Overenie správne"
                        v-if="addressDeliveryValue.ulica"
                      />
                      <img
                        src="@/assets/images/icons/nespravne.svg"
                        alt="Overenie nesprávne"
                        v-if="!addressDeliveryValue.ulica"
                      />
                    </div>
                  </div>

                  <label for="adresa">Čislo domu <span>*</span></label>
                  <div class="row last-row">
                    <div><img src="@/assets/images/icons/adresa.svg" /></div>
                    <input
                      @input="addressValueChange()"
                      @change="addressValueChange()"
                      type="text"
                      name="cisloDomuDelivery"
                      id=""
                      v-model="$store.state.user.deliveryAddressHouseNumber"
                    />
                    <div class="verify">
                      <img
                        src="@/assets/images/icons/spravne.svg"
                        alt="Overenie správne"
                        v-if="addressDeliveryValue.cisloDomu"
                      />
                      <img
                        src="@/assets/images/icons/nespravne.svg"
                        alt="Overenie nesprávne"
                        v-if="!addressDeliveryValue.cisloDomu"
                      />
                    </div>
                  </div>

                  <label for="priezvisko">Obec/mesto <span>*</span></label>
                  <div class="row last-row">
                    <div><img src="@/assets/images/icons/mesto.svg" /></div>
                    <input
                      @input="addressValueChange()"
                      @change="addressValueChange()"
                      type="text"
                      name="mestoDelivery"
                      id=""
                      v-model="$store.state.user.deliveryAddressCity"
                    />
                    <div class="verify">
                      <img
                        src="@/assets/images/icons/spravne.svg"
                        alt="Overenie správne"
                        v-if="addressDeliveryValue.mesto"
                      />
                      <img
                        src="@/assets/images/icons/nespravne.svg"
                        alt="Overenie nesprávne"
                        v-if="!addressDeliveryValue.mesto"
                      />
                    </div>
                  </div>
                  <p class="under">Mesto, lokalita</p>

                  <label for="date">PSČ <span>*</span></label>
                  <div class="row last-row">
                    <div>
                      <img src="@/assets/images/icons/adresa.svg" />
                    </div>
                    <input
                      @input="addressValueChange()"
                      @change="addressValueChange()"
                      type="text"
                      name="pscDelivery"
                      id=""
                      v-model="$store.state.user.deliveryAddressPostcode"
                    />
                    <div class="verify">
                      <img
                        src="@/assets/images/icons/spravne.svg"
                        alt="Overenie správne"
                        v-if="addressDeliveryValue.psc"
                      />
                      <img
                        src="@/assets/images/icons/nespravne.svg"
                        alt="Overenie nesprávne"
                        v-if="!addressDeliveryValue.psc"
                      />
                    </div>
                  </div>
                  <p class="under">Zadajte svoje PSČ. Napr. 013 03</p>

                  <label for="deliveryAddressState"
                    >Krajina <span>*</span></label
                  >
                  <div class="row last-row">
                    <div><img src="@/assets/images/icons/state.png" /></div>
                    <select
                      @change="addressValueChange()"
                      name="deliveryAddressState"
                      id="deliveryAddressState"
                      v-model="$store.state.user.deliveryAddressState"
                    >
                      <option disabled value="">-- Vyberte krajinu --</option>
                      <option value="Slovenská republika">
                        Slovenská republika
                      </option>
                      <option value="Česká republika">Česká republika</option>
                      <option value="Poľská republika">Poľská republika</option>
                    </select>
                    <div class="verify">
                      <img
                        src="@/assets/images/icons/spravne.svg"
                        alt="Overenie správne"
                        v-if="addressDeliveryValue.krajina"
                      />
                      <img
                        src="@/assets/images/icons/nespravne.svg"
                        alt="Overenie nesprávne"
                        v-if="!addressDeliveryValue.krajina"
                      />
                    </div>
                  </div>
                </div>

                <button class="button" type="submit">
                  <a>Uložiť</a>
                </button>
                <div @click="editAddress(false)" class="zrusit click">
                  <img src="@/assets/images/icons/zrusit.svg" alt="" />
                  <p>Zrušiť</p>
                </div>
              </form>
            </div>

            <div class="line horizontal"></div>

            <h4 class="m-right podrobnosti-objednavky">
              PODROBNOSTI O OBJEDNÁVKE
            </h4>
            <ul
              v-if="
                Object.keys(this.produktyData).length ==
                this.$store.state.userCart.length
              "
              class="cart"
            >
              <kosik-produkt
                v-for="item in $store.state.userCart"
                :key="item.id"
                :notes="{ id: item.id, count: item.count }"
                :produktyData="produktyData[item.id_delete]"
                :umiestnenie="'pokladna'"
              ></kosik-produkt>
            </ul>

            <div class="line horizontal"></div>

            <div class="gift_card">
              <h4 class="m-right">PLATBA</h4>
              <p class="platba-ako m-right">Ako chcete zaplatiť?</p>
              <div
                class="a_gift-cart"
                @click="zobrazPoukazkuInput = !zobrazPoukazkuInput"
              >
                <p>Pridať darčekové karty</p>
                <img src="@/assets/images/icons/darcek.svg" alt="" />
              </div>
              <div v-if="zobrazPoukazkuInput" class="voucher-input-wrap">
                <VoucherInput
                  v-model="kodDarcekovejPoukazky"
                  placeholder="Zadajte kód darčekovej poukážky"
                  @submit.stop.prevent="pridajDarcekovuPoukazku"
                />
                <button
                  @click.stop.prevent="pridajDarcekovuPoukazku"
                  class="button"
                >
                  Pridať
                </button>
              </div>
            </div>

            <div class="debet-cart">
              <div class="line"></div>
              <p>Karta</p>
              <div class="prijimame">
                <img src="@/assets/images/platby/platby1.png" alt="Maestro" />
                <img
                  src="@/assets/images/platby/platby2.png"
                  alt="Mastercadt"
                />
                <img src="@/assets/images/platby/platby3.png" alt="VISA" />
              </div>
            </div>
          </div>
          <div class="box-item right">
            <h4>ZHRNUTIE OBJEDNÁVKY</h4>

            <div class="zlavy">
              <p>Zľavy</p>
              <p
                v-if="!chcemZadatKupon && !uznanyKupon.stav"
                @click="chcemZadatKupon = true"
                class="uplatnenie-zlavy"
              >
                Uplatniť zľavu
              </p>
              <p
                v-if="chcemZadatKupon"
                @click="chcemZadatKupon = false"
                class="uplatnenie-zlavy"
              >
                Nechcem zľavu
              </p>
              <p
                v-if="uznanyKupon.stav"
                @click="zmazanieKuponu()"
                class="uplatnenie-zlavy"
              >
                Aktívny kupón
              </p>
            </div>

            <!-- Zľavy (kupóny aj poukážky) - jedna sekcia -->
            <div class="zlavy-list">
              <!-- Kupón -->
              <div v-if="uznanyKupon.stav" class="zlava-wrap">
                <div class="zlava-type-label">Kupón</div>
                <div
                  class="uznany-kupon flex-row"
                  @click="zmazanieKuponu()"
                  style="cursor: pointer"
                  title="Vymazať kupón"
                >
                  <span class="kupon-kod">
                    {{ uznanyKupon.nazov }} ({{ uznanyKupon.hodnota }}%)
                  </span>
                  <span class="zlava-suma">-{{ produktyCenaCouponLess }}€</span>
                </div>
              </div>

              <!-- Input na kupón (nerobím úpravy, len roztiahnuť na šírku) -->
              <form v-if="chcemZadatKupon" @submit.prevent="odoslanieKuponu()">
                <div class="zlava-type-label">Kupón</div>
                <div class="row full-width">
                  <input
                    type="text"
                    placeholder="Zľavový kód"
                    name="code"
                    id="code"
                    @input="kuponZmena()"
                    @change="kuponZmena()"
                    v-model="kuponKod"
                    class="full-width"
                  />
                </div>
              </form>

              <!-- Darčeková poukážka -->
              <div
                v-for="(poukazka, i) in giftCardsInCart"
                :key="poukazka.code"
                class="zlava-wrap"
              >
                <div class="zlava-type-label">Darčeková poukážka</div>
                <div
                  class="uznany-kupon flex-row one-gift-card"
                  @click="odstranDarcekovuPoukazku(poukazka.code)"
                  style="cursor: pointer"
                  title="Vymazať poukážku"
                >
                  <span class="kupon-kod">
                    🎁 {{ poukazka.code }}
                    <span v-if="poukazka.value">
                      ({{ Number(poukazka.value).toFixed(2).replace(".", ",") }}
                      €)
                    </span>
                  </span>
                  <span class="zlava-suma">{{ giftCardDiscounts[i] }}€</span>
                </div>
              </div>
            </div>
            <p class="small ta-left">
              Pokračovaním vyjadrujete súhlas so
              <a
                class="a"
                target="_blank"
                href="https://heligonkovaakademia.sk/obchodne-podmienky"
              >
                všeobecnými zmluvnými podmienkami spoločnosti
              </a>
            </p>
            <p class="small ta-left">
              Vaše osobné údaje budeme spracúvať v súlade s
              <a
                class="a"
                target="_blank"
                href="https://heligonkovaakademia.sk/obchodne-podmienky"
              >
                Vyhlásením o ochrane osobných údajov spoločnosti</a
              >
            </p>
            <div class="line horizontal"></div>

            <div v-if="sposobyDopravy.length != 0" class="sposob">
              <div class="suma">
                <p><b>Spôsob dopravy</b></p>
                <p></p>
              </div>
              <div class="size-options">
                <label v-for="item in sposobyDopravy" :key="item">
                  <input
                    type="radio"
                    :value="item.id"
                    v-model="sposobDopravy"
                    @change="nastavSposobDopravy(item.id)"
                  />
                  <span
                    >{{ item.name }} ( +
                    {{
                      (item.price / 100)
                        .toFixed(2)
                        .toString()
                        .replaceAll(".", ",")
                    }}
                    € )</span
                  >
                </label>
              </div>
            </div>
            <div
              class="line horizontal"
              v-if="sposobyDopravy.length != 0"
            ></div>

            <div class="suma">
              <p><b>Celkom</b></p>
              <p>{{ produktyCena }}€</p>
            </div>
            <div class="prijimame">
              <p>Prijímame</p>
              <img src="@/assets/images/platby/platby1.png" alt="Maestro" />
              <img src="@/assets/images/platby/platby2.png" alt="Mastercadt" />
              <img src="@/assets/images/platby/platby3.png" alt="VISA" />
            </div>

            <div @click="odoslaniePlatby()" class="button green">
              <a>Dokončiť nákup</a>
            </div>
            <p class="small">Odoslať objednávku s povinnosťou platby</p>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import TheHeadline from "@/components/Menu/TheHeadline.vue";
import KosikProdukt from "@/components/Functionality/KosikProdukt.vue";
import VoucherInput from "@/components/Functionality/VoucherInput.vue";

export default {
  async mounted() {
    window.scrollTo(0, 0);

    await this.nacitajGiftCardsInCart();
    this.nacitajInfoPrePokladnu();
  },
  components: { TheHeadline, KosikProdukt, VoucherInput },
  data() {
    return {
      editUserValue: false,
      editAddressValue: false,
      editAddressDeliveryValue: false,
      userValue: { meno: false, priezvisko: false, datum: false, phone: false },
      addressValue: {
        ulica: false,
        cisloDomu: false,
        mesto: false,
        psc: false,
        krajina: false,
      },
      addressDeliveryValue: {
        ulica: false,
        cisloDomu: false,
        mesto: false,
        psc: false,
        krajina: false,
      },
      obceData: [],
      produktyCenaCouponLess: "",
      gift_card_discount: "",
      produktyCena: "",
      produktyData: {},
      uznanyKupon: {},
      sposobDopravy: null,
      sposobyDopravy: [],
      zobrazPoukazkuInput: false,
      kodDarcekovejPoukazky: "",
      giftCardsInCart: [],
    };
  },

  computed: {
    giftCardDiscounts() {
      return this.giftCardsInCart.map((gc) => {
        const raw = parseFloat(String(gc.value).replace(",", ".")) || 0;
        return raw.toFixed(2).replace(".", ",");
      });
    },
  },

  methods: {
    async nacitajCenu() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "cart/getCartPriceAdvandec.php",
        headers: {},
      };

      try {
        const response = await axios.request(config);
        if (response.data && response.data.data) {
          this.produktyCenaCouponLess = response.data.data["coupon_discount"];
          this.produktyCena = response.data.data["price_total"];
          // >>> TOTO SI PRIDAJ <<<
          this.gift_card_discount = response.data.data["gift_card_discount"];
          // Debug, aby si videla hodnoty
          console.log("spravna cena je :>> ", response.data.data);
          console.log("gift_card_discount je: ", this.gift_card_discount);
        } else {
          console.log("Nedostali sme správne údaje z API.");
        }
      } catch (error) {
        console.log(error);
      }
    },
    async nacitajInfoPrePokladnu() {
      this.nacitajProdukty();
      await this.nacitajKupon();
      this.nacitajCenu();
    },
    nacitajProdukty() {
      this.produktyData = {};
      // Ak chceš zavolať metódu `nacitajDataOProduktoch` s ID produktov:
      this.$store.state.userCart.forEach((product) => {
        if (
          typeof product.id === "string" &&
          product.id.startsWith("gift_card")
        ) {
          const produkt = this.nacitajDataOProduktoch(
            product.id,
            product.count,
            product.id_delete,
            product.description
          );
          this.produktyData[product.id_delete] = produkt;
        } else {
          this.nacitajDataOProduktoch(
            product.id,
            product.count,
            product.id_delete,
            product.description
          );
        }
      });
    },
    async nacitajGiftCardsInCart() {
      try {
        const axios = require("axios");
        const res = await axios.get(
          this.$store.state.api + "cart/loadGiftCard.php"
        );
        if (Array.isArray(res.data.data)) {
          this.giftCardsInCart = res.data.data
            .map((item) => (typeof item === "string" ? JSON.parse(item) : item))
            .filter((gc) => !gc.used_timestamp); // NEPOUŽÍVAJ !gc.used_by
        } else {
          this.giftCardsInCart = [];
        }
      } catch (err) {
        this.giftCardsInCart = [];
      }
    },
    async pridajDarcekovuPoukazku() {
      if (
        !this.kodDarcekovejPoukazky ||
        this.kodDarcekovejPoukazky.length < 8
      ) {
        this.$store.state.SnackBarText =
          "Zadajte platný kód darčekovej poukážky.";
        return;
      }
      this.zobrazPoukazkuInput = false;
      try {
        const axios = require("axios");
        let res = await axios.get(
          this.$store.state.api + "cart/addGiftCard.php",
          {
            params: { code: this.kodDarcekovejPoukazky },
          }
        );
        if (
          res?.data?.status === "Request succesfull" ||
          res?.data?.status === "Request fullfiled"
        ) {
          this.$store.state.SnackBarText = "Darčeková poukážka bola pridaná!";
          this.kodDarcekovejPoukazky = "";
          await this.nacitajGiftCardsInCart();
          await this.nacitajCenu();
        } else if (res?.data?.status === "Request failed") {
          if (res.data.data === "Already used") {
            this.$store.state.SnackBarText = "Táto poukážka už bola použitá.";
          } else if (res.data.data === "Already in cart") {
            this.$store.state.SnackBarText = "Táto poukážka je už v košíku.";
          } else {
            this.$store.state.SnackBarText = "Neplatný kód alebo chyba.";
          }
        } else {
          this.$store.state.SnackBarText = "Neplatný kód alebo chyba.";
        }
      } catch (err) {
        this.$store.state.SnackBarText = "Chyba komunikácie so serverom.";
      }
    },
    async odstranDarcekovuPoukazku(code) {
      try {
        const axios = require("axios");
        const res = await axios.get(
          this.$store.state.api + "cart/removeGiftCard.php",
          { params: { code } }
        );
        if (res?.data?.status === "Request fullfiled") {
          this.$store.state.SnackBarText = "Poukážka bola vymazaná z košíka.";
          await this.nacitajGiftCardsInCart();
          await this.nacitajCenu();
        } else {
          this.$store.state.SnackBarText = "Nepodarilo sa odstrániť poukážku.";
        }
      } catch (err) {
        this.$store.state.SnackBarText = "Chyba pri mazaní poukážky.";
      }
    },
    nacitajDataOProduktoch(id, count, id_delete, description) {
      if (typeof id === "string" && id.startsWith("gift_card")) {
        // Získame hodnotu poukážky z id napr. "gift_card150" -> 150
        const hodnota = id.replace("gift_card", "");
        const hodnotaCent = Number(id.replace("gift_card", ""));
        const hodnotaEur = (hodnotaCent / 100).toFixed(2).replace(".", ",");

        return {
          id: id,
          id_delete: id_delete,
          name: "Darčeková poukážka",
          additional_text: "",
          hodnotaPoukazky: hodnota,
          count: count,
          description: description || "",
          price: hodnotaEur,
          details: JSON.stringify({
            typ: "gift_card",
            popis: "Darčeková poukážka pre milovníkov heligónky.",
            opis: "Poukážka, ktorú je možné uplatniť pri nákupe v našom Heli shope.",
            preKohoPopis: "",
            coSaPopis: "",
            technickyPopis: "",
            obsahPopis: "",
            farby: [],
            velkosti: [],
          }),
          virtuality: "true",
          category: "poukazky",
          deleted: "false",
          free: "false",
          new: "",
          difficulty: "0",
          expiration: "never",
          images: [
            "https://heligonkovaakademia.sk/data/images/poukazkaImage.png",
          ],
        };
      }
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/loadLimited.php/?id=" + id,
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          var id_index = id_delete;

          // Ukladanie dát o produkte do produktyData
          this.produktyData[id_index] = response.data.data;

          // Pridanie počtu (count) k produktu
          this.produktyData[id_index].count = count;
          // Pridanie počtu (count) k produktu
          this.produktyData[id_index].id_delete = id_delete;
          // Pridanie počtu (count) k produktu
          this.produktyData[id_index].description = description;

          // Formátovanie ceny na string s "," ako desatinný oddeľovač
          this.produktyData[id_index].price = this.produktyData[
            id_index
          ].price.replace(".", ",");

          //zistenie či je produkt virtuálny
          if (
            this.sposobyDopravy.length == 0 &&
            this.produktyData[id_index]?.virtuality == "false"
          ) {
            this.nacitajSposobyDopravy();
            this.nastavSposobDopravy(-1);
            this.editAddress(true);
            this.editUser(true);
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    nacitajSposobyDopravy() {
      const axios = require("axios");

      let config = {
        method: "get",
        url: this.$store.state.api + "/eshop/delivery.json",
      };

      axios
        .request(config)
        .then((response) => {
          this.sposobyDopravy = response.data; // Uloží získané dáta
          console.log("this.sposobyDopravy :>> ", this.sposobyDopravy);
        })
        .catch((error) => {
          console.error("Chyba pri načítaní spôsobov dopravy:", error);
        });
    },
    nastavSposobDopravy(id) {
      const axios = require("axios");
      let doprava = "";

      if (id === -1) {
        doprava = "";
      } else {
        doprava = "?delivery=" + id; // id = "01"
      }

      let config = {
        method: "get",
        url: `${this.$store.state.api}/cart/setDelivery.php` + doprava,
      };

      axios
        .request(config)
        .then((response) => {
          console.log("Spôsob dopravy bol nastavený:", response.data);

          if (id !== -1) {
            this.nacitajCenu();
          }

          this.sposobDopravy = response.data.data.delivery; // nechaj ako string, aby ostala "01"
        })
        .catch((error) => {
          console.error("Chyba pri nastavovaní dopravy:", error);
        });
    },
    async nacitajKupon() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "cart/loadCoupon.php",
        // headers: {},
      };

      try {
        const response = await axios.request(config);
        if (response.data.status === "Request sucessfull") {
          if (response.data.data.length === 0) {
            this.uznanyKupon = [];
            return;
          }

          console.log("response.data.data :>> ", response.data.data);

          var kupon = JSON.parse(response.data.data);
          console.log(kupon);
          this.uznanyKupon.nazov = kupon.code;

          // Prevod hodnoty na číslo s dvomi desatinnými miestami
          let newValue = parseFloat(kupon.value.replace(",", ".")).toFixed(2);

          // Zamenenie bodky za čiarku
          newValue = newValue.replace(".", ",");
          this.uznanyKupon.hodnota = newValue;

          this.uznanyKupon.hodnotaInt = parseFloat(kupon.value);

          console.log(kupon);
          console.log(this.uznanyKupon);

          this.uznanyKupon.stav = true;
        }
      } catch (error) {
        console.log(error);
      }
    },
    editUser(value) {
      this.editUserValue = value;
      this.isFormValidUserValue();
    },
    editAddress(value) {
      this.editAddressValue = value;
      if (this.$store.state.user.deliveryAddressCity == "") {
        this.editAddressDeliveryValue = false;
      } else {
        this.editAddressDeliveryValue = true;
      }
      this.isFormValidAddressValue();
    },
    async upravenieUser(platba) {
      if (this.isFormValidUserValue("Uloz")) {
        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();

        // Funkcia na korekciu hodnoty pred odoslaním
        const fixValue = (value) =>
          value === null || value === "null" ? "" : value;

        // Pridanie údajov z tabuľky
        data.append("name", fixValue(this.$store.state.user.name));
        data.append("surname", fixValue(this.$store.state.user.surname));
        data.append("phone", fixValue(this.$store.state.user.phone));
        data.append(
          "dateOfBirth",
          fixValue(this.$store.state.user.dateOfBirth)
        );

        // Pridanie údajov pre zabránenie zmazaniu
        data.append(
          "billingAddressStreet",
          fixValue(this.$store.state.user.billingAddressStreet)
        );
        data.append(
          "billingAddressHouseNumber",
          fixValue(this.$store.state.user.billingAddressHouseNumber)
        );
        data.append(
          "billingAddressCity",
          fixValue(this.$store.state.user.billingAddressCity)
        );
        data.append(
          "billingAddressPostcode",
          fixValue(this.$store.state.user.billingAddressPostcode)
        );
        data.append(
          "billingAddressState",
          fixValue(this.$store.state.user.billingAddressState)
        );
        // Pridanie údajov pre zabránenie zmazaniu delivery
        data.append(
          "deliveryAddressStreet",
          fixValue(this.$store.state.user.deliveryAddressStreet)
        );
        data.append(
          "deliveryAddressHouseNumber",
          fixValue(this.$store.state.user.deliveryAddressHouseNumber)
        );
        data.append(
          "deliveryAddressCity",
          fixValue(this.$store.state.user.deliveryAddressCity)
        );
        data.append(
          "deliveryAddressPostcode",
          fixValue(this.$store.state.user.deliveryAddressPostcode)
        );
        data.append(
          "deliveryAddressState",
          fixValue(this.$store.state.user.deliveryAddressState)
        );

        let config = {
          method: "post",
          maxBodyLength: Infinity,
          url:
            this.$store.state.api +
            "/user/info/updateAdditionalInformation.php",
          data: data,
        };

        try {
          const response = await axios.request(config);
          console.log(response);
          this.$store.dispatch("pouzivatelskeInformacie");
          this.editUserValue = false;
          if (platba == "Fyz") {
            return true; // Success
          }
          this.$store.state.SnackBarText = "Vaše údaje boli úspešne uložené";
          return true; // Success
        } catch (error) {
          console.log(error);
          return false; // Error
        }
      }
    },
    upravenieAddress() {
      if (this.isFormValidAddressValue("Uloz")) {
        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();

        // Funkcia na korekciu hodnoty pred odoslaním
        const fixValue = (value) =>
          value === null || value === "null" ? "" : value;

        //pridanie údajov pre zabránenie zmazaniu
        data.append("name", fixValue(this.$store.state.user.name));
        data.append("surname", fixValue(this.$store.state.user.surname));
        data.append("phone", fixValue(this.$store.state.user.phone));
        data.append(
          "dateOfBirth",
          fixValue(this.$store.state.user.dateOfBirth)
        );

        //pridanie údajov s tabuľky
        data.append(
          "billingAddressStreet",
          fixValue(this.$store.state.user.billingAddressStreet)
        );
        data.append(
          "billingAddressHouseNumber",
          fixValue(this.$store.state.user.billingAddressHouseNumber)
        );
        data.append(
          "billingAddressCity",
          fixValue(this.$store.state.user.billingAddressCity)
        );
        data.append(
          "billingAddressPostcode",
          fixValue(this.$store.state.user.billingAddressPostcode)
        );
        data.append(
          "billingAddressState",
          fixValue(this.$store.state.user.billingAddressState)
        );

        //delivery
        data.append(
          "deliveryAddressStreet",
          fixValue(this.$store.state.user.deliveryAddressStreet)
        );
        data.append(
          "deliveryAddressHouseNumber",
          fixValue(this.$store.state.user.deliveryAddressHouseNumber)
        );
        data.append(
          "deliveryAddressCity",
          fixValue(this.$store.state.user.deliveryAddressCity)
        );
        data.append(
          "deliveryAddressPostcode",
          fixValue(this.$store.state.user.deliveryAddressPostcode)
        );
        data.append(
          "deliveryAddressState",
          fixValue(this.$store.state.user.deliveryAddressState)
        );

        let config = {
          method: "post",
          maxBodyLength: Infinity,
          url:
            this.$store.state.api +
            "/user/info/updateAdditionalInformation.php",
          data: data,
        };

        axios
          .request(config)
          .then((response) => {
            console.log(response);
            this.$store.dispatch("pouzivatelskeInformacie");
            this.editAddressValue = false;
            this.$store.state.SnackBarText = "Vaše údaje boli úspešne uložené";

            return true;
          })
          .catch((error) => {
            console.log(error);

            return false;
          });

        return false;
      }
    },
    userValueChange() {
      this.isFormValidUserValue("Zmen");
    },
    addressValueChange() {
      this.isFormValidAddressValue("Zmen");
    },
    isFormValidUserValue(value) {
      // Získajte aktuálny čas
      // Získajte aktuálny čas
      const currentDate = new Date();

      // Získajte dátum narodenia ako objekt Date
      const birthDate = new Date(this.$store.state.user.dateOfBirth);

      // Vypočítajte rozdiel v rokoch
      const ageDifferenceInYears =
        currentDate.getFullYear() - birthDate.getFullYear();

      if (this.$store.state.user.name === "") {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Pre uloženie prosím vyplnte svoje meno";
          return false;
        } else {
          this.userValue.meno = false;
        }
      } else if (this.$store.state.user.name.length <= 2) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Prosím zadajte spráne svoje meno. Dĺžka mena musí byť vačšia ako 2 znaky";
          return false;
        } else {
          this.userValue.meno = false;
        }
      } else {
        this.userValue.meno = true;
      }

      if (this.$store.state.user.surname === "") {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Pre uloženie prosím vyplnte svoje priezvisko";
          return false;
        } else {
          this.userValue.priezvisko = false;
        }
      } else if (this.$store.state.user.surname.length <= 2) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Prosím zadajte spráne svoje priezvisko. Dĺžka priezviska musí byť vačšia ako 2 znaky";
          return false;
        } else {
          this.userValue.priezvisko = false;
        }
      } else {
        this.userValue.priezvisko = true;
      }

      if (
        this.$store.state.user.dateOfBirth == null ||
        this.$store.state.user.dateOfBirth == ""
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Pre uloženie prosím vyplnte svoj dátum narodenia";
          return false;
        } else {
          this.userValue.datum = false;
        }
      } else if (
        ageDifferenceInYears < 12 ||
        ageDifferenceInYears > 120 ||
        (ageDifferenceInYears === 12 &&
          currentDate <
            new Date(
              currentDate.getFullYear(),
              birthDate.getMonth(),
              birthDate.getDate()
            ))
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Prosím zadajte správny dátum narodenia. Pre zaplatenie musíte mať viac ako 12 rokov";
          return false;
        } else {
          this.userValue.datum = false;
        }
      } else {
        this.userValue.datum = true;
      }

      let phone = (this.$store.state.user.phone ?? "").replace(/\s+/g, "");

      // Automatické formátovanie pre SK, CZ, PL
      if (/^\+4219[0-9]{8}$/.test(phone)) {
        phone = phone.replace(
          /^(\+421)(9[0-9]{2})([0-9]{3})([0-9]{3})$/,
          "$1 $2 $3 $4"
        );
      } else if (/^09[0-9]{8}$/.test(phone)) {
        phone = phone.replace(/^(09[0-9]{2})([0-9]{3})([0-9]{3})$/, "$1 $2 $3");
      } else if (/^\+420[7-9][0-9]{8}$/.test(phone)) {
        phone = phone.replace(
          /^(\+420)([7-9][0-9]{2})([0-9]{3})([0-9]{3})$/,
          "$1 $2 $3 $4"
        );
      } else if (/^[7-9][0-9]{8}$/.test(phone)) {
        phone = phone.replace(
          /^([7-9][0-9]{2})([0-9]{3})([0-9]{3})$/,
          "$1 $2 $3"
        );
      } else if (/^\+48[5-9][0-9]{8}$/.test(phone)) {
        phone = phone.replace(
          /^(\+48)([5-9][0-9]{2})([0-9]{3})([0-9]{3})$/,
          "$1 $2 $3 $4"
        );
      } else if (/^[5-9][0-9]{8}$/.test(phone)) {
        phone = phone.replace(
          /^([5-9][0-9]{2})([0-9]{3})([0-9]{3})$/,
          "$1 $2 $3"
        );
      }

      // Uložíme upravené číslo späť do Vuex
      this.$store.state.user.phone = phone;

      // Validácia správneho čísla pre SK, CZ, PL
      if (
        !/^(\+421\s?9[0-9]{2}\s?[0-9]{3}\s?[0-9]{3}|09[0-9]{2}\s?[0-9]{3}\s?[0-9]{3}|\+420\s?[7-9][0-9]{2}\s?[0-9]{3}\s?[0-9]{3}|[7-9][0-9]{2}\s?[0-9]{3}\s?[0-9]{3}|\+48\s?[5-9][0-9]{2}\s?[0-9]{3}\s?[0-9]{3}|[5-9][0-9]{2}\s?[0-9]{3}\s?[0-9]{3})$/.test(
          phone
        )
      ) {
        if (value == "Uloz") {
          //pre preskočenie zadania tel čísla
          if (this.sposobyDopravy.length == 0) {
            return true;
          }
          this.$store.state.SnackBarText =
            "Prosím zadajte správne telefónne číslo. Podporované formáty: +421 900 000 000";
          return false;
        }
        this.userValue.phone = false;
      } else {
        this.userValue.phone = true;
      }

      return true;
    },
    isFormValidAddressValue(value) {
      if (
        this.$store.state.user.billingAddressStreet === "" ||
        this.$store.state.user.billingAddressStreet == undefined ||
        this.$store.state.user.billingAddressStreet == null ||
        this.$store.state.user.billingAddressStreet.includes("null")
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Pre uloženie prosím vyplnte vašu adresu";
          return false;
        } else {
          this.addressValue.ulica = false;
        }
      } else {
        this.addressValue.ulica = true;
      }

      if (
        this.$store.state.user.billingAddressHouseNumber == "" ||
        this.$store.state.user.billingAddressHouseNumber == undefined ||
        this.$store.state.user.billingAddressHouseNumber == null ||
        this.$store.state.user.billingAddressHouseNumber.includes("null")
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Pre uloženie prosím vyplnte vašu adresu";
          return false;
        } else {
          this.addressValue.cisloDomu = false;
        }
      } else if (
        /^[0-9/]+$/.test(this.$store.state.user.billingAddressHouseNumber) ==
        false
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Pre uloženie prosím vyplnte správne vaše číslo domu";
          return false;
        } else {
          this.addressValue.cisloDomu = false;
        }
      } else {
        this.addressValue.cisloDomu = true;
      }

      if (
        this.$store.state.user.billingAddressCity == "" ||
        this.$store.state.user.billingAddressCity == undefined ||
        this.$store.state.user.billingAddressCity == null ||
        this.$store.state.user.billingAddressCity.includes("null")
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Pre uloženie prosím vyplnte vašu adresu";
          return false;
        } else {
          this.addressValue.mesto = false;
        }
      } else if (this.$store.state.user.billingAddressCity <= 2) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Pre uloženie prosím vyplnte správne vašu obec/mesto";
          return false;
        } else {
          this.addressValue.mesto = false;
        }
      } else {
        this.addressValue.mesto = true;

        if (
          this.$store.state.user.billingAddressPostcode == "" ||
          this.$store.state.user.billingAddressPostcode == undefined ||
          this.$store.state.user.billingAddressPostcode == null ||
          this.$store.state.user.billingAddressPostcode.includes("null")
        ) {
          this.addPSC("billing");
        }
      }

      if (
        this.$store.state.user.billingAddressPostcode === "" ||
        this.$store.state.user.billingAddressPostcode == undefined ||
        this.$store.state.user.billingAddressPostcode == null ||
        this.$store.state.user.billingAddressPostcode.includes("null")
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Pre uloženie prosím vyplnte vašu adresu";
          return false;
        } else {
          this.addressValue.psc = false;
        }
      } else if (
        /\d{3} \d{2}$/.test(this.$store.state.user.billingAddressPostcode) ==
        false
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Neplatné PSČ, PSČ musí mať tvar 111 22";
          return false;
        } else {
          this.addressValue.psc = false;
        }
      } else {
        this.addressValue.psc = true;
      }
      if (
        this.$store.state.user.billingAddressState === "" ||
        this.$store.state.user.billingAddressState == undefined ||
        this.$store.state.user.billingAddressState == null
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Prosím vyberte krajinu fakturačnej adresy";
          return false;
        } else {
          this.addressValue.krajina = false;
        }
      } else {
        this.addressValue.krajina = true;
      }

      ///delivery iná podmienky

      if (this.editAddressDeliveryValue == true) {
        if (
          this.$store.state.user.deliveryAddressStreet === "" ||
          this.$store.state.user.deliveryAddressStreet == undefined ||
          this.$store.state.user.deliveryAddressStreet == null ||
          this.$store.state.user.deliveryAddressStreet.includes("null")
        ) {
          if (value == "Uloz") {
            this.$store.state.SnackBarText =
              "Pre uloženie prosím vyplnte vašu doručovaciu adresu";
            return false;
          } else {
            this.addressDeliveryValue.ulica = false;
          }
        } else {
          this.addressDeliveryValue.ulica = true;
        }

        if (
          this.$store.state.user.deliveryAddressHouseNumber == "" ||
          this.$store.state.user.deliveryAddressHouseNumber == undefined ||
          this.$store.state.user.deliveryAddressHouseNumber == null ||
          this.$store.state.user.deliveryAddressHouseNumber.includes("null")
        ) {
          if (value == "Uloz") {
            this.$store.state.SnackBarText =
              "Pre uloženie prosím vyplnte vašu doručovaciu adresu";
            return false;
          } else {
            this.addressDeliveryValue.cisloDomu = false;
          }
        } else if (
          /^[0-9/]+$/.test(this.$store.state.user.deliveryAddressHouseNumber) ==
          false
        ) {
          if (value == "Uloz") {
            this.$store.state.SnackBarText =
              "Pre uloženie prosím vyplnte správne vaše doručovacie číslo domu";
            return false;
          } else {
            this.addressDeliveryValue.cisloDomu = false;
          }
        } else {
          this.addressDeliveryValue.cisloDomu = true;
        }

        if (
          this.$store.state.user.deliveryAddressCity == "" ||
          this.$store.state.user.deliveryAddressCity == undefined ||
          this.$store.state.user.deliveryAddressCity == null ||
          this.$store.state.user.deliveryAddressCity.includes("null")
        ) {
          if (value == "Uloz") {
            this.$store.state.SnackBarText =
              "Pre uloženie prosím vyplnte vašu doručovaciu adresu";
            return false;
          } else {
            this.addressDeliveryValue.mesto = false;
          }
        } else if (this.$store.state.user.deliveryAddressCity <= 2) {
          if (value == "Uloz") {
            this.$store.state.SnackBarText =
              "Pre uloženie prosím vyplnte správne vašu obec/mesto";
            return false;
          } else {
            this.addressDeliveryValue.mesto = false;
          }
        } else {
          this.addressDeliveryValue.mesto = true;

          if (
            this.$store.state.user.deliveryAddressPostcode == "" ||
            this.$store.state.user.deliveryAddressPostcode == undefined ||
            this.$store.state.user.deliveryAddressPostcode == null ||
            this.$store.state.user.deliveryAddressPostcode.includes("null")
          ) {
            this.addPSC("delivery");
          }
        }

        if (
          this.$store.state.user.deliveryAddressPostcode === "" ||
          this.$store.state.user.deliveryAddressPostcode == undefined ||
          this.$store.state.user.deliveryAddressPostcode == null ||
          this.$store.state.user.deliveryAddressPostcode.includes("null")
        ) {
          if (value == "Uloz") {
            this.$store.state.SnackBarText =
              "Pre uloženie prosím vyplnte vašu doručovaciu adresu";
            return false;
          } else {
            this.addressDeliveryValue.psc = false;
          }
        } else if (
          /\d{3} \d{2}$/.test(this.$store.state.user.deliveryAddressPostcode) ==
          false
        ) {
          if (value == "Uloz") {
            this.$store.state.SnackBarText =
              "Neplatné PSČ, PSČ musí mať tvar 111 22";
            return false;
          } else {
            this.addressDeliveryValue.psc = false;
          }
        } else {
          this.addressDeliveryValue.psc = true;
        }
      } else {
        this.$store.state.user.deliveryAddressPostcode = "";
        this.$store.state.user.deliveryAddressCity = "";
        this.$store.state.user.deliveryAddressHouseNumber = "";
        this.$store.state.user.deliveryAddressStreet = "";
      }

      if (this.editAddressDeliveryValue == true) {
        if (
          this.$store.state.user.deliveryAddressState === "" ||
          this.$store.state.user.deliveryAddressState == undefined ||
          this.$store.state.user.deliveryAddressState == null
        ) {
          if (value == "Uloz") {
            this.$store.state.SnackBarText =
              "Prosím vyberte krajinu dodacej adresy";
            return false;
          } else {
            this.addressDeliveryValue.krajina = false;
          }
        } else {
          this.addressDeliveryValue.krajina = true;
        }
      } else {
        // reset dodacej krajiny, ak nie je aktivované pole "iná dodacia adresa"
        this.$store.state.user.deliveryAddressState = "";
        this.addressDeliveryValue.krajina = true; // aby sa nezobrazila červená ikonka
      }

      return true;
    },
    isFormValidDeliveryValue() {
      if (
        this.sposobyDopravy.length != 0 &&
        (this.sposobDopravy == 0 || this.sposobDopravy == null)
      ) {
        this.$store.state.SnackBarText = "Prosím vyberte spôsob dopravy";
        return false;
      } else {
        return true;
      }
    },
    async addPSC(type) {
      // Dynamicky nastavíme premenné podľa parametra 'type'
      const cityKey =
        type === "billing" ? "billingAddressCity" : "deliveryAddressCity";
      const postcodeKey =
        type === "billing"
          ? "billingAddressPostcode"
          : "deliveryAddressPostcode";

      try {
        // Načítanie dát zo súboru obce.json
        const response = await fetch(
          "https://heligonkovaakademia.sk/data/obce.json"
        );
        this.obceData = await response.json();

        // Overíme, či zadaná obec existuje
        this.obecExists = this.obceData.some(
          (entry) =>
            entry.OBEC.toLowerCase() ===
            this.$store.state.user[cityKey].toLowerCase()
        );

        if (this.obecExists) {
          const obecData = this.obceData.find(
            (entry) =>
              entry.OBEC.toLowerCase() ===
              this.$store.state.user[cityKey].toLowerCase()
          );

          // Nastavíme PSČ podľa zvolenej adresy
          this.$store.state.user[postcodeKey] = obecData ? obecData.PSC : "";

          // Ak PSČ existuje, nastavíme addressValue.psc na true
          if (this.$store.state.user[postcodeKey] != null) {
            this.addressValue.psc = true;
          }
        } else {
          this.$store.state.user[postcodeKey] = "";
        }
      } catch (error) {
        this.$store.state.user[postcodeKey] = "";
      }
    },
    isFormSaveValue() {
      if (this.editAddressValue) {
        this.$store.state.SnackBarText =
          "Pred zaplatením prosím uložte vašu adresu";
        return false;
      }

      if (this.editUserValue) {
        this.$store.state.SnackBarText =
          "Pred zaplatením prosím uložte vaše informácie";
        return false;
      }

      return true;
    },

    async odoslaniePlatby() {
      if (this.sposobyDopravy.length != 0) {
        if (
          this.isFormValidAddressValue("Uloz") &&
          (await this.upravenieUser("Fyz")) &&
          this.isFormValidDeliveryValue()
        ) {
          this.$store.dispatch("vytvorPlatbu");
        }
      } else {
        if (
          this.isFormValidAddressValue("Uloz") &&
          this.isFormValidAddressValue("Uloz") &&
          this.isFormSaveValue()
        ) {
          this.$store.dispatch("vytvorPlatbu");
        }
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.box {
  margin-top: 3em;
  padding: 0 3%;
  align-items: flex-start;
  justify-content: space-around;
}

.box:has(.cart) {
  gap: 10%;
}

.scroll > .line {
  width: 80%;
  margin: auto;
}

h4 {
  font-size: 1.9em;
  text-align: center;

  line-height: 115%;
  /* 2.51563rem */
  letter-spacing: 0.1em;
}

.ta-left {
  text-align: left;
}

.m-right {
  margin-right: auto;
}

.mobile h4 {
  margin: 0.5em 0 0;
}

.box-item {
  justify-content: center !important;
  gap: 7%;
}

.box-item:last-child > div:not(.button) {
  display: flex;
  width: 100%;
  align-items: center;
  justify-content: flex-start;
  margin: 0.6em auto;

  & p:last-child {
    margin-left: auto;
  }
}

section p {
  text-align: center;
  font-size: 1.5625em;
  font-weight: 400;
  line-height: 115%;
}

.button p {
  font-size: 1em;
}

.prijimame p,
.small {
  font-size: 1em;
  margin: 0.6em 0;
}

.prijimame {
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  align-items: center;
  gap: 5%;

  img:nth-child(1) {
    width: auto;
    height: 1.75em;
  }

  img:nth-child(2) {
    width: auto;
    height: 1.4375em;
  }

  img:nth-child(3) {
    width: auto;
    height: 1.4375em;
  }
}

.button {
  margin-top: 1em;
  padding: 0.4em 2em;
}

.button:not(.login) a {
  font-size: 90%;
}

select {
  font-size: 1.4em;
  font-style: normal;
  font-weight: 300;
  line-height: 115%;
  background: transparent;
  border: none;
  padding: 0.4em 0.5em;
  text-align: left;
  width: 80%;
}

.nadpis {
  font-size: 2.1875em;
  font-style: normal;
  font-weight: 600;
}

.right {
  flex: 1.5;
  max-width: 24em;
  min-width: 24em;

  position: sticky;
  top: 0;
}

.left {
  flex: 2;
  max-width: 35em;
  align-self: center;

  & > p:nth-child(2) {
    margin: 0.7em 0;
  }

  .button {
    margin-top: 0;
  }
}

//košík

.cart {
  width: 100%;
  display: flex;
  gap: 1.5em;
  flex-direction: column;
  height: auto;
  margin-bottom: 1em;
}

.cart-item {
  display: flex;
  gap: 2%;
  justify-content: space-between;
  margin-bottom: 1em;

  .line {
    width: 0.3rem;
  }

  img:first-child {
    width: 10vw;
    max-width: 8em;
    height: 100%;
  }

  img:last-child {
    align-self: flex-start;
    width: 1em;
    transition-duration: 0.1s;
    cursor: pointer;

    &:hover {
      transform: scale(1.1);
      transition-duration: 0.2s;
    }
  }

  p {
    text-align: left;
  }
}

.nazov {
  font-size: 1.875em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  /* 2.15625rem */
  letter-spacing: 0.05em;
}

.piesen,
.cena {
  font-size: 1.4375em;
  font-style: normal;
  font-weight: 600;
  line-height: 120%;
  /* 1.725rem */
}

.nazov-piesne {
  font-size: 1.25em;
  font-style: normal;
  font-weight: 600;
  line-height: 120%;
  /* 1.5rem */
}

.platba-ako {
  font-size: 1.55em;
  font-style: normal;
  font-weight: 600;
  line-height: 120%;
  /* 1.875rem */
}

.specifikacia {
  margin-right: auto;
  margin-left: 4%;
}

//kosik ↑
//pokladna ↓
.user-info-box {
  display: flex;
  align-items: flex-start;
  width: 100%;
  flex-direction: column;
  margin: 1.3em 0;
}

.uznany-kupon {
  font-size: 90%;
  background: #90ca50;
  padding: 0.5em 1em;
  border-radius: 1em;
  box-sizing: border-box;
  transition-duration: 0.5s;
}

.user-info-head {
  display: flex;
  width: 100%;
  justify-content: space-between;
  margin: 0.3em 0 0.4em;
  align-items: center;

  img {
    width: 1.5em;
  }
}

.user-info {
  margin-bottom: 1em;
}

.user-info p {
  text-align: left;
}

.gift_card {
  flex-direction: column;
  align-items: flex-start !important;
  gap: 1em;
  display: flex;
  width: 100%;
  margin-top: 2em;

  h4 {
    margin: 0 0 1em 0;
  }

  .platba-ako {
    font-weight: bold;
    margin-bottom: 0.5em;
  }

  .a_gift-cart {
    width: 100%;
    display: flex;
    justify-content: space-between;
    cursor: pointer;
  }

  .a_gift-cart p:hover {
    text-decoration: underline;
  }
}

:deep(.voucher-input-wrap) {
  width: 100%;

  .button {
    margin-top: 0.5em;
    justify-content: center;
    width: 60%;
    font-family: "Adumu", sans-serif;
  }
}

:deep(.voucher-input) {
  width: 100%;
  align-items: flex-start;

  .row {
    width: 100%;
  }
}

.added-gift-cards {
  width: 100%;
  flex-direction: column;

  .one-gift-card {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 1.2em;
  }

  .remove-gift-card {
    cursor: pointer;
  }
}

.debet-cart {
  display: flex;
  width: 100%;
  align-items: center;
  gap: 3%;
  height: 3em;
  margin-top: 1em;

  p {
    font-size: 1.55em;

    font-weight: 700;
    line-height: 120%;
    /* 1.875rem */
  }

  .prijimame {
    margin-left: auto;
    font-size: 90%;
  }

  .line {
    width: 0.3rem;
    align-self: stretch;
  }
}

//formulár
form {
  max-width: unset;
  display: flex;
  flex-direction: column;
  align-items: center;

  .verify {
    background: none;
    box-shadow: none;

    img {
      transform: scale(1.1);
    }
  }

  button.button {
    margin-top: 0.5em !important;
    margin-bottom: 0.8em;
  }

  .zrusit {
    display: flex;
    gap: 10%;
    align-items: center;
    justify-content: center;

    img {
      width: 1.2em;
    }
  }
}

.under {
  font-size: 1em;

  font-weight: 400;
  line-height: 115%;
  /* 1.22188rem */
  margin-right: auto;
  margin-bottom: 0.3em;
}

.row {
  margin: 0 0 0.9em 0;
  width: 100%;
}

.last-row {
  margin: 0 0 0.6em 0;
}

form label {
  font-size: 1.5em;
  font-style: normal;
  font-weight: 400;
  line-height: 115%;
  margin-right: auto;

  span {
    color: #f26d24;
  }
}

//chcekbox custom delivery
/* The container */
.checkbox {
  display: block;
  position: relative;
  padding-left: 2em;
  margin-bottom: 0.8em;
  margin: 1em;
  cursor: pointer;
  font-size: 1.5em;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.checkbox input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 1.1em;
  width: 1.1em;
  background-color: rgba($color: #51a327, $alpha: 0.47);
  border-radius: 0.5em;
}

/* On mouse-over, add a grey background color */
.checkbox:hover input ~ .checkmark {
  background-color: rgba($color: #51a327, $alpha: 0.77);
}

/* When the checkbox is checked, add a blue background */
.checkbox input:checked ~ .checkmark {
  background-color: #89c14d;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.checkbox input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.checkbox .checkmark:after {
  left: 0.35em;
  top: 0.2em;
  width: 0.2em;
  height: 0.4em;
  border: solid #fff;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}

//sposob dopravy form

.size-options {
  display: flex;
  gap: 10px;
  margin: 1em auto 1.3em 0;
  width: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.size-options label {
  background-color: #ddd;
  color: black;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  text-align: center;
  transition: background-color 0.3s, color 0.3s;

  font-size: 1.2em;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 2.2em;
  width: 2.1em;

  display: flex;
  flex-direction: row;
  align-items: center;
  width: 100%;
  justify-content: center;
  // padding: 0.3em;
}

.size-options input {
  display: none;
}

.size-options input:checked + span {
  background-color: #fef35a;
  color: black;
  border-radius: 8px;

  width: -webkit-fill-available;
  padding: 0.35em 0.7em;

  display: flex;
  align-items: center;
  justify-content: center;
  // height: -webkit-fill-available;
  height: inherit;
}

.zlava-type-label {
  font-size: 1.12em;
  font-weight: 700;
  color: #212121;
  margin-bottom: 0.5em;
  margin-left: 0.2em;
  margin-top: 0.4em;
  text-align: left;
  letter-spacing: 0.02em;
  width: 100%;
}

.zlava-type-label.left {
  text-align: left;
  align-items: flex-start;
}

.zlava-wrap {
  margin-bottom: 0.44em;
  width: 100%;
  align-items: flex-start;
  display: flex;
  flex-direction: column;
}

.zlavy-list {
  width: 100%;
  margin-top: 0.5em;
  display: flex;
  flex-direction: column;
  gap: 0.15em;
  align-items: flex-start;
}

.uznany-kupon.flex-row,
.one-gift-card.flex-row {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 1em;
  background: #90ca50;
  border-radius: 1em;
  font-size: 1.2em;
  box-sizing: border-box;
  padding: 0.25em 0.7em;
  cursor: pointer;
  transition: background 0.23s, transform 0.13s;
  width: 100%;

  &:hover {
    background: #9fd266;
    transform: scale(0.97);
  }
}

.zlava-type {
  font-size: 0.95em;
  font-weight: 600;
  color: #2d4422;
  margin-right: 1em;
  min-width: 5.2em;
}

.kupon-kod {
  font-size: 0.98em;
  font-weight: 500;
  color: #232323;
  flex: 1 1 auto;
  text-align: left;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  min-width: 7em;
}

.zlava-suma {
  font-size: 1.06em;
  font-weight: bold;
  color: #fff134;
  margin-left: auto;
  min-width: 4.2em;
  text-align: right;
}

.uznany-kupon,
.active-discount {
  font-size: 90%;
  background: #90ca50;
  padding: 0.5em 1em;
  border-radius: 1em;
  box-sizing: border-box;
  transition-duration: 0.5s;

  &:hover {
    transform: scale(0.97);
    transition-duration: 0.2s;
    background: #9fd266;
    cursor: pointer;
  }
}

.sposob .suma {
  display: flex;
  justify-content: flex-start;
  width: 100%;
}

.sposob {
  display: flex;
  width: 100%;
  align-items: center;
  justify-content: flex-start;
  margin: 0.6em auto;
  flex-direction: column;
}

.delivery-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  // align-items: flex-start;
}

@media only screen and (max-width: 1550px) {
  h4 {
    letter-spacing: 0.06em;
    font-size: 1.8em;
  }
}

@media only screen and (max-width: 1420px) {
  .box:has(.cart) {
    font-size: 85%;
  }
}

@media screen and (max-width: 1000px) {
  .second img {
    width: 16vw;
  }

  section p {
    margin: 0.3em 0;
    margin-right: auto;
  }

  .box {
    align-items: center;
  }

  .small {
    font-size: 1.4em;
  }

  .left,
  .right {
    max-width: none;
    min-width: unset;

    width: 100%;
  }

  .nadpis,
  h4 {
    margin-bottom: 0.8em;
  }

  .right .button {
    padding: 0.5em 1.5em;
  }

  .left {
    margin-bottom: 5em;
    margin-top: 2em;
  }

  .zlava-type-label .left {
    margin-bottom: 0.5em !important;
  }

  .cart-item img:first-child {
    width: 12em;
  }

  .cart-item img:last-child {
    width: 1.5em;
  }

  h4 {
    font-size: 3.5vw;
  }

  h4.podrobnosti-objednavky {
    margin-top: 0.5em;
    font-size: 3vw;
  }
}

@media only screen and (max-width: 750px) {
  .second img {
    width: 23vw;
  }

  .mobile {
    section p {
      margin: 0.3em 0;
    }

    .box {
      align-items: center;
    }

    .small {
      font-size: 3vw;
      letter-spacing: 0.1vw;
      margin-top: 1em;
    }

    .left,
    .right {
      max-width: none;
      min-width: unset;

      width: 100%;
    }

    .nadpis,
    h4 {
      margin-bottom: 0.8em;
    }

    .right .button {
      padding: 0.7em 1.5em 0.5em;
    }

    .left {
      margin-bottom: 3em;
      margin-top: 0;
    }

    form {
      width: 100%;
      max-width: unset;
    }
  }

  .mobile section {
    margin-bottom: 10em;
    height: auto;
  }

  .mobile .scroll {
    padding-bottom: 4em;
  }

  .cart-item img:first-child {
    width: 10em;
  }

  h4.podrobnosti-objednavky {
    font-size: 5vw;
  }

  .mobile select {
    font-size: 1.7em;
  }

  .zlava-type-label .left {
    margin-bottom: 0.5em !important;
  }
}

@media (max-width: 600px) {
  .uznany-kupon.flex-row,
  .one-gift-card.flex-row {
    font-size: 1.2em;
    padding: 0.19em 0.45em;
    gap: 0.4em;
  }

  .zlava-type {
    font-size: 1.2em;
    min-width: 3.8em;
    margin-right: 0.6em;
  }

  .kupon-kod {
    font-size: 1.2em;
    min-width: 5em;
  }

  .zlava-suma {
    font-size: 1.2em;
    min-width: 2.5em;
  }
}

@media only screen and (max-width: 450px) {
  .mobile section h4 {
    font-size: 7vw;
  }

  .mobile section h4.podrobnosti-objednavky {
    font-size: 5.2vw;
  }

  .cart-item img:first-child {
    width: 8em;
  }

  .mobile .nazov {
    letter-spacing: normal;
  }
}

@media only screen and (max-width: 350px) {
  #Pokladňa
    > section
    > div
    > div.box
    > div.box-item.left
    > div:nth-child(3)
    > div.user-info-head
    > p {
    font-size: 1.7em;
  }
}
</style>
