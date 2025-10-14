<template>
  <div class="voucher-input">
    <div class="row">
      <input
        type="text"
        :placeholder="placeholder"
        :value="formattedKod"
        @input="onInput"
        @keyup.enter="$emit('submit', formattedKod)"
        maxlength="14"
        autocomplete="off"
      />
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "VoucherInput",
  props: {
    modelValue: String,
    placeholder: {
      type: String,
      default: "Tvoj unikátny kód",
    },
  },
  computed: {
    formattedKod() {
      // Vždy formátuje podľa XXXX-XXXX-XXXX a uppercase
      let cleaned = (this.modelValue || "").replace(/[^A-Za-z0-9]/g, "");
      cleaned = cleaned.toUpperCase().slice(0, 12);
      return cleaned.match(/.{1,4}/g)?.join("-") || "";
    },
  },
  methods: {
    onInput(e) {
      let value = e.target.value.replace(/[^A-Za-z0-9]/g, "");
      value = value.toUpperCase().slice(0, 12);
      const formatted = value.match(/.{1,4}/g)?.join("-") || "";
      this.$emit("update:modelValue", formatted);
    },
  },
};
</script>

<style lang="scss">
.voucher-input {
  width: 90%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;

  input,
  select {
    font-size: 1.4em;
    font-style: normal;
    font-weight: 300;
    line-height: 115%;
    background: transparent;
    border: none;
    padding: 0.4em 0.5em;
    text-align: left;
    width: 100%;
    letter-spacing: 2px;
  }
}

@media only screen and (max-width: 750px) {
  .voucher-input {
    input {
      width: 15em;
      font-size: 1.3em;
    }

    margin-bottom: 3em;
  }
}
</style>
