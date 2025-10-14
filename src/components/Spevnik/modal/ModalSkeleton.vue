<template>
  <div class="overlay" @click.self="$emit('close')">
    <div class="modal-overlay" @click.self="$emit('close')">
      <div class="modal-content base-modal">
        <div class="modal-scroll">
          <component
            :is="contentComponent"
            v-bind="contentProps"
            @close="$emit('close')"
          />
        </div>
      </div>
    </div>
    <TheMenu
      class="fake-menu"
      style="margin: 0 1em; opacity: 0; z-index: -1; pointer-events: none"
    />
  </div>
</template>

<script>
import TheMenu from "@/components/Menu/TheMenu.vue";

export default {
  components: { TheMenu },
  props: {
    contentComponent: {
      type: [Object, Function, String],
      required: true,
    },
    contentProps: {
      type: Object,
      default: () => ({}),
    },
  },
  mounted() {
    document.body.style.overflow = "hidden";
    window.scrollTo({ top: 0 });
  },
  beforeUnmount() {
    document.body.style.overflow = "";
  },
};
</script>

<style scoped lang="scss">
.overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100vh;
  background: rgba(0, 0, 0, 0.36);
  z-index: 2000;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 1.5em 1em;
  box-sizing: border-box;
}

.modal-overlay {
  width: 70%;
  max-width: 990px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.base-modal {
  background: #fff;
  border-radius: 32px;
  border: 4px solid #fef35a;
  box-shadow: 0 4px 40px rgba(33, 34, 26, 0.13);
  width: 100%;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}

.modal-scroll {
  overflow-y: auto;
  padding: 2.2em 2.5em;
}

@media (max-width: 1220px) {
  .fake-menu {
    display: none;
  }

  .modal-overlay {
    width: 90%;
  }
}

@media (max-width: 660px) {
  .modal-overlay {
    align-items: flex-start;
    padding: 5vh 2em 10vh;
  }

  .base-modal {
    width: 100%;
    padding: 2em 0;
    height: 70vh;
  }

  .modal-scroll {
    overflow-y: scroll;
    padding: 2em 2em;
    height: 100%;
  }
}
</style>
