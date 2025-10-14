// src/components/Navigacia/tour/sections/naucneVidea.js
export function steps() {
  const sel = {
    heading: "[data-tour='naucne-heading']",
    subscription: "[data-tour='cta-subscription']",
    freePreview: "[data-tour='cta-free']",
    video: "[data-tour='lesson-video'], video[controls]",
    markComplete: "[data-tour='mark-complete']",
    lyricsToggle: "[data-tour='lyrics-toggle']",
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
  ];
}
