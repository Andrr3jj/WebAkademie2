// src/components/Navigacia/tour/sections/ciselneZapisy.js
import { steps as videoSteps, branch as videoBranch } from "./naucneVidea";
import {
  steps as classroomSteps,
  branch as classroomBranch,
} from "./mojaUcebna";
import avatarLike from "@/assets/images/gallery/avatar-like.png";

export function steps() {
  return [
    // 1) ZOZNAM – veľký obsah so zápismi
    {
      goto: "/ciselne-zapisy",
      selector: ".computer.zapisy .scroll",
      closest: ".computer.zapisy",
      title: "Všetky dostupné zápisy",
      text:
        "Tu sa zobrazia všetky dostupné pesničky podľa vyhľadávania a filtrov. " +
        "Keď zmeníš vyhľadávanie alebo kategóriu, upraví sa práve tento zoznam.",
      side: "top",
      pad: { x: 24, y: 16 },
      radius: 18,
    },

    // 2) VYHĽADÁVAČ – len samotné INPUT pole
    {
      selector: ".computer.vyber .categories-search .search input",
      closest: ".computer.vyber .categories-search .search",
      title: "Vyhľadávanie piesní",
      text: "Sem napíš názov piesne alebo jeho časť. Zoznam dole sa priebežne filtruje podľa textu.",
      side: "bottom",
      pad: { x: 18, y: 10 },
      radius: 12,
    },

    // === LAYOUT: zobrazenie do kariet (grid)
    {
      selectors: ".computer.vyber .layouts > img:first-of-type",
      closest: ".computer.vyber",
      title: "Zobrazenie kariet",
      text: "Prepne zoznam piesní do kariet. Každá pieseň je v samostatnej kartičke s detailmi.",
      side: "top",
      pad: { x: 16, y: 10 },
      radius: 12,
    },

    // === LAYOUT: zobrazenie do zoznamu (rows)
    {
      selectors: ".computer.vyber .layouts > img:nth-of-type(2)",
      closest: ".computer.vyber",
      title: "Zobrazenie zoznamu",
      text: "Prepne piesne do riadkov pod seba. Prehľadné, ak chceš rýchlo prechádzať veľa položiek.",
      side: "top",
      pad: { x: 16, y: 10 },
      radius: 12,
    },

    // === DARČEK / KREDITY
    {
      selectors: ".computer.vyber .layouts .credit-toggle",
      closest: ".computer.vyber",
      title: "Kredity & darčeky",
      text: "Tu zistíš aktuálny počet bodov (kreditov). Za kredity si vieš odomykať zápisy zadarmo.",
      side: "top",
      pad: { x: 18, y: 12 },
      radius: 14,
    },

    // 6) KATEGÓRIE – všetky tlačidlá naraz (Polka, Valčík, …)
    {
      selectors: ".computer.vyber .categories .buttons .button",
      closest: ".computer.vyber",
      title: "Kategórie piesní",
      text:
        "Filtrovanie podľa žánrov: Polka, Valčík, Česká, Terchovská, Moderná, Vianočná. " +
        "Klikni na ľubovoľnú kategóriu a zoznam sa hneď upraví.",
      side: "top",
      pad: { x: 22, y: 12 },
      radius: 14,
    },

    // ─────────────────────────────────────────────────────────────
    // DOPLNENÉ – JEDEN ČÍSELNÝ ZÁPIS (prvá položka v zozname)
    // (bez `closest`, aby sa nevybral celý kontajner)
    // ─────────────────────────────────────────────────────────────

    // A) Názov piesne (funguje pre grid / row aj pre zakúpené/nezakúpené)
    {
      selector:
        ".computer.zapisy .scroll .zapis:first-of-type .nadpis p, " +
        ".computer.zapisy .scroll .zapis:first-of-type .img-box-zapis .cena p, " +
        ".computer.zapisy .scroll .zapis:first-of-type .name p",
      title: "Názov piesne",
      text: "Tu nájdeš názov piesne.",
      side: "top",
      pad: { x: 16, y: 10 },
      radius: 10,
    },

    // B) Kartička s detailmi (flip karta – obtiažnosť, stupnice atď.)
    {
      selector: ".computer.zapisy .scroll .zapis:first-of-type .img-box-zapis",
      title: "Detailná kartička",
      text: "Kartička sa vie otočiť a na zadnej strane zobrazí stupnice, obtiažnosť a ďalšie informácie.",
      side: "top",
      pad: { x: 16, y: 12 },
      radius: 14,
    },

    // C) Do košíka / Zakúpiť (grid aj row; vylúčime zvukovú ikonu)
    {
      selector:
        ".computer.zapisy .scroll .zapis:first-of-type .button .rozdeleny-button:not(.hrat-ukazku), " +
        ".computer.zapisy .scroll .zapis:first-of-type .akcia .rozdeleny-button:not(.hrat-ukazku)",
      title: "Kúpa zápisu",
      text: "Týmto tlačidlom pridáš zápis do košíka. Ak používaš kredity, zobrazí sa „Zakúpiť“.",
      side: "left",
      pad: { x: 16, y: 12 },
      radius: 12,
    },

    // D) Zvuková ukážka (ikonka reproduktora)
    {
      selector:
        ".computer.zapisy .scroll .zapis:first-of-type .button .hrat-ukazku, " +
        ".computer.zapisy .scroll .zapis:first-of-type .akcia .hrat-ukazku",
      title: "Zvuková ukážka",
      text: "Spustí krátku ukážku piesne.",
      side: "left",
      pad: { x: 16, y: 12 },
      radius: 12,
    },

    // E) Menu – Zoznam piesní
    {
      bind: { where: "menu", text: "Zoznam piesní", hrefLike: "moje-piesne" },
      title: "Tvoj zoznam piesní",
      text: "V menu nájdeš tlačidlo Zoznam piesní. Po nákupe alebo získaní pesničky sa sem pridajú tvoje číselné zápisy.",
      side: "left",
      pad: { x: 22, y: 12 },
      radius: 14,
    },

    // F) Podstránka /ucebna/moje-piesne
    {
      goto: "/ucebna/moje-piesne",
      selectors: [
        "section.computer.mobile .scroll h1",
        "section.computer.mobile .scroll h5",
      ],
      title: "Moje piesne",
      text: "Tu je tvoja osobná zbierka. Každý zakúpený alebo získaný zápis sa okamžite zobrazí v Zozname piesní.",
      side: "top",
      pad: { x: 24, y: 16 },
      radius: 18,
      waitFor: 480,
    },

    // G) Konkrétny vlastnený zápis
    {
      selector: "[data-tour='owned-songs-card']",
      title: "Toto je tvoja pesnička",
      text: "Každý zakúpený alebo získaný číselný zápis sa objaví práve tu v zozname tvojich piesní.",
      side: "top",
      pad: { x: 20, y: 14 },
      radius: 16,
      waitFor: 520,
    },

    // H) Tlačidlo Hrať na vlastnenom zápise
    {
      selector: "[data-tour='owned-songs-play']",
      title: "Spusti hranie",
      text: "Klikni na Hrať a otvoríš prehrávač s číselným zápisom pripraveným na cvičenie.",
      side: "left",
      pad: { x: 18, y: 12 },
      radius: 12,
      waitFor: 520,
    },
  ];
}

export function branch(ctx = {}) {
  const hasCompleted =
    typeof ctx.hasCompleted === "function" ? ctx.hasCompleted : () => false;
  const videoDone = hasCompleted("video");
  const classroomDone = hasCompleted("ucebna");

  const options = [];
  let text = "Ako chceš pokračovať? Vyber si ďalší krok.";
  let planBridgeLabel = "Náučné videá";

  if (!videoDone) {
    options.push({
      label: "Pokračovať na Náučné videá",
      goto: "/naucne-videa",
      name: "video",
      planLabel: "Náučné videá",
      steps: videoSteps,
      branch: videoBranch,
      planBridgeLabel: "Náučné videá",
    });
    text = "Číselné zápisy máš prejdené. Pokračuj na náučné videá.";
    planBridgeLabel = "Náučné videá";
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
    text = "Máme hotové videá aj zápisy. Poďme teraz do Mojej učebne.";
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
    title: "Skvelé, číselné zápisy máš prejdené",
    text,
    avatar: avatarLike,
    planBridgeLabel,
    options,
  };
}
