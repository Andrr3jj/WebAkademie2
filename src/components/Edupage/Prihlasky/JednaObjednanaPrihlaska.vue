<template>
  <div class="jedna-objednavka">
    <div class="jedna">
      <p class="info">
        <b>ID:</b> <span>{{ formattedId }}</span>
      </p>

      <span v-if="!isEditable" class="badge badge-yellow">
        {{ typeLabel }}
      </span>
      <span
        v-else
        class="badge badge-yellow"
        style="cursor: pointer"
        @click="toggleFormOfStudy"
      >
        {{ typeLabel }}
      </span>

      <p class="info info-email">
        <b>Od:</b> <span>{{ itemData.email }}</span>
      </p>

      <p class="info">
        <b>Dňa:</b> <span>{{ dateStr }}</span>
      </p>

      <p class="info status-wrap">
        <b>Stav:</b>
        <img
          v-if="isCreated"
          class="status-icon"
          src="@/assets/images/icons/cakajuca-objednavka.png"
          alt="Čaká sa"
        />
        <img
          v-else-if="isConfirmed"
          class="status-icon"
          src="@/assets/images/icons/vybavena-objednavka.png"
          alt="Zaradený"
        />
        <img
          v-else-if="isCancelled"
          class="status-icon"
          src="@/assets/images/icons/nevybavena-objednavka.png"
          alt="Odhlásený"
        />
        <strong class="status-text">{{ statusLabel }}</strong>
      </p>

      <p class="info">
        <b>Učiteľ:</b> <span>{{ teacherName }}</span>
      </p>

      <div @click="showInfoToggle" class="button pill yellow">
        <p>{{ showInfo ? "Skryť" : "Zobraziť" }}</p>
      </div>

      <span
        @click="openDeleteModal"
        class="delete-btn"
        title="Vymazať objednávku"
      >
        <img src="@/assets/images/icons/kos.svg" alt="Vymazať" />
      </span>
    </div>

    <div v-if="showInfo" class="more-info card">
      <div class="cols">
        <div class="col">
          <h4 class="sect-title">Údaje o zákonnom zástupcovi:</h4>
          <p class="row">
            <b>Registroval:</b>
            <span v-if="!editing">{{
              slovenskyRegisterer[itemData.registerer] || "—"
            }}</span>
            <select v-else v-model="itemData.registerer">
              <option disabled value="">— vyber —</option>
              <option value="parent">Rodič</option>
              <option value="self">Dospelý</option>
            </select>
          </p>
          <p class="row">
            <b>Email:</b>
            <span v-if="!editing">{{ itemData.email || "—" }}</span>
            <input v-else v-model="itemData.email" />
          </p>
          <p class="row">
            <b>Tel. kontakt:</b>
            <span v-if="!editing">{{ itemData.phone || "—" }}</span>
            <input v-else v-model="itemData.phone" />
          </p>
          <p class="row mt">
            <b>Poznámka:</b>
            <span v-if="!editing">{{ itemData.note || "—" }}</span>
            <textarea v-else v-model="itemData.note"></textarea>
          </p>
        </div>

        <div class="col student-data">
          <h4 class="sect-title">Údaje o žiakovi:</h4>
          <div class="two-columns">
            <div class="left-col">
              <p class="row">
                <b>Meno:</b>
                <span v-if="!editing">{{ itemData.name }}</span>
                <input v-else v-model="itemData.name" />
              </p>
              <p class="row">
                <b>Priezvisko:</b>
                <span v-if="!editing">{{ itemData.surname }}</span>
                <input v-else v-model="itemData.surname" />
              </p>
              <p class="row">
                <b>Dátum narodenia:</b>
                <span v-if="!editing">{{ itemData.dateOfBirth }}</span>
                <input
                  v-else
                  v-model="itemData.dateOfBirth"
                  placeholder="dd-mm-yyyy"
                />
              </p>
              <p class="row">
                <b>Mesto bydliska:</b>
                <span v-if="!editing">{{ itemData.address }}</span>
                <input v-else v-model="itemData.address" />
              </p>
            </div>
            <div class="right-col">
              <p class="row">
                <b>Úroveň skúseností:</b>
                <span v-if="!editing">{{
                  slovenskyExperience[itemData.experience] || "—"
                }}</span>
                <select v-else v-model="itemData.experience">
                  <option value="beginner">Začiatočník</option>
                  <option value="intermediate">Stredne pokročilý</option>
                  <option value="advanced">Pokročilý</option>
                </select>
              </p>
              <p class="row">
                <b>Miesto narodenia:</b>
                <span v-if="!editing">{{ itemData.placeOfBirth }}</span>
                <input v-else v-model="itemData.placeOfBirth" />
              </p>
              <p class="row">
                <b>Miesto štúdia:</b>
                <span v-if="!editing">{{ itemData.placeOfStudy }}</span>
                <input v-else v-model="itemData.placeOfStudy" />
              </p>
              <p class="row">
                <b>Preferovaný čas:</b>
                <span v-if="!editing">{{
                  itemData.approximateTimeOfStudy
                }}</span>
                <input v-else v-model="itemData.approximateTimeOfStudy" />
              </p>
            </div>
          </div>

          <button v-if="!isCancelled" @click="odhlasitZiaka" class="red-btn">
            Odhlásiť žiaka
          </button>
          <div v-else class="date-action" style="align-items: end">
            <button class="red-btn" disabled>Odhlásený</button>
            <div class="date-label">
              <b>Dňa:</b>
              <p>{{ cancelledDate }}</p>
            </div>
          </div>
        </div>

        <div class="col slim">
          <div class="field">
            <label>Učiteľ</label>
            <span v-if="!editing">{{ teacherName }}</span>
            <select v-else v-model="ui.teacher" class="styled-select">
              <option disabled value="">Žiadny učiteľ</option>
              <option v-for="t in teachers" :key="t.id" :value="t.id">
                {{ t.name }}
              </option>
            </select>
          </div>

          <div class="field autocomplete">
            <label>Účet žiaka</label>
            <span v-if="!editing">{{ accountEmail }}</span>
            <template v-else>
              <input
                type="text"
                v-model="ui.studentQuery"
                @focus="showAccountSuggestions = true"
                @blur="hideSuggestionsLater"
                placeholder="Žiadny žiak"
              />
              <ul v-if="showAccountSuggestions" class="suggestions">
                <li
                  v-for="acc in filteredAccounts"
                  :key="acc.id"
                  @mousedown.prevent="selectStudent(acc)"
                >
                  {{ acc.email }}
                </li>
              </ul>
            </template>
          </div>

          <div
            class="field autocomplete"
            v-if="itemData.registerer === 'parent'"
          >
            <label>Rodič</label>
            <span v-if="!editing">{{ parentEmail }}</span>
            <template v-else>
              <input
                type="text"
                v-model="ui.parentQuery"
                @input="showParentSuggestions = true"
                @focus="showParentSuggestions = true"
                @blur="hideSuggestionsLater"
                placeholder="Žiadny rodič"
              />
              <ul v-if="showParentSuggestions" class="suggestions">
                <li
                  v-for="acc in filteredParents"
                  :key="acc.id"
                  @mousedown.prevent="selectParent(acc)"
                >
                  {{ acc.email }}
                </li>
              </ul>
            </template>
          </div>

          <div class="date-action">
            <template v-if="isConfirmed">
              <div class="date-label">
                <b>Dňa:</b>
                <p>{{ processedDate }}</p>
              </div>
              <button class="button" disabled>Potvrdené</button>
            </template>
            <template v-else-if="isCancelled">
              <button class="button" @click="znovaPrihlasit">
                Znova prihlásiť
              </button>
            </template>
            <template v-else>
              <button class="button" @click="potvrditObjednavku">
                Potvrdiť
              </button>
            </template>
          </div>
        </div>
      </div>
    </div>

    <div class="line horizontal w-90"></div>
  </div>
  <div
    v-if="showDeleteModal"
    class="modal-overlay"
    @click.self="closeDeleteModal"
  >
    <div class="modal-content">
      <h3>Vymazať prihlášku?</h3>
      <p>Naozaj chceš vymazať túto prihlášku? Táto akcia je nezvratná.</p>
      <div class="modal-buttons">
        <button @click="deleteOrder" class="red-btn">Vymazať</button>
        <button @click="closeDeleteModal" class="gray-btn">Zrušiť</button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
export default {
  props: {
    item: { type: Object, default: () => ({}) },
    users: { type: Array, default: () => [] },
    userMap: { type: Object, default: () => ({}) },
  },
  data() {
    return {
      itemData: {},
      showInfo: false,
      editing: false,
      teachers: [
        { id: 59, name: "Juraj" },
        { id: 54, name: "Andrej" },
        { id: 607, name: "Matej" },
      ],
      ui: {
        teacher: null,
        studentId: null,
        studentQuery: "",
        parentId: null,
        parentQuery: "",
      },
      showAccountSuggestions: false,
      showParentSuggestions: false,
      showDeleteModal: false,
    };
  },
  computed: {
    slovenskyExperience() {
      return {
        beginner: "Začiatočník",
        intermediate: "Stredne pokročilý",
        advanced: "Pokročilý",
      };
    },
    slovenskyRegisterer() {
      return {
        parent: "Rodič",
        self: "Dospelý",
      };
    },
    formattedId() {
      if (!this.itemData.timestamp_created || !this.itemData.id) return "";
      const y = new Date(this.itemData.timestamp_created * 1000).getFullYear();
      const num = String(this.itemData.id).padStart(3, "0");
      return `${num}-${y}`;
    },
    dateStr() {
      if (!this.itemData.timestamp_created) return "";
      const d = new Date(this.itemData.timestamp_created * 1000);
      return `${String(d.getDate()).padStart(2, "0")}-${String(
        d.getMonth() + 1
      ).padStart(2, "0")}-${d.getFullYear()}`;
    },
    processedDate() {
      if (!this.itemData.timestamp_included) return "";
      const d = new Date(this.itemData.timestamp_included * 1000);
      return `${String(d.getDate()).padStart(2, "0")}-${String(
        d.getMonth() + 1
      ).padStart(2, "0")}-${d.getFullYear()}`;
    },
    cancelledDate() {
      if (!this.itemData.timestamp_excluded) return "";
      const d = new Date(this.itemData.timestamp_excluded * 1000);
      return `${String(d.getDate()).padStart(2, "0")}-${String(
        d.getMonth() + 1
      ).padStart(2, "0")}-${d.getFullYear()}`;
    },
    isConfirmed() {
      return this.itemData.status === "zaradený";
    },
    isCancelled() {
      return this.itemData.status === "odhlásený";
    },
    isCreated() {
      return this.itemData.status === "čaká sa";
    },
    isEditable() {
      return this.itemData.status === "čaká sa";
    },
    statusLabel() {
      if (this.isConfirmed) return "Zaradený";
      if (this.isCancelled) return "Odhlásený";
      return "Čaká sa…";
    },
    teacherName() {
      const teacher = this.teachers.find(
        (t) => t.id == this.itemData.teacher_id
      );
      return teacher ? teacher.name : "Nepriradený";
    },
    typeLabel() {
      return this.itemData.formOfStudy === "duo"
        ? "Duo"
        : this.itemData.formOfStudy === "solo"
        ? "Solo"
        : "";
    },
    accountEmail() {
      return this.userMap[this.itemData.student_id]?.email || "—";
    },
    parentEmail() {
      return this.userMap[this.itemData.parent_id]?.email || "—";
    },
    filteredAccounts() {
      const q = this.ui.studentQuery.toLowerCase().trim();
      if (!q) return [];
      return this.users.filter(
        (u) => u.email && u.email.toLowerCase().includes(q)
      );
    },
    filteredParents() {
      const q = this.ui.parentQuery.toLowerCase().trim();
      if (!q) return [];
      return this.users.filter(
        (u) => u.email && u.email.toLowerCase().includes(q)
      );
    },
  },
  methods: {
    hydrateUIFromItem() {
      const t = this.itemData.teacher_id || null;
      const s = this.itemData.student_id || null;
      const p = this.itemData.parent_id || null;
      this.ui.teacher = t;
      this.ui.studentId = s;
      this.ui.parentId = p;
      const byId = (id) =>
        (id &&
          (this.userMap[id]?.email ||
            this.users.find((u) => String(u.id) === String(id))?.email)) ||
        "";
      this.ui.studentQuery = byId(s);
      this.ui.parentQuery = byId(p);
    },
    showInfoToggle() {
      this.showInfo = !this.showInfo;
    },
    cancelEdit() {
      this.editing = false;
      this.showInfoToggle();
    },
    toggleFormOfStudy() {
      this.itemData.formOfStudy =
        this.itemData.formOfStudy === "solo" ? "duo" : "solo";
    },
    openDeleteModal() {
      this.showDeleteModal = true;
    },
    closeDeleteModal() {
      this.showDeleteModal = false;
    },

    async deleteOrder() {
      try {
        const res = await axios.get(
          `https://heligonkovaakademia.sk/api/edupage/delete.php?id=${this.itemData.id}`
        );

        if (res.data.status === "Request succesfull") {
          this.$emit("deleted", this.itemData.id);
          this.showDeleteModal = false;
        } else {
          console.error("Mazanie zlyhalo:", res.data);
        }
      } catch (err) {
        console.error("Chyba pri mazaní:", err);
      }
    },

    formatValue(key, val) {
      if (val === undefined || val === null || val === "" || val === "NULL") {
        return "";
      }
      return String(val);
    },

    async ulozitZmeny() {
      try {
        const allForm = new FormData();
        allForm.append("id", this.itemData.id);
        allForm.append("mode", "all");

        for (const key in this.itemData) {
          allForm.append(key, this.formatValue(key, this.itemData[key]));
        }

        allForm.set(
          "teacher_id",
          this.ui.teacher ? String(this.ui.teacher) : "0"
        );
        allForm.set(
          "student_id",
          this.ui.studentId ? String(this.ui.studentId) : "0"
        );
        allForm.set(
          "parent_id",
          this.ui.parentId ? String(this.ui.parentId) : "0"
        );

        await axios.post(
          "https://heligonkovaakademia.sk/api/edupage/edit.php",
          allForm
        );

        this.itemData.teacher_id = this.ui.teacher || 0;
        this.itemData.student_id = this.ui.studentId || 0;
        this.itemData.parent_id = this.ui.parentId || 0;

        this.editing = false;
      } catch (e) {
        console.error("Chyba ulozitZmeny:", e);
      }
    },

    async potvrditObjednavku() {
      try {
        const now = Math.floor(Date.now() / 1000);

        const allForm = new FormData();
        allForm.append("id", this.itemData.id);
        allForm.append("mode", "all");
        for (const key in this.itemData) {
          allForm.append(key, this.formatValue(key, this.itemData[key]));
        }
        allForm.set("timestamp_included", String(now));

        if (this.ui.parentId !== null) {
          allForm.set("parent_id", String(this.ui.parentId));
        } else {
          allForm.set("parent_id", "0");
        }

        allForm.set(
          "teacher_id",
          this.ui.teacher ? String(this.ui.teacher) : "0"
        );
        allForm.set(
          "student_id",
          this.ui.studentId ? String(this.ui.studentId) : "0"
        );

        await axios.post(
          "https://heligonkovaakademia.sk/api/edupage/edit.php",
          allForm
        );

        const teacherForm = new FormData();
        teacherForm.append("id", this.itemData.id);
        teacherForm.append("mode", "teacher");
        teacherForm.append("teacher_id", String(this.ui.teacher));
        await axios.post(
          `https://heligonkovaakademia.sk/api/edupage/edit.php?teacher_id=${encodeURIComponent(
            this.ui.teacher ?? 0
          )}`,
          teacherForm
        );

        const statusForm = new FormData();
        statusForm.append("id", this.itemData.id);
        statusForm.append("mode", "status");
        statusForm.append("status", "zaradený");
        const quotedStatus = `'zaradený'`;
        await axios.post(
          `https://heligonkovaakademia.sk/api/edupage/edit.php?status=${encodeURIComponent(
            quotedStatus
          )}`,
          statusForm
        );

        this.itemData.status = "zaradený";
        this.itemData.teacher_id = this.ui.teacher || 0;
        this.itemData.parent_id = this.ui.parentId ?? 0;
        this.itemData.student_id = this.ui.studentId || 0;
        this.itemData.timestamp_included = now;

        this.editing = false;
        this.$emit("updateOrder", { ...this.itemData });
      } catch (err) {
        console.error("Chyba API:", err);
      }
    },

    async odhlasitZiaka() {
      try {
        const now = Math.floor(Date.now() / 1000);

        const allForm = new FormData();
        allForm.append("id", this.itemData.id);
        allForm.append("mode", "all");
        for (const key in this.itemData) {
          allForm.append(key, this.formatValue(key, this.itemData[key]));
        }
        allForm.set("timestamp_excluded", String(now));
        allForm.set("status", "odhlásený");

        allForm.set("teacher_id", "0"); // vymaže učiteľa
        allForm.set(
          "student_id",
          this.ui.studentId ? String(this.ui.studentId) : "0"
        );
        allForm.set(
          "parent_id",
          this.ui.parentId ? String(this.ui.parentId) : "0"
        );

        const quotedStatus = `'odhlásený'`;
        await axios.post(
          `https://heligonkovaakademia.sk/api/edupage/edit.php?status=${encodeURIComponent(
            quotedStatus
          )}`,
          allForm
        );

        this.itemData.status = "odhlásený";
        this.itemData.timestamp_excluded = now;
        this.itemData.teacher_id = 0;
        this.ui.teacher = null;

        this.$emit("updateOrder", { ...this.itemData });
      } catch (err) {
        console.error("Chyba API:", err);
      }
    },

    async znovaPrihlasit() {
      try {
        const allForm = new FormData();
        allForm.append("id", this.itemData.id);
        allForm.append("mode", "all");
        for (const key in this.itemData) {
          allForm.append(key, this.formatValue(key, this.itemData[key]));
        }
        allForm.set("timestamp_included", "0");
        allForm.set("timestamp_excluded", "0");
        allForm.set("status", "čaká sa");
        allForm.set("teacher_id", "0");
        allForm.set(
          "student_id",
          this.ui.studentId ? String(this.ui.studentId) : "0"
        );
        allForm.set(
          "parent_id",
          this.ui.parentId ? String(this.ui.parentId) : "0"
        );

        await axios.post(
          "https://heligonkovaakademia.sk/api/edupage/edit.php",
          allForm
        );

        const teacherForm = new FormData();
        teacherForm.append("id", this.itemData.id);
        teacherForm.append("mode", "teacher");
        await axios.post(
          "https://heligonkovaakademia.sk/api/edupage/edit.php?teacher_id=10",
          teacherForm
        );

        const statusForm = new FormData();
        statusForm.append("id", this.itemData.id);
        statusForm.append("mode", "status");
        await axios.post(
          "https://heligonkovaakademia.sk/api/edupage/edit.php?status='čaká sa'",
          statusForm
        );

        this.itemData.status = "čaká sa";
        this.itemData.teacher_id = 0;
        this.itemData.student_id = this.ui.studentId || 0;
        this.itemData.parent_id = this.ui.parentId || 0;
        this.ui.teacher = null;
        this.itemData.timestamp_included = null;
        this.itemData.timestamp_excluded = null;
        this.editing = true;
        this.$emit("updateOrder", { ...this.itemData });
      } catch (err) {
        console.error("Chyba API:", err);
      }
    },
    selectStudent(acc) {
      this.ui.studentId = acc.id;
      this.ui.studentQuery = acc.email;
      this.showAccountSuggestions = false;
    },
    selectParent(acc) {
      this.ui.parentId = acc.id;
      this.ui.parentQuery = acc.email;
      this.showParentSuggestions = false;
    },
    hideSuggestionsLater() {
      setTimeout(() => {
        this.showAccountSuggestions = false;
        this.showParentSuggestions = false;
      }, 200);
    },
  },
  mounted() {
    this.itemData = { ...this.item };
    if (this.itemData.status === "čaká sa") this.editing = true;
    this.hydrateUIFromItem();
  },
  watch: {
    item: {
      deep: true,
      handler() {
        this.itemData = { ...this.item };
        this.hydrateUIFromItem();
      },
    },
  },
};
</script>

<style lang="scss" scoped>
.jedna {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin: 0 auto;
  gap: 1%;
  padding: 1em 0;
}

.jedna-objednavka {
  transition-duration: 0.2s;
}

.jedna-objednavka:hover {
  background: rgba($color: #000000, $alpha: 0.01);
}

.jedna-objednavka:has(.more-info) {
  background: rgba(254, 243, 90, 0.5);
  margin-top: 1em;
  padding: 1em 2em 0em;
  border-radius: 2rem;
  transform: scale(1.02);
}

.jedna > .info:first-child {
  width: 16%;
}

.info {
  padding: 0.1em;
  font-size: 1.3em;
  width: 22%;
}

.badge {
  display: inline-block;
  padding: 0.1em 0.7em;
  text-align: center;
  border-radius: 9px;
  background: #fef35a;
  box-shadow: 2.5px 2.5px 7.5px rgba(0, 0, 0, 0.5);
  font-size: 0.875em;
  font-family: "Adumu", sans-serif;
  margin-left: -1em;
}

.info-email {
  width: 37%;
  font-size: 1.3em;
}

.status-wrap {
  display: flex;
  flex-direction: row;
  gap: 5%;
  align-items: center;
}

.status-text {
  font-weight: normal;
}

img {
  width: 1.3em;
}

.button {
  font-size: 0.9em;
}

.line {
  height: 0.35rem;
}

.delete-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-left: 0.5em;
  margin-right: 0.2em;
  cursor: pointer;
  transition: filter 0.15s;

  img {
    width: 1.2em;
    height: 1.2em;
    display: block;
    margin: 0 auto;
    transition: filter 0.15s, transform 0.18s;
  }

  &:hover img,
  &:focus img {
    filter: grayscale(0) drop-shadow(0 0 4px #e53e3e44);
    transform: scale(1.08) rotate(-12deg);
  }
}

.more-info {
  display: flex;
  flex-direction: row;
  align-items: flex-start;
  justify-content: space-between;
  padding: 0.5em 2% 1.5em;
  width: 96%;
  border-top: 0.2em solid;
  font-size: 80%;
}

.row {
  background: transparent !important;
  box-shadow: none !important;
  margin: 0.5em 0;
  align-items: center;
  text-align: left;

  span {
    margin-left: 0.3em;
  }
}

.row.mt {
  margin-top: 2em;
}

.cols {
  display: flex;
  justify-content: space-between;
  width: 100%;
  gap: 1em;
}

.col {
  .sect-title {
    font-family: "Bahnschrift", sans-serif;
    font-size: 1.25em;
    text-align: left;
    margin: 1em 0;
  }
}

.col.slim {
  width: 18%;
  margin-top: 1em;
}

.field {
  display: flex;
  flex-direction: column;
  align-items: start;
  margin-bottom: 1em;

  input,
  select {
    border-radius: 8px;
    background: #90ca50;
    box-shadow: inset -0.4375em 0.3125em 0.9375em rgba(0, 0, 0, 0.25),
      0 0.25em 0.25em rgba(0, 0, 0, 0.25);
    padding: 0.2em 5%;
    box-sizing: border-box;
    border: none;
    font-size: 1em;
    width: 100%;
  }

  select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background-image: none;
  }
}

.jedna-objednavka input,
.jedna-objednavka select,
.jedna-objednavka textarea {
  background: #fffde7; /* jemná žltá, aby ladila */
  border: 1px solid #e0c200; /* tenká linka */
  border-radius: 6px;
  padding: 4px 8px;
  font-size: 14px;
  font-family: inherit;
  color: #333;
  width: 100%;
  box-sizing: border-box;
}

.jedna-objednavka input:focus,
.jedna-objednavka textarea:focus {
  outline: none;
  border-color: #ffcc00; /* výraznejšia žltá */
  box-shadow: 0 0 4px rgba(255, 204, 0, 0.6);
}

.jedna-objednavka textarea {
  min-height: 60px;
  resize: vertical;
}

.current-chip {
  margin-top: 6px;
  font-size: 0.9em;
}

.chip {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 10px;
  background: #fffbe6;
  border: 1px solid #e0c200;
  line-height: 1.6;
}

.chip--empty {
  opacity: 0.7;
  border-style: dashed;
}

.date-action {
  display: flex;
  gap: 1em;
  align-items: center;
  justify-content: center;
  width: 17em;

  .date-label {
    display: flex;
    gap: 0.5em;
  }

  .button {
    padding: 0.15em 1.2em;
    font-size: 0.875em;
    font-family: "Adumu", sans-serif;
    align-self: center;
  }
}

.student-data .two-columns {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1em;
}

.student-data .row {
  margin: 0.3em 0;
}

.student-data b {
  margin-right: 0.4em;
}

.riadok .only-info {
  line-height: 1.8em;
}

.only-info {
  font-size: 1em;
  min-width: 5em;
  max-width: 28em;
  text-align: left;
}

.border-top {
  border-top: 0.13em solid #000;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

.red-btn,
.gray-btn {
  padding: 0.2em 1.9em;
  font-size: 0.875em;
  font-family: inherit;
  border-radius: 0.5em;
  border: none;
  outline: none;
  cursor: pointer;
  transition: background 0.17s,
    transform 0.18s cubic-bezier(0.39, 1.6, 0.7, 1.15), box-shadow 0.18s;
  box-shadow: 0 2px 14px 0 #0001;
  font-family: "Adumu", sans-serif;
}

.gray-btn {
  background: #ededed;
  color: #2b2b2b;
  margin-top: 2em;
}

.gray-btn:hover,
.gray-btn:focus {
  background: #ccc;
  transform: scale(1.08);
  box-shadow: 0 4px 24px 0 #aaaaaa33;
}

.red-btn {
  background: #f86e5c;
  color: #000;
  margin-top: 2em;
}

.red-btn:hover,
.red-btn:focus {
  background: #d1031e;
  color: #fff;
  transform: scale(1.08);
  box-shadow: 0 4px 24px 0 #d11a2a33;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.33);
  z-index: 1000;
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeIn 0.25s;
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }

  to {
    opacity: 1;
  }
}

.modal-content {
  background: #fff;
  padding: 2.2em 2.7em 1.5em 2.7em;
  border-radius: 1em;
  box-shadow: 0 8px 40px 0 #0002;
  text-align: center;
  min-width: 280px;
  transform: translateY(-30px) scale(0.96);
  animation: modalPop 0.33s cubic-bezier(0.4, 1.5, 0.6, 1) forwards;
  opacity: 0;
}

@keyframes modalPop {
  from {
    opacity: 0;
    transform: translateY(-30px) scale(0.96);
  }

  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.modal-content h3 {
  font-family: "Bahnschrift" sans-serif;
  font-size: 2em;
  margin-bottom: 0.8em;
  color: #d11a2a;
  letter-spacing: -0.01em;
  text-shadow: 1px 1px 0 #fff7;
}

.modal-buttons {
  margin-top: 2em;
  display: flex;
  gap: 1.3em;
  justify-content: center;
}

/* 💻 Menšie desktopy / veľké laptopy */
@media only screen and (max-width: 1380px) {
  .jedna {
    font-size: 90%;
  }
}

/* 💻 Väčšina notebookov */
@media only screen and (max-width: 1280px) {
  .jedna {
    font-size: 85%;
    gap: 0.8em;
  }
}

/* 📱 Tablety a iPady */
@media only screen and (max-width: 1024px) {
  .jedna {
    flex-direction: row;
    align-items: center;
    gap: 0.6em;
    font-size: 80%;
  }

  .info,
  .info-email {
    width: 100%;
    font-size: 1.1em;
  }

  .badge {
    margin-left: 0;
  }

  .status-wrap {
    flex-wrap: wrap;
    gap: 0.5em;
  }

  .more-info {
    flex-direction: row;
    align-items: flex-start;
    padding: 1em;
    width: 100%;
    font-size: 80%;
  }

  .cols {
    flex-direction: row;
  }

  .col.slim {
    width: 25%;
  }

  .date-action {
    flex-direction: column;
    gap: 0.8em;
    width: 100%;
  }

  .student-data .two-columns {
    grid-template-columns: 1fr;
  }

  .information {
    width: 100%;
    justify-content: flex-start;
    gap: 0.5em;
  }

  .tovar {
    width: 100%;
  }
}
</style>
