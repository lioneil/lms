<template>
  <web-container>
    <metatag :title="`${resource.data.title} - Overview`"></metatag>

    <page-header :back="{ to: { name: 'pages.index' }, text: trans('Pages') }">
      <template v-slot:title>
        <span v-text="trans(`${resource.data.title}`)"></span>
      </template>

      <template v-slot:utilities>
        <template v-if="hasResource">
          <can code="pages.edit">
            <router-link
              :to="{name: 'pages.edit', params: { id: resource.data.id }, query: {from: $route.fullPath}}"
              class="dt-link text-decoration-none mr-6"
              exact
              tag="a"
              v-if="hasResource"
              >
              <v-icon small class="mb-1">mdi-pencil-outline</v-icon>
              <span v-text="trans('Edit Page')"></span>
            </router-link>
          </can>
          <can code="pages.destroy">
            <a href="#" @click.prevent="askUserToDestroyResource(resource)" class="dt-link text-decoration-none mr-6">
              <v-icon small class="mb-1">mdi-delete-outline</v-icon>
              <span v-text="trans('Move to trash')"></span>
            </a>
          </can>
        </template>
      </template>
    </page-header>

    <v-slide-y-transition mode="in-out">
      <div>
        <template v-if="resource.loading">
          <skeleton-loading-overview></skeleton-loading-overview>
        </template>

        <template v-if="!resource.loading">
          <!-- Page Detail -->
          <v-chip
            class="mb-3"
            color="green"
            outlined
            v-if="resource.data.category_id"
            >
            <span v-text="getCategoryById(resource.data.category_id)"></span>
          </v-chip>

          <iframe ref="iframe"class="page-iframe" width="100%" :srcdoc="page" @load="pageLoaded">Click edit to add content</iframe>

          <div class="body-1 muted--text" v-text="`by ${resource.data.author}`"></div>
          <div class="overline muted--text" v-text="resource.data.created"></div>
          <!-- Page Detail -->

          <v-card color="transparent" flat height="100"></v-card>
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
  components: {
    SkeletonLoadingOverview: () => import('./cards/SkeletonLoadingOverview'),
  },
  computed: {
    hasResource () {
      return this.resource.data.id
    },

    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
    },

    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
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
    categories: null
  }),

  methods: {
    ...mapActions({
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      errorDialog: 'dialog/error',
      showSnackbar: 'snackbar/show',
      loadDialog: 'dialog/loading',
    }),

    async getResource () {
      try {
        const result = await axios.get(this.api.category.list())
        this.categories = result.data.data
      } catch (err) {
        console.error(err)
      }

      this.resource.loading = true
      axios.get($api.show(this.$route.params.id))
      .then(response => {
        this.resource.data = response.data.data

        // var parser = new DOMParser();
        // var htmlDoc = parser.parseFromString(response.data.data.body, 'text/html')
        // console.log(iframe)
        // iframe.srcdoc = '<p>a</p>'
      }).finally(() => { this.resource.loading = false })
    },

    askUserToDestroyResource (resource) {
      this.showDialog({
        color: 'warning',
        illustration: () => import('@/components/Icons/ManThrowingAwayPaperIcon.vue'),
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: 'You are about to move to trash the selected page.',
        text: ['Some data related to page will still remain.', trans('Are you sure you want to move :name to Trash?', {name: resource.data.title})],
        buttons: {
          cancel: { show: true, color: 'link' },
          action: {
            text: trans('Move to Trash'),
            color: 'warning',
            callback: (dialog) => {
              this.loadDialog(true)
              this.destroyResource(resource)
            }
          }
        }
      })
    },

    getCategoryById (id) {
      if(!this.categories || !id) return
      return this.categories.find(e => e.id === id).name
    },

    destroyResource (item) {
      item.loading = true
      axios.delete(
        $api.destroy(item.data.id)
      ).then(response => {
        this.hideDialog()
        this.showSnackbar({
          show: true,
          text: trans_choice('Page successfully moved to trash', 1)
        })
        this.$router.push({ name: 'pages.index' })
      }).catch(err => {
        this.errorDialog({
          width: 400,
          buttons: { cancel: { show: false } },
          title: trans('Whoops! An error occured'),
          text: err.response.data.message,
        })
      }).finally(() => { item.loading = false })
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
