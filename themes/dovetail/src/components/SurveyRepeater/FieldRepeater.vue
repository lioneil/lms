<template>
  <section>
    <template v-if="fieldrepeatersIsEmpty">
      <slot name="empty">
        <div class="text-center">
          <v-card-text style="filter: grayscale(0.8);">
            <note-taking-icon class="primary--text" width="150" height="150"></note-taking-icon>
          </v-card-text>
          <v-card-text>
            <slot name="text">
              <p class="muted--text font-weight-bold mb-0" v-text="trans('No items yet')"></p>
              <p class="muted--text" v-text="trans('Start adding field.')"></p>
            </slot>
          </v-card-text>
          <v-btn class="mt-3" :large="isLarge" @click="add()">
            <v-icon left small>{{ addButtonIcon }}</v-icon>
            {{ trans(addButtonText) }}
          </v-btn>
        </div>
      </slot>
    </template>

    <v-row align="center" :key="i" v-for="(item, i) in fieldrepeaters">
      <v-col cols="12">
        <v-card class="mb-4">
          <v-card-text>
            <div class="d-flex align-center justify-space-between">
              <div>
                <span class="text--text muted--text headline font-weight-bold" v-html="pad(i+1, 2)"></span>
              </div>

              <div>
                <context-prompt class="mr-n2">
                  <v-card max-width="280">
                    <v-card-title>{{ trans('Remove Item') }}</v-card-title>
                    <v-card-text>{{ trans('Doing so will permanently remove the fields from the list. Are you sure you want to proceed?') }}</v-card-text>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn large color="error" text @click.prevent="remove(i)">{{ trans('Remove') }}</v-btn>
                    </v-card-actions>
                  </v-card>
                </context-prompt>
              </div>
            </div>

            <v-row>
              <v-col cols="auto" md="1" class="d-none d-md-block">
                <div class="field-separator"></div>
              </v-col>
              <v-col>
                <v-row>
                  <v-col cols="12">
                    <validation-provider vid="title" :name="trans('title')" rules="required" v-slot="{ errors }">
                      <v-text-field
                        :error-messages="errors"
                        :label="trans('Field Title')"
                        class="dt-text-field"
                        outlined
                        v-model="item.title"
                        >
                      </v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field
                      :label="trans('Field Title (arabic)')"
                      class="dt-text-field"
                      outlined
                      v-model="item.title_arabic"
                      >
                    </v-text-field>
                  </v-col>
                  <v-col cols="12" md="6">
                    <validation-provider vid="total" :name="trans('total')" rules="required" v-slot="{ errors }">
                      <v-text-field
                        :error-messages="errors"
                        :label="trans('Total Number')"
                        class="dt-text-field"
                        outlined
                        v-model="item.total"
                        >
                      </v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12" md="6">
                    <validation-provider vid="wts" :name="trans('wts')" rules="required" v-slot="{ errors }">
                      <v-text-field
                        :error-messages="errors"
                        :label="trans('WTS')"
                        class="dt-text-field"
                        outlined
                        v-model="item.wts"
                        >
                      </v-text-field>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12">
                    <validation-provider vid="comment" :name="trans('comment')" rules="required" v-slot="{ errors }">
                      <v-textarea
                        :error-messages="errors"
                        :label="trans('Comment')"
                        class="dt-text-field"
                        outlined
                        auto-grow
                        v-model="item.comment"
                        >
                      </v-textarea>
                    </validation-provider>
                  </v-col>
                  <v-col cols="12">
                    <v-textarea
                      :label="trans('Comment (arabic)')"
                      class="dt-text-field"
                      outlined
                      auto-grow
                      v-model="item.comment_arabic"
                      >
                    </v-textarea>
                  </v-col>
                  <v-col cols="12">
                    <div class="my-3">
                      <h4 class="mb-3 muted--text">{{ trans('Choose Category') }}:</h4>
                      <validation-provider vid="categories" :name="trans('categories')" v-slot="{ errors }">
                        <v-chip-group
                          active-class="primary--text"
                          color="blue"
                          large
                          multiple
                          v-model="item.categories"
                          >
                          <v-chip :value="category" v-for="category in categories" :key="category">
                            {{ category }}
                          </v-chip>
                        </v-chip-group>
                      </validation-provider>
                    </div>
                  </v-col>
                </v-row>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Add Item -->
    <v-row v-if="fieldrepeatersIsNotEmpty">
      <v-col>
        <slot name="action" :on="{on: add}">
          <v-btn :large="isLarge" @click="add()">
            <v-icon left small>{{ addButtonIcon }}</v-icon>
            {{ trans(addButtonText) }}
          </v-btn>
        </slot>
      </v-col>
    </v-row>
    <!-- Add Item -->
  </section>
</template>

<script>
import clone from 'lodash/clone'
import isEmpty from 'lodash/isEmpty'
import { mapGetters } from 'vuex'

export default {
  name: 'FieldRepeater',
  props: {
    value: {
      type: [Array, Object],
    },
    addButtonText: {
      type: String,
      default: 'Add Field',
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
    dense: {
      type: Boolean
    },
  },
  computed: {
    // ...mapGetters({
    //   item: 'fieldrepeater/template',
    // }),
    item () {
      return {
        title: '',
        title_arabic: '',
        code: '',
        total: '',
        wts: '',
        comment: '',
        comment_arabic: '',
        categories: [],
      }
    },

    isLarge () {
      return !this.dense
    },

    fieldrepeatersIsEmpty () {
      return isEmpty(this.fieldrepeaters)
    },

    fieldrepeatersIsNotEmpty () {
      return !this.fieldrepeatersIsEmpty
    },

    fieldrepeaters: {
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
    categories: [
      'Documentation',
      'Talent',
      'Technology',
      'Workflow Processes',
    ],
  }),

  methods: {
    pad (num, places) {
      return String(num).padStart(places, '0')
    },
    add: function (focus = true) {
      this.fieldrepeaters.push(Object.assign({}, clone(this.item)))
      this.fieldrepeaters = Object.assign({}, this.fieldrepeaters)
      this.focusOnAdd(focus)
    },
    remove: function (i) {
      this.fieldrepeaters.splice(i, 1)
      this.fieldrepeaters = Object.assign({}, this.fieldrepeaters)
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
  },

  mounted () {
    this.addUserDefinedDefaults()
  },

  watch: {
    autofocus: function (val) {
      this.focus = parseInt(val)
    },
    fieldrepeatersX: {
      handler: function (val) {
        this.$emit('input', val)
      },
      deep: true,
    },
  },
}
</script>
