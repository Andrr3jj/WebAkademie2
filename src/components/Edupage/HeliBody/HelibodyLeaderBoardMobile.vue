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
        :body="top3[1].body"
      />
      <LeaderboardPlayerCard
        :poradie="1"
        v-if="top3[0]"
        :avatar="top3[0].avatar"
        :meno="top3[0].meno"
        :stars="top3[0].stars"
        :heliBody="true"
        :body="top3[0].body"
      />
      <LeaderboardPlayerCard
        :poradie="3"
        v-if="top3[2]"
        :avatar="top3[2].avatar"
        :meno="top3[2].meno"
        :stars="top3[2].stars"
        :heliBody="true"
        :body="top3[2].body"
      />
    </div>

    <div class="table-wrapper">
      <table class="player-table" v-if="ostatni.length">
        <thead>
          <tr>
            <th>Hráč</th>
            <th>Informácie</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(player, index) in ostatni" :key="index">
            <td>
              <div class="player-info">
                <span class="rank">{{ index + 4 }}.</span>
                <img
                  :src="player.avatar"
                  alt="Avatar"
                  class="avatar"
                  loading="lazy"
                />
                <span class="name">{{ player.meno }}</span>
              </div>
            </td>
            <td class="info-column">
              <div class="right">
                <TimeCubes
                  :dni="player.dni"
                  :hodiny="player.hodiny"
                  :minuty="player.minuty"
                />
              </div>
              <div class="right">
                <StarRating
                  :stars="player.stars"
                  :dni="player.dni"
                  :hodiny="player.hodiny"
                />
              </div>
              <div class="right">
                <TaskCount :count="player.ulohy" />
              </div>
              <div class="right">
                <p class="body">{{ player.body ?? 0 }}</p>
              </div>
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
  name: "HelibodyLeaderboardMobile",
  components: {
    LeaderboardPlayerCard,
    TimeCubes,
    StarRating,
    TaskCount,
  },
  data() {
    return {
      top3: [],
      ostatni: [],
      totalAchievements: 0,
    };
  },
  mounted() {
    this.nacitajTotalAchievements().then(() => this.nacitajData());
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
    initialsFromName(fullName) {
      const parts = fullName.trim().split(/\s+/);
      if (parts.length === 0) return "";
      if (parts.length === 1) return parts[0].slice(0, 2).toUpperCase();
      return (parts[0][0] + parts[1][0]).toUpperCase();
    },
    makeInitialsAvatar(initials) {
      const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32"><rect width="100%" height="100%" fill="#ddd"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" font-family="Bahnschrift, Arial, sans-serif" font-size="14" fill="#555">${initials}</text></svg>`;
      const encoded =
        typeof btoa !== "undefined" ? btoa(svg) : window.btoa(svg);
      return `data:image/svg+xml;base64,${encoded}`;
    },
    async nacitajData() {
      try {
        const response = await axios.get(
          "https://heligonkovaakademia.sk/api/achievements/getTabulka.php"
        );
        if (response.data.status === "Request succesfull") {
          const raw = Object.values(response.data.data || {});
          raw.sort((a, b) => a.rank - b.rank);
          const parsed = raw.map((player) => {
            const { dni, hodiny, minuty } = this.rozdelitMinuty(
              +player.helitime || 0
            );
            const fullName = `${(player.name || "").trim()} ${(
              player.surname || ""
            ).trim()}`.trim();
            const avatarProcessed = player.profilePhotoUrl
              ? this.stripWWW(player.profilePhotoUrl)
              : this.makeInitialsAvatar(this.initialsFromName(fullName));
            return {
              meno: fullName,
              avatar: avatarProcessed,
              dni,
              hodiny,
              minuty,
              stars: this.vypocitajHviezdy(dni, hodiny),
              ulohy: Math.max(
                (this.totalAchievements || 0) -
                  (Number(player.finishedAchievements) || 0),
                0
              ),
              body: Number(player.points ?? player.body ?? 0),
            };
          });
          this.top3 = parsed.slice(0, 3);
          this.ostatni = parsed.slice(3);
        }
      } catch {
        this.top3 = [];
        this.ostatni = [];
      }
    },
    async nacitajTotalAchievements() {
      try {
        const response = await axios.get(
          "https://heligonkovaakademia.sk/api/achievements/getAchievementsStats.php"
        );
        if (response.data.status === "Request succesfull") {
          this.totalAchievements =
            +(
              response.data.totalAchievements ??
              response.data.data?.total ??
              response.data.data?.totalAchievements ??
              0
            ) || 0;
        }
      } catch {
        this.totalAchievements = 0;
      }
    },
    rozdelitMinuty(minuty) {
      const dni = Math.floor(minuty / 1440);
      const zost = minuty % 1440;
      const hodiny = Math.floor(zost / 60);
      const minutyZost = zost % 60;
      return { dni, hodiny, minuty: minutyZost };
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
  },
};
</script>

<style scoped lang="scss">
@import "@/assets/sass/_colors.scss";

h2 {
  font-family: "Bahnshrift", sans-serif;
  font-size: 5vw;
  margin-bottom: 0.2em;
  text-align: center;
}

.table-row:hover,
tbody tr:hover {
  transform: scale(1);
}

.title {
  font-family: "Bahnshrift", sans-serif;
  font-size: 4.7vw;
  margin-bottom: 0.2em;
  text-align: center;
  margin-top: 1em;
}

.podnadpis {
  font-family: "Bahnshrift", sans-serif;
  font-size: 1.75em;
  text-align: center;
  margin-bottom: 2em;
}

.top-players {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6em;
  margin-bottom: 7em;
}

.top-players,
.player-table,
.table-wrapper {
  will-change: transform, opacity;
}

.table-wrapper {
  width: 100%;
  margin-bottom: 3em;
}

.player-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.95em;
}

.player-table th,
.player-table td {
  padding: 0.6em;
  text-align: left;
}

.player-info {
  display: flex;
  align-items: center;
  gap: 0.5em;
  font-size: 1.2em;
}

.body {
  font-size: 2.25em;
  font-weight: 800;
  color: #00913f;
  line-height: 1;
  font-family: "Adumu", sans-serif;
}

.info-column {
  display: flex;
  flex-direction: column;
  gap: 0.4em;
  align-items: stretch;
}

.centered {
  display: flex;
  justify-content: center;
}

.right {
  display: flex;
  align-items: flex-end;
}

.rank {
  font-weight: bold;
}

.avatar {
  width: 32px;
  height: 32px;
  border-radius: 8px;
  border: 2px solid #fef35a;
  object-fit: cover;
}

.name {
  font-weight: 600;
}

.how-it-works {
  padding: 1.25em 1em 1.5em;
  border-radius: 1em;
}

.hiw-head {
  margin-bottom: 1em;
}
.hiw-title {
  margin: 0;
  font-family: "Bahnshrift", sans-serif;
  font-weight: 800;
  font-size: 2em;
  line-height: 1.15;
  text-align: center;
}

.hiw-row {
  display: grid;
  grid-template-columns: 1fr; /* mobil = stĺpce pod seba */
  gap: 0.9em;
  padding: 1.1em 0;
}

.hiw-left {
  display: grid;
  grid-template-columns: 3em 1fr;
  gap: 0.9em;
  align-items: center;
}

.hiw-icon {
  width: 3em;
  height: 3em;
  object-fit: contain;
  filter: drop-shadow(0 0.25em 0.35em rgba(0, 0, 0, 0.18));
}

.hiw-left-text {
  display: grid;
  gap: 0.25em;
}
.hiw-left-title {
  font-family: "Bahnshrift", sans-serif;
  font-weight: 800;
  font-size: 2em;
  line-height: 1.05;
  letter-spacing: 0.01em;
}
.hiw-left-sub {
  font-family: "Bahnshrift", sans-serif;
  font-weight: 600;
  font-size: 1.1em;
  color: #7a7a7a;
}

.hiw-right {
  padding-left: 3.9em;
} /* zarovnanie pod text vľavo (ikona + medzera) */
.hiw-right p {
  margin: 0 0 0.5em 0;
  font-family: "Bahnshrift", sans-serif;
  font-weight: 600;
  font-size: 1.35em;
  line-height: 1.5;
}
.hiw-right b {
  font-weight: 800;
}

/* o kúsok väčšie telefóny */
@media (min-width: 28em) {
  .hiw-title {
    font-size: 2.2em;
  }
  .hiw-left-title {
    font-size: 2.2em;
  }
  .hiw-right p {
    font-size: 1.45em;
  }
}

/* malé tablety – dovolíme 2 stĺpce, ak je miesto */
@media (min-width: 48em) {
  .how-it-works {
    padding: 1.4em 1.2em 1.6em;
  }
  .hiw-title {
    font-size: 2.4em;
  }
  .hiw-row {
    grid-template-columns: 32em 1fr; /* ľavý blok fixný v em, pravý plynulý */
    gap: 2em;
  }
  .hiw-right {
    padding-left: 0;
  }
  .hiw-left {
    grid-template-columns: 3.2em 1fr;
  }
  .hiw-left-title {
    font-size: 2.4em;
  }
  .hiw-right p {
    font-size: 1.6em;
  }
}
</style>
