<template>
  <li class="cart-item">
    <img :src="getImage()" alt="Obrázok produktu" />
    <div class="line"></div>

    <div class="specifikacia">
      <p class="nazov">{{ getNazov() }}</p>
      <p class="piesen" v-if="getPopis()">{{ getPopis() }}</p>
      <p class="nazov-piesne" v-html="getSpecifikaciu()"></p>
      <p class="cena">{{ getCena() }}</p>
    </div>
    <img
      v-if="umiestnenie == 'kosik'"
      @click="odstranProdukt"
      src="@/assets/images/icons/kos.svg"
      alt=""
    />
  </li>
</template>

<script>
export default {
  props: {
    notes: {
      type: Object,
      default: () => ({}),
      required: true,
    },
    produktyData: {
      type: Object,
      default: () => ({}),
      required: true,
    },
    umiestnenie: {
      type: String,
    },
  },
  mounted() {
    console.log("notes :>> ", this.notes);
    console.log("produktyData :>> ", this.produktyData);
    console.log("umiestnenie :>> ", this.umiestnenie);
  },
  data() {
    return {
      details: JSON.parse(this.produktyData.details),
    };
  },
  methods: {
    getImage() {
      if (this.details.typ === "merch" || this.details.typ === "gift_card") {
        return this.produktyData.images[0];
      }
      return "https://heligonkovaakademia.sk/data/images/CiselneZapisyTitle.png";
    },

    getNazov() {
      if (
        this.details.typ === "merch" ||
        this.details.typ === "gift_card" ||
        this.produktyData.category === "poukazky"
      ) {
        return this.produktyData.name;
      }
      return "Číselné zápisy";
    },

    getPopis() {
      if (this.details.typ === "merch") {
        return this.produktyData.additional_text;
      }
      if (this.details.typ === "gift_card") {
        return ""; // ✅ nič nezobrazí pri poukážke
      }
      return "Pieseň:";
    },

    getSpecifikaciu() {
      if (this.details.typ === "gift_card") {
        const jednotkovaCena = parseFloat(
          this.produktyData.price.replace(",", ".")
        );
        const formattedJednotkova = jednotkovaCena.toFixed(2).replace(".", ",");

        return (
          "Hodnota poukážky: " +
          formattedJednotkova +
          " €<br />Počet ks: " +
          (this.notes.count || 1)
        );
      }

      if (this.details.typ === "merch") {
        return (
          "Počet ks: " +
          (this.notes.count || 0) +
          "   " +
          (this.produktyData?.description?.replace(",", "   ") || "")
        );
      }
      return this.produktyData?.name || "Nezistená";
    },

    getCena() {
      if (this.details.typ === "gift_card") {
        const jednotkovaCena = parseFloat(this.produktyData.price);
        const pocet = this.notes.count || 1;
        const celkovaCena = jednotkovaCena * pocet;

        return (
          "Cena: " +
          celkovaCena.toFixed(2).replace(".", ",") +
          " €" +
          " (" +
          jednotkovaCena.toFixed(2).replace(".", ",") +
          " €)"
        );
      }

      if (this.details.typ === "merch") {
        if (this.produktyData.discount_active === "true") {
          let cena = parseFloat(
            this.produktyData.price.toString().replace(",", ".")
          );
          let zlava = parseFloat(this.produktyData.discount.replace(",", "."));
          return (
            "Cena: " +
            this.vypocitanaCena() +
            " €" +
            " (" +
            (cena - (cena * zlava) / 100)
              .toFixed(2)
              .toString()
              .replace(".", ",") +
            "€)"
          );
        }
        let cena =
          parseFloat(this.produktyData?.price.replace(",", ".")) *
          this.notes.count;
        return (
          "Cena: " +
          cena.toFixed(2).replace(".", ",") +
          " €" +
          " (" +
          this.produktyData?.price.replace(".", ",") +
          "€)"
        );
      }
      if (this.details.typ === "gift_card") {
        const jednotkovaCena = parseFloat(
          this.produktyData.price.replace(",", ".")
        );
        const pocet = this.notes.count || 1;
        const celkovaCena = jednotkovaCena * pocet;

        return (
          "Cena: " +
          celkovaCena.toFixed(2).replace(".", ",") +
          " €" +
          " (" +
          jednotkovaCena.toFixed(2).replace(".", ",") +
          " €)"
        );
      }

      return (
        "Cena: " + (this.produktyData?.price.replace(".", ",") || "-,--") + " €"
      );
    },
    vypocitanaCena() {
      let cena = parseFloat(
        this.produktyData.price.toString().replace(",", ".")
      );
      let zlava = parseFloat(this.produktyData.discount.replace(",", "."));
      let pocetKS = this.notes.count; // Počet kusov

      if (this.produktyData.discount_active === "true") {
        let novaCena = (cena - (cena * zlava) / 100) * pocetKS; // Vypočíta cenu s počtom kusov
        return novaCena.toFixed(2).toString().replace(".", ","); // Prevod na európsky formát
      }

      // Ak nie je zľava, iba upraví formát a vynásobí počtom kusov
      return (cena * pocetKS).toFixed(2).toString().replace(".", ",");
    },
    async odstranProdukt() {
      const success = await this.$store.dispatch(
        "odstranZKosiku",
        this.produktyData?.id_delete
      );

      if (success) {
        setTimeout(() => {
          this.$emit("zmenitKuponNacitany");
          // this.$emit("nacitajProdukty");
          console.log("bolo  úspešne odstránenie z košíku emitujem ");
        }, 1000);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.cart-item {
  display: flex;
  gap: 2%;
  justify-content: space-between;

  .line {
    width: 0.3em;
  }

  img:first-child {
    width: 10vw;
    max-width: 8em;
    height: 100%;
    margin: auto;
  }
  img:last-child {
    align-self: flex-start;
    width: 1em;
    transition-duration: 0.1s;
    cursor: pointer;

    &:hover {
      transform: scale(1.25);
      transition-duration: 0.2s;
    }
  }

  p {
    text-align: left;
    margin: 0.1em 0;
  }
}

.nazov {
  font-size: 1.675em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  letter-spacing: 0.025em;
}
.piesen,
.cena {
  font-size: 1.3375em;
  font-style: normal;
  font-weight: 600;
  line-height: 120%; /* 1.725rem */
}
.nazov-piesne {
  font-size: 1.15em;
  font-style: normal;
  font-weight: 600;
  line-height: 120%; /* 1.5rem */
}

.specifikacia {
  margin-right: auto;
  margin-left: 4%;
  width: 70%;
}

@media only screen and (max-width: 1000px) {
  .cart-item img:first-child {
    width: 12em;
  }

  .cart-item img:last-child {
    width: 1.5em;
  }
}

@media only screen and (max-width: 750px) {
  .cart-item {
    margin: 1em 0 0;
  }

  .cart-item img:first-child {
    width: 10em;
    margin-top: 0.5em;
  }

  .cart-item > img:last-of-type {
    margin-top: 1.3em;
  }
}

@media only screen and (max-width: 450px) {
  .cart-item img:first-child {
    width: 8em;
  }
}
</style>
