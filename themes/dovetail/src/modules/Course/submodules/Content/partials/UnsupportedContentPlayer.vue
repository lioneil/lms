<template>
  <v-row justify="center" align="center">
    <v-card flat color="transparent" class="text-center muted--text py-6">
      <div class="icon--disabled">
        <sad-icon height="200" width="200" class="primary--text"></sad-icon>
      </div>
      <v-card-text>
        <p class="muted--text" v-text="trans('This content is currently unsupported.')"></p>
        <v-btn
          large
          color="primary"
          exact
          v-if="hasResource"
          :to="{name: 'courses.overview', params: { courseslug: course.data.slug }}">
          <v-icon small left>mdi-open-in-new</v-icon>
          <span v-text="trans('Back to Course Overview')"></span>
        </v-btn>
      </v-card-text>
    </v-card>
  </v-row>
</template>

<script>
import $api from '@/modules/Course/routes/api'

export default {
  computed: {
    hasResource () {
      return this.course.data.id
    }
  },

  data: () => ({
    course: {
      data: {}
    }
  }),

  methods: {
    getCourseResource () {
      axios.get($api.show(this.$route.params.id))
        .then(response => {
          this.course.data = response.data.data
        }).finally()
    },
  },

  mounted () {
    this.getCourseResource()
  }
}
</script>
