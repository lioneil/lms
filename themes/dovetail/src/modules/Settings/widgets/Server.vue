<template>
  <section>
    <h1 class="mb-3">
      {{ __('Server Information') }}
    </h1>

    <v-card>
      <v-card-text>
        <p>

        </p>
      </v-card-text>
    </v-card>
    </section>
</template>

<script>
import store from '@/store'

export default {
  store,
  name: 'Server',

  data () {
    return {
      resource: {}
    }
  },

  created() {
    axios
      .get('/api/v1/settings/system')
      .then((response) => {
        this.resource = response.data
      })
  },
}
</script>
