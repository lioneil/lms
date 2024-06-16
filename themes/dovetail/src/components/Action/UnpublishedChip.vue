<template>
  <context-prompt class="d-inline-block">
    <template v-slot:activator="{ on: context }">
      <v-chip
        :color="$vuetify.theme.isDark ? 'soft-error' : 'red lighten-5'"
        :title="trans('Click to publish this course')"
        text-color="error"
        v-on="{ ...context }"
        >
        <span v-text="trans('Unpublished')"></span>
      </v-chip>
    </template>
    <v-card max-width="280">
      <v-card-title>
        <slot name="context-title"></slot>
      </v-card-title>
      <v-card-text>
        <slot name="context-text"></slot>
      </v-card-text>
      <v-card-actions>
        <v-btn large color="link" text @click.prevent="cancel = false" v-text="trans('Cancel')"></v-btn>
        <v-spacer></v-spacer>
        <v-btn large color="success" text @click.prevent="publish" v-text="trans('Publish')"></v-btn>
      </v-card-actions>
    </v-card>
  </context-prompt>
</template>

<script>
export default {
  props: {
    url: {
      type: String
    },
    params: {
      type: Object
    },
  },

  methods: {
    publish () {
      axios.post(this.url, {
        data: this.params
      })
      .then(response => {
        this.$emit('unpublished', response.data)
      })
    },
  }
}
</script>
