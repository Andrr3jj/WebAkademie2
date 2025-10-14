<template>
  <div class="prikaz">
    <p class="nadpis">Príkaz k úhrade heligónky</p>

    <p class="popis">
      Mesačné podklady k úhrade výučby heligónky za {{ monthNameGenitive }}
      {{ currentYear }}.
    </p>

    <div class="uhrada">
      <div class="qr-code">
        <iframe
          v-if="payment && payment.qr"
          :src="payment.qr"
          style="width: 220px; height: 220px; border: none"
        ></iframe>
        <img
          v-else
          src="@/assets/images/gallery/defaultqrcode1.png"
          alt="Deafult platba"
        />
      </div>
      <div class="information">
        <p class="popis">💳 Údaje k platbe:</p>
        <div class="info">
          <p class="text">
            <span>Suma: </span>
            {{ payment && payment.price ? payment.price + " €" : "N/A" }}
          </p>
          <p class="text">
            <span>Splatnosť: </span>
            {{
              payment && payment.dueDate
                ? payment.dueDate
                : "do 15. " + monthName + " " + currentYear
            }}
          </p>
          <p class="text"><span>IBAN: </span> SK48 8330 0000 0027 0258 2577</p>
          <p class="text">
            <span>Poznámka k platbe: </span>
            {{
              qrInfoNote || (payment && payment.status ? payment.status : "N/A")
            }}
          </p>
        </div>
      </div>
    </div>

    <p class="popis">
      📷 Platbu môžete zrealizovať aj pomocou naskenovania QR kódu.
    </p>
  </div>
</template>

<script>
export default {
  props: {
    payment: { type: Object, required: true },
    monthName: { type: String, required: true },
    currentYear: { type: Number, required: true },
    monthNameGenitive: { type: String, required: false, default: "" },
  },
  computed: {
    qrInfoNote() {
      if (this.payment && this.payment.qr) {
        try {
          const url = new URL(this.payment.qr);
          return url.searchParams.get("info") || "";
        } catch {
          // fallback for non-standard URLs
          const match = this.payment.qr.match(/info=([^&]+)/);
          return match ? decodeURIComponent(match[1]) : "";
        }
      }
      return "";
    },
  },
};
</script>

<style lang="scss" scoped>
.prikaz {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}
.nadpis {
  font-size: 2.8em;
  font-weight: 600;
  margin-bottom: 0.8em;
  margin: 0.4em auto auto;
}

.popis {
  font-size: 1.7em;
  font-weight: 400;
  margin: 1.5em 0.2em;
  text-align: center;
}

.information .popis {
  margin: 1em 0.2em;
  text-align: left;
}

.uhrada {
  display: flex;
  gap: 3em;
  justify-content: center;
  align-items: center;
}

.qr-code img {
  max-width: 15em;
  min-width: 8em;
}

.info {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 0.5em;
}

.information {
  display: flex;
  flex-direction: column;
}

.text {
  font-size: 1.5em;
  font-weight: 200;
  text-align: left;
  span {
    font-weight: 600;
  }
}

@media only screen and (max-width: 600px) {
  .uhrada {
    gap: 2em;
    flex-direction: column-reverse;
  }
}
@media only screen and (max-width: 500px) {
  .nadpis {
    font-size: 2em;
    margin: auto;
  }

  .popis {
    font-size: 1.4em;
  }

  .text {
    font-size: 1.3em;
  }

  .popis {
    margin: 1em 0.2em;
  }
}
</style>
