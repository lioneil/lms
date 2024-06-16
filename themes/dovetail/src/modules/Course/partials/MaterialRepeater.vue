<template>
  <section>
    <template v-if="repeatersIsEmpty">
      <slot name="empty">
        <div class="text-center">
          <v-card-text style="filter: grayscale(0.8);">
            <empty-icon class="primary--text" width="250" height="250"></empty-icon>
          </v-card-text>
          <v-card-text>
            <slot name="text">
              <p class="muted--text font-weight-bold mb-0" v-text="trans('No items yet')"></p>
              <p class="muted--text" v-text="trans('Start uploading files.')"></p>
            </slot>
          </v-card-text>
          <v-badge
            bordered
            bottom
            class="dt-badge pa-0"
            color="dark"
            offset-x="20"
            offset-y="20"
            tile
            transition="fade-transition"
            v-model="$store.getters['shortkey/ctrlIsPressed']"
            >
            <template v-slot:badge>
              <div class="small" style="font-size: 11px">d</div>
            </template>
            <v-btn
              @click="add()"
              @shortkey="add()"
              class="mt-3"
              large
              v-shortkey="['ctrl', 'd']"
              v-text="trans(addButtonText)"
              >
              <v-icon left small v-text="addButtonIcon"></v-icon>
            </v-btn>
          </v-badge>
        </div>
      </slot>
    </template>
    <v-row align="start" :key="i" v-for="(item, i) in repeaters">
      <v-col md="6" sm="6">
        <validation-provider vid="title" rules="required" :name="trans('title')" v-slot="{ errors }">
          <v-text-field
            :error-messages="errors"
            :label="trans('Title')"
            autocomplete="off"
            class="dt-text-field dt-repeater--key"
            outlined
            :name="`coursewares[${i}][title]`"
            prepend-inner-icon="mdi-square-edit-outline"
            :title="item.title"
            v-model="item.title"
            v-shortkey.avoid
            >
          </v-text-field>
        </validation-provider>
      </v-col>
      <v-col>
        <template v-if="isNotEmpty(item.uri)">
          <input type="hidden" :name="`coursewares[${i}][uri_old]`" :value="item.uri">

          <div class="mt-4"><code :title="item.uri" class="text--ellipsis-1" v-text="item.uri"></code></div>
        </template>

        <template v-else>
          <validation-provider
            :name="trans('uri')"
            v-slot="{ errors }"
            vid="uri"
            >
            <v-file-input
              :error-messages="errors"
              accept=".pdf,.txt,.ppt,.pptx,.doc,.docx"
              class="dt-text-field dt-repeater--value"
              :name="`coursewares[${i}][uri]`"
              :prepend-icon="null"
              outlined
              :hint="`The maximum supported file size is ${app.settings.max_upload_file_size}`"
              persistent-hint
              placeholder="Upload the material file here"
              prepend-inner-icon="mdi-folder-table-outline"
              show-size
              counter
            ></v-file-input>
          </validation-provider>
        </template>

        <input type="hidden" :name="`coursewares[${i}][type]`" value="material">
        <input type="hidden" :name="`coursewares[${i}][pathname]`" :value="item.pathname">
      </v-col>
      <v-col cols="auto" class="mt-3">
        <context-prompt>
          <v-card max-width="280">
            <v-card-title v-text="trans('Remove Item')"></v-card-title>
            <v-card-text v-text="trans('Doing so will permanently remove this material from the list. Are you sure you want to proceed?')"></v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn large color="error" text @click.prevent="remove(i)" v-text="trans('Remove')"></v-btn>
            </v-card-actions>
          </v-card>
        </context-prompt>
      </v-col>
    </v-row>

    <v-row v-if="repeatersIsNotEmpty" no-gutters>
      <v-col>
        <slot name="action" :on="{on: add}">
          <v-badge
            color="dark"
            transition="fade-transition"
            offset-y="20"
            offset-x="20"
            class="dt-badge"
            bottom
            tile
            v-model="$store.getters['shortkey/ctrlIsPressed']"
            >
            <template v-slot:badge>
              <div class="small" style="font-size: 11px">d</div>
            </template>
            <v-btn
              @click="add()"
              @shortkey="add()"
              class="mt-3"
              large
              v-shortkey="['ctrl', 'd']"
              v-text="trans(addButtonText)"
              >
              <v-icon left small v-text="addButtonIcon"></v-icon>
            </v-btn>
          </v-badge>
        </slot>
      </v-col>
    </v-row>
  </section>
</template>

<script>
import app from '@/config/app'
import isEmpty from 'lodash/isEmpty'
import { mapGetters } from 'vuex'

export default {
  name: 'MaterialRepeater',
  props: {
    value: {
      type: [Array, Object],
    },
    addButtonText: {
      type: String,
      default: 'Add Item',
    },
    addButtonIcon: {
      type: String,
      default: 'mdi-plus-circle-outline',
    },
    fields: {
      type: [Number, String],
      default: 0,
    },
    autofocus: {
      type: Boolean,
    },
  },
  computed: {
    ...mapGetters({
      item: 'repeater/template',
    }),

    app () {
      return app
    },

    repeatersIsEmpty () {
      return isEmpty(this.repeaters)
    },

    repeatersIsNotEmpty () {
      return !this.repeatersIsEmpty
    },

    repeaters: {
      get () {
        return Object.values(this.value || {})
      },
      set (val) {
        this.$emit('input', val)
      },
    },
  },

  data: (vm) => ({
    focus: false,
  }),

  methods: {
    add: function (focus = true) {
      this.repeaters.push(Object.assign({}, this.item))
      this.repeaters = Object.assign({}, this.repeaters)
      this.focusOnAdd(focus)
    },
    remove: function (i) {
      this.repeaters.splice(i, 1)
      this.repeaters = Object.assign({}, this.repeaters)
    },
    addUserDefinedDefaults: function () {
      let fields = parseInt(this.fields)
      for (var i = fields - 1; i >= 0; i--) {
        this.add(false)
      }
    },
    focusOnAdd: function (val = true) {
      this.focus = val
    },
    isNotEmpty (text) {
      return !isEmpty(text)
    }
  },

  mounted () {
    this.addUserDefinedDefaults()
  },

  watch: {
    autofocus: function (val) {
      this.focus = parseInt(val)
    },
    repeatersX: {
      handler: function (val) {
        this.$emit('input', val)
      },
      deep: true,
    },
  },
}
</script>
