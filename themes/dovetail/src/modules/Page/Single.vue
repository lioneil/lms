<template>
  <web-container>
    <metatag :title="`${resource.data.title} - Overview`"></metatag>

    <dt-progressbar></dt-progressbar>

    <v-slide-y-transition mode="in-out">
      <div>
        <template v-if="!isFetching">
          <iframe ref="iframe" width="100%" @load="pageLoaded" :srcdoc="page"></iframe>
        </template>
      </div>
    </v-slide-y-transition>
  </web-container>
</template>

<script>
import $auth from '@/core/Auth/auth'
import $api from './routes/api'
import Page from './Models/Page'
import { mapActions } from 'vuex'

export default {
  computed: {
    hasResource () {
      return this.resource.data.id
    },

    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
    },

    isFetching () {
      return this.resource.loading
    },

    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
    },

    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },

    page () {
      if(!this.resource.data.body) return ''
      const { html, css } = JSON.parse(this.resource.data.body)
      let styles = `
        <style>
          html {
            overflow: hidden !important;
            scrollbar-width: none;
            -ms-overflow-style: none;
          }

          html::-webkit-scrollbar {
            width: 0;
            height: 0;
          }
          ${css}
        </style>`

      const doc = `
        <html>
          <head>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css">
            <link rel="stylesheet" href="${window.location.origin}/theme/dist/css/app.css">
            ${styles}
          </head>
          <body>
            ${html}
          </body>
        </html>`

      return doc
    }
  },

  data: () => ({
    resource: new Page,
    auth: $auth.getUser(),
    api: $api,
  }),

  methods: {
    ...mapActions({
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      errorDialog: 'dialog/error',
      showSnackbar: 'snackbar/show',
      loadDialog: 'dialog/loading',
      hideProgressbar: 'progressbar/hideProgressbar',
      showProgressbar: 'progressbar/showProgressbar',
    }),

    getResource () {
      this.showProgressbar()
      axios.get($api.show(this.$route.params.id))
      .then(response => {
        if(!response.data.data.published)
          this.$router.push({ path: '/404' })
        else
          this.resource.data = response.data.data
      })
      .catch(err => {
        this.$router.push({ path: '/404' })
      })
      .finally(() => {
        this.resource.loading = false
        this.hideProgressbar()
      })
    },

    getCategoryById (id) {
      if(!this.categories || !id) return
      return this.categories.find(e => e.id === id).name
    },

    pageLoaded (e) {
      const iframe = e.target;
      iframe.height = iframe.contentDocument.body.offsetHeight
    }
  },

  mounted () {
    this.getResource()
  },
}
</script>
