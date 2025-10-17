// src/i18n/index.js
import { createI18n } from 'vue-i18n/dist/vue-i18n.esm-bundler.js';

const DEFAULT_LOCALE = localStorage.getItem('locale') || 'sk';

const i18n = createI18n({
  legacy: false,
  locale: DEFAULT_LOCALE,
  fallbackLocale: 'sk',
  messages: {}, // lazy load
});

const loaded = new Set();

async function loadLocaleMessages(locale) {
  if (loaded.has(locale)) return;

  // Bezpečný dynamic import pre Vue CLI – nedávaj sem žiadne pluginy; stačí JSON
  const messages = await import(/* webpackChunkName: "i18n-[request]" */ `./locales/${locale}.json`)
    .then(m => m.default || m)
    .catch(() => ({}));

  i18n.global.setLocaleMessage(locale, messages);
  loaded.add(locale);
}

export async function setLocale(locale) {
  await loadLocaleMessages(locale);
  i18n.global.locale.value = locale;
  document.documentElement.setAttribute('lang', locale);
  localStorage.setItem('locale', locale);
}

export async function bootI18n() {
  await setLocale(DEFAULT_LOCALE);
}

export default i18n;
export { i18n };
