<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <div class="three-columns">
        <div class="column column-left">
          <ClassroomProfilePhoto :player="state.taskStarsData" />

          <StudentCalendar />

          <LessonStart />
        </div>

        <div class="column column-center">
          <RecordStats
            v-if="state.hodnotaPolka !== null && state.hodnotaValcik !== null"
            :sirkaPolka="state.sirkaPolka"
            :sirkaValcik="state.sirkaValcik"
            :hodnotaPolka="state.hodnotaPolka"
            :hodnotaValcik="state.hodnotaValcik"
          />

          <PlatbaZaVyucbu @open-modal="openPaymentModal" v-if="isParent" />

          <HodnotenieEdupage @open-modal="openReviewModal" />

          <FavoriteRecords
            :produktyOblubeneData="produktyOblubeneDataArr"
            :oblubenePesnickyID="state.oblubenePesnickyID"
          />
        </div>

        <div class="column column-right">
          <div class="teleported-achievements">
            <AchievementReminder
              v-for="vyzva in state.achievements"
              :key="vyzva.id"
              :ulohaData="vyzva"
              :role="userRole"
              @akcia="
                vyzva.typ === '7-dni' ? spustiOdmenu() : presmerujNaUlohy()
              "
              @zobraz-vsetko="openReviewModal"
            />
          </div>

          <FriendsList
            ref="friendsListRef"
            @open-search="() => openSearchModal(friendsListRef)"
          />

          <TimeStats :dobaHranie="state.dobaHranie" />
        </div>
      </div>
    </div>
    <div class="navigation">
      <ClassroomNavigation />
    </div>
  </section>
  <div class="mobile">
    <section>
      <div class="scroll">
        <div class="user-infos">
          <div class="profile-actions">
            <ClassroomProfilePhoto :player="state.taskStarsData" />

            <StudentCalendar />

            <LessonStart />
          </div>

          <RecordStats
            v-if="state.hodnotaPolka !== null && state.hodnotaValcik !== null"
            :sirkaPolka="state.sirkaPolka"
            :sirkaValcik="state.sirkaValcik"
            :hodnotaPolka="state.hodnotaPolka"
            :hodnotaValcik="state.hodnotaValcik"
          />

          <PlatbaZaVyucbu @open-modal="openPaymentModal" v-if="isParent" />

          <HodnotenieEdupage @open-modal="openReviewModal" />

          <FavoriteRecords
            :produktyOblubeneData="produktyOblubeneDataArr"
            :oblubenePesnickyID="state.oblubenePesnickyID"
          />
          <div class="pripomienky">
            <AchivementsReminderMobile
              v-for="vyzva in state.achievements"
              :key="vyzva.id"
              :ulohaData="vyzva"
              :role="userRole"
              @akcia="
                vyzva.typ === '7-dni' ? spustiOdmenu() : presmerujNaUlohy()
              "
            />
          </div>

          <FriendsList
            ref="friendsListRef"
            @open-search="() => openSearchModal(friendsListRef)"
          />

          <TimeStats :dobaHranie="state.dobaHranie" />
        </div>

        <div class="navigation">
          <ClassroomNavigation />
        </div>
      </div>
    </section>
  </div>

  <ModalSkeleton
    v-if="state.modalOpen"
    :content-component="state.modalComponent"
    :content-props="state.modalProps"
    @close="closeModal"
  />
</template>

<script>
import { ref, computed, onMounted } from "vue";
import { useStore } from "vuex";
import { useClassroom } from "@/composables/useClassroom";

import AchievementReminder from "@/components/ucebna/AchievementReminder.vue";
import ClassroomNavigation from "@/components/ucebna/ClassroomNavigation.vue";
import ClassroomProfilePhoto from "@/components/ucebna/ClassroomProfilePhoto.vue";
import RecordStats from "@/components/ucebna/RecordStats.vue";
import TimeStats from "@/components/ucebna/TimeStats.vue";
import AchivementsReminderMobile from "@/components/ucebna/AchivementsReminderMobile.vue";
import FriendsList from "@/components/ucebna/FriendsList.vue";
import ModalSkeleton from "@/components/Spevnik/modal/ModalSkeleton.vue";
import HodnotenieEdupage from "./HodnotenieEdupage.vue";
import FavoriteRecords from "./FavoriteRecords.vue";
import LessonStart from "./LessonStart.vue";
import StudentCalendar from "./StudentCalendar.vue";
import PlatbaZaVyucbu from "./PlatbaZaVyucbu.vue";

export default {
  name: "ClassroomStudent",
  components: {
    AchievementReminder,
    ClassroomNavigation,
    ClassroomProfilePhoto,
    RecordStats,
    TimeStats,
    AchivementsReminderMobile,
    FriendsList,
    ModalSkeleton,
    HodnotenieEdupage,
    FavoriteRecords,
    LessonStart,
    StudentCalendar,
    PlatbaZaVyucbu,
  },
  computed: {
    userInfo() {
      return this.$store?.state?.user || {};
    },
    isParent() {
      const u = this.userInfo || {};
      const role = (u.role || "").toString().toLowerCase();

      // kandidáti polí z rôznych verzií API
      let parentArr =
        u.edupage_registration_parent ??
        u.edupage_registration_parent_ids ??
        u.parent_links ??
        u.parents ??
        null;

      // ak príde string (napr. "[]"), skús ho parse-nuť
      if (typeof parentArr === "string") {
        try {
          const parsed = JSON.parse(parentArr);
          parentArr = parsed;
        } catch (e) {
          // nechaj ako je
        }
      }

      const byRole = role === "parent" || role === "rodic";

      // podporuj pole aj objekt s kľúčmi (ako vo výpise z logu)
      const byArray = Array.isArray(parentArr) && parentArr.length > 0;
      const byObject =
        !!parentArr &&
        typeof parentArr === "object" &&
        !Array.isArray(parentArr) &&
        Object.keys(parentArr).length > 0;

      // debug logy – vymaž, keď budeš spokojný
      console.log("[ClassroomStudent] user:", u);
      console.log("[ClassroomStudent] role:", role, "byRole:", byRole);
      console.log(
        "[ClassroomStudent] parentArr:",
        parentArr,
        "byArray:",
        byArray,
        "byObject:",
        byObject
      );

      return byRole || byArray || byObject;
    },
  },
  setup() {
    // neberieme props, aby nebol eslint no-unused-vars
    const store = useStore();

    const userRole = computed(() =>
      (store.state.user?.role ?? "student").toString()
    );

    const {
      state,
      openSearchModal,
      spustiOdmenu,
      presmerujNaUlohy,
      closeModal,
      produktyOblubeneDataArr,
      nacitajVsetkyVyzvy,
      openReviewModal,
      porovnajPomerPolkaValcik,
      celkoveHranie,
      nacitajAchievementsStats,
      oblubenePesnicky,
      nacitajPoslednySledovanyProdukt,
      nacitaj7DnovuVyzvu,
      statistikyPouzivatela,
      openPaymentModal,
    } = useClassroom(store);

    const friendsListRef = ref(null);

    onMounted(async () => {
      await Promise.allSettled([
        nacitajVsetkyVyzvy(),
        porovnajPomerPolkaValcik?.(),
        celkoveHranie?.(),
        nacitajAchievementsStats?.(),
        oblubenePesnicky?.(),
        nacitajPoslednySledovanyProdukt?.(),
        nacitaj7DnovuVyzvu?.(),
        statistikyPouzivatela?.(),
      ]);
    });

    return {
      userRole,
      state,
      openSearchModal,
      spustiOdmenu,
      presmerujNaUlohy,
      closeModal,
      produktyOblubeneDataArr,
      openReviewModal,
      openPaymentModal,
      friendsListRef,
    };
  },
};
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.three-columns {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 2em;
  font-size: 70% !important;
  height: 90%;

  .column {
    padding: 2em 1.5em;
    display: flex;
    flex-direction: column;
    gap: 1.5em;
  }

  .column-left {
    max-width: 46em;
    width: 100%;
    gap: 3em;
  }

  .column-center {
    max-width: 25em;
    width: 100%;
    gap: 6.7em;
  }

  .column-right {
    max-width: 40em;
    width: 100%;
    min-height: 30em;
    height: 65em;
  }

  .teleported-achievements {
    margin-right: -3em;
  }

  @media screen and (max-width: 1600px) {
    overflow-x: hidden;
    font-size: 60% !important;
    gap: 0;
  }

  @media screen and (max-width: 1024px) {
    font-size: 100% !important;
    flex-direction: column;
    gap: 2em;
    overflow-x: visible;

    .column {
      width: 100%;
      max-width: 100%;
      padding: 0;
    }

    .column-right {
      height: 30em;
      margin-bottom: 15em;
    }

    .teleported-achievements {
      margin-right: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
  }

  .column-center {
    align-items: center;
  }
}

.navigation {
  margin-top: -3em;
}

@media screen and (max-width: 1280px) {
  .navigation {
    margin-top: -4em;
  }

  .column-center {
    gap: 8.3em;
  }
}

@media screen and (max-width: 750px) {
  :deep(.base-modal) {
    height: auto !important;
  }
  .navigation {
    margin-top: 0;
  }

  .stats {
    display: flex;
    flex-direction: column;
    align-items: center;
  }

  .pripomienky {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
}
</style>
