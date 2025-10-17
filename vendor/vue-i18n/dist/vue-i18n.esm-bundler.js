import { computed, getCurrentInstance, inject, reactive, ref } from 'vue';

const I18N_SYMBOL = Symbol('mini-i18n');

function resolveMessage(messages, path) {
  if (!messages) return undefined;
  const segments = path.split('.');
  let current = messages;
  for (const segment of segments) {
    if (current && typeof current === 'object' && segment in current) {
      current = current[segment];
    } else {
      return undefined;
    }
  }
  return current;
}

function formatMessage(message, params) {
  if (message == null) return '';
  if (typeof message !== 'string') return String(message);
  if (!params) return message;
  return message.replace(/\{(\w+)\}/g, (_, key) => {
    if (params[key] == null) return '';
    return String(params[key]);
  });
}

function createGlobalScope(options = {}) {
  const fallbackLocale = options.fallbackLocale ?? 'en';
  const locale = ref(options.locale ?? fallbackLocale);
  const messages = reactive(options.messages ?? {});

  const getLocaleMessage = (loc) => messages[loc] ?? {};

  const translate = (key, params) => {
    const primaryMessage = resolveMessage(getLocaleMessage(locale.value), key);
    if (primaryMessage != null) {
      return formatMessage(primaryMessage, params);
    }
    const fallbackMessage = resolveMessage(getLocaleMessage(fallbackLocale), key);
    if (fallbackMessage != null) {
      return formatMessage(fallbackMessage, params);
    }
    return key;
  };

  return {
    locale,
    fallbackLocale,
    messages,
    getLocaleMessage,
    setLocaleMessage(loc, value) {
      messages[loc] = value ?? {};
    },
    t(key, params) {
      return translate(key, params);
    },
    rt: translate,
    availableLocales: computed(() => Object.keys(messages)),
  };
}

export function createI18n(options = {}) {
  const globalScope = createGlobalScope(options);

  const i18n = {
    global: globalScope,
    install(app) {
      app.provide(I18N_SYMBOL, globalScope);
      app.config.globalProperties.$t = (key, params) => globalScope.t(key, params);
    },
  };

  return i18n;
}

export function useI18n() {
  const instance = getCurrentInstance();
  const scope = instance?.appContext?.provides?.[I18N_SYMBOL] ?? inject(I18N_SYMBOL, null);

  if (!scope) {
    throw new Error('[mini-i18n] Missing i18n instance.');
  }

  return {
    t: scope.t,
    locale: scope.locale,
    availableLocales: scope.availableLocales,
  };
}

export default {
  createI18n,
  useI18n,
};
