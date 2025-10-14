<template>
  <section class="computer" :id="$route.name" ref="contentArea">
    <div class="scroll">
      <HeaderSection />
      <CategoryFilters
        :selected="selectedCategory"
        @select-category="selectedCategory = $event"
      />
      <SongTable
        :songs="paginatedSongs"
        :startIndex="(currentPage - 1) * itemsPerPage"
        @toggle-favorite="toggleFavorite"
        :noResultsMessage="noResultsMessage"
      />
      <SpevnikPagination
        :total="filteredMeta.length"
        :current="currentPage"
        :perPage="itemsPerPage"
        @change-page="currentPage = $event"
      />
      <SearchBar v-model="searchQuery" />
      <div class="line horizontal"></div>
      <FavoritesToggle
        :showingOnlyFavorites="onlyFavorites"
        @toggle-favorites="toggleFavorites"
        @add-song="addSong"
      />
    </div>
  </section>

  <div class="mobile" :id="$route.name">
    <section>
      <div class="scroll">
        <HeaderSection />
        <CategoryFilters
          :selected="selectedCategory"
          @select-category="selectedCategory = $event"
        />
        <SongTable
          :songs="paginatedSongs"
          :startIndex="(currentPage - 1) * itemsPerPage"
          @toggle-favorite="toggleFavorite"
          :noResultsMessage="noResultsMessage"
        />
        <SpevnikPagination
          :total="filteredMeta.length"
          :current="currentPage"
          :perPage="itemsPerPage"
          @change-page="currentPage = $event"
        />
        <SearchBar v-model="searchQuery" />
        <FavoritesToggle
          :showingOnlyFavorites="onlyFavorites"
          @toggle-favorites="toggleFavorites"
          @add-song="addSong"
        />
      </div>
    </section>
  </div>
  <!-- workaround na ESLint "unused" -->
  <ModalSkeleton
    v-if="showAddModal"
    :content-component="modalComponent"
    :content-props="{ anchorPosition: modalAnchor }"
    @close="showAddModal = false"
  />
</template>

<script>
import HeaderSection from "@/components/Spevnik/HeaderSection.vue";
import CategoryFilters from "@/components/Spevnik/CategoryFilters.vue";
import SearchBar from "@/components/Spevnik/SearchBar.vue";
import SongTable from "@/components/Spevnik/SongTable.vue";
import SpevnikPagination from "@/components/Spevnik/SpevnikPagination.vue";
import FavoritesToggle from "@/components/Spevnik/FavoritesToggle.vue";
import AddSongModalLoggedIn from "@/components/Spevnik/modal/AddSongModalLoggedIn.vue";
import AddSongModalNotLoggedIn from "@/components/Spevnik/modal/AddSongModalNotLoggedIn.vue";
import ModalSkeleton from "@/components/Spevnik/modal/ModalSkeleton.vue";

export default {
  name: "SpevnikView",
  components: {
    HeaderSection,
    CategoryFilters,
    SearchBar,
    SongTable,
    SpevnikPagination,
    FavoritesToggle,
    ModalSkeleton,
  },
  data() {
    return {
      allMeta: [],
      songs: [],
      favoriteSongIds: [],
      onlyFavorites: false,
      searchQuery: "",
      selectedCategory: null,
      currentPage: 1,
      itemsPerPage: 10,
      showAddModal: false,
      modalAnchor: null,
      isLoggedIn: false,
      isLoadingSongs: false,
    };
  },
  computed: {
    noResultsMessage() {
      if (this.searchQuery.trim()) {
        return `Nebola nájdená žiadna pieseň pre „${this.searchQuery.trim()}”`;
      }
      return "Nie sú tu žiadne piesne.";
    },
    filteredMeta() {
      let m = this.allMeta;

      if (this.onlyFavorites) {
        m = m.filter((x) => this.favoriteSongIds.includes(String(x.id)));
      }

      if (this.searchQuery.trim()) {
        const q = this.searchQuery.toLowerCase();
        m = m.filter((x) => x.name.toLowerCase().includes(q));
      }

      return m.sort((a, b) =>
        a.name.localeCompare(b.name, "sk", { sensitivity: "base" })
      );
    },
    paginatedMeta() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      return this.filteredMeta.slice(start, start + this.itemsPerPage);
    },
    paginatedSongs() {
      return this.songs;
    },
    modalComponent() {
      return this.isLoggedIn ? AddSongModalLoggedIn : AddSongModalNotLoggedIn;
    },
  },
  async mounted() {
    try {
      const res = await fetch(
        "https://heligonkovaakademia.sk/api/user/auth/isLoggedIn.php"
      );
      const json = await res.json();
      const msg = json?.data?.toLowerCase() || "";
      this.isLoggedIn = msg.includes("logged in") && !msg.includes("not");
    } catch {
      this.isLoggedIn = false;
    }

    await this.loadSongs(1, true);

    this.$nextTick(() => {
      const el = this.$refs.contentArea;
      if (el) {
        const rect = el.getBoundingClientRect();
        this.modalAnchor = {
          top: rect.top + rect.height / 2 + window.scrollY,
          left: rect.left + rect.width / 2 + window.scrollX,
        };
      }
    });
  },
  methods: {
    async loadSongs(page = 1, resetMeta = false) {
      if (this.isLoadingSongs) return;
      this.isLoadingSongs = true;

      // Zamrazíme aktuálne filtre, aby sa počas fetchovania nemenili
      const currentCategory = this.selectedCategory;
      const currentSearch = this.searchQuery.trim();

      try {
        this.currentPage = page;

        if (resetMeta || !this.allMeta.length) {
          this.allMeta = [];
          const limit = 1000;
          let offset = 0;
          let batch = [];
          let safety = 0;

          do {
            const qs = new URLSearchParams({
              pagination_index: offset,
              pagination_limit: limit,
              schvalene: 1,
            });
            if (currentCategory) qs.append("kategoria", currentCategory);
            if (currentSearch) qs.append("search", currentSearch);

            console.debug("Načítavam meta batch", {
              offset,
              limit,
              category: currentCategory,
              search: currentSearch,
            });

            const res = await fetch(
              `${this.$store.state.api}/noty/texty/search.php?${qs.toString()}`,
              { credentials: "include" }
            );
            const json = await res.json();
            batch = Array.isArray(json.data) ? json.data : [];

            this.allMeta.push(
              ...batch.map((x) => ({
                id: x.id,
                name: x.nazov,
                category: x.kategoria || "",
              }))
            );

            offset += limit;
            safety += 1;
            if (safety > 10) {
              console.warn("Príliš veľa batchov, prerušujem (safety cap).");
              break;
            }

            // Ak sa medzitým zmenil filter alebo search, prerušíme, lebo už nie je konzistentné
            if (
              this.selectedCategory !== currentCategory ||
              this.searchQuery.trim() !== currentSearch
            ) {
              console.warn(
                "Filter alebo vyhľadávanie sa zmenilo počas načítavania, prerušujem."
              );
              break;
            }
          } while (batch.length === limit);

          // deduplikuj a zorad podľa id
          const unique = Array.from(
            new Map(this.allMeta.map((x) => [x.id, x])).values()
          );
          unique.sort((a, b) => b.id - a.id);
          this.allMeta = unique;
          this.currentPage = 1;
        }

        // Načítaj detaily len pre aktuálnu stránku (paginatedMeta používa už aktualizované allMeta)
        const metaForDetails = this.paginatedMeta;
        const details = await Promise.all(
          metaForDetails.map(async (m) => {
            const res = await fetch(
              `${this.$store.state.api}/noty/texty/load.php?id=${m.id}`,
              {
                credentials: "include",
              }
            );
            const json = await res.json();
            const d = json.data || {};
            let oblubene = [];
            if (Array.isArray(d.oblubene)) {
              oblubene = d.oblubene;
            } else if (
              typeof d.oblubene === "string" &&
              d.oblubene.startsWith("[")
            ) {
              try {
                oblubene = JSON.parse(d.oblubene);
              } catch (parseErr) {
                console.warn("Nepodarilo sa parsovať oblubene:", parseErr);
              }
            }

            return {
              id: d.id,
              name: d.nazov,
              author: d.autor || "",
              zapis_id: d.product_id,
              views: Number(d.zobrazenia) || 0,
              category: (d.zaner || d.kategoria || "").toLowerCase(),
              isFavorite:
                this.isLoggedIn && Array.isArray(oblubene)
                  ? oblubene.includes(String(this.$store.state.user.id))
                  : false,
              favoriteCount: Array.isArray(oblubene) ? oblubene.length : 0,
            };
          })
        );

        this.songs = Array.from(
          new Map(details.map((d) => [d.id, d])).values()
        );
      } catch (err) {
        console.error("Chyba pri načítaní piesní:", err);
        this.songs = [];
      } finally {
        this.isLoadingSongs = false;
      }
    },

    async toggleFavorite(song) {
      if (!song?.id) return;
      const form = new FormData();
      form.append("id", song.id);
      const res = await fetch(
        "https://heligonkovaakademia.sk/api/noty/texty/favorite.php",
        { method: "POST", credentials: "include", body: form }
      );
      const json = await res.json();
      song.isFavorite = json.data === "favorite";
      if (song.isFavorite) song.favoriteCount++;
      else if (song.favoriteCount > 0) song.favoriteCount--;
    },
    async loadFavoriteSongIds() {
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/noty/texty/getMyFavorite.php",
          { method: "POST", credentials: "include" }
        );
        const json = await res.json();
        this.favoriteSongIds = json.status?.toLowerCase().includes("succes")
          ? json.data.map((r) => String(r.id))
          : [];
      } catch {
        this.favoriteSongIds = [];
      }
    },
    addSong() {
      this.showAddModal = true;
    },
    async toggleFavorites() {
      this.onlyFavorites = !this.onlyFavorites;
      if (this.onlyFavorites) {
        await this.loadFavoriteSongIds();
      }
      this.loadSongs(1, true);
    },
    async searchSongs(query) {
      this.searchQuery = query;
      this.loadSongs(1, true);
    },
    async loadFavoriteSongs() {
      try {
        const res = await fetch(
          "https://heligonkovaakademia.sk/api/noty/texty/getMyFavorite.php",
          { credentials: "include" }
        );
        const json = await res.json();
        if (json.status?.toLowerCase().includes("succes")) {
          this.allMeta = json.data.map((s) => ({
            id: s.id,
            name: s.nazov,
            category: s.kategoria || "",
          }));
          this.currentPage = 1;
          this.loadSongs(1, false);
        }
      } catch {
        this.allMeta = [];
        this.currentPage = 1;
        this.songs = [];
      }
    },
  },
  watch: {
    selectedCategory() {
      this.loadSongs(1, true);
    },
    searchQuery: {
      handler() {
        this.loadSongs(1, true);
      },
      immediate: false,
    },
    currentPage(p) {
      if (!this.isLoadingSongs) {
        this.loadSongs(p, false);
      }
    },
  },
};
</script>

<style scoped lang="scss">
.computer {
  position: relative;
}
</style>
