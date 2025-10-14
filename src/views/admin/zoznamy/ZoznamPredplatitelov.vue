<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <div class="nadpis">
        <h5>Zoznam predplatitelov</h5>
      </div>

      <div class="line horizontal w-80"></div>

      <table>
        <thead>
          <tr>
            <th style="width: 30%">Účet:</th>
            <th style="width: 40%">Email:</th>

            <th style="width: 30%">Typ pred:</th>
          </tr>
        </thead>
        <tbody>
          <!-- Tu môžete dynamicky generovať riadky podľa potreby -->
          <tr v-for="(item, index) in sortedData" :key="index">
            <td>
              <strong>{{ index + 1 }}. </strong>
              {{ item.name + " " + item.surname }}
            </td>
            <td>{{ item.email }}</td>
            <td>{{ item.subscription_name }}</td>
          </tr>
          <!-- Pridajte ďalšie riadky podľa potreby -->
        </tbody>
      </table>
    </div>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);

    this.nacitajUcty();
  },
  data() {
    return {
      data: [],
    };
  },
  computed: {
    sortedData() {
      const norm = (s) => (s || "").toString().trim().toLowerCase();
      const squeeze = (s) => norm(s).replace(/\s+/g, ""); // "pol rok" -> "polrok"
      const order = { rok: 3, polrok: 2, mesiac: 1 }; // väčšie = vyššie v tabuľke

      return [...this.data].sort((a, b) => {
        const ta = order[squeeze(a.subscription_name || a.name)] || 0;
        const tb = order[squeeze(b.subscription_name || b.name)] || 0;

        if (ta !== tb) return tb - ta; // rok > polrok > mesiac
        // sekundárne (stabilné): podľa priezviska, mena
        const A = `${a.surname || ""} ${a.name || ""}`.trim();
        const B = `${b.surname || ""} ${b.name || ""}`.trim();
        return A.localeCompare(B, "sk");
      });
    },
  },
  methods: {
    nacitajUcty() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/subscription/getSubscribers.php",
        // headers: {
        //   ...data.getHeaders()
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          console.log(JSON.stringify(response.data));

          if (response.data.status == "Request succesfull") {
            this.data = response.data.data || [];
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
  },
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
  line-height: 115%; /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

table {
  width: 80%;
  border-collapse: collapse;
  margin: 3em 10%;
  margin-bottom: 6em;
}

th,
td {
  padding: 0.1em 0.5em;
  text-align: left;
  font-size: 1.3em;
}

td:not(td:last-child) {
  border-bottom: 1px solid #dddddd5e;
}

th {
  padding: 0.5em;
}

th:nth-child(2),
td:nth-child(2),
th:nth-child(3),
td:nth-child(3) {
  text-align: center;
}

td:nth-child(2) {
  font-weight: 600;
}

.button {
  width: 2.2em;
  padding: 0.15em 0.8em;
  margin: 0.1em 0;
  a {
    font-size: 0.52em;
  }
}

.number {
  padding: 0.3em 2em;
  justify-content: center;

  p {
    font-size: 0.8em;
  }
}

.nadpis {
  display: flex;
  align-items: flex-start;
  justify-content: center;
  margin-left: 18%;
  gap: 3em;
}

td:last-child .button {
  margin-left: auto;
}

.search {
  border-radius: 1.5625rem;
  border: 6px solid #fef35a;
  background: rgba(233, 233, 233, 0.93);
  box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.35);

  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0 0.5em;
  margin: 1em 0;

  width: 40em;
  font-size: 0.8em;
  position: fixed;
  left: 50%;
  transform: translate(-50%, -50%);
  bottom: 0em;

  img {
    width: 2em;
  }

  input {
    width: 100%;

    font-size: 1.25em;
    font-style: normal;
    font-weight: 400;
    line-height: 137.53%; /* 1.71913rem */
    letter-spacing: 0.0625rem;

    margin-bottom: -0.2em;
  }
}

@media only screen and (max-width: 1500px) {
  table {
    margin: 3em 2%;
    width: 98%;
  }
}
</style>
