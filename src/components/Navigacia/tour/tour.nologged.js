// src/components/Navigacia/tour/tour.nologged.js
// Jednoduchý prepínač "mozgu" na guest režim (pre odhlásených).
// UI ani existujúce sekcie nemeníme – len dočasne upravíme metódu tour.start().

import { tour as baseTour } from "./tour.js";
import { steps as notLoggedSteps } from "./sections/notLogged";

let savedOriginalStart = null;

function cloneOptions(arr = []) {
  return (arr || []).map((o) => ({ ...o }));
}

/**
 * Guest štart – zobrazí iba BetweenOverlay z notLogged.js,
 * kým nie je používateľ prihlásený. Žiadny „plný“ flow sa nespustí.
 */
async function guestStart() {
  const n0 = (typeof notLoggedSteps === "function" ? notLoggedSteps() : [])[0];

  if (!n0 || n0.type !== "between") {
    // Fallback, keby sa niečo zmenilo v notLoggedSteps – nespadne to:
    return savedOriginalStart ? savedOriginalStart() : baseTour.start?.();
  }

  baseTour.state.open = true;
  baseTour.state.mode = "between";
  baseTour.state.between = {
    title: n0.title,
    text: n0.text,
    options: cloneOptions(n0.options),
  };
  baseTour.state.steps = [];
  baseTour.state.program = { stages: [{ name: "nologin", steps: [] }] };
  baseTour.state.stageIndex = 0;
  baseTour.state.index = 0;

  if (typeof window !== "undefined") {
    window.__haTourSteps = []; // pre kompatibilitu s tvojím UI
  }
}

/** Zapne guest mód (patchne start). Zavolaj na stránkach pre odhlásených. */
export function enableGuestTour() {
  if (!savedOriginalStart) {
    savedOriginalStart = baseTour.start?.bind(baseTour) || null;
  }
  baseTour.start = guestStart;
}

/** Vypne guest mód (vráti pôvodný start). Zavolaj na stránkach po prihlásení. */
export function disableGuestTour() {
  if (savedOriginalStart) {
    baseTour.start = savedOriginalStart;
    savedOriginalStart = null;
  }
}

/** Pre komfort – exportujeme aj aktuálny tour singleton (rovnaký ako v tour.js). */
export const tour = baseTour;
