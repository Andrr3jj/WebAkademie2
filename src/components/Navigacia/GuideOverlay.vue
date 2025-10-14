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
      { label: "Náučné videá", bridgeLabel: "Moja učebňa" },
      { label: "Moja učebňa", bridgeLabel: "Hotovo" },
      { label: "Hotovo", bridgeLabel: "" },
    ];

    const normalizeKey = (raw) =>
      String(raw || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/\p{Diacritic}/gu, "")
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/(^-|-$)/g, "");

    const stageKeyFrom = (stage, idx) => {
      if (!stage) return `stage-${idx + 1}`;
      if (stage.key) return stage.key;
      if (stage.name) return stage.name;
      if (stage.label) return stage.label;
      return `stage-${idx + 1}`;
    };

    const baseLabelFor = (stage, idx) => {
      if (stage?.label && String(stage.label).trim()) return stage.label.trim();
      if (stage?.name && String(stage.name).trim()) return stage.name.trim();
      const fallback = PLAN_BLUEPRINT[idx]?.label;
      if (fallback && String(fallback).trim()) return fallback.trim();
      return `Etapa ${idx + 1}`;
    };

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

      const completedKeys = new Set(
        (Array.isArray(tour.state.completedStageKeys)
          ? tour.state.completedStageKeys
          : []
        )
          .map((key) => normalizeKey(key))
          .filter(Boolean)
      );

      const items = programStages.map((stage, idx) => {
        const key = normalizeKey(stageKeyFrom(stage, idx));
        const label = baseLabelFor(stage, idx);
        const bridgeLabel =
          (typeof stage?.bridgeLabel === "string" &&
            stage.bridgeLabel.trim()) ||
          (typeof stage?.branch?.planBridgeLabel === "string" &&
            stage.branch.planBridgeLabel.trim()) ||
          (typeof PLAN_BLUEPRINT[idx]?.bridgeLabel === "string" &&
            PLAN_BLUEPRINT[idx].bridgeLabel.trim()) ||
          "";

        const item = {
          key,
          index: idx,
          label,
          bridgeLabel,
          status: "upcoming",
        };

        if (completedKeys.has(key)) {
          item.status = "done";
        } else if (!isBetween && idx === stageIndex) {
          item.status = "current";
        }

        return item;
      });

      if (!items.length) {
        const fallbackLabel = baseLabelFor(null, 0);
        return [
          {
            key: normalizeKey(stageKeyFrom(null, 0)),
            index: 0,
            label: fallbackLabel,
            bridgeLabel:
              (typeof PLAN_BLUEPRINT[0]?.bridgeLabel === "string" &&
                PLAN_BLUEPRINT[0].bridgeLabel.trim()) ||
              "",
            status: "current",
          },
        ];
      }

      // During between screens add a placeholder tile for the upcoming area.
      if (isBetween) {
        const currentStage =
          programStages[Math.min(stageIndex, items.length - 1)] || null;
        const placeholderLabel =
          (typeof currentStage?.bridgeLabel === "string" &&
            currentStage.bridgeLabel.trim()) ||
          (typeof PLAN_BLUEPRINT[items.length]?.label === "string" &&
            PLAN_BLUEPRINT[items.length].label.trim()) ||
          "";

        if (placeholderLabel) {
          items.push({
            key: `placeholder-${items.length}`,
            index: items.length,
            label: placeholderLabel,
            bridgeLabel: "",
            status: "upcoming",
            placeholder: true,
          });
        }
      }

      return items;
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
      const active = planItems.value.find(
        (item) => item.status === "current" && !item.placeholder
      );
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
