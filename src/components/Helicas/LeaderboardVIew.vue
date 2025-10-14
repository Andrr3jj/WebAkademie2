<template>
  <div class="leaderboard-wrapper">
    <h2 class="title">Rebríček najvytrvalejších hráčov</h2>
    <p class="podnadpis">Kto trávi najviac času učením?</p>

    <div class="top-players">
      <LeaderboardPlayerCard
        :poradie="2"
        v-if="top3[1]"
        :avatar="top3[1].avatar"
        :meno="top3[1].meno"
        :cas="formatCas(top3[1])"
        :stars="top3[1].stars"
      />
      <LeaderboardPlayerCard
        :poradie="1"
        v-if="top3[0]"
        :avatar="top3[0].avatar"
        :meno="top3[0].meno"
        :cas="formatCas(top3[0])"
        :stars="top3[0].stars"
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
      <table class="player-table" v-if="zobrazovani.length">
        <thead>
          <tr>
            <th>Názov účtu:</th>
            <th>Čas výučby:</th>
            <th>Hviezdy:</th>
            <th>Úlohy:</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(player, index) in zobrazovani" :key="index">
            <td>
              <div class="player-info">
                <span class="rank">{{ index + 4 }}.</span>
                <img :src="player.avatar" alt="Avatar" class="avatar" />
                <span class="name">{{ player.meno }}</span>
              </div>
            </td>
            <td>
              <TimeCubes
                :dni="player.dni"
                :hodiny="player.hodiny"
                :minuty="player.minuty"
              />
            </td>
            <td class="stars-cell">
              <StarRating
                :stars="player.stars"
                :dni="player.dni"
                :hodiny="player.hodiny"
              />
            </td>
            <td class="tasks-cell">
              <TaskCount :count="player.ulohy" />
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
  name: "LeaderboardView",
  components: {
    LeaderboardPlayerCard,
    TimeCubes,
    StarRating,
    TaskCount,
  },
  data() {
    return {
      top3: [],
      zobrazovani: [],
      totalAchievements: 0,
    };
  },
  mounted() {
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
            const avatarProcessed = player.profilePhotoUrl
              ? this.stripWWW(player.profilePhotoUrl)
              : null;

            return {
              meno: fullName,
              avatar: avatarProcessed,
              dni,
              hodiny,
              minuty,
              stars: this.vypocitajHviezdy(dni, hodiny),
              ulohy: player.finishedAchievements || 0,
            };
          });

          this.top3 = parsed.slice(0, 3);
          this.zobrazovani = parsed.slice(3); // Zobraz všetkých naraz
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

<style scoped>
.title {
  font-family: "Bahnshrift", sans-serif;
  font-size: 3.5em;
  margin-bottom: 0.2em;
  text-align: center;
}

.table-row:hover,
tbody tr:hover {
  transform: scale(1);
}

.podnadpis {
  font-family: "Bahnshrift", sans-serif;
  font-size: 2em;
  text-align: center;
  margin-bottom: 2em;
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
  margin-bottom: 4em;
  margin-top: 6em;
}

.player-table {
  width: 90%;
  border-collapse: collapse;
  font-size: 1em;
  min-width: 600px;
  margin-right: auto;
  margin-left: auto;
}

.player-table th {
  font-size: 20px;
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

.player-table td,
.player-table th {
  padding: 0.5em 0.8em;
  text-align: left;
}

.player-info {
  display: flex;
  align-items: center;
  gap: 0.5em;
  font-family: "Bahnshrift", sans-serif;
  font-size: 1em;
  justify-content: flex-start;
}

.rank {
  font-size: 20px;
  font-weight: bold;
}

.avatar {
  width: 36px;
  height: 36px;
  object-fit: cover;
  border-radius: 8px;
  object-fit: cover;
  border: 2px solid #fef35a;
  box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.25);
}

.name {
  font-size: 20px;
  font-weight: 600;
}

.stars-cell,
.tasks-cell {
  padding: 0 0.5em;
  text-align: center;
  width: 1%;
}

.stars-cell > *,
.tasks-cell > * {
  margin: 0 auto;
}

.stars-and-tasks {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 2em;
}

.how-it-works {
  text-align: center;
  margin: 5em auto 4em;
  max-width: 900px;
  padding: 0 1em;
}

.how-title {
  font-size: 2.5em;
  font-weight: bold;
  font-family: "Bahnschrift", sans-serif;
  margin-bottom: 0.4em;
}

.how-description {
  font-size: 1.5em;
  font-family: "Bahnschrift", sans-serif;
  margin-bottom: 1.5em;
}

.button {
  font-family: "Adumu", sans-serif;
  margin-left: auto;
  margin-right: auto;
}

@media screen and (max-width: 1024px) {
  .player-table {
    font-size: 0.9em;
    width: 100%;
    min-width: unset;
  }

  .name {
    text-align: left;
  }

  .player-table td {
    vertical-align: middle;
    height: 100%;
  }

  .player-table td:first-child {
    width: auto;
    display: table-cell;
  }

  .player-table td:nth-child(2) {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 40px;
    padding: 0.5em 0.8em;
  }

  .top-players {
    font-size: 70%;
  }

  .title {
    font-size: 2em;
  }

  .podnadpis {
    font-size: 1.2em;
    margin-bottom: 4em;
  }

  .player-table {
    font-size: 0.8em;
  }
}

@media screen and (max-width: 1240px) {
  .top-players {
    font-size: 80%;
  }
}
</style>
