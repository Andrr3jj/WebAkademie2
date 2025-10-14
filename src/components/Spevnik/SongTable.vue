<template>
  <div class="song-table">
    <table>
      <thead>
        <tr>
          <th style="width: 60%">Názov piesní</th>
          <th style="width: 20%" class="center">Obľúbené</th>
          <th style="width: 20%" class="center">Zobrazenie</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(song, index) in songs"
          :key="song.id"
          @click="goToSong(song)"
          :class="{ 'inactive-song': !song.zapis_id || song.zapis_id === '0' }"
          style="cursor: pointer"
        >
          <td>
            <span class="index">{{ startIndex + index + 1 }}.</span>
            <span v-if="song.zapis_id && song.zapis_id !== '0'">
              <img
                src="@/assets/images/gallery/heligonka.png"
                alt="heligonka"
                class="heligonka-icon"
              />
            </span>
            {{ song.name }}
          </td>
          <td class="center">
            {{ song.favoriteCount || 0 }}
            <span class="favorite-icon">
              <img
                src="@/assets/images/icons/oblubena.svg"
                alt="Obľúbené"
                class="favorite-icon"
                :class="{ active: song.isFavorite }"
              />
            </span>
          </td>
          <td class="center">
            {{ song.views || 0 }}
            <span class="view-icon"
              ><img
                src="@/assets/images/icons/zobrazene.svg"
                alt="Zobrazené"
                class="view-icon"
            /></span>
          </td>
        </tr>
        <tr v-if="songs.length === 0">
          <td
            colspan="3"
            style="text-align: center; vertical-align: middle; height: 200px"
          >
            {{ noResultsMessage }}
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "SongTable",
  props: {
    songs: Array,
    startIndex: Number,
    noResultsMessage: {
      type: String,
      default: "",
    },
  },
  methods: {
    slugify(str, id) {
      return (
        String(str)
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .toLowerCase()
          .replace(/[^a-z0-9]+/g, "-")
          .replace(/^-+|-+$/g, "") +
        "-" +
        id
      );
    },
    goToSong(song) {
      const slug = this.slugify(song.name, song.id);
      if (song.zapis_id && song.zapis_id !== "0") {
        this.$router.push(`/text-piesne/aktivna/${slug}`);
      } else {
        this.$router.push(`/text-piesne/neaktivna/${slug}`);
      }
    },
  },
};
</script>
<style scoped lang="scss">
.song-table {
  overflow-x: auto;
  margin: 3em auto;
  width: 70%;
  max-width: 1000px;

  table {
    position: relative;
    width: 100%;
    border-collapse: collapse;
    font-weight: 500;
    font-size: 1.11em;
    margin-bottom: 0;
    /* nastavíš tu výšku hlavičky (vrátane paddingu) */
    --header-height: 2em;
  }

  /* podklad + tieň hlavičky */
  table::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: var(--header-height);
    background: #fef35a;
    border-radius: 12px 12px 12px 12px;
    box-shadow: inset 0 6px 8px rgba(0, 0, 0, 0.15);
    z-index: 1;
  }

  thead th {
    position: relative;
    z-index: 2;
    background: transparent;
    padding: 0.5em 1em;
    font-weight: 600;
  }

  thead th:first-child {
    text-align: left;
  }

  thead th:nth-child(2),
  thead th:nth-child(3) {
    text-align: center;
  }

  tbody tr td {
    position: relative;
    z-index: 1;
    padding: 0.4em 1em;
    border-bottom: 1px solid rgba(0, 0, 0, 0.2);
  }

  tbody tr td:first-child {
    text-align: left;
  }

  tbody tr td:nth-child(2),
  tbody tr td:nth-child(3) {
    text-align: center;
  }

  .heligonka-icon {
    width: 1.5em;
    vertical-align: middle;
    margin-right: 0.5em;
  }

  .favorite-icon img,
  .view-icon img {
    width: 1.2em;
    vertical-align: middle;
  }

  .index {
    font-weight: bold;
    margin-right: 0.5em;
  }

  @media (max-width: 1500px) {
    width: 98%;
    margin: 2em 1%;
  }

  @media (max-width: 600px) {
    table {
      font-size: 1em;
      --header-height: 2em;
    }

    thead th,
    tbody tr td {
      padding: 0.3em 0.6em;
    }

    .heligonka-icon {
      width: 1em;
    }
  }
}
</style>
