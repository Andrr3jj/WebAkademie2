<template>
  <div v-if="open" class="guide-overlay">
    <!-- úvod alebo medzikrok s avatarom -->
    <BetweenOverlay
      v-if="showIntro || isBetween"
      :variant="showIntro ? 'intro' : 'branch'"
      :avatar-url="avatarUrl"
      :visible-total="visibleTotal"
      :plan-items="planItems"
      :plan-summary="planSummary"
      :plan-hint="planHint"
      :title="between?.title"
      :text="between?.text"
      :options="between?.options || []"
      @start="startFromFirst"
      @choose="onChoose"
      @close="close"
    />

    <!-- klasické kroky (ring + tooltip) -->
    <StepOverlay v-else @close="close" />
  </div>
</template>

<script>
import { computed, onMounted, onBeforeUnmount, watch } from "vue";
import { useStore } from "vuex";
import {
  tour,
  enableGuestTour,
  disableGuestTour,
} from "./tour/tour.nologged.js";
import BetweenOverlay from "./tour/ui/BetweenOverlay.vue";
import StepOverlay from "./tour/ui/StepOverlay.vue";
import avatarGuideDefault from "@/assets/images/gallery/avatar-guide-tight.png";

export default {
  name: "GuideOverlay",
  components: { BetweenOverlay, StepOverlay },
  setup() {
    const store = useStore();
    const open = computed(() => tour.state.open);
    const steps = computed(() => tour.state.steps || []);
    const isLoggedIn = computed(() => !!store.state.user);

    const hasRealSteps = computed(
      () =>
        isLoggedIn.value && steps.value.some((step) => step?.type !== "intro")
    );

    const syncTourMode = (loggedIn) => {
      if (loggedIn) {
        disableGuestTour();
        return;
      }
      enableGuestTour();
      if (tour.state.open && tour.state.mode !== "between") tour.close();
    };

    watch(
      isLoggedIn,
      (loggedIn) => {
        syncTourMode(loggedIn);
      },
      { immediate: true }
    );

    // intro = prvý step je typu "intro" a sme na indexe 0
    const showIntro = computed(
      () =>
        open.value && steps.value[0]?.type === "intro" && tour.state.index === 0
    );

    // medzikrok po dokončení etapy
    const isBetween = computed(() => tour.state.mode === "between");
    const between = computed(() => tour.state.between || null);

    // v plániku nezapočítavame úvod
    const visibleTotal = computed(() => {
      if (!hasRealSteps.value) return 0;
      return steps.value[0]?.type === "intro"
        ? Math.max(0, steps.value.length - 1)
        : steps.value.length;
    });

    const PLAN_BLUEPRINT = [
      { label: "Domovská stránka", bridgeLabel: "Ďalšia oblasť" },
      { label: "Číselné zápisy", bridgeLabel: "Náučné videá" },
      { label: "Náučné videá", bridgeLabel: "Hotovo" },
      { label: "Hotovo", bridgeLabel: "" },
    ];

    const planItems = computed(() => {
      if (!hasRealSteps.value) return [];

      const rawStageIndex = Number(tour.state.stageIndex);
      const stageIndex = Math.max(
        0,
        Number.isFinite(rawStageIndex) ? rawStageIndex : 0
      );
      const isBetween = tour.state.mode === "between";
      const programStages = Array.isArray(tour.state.program?.stages)
        ? tour.state.program.stages
        : [];

      const minLength = PLAN_BLUEPRINT.length;
      const total = Math.max(minLength, programStages.length, stageIndex + 2);

      const baseItems = Array.from({ length: total }, (_, idx) => {
        const stage = programStages[idx] || null;
        const blueprint = PLAN_BLUEPRINT[idx] || {};

        const label =
          (typeof stage?.label === "string" && stage.label.trim()) ||
          (typeof stage?.name === "string" && stage.name.trim()) ||
          (typeof blueprint.label === "string" && blueprint.label.trim()) ||
          `Etapa ${idx + 1}`;

        const bridgeLabel =
          (typeof stage?.bridgeLabel === "string" &&
            stage.bridgeLabel.trim()) ||
          (typeof stage?.branch?.planBridgeLabel === "string" &&
            stage.branch.planBridgeLabel.trim()) ||
          (typeof blueprint.bridgeLabel === "string" &&
            blueprint.bridgeLabel.trim()) ||
          "";

        return {
          index: idx,
          label,
          bridgeLabel,
        };
      });

      const completedCount = Math.min(
        baseItems.length,
        stageIndex + (isBetween ? 1 : 0)
      );
      const activeIndex = Math.min(
        baseItems.length - 1,
        isBetween ? stageIndex + 1 : stageIndex
      );

      return baseItems.map((item, idx) => ({
        ...item,
        status:
          idx < completedCount
            ? "done"
            : idx === activeIndex
            ? "current"
            : "upcoming",
      }));
    });

    const planSummary = computed(() => {
      if (!planItems.value.length) return "";
      const labels = planItems.value
        .map((item) => item.label)
        .filter((label) => !!label);
      if (!labels.length) return "";
      return labels.join(" · ");
    });

    const planHint = computed(() => {
      const active = planItems.value.find((item) => item.status === "current");
      if (!active) return "";
      return `${active.index + 1}. ${active.label}`;
    });

    const avatarUrl = computed(() => {
      if (isBetween.value && between.value?.avatar) return between.value.avatar;
      if (
        typeof window !== "undefined" &&
        window.__haTourAvatar &&
        window.__haTourAvatar !== ""
      )
        return window.__haTourAvatar;
      return avatarGuideDefault;
    });

    const startFromFirst = async () => {
      const firstReal = steps.value[0]?.type === "intro" ? 1 : 0;
      await tour.goto(firstReal);
    };

    const onChoose = async (opt) => {
      await tour.betweenChoose(opt || null);
    };

    const close = () => tour.close();

    onMounted(() => {
      window.haTour = {
        start: (i = 0, opts = {}) => tour.start({ ...opts, startIndex: i }),
        stop: () => tour.close(),
        next: () => tour.next(),
        prev: () => tour.prev(),
        goto: (i = 0) => tour.goto(i),
        isOpen: () => tour.state.open,
      };
      window.__haTourReady = true;
      window.dispatchEvent(new CustomEvent("ha.tour.ready"));
    });
    onBeforeUnmount(() => {
      window.__haTourReady = false;
      window.haTour = undefined;
    });

    return {
      open,
      showIntro,
      isBetween,
      between,
      visibleTotal,
      planItems,
      planSummary,
      planHint,
      avatarUrl,
      startFromFirst,
      onChoose,
      close,
    };
  },
};
</script>

<style>
/* len root kontajner (zdieľajú ho obe podkomponenty) */
.guide-overlay {
  position: fixed;
  inset: 0;
  z-index: 3990;
  background: transparent;
}
</style>
