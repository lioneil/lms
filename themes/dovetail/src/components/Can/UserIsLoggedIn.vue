<template>
  <keep-alive>
    <template v-if="userIsLoggedIn">
      <slot></slot>
    </template>
    <template v-else>
      <slot name="unpermitted"></slot>
    </template>
  </keep-alive>
</template>

<script>
import $auth from '@/core/Auth/auth'
import isEmpty from 'lodash/isEmpty'

export default {
  name: 'UserIsLoggedIn',

  computed: {
    userIsLoggedIn () {
      return !isEmpty($auth.getUser())
    }
  },
}
</script>
