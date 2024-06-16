<template>
  <keep-alive>
    <template v-if="check(code)">
      <slot></slot>
    </template>
    <template v-else>
      <slot name="unpermitted"></slot>
    </template>
  </keep-alive>
</template>

<script>
import $auth from '@/core/Auth/auth'
import includes from 'lodash/includes'
import isEmpty from 'lodash/isEmpty'
import isObject from 'lodash/isObject'

export default {
  name: 'Can',

  props: ['code', 'viewable'],

  data: () => {
    return {
      permissions: $auth.getUser().permissions,
    }
  },

  methods: {
    check (permission) {
      if (this.viewable != undefined && !this.viewable) {
        return $auth.isNotSuperAdmin()
      }

      if (isObject(permission)) {
        return !isEmpty(permission.filter((p) => {
          return includes(this.permissions || [], p)
        }))
      }

      return permission === false || includes(this.permissions || [], permission)
    }
  }
}
</script>
