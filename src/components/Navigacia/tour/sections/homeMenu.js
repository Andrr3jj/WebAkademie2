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
      title: "🔎 Zistiť viac",
      text:
        "Zaujíma ťa, čo je Heligónková Akadémia a ako funguje? Klikni sem a dozvieš sa, ako ťa naučíme hrať na heligónke od úplných základov až po majstrovské kúsky.",
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
      title: "🎁 Vyskúšať zadarmo",
      text:
        "Chceš si to najprv len vyskúšať? Pozri si ukážkové video a zisti, že hra na heligónke je jednoduchšia, než si myslíš.",
      side: "bottom",
      pad,
    },

    // --- dlaždice na home ---
    {
      bind: { text: "Náučné videá" },
      closest: ".box-main",
      title: "🎬 Náučné videá",
      text:
        "Tu nájdeš videonávody na konkrétne piesne aj techniku hrania. Ideálne miesto, ak sa chceš zlepšovať podľa krokov s Andrejom a lektormi Akadémie.",
      side: "top",
      pad: { top: 30, right: 26, bottom: 26, left: 26 },
      radius: 34,
    },
    {
      bind: { text: "Číselné zápisy", hrefLike: "cisel" },
      closest: ".box-main",
      title: "🎵 Číselné zápisy",
      text:
        "Objav našu unikátnu formu zápisu pre heligónku – jednoduché čísla namiesto nôt. Vyber si pieseň, ktorú máš rád, a nauč sa ju hrať podľa prehľadného zápisu.",
      side: "top",
      pad: [26, 24],
      radius: 34,
    },
    {
      bind: { text: "HeliShop", hrefLike: "helishop" },
      closest: ".box-main",
      title: "🛍️ HeliShop",
      text:
        "Všetko pre heligónkara na jednom mieste – knihy, tričká, vaky, darčeky či doplnky. Nakúp si, čo potrebuješ na svoje hudobné dobrodružstvo.",
      side: "top",
      pad: 26,
      radius: 34,
    },
    {
      bind: { text: "HeliFest", hrefLike: "helifest" },
      closest: ".box-main",
      title: "🎉 HeliFest",
      text:
        "Festival heligónkarov, kde sa stretávame, hráme, spievame a zabávame. Zisti, kedy a kde sa bude konať najbližší ročník.",
      side: "top",
      pad: 26,
      radius: 34,
    },

    // --- MENU položky (vpravo) ---
    {
      bind: { where: "menu", text: "Texty piesní", hrefLike: "spevnik" },
      title: "📜 Menu: Texty piesní",
      text: "Ak si rád zaspievaš, tu nájdeš texty k obľúbeným piesňam. Spoj hudbu so spevom a uži si to naplno.",
      side: "left",
      pad: 10,
    },
    {
      bind: { where: "menu", text: "O nás", hrefLike: "o-nas" },
      title: "👨‍🏫 Menu: O nás",
      text:
        "Zisti, kto stojí za Heligónkovou Akadémiou a prečo to celé robíme. Poznaj ľudí, ktorí ťa sprevádzajú na tvojej hudobnej ceste.",
      side: "left",
      pad: 10,
    },
    {
      bind: { where: "menu", text: "Košík", hrefLike: "kosik" },
      title: "🛒 Menu: Košík",
      text:
        "Tu nájdeš všetky svoje vybrané zápisy, videá a produkty pripravené na nákup. Stačí dokončiť objednávku a môžeš hneď pokračovať v hraní.",
      side: "left",
      pad: 10,
    },
    {
      bind: { where: "menu", text: "Pomoc", hrefLike: "pomoc" },
      title: "🆘 Menu: Pomoc",
      text:
        "Ak si niekde nevieš rady, napíš nám. Radi ti pomôžeme s prihlásením, platbou alebo čímkoľvek iným.",
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
      key: "zapisy",
      name: "zapisy",
      planLabel: "Číselné zápisy",
      steps: zapisySteps,
      branch: zapisyBranch,
      planBridgeLabel: "Náučné videá",
    },
    {
      label: "Náučné videá",
      goto: "/naucne-videa",
      key: "video",
      name: "video",
      planLabel: "Náučné videá",
      steps: videoSteps,
      branch: videoBranch,
      planBridgeLabel: "Číselné zápisy",
    },
  ],
};
