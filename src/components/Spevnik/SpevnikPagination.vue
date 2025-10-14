<template>
  <div class="pagination">
    <button
      v-for="page in totalPages"
      :key="page"
      class="page-button"
      :class="{ active: page === current }"
      @click="$emit('change-page', page)"
    >
      <p>{{ page }}</p>
    </button>

    <button
      class="page-button next"
      :disabled="current === totalPages"
      @click="$emit('change-page', current + 1)"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="6"
        height="10"
        viewBox="0 0 6 10"
        fill="none"
      >
        <path
          d="M1.14062 0.157509C0.909375 -0.0455796 0.61875 -0.0522749 0.382812 0.137424C0.146875 0.327122 0 0.684202 0 1.07253V8.92828C0 9.3166 0.146875 9.67368 0.382812 9.86338C0.61875 10.0531 0.909375 10.0442 1.14062 9.8433L5.64062 5.91542C5.86406 5.72126 6 5.37534 6 5.0004C6 4.62547 5.86406 4.28178 5.64062 4.08539L1.14062 0.157509Z"
          fill="black"
        />
      </svg>
    </button>
  </div>
</template>

<script>
export default {
  name: "SpevnikPagination",
  props: {
    total: {
      type: Number,
      required: true,
    },
    current: {
      type: Number,
      required: true,
    },
    perPage: {
      type: Number,
      required: true,
    },
  },
  computed: {
    totalPages() {
      return Math.ceil(this.total / this.perPage);
    },
  },
};
</script>

<style scoped lang="scss">
.pagination {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 0.5em;
  margin: 1em auto;

  .page-button {
    width: 21px;
    height: 21px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #fef35a;
    border: none;
    border-radius: 0.4em;
    cursor: pointer;
    box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.2);
    transition: transform 0.15s ease-in-out;
    font-size: 0.875rem;
    line-height: 1;

    &:hover:not(:disabled) {
      transform: scale(1.1);
    }

    &.active {
      background-color: #90ca50;
      font-weight: bold;
    }

    &.next {
      &:disabled {
        opacity: 0.4;
        cursor: default;
      }

      svg {
        width: 12px;
        height: 12px;
      }
    }

    p {
      margin: 0;
    }
  }
}
</style>
