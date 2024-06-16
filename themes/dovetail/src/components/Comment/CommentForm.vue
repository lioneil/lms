<template>
  <v-bottom-sheet
    inset
    persistent
    scrollable
    hide-overlay
    max-width="800"
    v-model="commentsheet"
    >
    <template v-slot:activator="{ on, attrs }">
      <slot name="activator" v-bind="{ on, attrs }">
        <v-hover v-slot:default="{ hover }">
          <v-card :class="{ 'bar': hover }" :ripple="false" class="mb-6 border-dashed" flat v-bind="attrs" v-on="on" block>
            <v-card-text>
              <v-row justify="center" align="center">
                <v-col cols="auto">
                  <v-avatar>
                    <img
                      :src="auth.avatar"
                      :alt="auth.displayname"
                    >
                  </v-avatar>
                </v-col>
                <v-col>
                  <p class="mb-0" v-text="trans('Contribute to the Discussion')"></p>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
        </v-hover>
      </slot>
    </template>
    <v-card flat class="text-center">
      <v-card-title>
        <h4>
          <span v-text="trans('Reply to ')"></span>
          <span class="primary--text" v-text="title"></span>
        </h4>
      </v-card-title>
      <validation-observer
        v-slot="{ handleSubmit, errors, invalid, passed }"
        >
        <v-form
          ref="comment-form"
          v-on:submit.prevent="handleSubmit(submit($event))"
            >
            <v-card-text>
              <validation-provider
                vid="body"
                :name="trans('field')"
                rules="required"
                v-slot="{ errors }"
                >
                <v-textarea
                  autofocus
                  :error-messages="errors"
                  :placeholder="trans('What\'s on your mind?')"
                  auto-grow
                  class="dt-text-field"
                  name="body"
                  outlined
                  style="max-height: 500px; overflow: auto;"
                  v-model="dataset.body"
                ></v-textarea>
              </validation-provider>
            </v-card-text>
            <v-card-text>
              <div class="text-right">
                <v-btn
                  large
                  @click="commentsheet = !commentsheet"
                  color="link"
                  text
                  v-text="trans('Cancel')"
                  >
                </v-btn>
                <v-btn
                  large
                  color="primary"
                  ref="submit-button"
                  type="submit"
                  v-text="trans('Post')"
                  >
                </v-btn>
              </div>
            </v-card-text>

          <input type="hidden" name="user_id" v-model="dataset.user_id">
          <input type="hidden" name="parent_id" v-model="dataset.parent_id">
          <input type="hidden" name="commentable_id" v-model="dataset.commentable_id">
          <input type="hidden" name="commentable_type" v-model="dataset.commentable_type">
        </v-form>
      </validation-observer>
    </v-card>
  </v-bottom-sheet>
</template>

<script>
import $auth from '@/core/Auth/auth'

export default {
  props: {
    commentableId: {
      type: [String, Number]
    },
    commentableType: {
      type: String
    },
    parentId: {
      type: Number
    },
    title: {
      type: String
    },
  },

  data: (vm) => ({
    auth: $auth.getUser(),
    commentsheet: false,
    dataset: {
      body: null,
      user_id: $auth.getId(),
      commentable_id: vm.commentableId,
      commentable_type: vm.commentableType,
      parent_id: vm.parentId,
    },
  }),

  methods: {
    submit (e) {
      this.$emit('form:submit', { e: e, data: this.dataset, clear: this.clear })
    },

    clear () {
      this.$refs['comment-form'].reset()
      this.commentsheet = false
    },
  },
}
</script>
