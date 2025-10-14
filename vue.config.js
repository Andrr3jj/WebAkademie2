const { defineConfig } = require("@vue/cli-service");
const PrerenderSPAPlugin = require("prerender-spa-plugin");
const path = require("path");

module.exports = defineConfig({
  transpileDependencies: true,
  publicPath: "/test3",
  productionSourceMap: false,
  indexPath: "index.html", // 👈 dôležité
  configureWebpack: (config) => {
    if (process.env.NODE_ENV === "production") {
      config.plugins.push(
        new PrerenderSPAPlugin({
          staticDir: path.join(__dirname, "dist"),
          routes: [
            "/",
            "/naucne-videa",
            "/ciselne-zapisy",
            "/miesta-vyucby",
            "/zistit-viac",
            "/helifest",
            "/helishop",
            "/o-nas",
            "/o-nas/juraj-kvocka",
            "/o-nas/andrej-trnka",
            "/o-nas/libor-hlinik",
            "/o-nas/matej-kondela",
            "/pomoc",
            "/obchodne-podmienky",
            "/uspesne-odoslanie-formularu",
            "/caste-otazky",
            "/registracia",
            "/prihlasenie",
            "/zabudnute-heslo",
            "/zabudnute-heslo-uspesne",
            "/vytvorenie-hesla",
            "/potrebne-prihlasenie",
            "/knizky/prvy-diel",
            "/pripravujeme",
            "/uspesny-nakup",
            "/neuspesny-nakup",
            "/helishop/produkt",
            "/ucebna/zadarmo-video",
            "/ucebna/helicas",
            "/ucebna/ako-funguje-helicas",
            "/ucebna/ulohy",
            "/ucebna/naucne-videa",
            "/ucebna/naucne-video",
          ],
          renderer: new PrerenderSPAPlugin.PuppeteerRenderer({
            inject: {}, // alebo false ak nechceš injectovať nič
            headless: true,
            renderAfterDocumentEvent: "render-event", // môžeš zrušiť ak použiješ renderAfterTime
            renderAfterTime: 5000, // bezpečná záloha
            skipThirdPartyRequests: true,
            consoleHandler: (route, msg) => {
              const type = msg.type();
              const text = msg.text();
              if (type === "error") {
                console.error(`[ERROR] Route: ${route} => ${text}`);
              } else {
                console.log(`[${type.toUpperCase()}] ${route}: ${text}`);
              }
            },
          }),
        })
      );
    }
  },
});
