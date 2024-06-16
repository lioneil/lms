<template>
  <v-container class="py-0 px-0">
    <v-row justify="space-between" align="center">
      <v-fade-transition>
        <v-col v-if="isNotFormPrestine" class="py-0" cols="auto">
          <v-toolbar-title class="muted--text d-none d-md-block" v-text="trans('Unsaved changes')"></v-toolbar-title>
        </v-col>
      </v-fade-transition>
      <v-spacer></v-spacer>
      <v-col class="py-0" cols="auto">
        <div class="d-flex justify-end align-center">
          <v-spacer></v-spacer>
          <v-btn
            @click="askUserToDiscardUnsavedChanges"
            class="ml-3 mr-0"
            large
            text
            v-text="trans('Discard')"
            >
          </v-btn>
          <v-badge
            bordered
            bottom
            class="dt-badge"
            color="dark"
            content="s"
            offset-x="20"
            offset-y="20"
            tile
            transition="fade-transition"
            v-model="shortkeyCtrlIsPressed"
            >
            <v-btn
              :disabled="isFormDisabled"
              :loading="isLoading"
              @click.prevent="emitSubmitForm"
              @shortkey="emitSubmitForm"
              class="ml-3 mr-0"
              color="primary"
              large
              ref="submit-button-main"
              type="submit"
              v-shortkey.once="['ctrl', 's']"
              >
              <v-icon left>mdi-content-save-outline</v-icon>
              <slot name="text"></slot>
            </v-btn>
          </v-badge>

          <slot name="utilities"></slot>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  props: ['value', 'routerName', 'isNotFormPrestine', 'shortkeyCtrlIsPressed', 'isFormDisabled', 'isLoading'],

  computed: {
    dataset: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },
  },

  methods: {
    ...mapActions({
      showDialog: 'dialog/show',
      hideDialog: 'dialog/hide',
    }),

    askUserToDiscardUnsavedChanges () {
      this.showDialog({
        illustration: () => import('@/components/Icons/WorkingDeveloperIcon.vue'),
        title: trans('Discard changes?'),
        text: trans('You have unsaved changes on this page. If you navigate away from this page, data will not be recovered.'),
        buttons: {
          cancel: {
            text: trans('Cancel'),
            callback: () => {
              this.hideDialog()
            },
          },
          action: {
            text: trans('Discard'),
            callback: () => {
              this.dataset.isPrestine = true
              this.hideDialog()
              this.$router.replace({ name: this.routerName })
            },
          },
        }
      })
    },

    emitSubmitForm () {
      this.$emit('item:submit', this.value)
    }
  }
}
</script>
