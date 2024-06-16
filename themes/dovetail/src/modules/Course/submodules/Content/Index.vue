<template>
  <admin>
    <metatag :title="trans('Manage Contents')"></metatag>

    <page-header>
      <template v-slot:title><span v-text="trans('Manage Contents')"></span></template>
      <template v-slot:utilities>
        <can code="courses.edit">
          <router-link
            v-if="hasCourseResource"
            :to="{ name: 'courses.edit', params: { id: course.data.id }, query: {from: $route.fullPath} }"
            class="dt-link text-decoration-none mr-6"
            exact
            tag="a"
            >
            <v-icon small class="mb-1">mdi-pencil-outline</v-icon>
            <span v-text="trans('Edit Course')"></span>
          </router-link>
        </can>
        <router-link
          v-if="hasCourseResource"
          :to="{ name: 'courses.overview', params: { courseslug: course.data.slug }, query: {from: $route.fullPath} }"
          class="dt-link text-decoration-none mr-6"
          exact
          tag="a"
          >
          <v-icon small class="mb-1">mdi-open-in-new</v-icon>
          <span v-text="trans('View Course Overview')"></span>
        </router-link>
        <router-link
          v-if="hasFirstLesson"
          tag="a"
          class="dt-link text-decoration-none mr-6"
          exact
          :to="goToSinglePage(course)"
          >
          <v-icon small class="mb-1">mdi-open-in-new</v-icon>
          <span v-text="trans('View Course Playlist')"></span>
        </router-link>
      </template>
      <template v-slot:action>
        <publish-button
          v-if="course.data.unpublished"
          :url="api.publish(course.data.id)"
          @publish="handleUnpublished"
          >
          <template v-slot:context-title>
            <span v-text="trans('Publish this course')"></span>
          </template>
          <template v-slot:context-text>
            <span v-text="trans('Publishing will let the users see this course and its lessons. Are you sure you want to proceed?')"></span>
          </template>
        </publish-button>

        <unpublish-button
          v-if="course.data.published"
          :url="api.unpublish(course.data.id)"
          @unpublish="handlePublished"
          >
          <template v-slot:context-title>
            <span v-text="trans('Unpublish this course')"></span>
          </template>
          <template v-slot:context-text>
            <span v-text="trans('Unpublishing will not let the users see this course and its lessons. Are you sure you want to proceed?')"></span>
          </template>
        </unpublish-button>
      </template>
    </page-header>

    <template v-if="resource.loading">
      <skeleton-loading-content></skeleton-loading-content>
    </template>

    <template v-else>
      <v-row>
        <v-col cols="12" md="9">
          <!-- Course Details -->
          <course-detail v-model="course.data"></course-detail>
          <!-- Course Details -->

          <!-- List of Contents -->
          <div v-show="resourcesIsNotEmpty">
            <v-row class="mb-4">
              <v-col cols="12">
                <data-sorter @sorted="sortSections" :items="course.data.contents">
                  <template v-slot:item="{ item }">
                    <v-hover v-slot:default="{ hover }">
                      <!-- List of Contents -->
                      <v-card
                        :class="cardClass(hover, item)"
                        class="mb-3"
                        :flat="!item.is_section"
                        :color="item.is_section ? 'section' : 'transparent'"
                        >
                        <v-card-text class="py-2">
                          <v-row no-gutters align="center">
                            <v-col cols="auto"><v-icon left v-text="item.icon"></v-icon></v-col>

                            <!-- Is Section -->
                            <v-col cols="auto" v-if="item.is_section">
                              <inline-edit v-model="item.title" :id="item.id" @save="updateSection" name="title">
                                <span
                                  :title="item.title"
                                  class="section-text font-weight-bold d-inline-block mr-4"
                                  v-text="item.title"
                                ></span>

                                <template v-slot:edit="{ edit }">
                                  <a
                                    :class="{ 't-d-none t-d-hover-lined d-inline-block show-btns mx-1': hover }"
                                    @click="edit"
                                    class="d-none"
                                    v-text="trans('Change name')"
                                    >
                                  </a>
                                </template>
                              </inline-edit>
                            </v-col>
                            <!-- Is Section -->

                            <v-col v-else cols="auto">
                              <div class="mr-4 d-inline-block section-text" :title="item.title" v-text="item.title"></div>
                            </v-col>
                            <v-col>
                              <div class="d-none" :class="{ 'd-flex': hover }">

                                <!-- Actions -->
                                <div class="d-inline-block">
                                  <!-- Edit -->
                                  <template v-if="!item.is_section">
                                    <router-link
                                      :class="{ 't-d-none t-d-hover-lined show-btns': hover }"
                                      :to="{ name: 'contents.edit', params: {id: course.data.id, content: item.id}, query: { type: item.metadata.type, from: $route.fullPath} }"
                                      class="mx-1 transparent"
                                      exact
                                      tag="a"
                                      v-text="trans('Edit')"
                                      >
                                    </router-link>
                                  </template>
                                  <!-- Edit -->

                                  <!-- Copy -->
                                  <a
                                    :class="{ 't-d-none t-d-hover-lined show-btns': hover }"
                                    @click.prevent="clone(item)"
                                    class="mx-1 transparent"
                                    v-text="trans('Copy of')"
                                  ></a>
                                  <!-- Copy -->

                                  <!-- Remove -->
                                  <context-prompt class="d-inline-block">
                                    <template v-slot:activator="{ on }">
                                      <a
                                        :class="{ 't-d-none t-d-hover-lined show-btns': hover }"
                                        class="mx-1 transparent"
                                        v-on="on"
                                        v-text="trans('Remove')"
                                      ></a>
                                    </template>
                                    <v-card max-width="280">
                                      <v-card-title v-text="trans('Remove Item')"></v-card-title>
                                      <v-card-text v-text="trans('Doing so will permanently remove the content from the list. Are you sure you want to proceed?')"></v-card-text>
                                      <v-card-actions>
                                        <v-btn large color="link" text @click.prevent="cancel = false" v-text="trans('Cancel')"></v-btn>
                                        <v-spacer></v-spacer>
                                        <v-btn large color="error" text @click.prevent="remove(item)" v-text="trans('Remove')"></v-btn>
                                      </v-card-actions>
                                    </v-card>
                                  </context-prompt>
                                  <!-- Remove -->
                                  <!-- Actions -->
                                </div>

                                <v-spacer></v-spacer>

                                <!-- Reorder -->
                                <div class="mx-1 d-inline-block handle sorter">
                                  <div><v-icon>mdi-drag</v-icon></div>
                                </div>
                                <!-- Reorder -->

                              </div>
                            </v-col>
                          </v-row>
                        </v-card-text>
                      </v-card>
                      <!-- List of Contents -->
                    </v-hover>
                  </template>
                </data-sorter>
              </v-col>
            </v-row>
          </div>
          <!-- List of Contents -->

          <!-- Empty state -->
          <div v-if="resourcesIsEmpty">
            <content-empty-state class="my-10 pt-10">
              <template v-slot:text>
                <p class="muted--text mb-8" v-text="trans('Choose contents from the right side to start building your course')"></p>
              </template>
            </content-empty-state>
          </div>
          <!-- Empty state -->
        </v-col>

        <v-col cols="12" md="3">
          <div class="sticky-w-space">
            <v-card class="mb-3">
              <v-card-title v-text="trans('Add Content')">
                <v-spacer></v-spacer>
                <v-icon color="primary">mdi-plus</v-icon>
              </v-card-title>
              <v-divider></v-divider>
              <v-list dense>
                <template v-for="item in resource.contentTypes">
                  <v-list-item
                    :ripple="{ class: 'primary--text' }"
                    @click="addSection(item)"
                    exact
                    >
                    <v-list-item-icon>
                      <v-icon small v-text="item.icon"></v-icon>
                    </v-list-item-icon>
                    <v-list-item-content>
                      <v-list-item-title v-text="item.name"></v-list-item-title>
                    </v-list-item-content>
                  </v-list-item>
                  <v-divider inset v-if="item.divider"></v-divider>
                </template>
              </v-list>
            </v-card>
          </div>
        </v-col>
      </v-row>
    </template>
  </admin>
</template>

<script>
import $api from '@/modules/Course/routes/api'
import $auth from '@/core/Auth/auth'
import clone from 'lodash/clone'
import Content from './Models/Content'
import Course from '@/modules/Course/Models/Course'
import isEmpty from 'lodash/isEmpty'
import { mapActions } from 'vuex'

export default {
  components: {
    ContentEmptyState: () => import('./partials/ContentEmptyState'),
    CourseDetail: () => import('./partials/CourseDetail'),
    SkeletonLoadingContent: () => import('@/modules/Course/cards/SkeletonLoadingContent'),
  },

  computed: {
    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },
    resourcesIsEmpty () {
      return isEmpty(this.course.data.contents) && !this.resource.loading
    },
    hasFirstLesson () {
      return !isEmpty(this.course.data.meta.lessons.first);
    },
    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
    },
    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
    },
    hasCourseResource () {
      return this.course.data.id
    }
  },

  data: () => ({
    api: $api,
    cancel: null,
    handleSort: false,
    course: new Course,
    resource: new Content,
    section: {
      text: 'Section',
      data: null
    },
    auth: $auth.getUser(),
  }),

  methods: {
    ...mapActions({
      showAlertbox: 'alertbox/show',
      hideAlertbox: 'alertbox/hide',
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
      showSnackbar: 'snackbar/show',
      hideSnackbar: 'snackbar/hide',
    }),

    cardClass (hover, item) {
      return {
        'text-truncate-section': hover,
        'mt-8': item.is_section,
        'text-truncate-section-display-md': this.isDesktop,
        'text-truncate-section-display-xs': this.isMobile,
      }
    },

    goToSinglePage (course) {
      return {
        name: 'courses.lesson',
        params: {
          courseslug: course.data.slug,
          contentslug: course.data.meta.lessons.first.slug
        },
        query: {
          from: this.$route.fullPath
        }
      }
    },

    handlePublished () {
      this.showSnackbar({
        text: trans('You have successfully unpublish the course'),
      })
      this.getCourseResource()
    },

    handleUnpublished () {
      this.showSnackbar({
        text: trans('You have successfully publish the course'),
      })
      this.getCourseResource()
    },

    addSection (item) {
      if (item.section) {
        let data = (new Content).data
        this.section.data = data
        this.saveSection({
          text: this.section.text
        })
      } else {
        this.$router.push({
          name: 'contents.create', query: { type: item.code }
        })
      }
    },

    saveSection (item) {
      this.section.text = item.text
      let text = item.text + ' ' + (this.course.data.contents.length + 1)
      let data = {
        title: text,
        subtitle: text,
        course_id: this.course.data.id,
        user_id: $auth.getId(),
        slug: this.slugify(text),
        content: 'content',
        type: 'section',
        sort: this.course.data.contents.length+1,
      }
      axios.post(
        $api.content.store(), data
      ).then(response => {
        this.getCourseResource()
        this.section.data = null
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' })

      })
    },

    updateSection (item) {
      let data = {
        title: item.text,
        subtitle: item.text,
        course_id: this.course.data.id,
        slug: this.slugify(item.text || ''),
        content: 'content',
        type: 'section',
        metadata: { type:'SectionContent' },
      }
      axios.put(
        $api.content.update(item.id), data
      ).then(response => {
        this.getCourseResource()
        this.section.data = null
      }).catch(err => {
        this.form.setErrors(err.response.data.errors)
      })
    },

    sortSections (items) {
      items = {
        contents: items.map((item, i) => {
          let parent = this.getParentSection(i);

          return {
            id: item.id,
            sort: i+1,
            metadata: {
              parent: item.is_section ? null : parent && parent.id,
            },
          }
        }),
      }

      axios.patch(
        $api.content.reorder(), items
      ).then(response => {
      })
    },

    getParentSection(index) {
      let items = clone(this.course.data.contents);
      let sliced = items.slice(0, index);
      sliced.reverse();

      for (var i = 0; i < sliced.length; i++) {
        let current = sliced[i];

        if (current.is_section) {
          return current
        }
      }

      return null;
    },

    load (val = true) {
      this.resource.loading = val
    },

    getCourseResource () {
      axios.get($api.show(this.$route.params.id))
        .then(response => {
          this.course.data = response.data.data
        }).finally(() => { this.load(false) })
    },

    clone (item) {
      axios.post(
        $api.content.clone(item.id)
      ).then(response => {
        this.getCourseResource()
      })
    },

    remove (item) {
      axios.delete(
        $api.content.delete(item.id)
      ).then(response => {
        this.showSnackbar({
          text: trans('Content deleted successfully'),
        })

        this.getCourseResource()
      })
    },
  },

  mounted () {
    this.load()
    this.getCourseResource()
  },
}
</script>
