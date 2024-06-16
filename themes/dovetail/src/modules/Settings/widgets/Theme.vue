<template>
  <section>
    <h1 class="mb-3">
      {{ __('Theme') }}
    </h1>

    <v-card>
      <v-img
        class=""
        height="300"
        :src="resource.thumbnail"
        gradient="to top right, rgba(100,115,201,.45), rgba(25,32,72,.3)"
        >
        <v-container fluid fill-height>
          <v-layout row wrap justify-center align-start>
            <v-card-text class="white--text">
              <h3 class="mb-2">Quill</h3>
              <p class="caption font-weight-bold">
                <span>{{ __('Theme by: ') }} Princess Ellen</span>
              </p>
              <p v-html="resource.description"></p>
            </v-card-text>
          </v-layout>
        </v-container>
      </v-img>
    </v-card>
  </section>
</template>

<script>
import store from '@/store'

export default {
  store,
  name: 'Theme',

  data () {
    return {
      resource: {}
    }
  },

  created() {
    axios
      .get('/api/v1/themes/all')
      .then(response => {
        this.resource = response.data
      })
  },
}
</script>
