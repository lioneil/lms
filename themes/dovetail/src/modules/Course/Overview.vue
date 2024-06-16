<template>
  <web-container>
    <metatag :title="`${resource.data.title} - Overview`"></metatag>

    <page-header :back="{ to: { name: 'courses.index' }, text: trans('Courses') }">
      <template v-slot:title>
        <span v-text="trans('Course Overview')"></span>
      </template>

      <template v-slot:utilities>
        <template v-if="hasResource">
          <can code="courses.edit">
            <router-link
              :to="{name: 'courses.edit', params: { id: resource.data.id }, query: {from: $route.fullPath}}"
              class="dt-link text-decoration-none mr-6"
              exact
              tag="a"
              v-if="hasResource"
              >
              <v-icon small class="mb-1">mdi-pencil-outline</v-icon>
              <span v-text="trans('Edit Course')"></span>
            </router-link>
          </can>
          <can code="courses.edit">
            <router-link v-if="hasResource" tag="a" class="dt-link text-decoration-none mr-6" exact :to="{name: 'contents.index', params: {id: resource.data.id}, query: {from: $route.fullPath}}">
              <v-icon small class="mb-1">mdi-file-document-edit-outline</v-icon>
              <span v-text="trans('Manage Contents')"></span>
            </router-link>
          </can>
          <can code="courses.destroy">
            <a href="#" @click.prevent="askUserToDestroyResource(resource)" class="dt-link text-decoration-none mr-6">
              <v-icon small class="mb-1">mdi-delete-outline</v-icon>
              <span v-text="trans('Move to trash')"></span>
            </a>
          </can>
        </template>
      </template>

      <template v-slot:action>
        <!-- Subscription -->
        <subscribe-button
          v-show="!resource.data.subscribed"
          @subscribed="handleSubscribed"
          :params="{ user_id: auth.id}"
          :url="api.subscribe(resource.data.id)"
        ></subscribe-button>

        <unsubscribe-button
          v-show="resource.data.subscribed"
          @unsubscribed="handleUnsubscribed"
          :params="{ user_id: auth.id}"
          :url="api.unsubscribe(resource.data.id)"
        ></unsubscribe-button>
        <!-- Subscription -->
      </template>
    </page-header>

    <v-slide-y-transition mode="in-out">
      <template v-if="resource.loading">
        <skeleton-loading-overview></skeleton-loading-overview>
      </template>
    </v-slide-y-transition>

    <template v-if="!resource.loading">
      <!-- Course Detail -->
      <course-overview-detail v-model="resource.data"></course-overview-detail>
      <!-- Course Detail -->

      <!-- Playlist -->
        <template v-if="hasPlaylist">
          <course-overview-playlist v-model="resource.data"></course-overview-playlist>
        </template>

        <template v-else>
          <content-empty-state class="mt-10">
            <template v-slot:text>
              <p class="muted--text" v-text="trans('Click the button below to start building your course.')"></p>
            </template>
            <template v-slot:actions>
              <v-btn v-if="hasResource" color="primary" large exact :to="{name: 'contents.index', params: {id: resource.data.id}, query: {from: $route.fullPath}}">
                <span v-text="trans('Add Content')"></span>
              </v-btn>
            </template>
          </content-empty-state>
        </template>
      <!-- Playlist -->

      <v-card color="transparent" flat height="100"></v-card>
    </template>
  </web-container>
</template>

<script>
import $auth from '@/core/Auth/auth'
import $api from './routes/api'
import Course from './Models/Course'
import { mapActions } from 'vuex'

export default {
  components: {
    SkeletonLoadingOverview: () => import('./cards/SkeletonLoadingOverview'),
    ContentEmptyState: () => import('@/modules/Course/submodules/Content/partials/ContentEmptyState'),
    CourseOverviewPlaylist: () => import('./cards/CourseOverviewPlaylist'),
    CourseOverviewDetail: () => import('./cards/CourseOverviewDetail'),
  },

  computed: {
    hasPlaylist () {
      return this.resource.data.meta.lessons.is_not_empty || false;
    },

    hasResource () {
      return this.resource.data.id
    }
  },

  data: () => ({
    resource: new Course,
    showLessonContent: false,
    auth: $auth.getUser(),
    api: $api
  }),

  methods: {
    ...mapActions({
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      errorDialog: 'dialog/error',
      showSnackbar: 'snackbar/show',
      loadDialog: 'dialog/loading',
    }),

    getResource () {
      this.resource.loading = true
      axios.get($api.single(this.$route.params.courseslug), {
        params: { playlist: true, subscribed_by: $auth.getId() }
      }).then(response => {
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
        title: 'You are about to move to trash the selected course.',
        text: ['Some data related to course will still remain.', trans('Are you sure you want to move :name to Trash?', {name: resource.data.title})],
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

    destroyResource (item) {
      item.loading = true
      axios.delete(
        $api.destroy(item.data.id)
      ).then(response => {
        this.hideDialog()
        this.showSnackbar({
          show: true,
          text: trans_choice('Course successfully moved to trash', 1)
        })
        this.$router.push({ name: 'courses.index' })
      }).catch(err => {
        this.errorDialog({
          width: 400,
          buttons: { cancel: { show: false } },
          title: trans('Whoops! An error occured'),
          text: err.response.data.message,
        })
      }).finally(() => { item.loading = false })
    },

    handleSubscribed () {
      this.showSnackbar({
        text: trans('You successfully subscribed to this course'),
      })
      this.getResource()
    },

    handleUnsubscribed () {
      this.showSnackbar({
        text: trans('You successfully unsubscribed to this course'),
      })
      this.getResource()
    }
  },

  mounted () {
    this.getResource()
  },
}
</script>
