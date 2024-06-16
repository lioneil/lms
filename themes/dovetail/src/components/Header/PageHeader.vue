<template>
  <v-row class="mb-8">
    <v-col cols="12">
      <div class="d-block d-sm-flex justify-space-between align-end">
        <div>
          <slot name="back" :back="back">
            <template v-if="$route.query.from">
              <div class="mb-2">
                <router-link tag="a" exact :to="$route.query.from" class="text-decoration-none body-1 dt-link">
                  <v-icon small class="mb-1">mdi mdi-chevron-left</v-icon>
                  <span v-text="trans('Back')"></span>
                </router-link>
              </div>
            </template>
            <template v-else>
              <div class="mb-2" v-if="back">
                <router-link tag="a" exact :to="back.to" class="text-decoration-none body-1 dt-link">
                  <v-icon small class="mb-1">mdi mdi-chevron-left</v-icon>
                  <span v-text="back.text"></span>
                </router-link>
              </div>
            </template>
          </slot>
          <h2 :class="$vuetify.breakpoint.smAndUp ? '' : 'title font-weight-bold'" class="mb-1" :title="heading.description">
            <slot name="title">
              {{ heading.title }}
            </slot>
          </h2>
          <slot name="utilities"></slot>
        </div>

        <div class="mt-sm-0 mt-3"><slot name="action"></slot></div>
      </div>
    </v-col>
  </v-row>
</template>

<script>
export default {
  props: {
    back: Object,
  },
}
</script>
