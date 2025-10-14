<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Zoznam náučných videí</h5>

      <div class="line horizontal w-80"></div>

      <div class="buttons">
        <div
          @click="
            videosDifficulty = '1';
            filterSongs(this.videosDifficulty);
          "
          class="button"
          :class="{ green: videosDifficulty === '1' }"
        >
          <p>Začiatočník</p>
        </div>

        <div
          @click="
            videosDifficulty = '2';
            filterSongs(this.videosDifficulty);
          "
          class="button"
          :class="{ green: videosDifficulty === '2' }"
        >
          <p>Stred. pokročilý</p>
        </div>
        <div
          @click="
            videosDifficulty = '3';
            filterSongs(this.videosDifficulty);
          "
          class="button"
          :class="{ green: videosDifficulty === '3' }"
        >
          <p>Pokročilý</p>
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th style="width: 70%">Názov:</th>
            <th style="width: 5%"></th>
            <th style="width: 25%"></th>
          </tr>
        </thead>
        <tbody v-if="dataVyhladavanie.length === 0 && filterVideos.length == 0">
          <!-- Tu môžete dynamicky generovať riadky podľa potreby -->
          <tr v-for="(item, index) in data" :key="index">
            <td>
              <strong>{{ index + 1 }}. </strong> {{ data[index].name }}
            </td>
            <td>{{ data[index].soldNumber }}</td>
            <td>
              <div @click="upravenieZapisu(data[index].id)" class="button">
                <a>Upraviť</a>
              </div>
            </td>
          </tr>
          <!-- Pridajte ďalšie riadky podľa potreby -->
        </tbody>
        <tbody v-if="dataVyhladavanie.length === 0 && filterVideos.length != 0">
          <!-- Tu môžete dynamicky generovať riadky podľa potreby -->
          <tr v-for="(item, index) in filterVideos" :key="index">
            <td>
              <strong>{{ index + 1 }}. </strong> {{ filterVideos[index].name }}
            </td>
            <td>{{ filterVideos[index].soldNumber }}</td>
            <td>
              <div
                @click="upravenieZapisu(filterVideos[index].id)"
                class="button"
                :class="{ none: filterVideos[index].id === 0 }"
              >
                <a>Upraviť</a>
              </div>
            </td>
          </tr>
          <!-- Pridajte ďalšie riadky podľa potreby -->
        </tbody>
        <tbody v-else>
          <!-- Tu môžete dynamicky generovať riadky podľa potreby -->
          <tr v-for="(item, index) in dataVyhladavanie" :key="index">
            <td>
              <strong>{{ index + 1 }}. </strong>
              {{ dataVyhladavanie[index].name }}
            </td>
            <td>{{ dataVyhladavanie[index].soldNumber }}</td>
            <td>
              <div
                @click="upravenieZapisu(dataVyhladavanie[index].id)"
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
          type="text"
          placeholder="Vyhľadať náučné video"
          v-model="search"
        />
      </div>
    </div>
  </section>
</template>

<script>
export default {
  mounted() {
    window.scrollTo(0, 0);

    this.nacitajZapisy();
  },
  data() {
    return {
      data: [],
      dataVyhladavanie: [],
      filterVideos: [],
      search: "",
      videosDifficulty: "1",
    };
  },
  methods: {
    nacitajZapisy() {
      const axios = require("axios");
      const FormData = require("form-data");
      let data = new FormData();

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/product/stats/listVideos.php",
        // headers: {
        //   ...data.getHeaders()
        // },
        data: data,
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.data = response.data.data;

            // Rozdelenie záznamov podľa difficulty
            this.filterSongs(this.videosDifficulty);
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    upravenieZapisu(id) {
      // Presmerovanie na inú stránku s query parametrom "id"
      this.$router.push({
        path: "/admin/pridanie-videa",
        query: { id: id },
      });
    },
    filterSongs(difficulty) {
      // Vyčistenie filtrovaného poľa pred každým filtrovaním
      this.filterVideos = [];

      // Filtrácia piesní podľa vybranej ťažkosti
      this.data.forEach((song) => {
        if (song.difficulty === difficulty) {
          // Pridanie názvu piesne do filtrovaného poľa
          this.filterVideos.push({
            id: song.id,
            name: song.name,
            details: song.details,
            difficulty: song.difficulty,
          });
        }
      });

      if (this.filterVideos.length == 0) {
        this.filterVideos.push({
          id: 0,
          name: "Žiadne piesne",
        });
      }
    },
    vyhladavanieZapisu() {
      this.videosDifficulty = "0";
      const axios = require("axios");

      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url:
          this.$store.state.api +
          "/product/stats/searchVideos.php?search=" +
          this.search,
        // headers: {},
      };

      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request succesfull") {
            this.dataVyhladavanie = response.data.data;

            if (response.data.data.length == this.data.length) {
              this.dataVyhladavanie = [];
              this.videosDifficulty = "1";
              this.filterSongs(this.videosDifficulty);
            }
          } else {
            this.dataVyhladavanie = [];
            this.videosDifficulty = "1";
            this.filterSongs(this.videosDifficulty);
          }
        })
        .catch((error) => {
          console.log("error :>> ", error);
          this.dataVyhladavanie = [];
          this.videosDifficulty = "1";
          this.filterSongs(this.videosDifficulty);
        });
    },
    oneskoreneVyhladavanie() {
      // Vyčkajte 500 ms a potom zavolajte stiahniID()
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.vyhladavanieZapisu();
      }, 500);
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

.none {
  display: none;
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
td:nth-child(2) {
  text-align: center;
}

.button {
  width: 2.2em;
  padding: 0.15em 0.8em;
  margin: 0.1em 0;
  a {
    font-size: 0.52em;
  }
}

td:last-child .button {
  margin-left: auto;
}

.buttons {
  display: flex;
  justify-content: center;
  gap: 5%;
  align-items: center;
  margin: 2em auto;

  .button {
    width: auto;
    border-radius: 0.8em;
  }
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
