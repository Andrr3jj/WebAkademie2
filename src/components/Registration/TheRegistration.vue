<template>
  <form class="register-form" @submit.prevent="handleSubmit" novalidate>
    <!-- ✅ OBAL LEN PRE Meno → Krajina -->
    <div id="reg-block-basic" class="reg-block-basic">
      <div class="row" v-for="field in fields" :key="field.name">
        <div>
          <img :src="require(`@/assets/images/icons/${field.icon}`)" />
        </div>
        <input
          :type="field.type"
          :placeholder="field.placeholder"
          :name="field.name"
          v-model="formData[field.model]"
          @invalid.prevent
          autocomplete="on"
        />
      </div>

      <div class="row">
        <div>
          <img
            src="@/assets/images/icons/state.png"
            class="krajina"
            alt="ikona krajiny"
          />
        </div>
        <select v-model="formData.krajina" required>
          <option disabled value="">-- Vyberte krajinu --</option>
          <option
            v-for="krajina in dostupneKrajiny"
            :key="krajina.code"
            :value="krajina.name"
          >
            {{ krajina.name }}
          </option>
        </select>
      </div>
    </div>
    <!-- /reg-block-basic -->

    <div class="row suhlas checkbox">
      <input type="checkbox" v-model="formData.suhlasReklama" />
      <label>Chcem dostávať informácie o novinkách</label>
    </div>

    <div class="row suhlas checkbox">
      <input type="checkbox" v-model="formData.suhlas" />
      <label class="vop">
        Súhlasím s
        <a
          target="_blank"
          href="https://heligonkovaakademia.sk/obchodne-podmienky"
        >
          obchodnými podmienkami
        </a>
      </label>
    </div>

    <button class="button" type="submit"><a>Registrovať</a></button>
  </form>
</template>

<script>
export default {
  data() {
    return {
      formData: {
        meno: "",
        priezvisko: "",
        email: "",
        heslo: "",
        hesloOpakovane: "",
        suhlasReklama: true,
        suhlas: false,
        krajina: "Slovenská republika",
      },
      dostupneKrajiny: [
        { code: "SK", name: "Slovenská republika" },
        { code: "CZ", name: "Česká republika" },
        { code: "PL", name: "Poľská republika" },
      ],
      fields: [
        {
          name: "meno",
          model: "meno",
          type: "text",
          placeholder: "Meno",
          icon: "profil.svg",
        },
        {
          name: "priezvisko",
          model: "priezvisko",
          type: "text",
          placeholder: "Priezvisko",
          icon: "profil.svg",
        },
        {
          name: "email",
          model: "email",
          type: "email",
          placeholder: "Email",
          icon: "email.svg",
        },
        {
          name: "heslo",
          model: "heslo",
          type: "password",
          placeholder: "Heslo",
          icon: "heslo.svg",
        },
        {
          name: "hesloOpakovane",
          model: "hesloOpakovane",
          type: "password",
          placeholder: "Heslo ",
          icon: "heslo.svg",
        },
      ],
    };
  },

  mounted() {
    this.syncAutofilledFields();
  },

  methods: {
    handleSubmit(version) {
      if (!this.validate()) return;

      const dataToSend = {
        meno: this.formData.meno,
        priezvisko: this.formData.priezvisko,
        email: this.formData.email,
        heslo: this.formData.heslo,
        ads: this.formData.suhlasReklama,
        krajina: this.formData.krajina,
        version,
      };

      this.$store.dispatch("registracia", dataToSend);
    },

    syncAutofilledFields() {
      // počkaj, kým prehliadač auto-vyplní polia
      this.$nextTick(() => {
        setTimeout(() => {
          this.fields.forEach((field) => {
            const input = document.querySelector(`input[name="${field.name}"]`);
            if (input && input.value) {
              this.formData[field.model] = input.value;
            }
          });
        }, 300);
      });
    },

    validate() {
      if (
        !this.formData.meno ||
        !this.formData.priezvisko ||
        !this.formData.email ||
        !this.formData.heslo ||
        !this.formData.hesloOpakovane
      ) {
        return this.showError(
          "Skontrolujte si vaše údaje aby boli správne vyplnené."
        );
      }

      const email = this.formData.email.trim();
      const allowedDomains = [
        "gmail.com",
        "yahoo.com",
        "hotmail.com",
        "azet.sk",
        "zoznam.sk",
        "centrum.sk",
        "seznam.cz",
        "outlook.com",
      ];

      if (!email) {
        return this.showError("Emailové pole nemôže byť prázdne.");
      }

      if (!email.includes("@")) {
        return this.showError("Email musí obsahovať znak @.");
      }

      const [localPart, domain] = email.split("@");

      if (!localPart || localPart.length < 2) {
        return this.showError("Pred znakom @ musí byť aspoň 2 znaky.");
      }

      if (!domain) {
        return this.showError("Email musí obsahovať doménu za znakom @.");
      }

      if (!domain.includes(".")) {
        return this.showError("Doména musí obsahovať bodku (napr. .com, .sk).");
      }

      const domainParts = domain.split(".");
      if (domainParts.some((part) => part.length === 0)) {
        return this.showError("Časť domény za bodkou je neplatná.");
      }
      if (!domain || !domain.includes(".")) {
        return this.showError("Zadali ste neplatnú doménu.");
      }

      if (!allowedDomains.includes(domain.toLowerCase())) {
        return this.showError(
          `Doména „${domain}“ sa nezdá byť platná. Skontrolujte preklep.`
        );
      }

      if (this.formData.heslo !== this.formData.hesloOpakovane) {
        return this.showError("Heslá sa nezhodujú.");
      }

      if (this.formData.heslo.length < 8) {
        return this.showError("Heslo musí mať aspoň 8 znakov.");
      }

      if (!/[A-Z]/.test(this.formData.heslo)) {
        return this.showError(
          "Heslo musí obsahovať aspoň jedno veľké písmeno."
        );
      }

      if (!/\d/.test(this.formData.heslo)) {
        return this.showError("Heslo musí obsahovať aspoň jedno číslo.");
      }

      if (!this.formData.krajina) {
        return this.showError("Vyberte krajinu pôvodu.");
      }

      if (!this.formData.suhlas) {
        return this.showError("Musíte súhlasiť s obchodnými podmienkami.");
      }

      return true;
    },

    showError(msg) {
      this.$store.state.SnackBarText = msg;
      return false;
    },
  },
};
</script>

<style scoped lang="scss">
@import "@/assets/sass/_colors.scss";
.button {
  padding: 0.5em 0.9em;
  margin: 0 auto;
}

.button.green {
  margin: 1.8em 18% auto auto;
}

.box-item {
  padding: 1em 0;
  justify-content: space-between;
}

.box {
  gap: 3%;
}

.absolute-img {
  position: absolute;
  bottom: -1.35%;
  left: -2%;
  width: 16vw;
  max-width: 18em;
  filter: drop-shadow(10px 5px 10px rgba(0, 0, 0, 0.35));
}

.row select {
  font-size: 1.4em;
  font-style: normal;
  font-weight: 300;
  line-height: 115%;
  background: transparent;
  border: none;
  padding: 0.4em 0.5em;
  text-align: left;
  width: 80%;
}

.krajina {
  height: 1.75em;
}

.strong {
  font-weight: 600;
}

.box-item p:first-child {
  font-size: 1.7vw; //1.8em
  width: 95%;
}

.mobile .box-item p:first-child {
  font-size: 5vw !important;
  text-align: center;
}

.box-item p:last-child {
  width: 60%;
}

.vop {
  text-decoration: underline;
}

.suhlas {
  color: #000;
  text-align: center;
  font-size: 1rem;
  font-style: normal;
  font-weight: 300;
  line-height: 115%; /* 1.4375rem */
  width: 100%;
  margin-bottom: 1.5em;

  background: transparent;
  box-shadow: none;
  justify-content: center;
}
#Registrácia > div > section:nth-child(1) > div > div {
  align-items: flex-end;
}

.mobile section:nth-child(1) {
  .absolute-img {
    max-width: 20em;
  }
  .box-item {
    padding: 0;
    p:first-child {
      width: 95%;
      margin-right: 2.5%;
      font-size: 1.9em;
    }
    p {
      width: 75%;
      max-width: 18em;
      margin-right: 10%;
    }

    p:last-child {
      width: 55%;
      text-align: right;
    }
  }
}
.mobile section p {
  margin: 0.3em 0;
  font-size: 3.9vw;
}

.checkbox {
  display: flex;
  align-items: center;
  flex-direction: row;

  input,
  select {
    width: auto;
    margin: auto 0.5em;
    /* font-size: 1em; */
    transform: scale(1.5);
    cursor: pointer;
  }

  label {
    font-size: 1em;
    font-weight: 100;
  }
}

@media screen and (max-width: 1300px) {
  // .absolute-img {
  //   display: none;
  // }

  #Registrácia > div > div > div:nth-child(1) > p:nth-child(2) {
    margin-top: auto;
  }

  #Registrácia > div > div > div:nth-child(1) > p:nth-child(5) {
    margin-bottom: auto;
  }

  p {
    font-size: 1.4em;
  }
}

@media screen and (max-width: 1000px) {
  .box-item p {
    margin: 0.6em auto;
    width: 80%;
    text-align: center;

    &:first-child {
      font-size: 2.5em;
    }
  }
}
@media screen and (max-width: 750px) {
  .absolute-img {
    left: -4%;
    width: 30vw;
    max-width: 13em;
  }

  label {
    font-size: 1.3em;
    font-weight: 100;
  }

  .box-item p {
    text-align: right;
  }
  .suhlas {
    text-align: center !important;
    margin: auto !important;
  }
  .mobile {
    padding-bottom: 7em;
  }
}

@media screen and (max-width: 450px) {
  .mobile section:nth-child(1) .box-item p:first-child {
    font-size: 5.3vw;
  }

  .box-item p {
    font-size: 1.3em;
  }

  .mobile section:nth-child(1) .box-item p {
    width: 80vw;
  }

  .checkbox input {
    transform: none;
  }
}
</style>
