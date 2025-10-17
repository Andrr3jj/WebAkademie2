// src/components/Navigacia/tour/sections/ciselneZapisy.js
import { steps as videoSteps, branch as videoBranch } from "./naucneVidea";
import {
  steps as classroomSteps,
  branch as classroomBranch,
} from "./mojaUcebna";
import avatarLike from "@/assets/images/gallery/avatar-like.png";

export function steps() {
  const listingSections = [
    ".computer.zapisy",
    ".mobile .zapisy",
    ".zapisy.tablet",
  ];

  const listingRoots = [
    ".computer.zapisy .scroll",
    ".mobile .zapisy",
    ".zapisy.tablet .scroll",
  ];

  const firstCardRoots = listingRoots.map(
    (root) => `${root} .zapis:first-of-type`
  );

  const firstCardSelectors = (suffixes) =>
    firstCardRoots.flatMap((root) =>
      suffixes.map((suffix) => `${root}${suffix}`)
    );

  return [
    // 1) ZOZNAM – veľký obsah so zápismi
    {
      goto: "/ciselne-zapisy",
      selectors: listingSections,
      title: "🪗 Všetky dostupné zápisy",
      text: "Tu nájdeš celú ponuku piesní pripravených na hranie. Vyber si podľa nálady alebo kategórie – a objav stále nové melódie.",
      side: "top",
      pad: { x: 24, y: 16 },
      radius: 18,
    },

    // 2) VYHĽADÁVAČ – len samotné INPUT pole
    {
      selectors: [
        ".computer.vyber .categories-search .search input",
        ".mobile .vyber .categories-search .search input",
      ],
      title: "🔍 Vyhľadávanie piesní",
      text: "Napíš názov piesne alebo jeho časť a zoznam sa ti hneď prispôsobí. Ideálne, ak hľadáš niečo konkrétne.",
      side: "bottom",
      pad: { x: 18, y: 10 },
      radius: 12,
    },

    // === LAYOUT: zobrazenie do kariet (grid)
    {
      selectors: [
        ".computer.vyber .layouts > img:first-of-type",
        ".mobile .vyber .layouts > img:first-of-type",
      ],
      title: "🧩 Zobrazenie kariet",
      text: "Zoznam sa zobrazí vo forme kariet – každá pieseň má svoju vlastnú „kartičku“ s prehľadom detailov a tlačidlom Hrať.",
      side: "top",
      pad: { x: 16, y: 10 },
      radius: 12,
    },

    // === LAYOUT: zobrazenie do zoznamu (rows)
    {
      selectors: [
        ".computer.vyber .layouts > img:nth-of-type(2)",
        ".mobile .vyber .layouts > img:nth-of-type(2)",
      ],
      title: "📋 Zobrazenie zoznamu",
      text: "Ak chceš rýchlo prechádzať viac piesní naraz, prepni si pohľad na zoznam. Prehľadné a praktické.",
      side: "top",
      pad: { x: 16, y: 10 },
      radius: 12,
    },

    // === DARČEK / KREDITY
    {
      selectors: [
        ".computer.vyber .layouts .credit-toggle",
        ".mobile .vyber .layouts .credit-toggle",
      ],
      title: "💰 Kredity & darčeky",
      text: "Tu vidíš, koľko máš kreditov. Môžeš ich použiť na odomykanie zápisov zdarma alebo na zľavy pri nákupe.",
      side: "top",
      pad: { x: 18, y: 12 },
      radius: 14,
    },

    // 6) KATEGÓRIE – všetky tlačidlá naraz (Polka, Valčík, …)
    {
      selectors: [
        ".computer.vyber .categories .buttons .button",
        ".mobile .vyber .categories .buttons .button",
      ],
      title: "🎶 Kategórie piesní",
      text: "Zvoľ si, čo máš chuť hrať – Polku, Valčík, Českú, Terchovskú, Modernú či Vianočnú. Stačí kliknúť a zoznam sa hneď upraví.",
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
      selectors: firstCardSelectors([" .img-box-zapis .cena p", " .name p"]),
      title: "🏷️ Názov piesne",
      text: "Každá pieseň má tu svoj názov – klikni na ňu a otvorí sa ti detail s ďalšími možnosťami.",
      side: "top",
      pad: { x: 16, y: 10 },
      radius: 10,
    },

    // B) Kartička s detailmi (flip karta – obtiažnosť, stupnice atď.)
    {
      selectors: firstCardSelectors([" .img-box-zapis", ""]),
      title: "🔄 Detailná kartička",
      text: "Otoč kartičku a pozri si ďalšie informácie: stupnice, obtiažnosť či tipy na hranie.",
      side: "top",
      pad: { x: 16, y: 12 },
      radius: 14,
    },

    // C) Do košíka / Zakúpiť (grid aj row; vylúčime zvukovú ikonu)
    {
      selectors: firstCardSelectors([
        " .button .rozdeleny-button:not(.hrat-ukazku)",
        " .akcia .rozdeleny-button:not(.hrat-ukazku)",
      ]),
      title: "🛒 Kúpa zápisu",
      text: "Týmto tlačidlom si zápis pridáš do košíka. Ak máš dostatok kreditov, zobrazí sa možnosť „Získať zdarma“.",
      side: "left",
      pad: { x: 16, y: 12 },
      radius: 12,
    },

    // D) Zvuková ukážka (ikonka reproduktora)
    {
      selectors: firstCardSelectors([
        " .button .hrat-ukazku",
        " .akcia .hrat-ukazku",
      ]),
      title: "🔊 Zvuková ukážka",
      text: "Chceš si pieseň najprv vypočuť? Spusti krátku ukážku a uvidíš, či ti sadne do ucha.",
      side: "left",
      pad: { x: 16, y: 12 },
      radius: 12,
    },

    // E) Menu – Zoznam piesní
    {
      bind: { where: "menu", text: "Zoznam piesní", hrefLike: "moje-piesne" },
      title: "🎼 Tvoj zoznam piesní",
      text: "Tu máš všetky svoje zakúpené a získané zápisy. Každá tvoja pieseň pekne pokope – pripravená na hranie.",
      side: "left",
      pad: { x: 22, y: 12 },
      radius: 14,
      mobileMenu: "open",
      mobileMenuDelay: 260,
      waitFor: 220,
    },

    // F) Podstránka /ucebna/moje-piesne
    {
      goto: "/ucebna/moje-piesne",
      selectors: [
        "section.computer.mobile .scroll h1",
        "section.computer.mobile .scroll h5",
      ],
      title: "⭐ Moje piesne",
      text: "Tvoja osobná zbierka – tu sa objavia všetky skladby, ktoré si si pridal alebo odomkol.",
      side: "top",
      pad: { x: 24, y: 16 },
      radius: 18,
      waitFor: 480,
      mobileMenu: "close",
      mobileMenuDelay: 220,
    },

    // G) Konkrétny vlastnený zápis
    {
      selector: "[data-tour='owned-songs-card']",
      title: "❤️ Toto je tvoja pesnička",
      text: "Každý zápis, ktorý si už získal, sa zobrazí tu. Tvoje osobné miesto plné hudby.",
      side: "top",
      pad: { x: 20, y: 14 },
      radius: 16,
      waitFor: 520,
    },

    // H) Tlačidlo Hrať na vlastnenom zápise
    {
      selector: "[data-tour='owned-songs-play']",
      title: "▶️ Spusti hranie",
      text: "Klikni na **Hrať** a otvorí sa prehrávač s číselným zápisom pripraveným na cvičenie. Môžeš začať kedykoľvek.",
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
  const hasCompletedAny =
    typeof ctx.hasCompletedAny === "function"
      ? ctx.hasCompletedAny
      : (...keys) => {
          const list = [];
          keys.forEach((key) => {
            if (Array.isArray(key)) list.push(...key);
            else if (key != null) list.push(key);
          });
          return list.some((key) => hasCompleted(key));
        };
  const videoDone = hasCompletedAny(["video", "naucne-videa"]);
  const classroomDone = hasCompletedAny(["ucebna", "moja-ucebna"]);

  const options = [];
  let text = "Ako chceš pokračovať? Vyber si ďalší krok.";
  let planBridgeLabel = "Náučné videá";

  if (!videoDone) {
    options.push({
      label: "Pokračovať na Náučné videá",
      goto: "/naucne-videa",
      key: "video",
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
      key: "ucebna",
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
