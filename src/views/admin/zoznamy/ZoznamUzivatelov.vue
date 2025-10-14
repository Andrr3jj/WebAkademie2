<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <div class="nadpis">
        <h5>Zoznam uživateľov</h5>
        <div class="button number">
          <p>{{ Object.keys(data).length === 0 ? "999+" : data.user_count }}</p>
        </div>
      </div>

      <div class="line horizontal w-80"></div>

      <table>
        <thead>
          <tr>
            <th style="width: 30%">Účet:</th>
            <th style="width: 30%">Email:</th>
            <th style="width: 20%">Registrácia:</th>
            <th style="width: 20%"></th>
          </tr>
        </thead>
        <!-- <tbody v-if="Object.keys(dataVyhladavanie).length === 0"> -->
        <tbody v-if="Object.keys(dataVyhladavanie).length === 0">
          <tr
            v-for="(item, index) in usersWithSummaries"
            :key="index"
            :class="{ 'mesiac-spocitanie': item.isSummary }"
          >
            <!-- Ak je sumárny riadok, zobrazíme len mesiac a súčet účtov -->
            <td
              v-if="item.isSummary"
              colspan="4"
              @click="toggleMonth(item.month)"
            >
              📅
              <strong>({{ getMonthName(item.month) }}) {{ item.month }}</strong>
              –
              <span>Počet účtov: {{ item.count }}</span>
            </td>

            <!-- Ak nie je sumárny riadok, zobrazíme bežného používateľa -->
            <td v-show="expandedMonths[item.month]" v-if="!item.isSummary">
              <strong>{{ getUserIndex(index) }}. </strong>
              {{ item.name }} {{ item.surname }}
            </td>
            <td v-show="expandedMonths[item.month]" v-if="!item.isSummary">
              {{ item.email }}
            </td>
            <td v-show="expandedMonths[item.month]" v-if="!item.isSummary">
              {{ item.timestamp.split(" ")[0].replace(/-/g, ".") }}
            </td>
            <td v-show="expandedMonths[item.month]" v-if="!item.isSummary">
              <div @click="upraveniePouzivatela(item.id)" class="button">
                <a>Upraviť</a>
              </div>
            </td>
          </tr>
        </tbody>
        <tbody v-else>
          <!-- Tu môžete dynamicky generovať riadky podľa potreby -->
          <tr v-for="(item, index) in dataVyhladavanie" :key="index">
            <td>
              <strong>{{ index + 1 }}. </strong>
              {{
                (dataVyhladavanie[index].name, dataVyhladavanie[index].surname)
              }}
            </td>
            <td>{{ dataVyhladavanie[index].email }}</td>
            <td>
              {{
                dataVyhladavanie[index].timestamp
                  .split(" ")[0]
                  .replace(/-/g, ".")
              }}
            </td>
            <td>
              <div
                @click="upraveniePouzivatela(dataVyhladavanie[index].id)"
                class="button"
              >
                <a>Upraviť</a>
              </div>
            </td>
          </tr>
          <!-- Pridajte ďalšie riadky podľa potreby -->
        </tbody>
      </table>

      <div class="search">
        <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
        <input
          @input="oneskoreneVyhladavanie()"
          v-model="search"
          type="text"
          placeholder="Vyhľadať podľa emailu"
        />
      </div>
    </div>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);

    if (this.$store.state.userRoles?.roles?.includes("user_master")) {
      this.nacitajUcty();
    } else {
      this.$router.go(-1);
    }
  },
  data() {
    return {
      data: {},
      dataVyhladavanie: [],
      search: "",
      expandedMonths: {},
    };
  },
  computed: {
    usersWithSummaries() {
      if (!this.data.users || this.data.users.length === 0) return [];

      let users = this.data.users.slice().reverse(); // Otočíme poradie (od najnovších po najstarších)
      let result = [];
      let previousMonth = "";
      let currentCount = 0;
      let tempUsers = [];

      users.forEach((user) => {
        let [year, month] = user.timestamp.split(" ")[0].split("-");
        let formattedMonth = `${month}.${year}`;

        // Ak sa mesiac zmenil, pridáme najskôr sumár a potom predchádzajúce používateľské záznamy
        if (formattedMonth !== previousMonth && previousMonth !== "") {
          result.push({
            isSummary: true,
            month: previousMonth,
            count: currentCount,
            expanded: false, // Rozbaľovanie skryté na začiatku
          });
          result.push(...tempUsers); // Pridáme zozbieraných používateľov pre predchádzajúci mesiac
          tempUsers = [];
          currentCount = 0;
        }

        // Uložíme používateľov do dočasného poľa
        tempUsers.push({ ...user, isSummary: false, month: formattedMonth });
        currentCount++;
        previousMonth = formattedMonth;
      });

      // Pridáme sumár a posledných používateľov po skončení cyklu
      if (previousMonth !== "") {
        result.push({
          isSummary: true,
          month: previousMonth,
          count: currentCount,
          expanded: false,
        });
        result.push(...tempUsers);
      }

      return result;
    },
  },

  methods: {
    toggleMonth(month) {
      if (this.expandedMonths[month] === undefined) {
        this.expandedMonths[month] = false; // Inicializácia, ak mesiac ešte neexistuje
      }
      this.expandedMonths[month] = !this.expandedMonths[month]; // Prepnutie hodnoty
    },
    getUserIndex(index) {
      // Získame celkový počet bežných používateľov
      let totalUsers = this.usersWithSummaries.filter(
        (item) => !item.isSummary
      ).length;

      console.log("totalUsers :>> ", totalUsers);
      console.log(
        "totalUsers - this.usersWithSummaries.slice(index).filter((item) => !item.isSummary).length +1 :>> ",
        totalUsers -
          this.usersWithSummaries.slice(index).filter((item) => !item.isSummary)
            .length +
          1
      );

      // Počítame od konca (najvyššie číslo je prvý používateľ)
      return this.usersWithSummaries
        .slice(index)
        .filter((item) => !item.isSummary).length;
    },
    getMonthName(monthYear) {
      const monthNames = {
        "01": "Január",
        "02": "Február",
        "03": "Marec",
        "04": "Apríl",
        "05": "Máj",
        "06": "Jún",
        "07": "Júl",
        "08": "August",
        "09": "September",
        10: "Október",
        11: "November",
        12: "December",
      };
      let [month] = monthYear.split(".");
      return monthNames[month] || "Neznámy mesiac";
    },
    nacitajUcty() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "user/stats/getUsers.php",
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
            this.data = response.data.data;
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    upraveniePouzivatela(id) {
      // this.$store.state.SnackBarText =
      //   "Buhužial ešte nieje možné upravovať používateľov: id používateľa: " +
      //   id;

      this.$router.push({
        path: "/admin/upravenie-pouzivatela",
        query: { id: id },
      });
    },
    vyhladavaniePouzivatela() {
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/user/search.php?email=" + this.search,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.dataVyhladavanie = response.data.data;
          } else {
            this.dataVyhladavanie = [];
          }
        })
        .catch((error) => {
          console.log(error);
          this.dataVyhladavanie = [];
        });
    },
    oneskoreneVyhladavanie() {
      // Vyčkajte 500 ms a potom zavolajte stiahniID()
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.vyhladavaniePouzivatela();
      }, 500);
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";
.mesiac-spocitanie {
  background-color: #f8f8f8;
  font-weight: bold;
  text-align: center;
  font-size: 1.2em;
  padding: 10px;

  td {
    border-radius: 2.1875em;
    background: #fef35a;
    box-shadow: 3px 3px 3px 0 rgba(0, 0, 0, 0.25);
    font-weight: 400;
    letter-spacing: 0.1rem;
    padding: 0.4em 1.5em;
    text-align: center;
  }
}

.mesiac-spocitanie:hover {
  background-color: #ffdb58; // Zvýraznenie pri hoveri
}

td span {
  font-size: 0.9em;
}

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

  border-collapse: separate;
  border-spacing: 0px 1px;
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
