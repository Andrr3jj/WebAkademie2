<template>
  <div class="profile-section">
    <div class="profile-combined">
      <div
        class="photo-container"
        @mouseover="hover = true"
        @mouseleave="hover = false"
      >
        <img
          v-if="profilePhoto"
          :src="profilePhoto"
          alt="Profilová fotka"
          class="profile-photo"
        />

        <div class="photo-actions" v-if="hover">
          <img
            src="@/assets/images/icons/upravit.svg"
            alt="Upraviť"
            class="edit-icon"
            @click="triggerFileInput"
          />
          <RefreshCcw class="refresh-icon" @click="setRandomProfilePhoto" />
          <Images
            class="gallery-icon"
            @click="zobrazGaleriu = !zobrazGaleriu"
          />
        </div>
        <input
          ref="fileInput"
          type="file"
          accept="image/jpeg, image/png, image/gif"
          class="upload-input"
          @change="uploadProfilePhoto"
        />
      </div>

      <div class="photo-text">
        <h2>Ahoj {{ userName }}!</h2>

        <template v-if="userRole === 'parent'">
          <p class="greeting-line">
            Všetko pod palcom: <br />
            Výučba, platby, pokroky
          </p>
        </template>

        <template v-else>
          <p class="greeting-line">Si pripravený naučiť <br />sa niečo nové?</p>
        </template>
      </div>
      <div
        v-if="userRole !== 'parent'"
        class="heligonka"
        :style="vypocitajMargin(player.stars)"
      >
        <TaskStarsCount :player="player" />
      </div>
    </div>
  </div>

  <div v-if="zobrazGaleriu" class="photo-gallery">
    <div class="photo-grid">
      <img
        v-for="(fotka, index) in defaultPhotos"
        :key="index"
        :src="stripWWW(fotka)"
        @click="nastavProfilovkuZUrl(fotka)"
        class="gallery-photo"
      />
    </div>
  </div>
</template>

<script>
import { RefreshCcw, Images } from "lucide-vue-next";
import TaskStarsCount from "./TaskStarsCount.vue";

export default {
  name: "ProfilePhotoWithFriendList",
  components: {
    RefreshCcw,
    Images,
    TaskStarsCount,
  },
  props: {
    player: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      hover: false,
      zobrazGaleriu: false,
      defaultPhotos: [],
    };
  },
  computed: {
    userName() {
      return this.$store.state.user?.name || "Používateľ";
    },
    profilePhotoRaw() {
      return this.$store.state.user?.profilePhotoUrl || null;
    },
    isDefaultPhoto() {
      return this.profilePhotoRaw?.includes("/default/");
    },
    profilePhoto() {
      if (!this.profilePhotoRaw) return null;
      // ak je relatívna cesta, nech to funguje
      if (this.profilePhotoRaw.startsWith("/")) {
        return this.stripWWW(window.location.origin + this.profilePhotoRaw);
      }
      return this.stripWWW(this.profilePhotoRaw);
    },
    avatarSrc() {
      // žiadny fallback, zobrazí len ak je fotka
      return this.profilePhoto;
    },
    userRole() {
      return this.$store.state.user?.role || "student";
    },
  },
  mounted() {
    this.$watch(
      () => this.zobrazGaleriu,
      (novyStav) => {
        if (novyStav) {
          this.nacitajDefaultneFotky();
        }
      }
    );
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput?.click();
    },
    async uploadProfilePhoto(event) {
      const file = event.target.files[0];
      if (file) {
        const formData = new FormData();
        formData.append("profilePhoto", file);
        try {
          const response = await fetch(
            "https://heligonkovaakademia.sk/api/user/info/uploadProfilePhoto.php",
            {
              method: "POST",
              body: formData,
            }
          );
          const result = await response.json();
          if (result.status === "Request succesfull") {
            await this.$store.dispatch("overeniePrihlasenia");
          } else {
            alert("Chyba pri nahrávaní fotky.");
          }
        } catch (e) {
          alert("Nastala chyba pri nahrávaní fotky.");
        }
      }
    },
    async nacitajDefaultneFotky() {
      try {
        const response = await fetch(
          "https://heligonkovaakademia.sk/api/user/info/loadProfilePhotos.php"
        );
        const result = await response.json();
        if (result.status === "Request succesfull") {
          this.defaultPhotos = result.data;
        }
      } catch (e) {
        alert("Nepodarilo sa načítať fotky z galérie.");
      }
    },
    async nastavProfilovkuZUrl(url) {
      try {
        const cleaned = this.stripWWW(url);
        const response = await fetch(
          "https://heligonkovaakademia.sk/api/user/info/setCustomProfilePhoto.php",
          {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: new URLSearchParams({
              profilePhotoUrl: cleaned,
            }).toString(),
          }
        );
        const result = await response.json();
        if (result.status === "Request succesfull") {
          await this.$store.dispatch("overeniePrihlasenia");
          this.zobrazGaleriu = false;
        } else {
          alert("Nepodarilo sa nastaviť profilovú fotku.");
        }
      } catch (e) {
        alert("Chyba pri nastavovaní profilovky.");
      }
    },
    stripWWW(url) {
      if (!url) return url;
      try {
        const u = new URL(url);
        if (u.hostname.startsWith("www.")) {
          u.hostname = u.hostname.slice(4);
        }
        return u.toString();
      } catch {
        // fallback: odstráni "www." ak je priamo v texte
        return url.replace(/^https?:\/\/www\./, (m) => m.replace("www.", ""));
      }
    },
    async setRandomProfilePhoto() {
      try {
        const response = await fetch(
          "https://heligonkovaakademia.sk/api/user/info/deleteProfilePhoto.php"
        );
        const result = await response.json();
        if (result.status === "Request succesfull") {
          await this.$store.dispatch("overeniePrihlasenia");
        } else {
          alert("Chyba pri nastavení náhodnej fotky.");
        }
      } catch (e) {
        alert("Chyba pri nastavení náhodnej fotky.");
      }
    },
    vypocitajMargin(hviezdyRaw) {
      const hviezdy = Number(hviezdyRaw);
      console.log("Počet hviezd:", hviezdy);

      if (hviezdy >= 5) return { marginLeft: "-11em" };
      if (hviezdy === 4) return { marginLeft: "-10em" };
      if (hviezdy === 3) return { marginLeft: "-9em" };
      return { marginLeft: "-8.5em" };
    },
  },
};
</script>

<style scoped lang="scss">
.profile-section {
  display: flex;
  animation: fadeIn 0.8s ease;
  margin-bottom: 1em;
  justify-content: space-between;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(10px);
  }

  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadePop {
  from {
    opacity: 0;
    transform: scale(0.9);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes photoSwitch {
  0% {
    opacity: 0;
    transform: scale(0.95);
  }

  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.photo-gallery {
  padding: 1rem;
  border-radius: 1em;
  animation: fadeIn 0.4s ease;
}

.photo-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
}

.gallery-photo {
  width: 80px;
  height: 80px;
  border-radius: 10px;
  cursor: pointer;
  object-fit: cover;
  transition: transform 0.2s;

  &:hover {
    transform: scale(1.1);
  }
}

.profile-combined {
  display: flex;
  align-items: center;
  gap: 1em;
  transition: all 0.3s ease;
  animation: fadePop 0.5s ease;

  @media screen and (max-width: 1024px) {
    flex-direction: column;
    text-align: center;
    gap: 0rem;
  }
}

.heligonka {
  display: flex;
  align-items: flex-start;
  margin-top: 5em;
}

.photo-container {
  position: relative;
  width: 117px;
  height: 117px;
  border-radius: 40px;
  overflow: hidden;
  border: 6px solid #ffef5c;
  background-color: #ddd;
  animation: popIn 0.5s ease;

  @keyframes popIn {
    from {
      transform: scale(0.95);
      opacity: 0;
    }

    to {
      transform: scale(1);
      opacity: 1;
    }
  }

  .profile-photo {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .photo-actions {
    position: absolute;
    top: 20px;
    right: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 8px;
    padding: 4px;
    animation: fadeIn 0.4s ease;
    z-index: 2;

    .edit-icon,
    .remove-icon,
    .refresh-icon,
    .gallery-icon {
      width: 20px;
      height: 20px;
      cursor: pointer;
      transition: transform 0.2s;

      &:hover {
        transform: scale(1.2);
      }
    }
  }

  .upload-input {
    display: none;
  }
}

.photo-text {
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: left;
  width: 20em;

  h2 {
    font-size: 2.7em;
    font-weight: 500;
    margin: 0;
    margin-bottom: 0.5em;
    animation: fadeIn 0.6s ease;
  }

  .greeting-line {
    font-size: 1.4em;
    font-weight: 500;
    color: #111;
    animation: fadeIn 0.8s ease;
    text-align: left;
  }

  @media screen and (max-width: 1024px) {
    h2 {
      font-size: 2em;
      text-align: center;
    }

    .greeting-line {
      font-size: 1.2em;
      text-align: center;
    }
  }
}

@media screen and (max-width: 1280px) {
  .profile-section {
    .heligonka {
      margin-top: 4em;
    }

    .photo-container {
      width: 80px;
      height: 80px;
    }
  }
}

@media only screen and (max-width: 1024px) {
  .profile-section {
    margin-top: 1em;
    justify-content: space-around !important;

    .heligonka {
      margin-top: 5em;
      margin-left: -5.5em !important;
    }

    .profile-combined {
      flex-direction: row;
      gap: 1em;
    }

    .photo-container {
      width: 117px;
      height: 117px;
    }
  }
}

@media screen and (max-width: 750px) {
  .profile-section {
    flex-direction: column;

    .photo-container {
      width: 130px;
      height: 90px;
    }

    .profile-combined {
      justify-content: center;
    }
    .heligonka {
      margin-top: 0;
      margin-left: 0 !important;
      justify-content: center;
    }
  }
}
</style>
