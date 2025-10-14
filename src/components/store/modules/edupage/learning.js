import defaultAvatar from "@/assets/images/icons/prihlasenie.svg";

const toNum = (v) => {
  if (v === null || v === undefined) return 0;
  const n = Number(String(v).replace(",", "."));
  return Number.isFinite(n) ? n : 0;
};

const stripWWW = (url) => {
  const s = String(url || "").trim();
  if (!s) return "";
  return s.replace(/^(https?:\/\/)www\./i, "$1").replace(/^www\./i, "");
};

const statusOk = (status) => {
  const s = String(status || "")
    .toLowerCase()
    .trim();
  return (
    s === "ok" ||
    s === "success" ||
    s === "request succesfull" ||
    s === "request successful"
  );
};

const mapStudentFromApi = (id, data = {}) => {
  const sid = String(id);
  const fullName = [data.name, data.surname].filter(Boolean).join(" ").trim();

  const normalizePhone = (p) =>
    String(p || "")
      .replace(/\s+/g, " ")
      .trim();
  const firstPhone = (reg) => {
    if (!reg) return null;
    const list = Array.isArray(reg) ? reg : Object.values(reg);
    const rec = (list || []).find(
      (x) => x && x.phone && String(x.phone).trim() !== ""
    );
    return rec ? normalizePhone(rec.phone) : null;
  };

  return {
    id: sid,
    name: fullName || `ID ${sid}`,
    email: data.email || "",
    photo: stripWWW(data.profilePhotoUrl) || defaultAvatar,
    completed: toNum(data.absolvedLessonsCount),
    missed: toNum(data.canceledLessonsCount),
    tasks: toNum(data.achievementsFulfiledCount),
    star: toNum(data.starsCount),
    lastHourStar: toNum(data.lastHourStar ?? data.starsCount),
    rank: toNum(data.rank ?? 0),
    time: { total: toNum(data.helitime) },
    note: data.note || "",
    edupage_registration_student: data.edupage_registration_student || [],
    edupage_registration_parent: data.edupage_registration_parent || [],
    studentPhone: firstPhone(data.edupage_registration_student),
    parentPhone: firstPhone(data.edupage_registration_parent),
  };
};

export default {
  namespaced: true,
  state: () => ({
    studentsById: {},
    order: [],
    activeId: null,
    person: null,

    _inflight: Object.create(null),
    _lastFetchAt: Object.create(null),
  }),
  getters: {
    list: (state) =>
      state.order.map((id) => state.studentsById[id]).filter(Boolean),
    activeStudent: (state) => state.studentsById[state.activeId] || null,
    hasAny: (state) => state.order.length > 0,
  },
  mutations: {
    UPSERT_STUDENT(state, student) {
      if (!student || typeof student.id === "undefined") return;
      const id = String(student.id);
      const normalized = { ...(state.studentsById[id] || {}), ...student, id };
      if ("photo" in normalized) {
        normalized.photo = stripWWW(normalized.photo) || defaultAvatar;
      }
      state.studentsById[id] = normalized;
      if (!state.order.includes(id)) state.order.push(id);
      if (!state.activeId) state.activeId = id;
      state.person = state.studentsById[state.activeId] || null;
      console.log("[UPSERT_STUDENT]", id, normalized);
    },
    SET_ACTIVE(state, id) {
      state.activeId = String(id);
      state.person = state.studentsById[state.activeId] || null;
      console.log("[SET_ACTIVE]", state.activeId, state.person);
    },
    REMOVE_STUDENT(state, id) {
      const sid = String(id);
      if (state.studentsById[sid]) delete state.studentsById[sid];
      state.order = state.order.filter((x) => x !== sid);
      if (state.activeId === sid) state.activeId = state.order[0] || null;
      state.person = state.studentsById[state.activeId] || null;
      console.log("[REMOVE_STUDENT]", sid);
    },
    CLEAR_ALL(state) {
      state.studentsById = {};
      state.order = [];
      state.activeId = null;
      state.person = null;
      state._inflight = Object.create(null);
      state._lastFetchAt = Object.create(null);
      console.log("[CLEAR_ALL]");
    },
  },
  actions: {
    async fetchStudentAdvanced({ commit, rootState, state }, id) {
      const sid = String(id);
      const apiRoot = String(rootState.api || "").replace(/\/+$/, "");
      if (!apiRoot || sid === "") return null;

      const now = Date.now();
      const last = state._lastFetchAt[sid] || 0;
      if (now - last < 800 && state.studentsById[sid]) {
        return state.studentsById[sid];
      }

      if (state._inflight[sid]) {
        try {
          return await state._inflight[sid];
        } catch {
          delete state._inflight[sid];
        }
      }

      const url = `${apiRoot}/edupage/loadStudentAdvanced.php?id=${encodeURIComponent(
        sid
      )}`;
      console.log("[fetchStudentAdvanced] fetching", url);

      const promise = (async () => {
        const res = await fetch(url, { credentials: "include" });
        const txt = await res.text();
        let out;
        try {
          out = JSON.parse(txt);
        } catch {
          out = null;
        }

        console.log("[fetchStudentAdvanced] response", out);

        const ok = res.ok && out && statusOk(out.status) && out.data;
        if (!ok) {
          if (!state.studentsById[sid]) {
            commit(
              "UPSERT_STUDENT",
              mapStudentFromApi(sid, { name: `ID ${sid}`, email: "" })
            );
          }
          state._lastFetchAt[sid] = Date.now();
          return state.studentsById[sid] || null;
        }

        const mapped = mapStudentFromApi(sid, out.data || {});
        commit("UPSERT_STUDENT", mapped);
        state._lastFetchAt[sid] = Date.now();
        return mapped;
      })();

      state._inflight[sid] = promise;
      try {
        return await promise;
      } finally {
        delete state._inflight[sid];
      }
    },

    async setActiveFromLesson({ commit, dispatch }, id) {
      const sid = String(id);
      commit("SET_ACTIVE", sid);
      const result = await dispatch("fetchStudentAdvanced", sid);
      console.log("[setActiveFromLesson]", sid, result);
      return result;
    },
  },
};
