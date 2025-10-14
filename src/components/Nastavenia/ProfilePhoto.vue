<template>
  <div class="profile-section">
    <!-- Profilová fotka + texty spolu -->
    <div class="profile-combined">
      <div
        class="photo-container"
        @mouseover="hover = true"
        @mouseleave="hover = false"
      >
        <img :src="avatarSrc" alt="Profilová fotka" class="profile-photo" />
        <div class="photo-actions" v-if="hover">
          <img
            :src="editIconSrc"
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
        <p class="greeting-line">
          <template v-if="!profilePhotoRaw || isDefaultPhoto">
            <span
              >Pridaj si svoju fotku a ukáž heligónkovému svetu, kto si!</span
            >
          </template>
          <template v-else>
            <span>Dnes vyzeráš super – úsmev ti fakt pristane 😊</span>
          </template>
        </p>
      </div>
    </div>
  </div>
  <div v-if="zobrazGaleriu" class="photo-gallery">
    <div class="photo-grid">
      <img
        v-for="(fotka, index) in defaultPhotos"
        :key="index"
        :src="fotka"
        @click="nastavProfilovkuZUrl(fotka)"
        class="gallery-photo"
      />
    </div>
  </div>
</template>

<script>
import { RefreshCcw, Images } from "lucide-vue-next";
import editIcon from "@/assets/images/icons/upravit.svg";

export default {
  name: "ProfilePhotoWithFriendList",
  components: {
    RefreshCcw,
    Images,
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
      return this.$store?.state?.user?.name || "Používateľ";
    },
    profilePhotoRaw() {
      return this.$store?.state?.user?.profilePhotoUrl || null;
    },
    isDefaultPhoto() {
      return this.profilePhotoRaw?.includes("/default/");
    },
    avatarSrc() {
      // čistá verzia bez www.
      return this.stripWWW(this.profilePhotoRaw);
    },
    editIconSrc() {
      return editIcon;
    },
  },
  watch: {
    zobrazGaleriu(nova) {
      if (nova) {
        this.nacitajDefaultneFotky();
      }
    },
  },
  methods: {
    triggerFileInput() {
      this.$refs.fileInput?.click();
    },
    stripWWW(url) {
      if (!url) return null;
      try {
        const u = new URL(url);
        if (u.hostname.startsWith("www.")) {
          u.hostname = u.hostname.slice(4);
        }
        return u.toString();
      } catch {
        return url.replace(/^https?:\/\/www\./, (m) => m.replace("www.", ""));
      }
    },
    async uploadProfilePhoto(event) {
      const file = event.target?.files?.[0];
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
          // odstrániť www. z každej
          this.defaultPhotos = (result.data || []).map((u) => this.stripWWW(u));
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
        alert("Chyba pri nastavovaní náhodnej fotky.");
      }
    },
    vypocitajMargin(hviezdyRaw) {
      const hviezdy = Number(hviezdyRaw);
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
  justify-content: center;
  padding: 2rem;
  animation: fadeIn 0.8s ease;
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
  gap: 2rem;
  transition: all 0.3s ease;
  animation: fadePop 0.5s ease;

  @media screen and (max-width: 1024px) {
    flex-direction: column;
    text-align: center;
    gap: 0rem;
  }
}

.photo-container {
  position: relative;
  width: 120px;
  height: 120px;
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

  h2 {
    font-size: 2.5em;
    font-weight: 500;
    margin-bottom: 0.3em;
    animation: fadeIn 0.6s ease;
  }

  .greeting-line {
    font-size: 1.5em;
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
</style>
