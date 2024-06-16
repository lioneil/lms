<template>
  <context-prompt class="d-inline-block">
    <template v-slot:activator="{ on: context }">
      <v-btn
        color="error"
        large
        v-on="{ ...context }"
        v-text="trans('Unpublish')"
        >
      </v-btn>
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
        <v-btn large color="error" text @click.prevent="unpublish" v-text="trans('unpublish')"></v-btn>
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
    button: {
      type: Boolean,
      default: false,
    },
    chip: {
      type: Boolean,
      default: false,
    },
  },

  methods: {
    unpublish () {
      axios.post(this.url, {
        data: this.params
      })
      .then(response => {
        this.$emit('unpublish', response.data)
      })
    },
  }
}
</script>
