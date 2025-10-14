<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Odosielanie e-mailov</h5>

      <div class="odosielanie-progress-box">
        <p v-if="odosielaniePrebieha">
          Odosielajú sa e-maily: {{ odoslaneCount }} / {{ celkovyPocet }}
        </p>

        <progress
          v-if="odosielaniePrebieha"
          :value="odoslaneCount"
          :max="celkovyPocet"
        ></progress>

        <p v-if="!odosielaniePrebieha && celkovyPocet > 0" class="success">
          ✅ E-maily boli úspešne odoslané!
        </p>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "OdosielanieMailovView",
  data() {
    return {
      odosielaniePrebieha: true,
      odoslaneCount: 0,
      celkovyPocet: 0,
    };
  },
  mounted() {
    const subject = localStorage.getItem("mail_subject");
    const image = localStorage.getItem("mail_image");

    if (!subject || !image) {
      this.$store.state.SnackBarText = "Chýbajú dáta na odoslanie.";
      this.$router.push("/admin");
      return;
    }

    this.spustiOdosielanie(subject, image);
  },
  methods: {
    async spustiOdosielanie(subject, image) {
      const axios = require("axios");
      const FormData = require("form-data");
      const data = new FormData();
      data.append("subject", subject);
      data.append("image", image);

      try {
        const response = await axios.post(
          this.$store.state.api + "/mail/adsSendEmail.php",
          data
        );

        if (
          response.data.status === "Request succesfull" &&
          Array.isArray(response.data.data)
        ) {
          const maily = response.data.data;
          this.celkovyPocet = maily.length;

          if (maily.length === 0) {
            this.$store.state.SnackBarText = "Neboli nájdené žiadne emaily.";
            this.odosielaniePrebieha = false;
            return;
          }

          for (let i = 0; i < maily.length; i++) {
            await new Promise((r) => setTimeout(r, 300));
            this.odoslaneCount = i + 1;
          }

          this.odosielaniePrebieha = false;
          this.$store.state.SnackBarText = `Úspešne odoslané: ${this.odoslaneCount}/${this.celkovyPocet}`;
        } else {
          this.$store.state.SnackBarText = "Chyba pri získaní emailov.";
          this.odosielaniePrebieha = false;
        }
      } catch (e) {
        console.warn("Chyba:", e);
        this.odosielaniePrebieha = false;
        this.$store.state.SnackBarText = "Spojenie zlyhalo.";
      }
    },
  },
};
</script>

<style scoped>
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

.odosielanie-progress-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1em;
  font-size: 1.5em;
  padding: 2em 0;
  text-align: center;

  progress {
    width: 70%;
    height: 25px;
    border-radius: 10px;
    overflow: hidden;
  }

  .success {
    font-size: 1.3em;
    color: green;
    margin-top: 1em;
  }
}
</style>
