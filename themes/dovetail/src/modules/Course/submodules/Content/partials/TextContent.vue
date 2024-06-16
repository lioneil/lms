<template>
  <v-card :color="$vuetify.theme.isDark ? 'workspace' : 'white'" :class="{ 'dt-full-width': fullWidth }" flat>
    <div :class="{ 'container': fullWidth }">
      <v-card-title class="px-0" v-text="trans('Text Content')">
        <v-spacer></v-spacer>
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn v-on="on" icon @click="toggleFullWidth">
              <v-icon v-text="fullWidth ? 'mdi-fullscreen-exit' : 'mdi-fullscreen'"></v-icon>
            </v-btn>
          </template>
          <span v-text="fullWidth ? 'Exit fullscreen' : 'Fullscreen'"></span>
        </v-tooltip>

        <v-divider vertical class="mx-3"></v-divider>

        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn
              @click="previewContent = true"
              icon
              text
              v-on="on"
              >
              <v-icon>mdi-monitor-eye</v-icon>
            </v-btn>
          </template>
          <span v-text="trans('Preview Content')"></span>
        </v-tooltip>
      </v-card-title>

      <v-card-text class="pa-0">
        <dt-editor :class="fullWidth ? 'fullwidth' : null" v-model="dataset" name="content" :url="url"></dt-editor>
        <!-- Preview -->
        <v-dialog v-model="previewContent" fullscreen hide-overlay transition="dialog-bottom-transition">
          <v-card flat tile>
            <v-toolbar elevation="1" color="bar">
              <v-container>
                <div class="d-flex justify-space-between align-center">
                  <v-toolbar-title v-text="trans('Preview Content')"></v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-btn icon @click="previewContent = false">
                      <v-icon>mdi-close</v-icon>
                    </v-btn>
                </div>
            </v-container>
            </v-toolbar>

            <v-container>
              <v-row>
                <v-col cols="12">
                  <div class="ck-content" v-html="dataset"></div>
                </v-col>
              </v-row>
            </v-container>
          </v-card>
        </v-dialog>
        <!-- Preview -->
      </v-card-text>
    </div>

    <input type="hidden" name="type" value="editor">
    <input type="hidden" name="metadata[type]" value="TextContent">
  </v-card>
</template>

<script>
import $api from '@/modules/Course/routes/api'

export default {
  props: ['value'],

  computed: {
    url () {
      return $api.content.upload()
    },

    dataset: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },
  },

  data: () => ({
    previewContent: false,
    fullWidth: false,
  }),

  methods: {
    toggleFullWidth () {
      this.fullWidth = ! this.fullWidth
    }
  }
}
</script>
