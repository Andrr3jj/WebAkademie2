// src/store/modules/credits.js
export default {
  namespaced: true,

  state: () => ({
    showCreditsBox: false,
    credits: null, // null = ešte nenačítané
    loading: false,
    error: null,
  }),

  getters: {
    hasCredits: (s) => s.credits !== null,
  },

  mutations: {
    SET_SHOW(state, val) {
      state.showCreditsBox = val;
    },
    SET_CREDITS(state, val) {
      state.credits = val;
    },
    SET_LOADING(state, val) {
      state.loading = val;
    },
    SET_ERROR(state, val) {
      state.error = val;
    },
    RESET(state) {
      state.showCreditsBox = false;
      state.credits = null;
      state.loading = false;
      state.error = null;
    },
  },

  actions: {
    async toggleCreditsBox({ commit, state, dispatch }) {
      // prepni viditeľnosť
      commit("SET_SHOW", !state.showCreditsBox);

      // ak sme práve zapli a ešte nemáme údaje, načítaj
      if (state.showCreditsBox && state.credits === null) {
        await dispatch("fetchCredits");
      }
    },

    async fetchCredits({ commit, rootState }) {
      const userId = rootState.user?.id;
      const baseUrl = rootState.api; // predpoklad: máš ho v root state

      if (!userId || !baseUrl) {
        // bez usera to aspoň nespôsobí chybu
        commit("SET_CREDITS", 0);
        return;
      }

      commit("SET_LOADING", true);
      commit("SET_ERROR", null);

      try {
        const axios = (await import("axios")).default;
        const url = `${baseUrl}/helitime/getCredit.php?id=${userId}`;
        const { data } = await axios.get(url, { withCredentials: true });

        if (data?.status === "Request succesfull") {
          const credits = data.data?.[userId] ?? 0;
          commit("SET_CREDITS", credits);
        } else {
          commit("SET_CREDITS", 0);
          commit("SET_ERROR", "Bad response");
        }
      } catch (e) {
        commit("SET_CREDITS", 0);
        commit("SET_ERROR", e?.message || "Network error");
      } finally {
        commit("SET_LOADING", false);
      }
    },

    // užitočné utility
    setShow({ commit }, val) {
      commit("SET_SHOW", val);
    },
    setCredits({ commit }, val) {
      commit("SET_CREDITS", val);
    },
    reset({ commit }) {
      commit("RESET");
    },
  },
};
