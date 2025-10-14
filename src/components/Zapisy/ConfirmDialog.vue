<template>
  <teleport to="body">
    <div v-if="visible" class="confirm-dialog-overlay" @click.self="onCancel">
      <div class="confirm-dialog">
        <div class="confirm-dialog-title">{{ title }}</div>
        <div class="confirm-dialog-message">{{ message }}</div>
        <div class="confirm-dialog-actions">
          <button class="button" @click="onCancel">Zrušiť</button>
          <button class="button red" @click="onConfirm">Vymazať</button>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script>
export default {
  name: "ConfirmDialog",
  props: {
    visible: Boolean,
    title: {
      type: String,
      default: "Potvrdenie",
    },
    message: String,
  },
  emits: ["confirm", "cancel"],
  methods: {
    onCancel() {
      this.$emit("cancel");
    },
    onConfirm() {
      this.$emit("confirm");
    },
  },
};
</script>

<style scoped>
.confirm-dialog-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.26);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
}

.confirm-dialog {
  background: #fff;
  border-radius: 24px;
  box-shadow: 0 8px 40px 0 rgba(80, 80, 90, 0.17);
  padding: 2em 2.5em 1.3em 2.5em;
  min-width: 320px;
  text-align: center;
  font-family: inherit;
}

.confirm-dialog-title {
  font-size: 1.45em;
  font-weight: 700;
  margin-bottom: 0.5em;
}

.confirm-dialog-message {
  font-size: 1.15em;
  margin-bottom: 1.2em;
}

.confirm-dialog-actions {
  display: flex;
  gap: 1em;
  justify-content: center;
}

.button {
  border: none;
  border-radius: 12px;
  padding: 0.6em 1.6em;
  font-size: 1em;
  cursor: pointer;
  background: #fef35a;
  color: #222;
  font-weight: 600;
  transition: background 0.16s;
}

.button.red {
  background: #fa4141;
  color: #fff;
}

.button:hover {
  filter: brightness(0.98);
}
</style>
