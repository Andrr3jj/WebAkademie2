<template>
  <!-- <button class="btn btn-lg" @click="SnackBar">Show Snackbar</button> -->

  <!-- The actual snackbar -->
  <Transition>
    <div v-if="snackBarVisible == true" @click="SnackBar" id="snackbar">
      {{ $store.state.SnackBarText }}
    </div>
  </Transition>
</template>

<script>
export default {
  data() {
    return {
      snackBarVisible: false,
    };
  },
  mounted() {
    setInterval(() => {
      if (
        this.$store.state.SnackBarText.trim().length > 0 &&
        this.snackBarVisible == false
      ) {
        this.SnackBar();
      }
    }, 500);
  },
  methods: {
    SnackBar() {
      if (this.snackBarVisible == false) {
        this.snackBarVisible = true;
        setTimeout(() => {
          this.snackBarVisible = false;
          this.$store.state.SnackBarText = "";
        }, 5000);
      } else {
        this.snackBarVisible = false;
        this.$store.state.SnackBarText = "";
      }
    },
  },
};
</script>

<style lang="scss" scoped>
body {
  display: flex; /* Center according to the main axis */
  justify-content: center; /* Center according to the secondary axis */
  align-items: center;
}

button {
  color: #000;
  background-color: #999;
  margin-top: 20px;
}

/* The snackbar - position it at the bottom and in the middle of the screen */
#snackbar {
  visibility: visible; /* Hidden by default. Visible on click */
  min-width: 250px; /* Set a default minimum width */
  transform: translateX(-50%);
  color: #000;
  text-align: center; /* Centered text */
  border-radius: 13px; /* Rounded borders */
  padding: 14px 50px; /* Padding */
  position: fixed; /* Sit on top of the screen */
  z-index: 1000; /* Add a z-index if needed */
  left: 50%; /* Center the snackbar */
  bottom: 30px; /* 30px from the bottom */

  // background: #ededed;
  // outline: 0.2em solid #fef35a;
  // box-shadow: inset 0 0 0.3em 1px #fef35a, 5px 5px 20px 0px rgba(0, 0, 0, 0.12);

  filter: drop-shadow(5px 5px 30px rgba(0, 0, 0, 0.2));
  box-shadow: inset 5px 5px 8px rgba(0, 0, 0, 0.35);
  background: #9bc64d;
  border: 3.5px solid #fef35a;

  font-size: 1.35em;
  font-style: normal;
  font-weight: 400;
  letter-spacing: 0.03em;
}

/* Show the snackbar when clicking on a button (class added with JavaScript) */
.show {
  visibility: visible !important; /* Show the snackbar */

  /* Add animation: Take 0.5 seconds to fade in and out the snackbar.
However, delay the fade out process for 2.5 seconds */
  -webkit-animation: fadein 0.5s, fadeout 0.5s 2.5s;
  animation: fadein 0.5s, fadeout 0.5s 2.5s;
}

@media only screen and (max-width: 750px) {
  #snackbar {
    font-size: 1.23em;

    letter-spacing: usnet;
  }
}

/* Animations to fade the snackbar in and out */
@-webkit-keyframes fadein {
  from {
    bottom: 0;
    opacity: 0;
  }
  to {
    bottom: 30px;
    opacity: 1;
  }
}

@keyframes fadein {
  from {
    bottom: 0;
    opacity: 0;
  }
  to {
    bottom: 30px;
    opacity: 1;
  }
}

@-webkit-keyframes fadeout {
  from {
    bottom: 30px;
    opacity: 1;
  }
  to {
    bottom: 0;
    opacity: 0;
  }
}

@keyframes fadeout {
  from {
    bottom: 30px;
    opacity: 1;
  }
  to {
    bottom: 0;
    opacity: 0;
  }
}

.v-enter-active,
.v-leave-active {
  transition: opacity 0.5s ease;
}

.v-enter-from,
.v-leave-to {
  opacity: 0;
}

@media only screen and (max-width: 750px) {
  #snackbar {
    box-sizing: border-box;
    padding: 14px 30px;
  }
}
</style>
