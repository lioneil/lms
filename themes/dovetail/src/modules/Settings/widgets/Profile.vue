<template>
  <section>
    <h1 class="mb-3">
      {{ __('Profile') }}
    </h1>

    <v-card>
      <v-card-text>
        <v-card-actions>
          <v-avatar size="120">
            <!-- <img :src="user().photo" alt=""> -->
          </v-avatar>
        </v-card-actions>
      </v-card-text>
    </v-card>
  </section>
</template>

<script>
import store from '@/store'

export default {
  store,
  name: 'Profile',

  data () {
    return {
      resource: {}
    }
  },

  created() {
    axios.get('/api/v1/users/all').then(response => {
      this.resource.items = response.data.data
    })
  },
}
</script>
