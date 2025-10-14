<template>
  <div class="friend-list">
    <header class="header">
      <div class="title-wrap">
        <h2 class="title">Heligónková partia</h2>
        <span class="count">({{ friends.length }})</span>
        <div v-if="!expanded" class="compact-row">
          <div class="mini-avatars">
            <div
              v-for="friend in friends.slice(0, 4)"
              :key="friend.id"
              class="mini"
            >
              <img :src="friend.profilePhotoUrl" alt="" />
            </div>
          </div>
          <button class="link-more" @click="expanded = true">
            <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
            Zobraziť viac
          </button>
        </div>
      </div>
      <button class="btn" @click="$emit('open-search')">
        <img src="@/assets/images/icons/priatelia.svg" />
        <p>Pridať priateľa</p>
      </button>
    </header>

    <ul v-if="expanded" class="list">
      <li v-for="item in combinedItems" :key="item._key" class="item">
        <div class="col-left">
          <div
            class="avatar-shell"
            :class="{
              online: item.online === true,
              offline: item.online === false,
              pending: item._type === 'request',
            }"
          >
            <div class="avatar-ring">
              <img :src="item.profilePhotoUrl" alt="" class="avatar" />
            </div>
            <span class="status-dot"></span>
          </div>
          <span class="name">{{ item.name }}</span>
        </div>

        <template v-if="item._type === 'friend'">
          <div class="col-right">
            <div class="time">
              <TimeCubes
                :dni="item.dobaHranie.dni"
                :hodiny="item.dobaHranie.hodiny"
                :minuty="item.dobaHranie.minuty"
              />
            </div>
            <div class="stars">
              <StarRating :stars="item.stars" />
            </div>
            <div class="heligonka">
              <TaskCount :count="item.heligonky" />
            </div>

            <button
              class="icon-btn icon-btn--remove"
              @click="unfriend(item)"
              aria-label="Odstrániť priateľa"
              title="Odstrániť"
            >
              <svg
                viewBox="0 0 24 24"
                stroke="currentColor"
                fill="none"
                stroke-width="2"
              >
                <path d="M3 6h18" />
                <path d="M8 6v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" />
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                <path d="M10 11v6" />
                <path d="M14 11v6" />
              </svg>
            </button>
          </div>
        </template>

        <template v-else>
          <div class="actions">
            <button
              class="btn btn-green-text"
              @click="acceptRequest(item._raw)"
            >
              <img src="@/assets/images/icons/pridaťPriateľa.svg" />
              <p>Potvrdiť</p>
            </button>

            <button
              class="icon-btn icon-btn--remove"
              @click="declineRequest(item._raw)"
              aria-label="Zamietnuť žiadosť"
              title="Zamietnuť žiadosť"
            >
              <svg
                viewBox="0 0 24 24"
                stroke="currentColor"
                fill="none"
                stroke-width="2"
              >
                <path d="M3 6h18" />
                <path d="M8 6v-1a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v1" />
                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6" />
                <path d="M10 11v6" />
                <path d="M14 11v6" />
              </svg>
            </button>
          </div>
        </template>
      </li>
    </ul>

    <div class="footer">
      <button
        v-if="expanded && friends.length > 0"
        class="show-less"
        @click="expanded = false"
      >
        Zobraziť menej
      </button>
      <div v-else-if="!expanded && friends.length === 0" class="no-friends">
        Zatiaľ nemáte žiadneho priateľa
      </div>
      <div class="line horizontal" />
    </div>
  </div>
</template>

<script>
import axios from "axios";
import TimeCubes from "@/components/Helicas/Leaderboard/TimeCubes.vue";
import StarRating from "@/components/Helicas/Leaderboard/StarRating.vue";
import TaskCount from "@/components/Helicas/Leaderboard/TaskCount.vue";

const api = axios.create({ baseURL: "/api/friend", withCredentials: true });
const ONLINE_WINDOW_SEC = 60 * 5;

export default {
  name: "FriendList",
  components: { TimeCubes, StarRating, TaskCount },

  data() {
    return {
      friends: [],
      requests: [],
      expanded: false,
      reqTimer: null,
      visHandler: null,
    };
  },

  computed: {
    combinedItems() {
      const requestRows = this.requests.map((r) => this.mapRequest(r));
      const friendRows = this.friends.map((f) => ({
        ...f,
        _type: "friend",
        _key: `f-${f.relation_key}-${f.id}`,
      }));
      return [...requestRows, ...friendRows];
    },
    firstFour() {
      return this.friends.slice(0, 4);
    },
    unreadCount() {
      return this.requests.length;
    },
  },

  methods: {
    apiListFriends() {
      return api.get("/list_friendship.php").then((r) => {
        const data = r?.data?.data;
        if (!data) return [];
        if (Array.isArray(data))
          return data.map((u) => ({
            ...u,
            relation_key: String(u.request_id || u.id_relation || u.id),
          }));
        if (typeof data === "object")
          return Object.entries(data).map(([key, user]) => ({
            ...user,
            relation_key: String(key),
          }));
        return [];
      });
    },

    apiListRequests() {
      return api.get("/list_request.php").then((r) => r.data?.data ?? []);
    },

    apiDecide(reqId, accept) {
      return api.get("/opt.php", { params: { request_id: reqId, accept } });
    },

    rozdelitMinuty(minuty) {
      const dni = Math.floor(minuty / (24 * 60));
      const zH = minuty % (24 * 60);
      const hodiny = Math.floor(zH / 60);
      const zostavajuceMinuty = zH % 60;
      return { dni, hodiny, minuty: zostavajuceMinuty };
    },

    vypocitajHviezdy(dni, hodiny) {
      const totalHours = dni * 24 + hodiny;
      if (totalHours < 8) return 0;
      if (totalHours < 24) return 1;
      if (dni === 1) return 2;
      if (dni >= 2 && dni < 5) return 3;
      if (dni >= 5 && dni < 10) return 4;
      return 5;
    },

    isOnline(ts) {
      const last = parseInt(ts ?? 0, 10);
      if (!last) return false;
      const now = Math.floor(Date.now() / 1000);
      const diff = Math.max(0, now - last);
      return diff <= ONLINE_WINDOW_SEC;
    },

    mapFriend(x) {
      let photoUrl =
        x.profilePhotoUrl ?? x.avatar ?? `https://i.pravatar.cc/100?u=${x.id}`;
      photoUrl = photoUrl.replace("://www.", "://");
      const helitimeMinutes = Number.isFinite(Number(x.helitime))
        ? Number(x.helitime)
        : 0;
      const dobaHranie = this.rozdelitMinuty(helitimeMinutes);
      const stars = this.vypocitajHviezdy(dobaHranie.dni, dobaHranie.hodiny);
      return {
        id: x.id,
        relation_key: x.relation_key,
        _raw: x,
        name:
          [`${x.name ?? ""}`.trim(), `${x.surname ?? ""}`.trim()]
            .filter(Boolean)
            .join(" ") || `Používateľ #${x.id}`,
        profilePhotoUrl: photoUrl,
        dobaHranie,
        stars,
        heligonky: Number(x.achievements_finished_count ?? 0),
        online: this.isOnline(x.online),
      };
    },

    mapRequest(r) {
      const requestId = r.id;
      const from = r.request_from || {};
      const userId = from.id ?? r.user_id1 ?? r.user_id2 ?? requestId;
      const fullName =
        [`${from.name ?? ""}`.trim(), `${from.surname ?? ""}`.trim()]
          .filter(Boolean)
          .join(" ") || `Žiadosť od používateľa #${userId}`;
      let photoUrl =
        from.profilePhotoUrl ??
        from.avatar ??
        `https://i.pravatar.cc/100?u=req-${userId}`;
      photoUrl = photoUrl.replace("://www.", "://");
      return {
        _type: "request",
        _key: `r-${requestId}`,
        _raw: r,
        _requestId: requestId,
        id: userId,
        name: fullName,
        profilePhotoUrl: photoUrl,
        online: false,
      };
    },

    async acceptRequest(req) {
      const id = req._requestId ?? req.request_id ?? req.id;
      await this.apiDecide(id, "yes");
      await Promise.all([this.loadFriends(), this.loadRequests()]);
    },

    async declineRequest(req) {
      const id = req._requestId ?? req.request_id ?? req.id;
      await this.apiDecide(id, "no");
      await this.loadRequests();
    },

    async loadFriends() {
      const arr = await this.apiListFriends();
      this.friends = arr.map(this.mapFriend);
    },

    async loadRequests() {
      const data = await this.apiListRequests();
      this.requests = Array.isArray(data)
        ? data
        : data && typeof data === "object"
        ? [data]
        : [];
    },

    startRequestsPolling(immediate = true) {
      // Ochrana proti duplicitnému intervalu
      if (this.reqTimer) {
        clearInterval(this.reqTimer);
        this.reqTimer = null;
      }
      const tick = () => {
        if (document.visibilityState === "visible")
          this.loadRequests().catch(() => {});
      };
      if (immediate) tick();
      this.reqTimer = setInterval(tick, 4000);
    },

    stopRequestsPolling() {
      if (this.reqTimer) clearInterval(this.reqTimer);
      this.reqTimer = null;
    },

    async unfriend(friend) {
      try {
        const res = await this.apiDecide(friend.relation_key, "no");
        const ok =
          res?.data &&
          typeof res.data.status === "string" &&
          res.data.status.toLowerCase().includes("succes");
        if (!ok) throw new Error();
        this.friends = this.friends.filter(
          (f) => String(f.relation_key) !== String(friend.relation_key)
        );
        this.$store && (this.$store.state.SnackBarText = "Priateľ odstránený.");
      } catch {
        this.$store &&
          (this.$store.state.SnackBarText =
            "Nepodarilo sa odstrániť priateľa.");
      }
    },
  },

  async mounted() {
    await this.loadFriends();
    this.startRequestsPolling(true);
    this.visHandler = () => {
      if (document.visibilityState === "visible") {
        this.startRequestsPolling(false);
      } else {
        this.stopRequestsPolling();
      }
    };
    document.addEventListener("visibilitychange", this.visHandler);
  },

  beforeUnmount() {
    this.stopRequestsPolling();
    if (this.visHandler) {
      document.removeEventListener("visibilitychange", this.visHandler);
      this.visHandler = null;
    }
  },

  deactivated() {
    // Zastaví interval aj pri použití <keep-alive>
    this.stopRequestsPolling();
  },
  expose: ["friends"],
};
</script>

<style scoped lang="scss">
.friend-list {
  font-family: "Bahnschrift", system-ui, sans-serif;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 0.6em 0 0.8em;
}

.title-wrap {
  display: flex;
  flex-direction: column;
  gap: 0.4em;
}

.title {
  font-size: 1.5em;
  font-weight: 500;
  margin-top: 1em;
  text-align: left;
}

.count {
  font-size: 1.25em;
  color: rgba(0, 0, 0, 0.6);
  margin-top: -1.2em;
  text-align: left;
}

.compact-row {
  display: flex;
  align-items: center;
  gap: 0.8em;
  margin-left: 1em;
  margin-top: -1.5em;
}

.mini-avatars {
  display: flex;
  align-items: center;
}
.mini {
  width: 30px;
  height: 29px;
  border-radius: 50%;
  border: 3px solid #fef35a;
  overflow: hidden;
  background: #fff;
  margin-left: -10px;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
}
.mini:first-child {
  margin-left: 0;
}
.mini img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.link-more {
  position: relative;
  border: 0;
  background: transparent;
  color: #6b6b6b;
  font-size: 1.05em;
  cursor: pointer;
  padding-left: 0.8em;
}
.badge {
  position: absolute;
  top: -0.8em;
  transform: translateX(-100%);
  background: #e3503c;
  color: #fff;
  font-weight: 800;
  border-radius: 999px;
  padding: 0.2em 0.5em;
  font-size: 0.95em;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25),
    inset 0 1px 3px rgba(0, 0, 0, 0.35);
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 0.6em;
  border: 0;
  border-radius: 999px;
  padding: 0.6em 1.1em;
  font-weight: 700;
  font-size: 1.1em;
  cursor: pointer;
  box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
  white-space: nowrap;
  color: #000;
  background: #fef35a;
}

.btn-green-text {
  color: #00913f;
  padding: 0.3em 2em;
}

.actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1em;
}

.icon-btn {
  background: transparent;
  border: 0;
  padding: 0.25em;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}
.icon-btn svg {
  width: 15px;
  height: 15px;
}
.icon-btn--remove {
  color: #c0342b;
}
.icon-btn--remove:hover {
  opacity: 0.9;
  transform: translateY(-0.5px);
  transition: opacity 120ms, transform 120ms;
}

.list {
  list-style: none;
  padding: 0;
  margin: 0.6em 0 1.2em;
  display: flex;
  flex-direction: column;
  gap: 1.2em;
}

.item {
  display: flex;
  align-items: center;
  gap: 1.8em;
  justify-content: space-between;
}

.col-left {
  display: flex;
  align-items: center;
  gap: 0.8em;
}

.avatar-shell {
  position: relative;
  width: 56px;
  height: auto;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  filter: drop-shadow(0 6px 18px rgba(0, 0, 0, 0.18));
  flex-direction: column;
}
.avatar-ring {
  width: 30px;
  height: 29px;
  border-radius: 50%;
  border: 3px solid #fef35a;
  overflow: hidden;
  background: #fff;
}
.avatar {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.status-dot {
  position: absolute;
  bottom: 1.5em;
  right: 0;
  width: 13px;
  height: 13px;
  border-radius: 50%;
  background: #cfcfcf;
  border: 2px solid #fef35a;
  transform: translate(0, 50%);
}
.avatar-shell.online .status-dot {
  background: #90ca50;
}
.avatar-shell.pending .status-dot {
  background: #cfcfcf;
}

.name {
  font-size: 1.1em;
  font-weight: 600;
}

.col-right {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 0.5em;
  .stars {
    width: 8em;
    max-width: 8em;
  }
}
:deep(.hviezda) {
  width: 1.5em;
}

.footer {
  display: flex;
  align-items: center;
  margin-top: 1.2em;
  flex-direction: column;
}
.no-friends {
  margin-bottom: 0.5em;
}
.show-less {
  font-size: 1.2em;
  color: #7a7a7a;
  margin: 0.4em 0;
  background: none;
  border: 0;
  cursor: pointer;
}

.context-menu {
  position: fixed;
  z-index: 3000;
  background: #fff;
  border-radius: 14px;
  border: 1px solid #eee;
  box-shadow: 0 10px 26px rgba(0, 0, 0, 0.12);
  min-width: 230px;
  padding: 6px;
  animation: appearMenu 0.16s cubic-bezier(0.42, 0, 0.6, 1.42);
}
@keyframes appearMenu {
  from {
    opacity: 0;
    transform: scale(0.97);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}
.menu-item {
  padding: 0.62em 1em;
  border-radius: 10px;
  cursor: pointer;
  font-weight: 600;
}
.menu-item:hover {
  background: #fff8b8;
}
.menu-item.danger {
  color: #c0342b;
}
.menu-item.danger:hover {
  background: #ffe4e1;
}

@media (max-width: 1024px) {
  .compact-row {
    margin-left: 1.2em;
  }
  .item {
    align-items: center;
    gap: 0.6em;
  }
}
@media (max-width: 850px) {
  .col-right {
    flex-direction: column;
    align-items: center;
  }
}
@media (max-width: 750px) {
  .header {
    flex-direction: column;
    gap: 1em;
    margin-bottom: 1.5em;
  }
  .compact-row {
    margin-left: 2em;
  }
}
</style>
