<template>
  <keep-alive v-if="check(code)">
    <slot></slot>
  </keep-alive>
</template>

<script>
import $auth from '@/core/Auth/auth'
import includes from 'lodash/includes'

export default {
  name: 'Cannot',

  props: ['code'],

  data: () => {
    return {
      permissions: $auth.getUser().permissions,
    }
  },

  methods: {
    check (permission) {
      return permission === false || !includes(this.permissions || [], permission)
    }
  }
}
</script>
