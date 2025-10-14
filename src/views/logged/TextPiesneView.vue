<template>
  <section v-if="!isMobile" class="computer" :id="$route.name">
    <div class="scroll">
      <component
        :key="songDetail?.id"
        :is="maZapis ? 'TextPiesneAktivny' : 'TextPiesneNeaktivny'"
        :song-detail="songDetail"
        :loading="loading"
        :error="error"
        :dalsie-produkty="dalsieProdukty"
      />
    </div>
  </section>

  <div v-else class="mobile" :id="$route.name">
    <section>
      <div class="scroll">
        <component
          :key="songDetail?.id"
          :is="maZapis ? 'TextPiesneAktivny' : 'TextPiesneNeaktivny'"
          :song-detail="songDetail"
          :loading="loading"
          :error="error"
          :dalsie-produkty="dalsieProdukty"
        />
      </div>
    </section>
  </div>
</template>

<script>
import TextPiesneAktivny from "@/components/Spevnik/TextPiesne/TextPiesneAktivny.vue";
import TextPiesneNeaktivny from "@/components/Spevnik/TextPiesne/TextPiesneNeaktivny.vue";

export default {
  name: "TextPiesneView",
  components: {
    TextPiesneAktivny,
    TextPiesneNeaktivny,
  },
  data() {
    return {
      maZapis: false,
      songId: null,
      songDetail: null,
      loading: true,
      error: false,
      dalsieProdukty: [],
      isMobile: false,
    };
  },
  async mounted() {
    // detekcia veľkosti okna pre mobil/desktop
    this.onResize = () => {
      this.isMobile = window.innerWidth < 751;
    };
    this.onResize();
    window.addEventListener("resize", this.onResize);

    // pôvodná inicializácia route, fetchovanie dát...
    this.songId =
      this.$route.params.id || this.$route.params.slug?.split("-").pop();
    this.maZapis = this.$route.name === "TextPiesneAktivna";

    if (!this.songId) {
      this.loading = false;
      this.error = true;
      return;
    }

    try {
      // load current song
      const r1 = await fetch(
        `https://heligonkovaakademia.sk/api/noty/texty/load.php?id=${this.songId}`,
        { credentials: "include" }
      );
      const j1 = await r1.json();
      this.songDetail = j1.data;

      // load list all
      const r2 = await fetch(
        "https://heligonkovaakademia.sk/api/noty/texty/listAll.php",
        { credentials: "include" }
      );
      const j2 = await r2.json();
      const schvalene = Array.isArray(j2.data?.schvalene)
        ? j2.data.schvalene
        : [];

      // enrich each
      const all = await Promise.all(
        schvalene.map(async (song) => {
          const tRes = await fetch(
            `https://heligonkovaakademia.sk/api/noty/texty/load.php?id=${song.id}`,
            { credentials: "include" }
          );
          const tJson = await tRes.json();
          const zaner = (tJson.data.zaner || "").toLowerCase();

          let prod = {};
          if (song.product_id && song.product_id !== "0") {
            const pRes = await fetch(
              `https://heligonkovaakademia.sk/api/product/loadLimited.php?id=${song.product_id}`
            );
            const pJson = await pRes.json();
            prod = pJson.data || {};
          }

          let md = {};
          if (prod.details) {
            try {
              md = JSON.parse(prod.details);
            } catch {
              console.error("Failed to parse product details:", prod.details);
            }
          }

          return {
            id: song.id,
            product_id: song.product_id,
            nazov: song.nazov,
            zaner,
            typ: tJson.data.typ,
            povod: tJson.data.povod,
            autor: tJson.data.autor,
            urcenePre: md.urcenePre || "",
            stupnice: Array.isArray(md.stupnice) ? md.stupnice : [],
            price: prod.price || prod.cena || "",
            difficulty: Number(prod.difficulty) || 0,
            details: prod.details || "",
            new: prod.new || "",
            timestamp: prod.timestamp || "",
          };
        })
      );

      const curGenre = (this.songDetail.zaner || "").toLowerCase();
      this.dalsieProdukty = all.filter(
        (p) =>
          p.zaner === curGenre && String(p.product_id) !== "0" && p.product_id
      );
    } catch (e) {
      console.error(e);
      this.error = true;
    } finally {
      this.loading = false;
    }
  },
  beforeUnmount() {
    window.removeEventListener("resize", this.onResize);
  },
};
</script>

<style scoped>
/* ponechaj svoje existujúce štýly */
</style>
