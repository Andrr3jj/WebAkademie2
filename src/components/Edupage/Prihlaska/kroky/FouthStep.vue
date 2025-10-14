<template>
  <div class="fourth-step">
    <div class="three-columns">
      <div class="left-column">
        <h2>Vyberte si formu výučby, ktorá vám najviac vyhovuje</h2>
        <p>
          Každému žiakovi vyhovuje niečo iné. Vyberte si spôsob, akým by ste
          chceli, aby prebiehali hodiny heligónky:
        </p>
        <p class="small-text">
          Kliknutím na jednu z možností si vyberiete formu výučby, ktorá vám
          najviac vyhovuje.
        </p>
      </div>

      <div class="dva-boxy">
        <div class="middle-column">
          <div
            class="vyucba-card solo"
            :class="{ selected: model === 'solo' }"
            @click="model = 'solo'"
            role="button"
            tabindex="0"
            @keydown.enter.prevent="model = 'solo'"
            @keydown.space.prevent="model = 'solo'"
            aria-pressed="model === 'solo'"
          >
            <h3>Sólo</h3>
            <h4>Individuálna výučba</h4>
            <p style="display: flex; align-items: center; gap: 6px">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="8"
                height="10"
                viewBox="0 0 8 10"
                fill="currentColor"
              >
                <path
                  d="M1.52083 0.157509C1.2125 -0.0455796 0.825 -0.0522749 0.510417 0.137424C0.195833 0.327122 0 0.684202 0 1.07253V8.92828C0 9.3166 0.195833 9.67368 0.510417 9.86338C0.825 10.0531 1.2125 10.0442 1.52083 9.8433L7.52083 5.91542C7.81875 5.72126 8 5.37534 8 5.0004C8 4.62547 7.81875 4.28178 7.52083 4.08539L1.52083 0.157509Z"
                  fill="black"
                />
              </svg>
              Čo zahŕňa:
            </p>

            <img src="@/assets/images/icons/osoba.svg" alt="obrázok osoby" />
            <ul>
              <li>Výučba 1 žiak + 1 učiteľ</li>
              <li>Dĺžka lekcie: 30 minút</li>
              <li>Individuálny prístup a tempo výučby</li>
              <li>Ideálne pre hanblivejších alebo úplných začiatočníkov</li>
            </ul>
            <div class="cena">
              <p>Cena za mesiac:</p>
              <h3>64€</h3>
            </div>
          </div>
        </div>

        <div class="right-column">
          <div
            class="vyucba-card duo"
            :class="{ selected: model === 'duo' }"
            @click="model = 'duo'"
            role="button"
            tabindex="0"
            @keydown.enter.prevent="model = 'duo'"
            @keydown.space.prevent="model = 'duo'"
            aria-pressed="model === 'duo'"
          >
            <h3>Duo</h3>
            <h4>Dvojčlenná výučba</h4>
            <p style="display: flex; align-items: center; gap: 6px">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="8"
                height="10"
                viewBox="0 0 8 10"
                fill="currentColor"
              >
                <path
                  d="M1.52083 0.157509C1.2125 -0.0455796 0.825 -0.0522749 0.510417 0.137424C0.195833 0.327122 0 0.684202 0 1.07253V8.92828C0 9.3166 0.195833 9.67368 0.510417 9.86338C0.825 10.0531 1.2125 10.0442 1.52083 9.8433L7.52083 5.91542C7.81875 5.72126 8 5.37534 8 5.0004C8 4.62547 7.81875 4.28178 7.52083 4.08539L1.52083 0.157509Z"
                  fill="black"
                />
              </svg>
              Čo zahŕňa:
            </p>

            <img
              src="@/assets/images/icons/dveOsoby.svg"
              alt="dve osoby vedľa seba"
            />
            <ul>
              <li>Výučba 2 žiaci + 1 učiteľ</li>
              <li>Dĺžka hodiny: 45 minút</li>
              <li>Ideálne pre súrodencov alebo kamarátov</li>
              <li>Zábavnejšia a dynamickejšia výučba v malom kolektíve</li>
            </ul>
            <div class="cena">
              <p>Cena za mesiac:</p>
              <h3>56€</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    <p v-if="attempt && !isValid" class="error">Vyberte prosím formu výučby.</p>
  </div>
</template>

<script>
export default {
  name: "FourthStep",
  props: {
    modelValue: String,
    attempt: { type: Boolean, default: false },
  },
  emits: ["update:modelValue", "step-valid"],
  computed: {
    model: {
      get() {
        return this.modelValue;
      },
      set(value) {
        this.$emit("update:modelValue", value);
      },
    },
    isValid() {
      return this.modelValue === "solo" || this.modelValue === "duo";
    },
  },
  watch: {
    attempt: {
      immediate: true,
      handler() {
        this.$emit("step-valid", this.isValid);
      },
    },
    modelValue() {
      this.$emit("step-valid", this.isValid);
    },
  },
};
</script>

<style lang="scss" scoped>
.fourth-step {
  text-align: center;
  padding: 4em 2em;
  align-self: center;

  .error {
    color: #b00020;
    margin-top: 0.9em;
    font-size: 1.1em;
  }

  .dva-boxy {
    display: contents;
  }

  .three-columns {
    display: flex;
    gap: 5em;
    justify-content: space-between;
    align-items: flex-start;
  }

  .left-column {
    max-width: 29em;
    text-align: left;

    h2 {
      font-size: 2em;
      font-family: "Bahnschrift", sans-serif;
      margin-bottom: 1em;
      text-align: center;
    }

    p {
      font-size: 1.5em;
      margin-bottom: 2em;
    }

    .small-text {
      width: 93%;
      font-size: 1em;
    }
  }

  .middle-column,
  .right-column {
    font-size: 90%;
    align-self: center;
  }

  .solo {
    img {
      width: 2.3em;
    }
  }

  .duo {
    img {
      width: 3em;
    }
    h3 {
      left: 30% !important;
    }
    .cena h3 {
      left: 0 !important;
    }
  }

  .vyucba-card {
    position: relative;
    background: #fef35a80;
    border-radius: 30px 70px 30px 70px;
    padding: 2em;
    padding-bottom: 0em;
    text-align: left;
    cursor: pointer;
    box-shadow: inset 0 9px 5px rgba(0, 0, 0, 0.25),
      0px 4px 4px rgba(0, 0, 0, 0.25);
    transition: all 0.2s ease;
    width: 14em;

    h3 {
      font-size: 3.75em;
      color: #fef35a;
      text-align: center;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.25);
      font-style: normal;
      font-weight: 400;
      -webkit-text-stroke: 1px black;
      margin: 0;
      position: absolute;
      top: -1em;
      left: 25%;
    }

    h4 {
      font-size: 1.375em;
      text-align: center;
      font-weight: 600;
      margin: 1em 0;
      font-family: "Bahnschrift", sans-serif;
      margin-top: 0;
    }

    p {
      text-align: left;
      font-weight: bold;
      font-size: 0.9375em;
    }

    img {
      position: absolute;
      right: 3em;
      top: 4em;
    }

    ul {
      font-size: 1em;
      padding-left: 0;
      margin-bottom: 1em;
      list-style: none;

      li {
        position: relative;
        margin: 0.6em 0;
        text-align: left;
        font-size: 0.75em;
        padding: 0.4em 0;

        &::before {
          content: "";
          position: absolute;
          top: 90%;
          left: -1.2em;
          transform: translateY(-50%);
          width: calc(100% + 8px);
          height: 7px;
          background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='224' height='7' viewBox='0 0 224 7' fill='none'><path d='M5 3.25L0 0.863249V6.63675L5 4.25V3.25ZM4.5 3.75V4.25H224V3.75V3.25H4.5V3.75Z' fill='black' fill-opacity='0.35'/></svg>");
          background-repeat: no-repeat;
          background-position: left center;
          background-size: 100% 7px;
          pointer-events: none;
        }
      }
    }

    .cena {
      text-align: center;

      p {
        font-family: "Adumu", sans-serif;
        font-size: 1.25em;
        text-align: center;
        padding-left: 0;
      }

      h3 {
        position: relative;
        top: 0;
        left: 0;
        font-size: 2.5em;
      }
    }

    &:hover {
      transform: scale(1.03);
    }

    &.selected {
      border: 3px solid #8ec84e;
      box-shadow: 0 0 15px rgba(142, 200, 78, 0.7);
      transform: scale(1.05);
      background: #fef35a;
    }
  }
}

@media screen and (max-width: 1024px) {
  .three-columns {
    flex-direction: column;
    align-items: center;
  }
  .dva-boxy {
    display: flex;
    gap: 1em;
    width: 100%;
    justify-content: center;
  }
}

@media (max-width: 750px) {
  .fourth-step {
    .left-column {
      h2 {
        font-size: 4.5vw !important;
      }
      p {
        font-size: 3.2vw !important;
      }
      .small-text {
        font-size: 1em !important;
      }
    }
  }
  .dva-boxy {
    display: contents;
  }
}
</style>
