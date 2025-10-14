import { createRouter, createWebHistory } from "vue-router";
// odporúčaná verzia s aliasom @
import store from "@/components/store";
import EventBlocker from "@/utils/EventBlocker";

function lazyLoad(view) {
  return () => import(`@/views/${view}.vue`);
}

const routes = [
  {
    path: "/",
    name: "Heligonková Akadémia",
    component: () => import("../views/DomovStranka.vue"),
    meta: {
      title:
        "Heligónková Akadémia - Moderný spôsob učenia sa hry na heligónku 🎶",
      description:
        "Online kurzy, číselné zápisy a výučbové materiály pre začiatočníkov aj pokročilých.",
      metaTags: [
        { property: "og:url", content: "https://heligonkovaakademia.sk" },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content:
            "Heligónková Akadémia - Moderný spôsob učenia sa hry na heligónku 🎶",
        },
        {
          property: "og:description",
          content:
            "Online kurzy, číselné zápisy a výučbové materiály pre začiatočníkov aj pokročilých.",
        },
        {
          property: "og:image",
          content:
            "https://heligonkovaakademia.sk/data/seo/SeoDomovskastranka.png",
        },
        { name: "twitter:card", content: "summary_large_image" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        { property: "twitter:url", content: "https://heligonkovaakademia.sk" },
        {
          name: "twitter:title",
          content:
            "Heligónková Akadémia - Moderný spôsob učenia sa hry na heligónku 🎶",
        },
        {
          name: "twitter:description",
          content:
            "Online kurzy, číselné zápisy a výučbové materiály pre začiatočníkov aj pokročilých.",
        },
        {
          name: "twitter:image",
          content:
            "https://heligonkovaakademia.sk/data/seo/SeoDomovskastranka.png",
        },
      ],
    },
  },
  {
    path: "/naucne-videa",
    name: "Náučné videá",
    component: () => import("../views/shop/NaucneVidea.vue"),
    meta: {
      title: "Heligónková Akadémia - Náučné videá",
      description:
        "Unikátna metóda hry na heligónke! Náučné videá pre začiatočníkov aj pokročilých.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/naucne-videa",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Náučné videá",
        },
        {
          property: "og:description",
          content:
            "Unikátna metóda hry na heligónke! Náučné videá pre začiatočníkov aj pokročilých.",
        },
        {
          property: "og:image",
          content: "https://heligonkovaakademia.sk/data/seo/SeoNaucnevidea.png",
        },
        { name: "twitter:card", content: "summary_large_image" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/naucne-videa",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Náučné videá",
        },
        {
          name: "twitter:description",
          content:
            "Unikátna metóda hry na heligónke! Náučné videá pre začiatočníkov aj pokročilých.",
        },
        {
          name: "twitter:image",
          content: "https://heligonkovaakademia.sk/data/seo/SeoNaucnevidea.png",
        },
      ],
    },
  },
  {
    path: "/ciselne-zapisy",
    name: "Číselné zápisy",
    component: () => import("../views/shop/CiselneZapisy.vue"),
    props: true,
    meta: {
      title:
        "Heligónková Akadémia - Všetky číselné zápisy pre heligónku na jednom mieste!",
      description:
        "Číselné zápisy piesní pre heligónku! Od ľudových melódií až po moderné skladby",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ciselne-zapisy",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content:
            "Heligónková Akadémia - Všetky číselné zápisy pre heligónku na jednom mieste",
        },
        {
          property: "og:description",
          content:
            "Číselné zápisy piesní pre heligónku! Od ľudových melódií až po moderné skladby",
        },
        {
          property: "og:image",
          content: "https://heligonkovaakademia.sk/data/seo/SeoNaucnevidea.png",
        },
        { name: "twitter:card", content: "summary_large_image" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ciselne-zapisy",
        },
        {
          name: "twitter:title",
          content:
            "Heligónková Akadémia - Všetky číselné zápisy pre heligónku na jednom mieste",
        },
        {
          name: "twitter:description",
          content:
            "Číselné zápisy piesní pre heligónku! Od ľudových melódií až po moderné skladby",
        },
        {
          name: "twitter:image",
          content: "https://heligonkovaakademia.sk/data/seo/SeoNaucnevidea.png",
        },
      ],
    },
  },
  {
    path: "/ucebna/ciselny-zapis",
    name: "Číselný zápis",
    component: () => import("../views/logged/ucebna/CiselnyZapis.vue"),
    meta: {
      title: "Heligonkovaakadémia - Číselný zápis",
      requiresAuth: true,
      description:
        "Ukážka číselného zápisu pre heligónku. Získaj prístup k výučbovým materiálom a pesničkám po prihlásení.",
    },
    props: true,
  },
  {
    path: "/miesta-vyucby",
    name: "Miesta výučby",
    component: () => import("../views/info/MiestaVyucby.vue"),
    meta: {
      title: "Heligónková Akadémia - Miesta výučby",
      description:
        "Kde vyučujeme heligónku? Prezrite si naše miesta prezenčnej výučby!",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/miesta-vyucby",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Miesta výučby",
        },
        {
          property: "og:description",
          content:
            "Kde vyučujeme heligónku? Prezrite si naše miesta prezenčnej výučby!",
        },

        { name: "twitter:card", content: "summary_large_image" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/miesta-vyucby",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Miesta výučby",
        },
        {
          name: "twitter:description",
          content:
            "Kde vyučujeme heligónku? Prezrite si naše miesta prezenčnej výučby!",
        },
      ],
    },
  },
  {
    path: "/zistit-viac",
    name: "Zistiť viac",
    component: () => import("../views/info/ZistitViac.vue"),
    meta: {
      title: "Heligónková Akadémia - Zisti viac",
      description:
        "Všetko, čo potrebuješ vedieť o našej výučbe, metódach a prístupe k učeniu heligónky.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/zistit-viac",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zisti viac",
        },
        {
          property: "og:description",
          content:
            "Všetko, čo potrebuješ vedieť o našej výučbe, metódach a prístupe k učeniu heligónky.",
        },

        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/zistit-viac",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zisti viac",
        },
        {
          name: "twitter:description",
          content:
            "Všetko, čo potrebuješ vedieť o našej výučbe, metódach a prístupe k učeniu heligónky.",
        },
      ],
    },
  },
  {
    path: "/helifest",
    name: "Helifest",
    component: () => import("../views/shop/HelifestStranka.vue"),
    meta: {
      title: "Heligónková Akadémia - HeliFest",
      description:
        "Festival plný heligónky! Spoznajte program, účastníkov a atmosféru nášho podujatia.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/helifest",
        },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - HeliFest" },
        {
          property: "og:description",
          content:
            "Festival plný heligónky! Spoznajte program, účastníkov a atmosféru nášho podujatia.",
        },

        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/helifest",
        },
        { name: "twitter:title", content: "Heligónková Akadémia - HeliFest" },
        {
          name: "twitter:description",
          content:
            "Festival plný heligónky! Spoznajte program, účastníkov a atmosféru nášho podujatia.",
        },
      ],
    },
  },
  {
    path: "/helishop",
    name: "Helishop",
    component: () => import("../views/shop/HelishopStranka.vue"),
    meta: {
      title: "Heligónková Akadémia - HeliShop",
      description: "Všetko pre heligónku na jednom mieste!",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/helishop",
        },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - HeliShop" },
        {
          property: "og:description",
          content: "Všetko pre heligónku na jednom mieste!",
        },

        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/helishop",
        },
        { name: "twitter:title", content: "Heligónková Akadémia - HeliShop" },
        {
          name: "twitter:description",
          content: "Všetko pre heligónku na jednom mieste!",
        },
      ],
    },
  },
  {
    path: "/o-nas",
    name: "O nás",
    component: () => import("../views/info/ONas.vue"),
    meta: {
      title: "Heligónková Akadémia - O nás",
      description:
        "Spoznajte náš tím, našu víziu a všetko, čo stojí za našou vášňou pre heligónku.",
      metaTags: [
        { property: "og:url", content: "https://heligonkovaakademia.sk/o-nas" },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - O nás" },
        {
          property: "og:description",
          content:
            "Spoznajte náš tím, našu víziu a všetko, čo stojí za našou vášňou pre heligónku.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/o-nas",
        },
        { name: "twitter:title", content: "Heligónková Akadémia - O nás" },
        {
          name: "twitter:description",
          content:
            "Spoznajte náš tím, našu víziu a všetko, čo stojí za našou vášňou pre heligónku.",
        },
      ],
    },
  },
  {
    path: "/o-nas/juraj-kvocka",
    name: "Juraj Kvocka",
    component: () => import("../views/about/JurajKvocka.vue"),
    meta: {
      title: "Heligónková Akadémia - Juraj Kvocka",
      description:
        "Zoznámte sa s Jurajom Kvockom, lektorom a vášnivým hráčom na heligónku, ktorý stojí pri zrode Akadémie.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/o-nas/juraj-kvocka",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Juraj Kvocka",
        },
        {
          property: "og:description",
          content:
            "Zoznámte sa s Jurajom Kvockom, lektorom a vášnivým hráčom na heligónku, ktorý stojí pri zrode Akadémie.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/o-nas/juraj-kvocka",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Juraj Kvocka",
        },
        {
          name: "twitter:description",
          content:
            "Zoznámte sa s Jurajom Kvockom, lektorom a vášnivým hráčom na heligónku, ktorý stojí pri zrode Akadémie.",
        },
      ],
    },
  },
  {
    path: "/o-nas/andrej-trnka",
    name: "Andrej Trnka",
    component: () => import("../views/about/AndrejTrnka.vue"),
    meta: {
      title: "Heligónková Akadémia - Andrej Trnka",
      description:
        "Spoznajte Andreja Trnku, člena nášho tímu, ktorý svojou vášňou pre heligónku inšpiruje ďalších hráčov.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/o-nas/andrej-trnka",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Andrej Trnka",
        },
        {
          property: "og:description",
          content:
            "Spoznajte Andreja Trnku, člena nášho tímu, ktorý svojou vášňou pre heligónku inšpiruje ďalších hráčov.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/o-nas/andrej-trnka",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Andrej Trnka",
        },
        {
          name: "twitter:description",
          content:
            "Spoznajte Andreja Trnku, člena nášho tímu, ktorý svojou vášňou pre heligónku inšpiruje ďalších hráčov.",
        },
      ],
    },
  },
  // {
  //   path: "/o-nas/matus-mahut",
  //   name: "Matúš Mahút",
  //   component: () => import("../views/about/MatusMahut.vue"),
  // },
  {
    path: "/o-nas/libor-hlinik",
    name: "Libor Hliník",
    component: () => import("../views/about/LiborHlinik.vue"),
    meta: {
      title: "Heligónková Akadémia - Libor Hliník",
      description:
        "Zoznámte sa s Liborom Hliníkom – hudobníkom, ktorý svojím nadšením a skúsenosťami obohacuje náš tím.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/o-nas/libor-hlinik",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Libor Hliník",
        },
        {
          property: "og:description",
          content:
            "Zoznámte sa s Liborom Hliníkom – hudobníkom, ktorý svojím nadšením a skúsenosťami obohacuje náš tím.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/o-nas/libor-hlinik",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Libor Hliník",
        },
        {
          name: "twitter:description",
          content:
            "Zoznámte sa s Liborom Hliníkom – hudobníkom, ktorý svojím nadšením a skúsenosťami obohacuje náš tím.",
        },
      ],
    },
  },
  {
    path: "/o-nas/matej-kondela",
    name: "Matej Kondela",
    component: () => import("../views/about/MatejKondela.vue"),
    meta: {
      title: "Heligónková Akadémia - Matej Kondela",
      description:
        "Zoznámte sa s Matejom Kondelom – mladým talentom a nadšencom heligónky, ktorý vnáša do Akadémie sviežu energiu.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/o-nas/matej-kondela",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Matej Kondela",
        },
        {
          property: "og:description",
          content:
            "Zoznámte sa s Matejom Kondelom – mladým talentom a nadšencom heligónky, ktorý vnáša do Akadémie sviežu energiu.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/o-nas/matej-kondela",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Matej Kondela",
        },
        {
          name: "twitter:description",
          content:
            "Zoznámte sa s Matejom Kondelom – mladým talentom a nadšencom heligónky, ktorý vnáša do Akadémie sviežu energiu.",
        },
      ],
    },
  },
  {
    path: "/pomoc",
    name: "Pomoc",
    component: () => import("../views/info/PomocStranka.vue"),
    meta: {
      title: "Heligónková Akadémia - Pomoc",
      description:
        "Potrebujete poradiť? Tu nájdete odpovede na najčastejšie otázky a návody, ako používať našu platformu.",
      metaTags: [
        { property: "og:url", content: "https://heligonkovaakademia.sk/pomoc" },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - Pomoc" },
        {
          property: "og:description",
          content:
            "Potrebujete poradiť? Tu nájdete odpovede na najčastejšie otázky a návody, ako používať našu platformu.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/pomoc",
        },
        { name: "twitter:title", content: "Heligónková Akadémia - Pomoc" },
        {
          name: "twitter:description",
          content:
            "Potrebujete poradiť? Tu nájdete odpovede na najčastejšie otázky a návody, ako používať našu platformu.",
        },
      ],
    },
  },
  {
    path: "/obchodne-podmienky",
    name: "Obchodné Podmienky",
    component: () => import("../views/info/ObchodnePodmienky.vue"),
    meta: {
      title: "Heligónková Akadémia - Obchodné podmienky",
      description:
        "Prečítajte si naše aktuálne obchodné podmienky a získajte prehľad o pravidlách našich služieb.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/obchodne-podmienky",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Obchodné podmienky",
        },
        {
          property: "og:description",
          content:
            "Prečítajte si naše aktuálne obchodné podmienky a získajte prehľad o pravidlách našich služieb.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/obchodne-podmienky",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Obchodné podmienky",
        },
        {
          name: "twitter:description",
          content:
            "Prečítajte si naše aktuálne obchodné podmienky a získajte prehľad o pravidlách našich služieb.",
        },
      ],
    },
  },
  {
    path: "/uspesne-odoslanie-formularu",
    name: "Úspešné odoslanie formuláru",
    component: () => import("../views/others/UspesneOdoslanieFormularu.vue"),
    meta: {
      title: "Heligónková Akadémia - Úspešné odoslanie formuláru",
      description: "Ďakujeme za vašu správu! Čoskoro sa vám ozveme späť.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/uspesne-odoslanie-formularu",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Úspešné odoslanie formuláru",
        },
        {
          property: "og:description",
          content: "Ďakujeme za vašu správu! Čoskoro sa vám ozveme späť.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/uspesne-odoslanie-formularu",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Úspešné odoslanie formuláru",
        },
        {
          name: "twitter:description",
          content: "Ďakujeme za vašu správu! Čoskoro sa vám ozveme späť.",
        },
      ],
    },
  },
  {
    path: "/caste-otazky",
    name: "Časté otázky",
    component: () => import("../views/info/CasteOtazky.vue"),
    meta: {
      title: "Heligónková Akadémia - Časté otázky",
      description:
        "Máte otázky? Tu nájdete odpovede na najčastejšie dotazy týkajúce sa kurzov, zápisov či objednávok.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/caste-otazky",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Časté otázky",
        },
        {
          property: "og:description",
          content:
            "Máte otázky? Tu nájdete odpovede na najčastejšie dotazy týkajúce sa kurzov, zápisov či objednávok.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/caste-otazky",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Časté otázky",
        },
        {
          name: "twitter:description",
          content:
            "Máte otázky? Tu nájdete odpovede na najčastejšie dotazy týkajúce sa kurzov, zápisov či objednávok.",
        },
      ],
    },
  },
  {
    path: "/kosik",
    name: "Košík",
    component: () => import("../views/payment/KosikStranka.vue"),
    meta: {
      title: "Heligónková Akadémia - Košík",
      description:
        "Skontrolujte obsah svojho košíka a pripravte sa na dokončenie objednávky. Všetko pre heligónku na jednom mieste.",
      metaTags: [
        { property: "og:url", content: "https://heligonkovaakademia.sk/kosik" },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - Košík" },
        {
          property: "og:description",
          content:
            "Skontrolujte obsah svojho košíka a pripravte sa na dokončenie objednávky. Všetko pre heligónku na jednom mieste.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/kosik",
        },
        { name: "twitter:title", content: "Heligónková Akadémia - Košík" },
        {
          name: "twitter:description",
          content:
            "Skontrolujte obsah svojho košíka a pripravte sa na dokončenie objednávky. Všetko pre heligónku na jednom mieste.",
        },
      ],
    },
  },
  {
    path: "/pokladna",
    name: "Pokladňa",
    component: () => import("../views/payment/PokladnaStranka.vue"),
    meta: {
      title: "Heligónková Akadémia - Pokladňa",
      description:
        "Bezpečne dokončite svoju objednávku a získajte prístup k hraniu na heligónke už dnes.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/pokladna",
        },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - Pokladňa" },
        {
          property: "og:description",
          content:
            "Bezpečne dokončite svoju objednávku a získajte prístup k hraniu na heligónke už dnes.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/pokladna",
        },
        { name: "twitter:title", content: "Heligónková Akadémia - Pokladňa" },
        {
          name: "twitter:description",
          content:
            "Bezpečne dokončite svoju objednávku a získajte prístup k hraniu na heligónke už dnes.",
        },
      ],
      requiresAuth: true,
    },
  },
  {
    path: "/registracia",
    name: "Registrácia",
    component: () => import("../views/login/RegistraciaStranka.vue"),
    meta: {
      title: "Heligónková Akadémia - Registrácia",
      description:
        "Zaregistrujte sa a získajte prístup k výučbe hry na heligónku, zápisom, videám a ďalším výhodám.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/registracia",
        },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - Registrácia" },
        {
          property: "og:description",
          content:
            "Zaregistrujte sa a získajte prístup k výučbe hry na heligónku, zápisom, videám a ďalším výhodám.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/registracia",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Registrácia",
        },
        {
          name: "twitter:description",
          content:
            "Zaregistrujte sa a získajte prístup k výučbe hry na heligónku, zápisom, videám a ďalším výhodám.",
        },
      ],
    },
  },
  {
    path: "/prihlasenie",
    name: "Prihlásenie",
    component: () => import("../views/login/PrihlasenieStranka.vue"),
    meta: {
      title: "Heligónková Akadémia - Prihlásenie",
      description:
        "Prihláste sa do svojho účtu a pokračujte v učení hry na heligónku tam, kde ste skončili.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/prihlasenie",
        },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - Prihlásenie" },
        {
          property: "og:description",
          content:
            "Prihláste sa do svojho účtu a pokračujte v učení hry na heligónku tam, kde ste skončili.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/prihlasenie",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Prihlásenie",
        },
        {
          name: "twitter:description",
          content:
            "Prihláste sa do svojho účtu a pokračujte v učení hry na heligónku tam, kde ste skončili.",
        },
      ],
    },
  },
  {
    path: "/ucebna/helicas",
    name: "Helicas",
    component: () => import("../views/logged/ucebna/HeliCas.vue"),
    meta: {
      title: "Heligónková Akadémia - Heličas",
      description:
        "Objavte sériu Helicas – interaktívne lekcie, ktoré vás krok za krokom prevedú svetom heligónky.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/helicas",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Heličas",
        },
        {
          property: "og:description",
          content:
            "Objavte sériu Helicas – interaktívne lekcie, ktoré vás krok za krokom prevedú svetom heligónky.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/helicas",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Heličas",
        },
        {
          name: "twitter:description",
          content:
            "Objavte sériu Helicas – interaktívne lekcie, ktoré vás krok za krokom prevedú svetom heligónky.",
        },
      ],
      //   requiresAuth: true,
    },
  },
  {
    path: "/zabudnute-heslo",
    name: "Zabudnuté heslo",
    component: () => import("../views/login/ZabudnuteHeslo.vue"),
    meta: {
      title: "Heligónková Akadémia - Zabudnuté heslo",
      description:
        "Zabudli ste heslo? Žiadny problém – obnovte si prístup k svojmu účtu rýchlo a jednoducho.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/zabudnute-heslo",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zabudnuté heslo",
        },
        {
          property: "og:description",
          content:
            "Zabudli ste heslo? Žiadny problém – obnovte si prístup k svojmu účtu rýchlo a jednoducho.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/zabudnute-heslo",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zabudnuté heslo",
        },
        {
          name: "twitter:description",
          content:
            "Zabudli ste heslo? Žiadny problém – obnovte si prístup k svojmu účtu rýchlo a jednoducho.",
        },
      ],
    },
  },
  {
    path: "/zabudnute-heslo-uspesne",
    name: "Zabudnuté heslo úspešné",
    component: () => import("../views/others/ZabudnuteHesloUspesne.vue"),
    meta: {
      title: "Heligónková Akadémia - Zabudnuté heslo úspešné",
      description:
        "Vaša žiadosť o obnovenie hesla bola úspešne odoslaná. Skontrolujte e-mail a postupujte podľa pokynov na vytvorenie nového hesla.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/zabudnute-heslo-uspesne",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zabudnuté heslo úspešné",
        },
        {
          property: "og:description",
          content:
            "Vaša žiadosť o obnovenie hesla bola úspešne odoslaná. Skontrolujte e-mail a postupujte podľa pokynov na vytvorenie nového hesla.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/zabudnute-heslo-uspesne",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zabudnuté heslo úspešné",
        },
        {
          name: "twitter:description",
          content:
            "Vaša žiadosť o obnovenie hesla bola úspešne odoslaná. Skontrolujte e-mail a postupujte podľa pokynov na vytvorenie nového hesla.",
        },
      ],
    },
  },
  {
    path: "/vytvorenie-hesla",
    name: "Vytvorenie hesla",
    component: () => import("../views/login/VytvorenieHesla.vue"),
    meta: {
      title: "Heligónková Akadémia - Vytvorenie hesla",
      description:
        "Nastavte si nové heslo a získajte opäť prístup k svojmu účtu na Heligónkovej Akadémii.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/vytvorenie-hesla",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Vytvorenie hesla",
        },
        {
          property: "og:description",
          content:
            "Nastavte si nové heslo a získajte opäť prístup k svojmu účtu na Heligónkovej Akadémii.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/vytvorenie-hesla",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Vytvorenie hesla",
        },
        {
          name: "twitter:description",
          content:
            "Nastavte si nové heslo a získajte opäť prístup k svojmu účtu na Heligónkovej Akadémii.",
        },
      ],
    },
  },
  {
    path: "/potrebne-prihlasenie",
    name: "Potrebné prihlásenie",
    component: () => import("../views/others/PotrebnePrihlasenie.vue"),
    meta: {
      title: "Heligónková Akadémia - Potrebné prihlásenie",
      description:
        "Pre zobrazenie tejto stránky sa musíte prihlásiť do svojho účtu na Heligónkovej Akadémii.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/potrebne-prihlasenie",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Potrebné prihlásenie",
        },
        {
          property: "og:description",
          content:
            "Pre zobrazenie tejto stránky sa musíte prihlásiť do svojho účtu na Heligónkovej Akadémii.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/potrebne-prihlasenie",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Potrebné prihlásenie",
        },
        {
          name: "twitter:description",
          content:
            "Pre zobrazenie tejto stránky sa musíte prihlásiť do svojho účtu na Heligónkovej Akadémii.",
        },
      ],
    },
  },
  {
    path: "/ucebna",
    name: "Moja učebňa",
    meta: {
      title: "Heligónková Akadémia - Moja učebňa",
      description:
        "Váš osobný priestor na učenie! Sledujte svoj pokrok, pristupujte k kurzom, zápisom a obľúbeným materiálom na jednom mieste.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Moja učebňa",
        },
        {
          property: "og:description",
          content:
            "Váš osobný priestor na učenie! Sledujte svoj pokrok, pristupujte k kurzom, zápisom a obľúbeným materiálom na jednom mieste.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Moja učebňa",
        },
        {
          name: "twitter:description",
          content:
            "Váš osobný priestor na učenie! Sledujte svoj pokrok, pristupujte k kurzom, zápisom a obľúbeným materiálom na jednom mieste.",
        },
      ],
      // requiresAuth: true,
    },
    component: () => import("../views/logged/ucebna/MojUcet.vue"),
  },
  {
    path: "/ucebna/helicas-body",
    name: "HeličasBody",
    meta: {
      title: "Heligónková Akadémia - Heličas pre žiakov Heligonkovej Akadémie",
      description:
        "Heličas ma svoje výhody aj pre žiakov Heligonkovej Akadémie. Hraj, uč sa a posúvaj svoje hranice hry na heligonke a na konci roka ťa odmeníme skvelou odmenou.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/helicas-body",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Heličas",
        },
        {
          property: "og:description",
          content:
            "Heličas ma svoje výhody aj pre žiakov Heligonkovej Akadémie. Hraj, uč sa a posúvaj svoje hranice hry na heligonke a na konci roka ťa odmeníme skvelou odmenou.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/helicas-body",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Heličas",
        },
        {
          name: "twitter:description",
          content:
            "Heličas ma svoje výhody aj pre žiakov Heligonkovej Akadémie. Hraj, uč sa a posúvaj svoje hranice hry na heligonke a na konci roka ťa odmeníme skvelou odmenou.",
        },
      ],
      //   requiresAuth: true,
    },
    component: () => import("../views/logged/ucebna/EdupageHelibody.vue"),
  },
  {
    path: "/ucebna/ako-funguje-helicas",
    name: "Heličas fungovanie",
    meta: {
      title: "Heligónková Akadémia - Ako funguje Heličas?",
      description:
        "Objavte systém Heličas – herný spôsob učenia, kde za svoj pokrok získavate body, hviezdy a odmeny.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/ako-funguje-helicas",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Ako funguje Heličas?",
        },
        {
          property: "og:description",
          content:
            "Objavte systém Heličas – herný spôsob učenia, kde za svoj pokrok získavate body, hviezdy a odmeny.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/ako-funguje-helicas",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Ako funguje Heličas?",
        },
        {
          name: "twitter:description",
          content:
            "Objavte systém Heličas – herný spôsob učenia, kde za svoj pokrok získavate body, hviezdy a odmeny.",
        },
      ],
    },
    component: () => import("../views/logged/ucebna/HelicasFungovanie.vue"),
  },
  {
    path: "/ucebna/ulohy",
    name: "Heličas úlohy",
    meta: {
      title: "Heligónková Akadémia - Heličas úlohy",
      description:
        "Získajte prístup k úlohám a cvičeniam z lekcií Helicas. Zlepšite svoje zručnosti a ovládnite heligónku.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/ulohy",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Heličas úlohy",
        },
        {
          property: "og:description",
          content:
            "Získajte prístup k úlohám a cvičeniam z lekcií Helicas. Zlepšite svoje zručnosti a ovládnite heligónku.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/ulohy",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Heličas úlohy",
        },
        {
          name: "twitter:description",
          content:
            "Získajte prístup k úlohám a cvičeniam z lekcií Helicas. Zlepšite svoje zručnosti a ovládnite heligónku.",
        },
      ],
      //   requiresAuth: true,
    },
    component: () => import("../views/logged/ucebna/HelicasUlohy.vue"),
  },
  {
    path: "/ucebna/zadarmo-video",
    name: "Zadarmo Video",
    component: lazyLoad("logged/ucebna/ZadarmoVideo"),
    meta: {
      title: "Heligónková Akadémia - Zadarmo Video",
      description:
        "Pozrite si ukážkové výučbové video zdarma a presvedčte sa, ako jednoducho sa dá naučiť hrať na heligónku.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/zadarmo-video",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zadarmo Video",
        },
        {
          property: "og:description",
          content:
            "Pozrite si ukážkové výučbové video zdarma a presvedčte sa, ako jednoducho sa dá naučiť hrať na heligónku.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/zadarmo-video",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zadarmo Video",
        },
        {
          name: "twitter:description",
          content:
            "Pozrite si ukážkové výučbové video zdarma a presvedčte sa, ako jednoducho sa dá naučiť hrať na heligónku.",
        },
      ],
    },
  },
  {
    path: "/ucebna/moje-nakupy",
    name: "Moje nákupy",
    meta: {
      title: "Heligónková Akadémia - Moje nákupy",
      description:
        "Prezrite si svoj zoznam zakúpených kurzov, zápisov a videí. Všetko, čo ste si zakúpili, nájdete prehľadne na jednom mieste.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/moje-nakupy",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Moje nákupy",
        },
        {
          property: "og:description",
          content:
            "Prezrite si svoj zoznam zakúpených kurzov, zápisov a videí. Všetko, čo ste si zakúpili, nájdete prehľadne na jednom mieste.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/moje-nakupy",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Moje nákupy",
        },
        {
          name: "twitter:description",
          content:
            "Prezrite si svoj zoznam zakúpených kurzov, zápisov a videí. Všetko, čo ste si zakúpili, nájdete prehľadne na jednom mieste.",
        },
      ],
      requiresAuth: true,
    },
    component: () => import("../views/logged/ucebna/MojeNakupy.vue"),
  },
  {
    path: "/ucebna/nastavenia-uctu",
    name: "Nastevenia účtu",
    meta: {
      title: "Heligónková Akadémia - Nastavenia účtu",
      description:
        "Spravujte svoje osobné údaje, predplatné a nastavenia účtu. Udržujte svoj profil aktuálny a prispôsobený vašim potrebám.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/nastavenia-uctu",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Nastavenia účtu",
        },
        {
          property: "og:description",
          content:
            "Spravujte svoje osobné údaje, predplatné a nastavenia účtu. Udržujte svoj profil aktuálny a prispôsobený vašim potrebám.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/nastavenia-uctu",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Nastavenia účtu",
        },
        {
          name: "twitter:description",
          content:
            "Spravujte svoje osobné údaje, predplatné a nastavenia účtu. Udržujte svoj profil aktuálny a prispôsobený vašim potrebám.",
        },
      ],
      requiresAuth: true,
    },
    component: () => import("../views/logged/ucebna/NastaveniaUctu.vue"),
  },
  {
    path: "/ucebna/clenstvo",
    name: "Predplatne",
    meta: {
      title: "Heligónková Akadémia - Predplatné",
      description:
        "Získajte prehľad o svojom členstve, jeho výhodách a platnosti. Spravujte predplatné a objavte exkluzívny obsah pre členov.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/clenstvo",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Predplatné",
        },
        {
          property: "og:description",
          content:
            "Získajte prehľad o svojom členstve, jeho výhodách a platnosti. Spravujte predplatné a objavte exkluzívny obsah pre členov.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/clenstvo",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Predplatné",
        },
        {
          name: "twitter:description",
          content:
            "Získajte prehľad o svojom členstve, jeho výhodách a platnosti. Spravujte predplatné a objavte exkluzívny obsah pre členov.",
        },
      ],
      requiresAuth: true,
    },
    params: true,
    component: () => import("../views/logged/ucebna/ClenstvoPredplatne.vue"),
  },
  {
    path: "/ucebna/clenstvo-zakupene",
    name: "Predplatne zakúpené",
    meta: {
      title: "Heligónková Akadémia - Predplatne zakúpené",
      description:
        "Ďakujeme za zakúpenie členstva! Získali ste prístup k exkluzívnemu obsahu a výhodám Heligónkovej Akadémie.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/clenstvo-zakupene",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Predplatne zakúpené",
        },
        {
          property: "og:description",
          content:
            "Ďakujeme za zakúpenie členstva! Získali ste prístup k exkluzívnemu obsahu a výhodám Heligónkovej Akadémie.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/clenstvo-zakupene",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Predplatne zakúpené",
        },
        {
          name: "twitter:description",
          content:
            "Ďakujeme za zakúpenie členstva! Získali ste prístup k exkluzívnemu obsahu a výhodám Heligónkovej Akadémie.",
        },
      ],
      requiresAuth: true,
    },
    component: () => import("../views/logged/ucebna/ClenstvoZakúpene.vue"),
  },

  {
    path: "/ucebna/moje-piesne",
    name: "Moje piesne",
    meta: {
      title: "Heligónková Akadémia - Moje piesne",
      description:
        "Prezrite si všetky piesne, ktoré ste si zakúpili alebo získali v rámci svojho členstva. Vaša osobná hudobná zbierka na jednom mieste.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/moje-piesne",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Moje piesne",
        },
        {
          property: "og:description",
          content:
            "Prezrite si všetky piesne, ktoré ste si zakúpili alebo získali v rámci svojho členstva. Vaša osobná hudobná zbierka na jednom mieste.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/moje-piesne",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Moje piesne",
        },
        {
          name: "twitter:description",
          content:
            "Prezrite si všetky piesne, ktoré ste si zakúpili alebo získali v rámci svojho členstva. Vaša osobná hudobná zbierka na jednom mieste.",
        },
      ],
      requiresAuth: true,
    },
    component: () => import("../views/logged/MojePiesne.vue"),
  },
  {
    path: "/ucebna/moje-kurzy",
    name: "Moje kurzy",
    meta: {
      title: "Heligónková Akadémia - Moje kurzy",
      description:
        "Všetky kurzy, ktoré máte k dispozícii, na jednom mieste. Pokračujte v učení hry na heligónku vlastným tempom.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/moje-kurzy",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Moje kurzy",
        },
        {
          property: "og:description",
          content:
            "Všetky kurzy, ktoré máte k dispozícii, na jednom mieste. Pokračujte v učení hry na heligónku vlastným tempom.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/moje-kurzy",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Moje kurzy",
        },
        {
          name: "twitter:description",
          content:
            "Všetky kurzy, ktoré máte k dispozícii, na jednom mieste. Pokračujte v učení hry na heligónku vlastným tempom.",
        },
      ],
      requiresAuth: true,
    },
    component: () => import("../views/logged/MojeKurzy.vue"),
  },
  {
    path: "/ucebna/naucne-videa",
    name: "Náučné videá zoznam",
    meta: {
      title: "Heligónková Akadémia - Náučné videá zoznam",
      description:
        "Sledujte náučné videá, ktoré vás krok za krokom prevedú technikou hry na heligónku – od základov až po pokročilé triky.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/naucne-videa",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Náučné videá zoznam",
        },
        {
          property: "og:description",
          content:
            "Sledujte náučné videá, ktoré vás krok za krokom prevedú technikou hry na heligónku – od základov až po pokročilé triky.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/naucne-videa",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Náučné videá zoznam",
        },
        {
          name: "twitter:description",
          content:
            "Sledujte náučné videá, ktoré vás krok za krokom prevedú technikou hry na heligónku – od základov až po pokročilé triky.",
        },
      ],
      isSubscribe: true,
    },
    params: true,
    component: () => import("../views/logged/ucebna/NaucneVidea.vue"),
  },
  {
    path: "/ucebna/naucne-video",
    name: "Náučné video",
    props: true,
    component: () => import("../views/shop/NaucneVideo.vue"),
    meta: {
      isSubscribe: true,
      title: "Heligónková Akadémia - Náučné video",
      description:
        "Pozrite si výukové video, ktoré vás naučí nové zručnosti a techniky hrania na heligónku.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/ucebna/naucne-video",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Náučné video",
        },
        {
          property: "og:description",
          content:
            "Pozrite si výukové video, ktoré vás naučí nové zručnosti a techniky hrania na heligónku.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/ucebna/naucne-video",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Náučné video",
        },
        {
          name: "twitter:description",
          content:
            "Pozrite si výukové video, ktoré vás naučí nové zručnosti a techniky hrania na heligónku.",
        },
      ],
    },
  },
  {
    path: "/admin/upravenie-pouzivatela",
    name: "Upravenie používateľa",
    meta: {
      title: "Heligónková Akadémia - Upravenie používateľa",
      description: "Administrátorské rozhranie pre úpravu údajov používateľa.",
      isAdmin: true,
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/upravenie-pouzivatela",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Upravenie používateľa",
        },
        {
          property: "og:description",
          content: "Administrátorské rozhranie pre úpravu údajov používateľa.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/upravenie-pouzivatela",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Upravenie používateľa",
        },
        {
          name: "twitter:description",
          content: "Administrátorské rozhranie pre úpravu údajov používateľa.",
        },
      ],
    },
    component: () =>
      import("../views/admin/pouzivatelia/UpraveniePouzivatela.vue"),
  },
  {
    path: "/knizky/prvy-diel",
    name: "Informácie pre prvý diel",
    component: () => import("../views/knizky/KnizkaPrva.vue"),
    meta: {
      title: "Heligónková Akadémia - Prvý diel učebnice",
      description:
        "Zistite viac o prvom diele našej učebnice pre heligónku – ideálny štart pre začiatočníkov.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/knizky/prvy-diel",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Prvý diel učebnice",
        },
        {
          property: "og:description",
          content:
            "Zistite viac o prvom diele našej učebnice pre heligónku – ideálny štart pre začiatočníkov.",
        },
        {
          property: "og:image",
          content: "https://heligonkovaakademia.sk/data/seo/SeoKniha.png",
        },
        { name: "twitter:card", content: "summary_large_image" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/knizky/prvy-diel",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Prvý diel učebnice",
        },
        {
          name: "twitter:description",
          content:
            "Zistite viac o prvom diele našej učebnice pre heligónku – ideálny štart pre začiatočníkov.",
        },
        {
          name: "twitter:image",
          content: "https://heligonkovaakademia.sk/data/seo/SeoKniha.png",
        },
      ],
    },
  },
  {
    path: "/pripravujeme",
    name: "Pripravujeme",
    component: () => import("../views/others/PripravujemeStranka.vue"),
    meta: {
      title: "Heligónková Akadémia - Pripravujeme",
      description:
        "Pozrite si, čo pre vás čoskoro pripravujeme. Nové kurzy, videá a ďalšie prekvapenia čoskoro!",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/pripravujeme",
        },
        {
          property: "og:type",
          content: "website",
        },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Pripravujeme",
        },
        {
          property: "og:description",
          content:
            "Pozrite si, čo pre vás čoskoro pripravujeme. Nové kurzy, videá a ďalšie prekvapenia čoskoro!",
        },
        {
          name: "twitter:card",
          content: "summary",
        },
        {
          property: "twitter:domain",
          content: "heligonkovaakademia.sk",
        },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/pripravujeme",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Pripravujeme",
        },
        {
          name: "twitter:description",
          content:
            "Pozrite si, čo pre vás čoskoro pripravujeme. Nové kurzy, videá a ďalšie prekvapenia čoskoro!",
        },
      ],
    },
  },
  {
    path: "/uspesny-nakup",
    name: "Úspešný nákup",
    component: () => import("../views/others/UspesnyNakup.vue"),
    meta: {
      title: "Heligónková Akadémia - Úspešný nákup",
      description:
        "Ďakujeme za váš nákup! Prístup k zakúpenému obsahu máte okamžite dostupný vo vašej učebni.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/uspesny-nakup",
        },
        {
          property: "og:type",
          content: "website",
        },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Úspešný nákup",
        },
        {
          property: "og:description",
          content:
            "Ďakujeme za váš nákup! Prístup k zakúpenému obsahu máte okamžite dostupný vo vašej učebni.",
        },
        {
          name: "twitter:card",
          content: "summary",
        },
        {
          property: "twitter:domain",
          content: "heligonkovaakademia.sk",
        },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/uspesny-nakup",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Úspešný nákup",
        },
        {
          name: "twitter:description",
          content:
            "Ďakujeme za váš nákup! Prístup k zakúpenému obsahu máte okamžite dostupný vo vašej učebni.",
        },
      ],
    },
  },
  {
    path: "/neuspesny-nakup",
    name: "Neúspešný nákup",
    component: () => import("../views/others/NeuspesnyNakup.vue"),
    meta: {
      title: "Heligónková Akadémia - Neúspešný nákup",
      description:
        "Nákup sa nepodarilo dokončiť. Skúste to znova alebo nás kontaktujte pre pomoc.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/neuspesny-nakup",
        },
        {
          property: "og:type",
          content: "website",
        },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Neúspešný nákup",
        },
        {
          property: "og:description",
          content:
            "Nákup sa nepodarilo dokončiť. Skúste to znova alebo nás kontaktujte pre pomoc.",
        },
        {
          name: "twitter:card",
          content: "summary",
        },
        {
          property: "twitter:domain",
          content: "heligonkovaakademia.sk",
        },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/neuspesny-nakup",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Neúspešný nákup",
        },
        {
          name: "twitter:description",
          content:
            "Nákup sa nepodarilo dokončiť. Skúste to znova alebo nás kontaktujte pre pomoc.",
        },
      ],
    },
  },
  {
    path: "/admin/reklamny-email",
    name: "Reklamný email",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Reklamný email",
      description:
        "Správa a odosielanie reklamných emailov používateľom Heligónkovej Akadémie.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/reklamny-email",
        },
        {
          property: "og:type",
          content: "website",
        },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Reklamný email",
        },
        {
          property: "og:description",
          content:
            "Správa a odosielanie reklamných emailov používateľom Heligónkovej Akadémie.",
        },
        {
          name: "twitter:card",
          content: "summary",
        },
        {
          property: "twitter:domain",
          content: "heligonkovaakademia.sk",
        },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/reklamny-email",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Reklamný email",
        },
        {
          name: "twitter:description",
          content:
            "Správa a odosielanie reklamných emailov používateľom Heligónkovej Akadémie.",
        },
      ],
    },
    component: () => import("../views/admin/emaily/ReklamnyEmail.vue"),
  },
  {
    path: "/admin/pridanie-polozky-helishop",
    name: "Pridanie helishop",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Pridanie položky do HeliShopu",
      description:
        "Administrátorská stránka na pridanie nového produktu do HeliShopu.",
      metaTags: [
        {
          property: "og:url",
          content:
            "https://heligonkovaakademia.sk/admin/pridanie-polozky-helishop",
        },
        {
          property: "og:type",
          content: "website",
        },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Pridanie položky do HeliShopu",
        },
        {
          property: "og:description",
          content:
            "Administrátorská stránka na pridanie nového produktu do HeliShopu.",
        },
        {
          name: "twitter:card",
          content: "summary",
        },
        {
          property: "twitter:domain",
          content: "heligonkovaakademia.sk",
        },
        {
          property: "twitter:url",
          content:
            "https://heligonkovaakademia.sk/admin/pridanie-polozky-helishop",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Pridanie položky do HeliShopu",
        },
        {
          name: "twitter:description",
          content:
            "Administrátorská stránka na pridanie nového produktu do HeliShopu.",
        },
      ],
    },
    component: () => import("../views/admin/pridavanie/PridanieHelishop.vue"),
  },
  {
    path: "/admin/objednavky-helishop",
    name: "Objednávky Helishop",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Objednávky HeliShop",
      description: "Prehľad objednávok z HeliShopu pre administrátorov.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/objednavky-helishop",
        },
        {
          property: "og:type",
          content: "website",
        },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Objednávky HeliShop",
        },
        {
          property: "og:description",
          content: "Prehľad objednávok z HeliShopu pre administrátorov.",
        },
        {
          name: "twitter:card",
          content: "summary",
        },
        {
          property: "twitter:domain",
          content: "heligonkovaakademia.sk",
        },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/objednavky-helishop",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Objednávky HeliShop",
        },
        {
          name: "twitter:description",
          content: "Prehľad objednávok z HeliShopu pre administrátorov.",
        },
      ],
    },
    component: () => import("../views/admin/zoznamy/ObjednavkyHelishop.vue"),
  },
  {
    path: "/helishop/produkt",
    name: "Produkt helishopu",
    component: () => import("../views/shop/HelishopProdukt.vue"),
    meta: {
      title: "Heligónková Akadémia - Produkt HeliShopu",
      description:
        "Detail produktu z nášho HeliShopu – všetko pre milovníkov heligónky.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/helishop/produkt",
        },
        {
          property: "og:type",
          content: "website",
        },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Produkt HeliShopu",
        },
        {
          property: "og:description",
          content:
            "Detail produktu z nášho HeliShopu – všetko pre milovníkov heligónky.",
        },
        {
          name: "twitter:card",
          content: "summary",
        },
        {
          property: "twitter:domain",
          content: "heligonkovaakademia.sk",
        },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/helishop/produkt",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Produkt HeliShopu",
        },
        {
          name: "twitter:description",
          content:
            "Detail produktu z nášho HeliShopu – všetko pre milovníkov heligónky.",
        },
      ],
    },
    // isAdmin: true,
  },
  {
    path: "/admin/vytvorenie-zapisu",
    name: "Vytvorenie zapisu",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Vytvorenie zápisu",
      description:
        "Stránka pre administrátorov na pridávanie nového číselného zápisu do systému.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/vytvorenie-zapisu",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Vytvorenie zápisu",
        },
        {
          property: "og:description",
          content:
            "Stránka pre administrátorov na pridávanie nového číselného zápisu do systému.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/vytvorenie-zapisu",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Vytvorenie zápisu",
        },
        {
          name: "twitter:description",
          content:
            "Stránka pre administrátorov na pridávanie nového číselného zápisu do systému.",
        },
      ],
    },
    component: () => import("../views/admin/pridavanie/VytvorenieZapisu.vue"),
  },
  {
    path: "/admin/editor-zapisov",
    name: "Editor zapisov",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Editor zápisov",
      description:
        "Správa a úprava existujúcich číselných zápisov pre administrátorov.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/editor-zapisov",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Editor zápisov",
        },
        {
          property: "og:description",
          content:
            "Správa a úprava existujúcich číselných zápisov pre administrátorov.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/editor-zapisov",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Editor zápisov",
        },
        {
          name: "twitter:description",
          content:
            "Správa a úprava existujúcich číselných zápisov pre administrátorov.",
        },
      ],
    },
    component: () => import("../views/admin/zoznamy/EditorZapisov.vue"),
  },
  {
    path: "/admin/pridanie-videa",
    name: "Pridanie videa",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Pridanie videa",
      description:
        "Nahrajte nové výučbové video pre študentov heligónky. Táto stránka je určená pre administrátorov.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/pridanie-videa",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Pridanie videa",
        },
        {
          property: "og:description",
          content:
            "Nahrajte nové výučbové video pre študentov heligónky. Táto stránka je určená pre administrátorov.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/pridanie-videa",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Pridanie videa",
        },
        {
          name: "twitter:description",
          content:
            "Nahrajte nové výučbové video pre študentov heligónky. Táto stránka je určená pre administrátorov.",
        },
      ],
    },
    component: () => import("../views/admin/pridavanie/PridanieVidea.vue"),
  },
  {
    path: "/admin/pridanie-zapisu",
    name: "Pridanie zápisu",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Pridanie zápisu",
      description:
        "Pridajte nový číselný zápis do systému Heligónkovej Akadémie. Táto sekcia je prístupná iba administrátorom.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/pridanie-zapisu",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Pridanie zápisu",
        },
        {
          property: "og:description",
          content:
            "Pridajte nový číselný zápis do systému Heligónkovej Akadémie. Táto sekcia je prístupná iba administrátorom.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/pridanie-zapisu",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Pridanie zápisu",
        },
        {
          name: "twitter:description",
          content:
            "Pridajte nový číselný zápis do systému Heligónkovej Akadémie. Táto sekcia je prístupná iba administrátorom.",
        },
      ],
    },
    component: () => import("../views/admin/pridavanie/PridanieZapisu.vue"),
  },
  {
    path: "/admin/zoznam-poloziek-helishop",
    name: "Zoznam Helishop",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Zoznam Helishop položiek",
      description:
        "Spravujte všetky produkty dostupné v Helishop sekcii. Táto stránka je určená pre administrátorov.",
      metaTags: [
        {
          property: "og:url",
          content:
            "https://heligonkovaakademia.sk/admin/zoznam-poloziek-helishop",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zoznam Helishop položiek",
        },
        {
          property: "og:description",
          content:
            "Spravujte všetky produkty dostupné v Helishop sekcii. Táto stránka je určená pre administrátorov.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content:
            "https://heligonkovaakademia.sk/admin/zoznam-poloziek-helishop",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zoznam Helishop položiek",
        },
        {
          name: "twitter:description",
          content:
            "Spravujte všetky produkty dostupné v Helishop sekcii. Táto stránka je určená pre administrátorov.",
        },
      ],
    },
    component: () => import("../views/admin/zoznamy/ZoznamHelishop.vue"),
  },
  {
    path: "/admin/zoznam-predplatitelov",
    name: "Zoznam predplatiteľov",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Zoznam predplatiteľov",
      description:
        "Získajte prehľad o všetkých aktuálnych predplatiteľoch našej platformy. Táto stránka je určená pre administrátorov.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-predplatitelov",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zoznam predplatiteľov",
        },
        {
          property: "og:description",
          content:
            "Získajte prehľad o všetkých aktuálnych predplatiteľoch našej platformy. Táto stránka je určená pre administrátorov.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-predplatitelov",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zoznam predplatiteľov",
        },
        {
          name: "twitter:description",
          content:
            "Získajte prehľad o všetkých aktuálnych predplatiteľoch našej platformy. Táto stránka je určená pre administrátorov.",
        },
      ],
    },
    component: () => import("../views/admin/zoznamy/ZoznamPredplatitelov.vue"),
  },
  {
    path: "/admin/zoznam-faktur",
    name: "Zoznam faktúr",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Zoznam faktúr",
      description:
        "Prehľad všetkých vystavených faktúr na platforme Heligónková Akadémia. Stránka je určená pre administrátorov.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-faktur",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zoznam faktúr",
        },
        {
          property: "og:description",
          content:
            "Prehľad všetkých vystavených faktúr na platforme Heligónková Akadémia. Stránka je určená pre administrátorov.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-faktur",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zoznam faktúr",
        },
        {
          name: "twitter:description",
          content:
            "Prehľad všetkých vystavených faktúr na platforme Heligónková Akadémia. Stránka je určená pre administrátorov.",
        },
      ],
    },
    component: () => import("../views/admin/zoznamy/ZoznamFaktur.vue"),
  },
  {
    path: "/admin/zoznam-uzivatelov",
    name: "Zoznam užívateľov",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Zoznam užívateľov",
      description:
        "Administrátorský prehľad všetkých registrovaných užívateľov na platforme Heligónková Akadémia.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-uzivatelov",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zoznam užívateľov",
        },
        {
          property: "og:description",
          content:
            "Administrátorský prehľad všetkých registrovaných užívateľov na platforme Heligónková Akadémia.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-uzivatelov",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zoznam užívateľov",
        },
        {
          name: "twitter:description",
          content:
            "Administrátorský prehľad všetkých registrovaných užívateľov na platforme Heligónková Akadémia.",
        },
      ],
    },
    component: () => import("../views/admin/zoznamy/ZoznamUzivatelov.vue"),
  },
  {
    path: "/admin/zoznam-videi",
    name: "Zoznam videí",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Zoznam videí",
      description:
        "Administrátorský zoznam výukových videí a edukačného obsahu v rámci Heligónkovej Akadémie.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-videi",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zoznam videí",
        },
        {
          property: "og:description",
          content:
            "Administrátorský zoznam výukových videí a edukačného obsahu v rámci Heligónkovej Akadémie.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-videi",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zoznam videí",
        },
        {
          name: "twitter:description",
          content:
            "Administrátorský zoznam výukových videí a edukačného obsahu v rámci Heligónkovej Akadémie.",
        },
      ],
    },
    component: () => import("../views/admin/zoznamy/ZoznamVidei.vue"),
  },
  {
    path: "/admin/zoznam-zapisov",
    name: "Zoznam zápisov",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Zoznam zápisov",
      description:
        "Administrátorský prehľad všetkých číselných zápisov dostupných v systéme Heligónkovej Akadémie.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-zapisov",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zoznam zápisov",
        },
        {
          property: "og:description",
          content:
            "Administrátorský prehľad všetkých číselných zápisov dostupných v systéme Heligónkovej Akadémie.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/zoznam-zapisov",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zoznam zápisov",
        },
        {
          name: "twitter:description",
          content:
            "Administrátorský prehľad všetkých číselných zápisov dostupných v systéme Heligónkovej Akadémie.",
        },
      ],
    },
    component: () => import("../views/admin/zoznamy/ZoznamZapisov.vue"),
  },
  {
    path: "/admin/textovy-system",
    name: "Textový systém",
    meta: {
      // isAdmin: true,
      title: "Heligónková Akadémia - Textový systém",
      description:
        "Administrátorský prehľad všetkých číselných zápisov dostupných v systéme Heligónkovej Akadémie.",
    },
    component: () => import("../views/admin/textovySystem/TextovySystem.vue"),
  },
  {
    path: "/admin/predplatne",
    name: "Predplatné",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Predplatné",
      description:
        "Administrácia predplatného a správa prístupov k náučným videám pre registrovaných používateľov.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/predplatne",
        },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - Predplatné" },
        {
          property: "og:description",
          content:
            "Administrácia predplatného a správa prístupov k náučným videám pre registrovaných používateľov.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/predplatne",
        },
        { name: "twitter:title", content: "Heligónková Akadémia - Predplatné" },
        {
          name: "twitter:description",
          content:
            "Administrácia predplatného a správa prístupov k náučným videám pre registrovaných používateľov.",
        },
      ],
    },
    component: () => import("../views/admin/predplatne/PredplatneVidea.vue"),
  },
  {
    path: "/admin/zlavy",
    name: "Zľavy",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Zľavy",
      description:
        "Správa a vytváranie zľavových kódov pre používateľov Heligónkovej Akadémie.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/zlavy",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zľavy",
        },
        {
          property: "og:description",
          content:
            "Správa a vytváranie zľavových kódov pre používateľov Heligónkovej Akadémie.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/zlavy",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zľavy",
        },
        {
          name: "twitter:description",
          content:
            "Správa a vytváranie zľavových kódov pre používateľov Heligónkovej Akadémie.",
        },
      ],
    },
    component: () => import("../views/admin/zlavy/ZlavoveKody.vue"),
  },
  {
    path: "/admin/darcekove-poukazky",
    name: "Darčekové poukážky",
    meta: {
      title: "Heligónková Akadémia - Darčekové poukážky",
      description:
        "Správa a vytváranie darčekových poukážok pre používateľov Heligónkovej Akadémie.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/darcekove-poukazky",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Darčekové poukážky",
        },
        {
          property: "og:description",
          content:
            "Správa a vytváranie darčekových poukážok pre používateľov Heligónkovej Akadémie.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/darcekove-poukazky",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Darčekové poukážky",
        },
        {
          name: "twitter:description",
          content:
            "Správa a vytváranie darčekových poukážok pre používateľov Heligónkovej Akadémie.",
        },
      ],
      isAdmin: true,
    },
    component: () => import("../views/admin/zlavy/DarcekovePoukazky.vue"),
  },
  {
    path: "/admin/generovanie-darcekove-poukazky",
    name: "Darčekové poukážky",
    meta: {
      title: "Heligónková Akadémia - Darčekové poukážky",
      description:
        "Správa a vytváranie darčekových poukážok pre používateľov Heligónkovej Akadémie.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/darcekove-poukazky",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Darčekové poukážky",
        },
        {
          property: "og:description",
          content:
            "Správa a vytváranie darčekových poukážok pre používateľov Heligónkovej Akadémie.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/darcekove-poukazky",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Darčekové poukážky",
        },
        {
          name: "twitter:description",
          content:
            "Správa a vytváranie darčekových poukážok pre používateľov Heligónkovej Akadémie.",
        },
      ],
      isAdmin: true,
    },
    component: () =>
      import("../views/admin/darcekovePoukazky/DarcekovePoukazky.vue"),
  },
  {
    path: "/ucebna/hodnota-poukazky",
    name: "Zisti hodnotu poukážky",
    meta: {
      title: "Heligónková Akadémia - Zistenie hodnoty poukážky",
      description:
        "Zisti hodnotu tvojej darčekovej poukážky na Heligónkovej Akadémii a využi ju pri nákupe.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/poukazka/hodnota",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Zistenie hodnoty poukážky",
        },
        {
          property: "og:description",
          content:
            "Zisti hodnotu tvojej darčekovej poukážky na Heligónkovej Akadémii a využi ju pri nákupe.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/poukazka/hodnota",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Zistenie hodnoty poukážky",
        },
        {
          name: "twitter:description",
          content:
            "Zisti hodnotu tvojej darčekovej poukážky na Heligónkovej Akadémii a využi ju pri nákupe.",
        },
      ],
    },
    component: () => import("../views/logged/ucebna/HodnotaPoukazky.vue"),
  },
  {
    path: "/admin/",
    name: "Domov",
    meta: {
      isAdmin: true,
      title: "Heligónková Akadémia - Admin Domov",
      description:
        "Prehľadná úvodná stránka administrátora Heligónkovej Akadémie s prístupom k správe obsahu.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/",
        },
        { property: "og:type", content: "website" },
        { property: "og:title", content: "Heligónková Akadémia - Admin Domov" },
        {
          property: "og:description",
          content:
            "Prehľadná úvodná stránka administrátora Heligónkovej Akadémie s prístupom k správe obsahu.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Admin Domov",
        },
        {
          name: "twitter:description",
          content:
            "Prehľadná úvodná stránka administrátora Heligónkovej Akadémie s prístupom k správe obsahu.",
        },
      ],
    },
    component: () => import("../views/admin/DomovStranka.vue"),
  },
  {
    path: "/admin/kalendar",
    name: "Kalendár výučby",
    meta: {
      // isAdmin: true,
      title: "Kalendár vyučovania - Heligonková akadémia",
      description: "Vyučovací kalendár pre učiteľov",
    },
    component: () => import("../views/admin/Edupage/EdupageKalendar.vue"),
  },

  {
    path: "/spevnik/",
    name: "Spevník",
    meta: {
      title: "Heligónková Akadémia - Spevník",
      description:
        "Prehľadný spevník piesní Heligónkovej Akadémie s možnosťou filtrovania, vyhľadávania a označovania obľúbených piesní.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/spevnik",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Spevník",
        },
        {
          property: "og:description",
          content:
            "Prehľadný spevník piesní Heligónkovej Akadémie s možnosťou filtrovania, vyhľadávania a označovania obľúbených piesní.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/spevnik",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Spevník",
        },
        {
          name: "twitter:description",
          content:
            "Prehľadný spevník piesní Heligónkovej Akadémie s možnosťou filtrovania, vyhľadávania a označovania obľúbených piesní.",
        },
      ],
    },
    component: () => import("../views/logged/SpevnikView.vue"),
  },
  {
    path: "/text-piesne/aktivna/:slug",
    name: "TextPiesneAktivna",
    component: () => import("@/views/logged/TextPiesneView.vue"),
    props: true,
    meta: {
      title: "Text piesne – Heligónková Akadémia",
      description:
        "Zobrazenie textu konkrétnej piesne zo spevníka Heligónkovej Akadémie.",
      metaTags: [
        {
          name: "description",
          content:
            "Zobrazenie textu konkrétnej piesne zo spevníka Heligónkovej Akadémie.",
        },
        {
          property: "og:type",
          content: "article",
        },
        {
          property: "og:title",
          content: "Text piesne – Heligónková Akadémia",
        },
        {
          property: "og:description",
          content:
            "Prečítajte si text tejto piesne zo spevníka Heligónkovej Akadémie.",
        },
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/text-piesne/aktivna/:slug",
        },
        {
          name: "twitter:card",
          content: "summary",
        },
        {
          name: "twitter:title",
          content: "Text piesne – Heligónková Akadémia",
        },
        {
          name: "twitter:description",
          content: "Pozrite si detailný text tejto piesne zo spevníka HA.",
        },
      ],
    },
  },
  {
    path: "/text-piesne/neaktivna/:slug",
    name: "TextPiesneNeaktivna",
    component: () => import("@/views/logged/TextPiesneView.vue"),
    props: true,
    meta: {
      title: "Text piesne – Heligónková Akadémia",
      description:
        "Zobrazenie textu neschválenej piesne zo spevníka Heligónkovej Akadémie.",
      metaTags: [
        {
          name: "description",
          content:
            "Zobrazenie textu neschválenej piesne zo spevníka Heligónkovej Akadémie.",
        },
        {
          property: "og:type",
          content: "article",
        },
        {
          property: "og:title",
          content: "Text piesne – Heligónková Akadémia",
        },
        {
          property: "og:description",
          content:
            "Prečítajte si neschválený text tejto piesne zo spevníka Heligónkovej Akadémie.",
        },
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/text-piesne/neaktivna/:slug",
        },
        {
          name: "twitter:card",
          content: "summary",
        },
        {
          name: "twitter:title",
          content: "Text piesne – Heligónková Akadémia",
        },
        {
          name: "twitter:description",
          content: "Pozrite si neschválený text tejto piesne zo spevníka HA.",
        },
      ],
    },
  },
  {
    path: "/admin/nastavenia-helicasu",
    name: "NastaveniaHeličasu",
    meta: {
      title: "Heligónková Akadémia - Nastavenia Helitimu",
      description:
        "Administračná stránka na úpravu konfigurácie Heličas systému a kreditových odmien.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/nastavenia-helicasu",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Nastavenia Helitimu",
        },
        {
          property: "og:description",
          content:
            "Administračná stránka na úpravu konfigurácie Heličas systému a kreditových odmien.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/nastavenia-helicasu",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Nastavenia Helitimu",
        },
        {
          name: "twitter:description",
          content:
            "Administračná stránka na úpravu konfigurácie Heličas systému a kreditových odmien.",
        },
      ],
    },
    component: () => import("../views/admin/Helicas/HelitimeConfig.vue"),
  },
  {
    path: "/admin/odosielanie",
    name: "OdosielanieMailovView",
    component: () => import("@/views/admin/emaily/OdosielanieMailovView.vue"),
  },
  {
    path: "/edupage/prihlaska",
    name: "OnlinePrihlaska",
    meta: {
      title: "Heligónková Akadémia – Online Prihláška",
      description:
        "Vyplň online prihlášku do Heligónkovej Akadémie a rezervuj svoje miesto na kurzoch.",
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/edupage/prihlaska",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia – Online Prihláška",
        },
        {
          property: "og:description",
          content:
            "Vyplň online prihlášku do Heligónkovej Akadémie a rezervuj svoje miesto na kurzoch.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/edupage/prihlaska",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia – Online Prihláška",
        },
        {
          name: "twitter:description",
          content:
            "Vyplň online prihlášku do Heligónkovej Akadémie a rezervuj svoje miesto na kurzoch.",
        },
      ],
    },
    component: () => import("../views/admin/Edupage/OnlinePrihlaska.vue"),
  },
  {
    path: "/admin/online-prihlasky",
    name: "AdminOnlinePrihlasky",
    meta: {
      title: "Administrácia – Online prihlášky",
      description:
        "Spravujte a vybavujte online prihlášky do Heligónkovej Akadémie v administrátorskom rozhraní.",
      isAdmin: true,
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/online-prihlasky",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Administrácia – Online prihlášky",
        },
        {
          property: "og:description",
          content:
            "Spravujte a vybavujte online prihlášky do Heligónkovej Akadémie v administrátorskom rozhraní.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/online-prihlasky",
        },
        {
          name: "twitter:title",
          content: "Administrácia – Online prihlášky",
        },
        {
          name: "twitter:description",
          content:
            "Spravujte a vybavujte online prihlášky do Heligónkovej Akadémie v administrátorskom rozhraní.",
        },
      ],
    },
    component: () => import("@/views/admin/Edupage/OnlinePrihlasky.vue"),
  },
  {
    path: "/admin/platobne-prikazy",
    name: "AdminPlatobnePrikazy",
    meta: {
      title: "Administrácia – Platobné príkazy",
      description:
        "Spravujte a spracovávajte platobné príkazy v administrátorskom rozhraní Heligónkovej Akadémie.",
      isAdmin: true,
      metaTags: [
        {
          property: "og:url",
          content: "https://heligonkovaakademia.sk/admin/platobne-prikazy",
        },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Administrácia – Platobné príkazy",
        },
        {
          property: "og:description",
          content:
            "Spravujte a spracovávajte platobné príkazy v administrátorskom rozhraní Heligónkovej Akadémie.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/admin/platobne-prikazy",
        },
        {
          name: "twitter:title",
          content: "Administrácia – Platobné príkazy",
        },
        {
          name: "twitter:description",
          content:
            "Spravujte a spracovávajte platobné príkazy v administrátorskom rozhraní Heligónkovej Akadémie.",
        },
      ],
    },
    component: () => import("@/views/admin/Edupage/EdupagePlatby.vue"),
  },
  {
    path: "/:catchAll(.*)",
    name: "NotFound",
    component: () => import("../views/server/404View.vue"),
    meta: {
      title: "Heligónková Akadémia - Stránka nenájdená",
      description:
        "Prepáčte, požadovaná stránka neexistuje. Skontrolujte URL alebo sa vráťte na hlavnú stránku.",
      metaTags: [
        { property: "og:url", content: "https://heligonkovaakademia.sk/404" },
        { property: "og:type", content: "website" },
        {
          property: "og:title",
          content: "Heligónková Akadémia - Stránka nenájdená",
        },
        {
          property: "og:description",
          content:
            "Prepáčte, požadovaná stránka neexistuje. Skontrolujte URL alebo sa vráťte na hlavnú stránku.",
        },
        { name: "twitter:card", content: "summary" },
        { property: "twitter:domain", content: "heligonkovaakademia.sk" },
        {
          property: "twitter:url",
          content: "https://heligonkovaakademia.sk/404",
        },
        {
          name: "twitter:title",
          content: "Heligónková Akadémia - Stránka nenájdená",
        },
        {
          name: "twitter:description",
          content:
            "Prepáčte, požadovaná stránka neexistuje. Skontrolujte URL alebo sa vráťte na hlavnú stránku.",
        },
      ],
    },
  },
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes,
});

// ⬇️ Zachytenie referral kódu z query a uloženie do Vuexu
router.beforeEach((to, from, next) => {
  if (to.path === "/ciselne-zapisy") {
    store.dispatch("credits/fetchCredits");
  }
  const q = to.query || {};
  const raw = Array.isArray(q.reflink) ? q.reflink[0] : q.reflink;
  if (typeof window !== "undefined" && raw) {
    store.dispatch("referral/capture", {
      codeParam: raw,
      currentHref: window.location.href,
    });
    try {
      const rest = { ...q };
      delete rest.reflink;
      const url = router.resolve({
        path: to.path,
        query: rest,
        hash: to.hash,
      }).href;
      window.history.replaceState({}, "", url);
    } catch (e) {
      console.error("Chyba pri odstránení reflink z URL", e);
    }
  }
  next();
});

router.beforeEach((to, from, next) => {
  const user = store.state.user;
  const isLoggedIn = user && user.id; // alebo user.token, podľa toho čo ukladáš

  if (
    isLoggedIn &&
    (to.path === "/prihlasenie" || to.path === "/registracia")
  ) {
    store.state.SnackBarText = "Už ste prihlásený";
    if (from && from.name) return next(false);
    return next("/");
  }

  if (to.matched.some((r) => r.meta?.requiresAuth) && !isLoggedIn) {
    return next({
      path: "/potrebne-prihlasenie",
      query: { redirect: to.fullPath },
    });
  }

  if (to.matched.some((r) => r.meta?.isSubscribe)) {
    if (!isLoggedIn || !user.isSubscribed) {
      return next("/ucebna/clenstvo");
    }
  }

  if (to.matched.some((r) => r.meta?.isAdmin)) {
    if (!isLoggedIn || !user.isAdmin) {
      return next("/");
    }
  }

  next();
});

router.afterEach((to) => {
  const defaultTitle = "Heligonkovaakademia";
  const defaultImage =
    "https://heligonkovaakademia.sk/data/seo/SeoDomovskastranka.png";
  document.title = to.meta.title || defaultTitle;
  const head = document.getElementsByTagName("head")[0];
  const setMetaTag = (attr, name, content) => {
    let tag =
      document.querySelector(`${attr}[name="${name}"]`) ||
      document.querySelector(`${attr}[property="${name}"]`);
    if (!tag) {
      tag = document.createElement("meta");
      tag.setAttribute(attr === "meta" ? "name" : "property", name);
      head.appendChild(tag);
    }
    tag.setAttribute("content", content);
  };
  setMetaTag("meta", "og:image", to.meta.ogImage || defaultImage);
  setMetaTag("meta", "twitter:image", to.meta.twitterImage || defaultImage);
});

router.beforeEach((to, from, next) => {
  store.state.isLoaded = false;
  next();
});

router.afterEach((to) => {
  store.state.isLoaded = true;
  if (to.matched.some((r) => r.meta.isAdmin)) {
    EventBlocker.destroy();
  } else {
    EventBlocker.init();
  }
});

export default router;
