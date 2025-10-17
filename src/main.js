import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./components/store";
import vClickOutside from "click-outside-vue3";
import i18n, { bootI18n } from "./i18n";
store.dispatch("referral/init");

const app = createApp(App);

app.directive("click-outside", vClickOutside);

// ⬇⬇⬇ PRIDAJ – sprístupni router pre overlay (kvôli navigateTo v krokoch)
if (typeof window !== "undefined") window.appRouter = router;

// ROUTER GUARDS (ODDELENE!)

router.beforeEach((to, from, next) => {
  const codeFromQuery = to.query.pozyvaci_kod;

  if (codeFromQuery) {
    store.commit("SET_INVITE_CODE", codeFromQuery);
    next();
  } else if (!store.state.inviteCode) {
    fetch(store.state.api + "/user/info/getInviteCode.php")
      .then((res) => res.json())
      .then((result) => {
        if (result.status === "Request succesfull" && result.data) {
          store.commit("SET_INVITE_CODE", result.data);
        } else {
          store.commit("SET_INVITE_CODE", "neznamy");
        }
        next();
      })
      .catch(() => {
        store.commit("SET_INVITE_CODE", "neznamy");
        next();
      });
  } else {
    next();
  }
});

router.afterEach((to) => {
  // Title
  if (to.meta?.title) {
    document.title = to.meta.title;
  }

  // Classic description
  const description = to.meta?.description;
  let descriptionTag = document.querySelector('meta[name="description"]');
  if (description) {
    if (descriptionTag) {
      descriptionTag.setAttribute("content", description);
    } else {
      descriptionTag = document.createElement("meta");
      descriptionTag.setAttribute("name", "description");
      descriptionTag.setAttribute("content", description);
      document.head.appendChild(descriptionTag);
    }
  }

  // Remove previous og/twitter tags
  document
    .querySelectorAll("meta[data-dynamic-meta]")
    .forEach((el) => el.remove());

  // Add new meta tags
  if (Array.isArray(to.meta?.metaTags)) {
    to.meta.metaTags.forEach((tagDef) => {
      const tag = document.createElement("meta");
      Object.entries(tagDef).forEach(([key, val]) => {
        tag.setAttribute(key, val);
      });
      tag.setAttribute("data-dynamic-meta", "true");
      document.head.appendChild(tag);
    });
  }
});

app.use(store).use(router).use(i18n);

bootI18n().then(() => {
  app.mount("#app");
});
