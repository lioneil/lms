<template>
  <v-card flat color="transparent">
    <v-row justify="center">
      <v-col cols="12" md="10">
        <div v-for="(item, i) in dataset.playlist" :key="i">
          <!-- With children -->
          <div v-if="item.has_child || item.is_section">
            <v-card class="my-4" color="section">
              <v-card-text class="d-flex justify-space-between">
                <div class="d-flex align-center">
                  <h3 :class="{ 'text--ellipsis-1': isMobile }" v-text="item.title"></h3>
                </div>

                <div>
                  <v-btn small icon v-if="item.has_child" @click="item.id = !item.id">
                    <v-icon v-text="item.id ? 'mdi-chevron-up' : 'mdi-chevron-down'"></v-icon>
                  </v-btn>
                </div>
              </v-card-text>
            </v-card>

            <!-- Children -->
            <template v-if="item.has_child">
              <template v-for="(subitem, k) in item.children">
                <v-card :disabled="subitem.progress.locked" exact :to="{ name: 'courses.lesson', params: { courseslug: subitem.course.slug, contentslug: subitem.slug }, query: {from: $route.path} }" class="mb-3" hover flat color="transparent" :key="k">
                  <v-expand-transition>
                    <div v-show="item.id">
                      <v-card-text>
                        <v-row>
                          <v-col cols="auto">
                            <v-avatar size="48" color="section">
                              <v-icon v-if="subitem.progress.locked" color="text lighten-2" v-text="'mdi-lock-outline'"></v-icon>
                              <v-icon v-else v-text="subitem.icon"></v-icon>
                            </v-avatar>
                          </v-col>
                          <v-col>
                            <h3 :class="{ 'text--text text--lighten-1': subitem.progress.locked }" class="mb-3" v-text="subitem.title"></h3>
                            <p :class="{ 'text--text text--lighten-1': subitem.progress.locked }" class="text--ellipsis mb-0" v-text="subitem.description"></p>
                          </v-col>
                        </v-row>
                      </v-card-text>
                    </div>
                  </v-expand-transition>
                </v-card>
              </template>
            </template>

            <template v-else>
              <v-card class="mb-3" flat color="transparent">
                <v-card-text>
                  <v-row>
                    <v-col>
                      <h3 class="mb-3 muted--text font-italic font-weight-regular" v-text="trans('Content coming soon.')"></h3>
                    </v-col>
                  </v-row>
                </v-card-text>
              </v-card>
            </template>
            <!-- Children -->
          </div>
          <!-- With children -->

          <!-- Without children -->
          <div v-else>
            <v-card class="mb-3" hover exact :to="{ name: 'courses.lesson', params: { courseslug: item.course.slug, contentslug: item.slug }, query: {from: $route.path} }" flat color="transparent">
              <v-card-text>
                <v-row>
                  <v-col cols="auto">
                    <v-avatar size="48" color="section">
                      <v-icon v-text="item.icon"></v-icon>
                    </v-avatar>
                  </v-col>
                  <v-col>
                    <h3 class="mb-3" v-text="item.title"></h3>
                    <p class="text--ellipsis mb-0" v-text="item.description"></p>
                  </v-col>
                </v-row>
              </v-card-text>
            </v-card>
          </div>
          <!-- Without children -->
        </div>
      </v-col>
    </v-row>
  </v-card>
</template>

<script>
export default {
  props: ['value'],

  computed: {
    dataset: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },

    isMobile () {
      return this.$vuetify.breakpoint.smAndDown
    },
  },
}
</script>
