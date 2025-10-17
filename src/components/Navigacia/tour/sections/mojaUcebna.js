// src/components/Navigacia/tour/sections/mojaUcebna.js
import avatarCelebration from "@/assets/images/gallery/avatar-like.png";

export function steps() {
  const waitShort = 520;
  const waitMedium = 640;
  const smoothScroll = {
    scrollMode: "nearest",
    scrollInline: "nearest",
    scrollBehavior: "smooth",
  };

  return [
    {
      goto: "/ucebna",
      selectors: [
        ".computer .column-center .time-stats-wrapper",
        ".mobile .stats .time-stats-wrapper",
      ],
      title: "⏱️ Čas výučby",
      text: "Tu uvidíš, koľko si už odohral. Každá minúta strávená učením sa premení na body do systému HeliČas.",
      side: "left",
      pad: { x: 26, y: 20 },
      radius: 20,
      waitFor: waitMedium,
      ...smoothScroll,
    },
    {
      selectors: [
        ".computer .column-center .statistika-wrapper",
        ".mobile .stats .statistika-wrapper",
      ],
      title: "📊 Štatistika zápisov",
      text: "Porovnaj si, koľko máš poliek, valčíkov či iných žánrov. Pomôže ti to udržiavať vyvážený repertoár.",
      side: "left",
      pad: { x: 24, y: 18 },
      radius: 18,
      waitFor: waitMedium,
      ...smoothScroll,
    },
    {
      selectors: [
        ".computer .column-left .friend-list",
        ".mobile .profile-actions .friend-list",
      ],
      title: "🧑‍🤝‍🧑 Heligónková partia",
      text: "Pozvi kamarátov a sledujte navzájom svoj pokrok. Spoločne sa motivujete a môžete sa aj trochu hecovať 😉.",
      side: "right",
      pad: { x: 26, y: 22 },
      radius: 22,
      waitFor: waitMedium,
      ...smoothScroll,
    },
    {
      selectors: [".computer .column-left .news-list", ".mobile .news-list"],
      title: "📰 Novinky na Akadémii",
      text: "Všetky nové piesne, videá alebo výzvy sa zobrazia tu. Máš tak vždy prehľad, čo pribudlo.",
      side: "right",
      pad: { x: 22, y: 18 },
      radius: 18,
      waitFor: waitMedium,
      ...smoothScroll,
    },
    {
      selectors: [
        ".computer .column-center .naposledy-sledovane",
        ".mobile .naposledy-sledovane",
      ],
      title: "▶️ Naposledy sledované",
      text: "Pokračuj presne tam, kde si naposledy skončil. Jedným klikom si spustíš posledné video alebo zápis.",
      side: "left",
      pad: { x: 24, y: 20 },
      radius: 20,
      waitFor: waitMedium,
      ...smoothScroll,
    },
    {
      selectors: [
        ".computer .column-right .teleported-achievements",
        ".mobile .pripomienky",
      ],
      title: "🏆 Výzvy a odmeny",
      text: "Získavaj body, plň úlohy a odmeň sa! Každá splnená výzva ťa posunie o krok bližšie k cieľu.",
      side: "left",
      pad: { x: 26, y: 22 },
      radius: 24,
      waitFor: waitMedium,
      ...smoothScroll,
    },
    {
      selectors: [
        ".teleported-achievements .vyzva-card.invite",
        ".pripomienky .vyzva-card.invite",
        ".teleported-achievements .vyzva-card",
      ],
      title: "👋 Pozvi priateľov",
      text: "Zdieľaj svoj pozývací odkaz a získaj odmenu za každého kamaráta, ktorý sa pridá. Hudba znie lepšie, keď ju hráme spolu.",
      side: "left",
      pad: { x: 22, y: 18 },
      radius: 18,
      waitFor: waitShort,
      ...smoothScroll,
    },
    {
      selectors: [
        ".teleported-achievements .vyzva-card.highlight",
        ".pripomienky .vyzva-card.highlight",
        ".teleported-achievements .vyzva-card",
      ],
      title: "📅 7-dňová výzva",
      text: "Plň úlohy každý deň a sleduj, ako sa plnia políčka. Po siedmich dňoch ťa čaká hlavná odmena!",
      side: "left",
      pad: { x: 22, y: 18 },
      radius: 18,
      waitFor: waitShort,
      ...smoothScroll,
    },
    {
      selectors: [
        ".computer .column-right .box-item.oblubene",
        ".mobile .oblubene-zapisy .box-item.oblubene",
      ],
      title: "❤️ Obľúbené zápisy",
      text: "Rýchly prístup k tvojim najobľúbenejším skladbám. Stačí kliknúť a hneď hráš.",
      side: "left",
      pad: { x: 26, y: 20 },
      radius: 20,
      waitFor: waitShort,
      ...smoothScroll,
    },
    {
      selectors: [".bottom-nav-wrapper .bottom-bar"],
      closest: ".bottom-nav-wrapper",
      title: "🧭 Navigácia na ďalšie podstránky",
      text: "Darčekové poukážky, predplatné, HeliČas, kniha aj nastavenia účtu – všetko nájdeš rýchlo v spodnej lište.",
      side: "top",
      pad: { x: 28, y: 24 },
      radius: 28,
      waitFor: waitShort,
      ...smoothScroll,
    },
  ];
}

export function branch() {
  return {
    title: "Moja učebňa je preskúmaná 🎉",
    text: "Máš hotovo! Pokračuj v cvičení alebo prehliadku pokojne ukonči.",
    avatar: avatarCelebration,
    planBridgeLabel: "Hotovo",
    options: [
      {
        label: "Dokončiť prehliadku",
        planLabel: "Hotovo",
        steps: [],
      },
    ],
  };
}
