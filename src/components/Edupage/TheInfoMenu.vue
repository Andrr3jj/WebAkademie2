<template>
  <section class="menu computer" :key="activeKey">
    <div class="top">
      <div v-if="hasTwo" class="profiles two">
        <div
          class="profile-photo"
          v-for="s in twoOrdered"
          :key="s ? s.id : 'none'"
          :class="{ 'is-active': active && s && s.id === active.id }"
        >
          <img :src="photoFor(s)" :alt="s ? s.name : 'Profil'" />
          <div
            class="stars"
            v-if="starsFor(s) > 0"
            :key="s ? `stars-${s.id}` : 'no-stars'"
          >
            <div
              v-for="n in starsFor(s)"
              :key="`${s ? s.id : 'x'}-${n}`"
              class="star"
            >
              <img src="@/assets/images/gallery/hviezda.svg" alt="hviezda" />
            </div>
          </div>
        </div>
      </div>

      <div v-else class="profile-photo">
        <img :src="profilePhoto" :alt="active ? active.name : 'Profil'" />
        <div class="stars" v-if="activeStars > 0" :key="activeKey">
          <div v-for="n in activeStars" :key="`${activeKey}-${n}`" class="star">
            <img src="@/assets/images/gallery/hviezda.svg" alt="hviezda" />
          </div>
        </div>
      </div>

      <div class="icons">
        <img
          @click="openEdit"
          src="@/assets/images/icons/update.svg"
          alt="Aktualizovať"
          title="Upraviť žiaka"
        />
        <img
          v-if="Array.isArray(students) && students.length >= 2"
          @click="toggleActive()"
          src="@/assets/images/icons/switch.svg"
          alt="Vymeniť"
          title="Vymeniť aktívneho žiaka"
        />
      </div>
    </div>

    <div class="info">
      <div class="basics">
        <p class="name">{{ active ? active.name : "—" }}</p>
        <div class="body">
          <p class="Adumu">
            {{ active && active.star != null ? active.star : 0 }}
          </p>
        </div>
      </div>

      <p class="email">{{ active && active.email ? active.email : "—" }}</p>

      <div class="stats">
        <div class="absolved">
          <p>Absolvované</p>
          <strong>{{
            active && active.completed != null ? active.completed : 0
          }}</strong>
        </div>
        <div class="absolved">
          <p>Vymeškané</p>
          <strong>{{
            active && active.missed != null ? active.missed : 0
          }}</strong>
        </div>
      </div>

      <div class="basics">
        <div class="time-wrap">
          <div class="nadpis"><p>Čas výučby:</p></div>
          <time-play :doba-hranie="rozdeleneHranie" class="time-val" />
        </div>
        <div class="ulohy">
          <div class="nadpis">Ulohy:</div>
          <img src="@/assets/images/icons/task.svg" alt="Heligonka" />
          <p>{{ active && active.tasks != null ? active.tasks : 0 }}</p>
        </div>
      </div>
    </div>

    <div class="line horizontal w-80"></div>

    <div class="chips-wrap" v-if="chips.length">
      <div class="scroll">
        <div class="chips">
          <span v-for="c in chips" :key="c.id" class="chip">{{ c.name }}</span>
        </div>
      </div>
    </div>

    <div class="navigation">
      <div class="contacts" v-if="studentPhone || parentPhone">
        <!-- žiak -->
        <div class="contact" v-if="studentPhone">
          <p class="label">Kontakt na žiaka:</p>
          <div class="contact-row">
            <img class="contact-icon" :src="contactPhoneIcon" alt="" />
            <span class="phone">{{ studentPhone }}</span>

            <!-- ⬇️ PRIDANÉ tlačidlo -->
            <button
              class="icon-bell"
              type="button"
              title="Pripomenúť nezaplatené"
              @click="notifyUnpaid(active?.id, 'student')"
            >
              <img
                src="@/assets/images/icons/neuhradenaPlatba.svg"
                alt="Pripomenúť"
              />
            </button>
          </div>
        </div>

        <!-- rodič -->
        <div class="contact" v-if="parentPhone">
          <p class="label">Kontakt na rodiča:</p>
          <div class="contact-row">
            <img class="contact-icon" :src="contactPhoneIcon" alt="" />
            <span class="phone">{{ parentPhone }}</span>

            <!-- ⬇️ PRIDANÉ tlačidlo -->
            <button
              class="icon-bell"
              type="button"
              title="Pripomenúť nezaplatené"
              @click="notifyUnpaid(active?.id, 'parent')"
            >
              <img
                src="@/assets/images/icons/neuhradenaPlatba.svg"
                alt="Pripomenúť"
              />
            </button>
          </div>
        </div>
      </div>

      <router-link
        v-if="$store.state.user == null"
        to="/prihlasenie"
        class="login button"
      >
        <img
          @click="odhlasenie()"
          src="@/assets/images/icons/prihlasenie.svg"
          alt=""
        />
        <router-link to="/prihlasenie">Prihlásiť sa</router-link>
      </router-link>

      <div class="moj-ucet">
        <router-link
          v-if="$store.state.user != null"
          to="/ucebna"
          class="login no-padding button"
        >
          <img :src="accountPhoto" alt="Profilová fotka" class="profile-icon" />
          <p>Moja učebňa</p>
          <a class="logout">
            <img
              @click="odhlasenie()"
              src="@/assets/images/icons/odhlasenie.svg"
              alt="odhlasenie"
            />
          </a>
        </router-link>
      </div>
    </div>
  </section>
</template>

<script>
import TimePlay from "../ucebna/TimePlay.vue";
import contactPhoneIcon from "@/assets/images/icons/contactPhoneIcon.svg";

export default {
  name: "TheInfoMenu",
  components: { TimePlay },
  emits: ["edit-request", "logout"],
  data() {
    return {
      contactPhoneIcon,
      chips: [],
    };
  },
  computed: {
    infoLesson() {
      return this.$store.state.ui?.info?.lesson || {};
    },
    allowedIds() {
      const ids = Array.isArray(this.infoLesson.studentIds)
        ? this.infoLesson.studentIds
            .filter((x) => x != null)
            .map((x) => String(x))
        : [];
      return ids;
    },
    students() {
      const list = this.$store.getters["edupage/learning/list"] || [];
      if (this.allowedIds.length) {
        return list.filter((s) => s && this.allowedIds.includes(String(s.id)));
      }
      return list;
    },
    active() {
      const a = this.$store.getters["edupage/learning/activeStudent"];
      if (
        a &&
        (!this.allowedIds.length || this.allowedIds.includes(String(a.id)))
      )
        return a;
      if (this.allowedIds.length) {
        const fb = this.students.find((s) =>
          this.allowedIds.includes(String(s.id))
        );
        return fb || null;
      }
      return a || null;
    },
    activeKey() {
      const a = this.active;
      return a && a.id != null ? String(a.id) : "no-active";
    },
    rozdeleneHranie() {
      const totalMin = Number(this.active?.time?.total ?? 0);
      const dni = Math.floor(totalMin / (24 * 60));
      const zostHod = totalMin % (24 * 60);
      const hodiny = Math.floor(zostHod / 60);
      const minuty = zostHod % 60;
      return { dni, hodiny, minuty };
    },
    defaultAvatar() {
      return 'data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160"><rect width="100%" height="100%" rx="24" ry="24" fill="%23eee"/></svg>';
    },
    profilePhoto() {
      return this.normalizeUrl(this.active?.photo) || this.defaultAvatar;
    },
    hasTwo() {
      return this.students.length === 2;
    },
    secondStudent() {
      if (!this.hasTwo) return null;
      const [s1, s2] = this.students;
      if (this.active && this.active.id != null)
        return String(s1?.id) === String(this.active.id) ? s2 : s1;
      return s2 || null;
    },
    twoOrdered() {
      if (!this.hasTwo) return [];
      return [this.secondStudent, this.active].filter(Boolean);
    },
    activeStars() {
      const a = this.active;
      const val =
        a && typeof a.lastHourStar !== "undefined" ? a.lastHourStar : 0;
      const num = Number(val);
      return Number.isFinite(num) && num > 0 ? num : 0;
    },
    accountPhoto() {
      const u = this.$store?.state?.user || {};
      const raw =
        u.profilePhotoUrl ||
        u.profile_photo ||
        u.photo ||
        u.avatar ||
        u.image ||
        "";
      return this.normalizeUrl(raw) || this.defaultAvatar;
    },
    calendarId() {
      return this.infoLesson?.id != null ? String(this.infoLesson.id) : null;
    },
    calendarDayName() {
      return this.infoLesson?.den || this.infoLesson?.dayName || "";
    },
    calendarHour() {
      return this.infoLesson?.cas || this.infoLesson?.hour || "";
    },
    formOfStudy() {
      return this.infoLesson?.isDuo ? "duo" : this.infoLesson?.forma || "solo";
    },
    studentPhone() {
      const src =
        this.active?.edupage_registration_student ??
        this.active?.data?.edupage_registration_student ??
        this.$store?.state?.user?.edupage_registration_student;

      if (!src) return null;
      const list = Array.isArray(src) ? src : Object.values(src);
      const rec = list.find(
        (x) => x && x.phone && String(x.phone).trim() !== ""
      );
      return rec ? String(rec.phone).replace(/\s+/g, " ").trim() : null;
    },
    parentPhone() {
      const src =
        this.active?.edupage_registration_parent ??
        this.active?.data?.edupage_registration_parent ??
        this.$store?.state?.user?.edupage_registration_parent;

      if (!src) return null;
      const list = Array.isArray(src) ? src : Object.values(src);
      const rec = list.find(
        (x) => x && x.phone && String(x.phone).trim() !== ""
      );
      return rec ? String(rec.phone).replace(/\s+/g, " ").trim() : null;
    },
  },
  methods: {
    normalizeUrl(url) {
      return String(url || "").replace(/^(https?:\/\/)www\./i, "$1");
    },
    async ensureLoaded() {
      const ids = this.allowedIds;
      if (ids.length) {
        for (const sid of ids) {
          try {
            await this.$store.dispatch(
              "edupage/learning/fetchStudentAdvanced",
              sid
            );
          } catch {
            try {
              await this.$store.dispatch("learning/fetchStudentAdvanced", sid);
            } catch {
              // ignore
            }
          }
        }
        const first = ids[0];
        if (first != null) {
          try {
            this.$store.commit("edupage/learning/SET_ACTIVE", first);
          } catch {
            try {
              this.$store.commit("learning/SET_ACTIVE", first);
            } catch {
              // ignore
            }
          }
        }
      } else {
        const aid =
          this.active && this.active.id != null ? String(this.active.id) : null;
        if (aid) {
          try {
            await this.$store.dispatch(
              "edupage/learning/fetchStudentAdvanced",
              aid
            );
          } catch {
            try {
              await this.$store.dispatch("learning/fetchStudentAdvanced", aid);
            } catch {
              // ignore
            }
          }
        }
      }
    },
    async loadNoteChips() {
      try {
        let note =
          this.active?.note ??
          this.active?.data?.note ??
          this.$store?.state?.user?.note ??
          "";

        if (!note && this.active?.id != null) {
          const aid = String(this.active.id);
          try {
            await this.$store.dispatch(
              "edupage/learning/fetchStudentAdvanced",
              aid
            );
          } catch {
            try {
              await this.$store.dispatch("learning/fetchStudentAdvanced", aid);
            } catch {
              // ignore
            }
          }
          note =
            this.$store.getters["edupage/learning/activeStudent"]?.note ??
            this.active?.note ??
            "";
        }

        const ids = Array.from(
          new Set(
            Array.from(
              String(note).matchAll(
                /(Pridané\s+noty\s+s\s+id|Add\s+notes\s+id)\s*[:=]\s*(\d+)/gi
              )
            )
              .map((m) => Number(m[2]))
              .filter((n) => Number.isFinite(n))
          )
        );

        if (!ids.length) {
          this.chips = [];
          return;
        }

        const base =
          this.$store.state.api || "https://heligonkovaakademia.sk/api";
        const metas = await Promise.all(
          ids.map(async (id) => {
            const r = await fetch(`${base}/product/loadLimited.php?id=${id}`);
            const j = await r.json().catch(() => ({}));
            const name = j?.data?.name || `Zápis #${id}`;
            return { id, name };
          })
        );

        const seen = new Set();
        this.chips = metas.filter((m) =>
          seen.has(m.id) ? false : seen.add(m.id)
        );
      } catch {
        this.chips = [];
      }
    },
    toggleActive() {
      const list = this.students;
      const activeId = this.active ? String(this.active.id) : null;
      if (list.length === 2) {
        const id1 = list[0] ? String(list[0].id) : null;
        const id2 = list[1] ? String(list[1].id) : null;
        const nextId = activeId === id1 ? id2 : id1;
        if (nextId != null)
          this.$store.commit("edupage/learning/SET_ACTIVE", nextId);
        return;
      }
      const mod = this.$store.state.edupage?.learning || {};
      const order = Array.isArray(mod.order)
        ? mod.order.map(String)
        : list.map((s) => s && String(s.id)).filter(Boolean);
      if (order.length > 1 && activeId != null) {
        const idx = order.indexOf(activeId);
        const nextId = order[(idx + 1) % order.length];
        if (nextId != null)
          this.$store.commit("edupage/learning/SET_ACTIVE", nextId);
      }
    },
    photoFor(s) {
      if (!s) return this.defaultAvatar;
      return this.normalizeUrl(s.photo) || this.defaultAvatar;
    },
    starsFor(s) {
      if (!s) return 0;
      const val =
        typeof s.lastHourStar !== "undefined" ? Number(s.lastHourStar) : 0;
      return Number.isFinite(val) && val > 0 ? val : 0;
    },
    odhlasenie() {
      this.$emit("logout");
    },
    openEdit() {
      const payload = {
        studentId: this.active ? String(this.active.id) : null,
        studentName: this.active?.name || "",
        duoStudentId: this.secondStudent ? String(this.secondStudent.id) : null,
        calendarId: this.calendarId,
        dayName: this.calendarDayName,
        hour: this.calendarHour,
        formOfStudy: this.formOfStudy,
      };
      try {
        this.$store.commit("ui/REQUEST_EDIT", payload);
      } catch {
        try {
          this.$store.commit("REQUEST_EDIT", payload);
        } catch {
          // ignore
        }
      }
      try {
        window.dispatchEvent(
          new CustomEvent("ui:edit-request", { detail: payload })
        );
      } catch {
        // ignore
      }
    },
  },
  async mounted() {
    await this.ensureLoaded();
    await this.loadNoteChips();
  },
  watch: {
    infoLesson: {
      handler() {
        this.ensureLoaded();
        this.loadNoteChips();
      },
      deep: true,
    },
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.time-val {
  font-size: 40%;
}
.name {
  font-size: 1.8em;
}

.absolved p {
  margin-bottom: 0.6em;
  font-size: #000;
  opacity: 0.5;
}
.ulohy {
  margin-bottom: 1em;
}
.ulohy p {
  font-size: 1.2em;
  color: #00913f;
  -webkit-text-stroke: 0.5px #90ca50;
  margin-top: -1em;
}
.ulohy > div {
  margin-bottom: 0.6em;
}
.line {
  background: #000000;
  opacity: 0.25;
}

.absolved {
  border-left: 0.15em solid #f7b11e;
  padding-left: 0.5em;
}
.absolved:first-child {
  border-left: 0.15em solid #90ca50;
  padding-left: 0.5em;
}
.absolved strong {
  font-size: 1.4em;
}

.basics,
.stats {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1em;
}

.body p {
  color: #00913f;
  font-weight: 400;
  font-size: 1.2em;
  padding: 0.4em 0.6em;
  border-radius: 0.8em;
  background: #fef35ab8;
  box-shadow: inset 0px 2px 4px rgba(0, 0, 0, 0.15);
}
.info > div,
.info > p {
  margin-bottom: 1em;
}
.email {
  font-size: 1.2em;
  opacity: 0.5;
}

.scroll {
  height: 4em;
  border-radius: 0;
}

.top {
  padding: 3.7em 0;
  position: relative;
}
.icons {
  position: absolute;
  right: 1.5em;
  top: 0.8em;
  display: flex;
  gap: 1em;
}
.icons img {
  width: 2.2em;
  transition-duration: 0.2s;
  cursor: pointer;
}
.icons img:hover {
  transform: scale(1.1);
}

.profiles.two {
  display: flex;
  align-items: stretch;
  justify-content: center;
  margin: 0 2em;
}
.profiles.two .profile-photo {
  position: relative;
  flex: 1 1 0;
  max-width: 12em;
}
.profiles.two .profile-photo.is-active {
  margin-left: -3em;
  margin-top: 4em;
  z-index: 2;
  border: 0.35em solid #90ca50;
}

.profile-photo {
  max-width: 12em;
  position: relative;
  width: 80%;
  margin: auto;
  border-radius: 15px;
  border: 0.35em solid #fef35a;
  display: flex;
}
.profile-photo img {
  width: 100%;
  object-fit: cover;
  border-radius: 10px;
  height: 100%;
}
.profile-photo .stars {
  position: absolute;
  bottom: -1.2em;
  left: 50%;
  transform: translateX(-50%);
}

.stars {
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
  align-items: center;
  gap: 0.3em;
}
.stars .star {
  opacity: 0;
  animation: starFadeIn 0.6s ease forwards;
  transition: opacity 0.2s ease;
}
.stars .star img {
  width: 1.5em;
  transition: transform 0.2s ease;
  pointer-events: auto;
  cursor: pointer;
}
.stars .star img:hover {
  transform: translateY(0) scale(1.1) rotate(10deg);
}
.stars .star:nth-child(1) {
  animation-delay: 0s;
}
.stars .star:nth-child(2) {
  animation-delay: 0.2s;
}
.stars .star:nth-child(3) {
  animation-delay: 0.4s;
}
.stars .star:nth-child(4) {
  animation-delay: 0.6s;
}
.stars .star:nth-child(5) {
  animation-delay: 0.8s;
}

.button.login.no-padding {
  padding: 0;
}
.profile-icon {
  border-radius: 50% !important;
  width: 1.5em !important;
  height: 1.5em !important;
  padding: 0.5em !important;
  margin-right: 0 !important;
  object-fit: cover;
  filter: drop-shadow(3px 3px 3px rgba(0, 0, 0, 0.3));
}

.chips-wrap {
  width: 100%;
  padding: 0.8em 1.2em 0;
  display: flex;
  justify-content: center;
  box-sizing: border-box;
}
.chips {
  display: flex;
  flex-wrap: wrap;
  gap: 0.4em 0.2em;
  justify-content: center;
}
.chip {
  background: #aad985;
  border-radius: 999px;
  padding: 0.35em 1.1em;
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
  line-height: 1.2;
  white-space: nowrap;
  font-size: 0.55em;
}

.menu {
  height: 100%;
  width: 27vw;
  max-width: 20em;
  min-width: 17em;
  position: relative;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
}
.navigation {
  height: 100%;
  padding-bottom: 5%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: flex-end;
}

.contacts {
  width: 100%;
  padding: 0 1.2em 1.2em;
  display: flex;
  flex-direction: column;
  gap: 0.9em;
}
.contact .label {
  font-weight: 800;
  font-size: 1em;
  margin: 0 0 0.2em 0;
}
.contact-row {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.6em;
}
.contact .phone {
  font-size: 1em;
  color: rgba($color: #000000, $alpha: 0.5);
  letter-spacing: 0.02em;
}
.contact-icon {
  width: 1.8em;
  height: 1.8em;
}

.logo {
  height: 30%;
}
.logo img {
  width: 83%;
  margin: 1vw auto auto;
  filter: drop-shadow(3px 3px 3px rgba(0, 0, 0, 0.3));
}

ul {
  display: flex;
  flex-direction: column;
  align-items: center;
}
ul a.login {
  font-size: 120%;
}

.cart-number {
  background: #fef35a;
  position: absolute;
  border-radius: 20rem;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 0.55em;
  box-shadow: 0px 2px 2px 0px rgba(0, 0, 0, 0.35);
  top: -0.3em;
  left: 1.15em;
  padding: 0.2em 0.5em 0.1em;
}

.navigation > div.bot-nav > ul > a:nth-child(4) {
  margin-top: 0.4em;
}
.navigation li {
  padding: 1.5% 2.5% 1.5% 5%;
  margin: 1% auto;
  display: flex;
  align-items: center;
  gap: 6%;
  position: relative;
  font-size: 1.5625em;
  font-weight: 700;
  letter-spacing: 0.03em;
  width: 8.5em;
}
.navigation li:hover {
  background: lighten($lt-gr-clr, 20%);
  transition-duration: 0.2s;
  border-radius: 0.625em;
}
.navigation li:hover img {
  filter: brightness(0);
}
.navigation li img {
  width: 5em;
}
.navigation li a {
  color: #000;
}
.navigation li > a {
  display: flex;
  gap: 6%;
  justify-content: flex-start;
  align-items: center;
}

.icon {
  width: 10%;
  display: flex;
  align-items: center;
  justify-content: center;
}
.icon img {
  height: 100%;
  width: 1em;
}

.login {
  padding: 0.4em 0.8em;
}
.login img {
  width: 1.4em;
}
.prihlaseny {
  padding: 0.4em 1.5em;
}
.login.no-padding img {
  width: 1em;
  padding: 0.6em 0 0.6em 0.8em;
}
.login.no-padding p {
  padding: 0.6em 0.4em 0.6em 0;
}

li:has(.router-link-active.router-link-exact-active) {
  background: $lt-gr-clr;
  border-radius: 0.625em;
}
li:has(.router-link-active.router-link-exact-active) img {
  filter: brightness(0);
}

section {
  padding: 0;
}

/* zvonček – rovnaký vizuál ako v platbách */
.icon-bell {
  background: #f86e5c;
  inline-size: 1.8em;
  block-size: 1.8em;
  margin-left: 0.4em;
  border: 0;
  border-radius: 0.6em;
  display: inline-grid;
  place-items: center;
  cursor: pointer;
  box-shadow: 0 0.6em 0.9em rgba(0, 0, 0, 0.16),
    inset 0 -0.15em 0 rgba(0, 0, 0, 0.08);
  transition: transform 0.05s ease, filter 0.15s ease,
    background-color 0.15s ease;
}
.icon-bell:hover {
  filter: brightness(0.98);
}
.icon-bell:active {
  transform: translateY(0.06em);
}
.icon-bell img {
  inline-size: 1.1em;
  block-size: 1.1em;
  object-fit: contain;
}
.contact-row {
  gap: 0.6em;
} /* nech má tlačidlo trošku priestoru */

.moj-ucet .login .logout {
  height: 100%;
  fill: #fef35a;
  background: url("@/assets/images/icons/elipsaOdhlasenie.svg");
  padding-right: 0.6em;
  background-position: center;
  background-size: cover;
  margin-bottom: 0 !important;
  transition-duration: 0.2s;
}
.moj-ucet .login .logout:hover {
  transform: scale(1.1);
}
.moj-ucet .login .logout img {
  width: 0.95em;
  margin-right: 0;
}

.moj-ucet p {
  font-size: 0.8em;
}

@media only screen and (max-width: 1500px) {
  .moj-ucet {
    font-size: 90%;
  }
}
@media only screen and (max-height: 700px) {
  .logo img {
    width: 70%;
  }
}

@keyframes starFadeIn {
  from {
    opacity: 0;
    transform: translateY(6px) scale(0.9);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}
</style>
