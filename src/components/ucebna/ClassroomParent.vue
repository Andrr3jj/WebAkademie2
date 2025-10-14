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
          <PlatbaZaVyucbu @open-modal="openPaymentModal" />
          <HodnotenieEdupage @open-modal="openReviewModal" />
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
              @zobraz-vsetko="otvorDetailPoslednejHodiny(vyzva)"
            />
          </div>
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

          <PlatbaZaVyucbu @open-modal="openPaymentModal" />

          <HodnotenieEdupage @open-modal="openReviewModal" />

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

<script setup>
import { onMounted, computed } from "vue";
import { useStore } from "vuex";
import { useClassroom } from "@/composables/useClassroom";

import AchievementReminder from "@/components/ucebna/AchievementReminder.vue";
import ClassroomNavigation from "@/components/ucebna/ClassroomNavigation.vue";
import ClassroomProfilePhoto from "@/components/ucebna/ClassroomProfilePhoto.vue";
import AchivementsReminderMobile from "@/components/ucebna/AchivementsReminderMobile.vue";
import ModalSkeleton from "@/components/Spevnik/modal/ModalSkeleton.vue";
import HodnotenieEdupage from "./HodnotenieEdupage.vue";
import LessonStart from "./LessonStart.vue";
import StudentCalendar from "./StudentCalendar.vue";
import PlatbaZaVyucbu from "./PlatbaZaVyucbu.vue";

const store = useStore();

const userRole = computed(() =>
  (store.state.user?.role ?? "student").toString()
);

const {
  state,
  spustiOdmenu,
  presmerujNaUlohy,
  closeModal,
  nacitajVsetkyVyzvy,
  openReviewModal,
  openPaymentModal,
} = useClassroom(store);

function ensureLocalPoslednaHodina() {
  const hasPosledna = state.achievements?.some((a) => {
    const t = (a?.typ || "").toString().toLowerCase().trim();
    return (
      t === "posledna-hodina" ||
      t === "posledna_hodina" ||
      t === "poslednahodina"
    );
  });
  if (!hasPosledna) {
    state.achievements.unshift({
      id: "local-posledna-1",
      typ: "posledna-hodina",
      hodina: "2025-09-03",
      piesne: ["Na Kráľovej holi", "Valčík v G"],
      poznamka: "Precvičiť basovanie a prechody",
      iconName: "@/assets/images/vyzvy/posledna-hodina.png",
    });
  }
}

onMounted(async () => {
  await nacitajVsetkyVyzvy();
  ensureLocalPoslednaHodina();
});
</script>

<style lang="scss" scoped>
@import "@/assets/sass/_colors.scss";

.three-columns {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  gap: 2em;
  height: 90%;
  font-size: 70% !important;

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
