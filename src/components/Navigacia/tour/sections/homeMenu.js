// src/components/Navigacia/tour/sections/homeMenu.js
import { steps as zapisySteps, branch as zapisyBranch } from "./ciselneZapisy";
import { steps as videoSteps, branch as videoBranch } from "./naucneVidea";

export function steps() {
  const pad = 18;

  return [
    // 1) Zistiť viac
    {
      goto: "/",
      selector: [
        "a[href*='zistit-viac' i]",
        "a[href*='zisti' i]",
        "[data-qa*='zistit-viac' i]",
      ].join(","),
      bind: { text: "Zistiť viac", hrefLike: "zisti" },
      title: "Zistiť viac",
      text: "Čo je Akadémia a ako to funguje.",
      side: "bottom",
      pad,
    },
    // 2) Vyskúšať zadarmo
    {
      goto: "/",
      selector: [
        "a[href*='zadarmo' i]",
        "a[href*='vyskusat' i]",
        "a[href*='trial' i]",
        "button[href*='zadarmo' i]",
        "[data-gtm*='zadarmo' i]",
        "[data-qa*='zadarmo' i]",
        "[data-qa*='trial' i]",
      ].join(","),
      bind: { text: "Vyskúšať zadarmo", hrefLike: "zadarmo" },
      title: "Vyskúšať zadarmo",
      text: "Pozri si ukážkové video bez záväzkov.",
      side: "bottom",
      pad,
    },

    // --- dlaždice na home ---
    {
      bind: { text: "Náučné videá" },
      closest: ".box-main",
      title: "Náučné videá",
      text: "Všetky náučné videá nájdeš tu.",
      side: "top",
      pad: { top: 30, right: 26, bottom: 26, left: 26 },
      radius: 34,
    },
    {
      bind: { text: "Číselné zápisy", hrefLike: "cisel" },
      closest: ".box-main",
      title: "Číselné zápisy",
      text: "Noty/číselné zápisy pre heligónku.",
      side: "top",
      pad: [26, 24],
      radius: 34,
    },
    {
      bind: { text: "HeliShop", hrefLike: "helishop" },
      closest: ".box-main",
      title: "HeliShop",
      text: "Merch a doplnky.",
      side: "top",
      pad: 26,
      radius: 34,
    },
    {
      bind: { text: "HeliFest", hrefLike: "helifest" },
      closest: ".box-main",
      title: "HeliFest",
      text: "Informácie o festivale.",
      side: "top",
      pad: 26,
      radius: 34,
    },

    // --- MENU položky (vpravo) ---
    {
      bind: { where: "menu", text: "Texty piesní", hrefLike: "spevnik" },
      title: "Menu: Texty piesní",
      text: "Texty k piesňam.",
      side: "left",
      pad: 10,
    },
    {
      bind: { where: "menu", text: "O nás", hrefLike: "o-nas" },
      title: "Menu: O nás",
      text: "Kto sme a ako učíme.",
      side: "left",
      pad: 10,
    },
    {
      bind: { where: "menu", text: "Košík", hrefLike: "kosik" },
      title: "Menu: Košík",
      text: "Nákupný košík.",
      side: "left",
      pad: 10,
    },
    {
      bind: { where: "menu", text: "Pomoc", hrefLike: "pomoc" },
      title: "Menu: Pomoc",
      text: "Podpora a kontakt.",
      side: "left",
      pad: 10,
    },
  ];
}

export const branch = {
  title: "Skvelé! Domovská stránka je hotová 🎉",
  text: "Už vieš, čo je kde. Vyber si ďalšiu časť.",
  planBridgeLabel: "Ďalšia oblasť",
  options: [
    {
      label: "Číselné zápisy",
      goto: "/ciselne-zapisy",
      steps: zapisySteps,
      branch: zapisyBranch,
      planBridgeLabel: "Náučné videá",
    },
    {
      label: "Náučné videá",
      goto: "/naucne-videa",
      steps: videoSteps,
      branch: videoBranch,
      planBridgeLabel: "Číselné zápisy",
    },
  ],
};
