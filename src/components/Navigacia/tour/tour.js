// src/components/Navigacia/tour/tour.js
import { reactive, nextTick } from "vue";
import router from "@/router";

// === Sekcie (kroky) – prihlásený flow ===
import * as homeMenu from "./sections/homeMenu";
import { steps as zapisyFlowSteps } from "./sections/ciselneZapisy";
import { steps as videoFlowSteps } from "./sections/naucneVidea";
import { steps as classroomFlowSteps } from "./sections/mojaUcebna";

// === Guest (nologged) – prvý krok musí byť type: "between" ===
import { steps as notLoggedSteps } from "./sections/notLogged";

/** ===============================
 *  Globálny stav
 * ================================ */
const state = reactive({
  open: false,
  index: 0, // index v rámci "splattených" krokov
  stageIndex: 0, // na ktorej etape (stage) sme
  steps: [], // aktuálne "splattené" kroky
  program: null, // { stages: [ {name, steps, branch?}, ... ] }
  completedStageKeys: [],

  // UI mód pre GuideOverlay prepínač
  mode: "steps", // 'steps' | 'between'
  between: null, // { title, text, options: [...] }
});

function firstRealIndex() {
  return state.steps[0]?.type === "intro" ? 1 : 0;
}

function isCountedStep(step, index) {
  if (!step) return false;
  if (index === 0 && step.type === "intro") return false;
  if (step.type === "intro") return false;
  return !step.__skipped;
}

function findNextAvailableIndex(fromIndex) {
  for (
    let i = Math.max(fromIndex + 1, firstRealIndex());
    i < state.steps.length;
    i++
  ) {
    if (isCountedStep(state.steps[i], i)) return i;
  }
  return null;
}

function findPrevAvailableIndex(fromIndex) {
  for (
    let i = Math.min(fromIndex - 1, state.steps.length - 1);
    i >= firstRealIndex();
    i--
  ) {
    if (isCountedStep(state.steps[i], i)) return i;
  }
  return null;
}

function normalizeStageKey(raw) {
  if (!raw) return "";
  return String(raw)
    .toLowerCase()
    .normalize("NFD")
    .replace(/\p{Diacritic}/gu, "")
    .replace(/[^a-z0-9]+/g, "-")
    .replace(/(^-|-$)/g, "");
}

function stageKeyFrom(stage) {
  if (!stage) return "";
  if (stage.key) return String(stage.key);
  if (stage.name) return normalizeStageKey(stage.name);
  if (stage.label) return normalizeStageKey(stage.label);
  return "";
}

function markStageCompleted(stage) {
  const key = stageKeyFrom(stage);
  if (!key) return;
  if (state.completedStageKeys.includes(key)) return;
  state.completedStageKeys = state.completedStageKeys.concat([key]);
}

function branchContext(stage = null) {
  const keys = Array.from(new Set(state.completedStageKeys));
  return {
    stage,
    completedKeys: keys,
    hasCompleted: (key) => {
      if (!key) return false;
      const normalized = normalizeStageKey(key);
      if (!normalized) return false;
      return keys.includes(normalized);
    },
  };
}

function resolveBranch(branch, stage = null) {
  if (!branch) return null;
  const ctx = branchContext(stage);
  const raw =
    typeof branch === "function"
      ? branch(ctx)
      : branch;
  if (!raw || typeof raw !== "object") return null;

  const planBridgeLabel =
    typeof raw.planBridgeLabel === "function"
      ? raw.planBridgeLabel(ctx)
      : raw.planBridgeLabel;
  const title = typeof raw.title === "function" ? raw.title(ctx) : raw.title;
  const text = typeof raw.text === "function" ? raw.text(ctx) : raw.text;
  const avatar =
    typeof raw.avatar === "function" ? raw.avatar(ctx) : raw.avatar;

  const optionsSource =
    typeof raw.options === "function" ? raw.options(ctx) : raw.options;
  const options = Array.isArray(optionsSource)
    ? optionsSource
        .map((opt) => {
          const resolved =
            typeof opt === "function" ? opt(ctx) : opt;
          if (!resolved || typeof resolved !== "object") return null;
          return { ...resolved };
        })
        .filter(Boolean)
    : [];

  return {
    ...raw,
    planBridgeLabel: planBridgeLabel || "",
    title,
    text,
    avatar,
    options,
  };
}

function markStepSkippedAt(index) {
  const step = state.steps[index];
  if (!step || step.type === "intro" || step.__skipped) return false;
  step.__skipped = true;
  if (typeof window !== "undefined" && Array.isArray(window.__haTourSteps)) {
    window.__haTourSteps[index] = step;
  }
  return true;
}

/** ===============================
 *  Helpery – routing, výber, text
 * ================================ */
function safePush(to) {
  if (!to) return Promise.resolve();
  const target =
    typeof to === "string"
      ? to.startsWith("/")
        ? { path: to }
        : { name: to }
      : to;
  const resolved = router.resolve(target);
  if (resolved?.matched?.length) return router.push(target);
  const home = router.resolve({ path: "/" });
  if (home?.matched?.length) return router.push({ path: "/" });
  return Promise.resolve();
}

function norm(s) {
  return String(s || "")
    .toLowerCase()
    .normalize("NFD")
    .replace(/\p{Diacritic}/gu, "")
    .replace(/\s+/g, " ")
    .trim();
}

function findMenuContainer() {
  const cands = [
    ".TheMenu",
    ".the-menu",
    ".menuRight",
    ".right-menu",
    ".Menu",
    ".menu",
    "aside[role='complementary']",
    "nav[aria-label*='menu']",
    "nav[role='navigation']",
  ];
  for (const sel of cands) {
    const el = document.querySelector(sel);
    if (el) return el;
  }
  // fallback: najpravácejší prvok
  const all = Array.from(document.querySelectorAll("nav,aside,div"));
  let best = null,
    bestLeft = -1;
  for (const el of all) {
    const r = el.getBoundingClientRect?.();
    if (!r || !r.width || !r.height) continue;
    if (r.left > bestLeft && r.left > window.innerWidth * 0.55) {
      best = el;
      bestLeft = r.left;
    }
  }
  return best;
}

function findInMenu({ text, hrefLike }) {
  const root = findMenuContainer() || document;
  if (hrefLike) {
    const byHref = root.querySelector(`a[href*="${hrefLike}"]`);
    if (byHref) return byHref;
  }
  if (text) {
    const want = norm(text);
    const nodes = root.querySelectorAll("a,button,[role='button'],li,div,span");
    for (const n of nodes) {
      const t = norm(n.textContent || n.getAttribute?.("aria-label") || "");
      if (t && t.includes(want)) return n;
    }
  }
  return null;
}

function resolveBind(bind) {
  if (!bind) return null;
  if (bind.where === "menu") return findInMenu(bind);
  if (bind.hrefLike) {
    const byHref = document.querySelector(`a[href*="${bind.hrefLike}"]`);
    if (byHref) return byHref;
  }
  if (bind.text) {
    const want = norm(bind.text);
    const nodes = document.querySelectorAll(
      "a,button,[role='button'],[aria-label]"
    );
    for (const n of nodes) {
      const t = norm(n.textContent || n.getAttribute?.("aria-label") || "");
      if (t && t.includes(want)) return n;
    }
  }
  return null;
}

/** ===============================
 *  Úvodný step – NEPOČÍTA SA ako krok 1
 * ================================ */
function makeIntroStep() {
  return {
    type: "intro",
    title: "Ahoj, som Andrej",
    text:
      "Krátko ťa prevediem stránkou, aby si hneď vedel, kde čo nájdeš. " +
      "Za dokončenie návodu získaš body použiteľné v Číselných zápisoch.",
  };
}

/** ===============================
 *  Normalizácia rámu (pad/size → pad+offset)
 * ================================ */
function toSides(pad) {
  if (pad == null) return null;
  if (typeof pad === "number")
    return { top: pad, right: pad, bottom: pad, left: pad };
  if (Array.isArray(pad)) {
    const [h, v] = pad;
    const hh = Number(h ?? 0);
    const vv = Number(v ?? hh);
    return { top: vv, right: hh, bottom: vv, left: hh };
  }
  if (typeof pad === "object") {
    if ("top" in pad || "right" in pad || "bottom" in pad || "left" in pad) {
      return {
        top: Number(pad.top ?? 0),
        right: Number(pad.right ?? 0),
        bottom: Number(pad.bottom ?? 0),
        left: Number(pad.left ?? 0),
      };
    }
    const xx = Number(pad.x ?? 0),
      yy = Number(pad.y ?? xx);
    return { top: yy, right: xx, bottom: yy, left: xx };
  }
  return null;
}

function normalizeFrame(step) {
  if (!step || step.__normalized) return step;
  const padSides = toSides(step.pad);
  const offset = { ...(step.offset || {}) };

  if (padSides) {
    const PAD0 = Math.min(
      padSides.top,
      padSides.right,
      padSides.bottom,
      padSides.left
    );
    const dxL = padSides.left - PAD0,
      dxR = padSides.right - PAD0;
    const dyT = padSides.top - PAD0,
      dyB = padSides.bottom - PAD0;
    offset.x = Number(offset.x || 0) - dxL;
    offset.y = Number(offset.y || 0) - dyT;
    offset.w = Number(offset.w || 0) + dxL + dxR;
    offset.h = Number(offset.h || 0) + dyT + dyB;
    step.pad = PAD0;
  } else if (typeof step.pad !== "number") {
    delete step.pad;
  }

  if (step.size && (step.size.w || step.size.h)) {
    offset.w = Number(offset.w || 0) + Number(step.size.w || 0);
    offset.h = Number(offset.h || 0) + Number(step.size.h || 0);
  }
  if (Object.keys(offset).length) step.offset = offset;

  step.__normalized = true;
  return step;
}

/** ===============================
 *  Dočasný binding + „lepenie“
 * ================================ */
const BOUND_ATTR = "data-tour-target";
const boundNow = new Set(); // všetky aktuálne „ciele“ (proxy aj real)
const proxyNodes = new Set(); // ktoré z nich sú proxy DIVy

function cleanupBindings() {
  for (const el of boundNow) {
    try {
      if (proxyNodes.has(el)) {
        el.parentNode && el.parentNode.removeChild(el);
        proxyNodes.delete(el);
      } else {
        el.removeAttribute(BOUND_ATTR);
      }
    } catch (e) {
      /* no-op, už odstránené */
    }
  }
  boundNow.clear();
}

// --- recalculácia pri scroll/resize (globálne) ---
let rafId = 0;
let watchersAttached = false;
function scheduleRecalc() {
  if (rafId) cancelAnimationFrame(rafId);
  rafId = requestAnimationFrame(() => {
    rafId = 0;
    recalcCurrentSpotlight();
  });
}
function recalcCurrentSpotlight() {
  if (!state.open || state.mode !== "steps") return;
  const s = state.steps[state.index];
  if (s && typeof s.__recalc === "function") s.__recalc();
}
function attachViewportWatchers() {
  if (watchersAttached) return;
  window.addEventListener("resize", scheduleRecalc, { passive: true });
  window.addEventListener("scroll", scheduleRecalc, {
    passive: true,
    capture: true,
  });
  watchersAttached = true;
}
function detachViewportWatchers() {
  if (!watchersAttached) return;
  window.removeEventListener("resize", scheduleRecalc);
  window.removeEventListener("scroll", scheduleRecalc, true);
  watchersAttached = false;
}

/**
 * Pre krok vytvorí „proxy“ cieľ s pevnou pozíciou:
 * - ak je `selectors:[...]` → union bbox všetkých nájdených prvkov
 * - inak `selector`/`bind` → bbox jedného prvku
 * Proxy sa udržuje „prilepené“ cez s.__recalc() pri scroll/resize.
 */
function bindStepAt(index) {
  const s = state.steps[index];
  if (!s || s.__bound) return;
  normalizeFrame(s);

  const key = `tour-${Date.now()}-${index}`;

  const createProxy = () => {
    const v = document.createElement("div");
    v.style.position = "fixed";
    v.style.pointerEvents = "none";
    v.style.background = "transparent";
    v.setAttribute(BOUND_ATTR, key);
    document.body.appendChild(v);
    boundNow.add(v);
    proxyNodes.add(v);
    s.selector = `[${BOUND_ATTR}="${key}"]`;
    s.__bound = true;
    if (Array.isArray(window.__haTourSteps)) window.__haTourSteps[index] = s;
    return v;
  };

  // 1) Skupina prvkov (selectors) → union bbox (iba VIDITEĽNÉ prvky)
  if (s.selectors) {
    const sels = Array.isArray(s.selectors) ? s.selectors : [s.selectors];

    const pickVisibleAll = (sel) =>
      Array.from(document.querySelectorAll(sel)).filter((el) => {
        const r = el.getBoundingClientRect?.();
        return r && r.width > 0 && r.height > 0;
      });

    if (!s.__scrollTarget) {
      const firstReal = sels
        .flatMap((sel) => pickVisibleAll(sel))
        .find((el) => el instanceof Element);
      if (firstReal) s.__scrollTarget = firstReal;
    }

    const getUnionRect = () => {
      const elements = sels.flatMap((sel) => pickVisibleAll(sel));
      let minL = Infinity,
        minT = Infinity,
        maxR = -Infinity,
        maxB = -Infinity;
      elements.forEach((el) => {
        const r = el.getBoundingClientRect?.();
        if (!r || !r.width || !r.height) return;
        minL = Math.min(minL, r.left);
        minT = Math.min(minT, r.top);
        maxR = Math.max(maxR, r.right);
        maxB = Math.max(maxB, r.bottom);
      });
      if (
        !isFinite(minL) ||
        !isFinite(minT) ||
        !isFinite(maxR) ||
        !isFinite(maxB)
      ) {
        return null;
      }
      return {
        left: minL,
        top: minT,
        width: Math.max(0, maxR - minL),
        height: Math.max(0, maxB - minT),
      };
    };

    const v = createProxy();
    const apply = () => {
      const rect = getUnionRect();
      if (!rect) return;
      v.style.left = `${rect.left}px`;
      v.style.top = `${rect.top}px`;
      v.style.width = `${rect.width}px`;
      v.style.height = `${rect.height}px`;
    };
    s.__recalc = apply;
    apply();
    requestAnimationFrame(apply);
    return;
  }

  // 2) Jeden prvok (selector/bind) → proxy sa lepí naň (preferuj VIDITEĽNÝ)
  const pickVisible = (nodes) => {
    for (const el of nodes || []) {
      const r = el?.getBoundingClientRect?.();
      if (r && r.width > 0 && r.height > 0) return el;
    }
    return null;
  };

  let target =
    resolveBind(s.bind) ||
    (s.selector ? document.querySelector(s.selector) : null);

  if ((!target || !(target instanceof Element)) && s.selector) {
    target = pickVisible(document.querySelectorAll(s.selector));
  } else if (target && s.selector) {
    const r = target.getBoundingClientRect?.();
    if (!r || !r.width || !r.height) {
      const alt = pickVisible(document.querySelectorAll(s.selector));
      if (alt) target = alt;
    }
  }
  if (!target) return;

  // ak je definované `closest`, vezmeme najbližšieho rodiča
  if (s.closest && typeof target.closest === "function") {
    const near = target.closest(s.closest);
    if (near) target = near;
  }

  if (target instanceof Element) {
    s.__scrollTarget = target;
  }

  const v = createProxy();
  const apply = () => {
    const r = target.getBoundingClientRect?.();
    if (!r || !r.width || !r.height) return;
    v.style.left = `${r.left}px`;
    v.style.top = `${r.top}px`;
    v.style.width = `${r.width}px`;
    v.style.height = `${r.height}px`;
  };
  s.__recalc = apply;
  apply();
  requestAnimationFrame(apply);
}

/** ===============================
 *  Program (etapy)
 * ================================ */
function buildProgram() {
  return {
    stages: [
      {
        key: "home",
        name: "home+menu",
        label: "Domovská stránka",
        bridgeLabel: homeMenu.branch?.planBridgeLabel || "",
        steps: homeMenu.steps(),
        branch: homeMenu.branch,
      },
    ],
  };
}

/** ===============================
 *  Vnútorná navigácia
 * ================================ */

// ⬇️ MINI utilita
const sleep = (ms) => new Promise((r) => setTimeout(r, ms));

async function goTo(i) {
  state.index = i;
  const s = state.steps[i];
  if (!s) return;

  if (s.goto) {
    await safePush(s.goto);
    await nextTick();
  }

  if (s.waitFor) {
    await sleep(Number(s.waitFor) || 0);
    await nextTick();
    // po čakaní skúsiť zaskrolovať na cieľ (ak je mimo viewportu)
    try {
      const target =
        resolveBind(s.bind) ||
        (s.selector ? document.querySelector(s.selector) : null);
      if (target && typeof target.scrollIntoView === "function") {
        const behavior =
          s.scrollBehavior && typeof s.scrollBehavior === "string"
            ? s.scrollBehavior
            : "auto";
        const block =
          s.scrollMode && typeof s.scrollMode === "string"
            ? s.scrollMode
            : "center";
        const inline =
          s.scrollInline && typeof s.scrollInline === "string"
            ? s.scrollInline
            : block === "nearest"
            ? "nearest"
            : "center";
        target.scrollIntoView({ behavior, block, inline });
      }
    } catch (e) {
      /* no-op */
    }
  }

  bindStepAt(i);
  scheduleRecalc();
}

function stageStartIndex(stageIdx) {
  const introOffset = state.steps[0]?.type === "intro" ? 1 : 0;
  let acc = introOffset;
  for (let i = 0; i < stageIdx; i++) {
    const stage = state.program.stages[i];
    acc += Array.isArray(stage?.steps) ? stage.steps.length : 0;
  }
  return acc;
}

function stepsForKey(key) {
  switch (key) {
    case "zapisy":
      return zapisyFlowSteps();
    case "video":
      return videoFlowSteps();
    case "ucebna":
      return classroomFlowSteps();
    default:
      return [];
  }
}

async function advanceStageIfNeeded() {
  const isLastStep = state.index === state.steps.length - 1;
  if (!isLastStep) return;

  const currentStage = state.program.stages[state.stageIndex];
  const stageStart = stageStartIndex(state.stageIndex);
  const stageEnd = stageStart + currentStage.steps.length - 1;
  const atEndOfThisStage = state.index === stageEnd;
  if (!atEndOfThisStage) return;

  markStageCompleted(currentStage);

  const branchConfig = resolveBranch(currentStage.branch, currentStage);
  if (branchConfig && branchConfig.options.length) {
    currentStage.bridgeLabel =
      branchConfig.planBridgeLabel || currentStage.bridgeLabel || "";

    state.mode = "between";
    state.between = {
      title:
        branchConfig.title ||
        currentStage.branch?.title ||
        "Skvelé! Domovská stránka je hotová 🎉",
      text:
        branchConfig.text ||
        currentStage.branch?.text ||
        "Už vieš, čo je kde. Vyber si ďalšiu časť.",
      avatar: branchConfig.avatar || currentStage.branch?.avatar || null,
      options: branchConfig.options.map((o) => ({ ...o })),
    };
    return;
  }

}

/** ===============================
 *  Verejné API
 * ================================ */
export const tour = {
  state,

  async start(opts = {}) {
    const { steps, startIndex = 0, showIntro = true } = opts;

    // --- GUEST (nologged) režim spínaný routerom cez window.__tourGuestEnabled
    if (typeof window !== "undefined" && window.__tourGuestEnabled) {
      const n0 = (
        typeof notLoggedSteps === "function" ? notLoggedSteps() : []
      )?.[0];
      if (n0 && n0.type === "between") {
        cleanupBindings();
        detachViewportWatchers();

        state.program = { stages: [{ key: "nologin", name: "nologin", steps: [] }] };
        state.stageIndex = 0;
        state.index = 0;
        state.open = true;
        state.mode = "between";
        state.between = {
          title: n0.title,
          text: n0.text,
          avatar: n0.avatar || null,
          options: (n0.options || []).map((o) => ({ ...o })),
        };
        state.completedStageKeys = [];
        if (typeof window !== "undefined") window.__haTourSteps = [];
        return; // ⬅️ nologged mód zobrazený, končíme
      }
    }
    // -------------------------------------------------------------------

    // bežný program – prihlásený flow
    state.program =
      steps && steps.length
        ? { stages: [{ name: "custom", steps: steps.slice() }] }
        : buildProgram();

    if (state.program?.stages?.length) {
      state.program.stages = state.program.stages.map((stage, idx) => {
        const key = stageKeyFrom(stage) || normalizeStageKey(`stage-${idx + 1}`);
        return { ...stage, key };
      });
    }

    cleanupBindings();
    detachViewportWatchers();

    state.stageIndex = 0;
    state.index = 0;
    state.open = true;
    state.mode = "steps";
    state.between = null;
    state.completedStageKeys = [];

    const firstStage = state.program.stages[0];
    const normalized = firstStage.steps.map((s) => normalizeFrame({ ...s }));

    const initialSteps = showIntro
      ? [makeIntroStep(), ...normalized]
      : normalized;

    state.steps = initialSteps;
    if (typeof window !== "undefined") {
      window.__haTourSteps = state.steps;
    }

    attachViewportWatchers();
    await goTo(Math.max(0, Math.min(startIndex, state.steps.length - 1)));
  },

  async next() {
    if (state.mode === "between") return;

    const current = state.steps[state.index];
    const shouldRunGotoAfter = current && !current.__skipped;
    const nextIndex = findNextAvailableIndex(state.index);
    if (nextIndex !== null) {
      await goTo(nextIndex);
      if (shouldRunGotoAfter && current?.gotoAfter) {
        await safePush(current.gotoAfter);
        await nextTick();
      }
      return;
    }

    if (shouldRunGotoAfter && current?.gotoAfter) {
      await safePush(current.gotoAfter);
      await nextTick();
    }

    await advanceStageIfNeeded();

    if (state.index === state.steps.length - 1 && state.mode !== "between") {
      this.close();
    }
  },

  async prev() {
    if (state.mode === "between") return;
    const prevIndex = findPrevAvailableIndex(state.index);
    if (prevIndex !== null) await goTo(prevIndex);
  },

  async goto(i = 0) {
    if (state.mode === "between") return;
    if (!state.steps.length) return;
    const firstReal = firstRealIndex();
    let target = Math.max(0, Math.min(i, state.steps.length - 1));
    if (target < firstReal && target !== 0) target = firstReal;
    if (!isCountedStep(state.steps[target], target)) {
      const forward = findNextAvailableIndex(target - 1);
      if (forward !== null) target = forward;
      else {
        const backward = findPrevAvailableIndex(target + 1);
        if (backward === null) return;
        target = backward;
      }
    }
    await goTo(target);
  },

  close() {
    state.open = false;
    cleanupBindings();
    detachViewportWatchers();
    state.mode = "steps";
    state.between = null;
  },

  // callback z BetweenOverlay (medzikrok po etape)
  async betweenChoose(opt) {
    if (!opt) {
      state.mode = "steps";
      state.between = null;
      return;
    }

    if (opt.goto) {
      await safePush(opt.goto);
      await nextTick();
    }

    const nextSteps =
      typeof opt.steps === "function"
        ? opt.steps()
        : opt.steps || stepsForKey(opt.to) || [];

    const normalizedNext = (nextSteps || []).map((s) =>
      normalizeFrame({ ...s })
    );

    const insertAt = state.steps.length; // index, kde začína nová etapa

    const newStageIndex = state.program.stages.length;
    const rawKey =
      opt.key || opt.name || opt.to || opt.planLabel || opt.label || "";
    const stageKey =
      normalizeStageKey(rawKey) || normalizeStageKey(`stage-${newStageIndex + 1}`);

    const stage = {
      key: stageKey,
      name: opt.name || opt.to || "branch",
      label: opt.planLabel || opt.label || opt.name || opt.to || "Ďalšia etapa",
      bridgeLabel: opt.planBridgeLabel || "",
      steps: normalizedNext,
    };

    if (opt.branch) {
      const branchSource =
        typeof opt.branch === "function" ? opt.branch : () => opt.branch;
      const resolvedBranch = resolveBranch(branchSource, stage);
      if (resolvedBranch) {
        stage.branch = branchSource;
        if (resolvedBranch.planBridgeLabel) {
          stage.bridgeLabel = resolvedBranch.planBridgeLabel;
        }
      }
    }

    state.program.stages.push(stage);
    state.steps = state.steps.concat(normalizedNext);
    if (typeof window !== "undefined") {
      window.__haTourSteps = state.steps;
    }

    state.mode = "steps";
    state.between = null;
    state.stageIndex += 1;

    attachViewportWatchers();

    if (!normalizedNext.length) {
      this.close();
      return;
    }

    const targetIndex = insertAt === 0 ? 0 : insertAt;
    await goTo(targetIndex);
  },

  async skipCurrentStep() {
    if (state.mode === "between") return false;
    if (!markStepSkippedAt(state.index)) return false;
    await this.next();
    return true;
  },

  // pre rýchly prístup
  presets: {
    homeMenuSteps: homeMenu.steps,
    zapisyFlowSteps,
    videoFlowSteps,
    classroomFlowSteps,
  },
};
