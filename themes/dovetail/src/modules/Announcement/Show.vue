<template>
  <web-container>
    <metatag :title="`${resource.data.title} - Overview`"></metatag>

    <page-header :back="{ to: { name: 'announcements.index' }, text: trans('Announcements') }">
      <template v-slot:title>
        <span v-text="trans(`${resource.data.title}`)"></span>
      </template>

      <template v-slot:utilities>
        <template v-if="hasResource">
          <can code="announcements.edit">
            <router-link
              :to="{name: 'announcements.edit', params: { id: resource.data.id }, query: {from: $route.fullPath}}"
              class="dt-link text-decoration-none mr-6"
              exact
              tag="a"
              v-if="hasResource"
              >
              <v-icon small class="mb-1">mdi-pencil-outline</v-icon>
              <span v-text="trans('Edit Announcement')"></span>
            </router-link>
          </can>
          <can code="announcements.destroy">
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
          <!-- Announcement Detail -->
          <v-row>
            <v-col cols="12" md="10">
            <v-chip
              class="mb-3"
              color="green"
              outlined
              v-if="resource.data.category_id"
              >
              <span v-text="getCategoryById(resource.data.category_id)"></span>
            </v-chip>
            <div class="ck-content dt-editor" v-html="resource.data.body"></div>
            </v-col>
          </v-row>

          <div class="body-1 muted--text" v-text="`by ${resource.data.author}`"></div>
          <div class="overline muted--text" v-text="resource.data.created"></div>
          <!-- Announcement Detail -->

          <v-card color="transparent" flat height="100"></v-card>
        </template>
      </div>
    </v-slide-y-transition>
  </web-container>
</template>

<script>
import $auth from '@/core/Auth/auth'
import $api from './routes/api'
import Announcement from './Models/Announcement'
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
  },

  data: () => ({
    resource: new Announcement,
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
      }).finally(() => { this.resource.loading = false })
    },

    askUserToDestroyResource (resource) {
      this.showDialog({
        color: 'warning',
        illustration: () => import('@/components/Icons/ManThrowingAwayPaperIcon.vue'),
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: 'You are about to move to trash the selected announcement.',
        text: ['Some data related to announcement will still remain.', trans('Are you sure you want to move :name to Trash?', {name: resource.data.title})],
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
          text: trans_choice('Announcement successfully moved to trash', 1)
        })
        this.$router.push({ name: 'announcements.index' })
      }).catch(err => {
        this.errorDialog({
          width: 400,
          buttons: { cancel: { show: false } },
          title: trans('Whoops! An error occured'),
          text: err.response.data.message,
        })
      }).finally(() => { item.loading = false })
    },
  },

  mounted () {
    this.getResource()
  },
}
</script>
