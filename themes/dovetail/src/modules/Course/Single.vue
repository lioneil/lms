<template>
  <player-container>
    <metatag :title="`${resource.data.course.title} | ${resource.data.title}`"></metatag>

    <page-header :back="{ to: { name: 'courses.overview' }, text: trans('Course Overview'), query: { from: $route.fullPath } }">
      <template v-slot:title>
        <span v-text="resource.data.title"></span>
      </template>
      <template v-slot:utilities>
        <can code="courses.edit">
          <router-link v-if="hasResource" tag="a" class="dt-link text-decoration-none mr-6" exact :to="{ name: 'courses.edit', params: { id: resource.data.course.id }, query: {from: $route.fullPath} }">
            <v-icon small class="mb-1">mdi-pencil-outline</v-icon>
            <span v-text="trans('Edit Course')"></span>
          </router-link>
        </can>
        <can code="courses.edit">
          <router-link v-if="hasResource" tag="a" class="dt-link text-decoration-none mr-6" exact :to="{name: 'contents.index', params: { id: resource.data.course.id }, query: {from: $route.fullPath}}">
            <v-icon small class="mb-1">mdi-file-document-edit-outline</v-icon>
            <span v-text="trans('Manage Contents')"></span>
          </router-link>
        </can>
      </template>
      <template v-slot:action>
        <div class="d-flex align-center">
          <portal-target name="header:buttons"></portal-target>
          <v-tooltip bottom>
            <template v-slot:activator="{ on, attrs }">
              <v-btn icon v-on="on" @click="togglePlayerMin(!playerMin)">
                <v-icon v-text="('mdi-monitor-screenshot')"></v-icon>
              </v-btn>
            </template>
            <span v-text="playerMin ? 'Maximize Player' : 'Minimize Player'"></span>
          </v-tooltip>
          <v-divider vertical class="mx-3"></v-divider>
          <toggle-theme></toggle-theme>
        </div>
      </template>
    </page-header>

    <template v-if="resource.loading">
      <skeleton-loading-single></skeleton-loading-single>
    </template>

    <v-slide-y-transition mode="in-out">
      <template v-if="!resource.loading">
        <v-row>
          <!-- Content Player -->
          <v-col :cols="$vuetify.breakpoint.smAndDown ? 12 : null" :md="playerMin ? null : '12'" order-md="1">
            <v-card v-if="hasResource" class="dt-player-card-container mb-10">
              <div v-if="resource.data.is_locked">
                <v-row justify="center" align="center">
                  <v-card flat color="transparent" class="text-center muted--text py-6">
                    <div class="icon--disabled">
                      <sad-icon height="200" width="200" class="primary--text"></sad-icon>
                    </div>
                    <v-card-text>
                      <p class="muted--text" v-text="trans('This content is locked.')"></p>
                      <v-btn
                        large
                        color="primary"
                        exact
                        v-if="hasResource"
                        :to="{name: 'courses.overview', params: { courseslug: resource.data.course.slug }}">
                        <v-icon small left>mdi-open-in-new</v-icon>
                        <span v-text="trans('Back to Course Overview')"></span>
                      </v-btn>
                    </v-card-text>
                  </v-card>
                </v-row>
              </div>
              <component
                :is="type"
                :type="resource.data.type"
                v-else
                v-model="resource.data"
              ></component>
            </v-card>

            <portal-target name="theater-mode"></portal-target>
          </v-col>
          <!-- Content Player -->

          <!-- Description and Resources -->
          <v-col :md="playerMin ? '12' : null" :order-md="playerMin ? '3' : '2'">
            <portal to="theater-mode" :disabled="!playerMin">
              <!-- Description -->
              <div>
                <h3 class="mb-3" v-text="trans('Description')"></h3>
                <p v-html="resource.data.description"></p>
                <p>
                  <span v-if="resource.data.course.meta.lessons.count" class="muted--text overline mr-3">
                    <v-icon small class="muted--text mb-1 mr-1">mdi-file-document-multiple-outline</v-icon>
                    <span v-text="`${resource.data.course.meta.lessons.count} Lessons`"></span>
                  </span>
                  <span v-if="resource.data.course.category" class="muted--text overline mr-3">
                    <v-icon small class="muted--text mb-1 mr-1">mdi-tag-outline</v-icon>
                    <span v-html="resource.data.course.category"></span>
                  </span>
                  <span class="muted--text overline mr-3">
                    <v-icon small class="muted--text mb-1 mr-1">mdi-clock-time-four-outline</v-icon>
                    <span v-html="`Published ${resource.data.created}`"></span>
                  </span>
                </p>
              </div>
              <!-- Description -->

              <v-divider class="mt-10"></v-divider>

              <!-- Materials -->
              <template v-if="hasMaterials">
                <div class="mt-10">
                  <h3 class="mb-3" v-text="trans('Resources')"></h3>
                  <div class="ml-n2">
                    <template v-for="material in resource.data.course.materials">
                      <p class="mb-2">
                        <v-icon class="mr-3" small right v-text="material.icon"></v-icon>
                        <v-hover v-slot:default="{ hover }">
                          <a
                            :class="{ 'text-decoration-underline': hover }"
                            :href="material.url"
                            class="text-decoration-none"
                            download
                            v-text="material.title"
                          ></a>
                        </v-hover>
                      </p>
                    </template>
                  </div>
                </div>
              </template>
              <!-- Materials -->

              <v-divider class="mt-10"></v-divider>

              <!-- Discussions -->
              <div class="mt-10">
                <h3 class="mb-6" v-text="trans('Discussions')"></h3>
                <comments-list
                  :title="resource.data.title"
                  :commentable-id="resource.data.id"
                  :items="resource.data.comments"
                  @form:submit="submitComment"
                  commentable-type="Course\Models\Content"
                ></comments-list>
              </div>
              <!-- Discussions -->
            </portal>
          </v-col>
          <!-- Description and Resources -->

          <!-- Playlist -->
          <v-col cols="auto" :order-md="playerMin ? '2' : '3'">
            <v-slide-y-transition>
              <v-card min-width="400" max-width="400">
                <v-card-text>
                  <div class="d-flex align-center justify-start">
                    <div class="pr-3">
                      <v-avatar><img :src="resource.data.course.image" :alt="resource.data.course.title"></v-avatar>
                    </div>
                    <div class="text-capitalize">
                      <h4 v-text="resource.data.course.title"></h4>
                      <div class="body-2 muted--text" v-text="resource.data.course.code"></div>
                    </div>
                  </div>
                </v-card-text>
                <v-divider></v-divider>
                <!-- Content Playlist -->
                <v-card-text>
                  <dt-playlist :items.sync="playlist" color="primary"></dt-playlist>
                </v-card-text>
                <!-- Content Playlist -->
              </v-card>
            </v-slide-y-transition>
          </v-col>
          <!-- Playlist -->
        </v-row>
      </template>
    </v-slide-y-transition>

    <v-card color="transparent" flat height="300"></v-card>
    <back-to-top></back-to-top>
  </player-container>
</template>

<script>
import $auth from '@/core/Auth/auth'
import $api from './routes/api'
import Content from '@/modules/Course/submodules/Content/Models/Content'
import mapValues from 'lodash/mapValues'
import isEmpty from 'lodash/isEmpty'
import { mapActions } from 'vuex'

export default {
  components: {
    AssignmentContentPlayer: () => import('@/modules/Course/submodules/Content/partials/AssignmentContentPlayer'),
    EmbedContentPlayer: () => import('@/modules/Course/submodules/Content/partials/EmbedContentPlayer'),
    PDFContentPlayer: () => import('@/modules/Course/submodules/Content/partials/PDFContentPlayer'),
    PresentationContentPlayer: () => import('@/modules/Course/submodules/Content/partials/PresentationContentPlayer'),
    ScormContentPlayer: () => import('@/modules/Course/submodules/Content/partials/ScormContentPlayer'),
    SkeletonLoadingSingle: () => import('./cards/SkeletonLoadingSingle'),
    TextContentPlayer: () => import('@/modules/Course/submodules/Content/partials/TextContentPlayer'),
    UnsupportedContentPlayer: () => import('@/modules/Course/submodules/Content/partials/UnsupportedContentPlayer'),
    VideoContentPlayer: () => import('@/modules/Course/submodules/Content/partials/VideoContentPlayer'),
  },

  computed: {
    type () {
      let type = `${this.resource.data.metadata.type}Player`
      if (type in this.$options.components) {
        return type
      }
      return 'UnsupportedContentPlayer'
    },

    playlist () {
      return (this.resource.data.playlist || []).map(item => {
        let children = mapValues(item.children, function (item) {
          return Object.assign(item, {
            to: { name: 'courses.lesson', params: { courseslug: item.course.slug, contentslug: item.slug } },
          })
        })

        return Object.assign(item, {
          to: { name: 'courses.lesson', params: { id: item.course_id, content: item.id } },
          active: false,
          children: children,
        })
      })
    },

    hasResource () {
      return this.resource.data.id
    },

    hasMaterials () {
      return !isEmpty(this.resource.data.course.materials)
    }
  },

  data: (vm) => ({
    resource: new Content,
    currentVideoInPlaylist: vm.$route.params.content,
    playerMin: false
  }),

  methods: {
    ...mapActions({
      showSnackbar: 'snackbar/show',
      hideSnackbar: 'snackbar/hide',
    }),

    getResource () {
      this.resource.loading = true
      axios.get($api.lesson(
        this.$route.params.courseslug,
        this.$route.params.contentslug
      ), {
        params: {
          with: ['playlist', 'course', 'comments'],
          subscribed_by: $auth.getId(),
          materials: true
        }
      }).then(response => {
        this.resource.data = response.data.data
        this.comments = response.data.data.comments
      }).finally(() => { this.resource.loading = false })
        // console.log(this.$route.params.courseslug)
    },

    // onContentCompleted (data) {
    //   this.resource.data.playlist = data.playlist
    // },

    // onContentEnded (e) {
    //   this.$store.dispatch('dialog/prompt', {
    //     show: true,
    //     illustration: () => import('@/components/Icons/ChecklistIcon.vue'),
    //     illustrationWidth: 200,
    //     title: `You have finished the title content`,
    //     text: "Next lesson ->",
    //     buttons: {
    //       cancel: {
    //         text: 'Replay',
    //         callback: () => {
    //           this.$store.dispatch('dialog/close')
    //           e.target.currentTime = 0
    //           e.target.play()
    //         },
    //       },
    //       action: {
    //         text: 'Take the Assessment',
    //         callback: (dialog) => {
    //           this.goToLesson({
    //             name: 'assessments.index',
    //           })
    //           this.$store.dispatch('dialog/close')
    //         }
    //       }
    //     }
    //   })
    // },

    getPlayerSizeState () {
      this.playerMin = localStorage.getItem('course:player:min') == 'true'
    },

    togglePlayerMin (val) {
      this.playerMin = val
      localStorage.setItem('course:player:min', val)
    },

    getCommentsList () {
      axios.get($api.lesson(
        this.$route.params.courseslug,
        this.$route.params.contentslug
      ), {
        params: {
          with: ['playlist', 'course', 'comments'],
          subscribed_by: $auth.getId(),
          materials: true
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
  },

  mounted () {
    this.getResource()
    this.getPlayerSizeState()
  },

  watch: {
    '$route.params.contentslug': function () {
      this.getResource()
    },
  },
}
</script>
