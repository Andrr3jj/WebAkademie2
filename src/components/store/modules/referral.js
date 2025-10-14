// src/store/modules/referral.js
export default {
  namespaced: true,

  state: () => ({
    code: null, // napr. "ABC123"
    url: null, // plná URL, ktorou prišiel návštevník
    setAt: null, // ISO dátum, kedy sme to uložili
  }),

  getters: {
    hasReferral: (s) => !!s.code,
  },

  mutations: {
    SET_REFERRAL(state, payload) {
      state.code = payload.code;
      state.url = payload.url;
      state.setAt = payload.setAt || new Date().toISOString();
    },
    CLEAR_REFERRAL(state) {
      state.code = null;
      state.url = null;
      state.setAt = null;
    },
  },

  actions: {
    capture({ state, commit }, { codeParam, currentHref }) {
      if (state.code) return; // first-click atribúcia (ak chceš last-click, odstráň)

      let code = (codeParam || "").trim().replace(/\/+$/, "");
      if (!code || !/^[A-Za-z0-9_-]{3,64}$/.test(code)) return;

      const payload = {
        code,
        url:
          currentHref ||
          (typeof window !== "undefined" ? window.location.href : ""),
        setAt: new Date().toISOString(),
      };

      commit("SET_REFERRAL", payload);
      try {
        localStorage.setItem("referral", JSON.stringify(payload));
      } catch (e) {
        /* noop */
      }
    },

    init({ commit }) {
      try {
        const raw = localStorage.getItem("referral");
        if (!raw) return;
        const parsed = JSON.parse(raw);
        if (parsed?.code) commit("SET_REFERRAL", parsed);
      } catch (e) {
        /* noop */
      }
    },

    clear({ commit }) {
      commit("CLEAR_REFERRAL");
      try {
        localStorage.removeItem("referral");
      } catch (e) {
        /* noop: localStorage alebo JSON môžu zlyhať (SSR / privacy módy) */
      }
    },
  },
};
