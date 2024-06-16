<template>
  <v-app class="dovetail-app" v-cloak>
    <dt-progressbar></dt-progressbar>

    <sidebar></sidebar>

    <snackbar></snackbar>

    <v-slide-y-transition mode="out-in">
      <router-view></router-view>
    </v-slide-y-transition>

    <dialogbox></dialogbox>
  </v-app>
</template>

<script>
import multiguard from 'vue-router-multiguard';
import permissions from '@/routes/middleware/permissions'
import { mapActions } from 'vuex'

export default {
  name: 'Blank',

  beforeRouteUpdate: multiguard([permissions]),

  created: function () {
    window.axios.interceptors.response.use(undefined, (err) => {
      return new Promise((resolve, reject) => {
        if (err.response.status === Response.HTTP_UNAUTHORIZED && err.config && !err.config.__isRetryRequest) {
          this.$store.dispatch('auth/logout')
        }

        // if (err.response.status === Response.HTTP_FORBIDDEN && err.config && !err.config.__isRetryRequest) {
        //   this.$router.push({ name: 'error.403' })
        // }

        if (err.response.status === Response.HTTP_NOT_FOUND) {
          this.$router.push({ name: 'error.404' })
        }

        if (err.response.status == Response.HTTP_UNPROCESSABLE_ENTITY) {
          this.$store.dispatch('errorbox/show', {
            text: this.$t(err.response.data.message),
            errors: err.response.data.errors,
          })
        }

        if (err.response.status == Response.HTTP_PAYLOAD_TOO_LARGE) {
          this.$store.dispatch('dialog/error', {
            show: true,
            width: 400,
            buttons: { cancel: { show: false } },
            title: "Unable to upload the file",
            text: "The file exceeded the maximum size.",
          })
        }

        if (err.response.status === Response.HTTP_INTERNAL_SERVER_ERROR) {
          this.$store.dispatch('dialog/error', {
            show: true,
            width: 400,
            buttons: { cancel: { show: false } },
            title: err.response.statusText,
            text: err.response.data.message,
          })
        }

        throw err;
      });
    });
  },

  methods: {
    ...mapActions({
      hideAlertbox: 'alertbox/hide',
      hideProgressbar: 'progressbar/hideProgressbar',
      showProgressbar: 'progressbar/showProgressbar',
    }),
  },

  mounted () {
    this.hideAlertbox()
    this.hideProgressbar()
  },
}
</script>

