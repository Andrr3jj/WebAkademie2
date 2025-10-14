<template>
  <div class="produkt">
    <div class="produkt-box">
      <div
        @click="
          produktyData.category === 'poukazka'
            ? $router.push({
                path: '/helishop/produkt',
                query: { produkt: 'poukazka' },
              })
            : $router.push({
                path: '/helishop/produkt',
                query: { cislo: produktyID, produkt: produktyData.category },
              })
        "
        class="image"
      >
        <img class="main-image" :src="hlavnyObrazok" alt="Obrázok produktu" />
        <img
          src="@/assets/images/icons/info.png"
          alt="zobraziť informácie"
          class="hover"
        />
      </div>
      <div class="text">
        <p class="nadpis">{{ produktyData.name }}</p>
        <div class="cena">
          <p class="Adumu">
            {{ getCena() }}
          </p>
          <div @click="odkzaMaprec()" class="add">
            <img src="@/assets/images/icons/pridanie.png" alt="Do košíka" />
          </div>
        </div>
      </div>
      <div v-if="produktyData.discount_active == 'true'" class="zlava">
        Zľava {{ produktyData.discount }}%
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    produktyID: {
      type: Array,
      default: () => [],
    },
    produktyData: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    hlavnyObrazok() {
      const obrazok = this.produktyData.images?.[0];
      return obrazok
        ? obrazok
        : "https://heligonkovaakademia.sk/data/images/logo.png";
    },
  },
  methods: {
    getCena() {
      if (this.produktyData.discount_active === "true") {
        let cena = parseFloat(
          this.produktyData.price.toString().replace(",", ".")
        );
        let zlava = parseFloat(this.produktyData.discount.replace(",", "."));
        return (
          "Cena: " +
          (cena - (cena * zlava) / 100)
            .toFixed(2)
            .toString()
            .replace(".", ",") +
          " €"
        );
      }
      return "Cena: " + this.produktyData?.price.replace(".", ",") + " €";
    },
    async odkzaMaprec() {
      if (this.produktyData.id.startsWith("gift_card")) {
        try {
          const axios = require("axios");
          const response = await axios.get(
            `https://heligonkovaakademia.sk/api/cart/add.php?id=${this.produktyData.id}`
          );

          if (response.status === 200) {
            const uzExistuje = this.$store.state.userCart.some(
              (item) => item.id === this.produktyData.id
            );

            if (!uzExistuje) {
              const cena = parseInt(
                this.produktyData.id.replace("gift_card", "")
              ); // napr. 50000
              this.$store.state.userCart.push({
                id: this.produktyData.id,
                id_delete: `gift_${cena}_${Date.now()}`,
                count: 1,
                description: `Darčeková poukážka v hodnote ${(cena / 100)
                  .toFixed(2)
                  .replace(".", ",")} €`,
                price: cena,
              });
            }

            this.$store.state.SnackBarText = "Poukážka bola pridaná do košíka.";
          } else {
            this.$store.state.SnackBarText = "Nepodarilo sa pridať poukážku.";
          }
        } catch (e) {
          console.error(e);
          this.$store.state.SnackBarText =
            "Chyba pri pridávaní poukážky do košíka.";
        }
        return;
      }

      // Klasický produkt:
      const data = JSON.parse(this.produktyData.details);
      if (data.farby.length !== 0 || data.velkosti.length !== 0) {
        this.$store.state.SnackBarText =
          "Pre pridanie variabilného produktu si prosím vyberte variácie";

        this.$router.push({
          path: "/helishop/produkt/",
          query: {
            cislo: this.produktyID,
            produkt: this.produktyData.category,
          },
        });
      } else {
        this.$store.dispatch("pridajDoKosika", {
          id: this.produktyData.id,
          meta_data: "",
          typ: this.produktyData.details?.typ || "",
        });
      }
    },
  },
};
</script>

<style lang="scss" scoped>
.produkt {
  width: 25%;
  font-size: 85%;
  display: flex;
  align-items: stretch;
  justify-content: center;
}

.produkt-box {
  transition-duration: 0.2s;
  border-radius: 2em;
  border: 0.5em solid #fef35a;
  background: var(
    --Linear,
    linear-gradient(140deg, #90c94f 35.64%, #fef35a 99.4%)
  );
  box-shadow: 3px 3px 5px 0 rgba(0, 0, 0, 0.25);
  width: 17em;
  margin: 1em;

  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.produkt-box {
  overflow: hidden;
  position: relative;
}

.produkt:hover .hover {
  display: block;
}
.produkt:hover .main-image {
  filter: blur(3px);
  transition-duration: 0.5s;
}

.image {
  position: relative;
  cursor: pointer;
}
.main-image {
  width: 100%;
  height: 17.5em;
  object-fit: cover;
  border-radius: 1.5em;
  box-shadow: inset 0 4px 4px rgba(0, 0, 0, 0.25);
}

.hover {
  position: absolute;
  display: none;
  transform: translate(-50%, -50%);
  left: 50%;
  top: 50%;
  width: 3.5em;

  &:hover {
    transform: translate(-50%, -50%) scale(1.1);
    transition-duration: 0.3s;
  }
}

.add img {
  width: 2.5em;
  cursor: pointer;

  &:hover {
    transform: scale(1.1);
    transition-duration: 0.3s;
  }
}

.add {
  display: flex;
  justify-content: center;
  align-items: center;
}

.cena {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  padding: 0 1em 0.7em;
}

.nadpis {
  font-size: 1.5em;
  padding: 0.3em 0.7em 0em;
  text-align: left;
  font-weight: 600;
}

.cena p {
  font-size: 1.8em;
  text-align: left;
}

.zlava {
  position: absolute;
  background-color: #fef35a;
  padding: 0.3em 3em;
  font-size: 1.8em;
  font-weight: 900;
  top: 1.2em;
  transform: rotate(-45deg);
  left: -2.8em;
}

@media only screen and (max-width: 1700px) {
  .produkt {
    width: 25%;
    font-size: 100%;
  }
}

@media only screen and (max-width: 1500px) {
  .produkt {
    width: 25%;
    font-size: 90%;
  }
}

@media only screen and (max-width: 1050px) {
  .produkt {
    font-size: 80%;
  }
}

@media only screen and (max-width: 950px) {
  .produkt {
    font-size: 70%;
  }
}
@media only screen and (max-width: 900px) {
  .produkt {
    font-size: 60%;
  }
}
@media only screen and (max-width: 750px) {
  .produkt {
    font-size: 80%;
  }
}
@media only screen and (max-width: 690px) {
  .produkt {
    font-size: 70%;
  }
}
@media only screen and (max-width: 570px) {
  .produkt {
    width: 50%;
    font-size: 2.2vw;
  }
}
</style>
