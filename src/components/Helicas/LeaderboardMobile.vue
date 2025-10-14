<template>
  <div class="leaderboard-wrapper">
    <h2 class="title">Rebríček najvytrvalejších hráčov</h2>
    <p class="podnadpis">Kto trávi najviac času učením?</p>

    <div class="top-players">
      <LeaderboardPlayerCard
        :poradie="1"
        v-if="top3[0]"
        :avatar="top3[0].avatar"
        :meno="top3[0].meno"
        :cas="formatCas(top3[0])"
        :stars="top3[0].stars"
      />
      <LeaderboardPlayerCard
        :poradie="2"
        v-if="top3[1]"
        :avatar="top3[1].avatar"
        :meno="top3[1].meno"
        :cas="formatCas(top3[1])"
        :stars="top3[1].stars"
      />
      <LeaderboardPlayerCard
        :poradie="3"
        v-if="top3[2]"
        :avatar="top3[2].avatar"
        :meno="top3[2].meno"
        :cas="formatCas(top3[2])"
        :stars="top3[2].stars"
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
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="line horizontal"></div>
    <div class="how-it-works">
      <h3 class="how-title">Ako to celé funguje?</h3>
      <p class="how-description">
        Nevieš, ako funguje zbieranie bodov, odmeny a úlohy? Zisti všetko o
        systéme Heličas a nauč sa, ako za učenie získaš čo najviac!
      </p>
      <button
        class="button"
        @click="$router.push('/ucebna/ako-funguje-helicas')"
      >
        Zistiť viac
      </button>
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
  name: "LeaderboardMobile",
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
    // najprv načítaj celkové achievementy, potom dáta (aby sa správne počítali úlohy)
    this.nacitajTotalAchievements().then(() => {
      this.nacitajData();
    });
  },
  methods: {
    stripWWW(url) {
      if (!url) return null;
      try {
        const u = new URL(url);
        if (u.hostname.startsWith("www.")) {
          u.hostname = u.hostname.slice(4);
        }
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
      const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32">
        <rect width="100%" height="100%" fill="#ddd"/>
        <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
          font-family="Bahnschrift, Arial, sans-serif" font-size="14" fill="#555">${initials}</text>
      </svg>`;
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
              +player.helitime
            );
            const fullName = `${(player.name || "").trim()} ${(
              player.surname || ""
            ).trim()}`.trim();
            let avatarProcessed = null;
            if (player.profilePhotoUrl) {
              avatarProcessed = this.stripWWW(player.profilePhotoUrl);
            } else {
              const initials = this.initialsFromName(fullName);
              avatarProcessed = this.makeInitialsAvatar(initials);
            }

            return {
              meno: fullName,
              avatar: avatarProcessed,
              dni,
              hodiny,
              minuty,
              stars: this.vypocitajHviezdy(dni, hodiny),
              ulohy: Math.max(
                this.totalAchievements - (player.finishedAchievements || 0),
                0
              ),
            };
          });
          this.top3 = parsed.slice(0, 3);
          this.ostatni = parsed.slice(3);
        }
      } catch (error) {
        console.error("Chyba pri načítaní dát:", error);
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
      } catch (error) {
        console.error("Chyba pri načítaní total achievements:", error);
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
  font-size: 2em;
  margin-bottom: 0.2em;
  text-align: center;
}

.table-row:hover,
tbody tr:hover {
  transform: scale(1);
}

.title {
  font-family: "Bahnshrift", sans-serif;
  font-size: 2em;
  margin-bottom: 0.2em;
  text-align: center;
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
  text-align: center;
  padding: 2em 1em;
}

.how-title {
  font-family: "Bahnshrift", sans-serif;
  font-size: 1.6em;
  margin-bottom: 0.4em;
}

.how-description {
  font-size: 1em;
  margin-bottom: 1.5em;
}

.button {
  font-size: 1em;
  font-family: "Adumu", sans-serif;
  margin-left: auto;
  margin-right: auto;
}
</style>
