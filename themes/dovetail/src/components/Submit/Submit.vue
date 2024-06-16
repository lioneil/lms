<template>
  <v-btn
    :type="dataset.type"
    :class="dataset.class"
    :color="dataset.color"
    >
    <template v-if="dataset.icon">
      <v-icon>mdi mdi-{{ dataset.icon }} &nbsp;</v-icon>
    </template>
    {{ dataset.label }}
  </v-btn>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Submit',

  props: {
    items: {
      type: [Object, Array],
      default: () => {
        return {}
      }
    }
  },

  data () {
    return {
      dataset: {}
    }
  },

  mounted () {
    this.dataset = Object.assign({}, this.submit, this.items)
  },

  computed: {
    ...mapGetters({
      submit: 'submit/submit'
    })
  }
}
</script>

