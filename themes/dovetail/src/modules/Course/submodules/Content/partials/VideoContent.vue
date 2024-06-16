<template>
  <div>
    <v-card color="transparent" flat>
      <v-card-text class="px-0">
        <div class="d-flex justify-space-between">
          <div>
            <h4 class="mb-3" v-text="trans('Video Content')"></h4>
          </div>
          <template v-if="hasOldValue">
            <!-- Delete Content -->
            <div class="text-right mb-3">
              <context-prompt class="d-inline-block">
                <template v-slot:activator="{ on }">
                  <v-btn x-large v-on="on" text color="error" v-text="trans('Remove')"></v-btn>
                </template>
                <v-card max-width="280">
                  <v-card-title v-text="trans('Remove Video')"></v-card-title>
                  <v-card-text v-text="trans('The video will be permanently removed from storage. Are you sure you want to proceed?')"></v-card-text>
                  <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn large color="error" text @click.prevent="removeOld" v-text="trans('Remove')"></v-btn>
                  </v-card-actions>
                </v-card>
              </context-prompt>
            </div>
            <!-- Delete Content -->
          </template>
        </div>
      </v-card-text>

      <template v-if="hasOldValue">
        <v-card-text class="pa-0">
          <input type="hidden" name="content" :value="dataset">

          <p class="overline" v-text="trans('Content')"></p>
          <div class="mb-3"><code v-text="dataset"></code></div>

          <!-- Preview -->
          <p class="overline" v-text="trans('Preview')"></p>
          <video :src="dataset" width="100%" height="auto" controls></video>
          <!-- Preview -->
        </v-card-text>
      </template>

      <template v-else>
        <v-card-text class="pa-0">
          <validation-provider
            :name="trans('content')"
            rules="required|ext:MP4,MKV,AVI,MOV,WEBM,MPEG,MPG,OGG,WMV"
            v-slot="{ errors }"
            vid="content"
            >
            <v-file-input
              :disabled="isLoading"
              :error-messages="errors"
              accept="video/*"
              class="dt-text-field"
              :hint="`The maximum supported file size is ${app.settings.max_upload_file_size}`"
              name="content"
              :prepend-icon="null"
              outlined
              persistent-hint
              placeholder="Upload video file here"
              prepend-inner-icon="mdi-video-outline"
              show-size
              counter
              v-model="dataset"
              >
            </v-file-input>
          </validation-provider>
        </v-card-text>
      </template>

      <input type="hidden" name="type" value="video">
      <input type="hidden" name="metadata[type]" value="VideoContent">
    </v-card>
  </div>
</template>

<script>
import app from '@/config/app'
import isEmpty from 'lodash/isEmpty'
import { mapGetters } from 'vuex'

export default {
  props: ['value', 'hasOld', 'type'],

  computed: {
    ...mapGetters({
      progressbar: 'progressbar/progressbar',
    }),

    app () {
      return app
    },

    isLoading () {
      return this.progressbar.loading
    },

    hasOldValue () {
      return !this.hasRemovedOld && this.hasOld && !isEmpty(this.dataset)
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

  data: (vm) => ({
    hasRemovedOld: false,
  }),

  methods: {
    removeOld () {
      this.dataset = null
      this.hasRemovedOld = true
    },
  },
}
</script>
