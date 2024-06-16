<template>
  <div class="dt-avatar-preview selects" :style="`background-image:url(${preview})`">
    <div class="d-flex justify-end ml-5 mt-4">
      <div class="mt-11" style="margin-right: -58px;">
        <v-btn v-if="hasPreview" x-small fab @click="clearPreview">
          <v-icon small color="muted">mdi-close</v-icon>
        </v-btn>
      </div>
      <div>
        <v-btn :disabled="loading" small fab @click="openFileBrowser">
          <v-icon small color="muted">mdi-upload</v-icon>
        </v-btn>
      </div>
    </div>
    <input
      :name="name"
      @change="onFileChange"
      accept="image/x-png,image/gif,image/jpeg"
      class="d-none"
      ref="fileupload"
      type="file"
      >
    <input type="hidden" :name="parsedAvatar" v-model="preview">
  </div>
</template>

<script>
import isEmpty from 'lodash/isEmpty'

export default {
  name: 'UploadAvatar',

  props: ['value', 'name', 'thumbnail', 'avatar', 'loading'],

  computed: {
    hasPreview () {
      return !isEmpty(this.preview)
    },

    parsedAvatar () {
      return this.avatar ? this.avatar : 'avatar'
    }
  },

  data: (vm) => ({
    file: null,
    preview: JSON.parse(JSON.stringify(vm.value || '')),
    previewWasFromFileBrowser: false,
  }),

  methods: {
    openFileBrowser () {
      this.$refs['fileupload'].click()
    },

    clearPreview () {
      this.preview = null
      this.file = null
    },

    onFileChange (e) {
      this.file = e.target.files[0]
      if (this.file) {
        this.preview = URL.createObjectURL(this.file)
        this.previewWasFromFileBrowser = true
      }
    },
  },

  watch: {
    value: function (val) {
      if (!this.previewWasFromFileBrowser) {
        this.preview = val
      }
    },

    file: function (val) {
      this.$emit('input', val)
      this.$emit('change', val)
    },
  },
}
</script>
