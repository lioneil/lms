<template>
  <admin>
    <metatag :title="trans('Manage Fields')"></metatag>

    <page-header>
      <template v-slot:title><span v-text="trans('Manage Fields')"></span></template>
      <template v-slot:utilities>
        <can code="assessments.edit">
          <router-link
            v-if="hasAssessmentResource"
            :to="{ name: 'assessments.edit', params: { id: assessment.data.id }, query: {from: $route.fullPath} }"
            class="dt-link text-decoration-none mr-6"
            exact
            tag="a"
            >
            <v-icon small class="mb-1">mdi-pencil-outline</v-icon>
            <span v-text="trans('Edit Assessment')"></span>
          </router-link>
        </can>
      </template>
      <template v-slot:action>
        <publish-button
          v-if="assessment.data.unpublished"
          :url="api.publish(assessment.data.id)"
          @publish="handleUnpublished"
          >
          <template v-slot:context-title>
            <span v-text="trans('Publish this assessment')"></span>
          </template>
          <template v-slot:context-text>
            <span v-text="trans('Publishing will let the users see this assessment and its lessons. Are you sure you want to proceed?')"></span>
          </template>
        </publish-button>

        <unpublish-button
          v-if="assessment.data.published"
          :url="api.unpublish(assessment.data.id)"
          @unpublish="handlePublished"
          >
          <template v-slot:context-title>
            <span v-text="trans('Unpublish this assessment')"></span>
          </template>
          <template v-slot:context-text>
            <span v-text="trans('Unpublishing will not let the users see this assessment and its lessons. Are you sure you want to proceed?')"></span>
          </template>
        </unpublish-button>
      </template>
    </page-header>

    <template v-if="resource.loading">
      <skeleton-loading-field></skeleton-loading-field>
    </template>

    <template v-else>
      <v-row>
        <v-col cols="12" md="9">
          <!-- Assessment Details -->
          <assessment-detail v-model="assessment.data"></assessment-detail>
          <!-- Assessment Details -->

          <!-- List of Fields -->
          <div v-show="resourcesIsNotEmpty">
            <v-row class="mb-4">
              <v-col cols="12">
                <data-sorter @sorted="sortFields" :items="assessment.data.fields">
                  <template v-slot:item="{ item }">
                    <v-hover v-slot:default="{ hover }">
                      <!-- List of fields -->
                      <v-card
                        :class="cardClass(hover, item)"
                        class="mb-3"
                        >
                        <v-card-text class="py-2">
                          <v-row no-gutters align="center">
                            <v-col cols="auto"><v-icon left v-text="getTypeIcon(item.metadata.type)"></v-icon></v-col>
                            <v-col cols="auto">
                              <div class="d-inline text-ellipsis" :title="item.excerpt" v-text="item.excerpt"></div>
                            </v-col>
                            <v-col>
                              <div class="d-none" :class="{ 'd-flex': hover }">

                                <!-- Actions -->
                                <div class="d-inline-block">
                                  <!-- Edit -->
                                  <template v-if="!item.is_section">
                                    <router-link
                                      :class="{ 't-d-none t-d-hover-lined show-btns': hover }"
                                      :to="{ name: 'fields.edit', params: {id: assessment.data.id, field: item.id}, query: { type: item.metadata.type, from: $route.fullPath} }"
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
                                      <v-card-text v-text="trans('Doing so will permanently remove the field from the list. Are you sure you want to proceed?')"></v-card-text>
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
                                <div class="mx-1 d-inline-block handle">
                                  <div><v-icon>mdi-drag</v-icon></div>
                                </div>
                                <!-- Reorder -->

                              </div>
                            </v-col>
                          </v-row>
                        </v-card-text>
                      </v-card>
                    </v-hover>
                  </template>
                </data-sorter>
              </v-col>
            </v-row>
          </div>
          <!-- List of Fields -->

          <!-- Empty state -->
          <div v-if="resourcesIsEmpty">
            <field-empty-state class="my-10 pt-10">
              <template v-slot:text>
                <p class="muted--text mb-8" v-text="trans('Choose fields from the right side to start building your assessment')"></p>
              </template>
            </field-empty-state>
          </div>
          <!-- Empty state -->
        </v-col>

        <v-col cols="12" md="3">
          <div class="sticky-w-space">
            <v-card class="mb-3">
              <v-card-title v-text="trans('Add Field')">
                <v-spacer></v-spacer>
                <v-icon color="primary">mdi-plus</v-icon>
              </v-card-title>
              <v-divider></v-divider>
              <v-list dense>
                <template v-for="item in resource.fieldTypes">
                  <v-list-item
                    :ripple="{ class: 'primary--text' }"
                    @click="addField(item)"
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
import $api from '@/modules/Assessment/routes/api'
import $auth from '@/core/Auth/auth'
import clone from 'lodash/clone'
import Field from './Models/Field'
import Assessment from '@/modules/Assessment/Models/Assessment'
import isEmpty from 'lodash/isEmpty'
import { mapActions } from 'vuex'

export default {
  components: {
    FieldEmptyState: () => import('./partials/FieldEmptyState'),
    AssessmentDetail: () => import('./partials/AssessmentDetail'),
    SkeletonLoadingField: () => import('@/modules/Assessment/cards/SkeletonLoadingField'),
  },

  computed: {
    resourcesIsNotEmpty () {
      return !this.resourcesIsEmpty
    },
    resourcesIsEmpty () {
      return isEmpty(this.assessment.data.fields) && !this.resource.loading
    },
    hasFirstLesson () {
      return !isEmpty(this.assessment.data.meta.lessons.first);
    },
    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
    },
    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
    },
    hasAssessmentResource () {
      return this.assessment.data.id
    }
  },

  data: () => ({
    api: $api,
    cancel: null,
    handleSort: false,
    assessment: new Assessment,
    resource: new Field,
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

    handlePublished () {
      this.showSnackbar({
        text: trans('You have successfully unpublish the assessment'),
      })
      this.getAssessmentResource()
    },

    handleUnpublished () {
      this.showSnackbar({
        text: trans('You have successfully publish the assessment'),
      })
      this.getAssessmentResource()
    },

    getTypeIcon (code) {
      let icon = ''
      this.resource.fieldTypes.forEach(e => {
        if(e.code === code) {
          icon = e.icon
        }
      })
      return icon
    },

    addField (item) {
      this.$router.push({
        name: 'fields.create', query: { type: item.code }
      })
    },

    sortFields (items) {
      items = {
        fields: items.map((item, i) => {
          return {
            id: item.id,
            sort: i + 1,
          }
        }),
      }

      axios.patch(
        $api.field.reorder(), items
      ).then(response => {
      })
    },

    load (val = true) {
      this.resource.loading = val
    },

    getAssessmentResource () {
      axios.get($api.show(this.$route.params.id))
        .then(response => {
          this.assessment.data = response.data.data
        }).finally(() => { this.load(false) })
    },

    clone (item) {
      axios.post(
        $api.field.clone(item.id)
      ).then(response => {
        this.getAssessmentResource()
      })
    },

    remove (item) {
      axios.delete(
        $api.field.delete(item.id)
      ).then(response => {
        this.showSnackbar({
          text: trans('Field deleted successfully'),
        })

        this.getAssessmentResource()
      })
    },
  },

  mounted () {
    this.load()
    this.getAssessmentResource()
  },
}
</script>
