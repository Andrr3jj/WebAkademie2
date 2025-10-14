export default {
  namespaced: true,
  state: () => ({
    info: {
      open: false,
      lesson: null, // { id, studentIds[], dayName, hour, formOfStudy, dateDMY, ... }
    },
    editRequest: null, // payload pre otvorenie edit okna
  }),
  mutations: {
    OPEN_INFO(state, lesson) {
      state.info.open = true;
      state.info.lesson = lesson || null;
    },
    CLOSE_INFO(state) {
      state.info.open = false;
      state.info.lesson = null;
    },
    REQUEST_EDIT(state, payload) {
      state.editRequest = payload || null;
    },
    CLEAR_EDIT(state) {
      state.editRequest = null;
    },
  },
};
