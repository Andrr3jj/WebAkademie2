<template>
  <div class="friend-search">
    <header class="head">
      <input
        v-model="q"
        @input="onInput"
        @keyup.enter="runSearch"
        type="text"
        placeholder="Hľadať meno alebo priezvisko…"
        autofocus
      />
      <button class="btn" @click="$emit('close')">Zavrieť</button>
    </header>

    <section class="body">
      <div v-if="loading" class="muted">Hľadám…</div>
      <div v-else-if="!q" class="muted">Napíš meno pre vyhľadávanie.</div>

      <ul v-else class="results">
        <li v-for="u in results" :key="u.id" class="result-item">
          <div class="name">{{ u.fullName }}</div>
          <button
            class="btn"
            :disabled="u._requested || isFriend(u.id) || isSelf(u.id)"
            @click="sendRequest(u)"
          >
            {{
              isSelf(u.id)
                ? "To si ty"
                : isFriend(u.id)
                ? "Už ste priatelia"
                : u._requested
                ? "Odoslané"
                : "Požiadať"
            }}
          </button>
        </li>

        <li v-if="!results.length && q && !loading" class="empty muted">
          Nenašli sa žiadni používatelia.
        </li>
      </ul>
    </section>
  </div>
</template>

<script>
import axios from "axios";
const api = axios.create({ baseURL: "/api/friend", withCredentials: true });

export default {
  name: "FriendsSearch",
  props: {
    initialQuery: { type: String, default: "" },
    friendIds: { type: Array, default: () => [] },
    currentUserId: { type: [String, Number], required: true },
  },
  emits: ["close", "requested"],
  data() {
    return {
      q: this.initialQuery,
      loading: false,
      results: [],
      debounce: null,
      pendingRequestIds: JSON.parse(
        localStorage.getItem("pendingRequestIds") || "[]"
      ),
    };
  },
  watch: {
    friendIds: {
      handler(newIds) {
        const newFriendIds = newIds.map(String);
        this.pendingRequestIds = this.pendingRequestIds.filter(
          (id) => !newFriendIds.includes(String(id))
        );
        localStorage.setItem(
          "pendingRequestIds",
          JSON.stringify(this.pendingRequestIds)
        );
      },
      immediate: true,
      deep: true,
    },
  },
  methods: {
    isFriend(id) {
      return this.friendIds.map(String).includes(String(id));
    },
    isSelf(id) {
      return String(id) === String(this.currentUserId);
    },
    async apiSearch(q) {
      const r = await api.get("/search.php", { params: { search: q } });
      const arr = Array.isArray(r.data?.data) ? r.data.data : [];
      return arr.map((u) => ({
        id: u.id,
        fullName:
          `${u.name ?? ""} ${u.surname ?? ""}`.trim() || `Používateľ #${u.id}`,
        _requested: this.pendingRequestIds.includes(u.id),
      }));
    },
    async apiSendRequest(id) {
      const r = await api.get("/request.php", { params: { friend_id: id } });
      return r.data;
    },
    onInput() {
      if (this.debounce) clearTimeout(this.debounce);
      this.debounce = setTimeout(this.runSearch, 250);
    },
    async runSearch() {
      const q = this.q.trim();
      if (!q) {
        this.results = [];
        this.loading = false;
        return;
      }
      this.loading = true;
      try {
        let res = await this.apiSearch(q);
        this.results = res.filter((u) => !this.isSelf(u.id));
      } finally {
        this.loading = false;
      }
    },
    async sendRequest(user) {
      if (user._requested || this.isFriend(user.id) || this.isSelf(user.id))
        return;
      user._requested = true;
      try {
        const data = await this.apiSendRequest(user.id);
        const ok =
          data &&
          typeof data.status === "string" &&
          data.status.toLowerCase().includes("success");
        if (!ok) {
          user._requested = false;
          this.$store &&
            (this.$store.state.SnackBarText = "Nepodarilo sa odoslať žiadosť.");
          return;
        }
        // uložíme ID do localStorage
        if (!this.pendingRequestIds.includes(user.id)) {
          this.pendingRequestIds.push(user.id);
          localStorage.setItem(
            "pendingRequestIds",
            JSON.stringify(this.pendingRequestIds)
          );
        }
        this.$emit("requested", { id: user.id });
      } catch {
        user._requested = false;
        this.$store &&
          (this.$store.state.SnackBarText = "Chyba pri odosielaní žiadosti.");
      }
    },
  },
};
</script>

<style scoped lang="scss">
.friend-search {
  display: flex;
  flex-direction: column;
  gap: 0.8em;
}
.head {
  display: flex;
  gap: 0.6em;
  align-items: center;
}
.head input {
  flex: 1;
  border: 2px solid #fef35a;
  border-radius: 999px;
  padding: 0.6em 0.9em;
  font-size: 1.05em;
  outline: none;
}
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 0;
  border-radius: 999px;
  padding: 0.6em 1.1em;
  font-weight: 700;
  font-size: 1.02em;
  cursor: pointer;
  background: #fef35a;
  color: #000;
  box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
}
.body {
  min-height: 240px;
}
.muted {
  color: #7a7a7a;
  font-style: italic;
}
.results {
  list-style: none;
  padding: 0;
  margin: 0.4em 0 0;
  display: flex;
  flex-direction: column;
  gap: 0.7em;
  max-height: 300px;
  overflow-y: auto;
}
.result-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.4em 0.2em;
  border-bottom: 1px solid #eee;
}
.name {
  font-weight: 600;
}
.empty {
  padding: 0.6em 0.2em;
  text-align: center;
}
</style>
