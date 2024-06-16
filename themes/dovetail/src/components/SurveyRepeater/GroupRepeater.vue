<template>
  <section>
    <template v-if="grouprepeatersIsEmpty">
      <slot name="empty">
        <div class="text-center">
          <v-card-text style="filter: grayscale(0.8);">
            <note-taking-icon class="primary--text" height="300" width="300"></note-taking-icon>
          </v-card-text>
          <v-card-text>
            <slot name="text">
              <p class="muted--text font-weight-bold mb-0" v-text="trans('No items yet')"></p>
              <p class="muted--text" v-text="trans('Start adding group.')"></p>
            </slot>
          </v-card-text>
          <v-btn class="mt-3" :large="isLarge" @click="add()">
            <v-icon left small>{{ addButtonIcon }}</v-icon>
            {{ trans(addButtonText) }}
          </v-btn>
        </div>
      </slot>
    </template>

    <v-row :key="i" v-for="(item, i) in grouprepeaters">
      <v-col>
        <v-card>
          <v-toolbar dark color="muted" class="sticky elevation-1">
            <v-avatar><v-icon>mdi-circle</v-icon></v-avatar>
            <v-spacer></v-spacer>
            <v-toolbar-title class="headline font-weight-bold" v-text="item.group">Test bar</v-toolbar-title>
            <v-spacer></v-spacer>
            <context-prompt class="mr-n2">
              <v-card max-width="280">
                <v-card-title>{{ trans('Remove Group') }}</v-card-title>
                <v-card-text>{{ trans('Doing so will permanently remove the group and fields from the list. Are you sure you want to proceed?') }}</v-card-text>
                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn large color="error" text @click.prevent="remove(i)">{{ trans('Remove') }}</v-btn>
                </v-card-actions>
              </v-card>
            </context-prompt>
          </v-toolbar>

          <v-card-text>
            <v-row>
              <v-col cols="auto" md="1" class="d-none d-md-block">
                <div class="group-separator"></div>
              </v-col>
              <v-col>
                <validation-provider vid="group" :name="trans('group')" rules="required" v-slot="{ errors }">
                  <v-text-field
                    :error-messages="errors"
                    :label="trans('Group Name')"
                    class="dt-text-field mt-4"
                    outlined
                    v-model="item.group"
                    >
                  </v-text-field>
                </validation-provider>
                <v-row>
                  <v-col>
                    <v-text-field
                      :label="trans('Group Name (arabic)')"
                      class="dt-text-field"
                      outlined
                      v-model="item.group_arabic"
                      >
                    </v-text-field>
                  </v-col>
                </v-row>
                <field-repeater :fields="fields" :key="i" v-model="item.children"></field-repeater>
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>
      </v-col>
    </v-row>

    <!-- Add Item -->
    <div v-if="grouprepeatersIsNotEmpty">
      <slot name="action" :on="{on: add}">
        <v-btn class="mt-3" :large="isLarge" @click="add()">
          <v-icon left small>{{ addButtonIcon }}</v-icon>
          {{ trans(addButtonText) }}
        </v-btn>
      </slot>
    </div>
    <!-- Add Item -->
  </section>
</template>

<script>
import clone from 'lodash/clone'
import isEmpty from 'lodash/isEmpty'

export default {
  name: 'GroupRepeater',
  props: {
    value: {
      type: [Array, Object],
    },
    addButtonText: {
      type: String,
      default: 'Add Group',
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
    item () {
      return {
        group: '',
        group_arabic: '',
        type: '',
        children: [],
      }
    },

    isLarge () {
      return !this.dense
    },

    grouprepeatersIsEmpty () {
      return isEmpty(this.grouprepeaters)
    },

    grouprepeatersIsNotEmpty () {
      return !this.grouprepeatersIsEmpty
    },

    grouprepeaters: {
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
      this.grouprepeaters.push(Object.assign({}, clone(this.item)))
      this.grouprepeaters = Object.assign({}, this.grouprepeaters)
      this.focusOnAdd(focus)
    },
    remove: function (i) {
      this.grouprepeaters.splice(i, 1)
      this.grouprepeaters = Object.assign({}, this.grouprepeaters)
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
    // this.addUserDefinedDefaults()
  },

  watch: {
    autofocus: function (val) {
      this.focus = parseInt(val)
    },
    grouprepeatersX: {
      handler: function (val) {
        this.$emit('input', val)
      },
      deep: true,
    },
  },
}
</script>
