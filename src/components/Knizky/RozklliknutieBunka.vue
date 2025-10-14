<template>
  <div class="bunka" v-if="typ !== 'zvuk'">
    <p class="text oznacenie">{{ popis }}</p>
    <p class="text rozdelovac">|</p>
    <p class="text nazov">{{ nazov }}</p>
    <img src="@/assets/images/icons/hrat.png" alt="Zapnúť" />
  </div>
  <div v-else class="bunka zvuk">
    <AudioPlayer
      :src="video"
      :id="id"
      :aktualnePrehravaneId="aktualnePrehravaneId"
      @update-id="vyberText"
      @prehravanie="emitPrehravanie"
    />
    <p class="text nazov">{{ nazov }}</p>
    <p class="text oznacenie">{{ popis }}</p>
    <div @click.stop="vyberText" class="bunka for-text">
      <p class="text">Text</p>
    </div>
  </div>
</template>

<script>
import AudioPlayer from "@/components/Functionality/AudioPlayer.vue"; // uprav cestu ak treba

export default {
  components: { AudioPlayer },
  props: {
    nazov: String,
    video: String,
    popis: String,
    typ: String,
    id: Number,
    aktualnePrehravaneId: Number,
  },
  methods: {
    vyberText() {
      this.$emit("update-id", this.id);
      this.$emit("scroll-k-spevniku");
    },
    emitPrehravanie() {
      this.$emit("prehravanie");
    },
  },
};
</script>

<style scoped lang="scss">
/* 💛 Presne tvoje pôvodné štýly 💛 */
.bunka:not(.bunka.zvuk) {
  background-color: #fef35a;
  box-shadow: 0.1em 0.1em 1em #00000040;
  border-radius: 2rem;
  padding: 0.2em 1.5em;
  min-height: 2em;
  margin: 1em 0;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  transition: background-color 0.3s, transform 0.2s, box-shadow 0.2s;

  &:hover {
    background-color: #ffeb3b;
    transform: translateY(-3px);
    cursor: pointer;
    box-shadow: 0.1em 0.1em 0.5em #00000040;
  }

  &:active {
    transform: translateY(1px);
    background-color: #90ca50;
    filter: brightness(0.95);
  }
}

.zvuk {
  display: flex;
  align-items: center;
  justify-content: left;
  gap: 0.5em;
  margin: 0.4em 0;
  cursor: pointer;
  position: relative;
}

.zvuk > p {
  flex: unset;
}

.zvuk .oznacenie {
  font-weight: 300;
  margin: 0 0.3em 0 auto;
}

.zvuk .nazov {
  font-weight: 500;
}

.zvuk img {
  background-color: #fef35a;
  box-shadow: 0.1em 0.1em 1em #00000040;
  border-radius: 2rem;
  padding: 0.5em 1em;
  margin: 0 1em;
  z-index: 3;
}

.text {
  font-size: 1em;
  text-align: left;
  margin: 0 0.2em;
}

.for-text {
  padding: 0em 2em !important;
  margin-top: 0 !important;
  margin-bottom: 0 !important;

  p {
    font-size: 1em;
  }
}

.oznacenie {
  font-weight: 600;
  flex: 0.2;
}

.nazov {
  flex: 0.7;
}

.rozdelovac {
  transform: scale(1.1);
  flex: 0.03;
}

img {
  width: 1em;
  height: auto;
  flex: 0.038;
  transition: transform 0.2s;

  .bunka:hover:not(.bunka.zvuk) & {
    transform: scale(1.1);
  }

  .bunka:active:not(.bunka.zvuk) & {
    transform: scale(0.95);
  }
}
</style>
