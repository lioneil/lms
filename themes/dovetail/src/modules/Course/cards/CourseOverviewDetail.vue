<template>
  <v-card outlined class="mb-4">
    <v-card-text>
      <v-row justify="center" align="center">
        <v-col md="7" col="12" class="order-2 order-md-1">
          <div class="text-center text-md-left">
            <h2 class="mb-6 text-capitalize" v-text="dataset.title"></h2>
            <p :title="dataset.description" class="font-weight-regular title" v-html="dataset.description"></p>
            <span class="muted--text overline mr-3" v-text="dataset.code"></span>
            <div v-if="dataset.meta.lessons.count" class="muted--text overline d-block d-md-inline-block mr-3">
              <v-icon small class="muted--text mb-1 mr-1">mdi-file-document-multiple-outline</v-icon>
              <span v-text="`${dataset.meta.lessons.count} Lessons`"></span>
            </div>
            <div v-if="dataset.category" class="muted--text d-inline-block overline mr-3">
              <v-icon small class="muted--text mb-1 mr-1">mdi-tag-outline</v-icon>
              <span v-html="dataset.category"></span>
            </div>

            <div v-if="dataset.subscribed" class="mt-10">
              <v-btn
                :to="{ name: 'courses.lesson', params: { courseslug: dataset.slug, contentslug: dataset.meta.lessons.first.slug }, query: {from: $route.fullPath} }"
                color="primary"
                exact
                :x-large="isMobile"
                :large="isDesktop"
                v-if="hasLesson"
                v-text="trans('Start Course')"
                >
                <v-icon right>mdi-arrow-right</v-icon>
              </v-btn>
            </div>
          </div>
        </v-col>
        <v-col md="auto" sm="12" cols="12" offset-md="1" class="order-1 order-md-2">
          <div class="text-md-left text-center">
            <v-avatar class="dt-box-shadow" :size="isDesktop ? 250 : 150">
              <img :src="dataset.image" :alt="dataset.title">
            </v-avatar>
          </div>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
export default {
  props: ['value'],

  computed: {
    dataset: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },

    hasLesson () {
      return this.dataset
        && this.dataset.meta.lessons.first
        && this.dataset.meta.lessons.is_not_empty
    },

    isDesktop () {
      return this.$vuetify.breakpoint.mdAndUp
    },

    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
    },
  },
}
</script>
