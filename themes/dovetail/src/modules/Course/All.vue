<template>
  <web-container>
    <metatag title="All Courses"></metatag>

    <page-header>
      <template v-slot:utilities>
        <can code="courses.index">
          <router-link
            tag="a"
            class="dt-link t-d-none mr-4"
            exact
            :to="{name: 'courses.index', query: {from: $route.fullPath}}"
            >
            <v-icon small left>mdi-open-in-new</v-icon>
            <span v-text="trans('Manage Courses')"></span>
          </router-link>
        </can>
      </template>
    </page-header>

    <template v-if="resources.loading">
      <skeleton-all></skeleton-all>
    </template>

    <template v-else>
      <template v-if="resourcesIsNotEmpty">
        <v-row>
          <!-- Category -->
          <!-- <v-col cols="auto" md="2">
            <v-row>
              <v-col cols="12">
                <v-card flat color="transparent">
                  Category 1
                </v-card>
              </v-col>
            </v-row>
          </v-col> -->
          <!-- Category -->

          <v-col>
            <toolbar-menu :bottomdivider="false" switchable toggleview></toolbar-menu>

            <div class="pa-3">
              <!-- Grid View -->
              <template v-if="toggletoolbar.toggleview">
                <v-row>
                  <v-col cols="12" md="4" v-for="(item, i) in resources.data" :key="i">
                    <v-card
                      height="100%"
                      hover
                      exact
                      :to="{ name: 'courses.overview', params: { courseslug: item.slug }, query: {from: $route.fullPath} }"
                      >
                      <v-img :lazy-src="item.image" :src="item.image" height="250"></v-img>
                      <v-card-text>
                        <p class="overline primary--text mb-1" v-text="item.category"></p>
                        <h3 class="mb-3" v-text="item.title"></h3>
                        <p class="text--ellipsis mb-0" v-text="item.description"></p>
                      </v-card-text>
                    </v-card>
                  </v-col>
                </v-row>
              </template>
              <!-- Grid View -->

              <!-- List View -->
              <template v-else>
                <div class="mb-6" v-for="(item, i) in resources.data" :key="i">
                  <v-card
                    :hover="isDesktop"
                    :to="{ name: 'courses.overview', params: { courseslug: item.slug }, query: {from: $route.fullPath} }"
                    exact
                    outlined
                    >
                    <v-row justify="center" align="center">
                      <v-col cols="12" md="4" align="center">
                        <v-avatar size="160">
                          <v-img
                            :lazy-src="item.image"
                            max-height="200"
                            class="mx-auto"
                            contain
                            :src="item.image">
                          </v-img>
                        </v-avatar>
                      </v-col>
                      <v-col cols="12" md="8" class="pb-0">
                        <v-card-title class="mb-auto pb-0 text-capitalize" v-text="item.title"></v-card-title>
                        <v-card-text class="">
                          <p v-if="item.description" class="text--ellipsis" v-text="item.description"></p>
                          <span class="muted--text overline mr-3" v-text="item.code"></span>
                          <div v-if="item.meta.lessons.count" class="muted--text overline d-block d-md-inline-block mr-3">
                            <v-icon small class="muted--text mb-1 mr-1">mdi-file-document-multiple-outline</v-icon>
                            <span v-text="`${item.meta.lessons.count} Lessons`"></span>
                          </div>
                          <div v-if="item.category" class="muted--text d-inline-block overline mr-3">
                            <v-icon small class="muted--text mb-1 mr-1">mdi-tag-outline</v-icon>
                            <span v-text="item.category"></span>
                          </div>
                          <v-progress-linear color="teal" height="15" :value="item.progress" rounded class="mt-4">
                            <template v-slot="{ value }">
                              <span class="white--text body-2" v-text="item.progress"></span>
                            </template>
                          </v-progress-linear>
                        </v-card-text>
                        <v-card-actions>
                          <v-btn
                            v-if="isMobile"
                            :large="isMobile"
                            :to="isMobile ? { name: 'courses.overview', params: {slug: item.slug} } : null"
                            color="primary"
                            exact
                            text
                            >
                            {{ trans('Overview') }}
                          </v-btn>
                          <v-spacer></v-spacer>
                          <v-btn
                            :large="isMobile"
                            to=""
                            color="primary"
                            exact
                            text
                            v-if="item.progress == '100%'"
                            >
                            <v-icon left>mdi-replay</v-icon>
                            {{ trans('Replay') }}
                          </v-btn>
                          <v-btn
                            :large="isMobile"
                            to=""
                            color="primary"
                            exact
                            text
                            v-else-if="item.next"
                            >
                            {{ trans(item.progress == '0%' ? 'Start' : 'Continue') }}
                          </v-btn>
                        </v-card-actions>
                      </v-col>
                    </v-row>
                  </v-card>
                </div>
              </template>
              <!-- List View -->

              <dt-pagination @change="getResource" v-model="resources.meta"></dt-pagination>
            </div>
          </v-col>
        </v-row>
      </template>

      <template v-else>
        <empty-state></empty-state>
      </template>
    </template>

    <v-card color="transparent" flat height="100"></v-card>
  </web-container>
</template>

<script>
import $api from './routes/api'
import Course from './Models/Course'
import debounce from 'lodash/debounce'
import isEmpty from 'lodash/isEmpty'
import { mapActions, mapGetters } from 'vuex'

export default {
  components: {
    SkeletonAll: () => import('./cards/SkeletonAll'),
  },

  computed: {
    ...mapGetters({
      toggletoolbar: 'toolbar/toolbar',
    }),
    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
    },
    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
    },
    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },
    resourcesIsEmpty () {
      return isEmpty(this.resources.data) && !this.resources.loading
    },
  },

  data: () => ({
    resources: {
      loading: true,
      data: [],
    },
  }),

  methods: {
    ...mapActions({
      errorDialog: 'dialog/error',
      loadDialog: 'dialog/loading',
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      showSnackbar: 'snackbar/show',
    }),

    getResource (params = {}) {
      this.resources.loading = true

      axios.get(
        $api.all(), { params }
      ).then(response => {
        this.resources.data = response.data.data
        this.resources.links = response.data.links
        this.resources.meta = response.data.meta
      }).finally(() => { this.resources.loading = false })
    },
  },

  mounted () {
    this.getResource()
  },
}
</script>

