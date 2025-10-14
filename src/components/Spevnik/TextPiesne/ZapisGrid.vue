<template>
  <div class="zapis-grid-wrapper">
    <div
      v-if="nahodneProdukty.length"
      :class="['zapis-grid', nahodneProdukty.length < 4 ? 'centered-grid' : '']"
    >
      <div
        v-for="produkt in nahodneProdukty"
        :key="produkt.id"
        class="zapis-item"
      >
        <h3 class="zapis-nazov">{{ produkt.name }}</h3>
        <JedenZapis
          :produktyID="produkt.id"
          :produktyData="produkt"
          :layouts="true"
          :flexCenter="false"
        />
      </div>
    </div>

    <div v-else class="no-results">
      <p>Ľutujeme, ale podobné zápisy sme nenašli.</p>
    </div>
  </div>
</template>

<script>
import JedenZapis from "@/components/Zapisy/JedenZapis.vue";

export default {
  name: "ZapisGrid",
  components: { JedenZapis },
  props: {
    produkty: { type: Array, required: true },
    activeId: { type: [String, Number], default: null },
    activeProductId: { type: [String, Number], default: null },
  },
  data() {
    return {
      nahodneProdukty: [],
    };
  },
  watch: {
    produkty: {
      handler() {
        this.vygenerujNahodne();
      },
      immediate: true,
    },
  },
  methods: {
    async vygenerujNahodne() {
      const vyfiltruj = this.produkty.filter(
        (p) =>
          p.product_id &&
          p.product_id !== "0" &&
          String(p.product_id) !== String(this.activeId) &&
          String(p.product_id) !== String(this.activeProductId)
      );

      const nahodne = vyfiltruj.sort(() => 0.5 - Math.random()).slice(0, 4);

      // načítaj detail každého produktu
      const produktyDetail = await Promise.all(
        nahodne.map(async (p) => {
          try {
            const res = await fetch(
              `https://heligonkovaakademia.sk/api/product/loadLimited.php?id=${p.product_id}`
            );
            const json = await res.json();
            const data = json.data || {};

            return {
              id: p.product_id,
              name: data.name || "Zápis",
              price: data.price || "0.00",
              difficulty: Number(data.difficulty) || 0,
              details: data.details || "",
              new: data.new || "",
              timestamp: data.timestamp || "",
              urcenePre: data.urcenePre || "",
              stupnice: Array.isArray(data.stupnice) ? data.stupnice : [],
            };
          } catch (e) {
            console.warn("Chyba pri načítaní produktu:", p.product_id, e);
            return {
              id: p.product_id,
              name: "Zápis",
              price: "0.00",
              difficulty: 0,
              details: "",
              new: "",
              timestamp: "",
              urcenePre: "",
              stupnice: [],
            };
          }
        })
      );

      this.nahodneProdukty = produktyDetail;
    },
  },
};
</script>

<style scoped>
.zapis-grid-wrapper {
  text-align: center;
}

.zapis-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
  justify-items: center;
  padding: 1rem;
}

.zapis-grid.centered-grid {
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  justify-content: center;
}

.zapis-item {
  width: 100%;
  max-width: 270px;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  min-height: 420px;
  justify-content: space-between;
}

.zapis-nazov {
  font-size: 1.3em;
  font-weight: 700;
  margin-bottom: 0.4em;
  font-family: "Bahnschrift", sans-serif;
  max-width: 12.5em;
  white-space: normal;
  word-break: break-word;
  overflow-wrap: break-word;

  /* Fixná výška na 2 riadky, ak treba */
  min-height: 3.6em;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.no-results p {
  font-size: 1.2em;
  color: #666;
  margin: 2em 0;
}

/* tablet: 2 stĺpce */
@media (max-width: 1024px) {
  .zapis-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* mobil: 1 stĺpec */
@media (max-width: 750px) {
  .zapis-grid {
    grid-template-columns: 1fr;
  }
  .zapis-item {
    min-height: 0;
  }
}
</style>
