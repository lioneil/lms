<template>
  <div v-if="loader" class="loader preloader">
    <v-container fill-height fluid>
      <v-row align="center" justify="center">
        <div class="spinner">
          <svg viewBox="-10 -10 220 220">
            <path d="M200,100 C200,44.771525 155.228475,0 100,0 C44.771525,0 0,44.771525 0,100 C0,155.228475 44.771525,200 100,200 C155.228475,200 200,155.228475 200,100 Z"></path>
          </svg>
        </div>
      </v-row>
    </v-container>
  </div>
</template>

<script>
  export default {
    data: () => ({
     loader: true,
    }),

    methods: {
      removePageLoader() {
        const preloader = document.querySelector('.preloader');

        const fadeEffect = setInterval(() => {
          // if we don't set opacity 1 in CSS, then   //it will be equaled to "", that's why we   // check it
          if (!preloader.style.opacity) {
            preloader.style.opacity = 1;
          }
          if (preloader.style.opacity > 0) {
            preloader.style.opacity -= 0.1;
          } else {
            clearInterval(fadeEffect);
          }
        }, 100);

        window.addEventListener('load', () => { this.loader = false });
      }
    },

    mounted () {
      this.removePageLoader()
    },
  }
</script>
