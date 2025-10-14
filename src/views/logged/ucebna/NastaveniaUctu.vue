<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <h1>Nastavenie účtu</h1>
      <h5>Tu môžete spravovať svoj účet a odbery</h5>
      <div class="line horizontal w-80"></div>

      <div class="profile">
        <ProfilePhoto />
      </div>
      <div class="line horizontal w-50"></div>

      <div class="obsah">
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
            <p class="small-nazov">Email:</p>
            <p class="nazov-piesne">
              {{ $store.state.user.email }}
            </p>
            <p v-if="!editUserValue" class="small-nazov">Krstné meno:</p>
            <p v-if="!editUserValue" class="nazov-piesne">
              {{
                $store.state.user.name == "" || $store.state.user.name == null
                  ? "Meno"
                  : $store.state.user.name
              }}
            </p>
            <p v-if="!editUserValue" class="small-nazov">Priezvisko:</p>
            <p v-if="!editUserValue" class="nazov-piesne">
              {{
                $store.state.user.surname == "" ||
                $store.state.user.surname == null
                  ? "Priezvisko"
                  : $store.state.user.surname
              }}
            </p>
            <p v-if="!editUserValue" class="small-nazov">Mobil:</p>
            <p v-if="!editUserValue" class="nazov-piesne">
              {{
                $store.state.user.phone == "" || $store.state.user.phone == null
                  ? "+421 900 000 000"
                  : $store.state.user.phone
              }}
            </p>
            <p v-if="!editUserValue" class="small-nazov">Dátum narodenia:</p>
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
            <p v-if="!editAddressValue" class="small-nazov">
              Ulica a číslo domu:
            </p>
            <p v-if="!editAddressValue" class="nazov-piesne">
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
            <p v-if="!editAddressValue" class="small-nazov">Mesto / Obec</p>
            <p v-if="!editAddressValue" class="nazov-piesne">
              {{
                $store.state.user.billingAddressCity == undefined ||
                $store.state.user.billingAddressCity == null
                  ? "Žiadna adresa"
                  : $store.state.user.billingAddressCity
              }}
            </p>
            <p v-if="!editAddressValue" class="small-nazov">PSČ:</p>
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
            <p v-if="!editAddressValue" class="small-nazov">Krajina:</p>
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
            <p v-if="!editAddressValue" class="small-nazov">
              Ulica a číslo domu:
            </p>
            <p v-if="!editAddressValue" class="nazov-piesne">
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
            <p v-if="!editAddressValue" class="small-nazov">Mesto / Obec</p>
            <p v-if="!editAddressValue" class="nazov-piesne">
              {{
                $store.state.user.deliveryAddressCity == undefined ||
                $store.state.user.deliveryAddressCity == null
                  ? "Žiadna adresa"
                  : $store.state.user.deliveryAddressCity
              }}
            </p>
            <p v-if="!editAddressValue" class="small-nazov">PSČ:</p>
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
            <p v-if="!editAddressValue" class="small-nazov">Krajina:</p>
            <p v-if="!editAddressValue" class="nazov-piesne">
              {{
                $store.state.user.deliveryAddressState || "Slovenská republika"
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
                <option value="Slovenská republika">Slovenská republika</option>
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

            <label for="editAddressDeliveryValue" class="checkbox">
              Iná dodacia adresa
              <input
                type="checkbox"
                name="editAddressDeliveryValue"
                id="editAddressDeliveryValue"
                checked="checked"
                v-model="editAddressDeliveryValue"
              />
              <span class="checkmark"></span>
            </label>

            <p v-if="editAddressDeliveryValue" class="nazov">Dodacia adresa</p>

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

        <div class="user-info-box">
          <div class="user-info-head">
            <p class="nazov">Odoberanie Noviniek</p>
            <img
              @click="editNews(true)"
              v-if="!editNewsValue"
              src="@/assets/images/icons/upravit.svg"
              alt=""
              class="click"
            />
          </div>
          <div class="user-info">
            <p v-if="!editNewsValue" class="small-nazov">
              Odoberanie noviniek formou emailu
            </p>
            <p v-if="!editNewsValue" class="nazov-piesne">
              {{
                $store.state.user.ads == true
                  ? "Áno, ponechajte moje odbery."
                  : "Nie, neodoberám novinky"
              }}
            </p>
          </div>
          <form @submit.prevent="editNewsSave" v-if="editNewsValue">
            <!-- Checkbox pre odoberanie noviniek -->
            <div class="checkbox-container">
              <input
                type="checkbox"
                id="newsletterCheckbox"
                class="newsletter"
                v-model="chcemOdoberat"
                :checked="$store.state.user.ads"
                @change="editNewsChange('chcem')"
              />
              <label for="newsletterCheckbox">Chcem odoberať novinky</label>
            </div>

            <!-- Checkbox pre neodoberanie noviniek -->
            <div class="checkbox-container">
              <input
                type="checkbox"
                id="noNewsletterCheckbox"
                class="newsletter"
                @change="editNewsChange('nechcem')"
                v-model="nechcemOdoberat"
                :checked="!$store.state.user.ads"
              />
              <label for="noNewsletterCheckbox"
                >Chcem sa odhlásiť z odberu.</label
              >
            </div>

            <button class="button" type="submit">
              <a>Uložiť</a>
            </button>
            <div @click="editNews(false)" class="zrusit click">
              <img src="@/assets/images/icons/zrusit.svg" alt="" />
              <p>Zrušiť</p>
            </div>
          </form>
        </div>
        <div class="line horizontal"></div>

        <div class="user-info-box">
          <div class="user-info-head">
            <p class="nazov">Ochrana súkromia</p>
            <img
              @click="editUserPassword(true)"
              v-if="!editUserPasswordValue"
              src="@/assets/images/icons/upravit.svg"
              alt="upraviť"
              class="click"
            />
          </div>
          <div v-if="!editUserPasswordValue" class="user-info">
            <p class="nazov-piesne">ZMENIŤ HESLO</p>
          </div>
          <form @submit.prevent="upravenieHesla" v-if="editUserPasswordValue">
            <label for="aktualne">Aktuálne heslo <span>*</span></label>
            <div class="row">
              <div><img src="@/assets/images/icons/profil.svg" /></div>
              <input
                @input="userPasswordChange('aktualne')"
                @change="userPasswordChange('aktualne')"
                type="password"
                name="aktualne"
                id=""
                v-model="userpasswordValue.aktualne"
              />
            </div>

            <label for="nove">Nové heslo <span>*</span></label>
            <div class="row">
              <div><img src="@/assets/images/icons/profil.svg" /></div>
              <input
                @input="userPasswordChange('nove')"
                @change="userPasswordChange('nove')"
                type="password"
                name="nove"
                id=""
                v-model="userpasswordValue.nove"
              />
              <div class="verify">
                <img
                  src="@/assets/images/icons/spravne.svg"
                  alt="Overenie správne"
                  v-if="userPasswordCorrect.nove"
                />
                <img
                  src="@/assets/images/icons/nespravne.svg"
                  alt="Overenie nesprávne"
                  v-if="!userPasswordCorrect.nove"
                />
              </div>
            </div>

            <label for="noveRovnake">Potvrdte nové heslo <span>*</span></label>
            <div class="row">
              <div><img src="@/assets/images/icons/profil.svg" /></div>
              <input
                @input="userPasswordChange('noveRovnake')"
                @change="userPasswordChange('noveRovnake')"
                type="password"
                name="noveRovnake"
                id=""
                v-model="userpasswordValue.noveRovnake"
              />
              <div class="verify">
                <img
                  src="@/assets/images/icons/spravne.svg"
                  alt="Overenie správne"
                  v-if="userPasswordCorrect.noveRovnake"
                />
                <img
                  src="@/assets/images/icons/nespravne.svg"
                  alt="Overenie nesprávne"
                  v-if="!userPasswordCorrect.noveRovnake"
                />
              </div>
            </div>

            <button class="button" type="submit">
              <a>Uložiť</a>
            </button>
            <div @click="editUserPassword(false)" class="zrusit click">
              <img src="@/assets/images/icons/zrusit.svg" alt="" />
              <p>Zrušiť</p>
            </div>
          </form>
        </div>

        <div class="line horizontal"></div>

        <div class="user-info-box">
          <div class="user-info-head">
            <p class="nazov">Dôležité linky</p>
          </div>
          <div class="user-info">
            <p class="nazov-piesne">
              <router-link to="/obchodne-podmienky"
                >Ochrana osobných údajov</router-link
              >
            </p>
            <p class="nazov-piesne">
              <router-link to="/obchodne-podmienky"
                >Obchodné podmienky</router-link
              >
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <div :id="$route.name" class="mobile">
    <section>
      <div class="scroll">
        <h1>Nastavenie účtu</h1>
        <h5>Tu môžete spravovať svoj účet a odbery</h5>
        <div class="line horizontal w-80"></div>

        <div class="profile">
          <ProfilePhoto />
        </div>
        <div class="line horizontal"></div>

        <div class="obsah">
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
              <p class="small-nazov">Email:</p>
              <p class="nazov-piesne">
                {{ $store.state.user.email }}
              </p>
              <p v-if="!editUserValue" class="nazov-piesne">
                {{
                  $store.state.user.name == "" || $store.state.user.name == null
                    ? "Meno"
                    : $store.state.user.name
                }}
              </p>
              <p v-if="!editUserValue" class="small-nazov">Priezvisko:</p>
              <p v-if="!editUserValue" class="nazov-piesne">
                {{
                  $store.state.user.surname == "" ||
                  $store.state.user.surname == null
                    ? "Priezvisko"
                    : $store.state.user.surname
                }}
              </p>
              <p v-if="!editUserValue" class="small-nazov">Mobil:</p>
              <p v-if="!editUserValue" class="nazov-piesne">
                {{
                  $store.state.user.phone == "" ||
                  $store.state.user.phone == null
                    ? "+421 900 000 000"
                    : $store.state.user.phone
                }}
              </p>
              <p v-if="!editUserValue" class="small-nazov">Dátum narodenia:</p>
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
              <p v-if="!editAddressValue" class="small-nazov">
                Ulica a číslo domu:
              </p>
              <p v-if="!editAddressValue" class="nazov-piesne">
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
              <p v-if="!editAddressValue" class="small-nazov">Mesto / Obec</p>
              <p v-if="!editAddressValue" class="nazov-piesne">
                {{
                  $store.state.user.billingAddressCity == undefined ||
                  $store.state.user.billingAddressCity == null
                    ? "Žiadna adresa"
                    : $store.state.user.billingAddressCity
                }}
              </p>
              <p v-if="!editAddressValue" class="small-nazov">PSČ:</p>
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
              <p v-if="!editAddressValue" class="small-nazov">Krajina:</p>
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
              <p v-if="!editAddressValue" class="small-nazov">
                Ulica a číslo domu:
              </p>
              <p v-if="!editAddressValue" class="nazov-piesne">
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
              <p v-if="!editAddressValue" class="small-nazov">Mesto / Obec</p>
              <p v-if="!editAddressValue" class="nazov-piesne">
                {{
                  $store.state.user.deliveryAddressCity == undefined ||
                  $store.state.user.deliveryAddressCity == null
                    ? "Žiadna adresa"
                    : $store.state.user.deliveryAddressCity
                }}
              </p>
              <p v-if="!editAddressValue" class="small-nazov">PSČ:</p>
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
              <p v-if="!editAddressValue" class="small-nazov">Krajina:</p>
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

              <label for="editAddressDeliveryValue" class="checkbox">
                Iná dodacia adresa
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

          <div class="user-info-box">
            <div class="user-info-head">
              <p class="nazov">Odoberanie Noviniek</p>
              <img
                @click="editNews(true)"
                v-if="!editNewsValue"
                src="@/assets/images/icons/upravit.svg"
                alt=""
                class="click"
              />
            </div>
            <div class="user-info">
              <p v-if="!editNewsValue" class="small-nazov">
                Odoberanie noviniek formou emailu
              </p>
              <p v-if="!editNewsValue" class="nazov-piesne">
                {{
                  $store.state.user.ads == true
                    ? "Áno, ponechajte moje odbery."
                    : "Nie, neodoberám novinky"
                }}
              </p>
            </div>
            <form @submit.prevent="editNewsSave" v-if="editNewsValue">
              <!-- Checkbox pre odoberanie noviniek -->
              <div class="checkbox-container">
                <input
                  type="checkbox"
                  id="newsletterCheckbox"
                  class="newsletter"
                  v-model="chcemOdoberat"
                  :checked="$store.state.user.ads == true"
                  @change="editNewsChange('chcem')"
                />
                <label for="newsletterCheckbox">Chcem odoberať novinky</label>
              </div>

              <!-- Checkbox pre neodoberanie noviniek -->
              <div class="checkbox-container">
                <input
                  type="checkbox"
                  id="noNewsletterCheckbox"
                  class="newsletter"
                  @change="editNewsChange('nechcem')"
                  v-model="nechcemOdoberat"
                  :checked="$store.state.user.ads == false"
                />
                <label for="noNewsletterCheckbox"
                  >Chcem sa odhlásiť z odberu.</label
                >
              </div>

              <button class="button" type="submit">
                <a>Uložiť</a>
              </button>
              <div @click="editNews(false)" class="zrusit click">
                <img src="@/assets/images/icons/zrusit.svg" alt="" />
                <p>Zrušiť</p>
              </div>
            </form>
          </div>
          <div class="line horizontal"></div>

          <div class="user-info-box">
            <div class="user-info-head">
              <p class="nazov">Ochrana súkromia</p>
              <img
                @click="editUserPassword(true)"
                v-if="!editUserPasswordValue"
                src="@/assets/images/icons/upravit.svg"
                alt="upraviť"
                class="click"
              />
            </div>
            <div v-if="!editUserPasswordValue" class="user-info">
              <p class="nazov-piesne">ZMENIŤ HESLO</p>
            </div>
            <form @submit.prevent="upravenieHesla" v-if="editUserPasswordValue">
              <label for="aktualne">Aktuálne heslo <span>*</span></label>
              <div class="row">
                <div><img src="@/assets/images/icons/profil.svg" /></div>
                <input
                  @input="userPasswordChange('aktualne')"
                  @change="userPasswordChange('aktualne')"
                  type="password"
                  name="aktualne"
                  id=""
                  v-model="userpasswordValue.aktualne"
                />
              </div>

              <label for="nove">Nové heslo <span>*</span></label>
              <div class="row">
                <div><img src="@/assets/images/icons/profil.svg" /></div>
                <input
                  @input="userPasswordChange('nove')"
                  @change="userPasswordChange('nove')"
                  type="password"
                  name="nove"
                  id=""
                  v-model="userpasswordValue.nove"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="userPasswordCorrect.nove"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!userPasswordCorrect.nove"
                  />
                </div>
              </div>

              <label for="noveRovnake"
                >Potvrdte nové heslo <span>*</span></label
              >
              <div class="row">
                <div><img src="@/assets/images/icons/profil.svg" /></div>
                <input
                  @input="userPasswordChange('noveRovnake')"
                  @change="userPasswordChange('noveRovnake')"
                  type="password"
                  name="noveRovnake"
                  id=""
                  v-model="userpasswordValue.noveRovnake"
                />
                <div class="verify">
                  <img
                    src="@/assets/images/icons/spravne.svg"
                    alt="Overenie správne"
                    v-if="userPasswordCorrect.noveRovnake"
                  />
                  <img
                    src="@/assets/images/icons/nespravne.svg"
                    alt="Overenie nesprávne"
                    v-if="!userPasswordCorrect.noveRovnake"
                  />
                </div>
              </div>

              <button class="button" type="submit">
                <a>Uložiť</a>
              </button>
              <div @click="editUserPassword(false)" class="zrusit click">
                <img src="@/assets/images/icons/zrusit.svg" alt="" />
                <p>Zrušiť</p>
              </div>
            </form>
          </div>

          <div class="line horizontal"></div>

          <div class="user-info-box">
            <div class="user-info-head">
              <p class="nazov">Dôležité linky</p>
            </div>
            <div class="user-info">
              <p class="nazov-piesne">
                <router-link to="/obchodne-podmienky"
                  >Ochrana osobných údajov</router-link
                >
              </p>
              <p class="nazov-piesne">
                <router-link to="/obchodne-podmienky"
                  >Obchodné podmienky</router-link
                >
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import ProfilePhoto from "@/components/Nastavenia/ProfilePhoto.vue";
export default {
  components: {
    ProfilePhoto,
  },
  mounted() {
    window.scrollTo(0, 0);

    if (this.$store.state.user.ads == true) {
      this.chcemOdoberat = true;
      this.nechcemOdoberat = false;
    } else {
      this.chcemOdoberat = false;
      this.nechcemOdoberat = true;
    }

    if (this.$store.state.user.deliveryAddressCity == "") {
      this.editAddressDeliveryValue = false;
    } else {
      this.editAddressDeliveryValue = true;
    }
  },
  data() {
    return {
      editUserValue: false,
      editNewsValue: false,
      editAddressValue: false,
      editAddressDeliveryValue: false,
      editUserPasswordValue: false,
      userPasswordCorrect: { nove: false, noveRovnake: false },
      userValue: { meno: false, priezvisko: false, datum: false, phone: false },
      chcemOdoberat: false,
      nechcemOdoberat: false,
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
      userpasswordValue: {
        aktualne: "",
        nove: "",
        noveRovnake: "",
      },
      obceData: [],
    };
  },
  methods: {
    editUser(value) {
      this.editUserValue = value;
      this.editAddressValue = false;
      this.editNewsValue = false;
      this.editUserPasswordValue = false;
      this.isFormValidUserValue();
    },
    editAddress(value) {
      this.editAddressValue = value;
      this.editUserValue = false;
      this.editNewsValue = false;
      this.editUserPasswordValue = false;

      if (this.$store.state.user.deliveryAddressCity == "") {
        this.editAddressDeliveryValue = false;
      } else {
        this.editAddressDeliveryValue = true;
      }
      this.isFormValidAddressValue();
    },
    editNews(value) {
      this.editNewsValue = value;
      this.editUserValue = false;
      this.editAddressValue = false;
      this.editUserPasswordValue = false;

      this.isFormValidAddressValue();
    },
    editUserPassword(value) {
      this.editUserPasswordValue = value;
      this.editNewsValue = false;
      this.editUserValue = false;
      this.editAddressValue = false;

      this.isFormValidAddressValue();
    },
    userValueChange() {
      this.isFormValidUserValue("Zmen");
    },
    addressValueChange() {
      this.isFormValidAddressValue("Zmen");
    },
    userPasswordChange() {
      this.isFormValidPasswordValue("Zmen");
    },

    editNewsChange(ciChcem) {
      if (ciChcem == "chcem") {
        this.chcemOdoberat = true;
        this.nechcemOdoberat = false;
        this.$store.state.user.ads = true;
      } else {
        this.chcemOdoberat = false;
        this.nechcemOdoberat = true;
        this.$store.state.user.ads = false;
      }
    },
    upravenieHesla() {
      if (this.isFormValidPasswordValue("Uloz")) {
        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();
        data.append("oldPassword", this.userpasswordValue.aktualne);
        data.append("newPassword", this.userpasswordValue.nove);

        let config = {
          method: "post",
          maxBodyLength: Infinity,
          url: this.$store.state.api + "/user/auth/changePassword.php",
          headers: {},
          data: data,
        };

        axios
          .request(config)
          .then((response) => {
            console.log(response.data);
            if (response.data.status == "Request succesfull") {
              this.editUserPasswordValue = false;
              this.$store.state.SnackBarText = "Vaše heslo bolo zmenené";
            } else if (response.data.data == "Wrong password") {
              this.$store.state.SnackBarText =
                "Heslo ktoré ste zadali je nesprávne";
            } else {
              this.$store.state.SnackBarText = "Niečo sa pokazilo";
            }
          })
          .catch((error) => {
            console.log(error);
            this.$store.state.SnackBarText = "Niečo sa pokazilo";
          });
      }
    },
    editNewsSave() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      data.append("ads", this.$store.state.user.ads);

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/user/info/setEmailAds.php/",
        // headers: {
        //   ...data.getHeaders()
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.data == "Data updated") {
            if (this.$store.state.user.ads) {
              this.$store.state.SnackBarText =
                "Ďakujeme že chcete odoberať naše novinky";
              this.editNewsValue = false;
            } else {
              this.$store.state.SnackBarText =
                "Odoberanie noviniek bolo pozastavené";
              this.editNewsValue = false;
            }
          } else {
            this.$store.state.SnackBarText =
              "Niečo sa pokazilo skúste neskor alebo nám napíšte";
            this.editNewsValue = false;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    upravenieUser() {
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

        axios
          .request(config)
          .then(async (response) => {
            console.log(response);
            this.$store.dispatch("pouzivatelskeInformacie");
            this.editUserValue = false;
            this.$store.state.SnackBarText = "Vaše údaje boli úspešne uložené";
          })
          .catch((error) => {
            console.log(error);
          });
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

        axios
          .request(config)
          .then(async (response) => {
            console.log(response);
            this.$store.dispatch("pouzivatelskeInformacie");
            this.editAddressValue = false;
            this.$store.state.SnackBarText = "Vaše údaje boli úspešne uložené";
          })
          .catch((error) => {
            console.log(error);
          });
      }
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

      let phone = this.$store.state.user.phone.replace(/\s+/g, ""); // Odstráni medzery

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
    isFormValidPasswordValue(value) {
      var isGreen = 0;

      if (this.userpasswordValue.nove.length < 8) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText = "Heslo musí mať aspoň 8 znakov";
          return false;
        } else {
          this.userPasswordCorrect.nove = false;
        }
      } else {
        isGreen++;
      }
      if (!/[A-Z]/.test(this.userpasswordValue.nove)) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Heslo musí obsahovať aspoň jedno veľké písmeno";
          return false;
        } else {
          this.userPasswordCorrect.nove = false;
        }
      } else {
        isGreen++;
      }
      if (!/\d/.test(this.userpasswordValue.nove)) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText =
            "Heslo musí obsahovať aspoň jedno číslo";
          return false;
        } else {
          this.userPasswordCorrect.nove = false;
        }
      } else {
        isGreen++;
      }

      if (isGreen >= 3) {
        this.userPasswordCorrect.nove = true;
      }

      if (
        this.userpasswordValue.nove !== this.userpasswordValue.noveRovnake &&
        this.userpasswordValue.nove != ""
      ) {
        if (value == "Uloz") {
          this.$store.state.SnackBarText = "Vaše heslá sa nezhodujú";
          return false;
        } else {
          this.userPasswordCorrect.noveRovnake = false;
        }
      } else {
        this.userPasswordCorrect.noveRovnake = true;

        if (isGreen >= 3) {
          return true;
        }
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
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.75vw;
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

  font-size: 2.5em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%; /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

.profile {
  position: relative;
}

.obsah {
  display: flex;
  flex-direction: column;
  width: 75%;
  max-width: 30em;
  margin: 3em auto;
  align-items: center;
}

.user-info-box {
  display: flex;
  align-items: flex-start;
  width: 90%;
  flex-direction: column;
}

.user-info-head {
  display: flex;
  width: 110%;
  justify-content: space-between;
  margin: 1.5em 0 0.4em -5%;
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
.nazov {
  font-size: 1.65em;
  font-weight: 600;
}

.small-nazov {
  font-size: 0.9em;
}

.nazov-piesne {
  font-size: 1.2em;
  margin: 0.1em 0 0.3em;
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

.delivery-form {
  width: 100%;
  display: flex;
  flex-direction: column;
  // align-items: flex-start;
}

//formulár
form {
  max-width: unset;
  display: flex;
  flex-direction: column;
  align-items: center;
  margin: 0 0 1em;

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
  .row {
    margin: 0.1em 0px 0.8em;
    width: 100%;
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

  label {
    margin-right: auto;
    font-size: 1.4em;

    span {
      color: #f26d24;
    }
  }

  .under {
    margin-right: auto;
    font-size: 0.9em;
    margin-top: -0.5em;
    margin-bottom: 0.5em;
  }

  .button {
    padding: 0.4em 1.4em;
    font-size: 1.5em;
  }

  .newsletter {
    display: none;
  }

  .checkbox-container {
    margin: 0.5em auto 0.5em 10%;
    font-size: 1em;
    display: flex;
    align-items: center;
  }

  .newsletter + label {
    position: relative;
    padding-left: 30px;
    cursor: pointer;
  }

  .newsletter + label:before {
    content: "";
    position: absolute;
    // left: 0;
    // top: 0;
    // width: 0.97em;
    // height: 0.97em;
    // border: 0.1em solid #90CA50;
    left: 0;
    top: 0;
    width: 22px;
    height: 22px;
    border: 2px solid #90ca50;
    background-color: #ededed;
    border-radius: 50%;
    box-sizing: border-box;
  }

  .newsletter + label:after {
    content: "";
    position: absolute;
    // left: 0.09em;
    // top: 0.09em;
    // width: 0.59em;
    // height: 0.59em;
    // border: 0.1em solid #ededed;
    left: 2px;
    top: 2px;
    width: 14px;
    height: 14px;
    border: 2px solid #ededed;
    border-radius: 50%;
  }

  .newsletter:checked + label:before {
    background-color: #00913f; /* Strong green fill when checked */
  }
}

@media only screen and (max-width: 1000px) {
  h5 {
    font-size: 1.8em;
  }
}

@media only screen and (max-width: 750px) {
  .mobile section h1 {
    font-size: 11.5vw !important;
  }
  section .scroll {
    border-radius: 0;
  }

  .mobile section h5 {
    font-size: 6vw !important;
    padding: 0 10%;
    margin: 0.3em auto 0.7em;
  }

  .mobile section p {
    margin: 0;
  }

  .mobile section form {
    width: 100%;
    font-size: 70%;
  }

  .mobile form label {
    font-size: 1.8em;
  }

  .mobile form .button {
    font-size: 2.2em;
    margin-bottom: 0.4em;
  }

  .mobile form .zrusit {
    gap: 5%;
    width: auto;
    align-items: stretch;

    p {
      font-size: 1.9em !important;
    }
  }
  .mobile form .under {
    margin: 0em auto 0.5em 0em;
    font-size: 1.1em;
  }

  .mobile form .checkbox-container {
    margin: 0.5em auto 0.5em 0%;
  }

  form .newsletter + label:before {
    width: 16px;
    height: 16px;
  }

  form .newsletter + label:after {
    width: 8px;
    height: 8px;
  }

  .obsah {
    width: 95%;
  }

  .mobile select {
    font-size: 1.7em;
  }
}

@media only screen and (max-width: 500px) {
  .mobile .user-info-head {
    width: 100%;
    margin: 1.5em 0 0.4em 0%;

    img {
      width: 1.4em;
    }
  }

  .mobile section {
    .small-nazov {
      margin: 0.4em 0 0.2em;
      font-size: 1em;
    }

    .nazov {
      font-size: 4.7vw;
      font-weight: 800;
    }

    .nazov-piesne {
      font-size: 1.3em;
    }
  }
}
</style>
