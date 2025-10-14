import { reactive, computed } from "vue";
import { useStore } from "vuex";
import FriendsSearch from "@/components/ucebna/FriendsSearch.vue";
import PrikazKUrhade from "@/components/Edupage/PrikazKUrhade.vue";
import PrehladVyucby from "@/components/Edupage/PrehladVyucby.vue";

export function useClassroom() {
  const store = useStore();

  const state = reactive({
    sirkaPolka: 10,
    sirkaValcik: 10,
    hodnotaPolka: 0,
    hodnotaValcik: 0,
    dobaHranie: { dni: 0, hodiny: 0, minuty: 0 },
    statistika: {
      zapisOwned: 0,
      zapisFinished: 0,
      videoOwned: 0,
      videoFinished: 0,
    },
    oblubenePesnickyID: [],
    produktyOblubeneData: [],
    poslednySledovanyProdukt: null,
    taskStarsData: { ulohy: 0, splnene: 0, dni: 0, hodiny: 0, stars: 0 },
    achievements: [],
    progress7dni: 0,
    modalOpen: false,
    modalComponent: null,
    modalProps: {},
  });

  const produktyOblubeneDataArr = computed(() =>
    state.oblubenePesnickyID
      .map((id) => state.produktyOblubeneData.find((p) => p && p.id == id))
      .filter(Boolean)
  );

  function openModal({ component, props }) {
    state.modalComponent = component;
    state.modalProps = props || {};
    state.modalOpen = true;
  }

  function closeModal() {
    state.modalOpen = false;
    state.modalComponent = null;
    state.modalProps = {};
  }

  function openReviewModal(props = {}) {
    openModal({ component: PrehladVyucby, props });
  }

  function openPaymentModal(props = {}) {
    openModal({ component: PrikazKUrhade, props });
  }

  function openSearchModal(friendsListRef) {
    openModal({
      component: FriendsSearch,
      props: {
        friendIds: friendsListRef?.friends?.map((f) => f.id) ?? [],
        currentUserId: store.state.user?.id,
      },
    });
  }

  function vypocitajHviezdy(dni, hodiny) {
    const totalHours = dni * 24 + hodiny;
    if (totalHours < 8) return 0;
    if (totalHours < 24) return 1;
    if (dni === 1) return 2;
    if (dni >= 2 && dni < 5) return 3;
    if (dni >= 5 && dni < 10) return 4;
    return 5;
  }

  function vypocitajTaskStars() {
    const { dni, hodiny } = state.dobaHranie || {};
    const stars = vypocitajHviezdy(dni, hodiny);
    state.taskStarsData = {
      ...state.taskStarsData,
      dni,
      hodiny,
      stars,
    };
  }

  function rozdelitMinuty(minuty) {
    const dni = Math.floor(minuty / (24 * 60));
    const zostavajuceHodiny = minuty % (24 * 60);
    const hodiny = Math.floor(zostavajuceHodiny / 60);
    const zostavajuceMinuty = zostavajuceHodiny % 60;
    return { dni, hodiny, minuty: zostavajuceMinuty };
  }

  async function porovnajPomerPolkaValcik() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        store.state.api + "/user/stats/getZapisOwnedSeparated.php/"
      );
      if (res.data.status == "Request succesfull") {
        state.hodnotaPolka = res.data.data.polka;
        state.hodnotaValcik = res.data.data.valcik;
        let total = state.hodnotaPolka + state.hodnotaValcik;
        if (total === 0 || state.hodnotaPolka == state.hodnotaValcik) {
          state.sirkaPolka = 50;
          state.sirkaValcik = 50;
        } else if (state.hodnotaPolka == 0) {
          state.sirkaPolka = 0;
          state.sirkaValcik = 100;
        } else if (state.hodnotaValcik == 0) {
          state.sirkaPolka = 100;
          state.sirkaValcik = 0;
        } else {
          let ratio = state.hodnotaPolka / state.hodnotaValcik;
          let weightedRatio = Math.exp(ratio - 1) / (Math.exp(ratio - 1) + 1);
          state.sirkaPolka = (weightedRatio * 100).toFixed(0);
          state.sirkaValcik = (100 - state.sirkaPolka).toFixed(0);
        }
      }
    } catch (e) {
      state.hodnotaPolka = 0;
      state.hodnotaValcik = 0;
      state.sirkaPolka = 50;
      state.sirkaValcik = 50;
    }
  }

  async function celkoveHranie() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        store.state.api + "/user/stats/getUserWatchtime.php"
      );
      if (res.data.status === "Request succesfull") {
        if (res.data.data == 0 || res.data.data == "0") {
          state.dobaHranie = { dni: 0, hodiny: 0, minuty: 0 };
        } else {
          state.dobaHranie = rozdelitMinuty(Object.values(res.data.data)[0]);
        }
        vypocitajTaskStars();
      }
    } catch (e) {
      state.dobaHranie = { dni: 0, hodiny: 0, minuty: 0 };
      state.taskStarsData = {
        ulohy: 0,
        splnene: 0,
        dni: 0,
        hodiny: 0,
        stars: 0,
      };
    }
  }

  async function nacitajAchievementsStats() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        "https://heligonkovaakademia.sk/api/achievements/getAchievementsStats.php"
      );
      if (res.data.status === "Request succesfull") {
        state.taskStarsData.splnene = +(res.data.data?.finished ?? 0);
      }
    } catch (e) {
      state.taskStarsData.splnene = 0;
    }
  }

  async function spustiOdmenu() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        "https://heligonkovaakademia.sk/api/achievements/claim7Day.php"
      );
      if (res.data?.status === "Request succesfull" && res.data?.success) {
        alert("🎉 Odmena úspešne pripísaná!");
        nacitajVsetkyVyzvy();
      } else {
        alert("⚠️ Chyba pri vyzdvihovaní odmeny.");
      }
    } catch (e) {
      alert("⚠️ Chyba pri komunikácii so serverom.");
    }
  }

  async function nacitajVsetkyVyzvy() {
    try {
      const res = await fetch(
        "https://heligonkovaakademia.sk/api/achievements/getAchievements.php"
      );
      const data = await res.json();
      const raw = Object.values(data.data);
      const parsed = raw.map((item) => {
        const isSevenDay = item.achievement_id === "7_day";
        const max = isSevenDay ? 7 : Number(item.points_max) || 1;
        const actual = Number(item.points_actual) || 0;
        const percent = (actual / max) * 100;
        return {
          id: item.id,
          nadpis: item.name || "7-dňová výzva",
          popis: item.text || "Zahraj si každý deň aspoň 10 minút",
          progress: isSevenDay ? 0 : Math.min(percent, 100),
          typ: isSevenDay ? "7-dni" : "percenta",
          dniSplnene: isSevenDay ? 0 : null,
          dniSplneneToday: null,
          odmena: Number(item.reward) || 0,
          dokonceneDatum: item.finished_date || "",
          finished:
            item.reward_claimed === "true" || item.reward_claimed === true,
          achievement_id: item.achievement_id,
        };
      });
      const referralVyzva = {
        id: "invite-friends",
        nadpis: "Pozvi svojich priateľov",
        podnadpis: "Odporuč stránku a získaj body navyše!",
        popis: "Stačí, ak sa tvoj priateľ zaregistruje cez tvoj link.",
        typ: "pozvat-priatelov",
      };
      let siedmaVyzva = parsed.find((v) => v.achievement_id === "7_day");
      const najblizsia = parsed
        .filter(
          (v) =>
            v.achievement_id !== "7_day" &&
            !v.finished &&
            Number(v.progress) < 100
        )
        .sort((a, b) => b.progress - a.progress)[0];
      if (siedmaVyzva) {
        const resp = await fetch(
          "https://heligonkovaakademia.sk/api/achievements/get7Day.php"
        ).then((r) => r.json());
        let dniSplnene = 0;
        let dniSplneneToday = false;
        if (resp.status === "Request succesfull" && resp.data !== undefined) {
          if (typeof resp.data === "number") {
            dniSplnene = resp.data;
          } else if (typeof resp.data === "object") {
            dniSplnene = resp.data.dni_splnene ?? 0;
            dniSplneneToday = resp.data.splnene_today ?? false;
          }
        }
        siedmaVyzva = {
          ...siedmaVyzva,
          progress: dniSplnene,
          dniSplnene: dniSplnene,
          dniSplneneToday: dniSplneneToday,
          finished: dniSplnene >= 7,
        };
        state.achievements = [siedmaVyzva, najblizsia, referralVyzva].filter(
          Boolean
        );
      } else {
        state.achievements = [najblizsia, referralVyzva].filter(Boolean);
      }
    } catch (e) {
      state.achievements = [
        {
          id: "invite-friends",
          nadpis: "Pozvi svojich priateľov",
          podnadpis: "Odporuč stránku a získaj body navyše!",
          popis: "Stačí, ak sa tvoj priateľ zaregistruje cez tvoj link.",
          typ: "pozvat-priatelov",
        },
      ];
    }
  }

  async function nacitaj7DnovuVyzvu() {
    const dnes = new Date().toISOString().split("T")[0];
    const poslednySpust = localStorage.getItem("7day-vyzva-last");
    if (poslednySpust === dnes) return;
    const axios = require("axios");
    try {
      const res = await axios.get(
        "https://heligonkovaakademia.sk/api/achievements/get7Day.php"
      );
      if (res.data.status === "Request succesfull") {
        state.progress7dni = res.data.data;
        localStorage.setItem("7day-vyzva-last", dnes);
      }
    } catch (e) {
      state.progress7dni = 0;
      localStorage.removeItem("7day-vyzva-last");
    }
  }

  async function oblubenePesnicky() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        store.state.api + "/user/like/getProductsLiked.php/"
      );
      if (res.data.status == "Request succesfull" && res.data.data.length > 0) {
        state.oblubenePesnickyID = res.data.data;
        nacitajProduktyOblubene();
      } else {
        state.oblubenePesnickyID = [];
        state.produktyOblubeneData = [];
      }
    } catch (e) {
      state.oblubenePesnickyID = [];
      state.produktyOblubeneData = [];
    }
  }

  async function nacitajProduktyOblubene() {
    state.produktyOblubeneData = [];
    const axios = require("axios");
    Promise.all(
      state.oblubenePesnickyID.map((id) =>
        axios
          .get(store.state.api + "/product/loadLimited.php/?id=" + id)
          .then((res) =>
            res.data.status === "Request succesfull" ? res.data.data : null
          )
          .catch(() => null)
      )
    ).then((produkty) => {
      state.produktyOblubeneData = produkty.filter(Boolean);
    });
  }

  async function nacitajPoslednySledovanyProdukt() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        store.state.api + "/user/watchtime/getLastProductWatchtime.php/"
      );
      const res2 = await axios.get(
        store.state.api + "/product/loadLimited.php/?id=" + res.data.data
      );
      if (res2.data.status == "Request succesfull") {
        state.poslednySledovanyProdukt = res2.data.data;
      } else {
        state.poslednySledovanyProdukt = null;
      }
    } catch (e) {
      state.poslednySledovanyProdukt = null;
    }
  }

  async function zapisiOwned() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        store.state.api + "/user/stats/getZapisOwned.php"
      );
      if (res.data.status == "Request succesfull") {
        state.statistika.zapisOwned = res.data.data;
      }
    } catch (e) {
      state.statistika.zapisOwned = 0;
    }
  }

  async function zapisFinished() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        store.state.api + "/user/stats/getZapisFinished.php"
      );
      if (res.data.status == "Request succesfull") {
        state.statistika.zapisFinished = res.data.data;
      }
    } catch (e) {
      state.statistika.zapisFinished = 0;
    }
  }

  async function videoOwned() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        store.state.api + "/user/stats/getVideoOwned.php"
      );
      if (res.data.status == "Request succesfull") {
        state.statistika.videoOwned = res.data.data;
      }
    } catch (e) {
      state.statistika.videoOwned = 0;
    }
  }

  async function videoFinished() {
    const axios = require("axios");
    try {
      const res = await axios.get(
        store.state.api + "/user/stats/getVideoFinished.php"
      );
      if (res.data.status == "Request succesfull") {
        state.statistika.videoFinished = res.data.data;
      }
    } catch (e) {
      state.statistika.videoFinished = 0;
    }
  }

  function statistikyPouzivatela() {
    zapisiOwned();
    zapisFinished();
    videoOwned();
    videoFinished();
  }

  return {
    state,
    produktyOblubeneDataArr,
    openModal,
    closeModal,
    openPaymentModal,
    openSearchModal,
    openReviewModal,
    porovnajPomerPolkaValcik,
    celkoveHranie,
    nacitajAchievementsStats,
    spustiOdmenu,
    nacitajVsetkyVyzvy,
    nacitaj7DnovuVyzvu,
    oblubenePesnicky,
    nacitajProduktyOblubene,
    nacitajPoslednySledovanyProdukt,
    statistikyPouzivatela,
  };
}
