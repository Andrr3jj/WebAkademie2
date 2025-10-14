<template>
  <div class="leaderboard-wrapper helibody">
    <p class="title">
      V Heligónkovej Akadémii nezbierame známky, ale hviezdičky za snahu, pokrok
      a chuť hrať. Za každú lekciu môžeš získať 1 až 5 hviezdičiek, podľa toho,
      ako si pripravený. Každá hviezdička sa premení na body, ktoré sa ti
      automaticky započítajú do rebríčka.
    </p>
    <h2 class="podnadpis">
      Na konci školského roka čakajú na najlepších z vás odmeny!
    </h2>

    <div class="top-players">
      <LeaderboardPlayerCard
        :poradie="2"
        v-if="top3[1]"
        :avatar="top3[1].avatar"
        :meno="top3[1].meno"
        :stars="top3[1].stars"
        :heliBody="true"
        :body="top3[1].points"
      />
      <LeaderboardPlayerCard
        :poradie="1"
        v-if="top3[0]"
        :avatar="top3[0].avatar"
        :meno="top3[0].meno"
        :stars="top3[0].stars"
        :heliBody="true"
        :body="top3[0].points"
      />
      <LeaderboardPlayerCard
        :poradie="3"
        v-if="top3[2]"
        :avatar="top3[2].avatar"
        :meno="top3[2].meno"
        :stars="top3[2].stars"
        :heliBody="true"
        :body="top3[2].points"
      />
    </div>

    <div class="table-wrapper">
      <table class="player-table" v-if="rows.length">
        <thead>
          <tr>
            <th>Názov účtu:</th>
            <th>Čas výučby:</th>
            <th>Počet hviezd:</th>
            <th>Úlohy:</th>
            <th>Body:</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(p, i) in rows" :key="i">
            <td>
              <div class="player-info">
                <span class="rank">{{ i + 4 }}.</span>
                <img :src="p.avatar" alt="Avatar" class="avatar" />
                <span class="name">{{ p.meno }}</span>
              </div>
            </td>
            <td>
              <TimeCubes :dni="p.dni" :hodiny="p.hodiny" :minuty="p.minuty" />
            </td>
            <td class="stars-cell">
              <StarRating :stars="p.stars" :dni="p.dni" :hodiny="p.hodiny" />
            </td>
            <td class="tasks-cell">
              <TaskCount :count="p.ulohy" />
            </td>
            <td class="points-cell">
              <p class="body">{{ p.points ?? 0 }}</p>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="line horizontal"></div>

    <div class="how-it-works helibody-info">
      <div class="hiw-head">
        <h3 class="hiw-title">Ako získavaš body?</h3>
      </div>

      <div class="hiw-row">
        <div class="hiw-left">
          <img
            class="hiw-icon"
            src="@/assets/images/icons/star.svg"
            alt="Hviezdička"
          />
          <div class="hiw-left-text">
            <div class="hiw-left-title">Hviezdičky za lekcie:</div>
            <div class="hiw-left-sub">Jedna hviezdička = 1bod</div>
          </div>
        </div>
        <div class="hiw-right">
          <p>Po každej hodine ti učiteľ udelí 1 až 5 hviezdičiek</p>
          <p>Zohľadňuje sa: <b>pripravenosť, pokrok, aktivita a prístup</b></p>
        </div>
      </div>

      <div class="hiw-row">
        <div class="hiw-left">
          <img
            class="hiw-icon"
            src="@/assets/images/icons/calendar_body.svg"
            alt="Kalendár"
          />
          <div class="hiw-left-text">
            <div class="hiw-left-title">Body za dochádzku:</div>
            <div class="hiw-left-sub">100% dochádzka = 2body</div>
          </div>
        </div>
        <div class="hiw-right">
          <p>Ak v danom mesiaci prídeš na všetky naplánované hodiny</p>
          <p>získaš extra body navyše ako odmenu za zodpovednosť</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import LeaderboardPlayerCard from "@/components/Helicas/Leaderboard/LeaderboardPlayerCard.vue";
import TimeCubes from "@/components/Helicas/Leaderboard/TimeCubes.vue";
import StarRating from "@/components/Helicas/Leaderboard/StarRating.vue";
import TaskCount from "@/components/Helicas/Leaderboard/TaskCount.vue";

export default {
  props: {
    limit: { type: Number, default: 50 },
    // Ak príde zoznam hráčov zvonka, komponent ho použije a NEBUDE fetchovať
    items: { type: Array, default: null },
    // Ak true, vypne vnútorné fetchovanie aj keď items nie sú poskytnuté
    disableFetch: { type: Boolean, default: false },
    // Voliteľne vieme doposlať počet všetkých úloh
    totalAchievementsProp: { type: Number, default: null },
  },
  name: "HelibodyLeaderboard",
  components: { LeaderboardPlayerCard, TimeCubes, StarRating, TaskCount },
  data() {
    return {
      top3: [],
      rows: [],
      totalAchievements: 0,
    };
  },
  mounted() {
    // Ak sú dáta poslané cez props, spracuj a nefetchuj
    if (Array.isArray(this.items) && this.items.length) {
      this.useProvidedItems(this.items);
      if (typeof this.totalAchievementsProp === "number") {
        this.totalAchievements = this.totalAchievementsProp;
      }
      return;
    }

    // Ak je fetch vypnutý, nerob nič ďalšie
    if (this.disableFetch) return;

    // Inak fallback na pôvodné fetchovanie
    this.loadTotals().then(() => this.loadTable());
  },
  watch: {
    items: {
      immediate: false,
      deep: true,
      handler(val) {
        if (Array.isArray(val) && val.length) {
          this.useProvidedItems(val);
        } else if (!this.disableFetch) {
          // keď items vyprázdniš a fetch nie je vypnutý, skús fallback fetch
          this.loadTotals().then(() => this.loadTable());
        } else {
          this.top3 = [];
          this.rows = [];
        }
      },
    },
    totalAchievementsProp(val) {
      if (typeof val === "number") this.totalAchievements = val;
    },
  },
  methods: {
    stripWWW(url) {
      if (!url) return null;
      try {
        const u = new URL(url);
        if (u.hostname.startsWith("www.")) u.hostname = u.hostname.slice(4);
        return u.toString();
      } catch {
        return url.replace(/^https?:\/\/www\./, (m) => m.replace("www.", ""));
      }
    },
    async loadTable() {
      if (
        (Array.isArray(this.items) && this.items.length) ||
        this.disableFetch
      ) {
        return; // nepokračuj vo fetchovaní
      }
      try {
        const r = await axios.get(
          "https://heligonkovaakademia.sk/api/achievements/getTabulka.php",
          { params: { limit: this.limit } } // ⬅️ pošli limit=50
        );

        if (r.data.status === "Request succesfull") {
          const raw = Object.values(r.data.data || {}).sort(
            (a, b) => a.rank - b.rank
          );

          const parsed = raw.map((player) => {
            const { dni, hodiny, minuty } = this.splitMinutes(+player.helitime);
            const fullName = `${(player.name || "").trim()} ${(
              player.surname || ""
            ).trim()}`.trim();
            return {
              meno: fullName,
              avatar: player.profilePhotoUrl
                ? this.stripWWW(player.profilePhotoUrl)
                : null,
              dni,
              hodiny,
              minuty,
              stars: this.computeStars(dni, hodiny),
              ulohy: player.finishedAchievements || 0,
              points: Number(player.points ?? player.body ?? 0),
            };
          });

          this.top3 = parsed.slice(0, 3);
          this.rows = parsed.slice(3);
        }
      } catch (e) {
        console.error("Chyba pri načítaní tabuľky:", e);
      }
    },
    async loadTotals() {
      if (
        (Array.isArray(this.items) && this.items.length) ||
        this.disableFetch
      ) {
        if (typeof this.totalAchievementsProp === "number") {
          this.totalAchievements = this.totalAchievementsProp;
        }
        return;
      }
      try {
        const r = await axios.get(
          "https://heligonkovaakademia.sk/api/achievements/getAchievementsStats.php"
        );
        if (r.data.status === "Request succesfull") {
          this.totalAchievements =
            +(
              r.data.totalAchievements ??
              r.data.data?.total ??
              r.data.data?.totalAchievements ??
              0
            ) || 0;
        }
      } catch (e) {
        console.error("Chyba pri načítaní totals:", e);
      }
    },
    splitMinutes(min) {
      const dni = Math.floor(min / 1440);
      const rest = min % 1440;
      const hodiny = Math.floor(rest / 60);
      const minuty = rest % 60;
      return { dni, hodiny, minuty };
    },
    computeStars(dni, hodiny) {
      const h = dni * 24 + hodiny;
      if (h < 8) return 0;
      if (h < 24) return 1;
      if (dni === 1) return 2;
      if (dni >= 2 && dni < 5) return 3;
      if (dni >= 5 && dni < 10) return 4;
      return 5;
    },
    formatCas({ dni, hodiny, minuty }) {
      const parts = [];
      if (dni === 1) parts.push("1 deň");
      else if (dni >= 2 && dni <= 4) parts.push(`${dni} dni`);
      else if (dni >= 5) parts.push(`${dni} dní`);
      if (hodiny === 1) parts.push("1 hodina");
      else if (hodiny >= 2 && hodiny <= 4) parts.push(`${hodiny} hodiny`);
      else if (hodiny >= 5) parts.push(`${hodiny} hodín`);
      if (minuty === 1) parts.push("1 minúta");
      else if (minuty >= 2 && minuty <= 4) parts.push(`${minuty} minúty`);
      else if (minuty >= 5) parts.push(`${minuty} minút`);
      return parts.join(" ");
    },
    useProvidedItems(list) {
      try {
        const raw = Array.isArray(list) ? list : [];
        const parsed = raw.map((player) => {
          const helitimeNum = Number(player.helitime || 0);
          const { dni, hodiny, minuty } = this.splitMinutes(helitimeNum);
          const fullName = `${(player.name || "").trim()} ${(
            player.surname || ""
          ).trim()}`.trim();
          return {
            meno: fullName,
            avatar: player.profilePhotoUrl
              ? this.stripWWW(player.profilePhotoUrl)
              : null,
            dni,
            hodiny,
            minuty,
            stars: this.computeStars(dni, hodiny),
            ulohy: 0,
            points: Number(player.stars ?? 0),
          };
        });
        this.top3 = parsed.slice(0, 3);
        this.rows = parsed.slice(3);
      } catch (e) {
        console.error("Chyba pri spracovaní poskytnutých items:", e);
        this.top3 = [];
        this.rows = [];
      }
    },
  },
};
</script>

<style scoped>
.title {
  font-family: "Bahnshrift", sans-serif;
  font-size: 1.8125em;
  text-align: center;
  margin-bottom: 0.2em;
  padding: 3em;
  width: 75%;
  margin-inline: auto;
}
.podnadpis {
  font-family: "Bahnshrift", sans-serif;
  font-size: 2em;
  text-align: center;
  margin-bottom: 3em;
  margin-top: -2em;
}

.top-players {
  display: grid;
  grid-template-columns: 1fr auto 1fr;
  align-items: end;
  justify-items: center;
  margin-bottom: 3em;
}

.table-wrapper {
  overflow-x: auto;
  width: 100%;
  margin: 2.5em 0 4em;
}
.player-table {
  width: 90%;
  margin: 0 auto;
  border-collapse: collapse;
  font-size: 90%;
}
.player-table th {
  font-size: 1.25em;
}
.player-table td,
.player-table th {
  padding: 0.5em 0.8em;
  text-align: left;
}
.player-table th:nth-child(3) {
  text-align: center;
  width: 10%;
}
.player-table td:nth-child(3) {
  text-align: right;
}
.player-table td:nth-child(1) {
  width: 45%;
}
.player-table td:nth-child(2) {
  display: flex;
}

.player-info {
  display: flex;
  align-items: center;
  gap: 0.5em;
  font-family: "Bahnshrift", sans-serif;
}
.rank {
  font-size: 1.25em;
  font-weight: bold;
}
.avatar {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  object-fit: cover;
  border: 2px solid #fef35a;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.25);
}
.name {
  font-size: 1.25em;
  font-weight: 600;
}

.body {
  font-size: 2.25em;
  font-weight: 800;
  color: #00913f;
  line-height: 1;
  font-family: "Adumu", sans-serif;
}

.stars-cell,
.tasks-cell,
.points-cell {
  text-align: center;
  width: 1%;
  padding: 0 0.5em;
}
.stars-cell > *,
.tasks-cell > *,
.points-cell > * {
  margin: 0 auto;
}

.line.horizontal {
  width: 90%;
  height: 4px;
  margin: 2.5em auto;
  background: #f2e94b;
  border-radius: 2px;
}

.how-it-works {
  padding: 1.5em 1.375em 1.75em;
  border-radius: 1.125em;
}

.hiw-head {
  margin-bottom: 1.25em;
}
.hiw-title {
  margin: 0;
  font-family: "Bahnshrift", sans-serif;
  font-weight: 800;
  font-size: 2.5em;
  line-height: 1.1;
  text-align: center;
}

.hiw-row {
  display: flex;
  flex-direction: row;
  justify-content: center;
  gap: 6em;
  padding: 1.5em 0;
}

.hiw-left {
  display: grid;
  grid-template-columns: 4em 1fr;
  gap: 1.1em;
  align-items: center;
}

.hiw-icon {
  width: 4em;
  height: 4em;
  object-fit: contain;
  filter: drop-shadow(0 0.25em 0.375em rgba(0, 0, 0, 0.18));
}

.hiw-left-text {
  display: grid;
  gap: 0.35em;
}
.hiw-left-title {
  font-family: "Bahnshrift", sans-serif;
  font-weight: 800;
  font-size: 2em;
  text-align: left;
}
.hiw-left-sub {
  font-family: "Bahnshrift", sans-serif;
  font-size: 1.25em;
  color: rgba(0, 0, 0, 0.5);
  text-align: left;
}

.hiw-right {
  padding-top: 0.25em;
  display: flex;
  flex-direction: column;
  gap: 0.5em;
}
.hiw-right p {
  font-family: "Bahnshrift", sans-serif;
  font-size: 1.5em;
  text-align: left;
}
.hiw-right b {
  font-weight: 800;
}

@media (max-width: 80em) {
  .hiw-title {
    font-size: 2.5em;
  }
  .hiw-row {
    grid-template-columns: 1fr;
    gap: 1.25em;
  }
  .hiw-left {
    grid-template-columns: 3.5em 1fr;
  }
  .hiw-icon {
    width: 3.5em;
    height: 3.5em;
  }
  .hiw-left-title {
    font-size: 2.4em;
  }
  .hiw-left-sub {
    font-size: 1.25em;
  }
  .hiw-right p {
    font-size: 1.6em;
  }
}

@media (max-width: 48em) {
  .how-it-works {
    padding: 1.25em 1em 1.5em;
  }
  .hiw-title {
    font-size: 2em;
  }
  .hiw-left {
    grid-template-columns: 3em 1fr;
    gap: 0.9em;
  }
  .hiw-icon {
    width: 3em;
    height: 3em;
  }
  .hiw-left-title {
    font-size: 2em;
  }
  .hiw-left-sub {
    font-size: 1.1em;
  }
  .hiw-right p {
    font-size: 1.3em;
    line-height: 1.45;
  }
}
</style>
