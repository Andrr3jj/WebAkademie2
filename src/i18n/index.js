// src/i18n/index.js
import { createI18n } from 'vue-i18n/dist/vue-i18n.esm-bundler.js';

const FALLBACK_LOCALE = 'sk';

function readStoredLocale() {
  try {
    if (typeof window !== 'undefined' && window.localStorage) {
      return window.localStorage.getItem('locale');
    }
  } catch (error) {
    // Ignored – localStorage may be unavailable (SSR, private mode, etc.)
  }
  return null;
}

function persistLocale(locale) {
  try {
    if (typeof window !== 'undefined' && window.localStorage) {
      window.localStorage.setItem('locale', locale);
    }
  } catch (error) {
    // Ignore storage write errors
  }
}

const DEFAULT_LOCALE = readStoredLocale() || FALLBACK_LOCALE;

const i18n = createI18n({
  legacy: false,
  locale: DEFAULT_LOCALE,
  fallbackLocale: FALLBACK_LOCALE,
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

  if (typeof document !== 'undefined') {
    document.documentElement.setAttribute('lang', locale);
  }

  persistLocale(locale);
}

export async function bootI18n() {
  await loadLocaleMessages(FALLBACK_LOCALE);
  await setLocale(DEFAULT_LOCALE);
}

export default i18n;
export { i18n };
