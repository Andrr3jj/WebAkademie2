import { createI18n } from 'vue-i18n';

const DEFAULT_LOCALE = localStorage.getItem('locale') || 'sk';

const i18n = createI18n({
  legacy: false,
  locale: DEFAULT_LOCALE,
  fallbackLocale: 'sk',
  messages: {}, // lazy loading
});

const loaded = new Set();

async function loadLocaleMessages(locale) {
  if (loaded.has(locale)) return;
  const messages = await import(/* @vite-ignore */ `./locales/${locale}.json`)
    .then((m) => m.default || m)
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
