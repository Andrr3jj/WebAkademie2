<template>
  <section :id="$route.name" class="computer">
    <div class="scroll">
      <div class="three-columns">
        <div class="column column-left">
          <ClassroomProfilePhoto :player="state.taskStarsData" />
          <div class="line horizontal"></div>
          <FriendsList
            ref="friendsListRef"
            @open-search="() => openSearchModal(friendsListRef.value)"
          />
          <NewsList />
        </div>

        <div class="column column-center">
          <TimeStats :dobaHranie="state.dobaHranie" />
          <RecordStats
            v-if="state.hodnotaPolka !== null && state.hodnotaValcik !== null"
            :sirkaPolka="state.sirkaPolka"
            :sirkaValcik="state.sirkaValcik"
            :hodnotaPolka="state.hodnotaPolka"
            :hodnotaValcik="state.hodnotaValcik"
          />

          <LastWatched :produkt="state.poslednySledovanyProdukt" />
        </div>

        <div class="column column-right">
          <div class="teleported-achievements">
            <AchievementReminder
              v-for="vyzva in state.achievements"
              :key="vyzva.id"
              :ulohaData="vyzva"
              @akcia="
                vyzva.typ === '7-dni' ? spustiOdmenu() : presmerujNaUlohy()
              "
            />
          </div>

          <FavoriteRecords
            :produktyOblubeneData="produktyOblubeneDataArr"
            :oblubenePesnickyID="state.oblubenePesnickyID"
          />
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
            <div class="line horizontal"></div>
            <FriendsList
              ref="friendsListRef"
              @open-search="() => openSearchModal(friendsListRef.value)"
            />
          </div>

          <div class="stats">
            <TimeStats :dobaHranie="state.dobaHranie" />
            <RecordStats
              :sirkaPolka="state.sirkaPolka"
              :sirkaValcik="state.sirkaValcik"
              :hodnotaPolka="state.hodnotaPolka"
              :hodnotaValcik="state.hodnotaValcik"
            />
          </div>

          <LastWatched :produkt="state.poslednySledovanyProdukt" />

          <div class="pripomienky">
            <AchivementsReminderMobile
              v-for="vyzva in state.achievements"
              :key="vyzva.id"
              :ulohaData="vyzva"
              @akcia="
                vyzva.typ === '7-dni' ? spustiOdmenu() : presmerujNaUlohy()
              "
            />
          </div>
        </div>

        <div class="oblubene-zapisy">
          <FavoriteRecords
            :produktyOblubeneData="produktyOblubeneDataArr"
            :oblubenePesnickyID="state.oblubenePesnickyID"
          />
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
import { ref, onMounted } from "vue";
import { useStore } from "vuex";
import { useClassroom } from "@/composables/useClassroom";

import AchievementReminder from "@/components/ucebna/AchievementReminder.vue";
import ClassroomNavigation from "@/components/ucebna/ClassroomNavigation.vue";
import ClassroomProfilePhoto from "@/components/ucebna/ClassroomProfilePhoto.vue";
import FavoriteRecords from "@/components/ucebna/FavoriteRecords.vue";
import NewsList from "@/components/ucebna/NewsList.vue";
import RecordStats from "@/components/ucebna/RecordStats.vue";
import TimeStats from "@/components/ucebna/TimeStats.vue";
import LastWatched from "@/components/ucebna/LastWatched.vue";
import AchivementsReminderMobile from "@/components/ucebna/AchivementsReminderMobile.vue";
import FriendsList from "@/components/ucebna/FriendsList.vue";
import ModalSkeleton from "@/components/Spevnik/modal/ModalSkeleton.vue";

const store = useStore();

const {
  state,
  produktyOblubeneDataArr,
  openSearchModal,
  spustiOdmenu,
  presmerujNaUlohy,
  closeModal,
  porovnajPomerPolkaValcik,
  celkoveHranie,
  nacitajAchievementsStats,
  nacitajVsetkyVyzvy,
  nacitaj7DnovuVyzvu,
  oblubenePesnicky,
  nacitajPoslednySledovanyProdukt,
  statistikyPouzivatela,
} = useClassroom(store);

onMounted(() => {
  nacitajVsetkyVyzvy();
  porovnajPomerPolkaValcik();
  celkoveHranie();
  nacitajAchievementsStats();
  oblubenePesnicky();
  nacitajPoslednySledovanyProdukt();
  nacitaj7DnovuVyzvu();
  statistikyPouzivatela();
});

const friendsListRef = ref(null);
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
    gap: 0;
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
