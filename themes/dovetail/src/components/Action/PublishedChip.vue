<template>
  <context-prompt class="d-inline-block">
    <template v-slot:activator="{ on: context }">
      <v-chip
        :color="$vuetify.theme.isDark ? 'soft-success' : 'teal lighten-5'"
        :title="trans('Click to unpublish this course')"
        text-color="success"
        v-on="{ ...context }"
        >
        <span v-text="trans('Published')"></span>
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
        <v-btn large color="error" text @click.prevent="unpublish" v-text="trans('Unpublish')"></v-btn>
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
    unpublish () {
      axios.post(this.url, {
        data: this.params
      })
      .then(response => {
        this.$emit('published', response.data)
      })
    },
  }
}
</script>
