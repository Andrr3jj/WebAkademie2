<template>
  <div class="invite-friends-wrapper">
    <div class="text">
      <h3>Pozvi priateľov a získaj ešte viac!</h3>
      <p>
        Chceš získať extra body len za to, že niekomu odporučíš stránku? Pošli
        im svoj unikátny odkaz a ak sa prihlásia a splnia podmienky, získavaš
        bonusové body!
      </p>
    </div>
    <div class="link-block">
      <p class="label">Tvoj unikátny odkaz:</p>
      <p class="link" ref="refLink">{{ referralUrl }}</p>
      <button class="button" @click="copyToClipboard">Skopírovať</button>
      <transition name="fade">
        <p v-if="showCopied" class="copied-msg">Skopírované!</p>
      </transition>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations } from "vuex";

export default {
  name: "InviteFriends",
  data() {
    return {
      showCopied: false,
    };
  },
  computed: {
    ...mapState(["inviteCode"]),
    referralUrl() {
      return this.inviteCode
        ? `https://heligonkovaakademia.sk?reflink=${this.inviteCode}/`
        : "Načítavam...";
    },
  },
  mounted() {
    if (!this.inviteCode) {
      fetch("/user/info/getInviteCode.php")
        .then((res) => res.json()) // ✅ SPRÁVNE
        .then((result) => {
          if (result.status === "Request succesfull" && result.data) {
            this.setInviteCode(result.data); // ✅ použijeme IBA to, čo je v "data"
          } else {
            this.setInviteCode("chyba");
          }
        })
        .catch(() => {
          this.setInviteCode("chyba");
        });
    }
  },
  methods: {
    ...mapMutations(["setInviteCode"]),
    copyToClipboard() {
      const text = this.$refs.refLink.innerText;
      navigator.clipboard.writeText(text).then(() => {
        this.showCopied = true;
        setTimeout(() => {
          this.showCopied = false;
        }, 2000);

        this.$store.state.SnackBarText =
          "Pošli kamarátovy a po registrovaní a nákupu si vyzdvihni svoju odmenu.";
      });
    },
  },
};
</script>

<style scoped lang="scss">
@import "@/assets/sass/_colors.scss";

.invite-friends-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-left: 8em;
  margin-top: 1em;

  .text {
    flex: 1;
    font-size: 80%;

    h3 {
      font-family: "Bahnschrift", sans-serif;
      font-size: 2.2em;
      font-weight: bold;
      margin-bottom: 0.8em;
      text-align: left;
    }

    p {
      font-family: "Bahnschrift", sans-serif;
      font-size: 1.8em;
      line-height: 115%;
      text-align: left;
      width: 85%;
    }
  }

  .link-block {
    font-size: 80%;
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.8em;
    margin-top: 2em;
    margin-left: -5em;

    .label {
      font-family: "Bahnschrift", sans-serif;
      font-size: 1.9em;
      font-weight: bold;
    }

    .link {
      border: 6px solid #fef35a;
      border-radius: 25px;
      padding: 0.6em 1.2em;
      font-family: "Bahnschrift", sans-serif;
      font-size: 1.1em;
      word-break: break-word;
    }

    .button {
      font-family: "Adumu", sans-serif;
      padding: 0.5em 2em;
      color: black;
    }
  }
  .copied-msg {
    font-size: 1em;
    color: green;
    font-weight: bold;
    animation: pop 0.3s ease;
  }

  @keyframes pop {
    0% {
      opacity: 0;
      transform: scale(0.8);
    }
    100% {
      opacity: 1;
      transform: scale(1);
    }
  }

  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.5s;
  }
  .fade-enter,
  .fade-leave-to {
    opacity: 0;
  }
}
@media (max-width: 900px) {
  .invite-friends-wrapper {
    flex-direction: column;
    align-items: center;
    margin: 2em 1.5em;

    .text {
      text-align: center;

      h3,
      p {
        text-align: center !important;
        width: 100%;
      }

      p {
        font-size: 1.6em;
      }
    }

    .link-block {
      margin-left: 0;
      margin-top: 2em;
      align-items: center;
      text-align: center;

      .label {
        font-size: 1.6em;
      }

      .link {
        font-size: 1em;
        padding: 0.6em 1em;
        max-width: 100%;
      }

      .button {
        align-self: center;
      }
    }
  }
}
</style>
