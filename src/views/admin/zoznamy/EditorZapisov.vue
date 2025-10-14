<template>
  <section :id="$route.name">
    <div class="scroll">
      <h1>Administratívny systém</h1>
      <h5>Zoznam vytvorených číselných zápisov</h5>

      <div class="line horizontal w-80"></div>

      <!-- Priečinky a autori -->
      <div
        ref="folderContext"
        @contextmenu.prevent="openGlobalContextMenu"
        style="position: relative"
      >
        <FolderList
          :folders="folders"
          :selected="selectedFolder"
          :authors="authors"
          :selectedAuthor="selectedAuthor"
          :creatingNewFolder="creatingNewFolder"
          :renamingFolder="renamingFolder"
          @select="onSelectFolder"
          @select-author="selectAuthor"
          @confirm-new-folder="finalizeNewFolder"
          @cancel-new-folder="cancelNewFolder"
          @rename-folder="startRenameFolder"
          @delete-folder="deleteFolder"
          @close-global-context="closeGlobalContextMenu"
        />
      </div>

      <ConfirmDialog
        :visible="showDeleteDialog"
        title="Vymazať priečinok?"
        :message="
          folderToDelete
            ? `Naozaj chcete vymazať priečinok „${folderToDelete.name}“?`
            : ''
        "
        @cancel="showDeleteDialog = false"
        @confirm="confirmDeleteFolder"
      />

      <!-- Kontextové menu -->
      <Teleport to="body">
        <div
          v-if="globalContextMenu.visible"
          class="context-menu"
          :style="{
            position: 'fixed',
            top: globalContextMenu.y + 'px',
            left: globalContextMenu.x + 'px',
          }"
        >
          <div class="menu-item" @click="addFolderInline">
            Vytvoriť priečinok
          </div>
        </div>
      </Teleport>

      <div class="line horizontal w-80"></div>

      <table>
        <thead>
          <tr>
            <th style="width: 30%">
              Názov pesničky:
              <SortArrow
                column="nazov"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 15%">
              Stupnica:
              <SortArrow
                column="stupnica"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 20%; text-align: center">
              Autor:
              <SortArrow
                column="autor"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 15%">
              Vytvorené:
              <SortArrow
                column="datum_vytvorenia"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 10%">
              Aktívne:
              <SortArrow
                column="stav_aktivne"
                :sortKey="sortKey"
                :sortDirection="sortDirection"
                @update:sort="updateSort"
              />
            </th>
            <th style="width: 10%"></th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(item, index) in sortedData"
            :key="item.id || index"
            @contextmenu.prevent="openRowContextMenu($event, item)"
          >
            <td>{{ item.nazov }}</td>
            <td>{{ item.stupnica }}</td>
            <td style="text-align: center">{{ item.autor }}</td>
            <td>{{ formatDatum(item.timestamp) }}</td>
            <td style="text-align: center">
              <img
                :src="
                  item.stav_aktivne
                    ? require('@/assets/images/icons/vybavena-objednavka.png')
                    : require('@/assets/images/icons/nevybavena-objednavka.png')
                "
                alt="stav"
                style="width: 18px; height: 18px"
              />
            </td>
            <td class="button-container">
              <div @click="upravenieZapisu(item.id)" class="button">
                <a>Upraviť</a>
              </div>
              <div
                :class="{
                  'not-have-permission':
                    !this.$store.state.userRoles?.roles?.includes(
                      'record_delete'
                    ),
                }"
                @click="vymazanieZapisu(item.id)"
                class="button red-button"
                title="Vymazať"
              >
                <a>x</a>
              </div>
            </td>
          </tr>
          <tr v-if="sortedData.length === 0">
            <td
              colspan="6"
              style="text-align: center; vertical-align: middle; height: 200px"
            >
              {{ noResultsMessage }}
            </td>
          </tr>
        </tbody>
      </table>

      <Teleport to="body">
        <div
          v-if="rowContextMenu.visible"
          class="context-menu"
          :style="{
            position: 'fixed',
            top: rowContextMenu.y + 'px',
            left: rowContextMenu.x + 'px',
            zIndex: 3000,
          }"
          @mousedown.stop
        >
          <div class="menu-item" @click="addToFolder(rowContextMenu.item)">
            Pridať do priečinka
          </div>
          <div class="menu-item" @click="removeFromFolder(rowContextMenu.item)">
            Odstrániť z priečinka
          </div>
        </div>
      </Teleport>

      <AddToFolderDialog
        v-if="showAddToFolderModal"
        :item="addToFolderItem"
        :folders="addToFolderFolders"
        @close="showAddToFolderModal = false"
        @confirm="handleAddToFolderConfirm"
        @done="onAddToFolderDone"
      />

      <div class="search">
        <img src="@/assets/images/icons/hladanie.svg" alt="Vyhladanie" />
        <input
          @input="oneskoreneVyhladavanie"
          type="text"
          placeholder="Vyhľadať pieseň"
          v-model="search"
        />
      </div>
    </div>
  </section>
</template>

<script>
import SortArrow from "@/components/Functionality/SortArrow.vue";
import AddToFolderDialog from "@/components/Zapisy/AddToFolderDialog.vue";
import ConfirmDialog from "@/components/Zapisy/ConfirmDialog.vue";
import FolderList from "@/components/Zapisy/FolderList.vue";
import axios from "axios";

export default {
  components: { FolderList, ConfirmDialog, AddToFolderDialog, SortArrow },
  mounted() {
    window.scrollTo(0, 0);
    this.loadFolders();
    this.nacitajZapisy();
    this.ziskajPrihlasenehoPouzivatela();
  },
  data() {
    return {
      data: [],
      dataVyhladavanie: [],
      search: "",
      folders: [],
      creatingNewFolder: false,
      editingFolder: null,
      selectedFolder: "Všetko",
      authors: [],
      selectedAuthor: "",
      menoPouzivatela: "",
      globalContextMenu: {
        visible: false,
        x: 0,
        y: 0,
      },
      allDataRaw: [],
      renamingFolder: null,
      originalFolderName: "",
      showDeleteDialog: false,
      folderToDelete: null,
      rowContextMenu: {
        visible: false,
        x: 0,
        y: 0,
        item: null,
      },
      showAddToFolderModal: false,
      addToFolderItem: null,
      addToFolderFolders: [],
      sortKey: "",
      sortDirection: "",
      showInactiveOnly: false,
      timer: null,
    };
  },
  computed: {
    displayedData() {
      const normalize = (str) =>
        String(str || "")
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .trim()
          .toLowerCase();

      let list = this.data.slice();

      if (this.selectedAuthor) {
        list = list.filter(
          (item) =>
            normalize(item.autor) === normalize(this.selectedAuthor) &&
            (!this.showInactiveOnly || item.stav_aktivne === false)
        );
      }
      const q = normalize(this.search);
      if (q) {
        list = list.filter((item) => normalize(item.nazov).includes(q));
      }

      return list;
    },

    sortedData() {
      const base = this.displayedData;

      if (!this.sortKey && !this.sortDirection) {
        return [...base].sort((a, b) => Number(b.id) - Number(a.id));
      }

      const normalize = (val) =>
        String(val || "")
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .trim()
          .toLowerCase();

      return [...base].sort((a, b) => {
        let aVal, bVal;

        if (this.sortKey === "datum_vytvorenia") {
          // Tu použijeme Date
          aVal = a.timestamp ? new Date(a.timestamp) : new Date(0);
          bVal = b.timestamp ? new Date(b.timestamp) : new Date(0);
          return this.sortDirection === "asc" ? aVal - bVal : bVal - aVal;
        }

        if (this.sortKey === "stav_aktivne") {
          // Tu explicitne na číslo
          aVal = a.stav_aktivne ? 1 : 0;
          bVal = b.stav_aktivne ? 1 : 0;
          return this.sortDirection === "asc" ? aVal - bVal : bVal - aVal;
        }

        aVal = a[this.sortKey];
        bVal = b[this.sortKey];

        // Číselné porovnanie (aj "12,5" → 12.5)
        const aNum = parseFloat(String(aVal).replace(",", "."));
        const bNum = parseFloat(String(bVal).replace(",", "."));
        if (!isNaN(aNum) && !isNaN(bNum)) {
          return this.sortDirection === "asc" ? aNum - bNum : bNum - aNum;
        }

        // Textové porovnanie
        aVal = normalize(aVal);
        bVal = normalize(bVal);
        const cmp = aVal.localeCompare(bVal, "sk", {
          sensitivity: "base",
          numeric: true,
        });

        return this.sortDirection === "asc" ? cmp : -cmp;
      });
    },

    noResultsMessage() {
      const q = this.search.trim();
      if (q) {
        return `Nebola nájdená žiadna pieseň pre “${q}”`;
      }
      if (this.selectedAuthor) {
        return `Autor “${this.selectedAuthor}” nemá žiadne piesne v tomto priečinku`;
      }
      return `V tomto priečinku nie sú žiadne záznamy`;
    },
  },
  methods: {
    normalizeIds(raw) {
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
        return data.map((x) => String(x));
      }

      if (data && typeof data === "object") {
        const keys = Object.keys(data).sort((a, b) => Number(a) - Number(b));
        return keys.map((k) => String(data[k]));
      }

      return [];
    },

    formatDatum(timestamp) {
      if (!timestamp) return "—";

      const d = new Date(String(timestamp).replace(" ", "T"));
      if (isNaN(d.getTime())) return timestamp;

      const den = d.getDate();
      const mesiac = d.getMonth() + 1;
      const rok = d.getFullYear();
      const hodina = String(d.getHours()).padStart(2, "0");
      const minuta = String(d.getMinutes()).padStart(2, "0");

      return `${den}. ${mesiac}. ${rok}, ${hodina}:${minuta}`;
    },

    updateSort({ key, direction }) {
      this.sortKey = key;
      this.sortDirection = direction;
    },

    async onAddToFolderDone() {
      await this.loadFolders();
      if (this.selectedFolder === "Všetko") {
        this.data = this.allDataRaw.slice();
      } else {
        await this.selectFolder(this.selectedFolder);
      }
    },

    async ziskajPrihlasenehoPouzivatela() {
      try {
        const res = await axios.get(
          "https://heligonkovaakademia.sk/api/user/info/getAdditionalInformation.php"
        );
        if (res.data.status === "Request succesfull") {
          const user = res.data.data;
          this.menoPouzivatela = `${user.name} ${user.surname}`.trim();
        }
      } catch (err) {
        console.error("Chyba pri nacitani pouzivatela", err);
      }
    },

    async handleAddToFolderConfirm(updated) {
      await Promise.all(
        updated.map((f) => {
          const ep = f.checked ? "add.php" : "remove.php";
          const form = new FormData();
          form.append("id", this.addToFolderItem.id);
          form.append("name", f.name);
          return axios.post(`${this.$store.state.api}/folder/${ep}`, form);
        })
      );
      await this.loadFolders();
      if (this.selectedFolder !== "Všetko")
        await this.selectFolder(this.selectedFolder);
      else this.data = this.allDataRaw.slice();

      this.showAddToFolderModal = false;
      this.$store.state.SnackBarText = "Hotovo";
    },

    async removeFromFolder(item) {
      const folderName = this.selectedFolder;
      if (folderName === "Všetko") {
        this.$store.state.SnackBarText =
          "Vyber najprv konkrétny priečinok, z ktorého chceš odstrániť pieseň.";
        this.closeRowContextMenu();
        return;
      }

      try {
        const form = new FormData();
        form.append("id", item.id);
        form.append("name", folderName);

        const res = await fetch(`${this.$store.state.api}/folder/remove.php`, {
          method: "POST",
          body: form,
        });
        const { status, message } = await res.json();
        if (status !== "Request succesfull") {
          throw new Error(message || "Unknown error");
        }

        await this.loadFolders();
        await this.selectFolder(folderName);
      } catch (err) {
        console.error("Chyba pri remove.php:", err);
        this.$store.state.SnackBarText =
          "Nepodarilo sa odstrániť pieseň z priečinka.";
        this.closeRowContextMenu();
      }
    },

    onSelectFolder(folderName) {
      this.closeGlobalContextMenu();
      this.selectFolder(folderName);
    },

    async selectFolder(folderName) {
      this.selectedFolder = folderName;
      this.selectedAuthor = "";
      this.search = "";

      if (folderName === "Všetko") {
        this.data = this.allDataRaw.slice();
        return;
      }

      try {
        const res = await axios.get(
          `${this.$store.state.api}/folder/list.php`,
          { params: { name: folderName } }
        );

        if (res.data?.status === "Request succesfull") {
          const ids = this.normalizeIds(res.data.data);
          this.data = this.allDataRaw.filter((item) =>
            ids.includes(String(item.id))
          );
        } else {
          this.data = [];
        }
      } catch (err) {
        console.error("Chyba pri načítaní priečinka:", err);
        this.data = [];
      }
    },

    openGlobalContextMenu(event) {
      event.preventDefault();

      if (event.target.closest(".folder-item")) return;

      let x = event.clientX;
      let y = event.clientY;
      const menuWidth = 270;
      const menuHeight = 54;
      if (x + menuWidth > window.innerWidth)
        x = window.innerWidth - menuWidth - 6;
      if (y + menuHeight > window.innerHeight)
        y = window.innerHeight - menuHeight - 6;

      this.globalContextMenu = {
        visible: true,
        x,
        y,
      };

      document.addEventListener("keydown", this.handleContextMenuEsc);
      setTimeout(() => {
        document.addEventListener("click", this.closeGlobalContextMenu, {
          once: true,
        });
      }, 0);
    },

    closeGlobalContextMenu() {
      this.globalContextMenu.visible = false;
      document.removeEventListener("keydown", this.handleContextMenuEsc);
    },

    handleContextMenuEsc(e) {
      if (e.key === "Escape") this.closeGlobalContextMenu();
    },

    addFolderInline() {
      this.closeGlobalContextMenu();

      const hasNewFolder = this.folders.some((f) => f.isNew);

      if (hasNewFolder) {
        this.$nextTick(() => {
          const input = document.querySelector(
            'input[placeholder="Nový priečinok"]'
          );
          if (input) input.focus();
        });
        return;
      }

      const newFolder = { name: "", count: 0, isNew: true };
      this.folders.unshift(newFolder);

      this.$nextTick(() => {
        const input = document.querySelector(
          'input[placeholder="Nový priečinok"]'
        );
        if (input) input.focus();
      });
    },

    async finalizeNewFolder(folder) {
      const trimmedName = folder.name?.trim();
      if (!trimmedName) {
        this.cancelNewFolder(folder);
        return;
      }
      try {
        const formData = new FormData();
        formData.append("name", trimmedName);
        formData.append("type", "noty");

        const createResponse = await axios.post(
          this.$store.state.api + "/folder/create.php",
          formData
        );

        const status = (createResponse.data.status || "").toLowerCase();
        const msg = (createResponse.data.message || "").toLowerCase();

        if (status.includes("succes")) {
          this.$store.state.SnackBarText = `Priečinok "${trimmedName}" bol vytvorený.`;
          this.creatingNewFolder = false;
          this.editingFolder = null;

          await this.loadFolders();

          // Prepnúť sa do nového priečinka a vyčistiť filtre
          this.selectedFolder = trimmedName;
          this.search = "";
          this.selectedAuthor = "";
          this.dataVyhladavanie = [];

          // Načítať obsah nového priečinka
          await this.selectFolder(trimmedName);
        } else if (msg.includes("existuje") || msg.includes("already exists")) {
          this.$store.state.SnackBarText =
            "Priečinok s týmto názvom už existuje.";
          this.cancelNewFolder(folder);
        } else {
          this.$store.state.SnackBarText =
            "Nepodarilo sa vytvoriť priečinok: " +
            (createResponse.data.message || "Neznáma chyba.");
          this.cancelNewFolder(folder);
        }
      } catch (error) {
        this.$store.state.SnackBarText =
          "Chyba pri vytváraní priečinka. Skús neskôr.";
        this.cancelNewFolder(folder);
      }
    },

    cancelNewFolder(folder) {
      this.folders = this.folders.filter((f) => f !== folder);
      this.creatingNewFolder = false;
      this.editingFolder = null;
    },

    async loadFolders() {
      try {
        const allNotesRes = await axios.get(
          this.$store.state.api + "/noty/list.php"
        );
        const allNotes = Array.isArray(allNotesRes.data?.data)
          ? allNotesRes.data.data
          : [];
        const totalCount = allNotes.length;

        const foldersRes = await axios.get(
          this.$store.state.api + "/folder/list.php"
        );
        const folderNames = Array.isArray(foldersRes.data?.data)
          ? foldersRes.data.data
          : [];

        const folderPromises = folderNames.map(async (name) => {
          const r = await axios.get(
            this.$store.state.api +
              `/folder/list.php?name=${encodeURIComponent(name)}`
          );
          const ids = this.normalizeIds(r.data?.data);
          return { name, count: ids.length };
        });

        const foldersArr = await Promise.all(folderPromises);
        this.folders = [{ name: "Všetko", count: totalCount }, ...foldersArr];
      } catch (error) {
        console.error("Chyba pri načítaní priečinkov alebo zápisov:", error);
        this.folders = [{ name: "Všetko", count: 0 }];
      }
    },

    async nacitajZapisy() {
      try {
        const response = await axios.get(
          this.$store.state.api + "/noty/list.php"
        );
        if (response.data.status === "Request succesfull") {
          const allData = response.data.data;
          this.allDataRaw = allData;

          const autorMap = {};
          allData.forEach((item) => {
            const name = (item.autor || "").trim();
            if (!autorMap[name])
              autorMap[name] = { name, total: 0, inactive: 0 };
            autorMap[name].total++;
            if (item.stav_aktivne === false) autorMap[name].inactive++;
          });
          this.authors = Object.values(autorMap);

          // Všetci vidia všetko
          this.data = allData;
          this.selectedAuthor = "";
        } else {
          this.data = [];
          this.authors = [];
        }
      } catch (error) {
        console.error("Chyba pri načítaní zápisov:", error);
        this.data = [];
        this.authors = [];
      }
    },

    upravenieZapisu(id) {
      this.$router.push({
        path: "/admin/vytvorenie-zapisu",
        query: { id: id },
      });
    },

    vymazanieZapisu(id) {
      const jePotvrdene = confirm(
        "Ste si istý, že chcete vymazať tento zápis? \nPo vymazaní bude navždy stratený."
      );
      if (!jePotvrdene) {
        this.$store.state.SnackBarText = "Vymazanie zápisu bolo zrušené";
        return;
      }
      const FormData = require("form-data");
      let data = new FormData();
      let config = {
        method: "get",
        maxBodyLength: Infinity,
        url: this.$store.state.api + "/noty/delete.php?id=" + id,
        data: data,
      };
      axios
        .request(config)
        .then((response) => {
          if (response.data.status == "Request fullfiled") {
            this.$store.state.SnackBarText = "Vymazanie zápisu bolo úspešné";
            setTimeout(() => {
              this.data = [];
              this.nacitajZapisy();
            }, 200);
          } else {
            this.$store.state.SnackBarText = "Nepodarilo sa vymazať zápis";
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    vyhladavanieZapisu() {
      const normalize = (text) =>
        text
          ?.toLowerCase()
          .normalize("NFD")
          .replace(/[\u0300-\u036f]/g, "")
          .trim();

      const query = normalize(this.search);
      if (!query) {
        this.applyFolderAndAuthorFilter();
        return;
      }

      const base = this.getFolderAndAuthorFilteredData();

      this.dataVyhladavanie = base.filter((item) =>
        normalize(item.nazov).includes(query)
      );
    },

    getFolderAndAuthorFilteredData() {
      let arr = this.allDataRaw.slice();

      if (this.selectedFolder !== "Všetko") {
        arr = this.data.slice();
      }

      if (this.selectedAuthor) {
        arr = arr.filter(
          (item) => (item.autor || "").trim() === this.selectedAuthor
        );
      }

      return arr;
    },

    applyFolderAndAuthorFilter() {
      const base = this.getFolderAndAuthorFilteredData();
      this.dataVyhladavanie = base;
    },

    oneskoreneVyhladavanie() {
      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.vyhladavanieZapisu();
      }, 500);
    },

    selectAuthor({ name, inactiveOnly }) {
      // Ak klikneš znova na rovnaké, vypne filter
      if (
        this.selectedAuthor === name &&
        this.showInactiveOnly === inactiveOnly
      ) {
        this.selectedAuthor = "";
        this.showInactiveOnly = false;
      } else {
        this.selectedAuthor = name;
        this.showInactiveOnly = inactiveOnly;
      }
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

    confirmRename(folder, event) {
      const newName = event.target.value.trim();
      if (!newName) {
        folder.name = this.originalFolderName;
      } else {
        folder.name = newName;
      }
      this.renamingFolder = null;
    },

    cancelRename(folder) {
      folder.name = this.originalFolderName;
      this.renamingFolder = null;
    },

    deleteFolder(folder) {
      if (!folder || folder.name === "Všetko") return;
      this.folderToDelete = folder;
      this.showDeleteDialog = true;
    },

    async confirmDeleteFolder() {
      const folder = this.folderToDelete;
      this.showDeleteDialog = false;
      if (!folder) return;
      try {
        const formData = new FormData();
        formData.append("name", folder.name);
        const response = await axios.post(
          this.$store.state.api + "/folder/delete.php",
          formData
        );
        const status = (response.data.status || "").toLowerCase();
        if (status.includes("succes")) {
          this.$store.state.SnackBarText = `Priečinok "${folder.name}" bol vymazaný.`;
          await this.loadFolders();
          if (this.selectedFolder === folder.name) {
            this.selectFolder("Všetko");
          }
        } else {
          this.$store.state.SnackBarText =
            "Nepodarilo sa vymazať priečinok: " +
            (response.data.message || "Neznáma chyba.");
        }
      } catch (error) {
        this.$store.state.SnackBarText = "Chyba pri vymazávaní priečinka.";
        console.error(error);
      } finally {
        this.folderToDelete = null;
      }
    },

    openRowContextMenu(event, item) {
      event.preventDefault();
      let x = event.clientX;
      let y = event.clientY;
      const menuWidth = 210;
      const menuHeight = 54;

      if (x + menuWidth > window.innerWidth)
        x = window.innerWidth - menuWidth - 6;
      if (y + menuHeight > window.innerHeight)
        y = window.innerHeight - menuHeight - 6;

      this.rowContextMenu = {
        visible: true,
        x,
        y,
        item,
      };

      document.addEventListener("keydown", this.handleRowMenuEsc);
      setTimeout(() => {
        document.addEventListener("click", this.closeRowContextMenu, {
          once: true,
        });
      }, 0);
    },

    closeRowContextMenu() {
      this.rowContextMenu.visible = false;
      document.removeEventListener("keydown", this.handleRowMenuEsc);
    },

    handleRowMenuEsc(e) {
      if (e.key === "Escape") this.closeRowContextMenu();
    },

    async addToFolder(item) {
      this.closeRowContextMenu();
      const folders = this.folders.filter((f) => f.name !== "Všetko");
      const checkedFolders = await Promise.all(
        folders.map(async (f) => {
          const res = await axios.get(
            `${this.$store.state.api}/folder/list.php`,
            { params: { name: f.name } }
          );
          const ids = this.normalizeIds(res.data?.data);
          return { name: f.name, checked: ids.includes(String(item.id)) };
        })
      );
      this.addToFolderItem = item;
      this.addToFolderFolders = checkedFolders;
      this.showAddToFolderModal = true;
    },
  },
  watch: {
    folders: {
      handler(val) {
        this.localFolders = val.map((f) => ({
          ...f,
          checked: !!f.checked,
        }));
      },
      immediate: true,
      deep: true,
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

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
  line-height: 115%;
  /* 2.51563rem */
  margin: 0;
  margin-bottom: 0.3em;
}

table {
  width: 80%;
  border-collapse: collapse;
  margin: 2em 10% 3em;
  margin-bottom: 6em;
  font-size: 80%;
}

th,
td {
  padding: 0.1em 0.5em;
  text-align: center;
  font-size: 1.3em;
}

td:nth-child(1),
td:nth-child(2) {
  border-bottom: 1px solid #dddddd5e;
}

th {
  padding: 0.5em;
}

th:nth-child(2),
td:nth-child(2) {
  text-align: center;
}

.button {
  width: 2.2em;
  padding: 0.15em 0.8em;
  margin: 0.1em 0;

  a {
    font-size: 0.52em;
  }
}

td:nth-child(3) .button,
td:last-child .button {
  margin-left: auto;
}

.search {
  border-radius: 1.5625rem;
  border: 6px solid #fef35a;
  background: rgba(233, 233, 233, 0.93);
  box-shadow: 5px 5px 10px 0px rgba(0, 0, 0, 0.35);

  display: flex;
  align-items: center;
  justify-content: flex-start;
  padding: 0 0.5em;
  margin: 1em 0;

  width: 40em;
  font-size: 0.8em;
  position: fixed;
  left: 50%;
  transform: translate(-50%, -50%);
  bottom: 0em;

  img {
    width: 2em;
  }

  input {
    width: 100%;

    font-size: 1.25em;
    font-style: normal;
    font-weight: 400;
    line-height: 137.53%;
    /* 1.71913rem */
    letter-spacing: 0.0625rem;

    margin-bottom: -0.2em;
  }
}

.vytvorenie-zapisu {
  width: max-content;
  margin: 0.5em 1.2em 0;
  font-size: 2.2em;
}

.context-menu {
  position: absolute;
  z-index: 2000;
  background: #fff;
  border-radius: 16px;
  box-shadow: 0 4px 20px 0 rgba(80, 80, 90, 0.13),
    0 1.5px 7px 0 rgba(120, 120, 120, 0.12);
  border: 1px solid #eee;
  font-size: 1rem;
  transition: opacity 0.18s;
  opacity: 1;
  user-select: none;
  animation: appearMenu 0.17s cubic-bezier(0.42, 0, 0.6, 1.42);
}

@keyframes appearMenu {
  from {
    opacity: 0;
    transform: scale(0.97);
  }

  to {
    opacity: 1;
    transform: scale(1);
  }
}

.context-menu .menu-item {
  padding: 0.7em 1.5em;
  cursor: pointer;
  border-radius: 8px;
  text-align: center;
  font-weight: 500;
  transition: background 0.18s, color 0.12s;
}

.context-menu .menu-item:hover {
  background: #fcf59b55;
  color: #28281a;
}

.button-container {
  display: flex;
  gap: 0.5em;
}

@media only screen and (max-width: 1500px) {
  table {
    margin: 2em 2% 3em;
    width: 98%;
  }
}

.button.red-button {
  width: max-content;
  display: flex;
  justify-content: flex-end;
}

@media only screen and (max-width: 1020px) {
  .button-container {
    flex-direction: column;
  }
}
</style>
