<template>
  <v-skeleton-loader :type="type" :transition="transition"></v-skeleton-loader>
</template>

<script>
export default {
  props: ['type', 'transition'],
}
</script>
