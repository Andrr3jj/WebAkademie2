// src/components/Navigacia/tour/sections/mojaUcebna.js
import avatarCelebration from "@/assets/images/gallery/avatar-like.png";

export function steps() {
  const waitShort = 520;
  const waitMedium = 640;

  return [
    {
      goto: "/ucebna",
      selectors: [
        ".computer .column-center .time-stats-wrapper",
        ".mobile .stats .time-stats-wrapper",
      ],
      title: "Čas výučby",
      text: "Tu vidíš, koľko si už odohral. Minúty z náučných videí aj zápisov sa prepočítavajú na body do HeliČasu.",
      side: "left",
      pad: { x: 26, y: 20 },
      radius: 20,
      waitFor: waitMedium,
    },
    {
      selectors: [
        ".computer .column-center .statistika-wrapper",
        ".mobile .stats .statistika-wrapper",
      ],
      title: "Štatistika zápisov",
      text: "Porovnaj si, koľko máš poliek a valčíkov. Pomôže ti to držať rovnováhu v repertoári.",
      side: "left",
      pad: { x: 24, y: 18 },
      radius: 18,
      waitFor: waitMedium,
    },
    {
      selectors: [
        ".computer .column-left .friend-list",
        ".mobile .profile-actions .friend-list",
      ],
      title: "Heligónková partia",
      text: "Pozývaj kamarátov, sleduj ich aktivitu a porovnávajte si výsledky. Tlačidlo Pridať priateľa otvorí vyhľadávanie.",
      side: "right",
      pad: { x: 26, y: 22 },
      radius: 22,
      waitFor: waitMedium,
    },
    {
      selectors: [
        ".computer .column-left .news-list",
        ".mobile .news-list",
      ],
      title: "Novinky na Akadémii",
      text: "Každá nová pieseň alebo video sa objaví v prehľade noviniek. Máš tak prehľad o tom, čo pribudlo.",
      side: "right",
      pad: { x: 22, y: 18 },
      radius: 18,
      waitFor: waitMedium,
    },
    {
      selectors: [
        ".computer .column-center .naposledy-sledovane",
        ".mobile .naposledy-sledovane",
      ],
      title: "Naposledy sledované",
      text: "Posledné video si spustíš jediným klikom. Pokračuj presne tam, kde si minule skončil.",
      side: "left",
      pad: { x: 24, y: 20 },
      radius: 20,
      waitFor: waitMedium,
    },
    {
      selectors: [
        ".computer .column-right .teleported-achievements",
        ".mobile .pripomienky",
      ],
      title: "Výzvy a odmeny",
      text: "Tu nájdeš všetky aktívne úlohy, špeciálne kampane a pripomienky. Po splnení si vyzdvihni odmenu jedným klikom.",
      side: "left",
      pad: { x: 26, y: 22 },
      radius: 24,
      waitFor: waitMedium,
    },
    {
      selectors: [
        ".teleported-achievements .vyzva-card.invite",
        ".pripomienky .vyzva-card.invite",
        ".teleported-achievements .vyzva-card",
      ],
      title: "Pozvi priateľov",
      text: "Špeciálna výzva ti dá odmenu za každého kamoša, ktorého pozveš. Tlačidlo Pozvať skopíruje odkaz na zdieľanie.",
      side: "left",
      pad: { x: 22, y: 18 },
      radius: 18,
      waitFor: waitShort,
    },
    {
      selectors: [
        ".teleported-achievements .vyzva-card.highlight",
        ".pripomienky .vyzva-card.highlight",
        ".teleported-achievements .vyzva-card",
      ],
      title: "7-dňová výzva",
      text: "Plň úlohy každý deň a sleduj, ako sa plnia políčka. Po siedmich dňoch sa ti sprístupní hlavná odmena.",
      side: "left",
      pad: { x: 22, y: 18 },
      radius: 18,
      waitFor: waitShort,
    },
    {
      selectors: [
        ".computer .column-right .box-item.oblubene",
        ".mobile .oblubene-zapisy .box-item.oblubene",
      ],
      title: "Obľúbené zápisy",
      text: "Rýchlo si otvor svoje najobľúbenejšie piesne. Šípkami sa prepínaš medzi kartičkami a tlačidlo Hrať spustí zápis.",
      side: "left",
      pad: { x: 26, y: 20 },
      radius: 20,
      waitFor: waitShort,
    },
    {
      selectors: [
        ".bottom-nav-wrapper .bottom-bar",
      ],
      closest: ".bottom-nav-wrapper",
      title: "Navigácia na ďalšie podstránky",
      text: "Darčekové poukážky, predplatné, HeliČas, kniha aj nastavenia účtu sú vždy poruke v spodnej lište.",
      side: "top",
      pad: { x: 28, y: 24 },
      radius: 28,
      waitFor: waitShort,
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
