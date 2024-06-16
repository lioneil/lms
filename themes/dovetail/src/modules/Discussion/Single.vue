<template>
  <web-container>
    <metatag :title="`${resource.data.title} - Overview`"></metatag>

    <page-header :back="{ to: { name: 'threads.index' }, text: trans('Discussions') }">
      <template v-slot:title>
        <span v-text="trans(`${resource.data.title}`)"></span>
      </template>

      <template v-slot:utilities>
        <template v-if="hasResource">
          <can code="threads.edit">
            <router-link
              :to="{name: 'threads.edit', params: { id: resource.data.id }, query: {from: $route.fullPath}}"
              class="dt-link text-decoration-none mr-6"
              exact
              tag="a"
              v-if="hasResource"
              >
              <v-icon small class="mb-1">mdi-pencil-outline</v-icon>
              <span v-text="trans('Edit Discussion')"></span>
            </router-link>
          </can>
          <can code="threads.destroy">
            <a href="#" @click.prevent="askUserToDestroyResource(resource)" class="dt-link text-decoration-none mr-6">
              <v-icon small class="mb-1">mdi-delete-outline</v-icon>
              <span v-text="trans('Move to trash')"></span>
            </a>
          </can>
        </template>
      </template>

      <template v-slot:action>
        <can code="threads.create">
          <div v-show="resourcesIsNotEmpty">
            <v-btn
              :block="$vuetify.breakpoint.smAndDown"
              to=""
              color="primary"
              exact
              large
              >
              <v-icon small left>mdi-chat-plus-outline</v-icon>
              <span v-text="trans('Reply')"></span>
            </v-btn>
          </div>
        </can>
      </template>
    </page-header>

    <v-slide-y-transition mode="in-out">
      <div>
        <template v-if="resource.loading">
          <skeleton-loading-overview></skeleton-loading-overview>
        </template>

        <template v-if="!resource.loading">
          <!-- Discussion Detail -->
          <v-row>
            <v-col cols="12" md="9">
              <v-chip
                class="mb-3"
                color="green"
                outlined
                v-if="resource.data.category_id"
                >
                <span v-text="getCategoryById(resource.data.category_id)"></span>
              </v-chip>
              <div class="ck-content dt-editor mb-4" v-html="resource.data.body"></div>
              <!-- Discussion Detail -->

              <!-- Author -->
              <v-list flat>
                <v-list-item class="pa-0">
                  <v-list-item-avatar>
                    <v-img
                      :alt="`${resource.data.user.displayname} avatar`"
                      :src="resource.data.user.photo"
                    ></v-img>
                  </v-list-item-avatar>
                  <v-list-item-content>
                    <v-list-item-title class="muted--text" v-text="`by ${resource.data.user.displayname}`"></v-list-item-title>
                    <v-list-item-subtitle class="muted--text" v-text="`posted ${resource.data.created}`"></v-list-item-subtitle>
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <!-- Author -->

              <v-divider class="mt-10"></v-divider>

              <!-- Comments -->
              <div class="mt-10">
                <h3 class="mb-6" v-text="trans('Comments')"></h3>
                <comments-list
                  :title="resource.data.title"
                  :commentable-id="resource.data.id"
                  :items="resource.data.comments"
                  @form:submit="submitComment"
                  commentable-type="Forum\Models\Thread"
                >
                  <template v-slot:formtitle>
                    asd
                  </template>
                </comments-list>
              </div>
              <!-- Comments -->
            </v-col>

            <v-col cols="12" md="3">
              <v-card>
                <v-list>
                  <v-subheader v-text="trans('Categories')"></v-subheader>
                  <v-list-item-group
                    color="primary"
                    >
                    <v-list-item
                      v-for="(item, i) in categories"
                      :key="i"
                    >
                      <v-list-item-content>
                        <v-list-item-title v-text="item.name"></v-list-item-title>
                      </v-list-item-content>
                    </v-list-item>
                  </v-list-item-group>
                </v-list>
              </v-card>
            </v-col>
          </v-row>

          <v-card color="transparent" flat height="100"></v-card>
        </template>
      </div>
    </v-slide-y-transition>
  </web-container>
</template>

<script>
import $auth from '@/core/Auth/auth'
import $api from './routes/api'
import Discussion from './Models/Discussion'
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

    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },
  },

  data: () => ({
    resource: new Discussion,
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
      axios.get($api.show(this.$route.params.id), {
        params:{
          comments: true
        }
      })
      .then(response => {
        this.resource.data = response.data.data
        this.comments = response.data.data.comments
      }).finally(() => { this.resource.loading = false })
    },

    getCommentsList () {
     axios.get($api.show(this.$route.params.id), {
        params:{
          comments: true
        }
      }).then(response => {
        this.resource.data.comments = response.data.data.comments
      })
    },

    submitComment ({ data, clear }) {
      axios.post(
        $api.comments.store(), data
      ).then(() => {
        this.getCommentsList()
      }).then(() => {
        this.showSnackbar({
          text: trans('Your comment posted successfully'),
        })
        clear()
      })
    },

    askUserToDestroyResource (resource) {
      this.showDialog({
        color: 'warning',
        illustration: () => import('@/components/Icons/ManThrowingAwayPaperIcon.vue'),
        illustrationWidth: 200,
        illustrationHeight: 160,
        width: '420',
        title: 'You are about to move to trash the selected discussion.',
        text: ['Some data related to discussion will still remain.', trans('Are you sure you want to move :name to Trash?', {name: resource.data.title})],
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
          text: trans_choice('Discussion successfully moved to trash', 1)
        })
        this.$router.push({ name: 'threads.index' })
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
