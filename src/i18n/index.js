import { createI18n } from 'vue-i18n';

const FALLBACK_LOCALE = 'sk';

function readStoredLocale() {
  if (typeof window === 'undefined') return FALLBACK_LOCALE;

  try {
    return window.localStorage.getItem('locale') || FALLBACK_LOCALE;
  } catch (error) {
    console.warn('[i18n] Unable to access localStorage:', error);
    return FALLBACK_LOCALE;
  }
}

const DEFAULT_LOCALE = readStoredLocale();

const i18n = createI18n({
  legacy: false,
  locale: DEFAULT_LOCALE,
  fallbackLocale: FALLBACK_LOCALE,
  messages: {}, // lazy loading
});

const loaded = new Set();

async function loadLocaleMessages(locale) {
  if (loaded.has(locale)) return;
  const messages = await import(
    /* webpackChunkName: "locale-[request]" */ `./locales/${locale}.json`
  )
    .then((m) => m.default || m)
    .catch(() => ({}));
  i18n.global.setLocaleMessage(locale, messages);
  loaded.add(locale);
}

function persistLocale(locale) {
  if (typeof window === 'undefined') return;

  try {
    window.localStorage.setItem('locale', locale);
  } catch (error) {
    console.warn('[i18n] Unable to persist locale:', error);
  }
}

function updateDocumentLang(locale) {
  if (typeof document === 'undefined') return;
  document.documentElement.setAttribute('lang', locale);
}

export async function setLocale(locale) {
  await loadLocaleMessages(locale);
  i18n.global.locale.value = locale;
  updateDocumentLang(locale);
  persistLocale(locale);
}

export async function bootI18n() {
  await setLocale(DEFAULT_LOCALE);
}

export default i18n;
export { i18n };
