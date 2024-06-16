<template>
  <div>
    <v-card color="transparent" flat>
      <v-card-text class="px-0">
        <div class="d-flex justify-space-between">
          <div>
            <h4 class="mb-3" v-text="trans('Embed Content')"></h4>
          </div>
          <template v-if="hasOldValue">
            <!-- Delete Content -->
            <div class="text-right mb-3">
              <context-prompt class="d-inline-block">
                <template v-slot:activator="{ on }">
                  <v-btn x-large v-on="on" text color="error" v-text="trans('Remove')"></v-btn>
                </template>
                <v-card max-width="280">
                  <v-card-title v-text="trans('Remove Embed')"></v-card-title>
                  <v-card-text v-text="trans('The embed code will be permanently removed from storage. Are you sure you want to proceed?')"></v-card-text>
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
          <span v-html="dataset"></span>
          <!-- Preview -->
        </v-card-text>
      </template>

      <template v-else>
        <v-card-text class="pa-0">
          <validation-provider
            :name="trans('content')"
            :rules="{ required: true, regex: /^\<iframe.*$/ }"
            v-slot="{ errors }"
            vid="content"
            >
            <v-textarea
              :disabled="isLoading"
              :error-messages="errors"
              :placeholder="trans('Paste an embed code here')"
              auto-grow
              class="dt-text-field mt-5"
              hint="An embed code is wrapped inside iframe tag"
              name="content"
              outlined
              persistent-hint
              v-model="dataset"
              >
            </v-textarea>
          </validation-provider>

          <!-- Preview -->
          <span v-html="dataset"></span>
          <!-- Preview -->
        </v-card-text>
      </template>

      <input type="hidden" name="type" value="embed">
      <input type="hidden" name="metadata[type]" value="EmbedContent">
    </v-card>
  </div>
</template>

<script>
import isEmpty from 'lodash/isEmpty'
import { mapGetters } from 'vuex'

export default {
  props: ['value', 'hasOld', 'type'],

  computed: {
    ...mapGetters({
      progressbar: 'progressbar/progressbar',
    }),

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
