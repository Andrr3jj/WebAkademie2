<template>
  <div class="folder-wrapper">
    <!-- Autori -->
    <div class="author-list" v-if="authors && authors.length">
      <div v-for="author in authors" :key="author.name" class="author-tile">
        <strong
          :class="{
            active: selectedAuthor === author.name && !showInactiveOnly,
            pressed: selectedAuthor === author.name && !showInactiveOnly,
          }"
          @click.stop="
            $emit('select-author', { name: author.name, inactiveOnly: false })
          "
          style="cursor: pointer"
        >
          {{ author.name }}: {{ author.total }}
        </strong>
        <strong
          class="inactive"
          :class="{
            active: selectedAuthor === author.name && showInactiveOnly,
            pressed: selectedAuthor === author.name && showInactiveOnly,
          }"
          @click.stop="
            $emit('select-author', { name: author.name, inactiveOnly: true })
          "
          style="margin-left: 2px; cursor: pointer"
        >
          (Neaktívnych: {{ author.inactive }})
        </strong>
      </div>
    </div>

    <div
      class="folder-list"
      :class="{
        'grid-wrap': showHover && showAllFolders,
        'disabled-hover': !showHover,
      }"
      @mouseenter="onMouseEnter"
      @mouseleave="onMouseLeave"
    >
      <template
        v-for="(folder, i) in paddedFolders"
        :key="
          folder
            ? folder.isNew
              ? 'new-folder-' + i
              : folder.name
            : 'empty-' + i
        "
      >
        <div
          v-if="folder"
          class="folder-item"
          :class="{
            active: folder.name === selected,
            highlight: folder.name === 'Všetko',
            'new-folder': folder.isNew,
          }"
          @click="$emit('select', folder.name)"
          @contextmenu.prevent="openContextMenu($event, folder)"
        >
          <svg
            v-if="folder.name === selected"
            xmlns="http://www.w3.org/2000/svg"
            width="80"
            height="60"
            viewBox="0 0 122 92"
            fill="none"
            class="folder-icon"
          >
            <path
              d="M114.618 92C118.72 92 122 88.5974 122 84.5302V3.86257C122 1.72946 120.256 0 118.105 0H70.9596C68.8085 0 67.0645 1.72946 67.0645 3.86257V5.09922H20.9872C18.8361 5.09922 17.0921 6.82868 17.0921 8.9618V17.7232C17.0921 17.7232 81.5596 92 114.618 92Z"
              fill="#00913F"
            />
            <path
              d="M107.121 84.6228H113.782C114.212 84.6228 114.561 84.277 114.561 83.8505V12.8136C114.561 12.3872 114.212 12.0413 113.782 12.0413H25.2575C24.8274 12.0413 24.4786 12.3872 24.4786 12.8136V17.7224L107.121 84.6228Z"
              fill="white"
            />
            <path
              d="M-0.000236511 21.585V84.6228C-0.000236511 88.6965 3.32957 91.9999 7.43899 91.9999H114.561C110.453 91.9999 107.121 88.6972 107.121 84.6228V21.585C107.121 19.4512 105.377 17.7224 103.226 17.7224H3.89413C1.74306 17.7224 -0.000236511 19.4512 -0.000236511 21.585Z"
              fill="#90CA50"
            />
            <path
              d="M0.000484467 21.585V84.6228C0.000484467 88.6965 3.3303 91.9999 7.4397 91.9999H53.2617C46.9746 91.3624 40.7141 90.4723 34.5004 89.3298C28.1507 88.1623 21.4688 86.5313 16.9595 81.9484C13.9483 78.8874 12.2165 74.7688 11.3643 70.5768C10.5113 66.3847 10.4747 62.0771 10.4416 57.8009C10.3373 44.4409 10.233 31.0824 10.128 17.7224H3.8963C1.74522 17.7224 0.00120544 19.4512 0.00120544 21.585H0.000484467Z"
              fill="#00913F"
            />
            <path
              d="M101.162 37.5987V26.3547C101.162 25.1402 100.169 24.1553 98.9442 24.1553H71.9043C71.3865 24.1553 71.3117 24.8963 71.8195 24.9982L78.5791 26.3533C86.6044 27.9622 93.8049 32.2185 99.0391 38.3739C99.7611 39.2233 101.161 38.7091 101.161 37.598Z"
              fill="#FFE45F"
            />
          </svg>
          <svg
            v-else
            xmlns="http://www.w3.org/2000/svg"
            width="80"
            height="60"
            viewBox="0 0 122 92"
            fill="none"
            class="folder-icon"
          >
            <path
              d="M114.618 92C118.72 92 122 88.5974 122 84.5302V3.86257C122 1.72946 120.256 0 118.105 0H70.9596C68.8085 0 67.0645 1.72946 67.0645 3.86257V5.09922H20.9872C18.8361 5.09922 17.0921 6.82868 17.0921 8.9618V17.7232C17.0921 17.7232 81.5596 92 114.618 92Z"
              fill="#F4AB2A"
            />
            <path
              d="M107.121 84.6228H113.782C114.212 84.6228 114.561 84.2769 114.561 83.8505V12.8136C114.561 12.3872 114.212 12.0413 113.782 12.0413H25.2575C24.8274 12.0413 24.4786 12.3872 24.4786 12.8136V17.7224L107.121 84.6228Z"
              fill="white"
            />
            <path
              d="M-0.000221252 21.585V84.6228C-0.000221252 88.6965 3.32959 91.9999 7.439 91.9999H114.561C110.453 91.9999 107.121 88.6972 107.121 84.6228V21.585C107.121 19.4512 105.377 17.7224 103.226 17.7224H3.89415C1.74307 17.7224 -0.000938416 19.4512 -0.000938416 21.585H-0.000221252Z"
              fill="#F9C32D"
            />
            <path
              d="M0.000484467 21.585V84.6228C0.000484467 88.6965 3.3303 91.9999 7.4397 91.9999H53.2617C46.9746 91.3624 40.7141 90.4723 34.5004 89.3298C28.1507 88.1623 21.4688 86.5313 16.9595 81.9484C13.9483 78.8874 12.2165 74.7688 11.3643 70.5768C10.5113 66.3847 10.4747 62.0771 10.4416 57.8009C10.3373 44.4409 10.233 31.0824 10.128 17.7224H3.8963C1.74522 17.7224 0.00120544 19.4512 0.00120544 21.585H0.000484467Z"
              fill="#F4AB2A"
            />
            <path
              d="M101.162 37.5987V26.3547C101.162 25.1402 100.169 24.1553 98.9442 24.1553H71.9043C71.3865 24.1553 71.3117 24.8963 71.8195 24.9982L78.5791 26.3533C86.6044 27.9622 93.8049 32.2185 99.0391 38.3739C99.7611 39.2233 101.161 38.7091 101.161 37.598Z"
              fill="#FFE45F"
            />
          </svg>
          <div v-if="!folder.isNew" class="folder-count">
            {{ folder.count }}
          </div>
          <div class="folder-name">
            <template v-if="renamingFolder === folder">
              <input
                class="folder-rename-input"
                :value="folder.name"
                @keyup.enter="confirmRename(folder, $event)"
                @blur="confirmRename(folder, $event)"
                @keyup.esc="cancelRename(folder)"
                autofocus
                style="font-size: 16px; text-align: center"
              />
            </template>
            <template v-else-if="folder.isNew">
              <input
                v-model="folder.name"
                @keyup.enter="confirmFolder(folder, $event)"
                @blur="confirmFolder(folder, $event)"
                @keyup.esc="$emit('cancel-new-folder', folder)"
                placeholder="Nový priečinok"
                ref="newFolderInput"
                autofocus
                style="font-size: 16px; text-align: center"
              />
            </template>
            <template v-else>
              {{ folder.name }}
            </template>
          </div>
        </div>
        <div v-else class="folder-item empty"></div>
      </template>
    </div>

    <div class="add-button-wrapper">
      <div class="button vytvorenie-zapisu">
        <router-link to="/admin/vytvorenie-zapisu">Pridať zápis</router-link>
      </div>
    </div>

    <teleport to="body">
      <div
        v-if="contextMenu.visible"
        class="context-menu"
        :style="{
          position: 'fixed',
          top: contextMenu.y + 'px',
          left: contextMenu.x + 'px',
          zIndex: 9999,
        }"
        @mousedown.stop
      >
        <div class="menu-item" @click="renameFolder">Premenovať</div>
        <div class="menu-item" @click="deleteFolder">Vymazať</div>
      </div>
    </teleport>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "FolderList",
  props: {
    folders: {
      type: Array,
      required: true,
    },
    selected: {
      type: String,
      required: true,
    },
    authors: Array,
    selectedAuthor: String,
    creatingNewFolder: Boolean,
  },
  emits: [
    "select",
    "context",
    "select-author",
    "confirm-new-folder",
    "cancel-new-folder",
    "rename-folder",
    "delete-folder",
    "confirm-rename-folder",
    "cancel-rename-folder",
    "close-global-context",
  ],
  data() {
    return {
      newFolderName: "",
      isSaving: false,
      showAllFolders: false,
      contextMenu: {
        visible: false,
        x: 0,
        y: 0,
        folder: null,
      },
      renamingFolder: null,
      originalFolderName: "",
      hoverTimeout: null,
    };
  },
  methods: {
    normalizeStringArray(raw) {
      let data = raw;
      if (typeof raw === "string") {
        try {
          data = JSON.parse(raw);
        } catch {
          const parts = raw
            .split(",")
            .map((s) => s.trim())
            .filter(Boolean);
          if (parts.length) return parts.map(String);
          return [];
        }
      }
      if (Array.isArray(data)) {
        return data.map(String);
      }
      if (data && typeof data === "object") {
        const keys = Object.keys(data).sort((a, b) => Number(a) - Number(b));
        return keys.map((k) => String(data[k]));
      }
      return [];
    },
    onMouseEnter() {
      if (this.hoverTimeout) {
        clearTimeout(this.hoverTimeout);
        this.hoverTimeout = null;
      }
      this.showAllFolders = true;
    },
    onMouseLeave() {
      this.hoverTimeout = setTimeout(() => {
        this.showAllFolders = false;
        this.hoverTimeout = null;
      }, 150);
    },
    async confirmFolder(folder, event) {
      if (this.isSaving) return;
      this.isSaving = true;
      if (event && event.type === "keyup" && event.key === "Enter") {
        event.target.blur();
      }
      const trimmedName = folder.name?.trim();
      if (!trimmedName) {
        this.$emit("cancel-new-folder", folder);
        this.isSaving = false;
        return;
      }
      folder.name = trimmedName;
      this.$emit("confirm-new-folder", folder, event);
      this.isSaving = false;
    },
    cancelNewFolder(folder) {
      this.$emit("cancel-new-folder", folder);
    },
    startRenameFolder(folder) {
      this.renamingFolder = folder;
      this.originalFolderName = folder.name;
      this.closeContextMenu();
      this.$nextTick(() => {
        const input = this.$el.querySelector(".folder-rename-input");
        if (input) input.focus();
      });
    },
    async confirmRename(folder, event) {
      if (this.isSaving) return;
      this.isSaving = true;
      const newName = event.target.value.trim();
      if (!newName) {
        folder.name = this.originalFolderName;
        this.renamingFolder = null;
        this.isSaving = false;
        return;
      }
      if (newName.toLowerCase() === this.originalFolderName.toLowerCase()) {
        this.renamingFolder = null;
        this.isSaving = false;
        return;
      }
      try {
        const response = await axios.get(
          this.$store.state.api + "/folder/list.php"
        );
        if (response.data.status === "Request succesfull") {
          const existingFolders = this.normalizeStringArray(response.data.data);
          const nameExists = existingFolders.some(
            (name) => name.trim().toLowerCase() === newName.toLowerCase()
          );
          if (nameExists) {
            this.$store.state.SnackBarText =
              "Priečinok s týmto názvom už existuje.";
            folder.name = this.originalFolderName;
            this.renamingFolder = null;
            this.isSaving = false;
            return;
          }
        } else {
          this.$store.state.SnackBarText =
            "Nepodarilo sa overiť existenciu priečinka. Skús znova neskôr.";
          folder.name = this.originalFolderName;
          this.renamingFolder = null;
          this.isSaving = false;
          return;
        }
      } catch (error) {
        this.$store.state.SnackBarText =
          "Chyba pri overovaní priečinka. Skús znova neskôr.";
        folder.name = this.originalFolderName;
        this.renamingFolder = null;
        this.isSaving = false;
        return;
      }
      folder.name = newName;
      this.renamingFolder = null;
      this.$emit("confirm-rename-folder", folder, newName);
      this.isSaving = false;
    },
    handleMenuEsc(e) {
      if (e.key === "Escape") this.closeContextMenu();
    },
    openContextMenu(event, folder) {
      event.preventDefault();
      this.$emit("close-global-context");
      let x = event.clientX;
      let y = event.clientY;
      const menuWidth = 190;
      const menuHeight = 110;
      if (x + menuWidth > window.innerWidth) {
        x = window.innerWidth - menuWidth - 8;
      }
      if (y + menuHeight > window.innerHeight) {
        y = window.innerHeight - menuHeight - 8;
      }
      if (x < 0) x = 0;
      if (y < 0) y = 0;
      this.contextMenu = {
        visible: true,
        x,
        y,
        folder,
      };
      document.removeEventListener("keydown", this.handleMenuEsc);
      document.removeEventListener("mousedown", this.closeContextMenu);
      document.addEventListener("keydown", this.handleMenuEsc);
      document.addEventListener("mousedown", this.closeContextMenu, {
        once: true,
      });
    },
    closeContextMenu() {
      this.contextMenu.visible = false;
      document.removeEventListener("keydown", this.handleMenuEsc);
    },
    renameFolder() {
      this.startRenameFolder(this.contextMenu.folder);
      this.closeContextMenu();
    },
    deleteFolder() {
      this.$emit("delete-folder", this.contextMenu.folder);
      this.closeContextMenu();
    },
  },
  computed: {
    orderedFolders() {
      return this.folders;
    },
    visibleFolders() {
      return this.orderedFolders.slice(0, 8);
    },
    paddedFolders() {
      let list = this.showAllFolders
        ? this.orderedFolders
        : this.visibleFolders;
      if (list.length < 8) {
        return [...list, ...Array(8 - list.length).fill(null)];
      }
      return list;
    },
    showHover() {
      return this.orderedFolders.length > 8;
    },
  },
};
</script>

<style scoped lang="scss">
.folder-wrapper {
  display: flex;
  flex-direction: column;
  gap: 2em;
  align-items: center;
}

.author-list {
  margin-top: 2em;
  display: flex;
  gap: 2em;
  justify-content: center;
  flex-wrap: wrap;
  font-size: 1.1em;
}

.author-tile {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  text-align: center;
  color: black;

  strong {
    font-size: 24px;
  }
}

.author-tile.active {
  text-decoration: underline;
  font-weight: bold;
}

.author-tile .inactive {
  font-size: 15px;
  color: #000;
  font-weight: 400;
}

.author-tile strong {
  transition: background 0.14s, color 0.14s, text-decoration 0.15s;
  padding: 0.08em 0.45em;
  border-radius: 0.7em;
}

.author-tile strong.active {
  text-decoration: underline;
  font-weight: 700;
  color: #000; // môžeš zmeniť na svoju farbu
}
.author-tile strong.inactive.active {
  text-decoration: underline;
  font-weight: 700;
  color: #000;
}

.folder-list {
  display: flex;
  gap: 1em;
  flex-wrap: nowrap;
  /* aby boli v jednom riadku */
  overflow-x: auto;
  /* horizontálny scroll ak treba */
  max-width: 100%;
  width: 80%;
  position: relative;
  min-height: 130px;
  transition: all 0.3s ease;
  align-items: center;
  justify-content: center;
}

/* po hover expandujeme na grid */

.folder-list.grid-wrap {
  display: grid !important;
  grid-template-columns: repeat(8, 1fr);
  gap: 1em;
  width: 80%;
  overflow-x: unset;
  padding: 1em 0;
  justify-items: center;
}

.folder-list:not(.grid-wrap) {
  display: flex;
  gap: 1em;
  flex-wrap: nowrap;
  overflow-x: auto;
}

.folder-item.empty {
  opacity: 0;
  pointer-events: none;
  height: 1px;
}

.folder-list.disabled-hover .folder-item:hover {
  transform: none !important;
}

.folder-item.empty {
  opacity: 0;
  pointer-events: none;
  height: 1px;
}

.folder-item {
  position: relative;
  cursor: pointer;
  transition: transform 0.2s ease;
  height: max-content;
}

.folder-item:hover {
  transform: scale(1.1);
}

.folder-icon {
  width: 80px;
  height: auto;
}

.folder-count {
  position: absolute;
  top: 41%;
  left: 45%;
  transform: translate(-50%, -50%);
  font-weight: bold;
  font-size: 24px;
  line-height: 1;
  pointer-events: none;
}

.folder-name {
  font-size: 20px;
  font-weight: 500;
  text-align: center;
  margin-top: 0.2em;
}

.button {
  font-size: 15px;
  font-family: "Adumu", sans-serif;
  margin-right: 1em;
  padding: 1em;
}

.context-menu {
  position: fixed;
  z-index: 9999;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 20px 0 rgba(80, 80, 90, 0.13),
    0 1.5px 7px 0 rgba(120, 120, 120, 0.12);
  border: 1px solid #eee;
  font-size: 1rem;
  user-select: none;
  min-width: 190px;
  transition: opacity 0.18s;
  opacity: 1;
  animation: appearMenu 0.17s cubic-bezier(0.42, 0, 0.6, 1.42);

  .menu-item {
    padding: 0.7em 1.5em;
    cursor: pointer;
    border-radius: 8px;
    text-align: center;
    font-weight: 500;
    transition: background 0.18s, color 0.12s;

    &:hover {
      background: #fcf59b55;
      color: #28281a;
    }
  }
}

.add-button-wrapper {
  width: 125px;
  margin-top: 1em;
  margin-bottom: 1em;
}

.button.vytvorenie-zapisu {
  background-color: #fef35a;
  color: #000000;
  font-family: "Adumu", sans-serif;
  font-weight: 700;
  font-size: 15px;
  line-height: 1.2;
  text-align: center;
  padding: 20px 0;
  border-radius: 24px;
  box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
  cursor: pointer;
  user-select: none;
  display: flex;
  justify-content: center;
  align-items: center;
  white-space: pre-line;

  transition: box-shadow 0.2s ease;

  &:hover {
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.25);
  }
}
</style>
