<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Darčekové poukážky / Kódy do knihy</h5>

      <div class="line horizontal w-80"></div>

      <p class="nadpis">Generovanie darčekových spoukážok</p>
      <div class="generovanie">
        <div class="row-form">
          <label for="knizky">Jednotlivé knižky:</label>
          <select ref="knizky" v-model="knizka" name="knizky">
            <option value="1">1. diel</option>
            <option value="2">2. diel</option>
            <option value="3">3. diel</option>
            <option value="4">4. diel</option>
          </select>
        </div>

        <div class="row-form">
          <label for="spec-kod">Špecialný kód:</label>
          <input
            ref="spec-kod"
            type="text"
            name="spec-kod"
            placeholder="HELI-2024-XXXX"
            v-model="specKod"
          />
        </div>
        <div class="row-form">
          <label for="kusov">Počet ks:</label>
          <input
            ref="kusov"
            type="number"
            name="kusov"
            placeholder="0"
            v-model="pocetKS"
            class="hodnota"
          />
        </div>
        <div class="row-form">
          <label for="">Generovať kódy:</label>
          <div class="button">
            <p>Vytvoriť</p>
          </div>
        </div>
      </div>

      <div class="all-statistic">
        <div class="darcekova-statistic">
          <p class="nadpis">Darčekové poukážky</p>
          <div class="box-statistic">
            <div class="stat-item">
              <p class="podnadpis">Zakúpené:</p>
              <p class="hodnota">50</p>
            </div>
            <div class="stat-item">
              <p class="podnadpis">Využité:</p>
              <p class="hodnota">50</p>
            </div>
            <div class="stat-item">
              <p class="podnadpis">Nevyužité:</p>
              <p class="hodnota">50</p>
            </div>
          </div>
          <div class="button">
            <p>Zobraziť</p>
          </div>
        </div>
        <div class="darcekova-statistic">
          <p class="nadpis">Kódy do knižky</p>
          <div class="box-statistic">
            <div class="stat-item">
              <p class="podnadpis">Vygenerované:</p>
              <p class="hodnota">50</p>
            </div>
            <div class="stat-item">
              <p class="podnadpis">Využité:</p>
              <p class="hodnota">50</p>
            </div>
            <div class="stat-item">
              <p class="podnadpis">Nevyužité:</p>
              <p class="hodnota">50</p>
            </div>
          </div>
          <div class="button">
            <p>Zobraziť</p>
          </div>
        </div>
      </div>

      <div class="darceky-table">
        <div class="table-top">
          <p class="Bahnschrift">Email:</p>
          <p class="Bahnschrift">Kód poukážky:</p>
          <p class="Bahnschrift">Zakúpené:</p>
          <p class="Bahnschrift">Využil email:</p>
          <p class="Bahnschrift">Platnosť:</p>
          <p class="Bahnschrift">Hodnota</p>
        </div>
      </div>

      <div class="table">
        <div class="table-row">
          <p class="item">emailik@gmail.com</p>
          <p class="item">DHZJ-DSA5-789S</p>
          <p class="item">24.6.2025</p>
          <p class="item">emailik@gmail.com</p>
          <p class="item">456 dní</p>
          <p class="item">150€/33€</p>
        </div>
        <div class="table-row">
          <p class="item">emailik@gmail.com</p>
          <p class="item">DHZJ-DSA5-789S</p>
          <p class="item">24.6.2025</p>
          <p class="item">emailik@gmail.com</p>
          <p class="item">456 dní</p>
          <p class="item">150€/33€</p>
        </div>
        <div class="table-row">
          <p class="item">emailik@gmail.com</p>
          <p class="item">DHZJ-DSA5-789S</p>
          <p class="item">24.6.2025</p>
          <p class="item">emailik@gmail.com</p>
          <p class="item">456 dní</p>
          <p class="item">150€/33€</p>
        </div>
        <div class="table-row">
          <p class="item">emailik@gmail.com</p>
          <p class="item">DHZJ-DSA5-789S</p>
          <p class="item">24.6.2025</p>
          <p class="item">emailik@gmail.com</p>
          <p class="item">456 dní</p>
          <p class="item">150€/33€</p>
        </div>
        <div class="table-row">
          <p class="item">emailik@gmail.com</p>
          <p class="item">DHZJ-DSA5-789S</p>
          <p class="item">24.6.2025</p>
          <p class="item">emailik@gmail.com</p>
          <p class="item">456 dní</p>
          <p class="item">150€/33€</p>
        </div>
        <div class="table-row">
          <p class="item">emailik@gmail.com</p>
          <p class="item">DHZJ-DSA5-789S</p>
          <p class="item">24.6.2025</p>
          <p class="item">emailik@gmail.com</p>
          <p class="item">456 dní</p>
          <p class="item">150€/33€</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);
  },
  data() {
    return {};
  },
  methods: {},
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

h1 {
  color: #fef35a;
  text-align: center;
  text-shadow: 5px 5px 5px rgba(0, 0, 0, 0.25);
  font-size: 5.75vw;
  font-style: normal;
  letter-spacing: -0.03em;
  font-weight: 400;
  -webkit-text-stroke-width: 0.04em;
  -webkit-text-stroke-color: black;
  line-height: 105%;
  padding: 0.2em 0 0.2em 0.1em;
  margin: 0;
}

h5 {
  text-align: center;

  font-size: 2.8em;
  font-style: normal;
  font-weight: 700;
  line-height: 115%;
  /* 2.51563rem */

  margin: 0;
  margin-bottom: 0.3em;
}

.table-top {
  border-radius: 2.1875em;
  background: #fef35a;
  box-shadow: 3px 3px 3px 0px rgba(0, 0, 0, 0.25);
  display: flex;
  flex-direction: row;
  align-items: flex-end;
  font-size: 1.1em;
  font-style: normal;
  font-weight: 400;
  letter-spacing: 0.1rem;
  padding: 0.4em 1em;
  border: none;
  justify-content: space-between;
  box-sizing: border-box;
  white-space: nowrap;
  z-index: 5;
  gap: 1%;
}

.table {
  font-size: 1.2em;
}

.table-top {
  font-size: 1.4em;
}

.table-row {
  display: flex;
  flex-direction: row;
  align-items: flex-end;
  justify-content: space-between;
  white-space: nowrap;
  box-sizing: border-box;
  padding: 0.4em 1em;
  font-size: 1.1em;
  gap: 1%;
}

.info {
  display: none;
}

.table-top p,
.table-row p {
  text-align: left;
}

.table-top p:nth-child(1),
.table-row p:nth-child(1) {
  width: 20%;
}

.table-top p:nth-child(2),
.table-row p:nth-child(2) {
  width: 18%;
}

.table-top p:nth-child(3),
.table-row p:nth-child(3),
.table-top p:nth-child(5),
.table-row p:nth-child(5) {
  width: 12%;
}

.table-top p:nth-child(4),
.table-row p:nth-child(4) {
  width: 20%;
}

.table-top p:nth-child(7),
.table-row p:nth-child(7) {
  width: 9%;
}

.Bahnschrift {
  font-weight: 800;
  letter-spacing: 0.02em;
}

.Adumu {
  font-size: 110%;
}

//////////@at-root
///

.all-statistic {
  display: flex;
}

.darcekova-statistic {
  display: flex;
  justify-content: center;
  flex-direction: column;
  row-gap: 1.3em;
  margin: 1em auto 3em;
  align-items: center;
}

.hodnota {
  background-color: #ddd;
  color: black;
  font-weight: bold;
  border-radius: 8px;
  cursor: pointer;
  text-align: center;
  transition: background-color 0.3s, color 0.3s;
  font-size: 1.6em;
  display: flex;
  align-items: center;
  justify-content: center;
  height: 2em;
  width: 3em;

  &:hover {
    transform: scale(1.1);
    transition-duration: 0.2s;
  }
}

.nadpis {
  font-size: 1.9em;
  font-weight: 900;
  margin: 1em auto 0em;
}

.podnadpis {
  font-size: 1.6em;
  margin: 0.5em auto;
}

.stat-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  width: min-content;
}

.box-statistic {
  display: flex;
  justify-content: space-around;
  max-width: 40em;
  margin: auto;
  min-width: 25em;
}

.button {
  font-size: 1.2em;
  margin: auto;
}

//generovanie
.generovanie {
  display: flex;
  justify-content: center;
  gap: 5%;
  flex-wrap: wrap;
  align-items: center;
  margin: 2em auto;
  font-size: 0.9em;
}

input,
textarea,
select {
  border-radius: 1.0625em;
  background: #90ca50;
  box-shadow: -7px 5px 15px 0px rgba(0, 0, 0, 0.25) inset,
    0px 4px 4px 0px rgba(0, 0, 0, 0.25);
  padding: 0.35em 5%;
  width: 12em;
  box-sizing: border-box;
}

select {
  width: 16em;
}

label {
  font-size: 1.6em;
}

.generovanie .hodnota {
  height: 1.9em;
  width: 3em;
  margin: auto;
}

.row-form {
  display: flex;
  flex-direction: column;
  gap: 1em;
}

@media only screen and (max-width: 1000px) {
  //   .table-top p:nth-child(1) {
  //     width: 60%;
  //   }
  //   .table-top p:nth-child(2),
  //   .table-top p:nth-child(3),
  //   .table-top p:nth-child(4),
  //   .table-top p:nth-child(5) {
  //     display: none;
  //   }

  .table-top .info {
    display: block;
    text-align: right;
  }

  //   .table-top p:nth-child(6) {
  //     width: 15%;
  //   }

  //   .table-top p:nth-child(7) {
  //     width: 25%;
  //   }

  .table-top {
    gap: 3%;
  }

  .mobile section p {
    font-size: 1em;
  }
}

// @media only screen and (max-width: 650px) {
//   .table-top p:nth-child(1) {
//     width: 45%;
//   }

//   .table-top p:nth-child(7) {
//     width: 40%;
</style>
