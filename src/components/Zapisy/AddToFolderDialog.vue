<template>
  <teleport to="body">
    <div class="overlay" @click.self="$emit('close')">
      <div class="modal-overlay" @click.self="$emit('close')">
        <div class="modal-content base-modal">
          <div class="modal-scroll">
            <h2>Pridať „{{ item.nazov }}“ do priečinka</h2>
            <div v-if="localFolders.length === 0" class="no-folders">
              Žiadne dostupné priečinky.
            </div>
            <div v-else class="folders-list">
              <div
                class="folder-row"
                v-for="(folder, idx) in localFolders"
                :key="folder.name"
              >
                <input
                  type="checkbox"
                  v-model="localFolders[idx].checked"
                  :id="'folder-' + idx"
                />
                <label :for="'folder-' + idx">{{ folder.name }}</label>
              </div>
            </div>
            <div class="dialog-actions">
              <button @click="$emit('close')" class="cancel">Zrušiť</button>
              <button
                @click="confirm"
                :disabled="!hasSelected || loading"
                class="confirm"
              >
                Pridať
              </button>
            </div>
            <div v-if="error" class="error">{{ error }}</div>
            <div v-if="success" class="success">{{ success }}</div>
          </div>
        </div>
      </div>
      <TheMenu
        class="fake-menu"
        style="margin: 0 1em; opacity: 0; z-index: -1; pointer-events: none"
      />
    </div>
  </teleport>
</template>

<script>
import TheMenu from "@/components/Menu/TheMenu.vue";
import axios from "axios";
export default {
  components: { TheMenu },
  props: {
    item: { type: Object, required: true },
    folders: { type: Array, required: true },
  },
  data() {
    return {
      loading: false,
      error: "",
      success: "",
      localFolders: [],
    };
  },
  computed: {
    hasSelected() {
      return this.localFolders.some((f) => f.checked);
    },
  },
  watch: {
    folders: {
      handler(val) {
        this.localFolders = val.map((f) => ({
          name: f.name,
          checked: f.checked || false,
        }));
      },
      immediate: true,
      deep: true,
    },
  },
  methods: {
    async confirm() {
      this.loading = true;
      this.error = "";
      try {
        await Promise.all(
          this.localFolders.map((f) => {
            const url = `${this.$store.state.api}/folder/${
              f.checked ? "add.php" : "remove.php"
            }`;
            const form = new FormData();
            form.append("id", this.item.id);
            form.append("name", f.name);
            return axios.post(url, form);
          })
        );
        this.$emit("done");
        this.$emit("close");
      } catch {
        this.error = "Chyba pri aktualizácii priečinkov.";
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>

<style scoped lang="scss">
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.36);
  z-index: 2000;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1.5em 1em;
  box-sizing: border-box;
}

.modal-overlay {
  width: 70%;
  max-width: 650px;
  min-height: 380px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.base-modal {
  background: #fff;
  border-radius: 32px;
  border: 4px solid #fef35a;
  box-shadow: 0 4px 40px rgba(33, 34, 26, 0.13);
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.modal-scroll {
  overflow-y: auto;
  padding: 2.2em 2.5em;
}

.folders-list {
  margin: 1.3em 0 2.1em 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1em 2em;
  max-height: 260px; // výšku uprav podľa potreby
  overflow-y: auto;
  align-items: start;
  scrollbar-width: thin;
  scrollbar-color: #fef35a #ececec;
}

.folder-row {
  display: flex;
  align-items: center;
  gap: 1em;
  font-size: 1.18em;
}

.folder-row input[type="checkbox"] {
  width: 22px;
  height: 22px;
  accent-color: #fef35a;
  margin-right: 0.25em;
}

.folder-row label {
  cursor: pointer;
  font-size: 1em;
  font-family: inherit;
}

.dialog-actions {
  margin-top: 2.2em;
  display: flex;
  gap: 1.1em;
  justify-content: flex-end;
}

.cancel,
.confirm {
  padding: 0.6em 2em;
  border-radius: 16px;
  border: none;
  font-size: 1.13em;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  font-family: inherit;
  transition: background 0.17s, color 0.12s, box-shadow 0.12s;
}

.confirm {
  background: #fef35a;
  font-weight: 700;
  color: #222;
}

.confirm:disabled {
  opacity: 0.7;
}

.cancel {
  background: #eee;
  color: #555;
}

h2 {
  font-size: 2em;
  margin-bottom: 1.3em;
  text-align: left;
  font-family: "Bahnschrift", "Arial", sans-serif;
}

.error {
  color: #a70000;
  margin-top: 1em;
}

.success {
  color: #237c07;
  margin-top: 1em;
}

.no-folders {
  color: #777;
  margin: 1.4em 0 2em;
}

@media (max-width: 900px) {
  .modal-overlay {
    width: 96%;
    max-width: 98vw;
  }

  .modal-scroll {
    padding: 1.2em 1em;
  }

  .folders-list {
    grid-template-columns: 1fr;
    max-height: 180px;
  }
}
</style>
