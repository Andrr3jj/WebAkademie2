// ==== DEV FORCE LOGIN (iba localhost alebo ručne cez window.__FORCE_DEV_LOGIN__ = true)
const DEV_FORCE_LOGIN =
  (typeof location !== "undefined" && location.hostname === "localhost") ||
  (typeof window !== "undefined" && window.__FORCE_DEV_LOGIN__ === true);

// Predvolený “fake” používateľ pre DEV
const FAKE_USER = {
  id: 1,
  email: "kuklalubos@gmail.com",
  phone: "0917 471 508",
  name: "Ľuboško",
  surname: "Kukla",
  dateOfBirth: "2004-04-23",
  deliveryAddressState: "Česká republika",
  deliveryAddressPostcode: "029 52",
  deliveryAddressCity: "Babín",
  deliveryAddressStreet: "Podkošariská",
  deliveryAddressHouseNumber: "647/17",
  billingAddressState: "Slovenská republika",
  billingAddressPostcode: "029 51",
  billingAddressCity: "Lokca",
  billingAddressStreet: "Podkošariská",
  billingAddressHouseNumber: "647/17",
  profilePhotoUrl:
    "https://www.heligonkovaakademia.sk/data/uploads/profile_pictures/default/def-5.png",
  ads: true,
  accesses: null,
  credit: "4251",
  isAdmin: true,
  isSubscribed: true, // nech sa učebňa tvári aktívna
  ownedNotes: [], // prázdne pole -> v „Číselných zápisoch“ sa nezmiznú položky kvôli filtru
};

import { createStore } from "vuex";
import router from "../../router";

import credits from "./modules/credits";
import referral from "./modules/referral";
import learning from "./modules/edupage/learning";
import ui from "./modules/ui";

export default createStore({
  modules: {
    credits,
    referral,
    edupage: {
      namespaced: true,
      modules: { learning },
    },
    ui,
  },

  state: {
    user: null,
    userRoles: { roles: [] },
    prihlasenyStav: 0,
    userCart: {},
    api: "https://heligonkovaakademia.sk/api/",
    SnackBarText: "",
    info: { gratulation: { show: false, message: "" } },
    isLoaded: false,
    ciselnyZapisObrazky: [],
    overlayVisible: false,
    globalVariables: {},
    inviteCode: "",
  },

  getters: {},

  mutations: {
    SET_USER(state, userData) {
      state.user = userData;

      if (state.user?.ads == "true") {
        state.user.ads = true;
      } else if (state.user?.ads == "false") {
        state.user.ads = false;
      }

      // pre istotu vždy pole
      if (!Array.isArray(state.user?.ownedNotes)) state.user.ownedNotes = [];

      window.haCanPrint = !!(
        state.user?.edupage_in_calendar ||
        (state.user?.isAdmin &&
          state.userRoles?.roles?.includes("subscription_edit"))
      );
    },
    SET_USER_DATA(state, payload) {
      state.userRoles.roles = Array.isArray(payload) ? payload : [];
      window.haCanPrint = !!(
        state.user?.edupage_in_calendar ||
        (state.user?.isAdmin &&
          state.userRoles?.roles?.includes("subscription_edit"))
      );
    },
    ADD_USER_INFO_VLASTNENE_ZAPISY(state, notesData) {
      if (!Array.isArray(state.user?.ownedNotes)) state.user.ownedNotes = [];
      if (Array.isArray(notesData)) {
        state.user.ownedNotes = [...state.user.ownedNotes, ...notesData];
      }
    },
    ADD_USER_INFO_KOSIK(state, cartData) {
      state.userCart = cartData || {};
    },
    DELETE_USER_INFO_KOSIK(state) {
      state.user = [];
    },
    ADD_USER_ADMIN(state) {
      if (state.user) state.user.isAdmin = true;
      window.haCanPrint = !!(
        state.user?.edupage_in_calendar ||
        (state.user?.isAdmin &&
          state.userRoles?.roles?.includes("subscription_edit"))
      );
    },
    ADD_USER_ACTIVE_SUBSCRIPTION(state) {
      if (state.user) state.user.isSubscribed = true;
    },
    REMOVE_USER_ACTIVE_SUBSCRIPTION(state) {
      if (state.user) state.user.isSubscribed = false;
      console.log(JSON.stringify(state.user));
    },
    SET_GRATULATION(state, value) {
      const current =
        typeof state.info.gratulation === "object"
          ? state.info.gratulation
          : { show: !!state.info.gratulation, message: "" };

      if (typeof value === "string") {
        state.info.gratulation = { show: true, message: value };
      } else if (typeof value === "boolean") {
        state.info.gratulation = { ...current, show: value };
      } else if (value && typeof value === "object") {
        state.info.gratulation = {
          show: value.show ?? current.show,
          message: value.message ?? current.message,
        };
      } else {
        state.info.gratulation = current;
      }
    },
    SET_INVITE_CODE(state, code) {
      state.inviteCode = code;
    },
  },

  actions: {
    // --- PRIHÁSENIE / REGISTRÁCIA nechávam bez zmien (produkčné volania) ---
    prihlasenie({ state }, { email, heslo }) {
      this.dispatch("odhlasenie");
      setTimeout(() => {
        const axios = require("axios");
        const FormData = require("form-data");
        let data = new FormData();
        data.append("email", email);
        data.append("password", heslo);

        let config = {
          method: "post",
          maxBodyLength: Infinity,
          url: state.api + "/user/auth/logIn.php",
          data: data,
        };

        axios
          .request(config)
          .then((response) => {
            setTimeout(() => {
              switch (response.data.status) {
                case "Request fullfiled":
                  state.SnackBarText = "Úspešne prihlásený na účet";
                  this.dispatch("overeniePrihlasenia");
                  if (router.currentRoute.value.path === "/prihlasenie") {
                    router.push("/");
                  }
                  return;
                case "Request failed":
                  switch (response.data.data) {
                    case "Already logged in":
                      state.SnackBarText = "Už ste prihlásený";
                      return;
                    case "Wrong password":
                      state.SnackBarText = "Nesprávne heslo";
                      return;
                    case "Email not found":
                      state.SnackBarText = "Účet s týmto emailom neexistuje";
                      return;
                    case "Trying too often":
                      state.SnackBarText = "Antispam: Príliš často sa pokúšate";
                      return;
                    default:
                      state.SnackBarText = "Neznáma chyba pri prihlásení";
                      return;
                  }
                case "Invalid Request":
                  state.SnackBarText = "Vyplňte všetky polia formuláru";
                  return;
                default:
                  state.SnackBarText =
                    "Neznáma chyba pri prihlásení. Prosím skúste neskôr";
                  return;
              }
            }, 100);
            console.log(response.data);
          })
          .catch((error) => {
            console.log(error);
          });
      }, 400);
    },

    registracia(
      { state, rootState },
      { meno, priezvisko, email, heslo, ads, krajina }
    ) {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      data.append("name", meno);
      data.append("surname", priezvisko);
      data.append("email", email);
      data.append("password", heslo);
      data.append("ads", ads);
      data.append("state", krajina);

      let invite = rootState?.referral?.code || "";
      invite = invite.replace(/\/+$/, "");
      if (invite) data.append("invite_code", invite);

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: state.api + "/user/auth/register.php",
        data,
      };

      axios
        .request(config)
        .then((response) => {
          setTimeout(() => {
            switch (response.data.status) {
              case "Request fullfiled":
                state.SnackBarText = "Účet bol úspešne vytvorený";
                this.dispatch("prihlasenie", { email, heslo });
                router.push("/");
                return;
              case "Request failed":
                switch (response.data.data) {
                  case "Email already registered":
                    state.SnackBarText = "E-mail už je zaregistrovaný";
                    return;
                  case "":
                    state.SnackBarText =
                      "Chyba pripojenia PHP na DB server. Prosím kontaktujte správcu";
                    return;
                  case "Trying too often":
                    state.SnackBarText =
                      "Antispam: Príliš často sa pokúšate prihlásiť";
                    return;
                  default:
                    state.SnackBarText =
                      "Niečo sa pokazilo. Prosím skúste neskôr";
                    return;
                }
              case "Invalid Request":
                state.SnackBarText = "Polia tela neboli správne vyplnené";
                return;
              default:
                state.SnackBarText = "Niečo sa pokazilo. Prosím skúste neskôr";
                return;
            }
          }, 100);
          console.log(response.data);
        })
        .catch((error) => {
          console.log(error);
        });
    },

    // --- TU PRICHÁDZA DEV SKRATKA ---
    overeniePrihlasenia({ state, commit, dispatch }) {
      if (DEV_FORCE_LOGIN) {
        // 1) nastav užívateľa
        commit("SET_USER", { ...FAKE_USER });

        // 2) pridaj roly – product_pass ti odomkne veci, subscription_edit + admin = tlač
        commit("SET_USER_DATA", ["product_pass", "subscription_edit"]);

        // 3) simuluj, že prebehli aj ostatné kroky
        state.prihlasenyStav++;

        // (voliteľné) pridaj si falošný košík alebo zápisy – podľa potreby
        // dispatch("pouzivatelskyKosikDev"); // ak by si chcel
        dispatch("pouzivatelAktivnePredplatne");
        dispatch("pouzivatelVlastneneProdukty"); // dev vetva nižšie pridá fake záznamy, ak chceš
        return;
      }

      // --- PRODUKČNÁ VETVA NECHÁVAM ---
      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: state.api + "/user/auth/isLoggedIn.php",
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          setTimeout(() => {
            switch (response.data.status) {
              case "Request fullfiled":
                state.prihlasenyStav++;
                this.dispatch("pouzivatelskeInformacie");
                setTimeout(() => {
                  this.dispatch("pouzivatelAktivnePredplatne");
                  this.dispatch("pouzivatelVlastneneProdukty");
                  this.dispatch("pouzivatelskyKosik");
                }, 100);
                setTimeout(() => {
                  if (response.data.data == "Is logged in as admin") {
                    commit("ADD_USER_ADMIN");
                    this.dispatch("nacitajRolyPouzivatela");
                  }
                }, 500);
                return;
              case "Request failed":
                switch (response.data.data) {
                  case "Not logged in":
                  case "Trying too often":
                  default:
                    return;
                }
              default:
                return;
            }
          }, 100);
        })
        .catch((error) => {
          console.log(error);
        });
    },

    pouzivatelskeInformacie({ commit, state }) {
      if (DEV_FORCE_LOGIN) {
        // v DEV netaháme – už máme FAKE_USER
        return;
      }
      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: state.api + "/user/info/getAdditionalInformation.php",
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          switch (response.data.status) {
            case "Request succesfull":
              var data = response.data.data;
              data.accesses = JSON.parse(data.accesses);
              commit("SET_USER", data);
              console.log(JSON.stringify(data));
              state.prihlasenyStav++;
              return;
            default:
              return;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    nacitajRolyPouzivatela({ commit, state }) {
      if (DEV_FORCE_LOGIN) {
        // už to máme z overeniePrihlasenia
        return;
      }
      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          state.api +
          "/user/info/getAllInformationAboutUser.php/?email=" +
          state.user.email,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status === "Request succesfull") {
            var userData = response.data.data;
            let rolesArray = [];
            if (userData?.roles) {
              if (typeof userData.roles === "string") {
                rolesArray = userData.roles
                  .slice(1, -1)
                  .replace(/\\/g, "")
                  .replace(/"/g, "")
                  .replace(/'/g, "")
                  .split(",")
                  .map((item) => item.trim());
              } else if (Array.isArray(userData.roles)) {
                rolesArray = [...userData.roles];
              }
            }
            commit("SET_USER_DATA", rolesArray);
            if (this.state.userRoles?.roles?.includes("product_pass")) {
              this.dispatch("nacitajNevlastneneZapisy");
            }
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    // Ak chceš mať vždy aspoň nejaký „falošný zápis“ pre test selektorov,
    // pridáme ho v DEV vetve. Pozor: v Číselných zápisoch sa zoznam filtruje tak,
    // aby NEzobrazoval ownedNotes. Preto nechávam ownedNotes prázdne
    // a tu len zvýšime počítadlo stavu (aby správanie zostalo konzistentné).
    pouzivatelVlastneneProdukty({ commit, state }) {
      if (DEV_FORCE_LOGIN) {
        // Ak by si chcel simulovať „Moje piesne“, odkomentuj:
        // commit("ADD_USER_INFO_VLASTNENE_ZAPISY", [{ id: 999, expire: "" }]);
        state.prihlasenyStav++;
        return;
      }

      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: state.api + "/product/getOwned.php",
      };

      axios
        .request(config)
        .then((response) => {
          switch (response.data.status) {
            case "Request succesfull":
              if (response.data.data.length > 0) {
                commit("ADD_USER_INFO_VLASTNENE_ZAPISY", response.data.data);
                console.log(JSON.stringify(state.user) + "po pripísaní dát");
              } else {
                commit("ADD_USER_INFO_VLASTNENE_ZAPISY", null);
                console.log(JSON.stringify(state.user) + "bez pripísania dát");
              }
              state.prihlasenyStav++;
              return;
            default:
              return;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    pouzivatelAktivnePredplatne({ commit, state }) {
      if (DEV_FORCE_LOGIN) {
        commit("ADD_USER_ACTIVE_SUBSCRIPTION");
        state.prihlasenyStav++;
        return;
      }

      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: state.api + "/subscription/isSubscribed.php/",
      };

      axios
        .request(config)
        .then((response) => {
          console.log(response.data);
          if (response.data.status == "Request succesfull") {
            if (
              response.data.data == "Is subscribed" ||
              this.state.userRoles?.roles?.includes("product_pass")
            ) {
              commit("ADD_USER_ACTIVE_SUBSCRIPTION");
              console.log("odberá");
            } else if (response.data.data == "Is not subscribed") {
              commit("REMOVE_USER_ACTIVE_SUBSCRIPTION");
              console.log(" neodoberá");
            }
            state.prihlasenyStav++;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    resetovanieHesla({ state }, email) {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();
      data.append("email", email);

      let config = {
        method: "post",
        maxBodyLength: Infinity,
        url: state.api + "/user/auth/forgotPasswordSendEmail.php",
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          switch (response.data.status) {
            case "Request fullfiled":
              console.log(JSON.stringify(response.data.data));
              state.SnackBarText = "Obnovovací email bol zaslaný.";
              router.push("/zabudnute-heslo-uspesne");
              return;
            case "Request failed":
              switch (response.data.data) {
                case "Email not found":
                  state.SnackBarText =
                    "Zadaný email sa nenašiel. Prosím skontrolujte si Vašu emailovú adresu.";
                  return;
                case "Trying too often":
                  state.SnackBarText =
                    "Email bol odoslaný. Další email bude možné odoslať za 1 minútu.";
                  return;
                default:
                  state.SnackBarText =
                    "Niečo sa pokazilo. Ak problém pretrváva prosím kontaktujte nás.";
                  return;
              }
            case "Invalid request":
              state.SnackBarText = "Prosím vyplnte všetky povinné polia";
              return;
            default:
              state.SnackBarText =
                "Niečo sa pokazilo. Ak problém pretrváva prosím kontaktujte nás.";
              return;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    odhlasenie({ state }) {
      if (DEV_FORCE_LOGIN) {
        state.user = null;
        state.userRoles.roles = [];
        window.haCanPrint = false;
        router.push("/");
        return;
      }

      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: state.api + "/user/auth/logOut.php",
        headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));
          switch (response.data.data) {
            case "":
              state.SnackBarText = "Boli ste úspešne odhlásení";
              state.user = null;
              window.haCanPrint = false;
              router.push("/");
              return;
            case "Not logged in":
              return;
            case "Trying too often":
              state.SnackBarText = "Antispam: Príliš často sa pokúšate";
              return;
            default:
              state.SnackBarText = "Neznáma chyba pri odhlasovaní";
              return;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    pouzivatelskyKosik({ commit, state }) {
      if (DEV_FORCE_LOGIN) {
        // ak chceš niečo mať v košíku v DEV:
        // commit("ADD_USER_INFO_KOSIK", [{ id: 123, count: 1, description: "Test položka", id_delete: "x" }]);
        state.prihlasenyStav++;
        return;
      }

      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: state.api + "/cart/load.php",
      };

      axios
        .request(config)
        .then((response) => {
          setTimeout(() => {
            if (response.data.status === "Request fullfiled") {
              const data = response.data.data[0];
              const deleteData = response.data.data[1];

              if (!Array.isArray(data)) {
                console.error(
                  "pouzivatelskyKosik očakáva pole, ale dostal:",
                  data
                );
                return;
              }

              const uniqueItems = data.map((id) => ({
                id:
                  typeof id === "string" && id.startsWith("gift_card")
                    ? id
                    : Number(id),
                count: 1,
                id_delete: id,
                description: "",
              }));

              const uniqueIdsWithCount = Object.entries(deleteData).map(
                ([key, value]) => {
                  const rawId = value[0];
                  const description = value[1];

                  const itemId =
                    typeof rawId === "string" && rawId.startsWith("gift_card")
                      ? rawId
                      : Number(rawId);

                  return {
                    id: itemId,
                    description: description.replaceAll(" - ", " "),
                    count: value.filter((item) => item === rawId).length,
                    id_delete: key,
                  };
                }
              );

              const mergedItems = [
                ...uniqueItems,
                ...uniqueIdsWithCount,
              ].reduce((acc, item) => {
                const existingItem = acc.find(
                  (i) => i.id === item.id && i.description === item.description
                );
                if (existingItem) {
                  existingItem.count += item.count;
                } else {
                  acc.push(item);
                }
                return acc;
              }, []);

              commit("ADD_USER_INFO_KOSIK", mergedItems);
              state.prihlasenyStav++;
              return;
            }

            switch (response.data.data) {
              case "Not logged in":
                return;
              case "Trying too often":
                state.SnackBarText = "Váš košík nebol načítaný správne";
                return;
              default:
                state.SnackBarText = "Váš košík nebol načítaný správne";
                return;
            }
          }, 1000);
        })
        .catch((error) => {
          console.log(error);
        });
    },

    async pridajDoKosika({ state, dispatch }, payload) {
      const axios = require("axios");
      const { id, meta_data } = payload;
      let data = meta_data ? "&cart_real_meta= - " + meta_data : "";

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: state.api + "/cart/add.php/?id=" + id + data,
      };

      try {
        const response = await axios.request(config);
        console.log(JSON.stringify(response.data));

        switch (response.data.status) {
          case "Request fullfiled":
            await dispatch("pouzivatelskyKosik");
            state.SnackBarText = "Produkt bol pridaný do košíka";
            break;
          case "Request failed":
            if (response.data.data === "Not logged in") {
              state.SnackBarText =
                "Pre pridanie produktu do košíka musíte byť prihlásený";
            } else if (response.data.data === "Already in cart") {
              state.SnackBarText = "Produkt sa už nachádza vo vašom košíku";
            } else {
              state.SnackBarText = "Nepodarilo sa vložiť produkt do košíka";
            }
            break;
          default:
            state.SnackBarText = "Nepodarilo sa vložiť produkt do košíka";
        }
      } catch (error) {
        console.log(error);
        state.SnackBarText = "Došlo k chybe pri pridávaní produktu do košíka";
      }
    },

    async odstranZKosiku({ state, dispatch }, id) {
      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: state.api + "/cart/remove.php/?id=" + id,
      };

      try {
        const response = await axios.request(config);
        console.log(JSON.stringify(response.data));

        if (response.data.status === "Request fullfiled") {
          await dispatch("pouzivatelskyKosik");
          state.SnackBarText = "Produkt bol odstránený z košíka";
          return true;
        } else {
          state.SnackBarText = "Nepodarilo sa odstrániť produkt";
          return false;
        }
      } catch (error) {
        console.log(error);
        state.SnackBarText = "Chyba pri odstraňovaní produktu";
        return false;
      }
    },

    vytvorPlatbu({ state }) {
      const axios = require("axios");
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: state.api + "/cart/paymentCreate.php",
      };

      axios
        .request(config)
        .then((response) => {
          console.log("PaymentCreate response:", response.data);
          const redirectUrl = response.data.data;

          if (redirectUrl.includes("heligonkova")) {
            axios
              .get(redirectUrl)
              .then((r) => {
                window.location.href = "/uspesny-nakup";
                console.log("Heligonkova platba spracovaná na pozadí:", r.data);
              })
              .catch((err) => {
                window.location.href = "/neuspesny-nakup";
                console.error("Chyba pri Heligonkova platbe:", err);
              });
          } else if (redirectUrl.includes("gopay")) {
            window.location.href = redirectUrl;
          } else {
            console.warn("Neznámy payment provider, presmerujem defaultne:");
            window.location.href = redirectUrl;
          }
        })
        .catch((error) => {
          console.error("Error vytvorPlatbu:", error);
          window.location.href = "/neuspesny-nakup";
        });
    },
  },
});
