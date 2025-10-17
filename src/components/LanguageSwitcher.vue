<template>
  <div class="lang-switcher" role="group" aria-label="Language switcher">
    <button
      v-for="l in buttons"
      :key="l.code"
      @click="switchTo(l.code)"
      :class="{ active: current === l.code }"
      type="button"
    >
      {{ l.label }}
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import { setLocale } from '@/i18n';
import i18n from '@/i18n';

const locales = [
  { code: 'sk', labelKey: 'ui.lang.sk' },
  { code: 'cs', labelKey: 'ui.lang.cs' },
  { code: 'pl', labelKey: 'ui.lang.pl' }
];

const { t } = useI18n();

const buttons = computed(() =>
  locales.map((locale) => ({ ...locale, label: t(locale.labelKey) }))
);

const current = computed(() => i18n.global.locale.value);

async function switchTo(code) {
  await setLocale(code);
}
</script>

<style scoped>
.lang-switcher {
  display: flex;
  gap: 0.5rem;
}
.lang-switcher button {
  padding: 0.35rem 0.6rem;
  border: 1px solid #ddd;
  border-radius: 0.4rem;
  cursor: pointer;
  background: #fff;
}
.lang-switcher button.active {
  border-color: #999;
  font-weight: 600;
}
</style>
