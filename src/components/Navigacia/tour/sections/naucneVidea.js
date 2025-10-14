// src/components/Navigacia/tour/sections/naucneVidea.js
import { steps as zapisySteps, branch as zapisyBranch } from "./ciselneZapisy";
import {
  steps as classroomSteps,
  branch as classroomBranch,
} from "./mojaUcebna";
export function steps() {
  const sel = {
    heading: "[data-tour='naucne-heading']",
    subscription: "[data-tour='cta-subscription']",
    freePreview: "[data-tour='cta-free']",
    video: "[data-tour='lesson-video'], video[controls]",
    markComplete: "[data-tour='mark-complete']",
    lyricsToggle: "[data-tour='lyrics-toggle']",
    onlineDifficulty: "[data-tour='mojekurzy-difficulty']",
    onlineSongsHeading: "[data-tour='mojekurzy-songs-heading']",
  };

  return [
    // LIST videí
    {
      goto: "/naucne-videa",
      selector: sel.heading,
      title: "Náučné videá",
      text: "Tu nájdete všetky informácie o online kurzoch a ich obsahu.",
      side: "left",
      pad: 24,
      radius: 26,
    },
    {
      selector: sel.subscription,
      title: "Predplatné",
      text: "Zakúpte si prístup ku kompletnej knižnici videí.",
      side: "top",
      pad: { x: 22, y: 18 },
      radius: 18,
    },
    {
      selector: sel.freePreview,
      title: "Video zadarmo",
      text: "Pozrite si ukážkové video bez záväzkov.",
      side: "top",
      pad: { x: 22, y: 18 },
      radius: 18,
    },

    // DETAIL videa (zadarmo)
    {
      goto: "/ucebna/zadarmo-video",
      selector: sel.video,
      waitFor: 800, // nech sa DOM dotiahne
      closest: "video, .lesson-video, .plyr__video-wrapper, .video-wrapper",
      title: "Ukážkové video",
      text: "Spustite si ukážku, aby ste videli, ako prebieha lekcia.",
      side: "left",
      pad: 24,
      radius: 26,
    },

    // === OZNAČIŤ AKO ZVLÁDNUTÉ ===
    {
      goto: "/ucebna/zadarmo-video",
      selector: sel.markComplete,
      waitFor: 400,
      bind: { text: "Označiť ako zvládnuté" }, // (nájde to aj stav „Zvládnuté“ cez aria-label)
      closest: ".button, [data-tour='mark-complete']",
      title: "Označiť ako zvládnuté",
      text: "Keď máš hotovo, označ lekciu a sleduj progres.",
      side: "top",
      pad: { x: 22, y: 18 },
      radius: 18,
    },

    // === TEXT PIESNE ===
    {
      goto: "/ucebna/zadarmo-video",
      selector: sel.lyricsToggle,
      waitFor: 800, // nech sa dotiahne DOM
      // vyhľadá podľa textu na [role="button"] a [aria-label]
      bind: { text: "Text piesne" },
      // ring nasadíme na celý box
      closest: ".button, [data-tour='lyrics-toggle']",
      title: "Text piesne",
      text: "Zobraz si text skladby a precvičuj rovno s hudbou.",
      side: "top",
      pad: { x: 22, y: 18 },
      radius: 18,
    },

    // MENU
    {
      bind: { where: "menu", text: "Online výučba", hrefLike: "moje-kurzy" },
      title: "Menu: Online výučba",
      text: "Kedykoľvek sa vieš vrátiť späť k celej ponuke videí.",
      side: "left",
      pad: 18,
    },
    {
      goto: "/ucebna/moje-kurzy",
      selector: sel.onlineDifficulty,
      waitFor: 600,
      closest: ".box-item.oblubene.narocnost",
      title: "Vyber si náročnosť",
      text: "Tu si zvolíš, či chceš lekcie pre začiatočníkov, pokročilejších alebo profíkov.",
      side: "left",
      pad: 24,
      radius: 24,
    },
    {
      selector: sel.onlineSongsHeading,
      title: "Zoznam piesní v kurze",
      text: "Podľa zvolenej úrovne sa ti tu zobrazia skladby pripravené na štúdium.",
      side: "top",
      pad: { x: 22, y: 18 },
      radius: 14,
      scrollMode: "nearest",
      scrollInline: "nearest",
    },
  ];
}

export function branch(ctx = {}) {
  const hasCompleted =
    typeof ctx.hasCompleted === "function" ? ctx.hasCompleted : () => false;
  const zapisyDone = hasCompleted("zapisy");
  const classroomDone = hasCompleted("ucebna");

  const options = [];
  let text = "Vyber si, ako chceš pokračovať v ďalšom kroku.";
  let planBridgeLabel = "Číselné zápisy";

  if (!zapisyDone) {
    options.push({
      label: "Pokračovať na Číselné zápisy",
      goto: "/ciselne-zapisy",
      name: "zapisy",
      planLabel: "Číselné zápisy",
      steps: zapisySteps,
      branch: zapisyBranch,
      planBridgeLabel: "Číselné zápisy",
    });
    text = "Náučné videá máš splnené. Pokračuj na číselné zápisy.";
    planBridgeLabel = "Číselné zápisy";
  } else if (!classroomDone) {
    options.push({
      label: "Pokračovať do Mojej učebne",
      goto: "/ucebna",
      name: "ucebna",
      planLabel: "Moja učebňa",
      steps: classroomSteps,
      branch: classroomBranch,
      planBridgeLabel: "Moja učebňa",
    });
    text =
      "Skvelá práca! Zápisy aj videá máš hotové, pokračuj do Mojej učebne.";
    planBridgeLabel = "Moja učebňa";
  } else {
    options.push({
      label: "Dokončiť prehliadku",
      planLabel: "Hotovo",
      steps: [],
    });
    text = "Všetko máš prejdené. Prehliadku môžeš ukončiť.";
    planBridgeLabel = "Hotovo";
  }

  return {
    title: "Skvelé! Náučné videá máš prejdené 🎉",
    text,
    planBridgeLabel,
    options,
  };
}
