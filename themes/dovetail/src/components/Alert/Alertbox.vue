<template>
  <div class="dt-alertbox">
    <error-box></error-box>
    <success-box></success-box>
  </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  name: 'Alertbox',

  computed: {
    actionWasSuccessful () {
      return this.$route.query.success
    }
  },

  methods: {
    ...mapActions({
      hideErrorbox: 'errorbox/hide',
      hideSuccessbox: 'successbox/hide',
    }),
  },

  mounted () {
    this.hideErrorbox()

    if (!this.actionWasSuccessful) {
      this.hideSuccessbox()
    }
  },
}
</script>
