<template>
  <section>
    <template v-if="repeatersIsEmpty">
      <slot name="empty">
        <div class="text-center">
          <v-card-text style="filter: grayscale(0.8);">
            <empty-icon class="primary--text" width="300" height="280"></empty-icon>
          </v-card-text>
          <v-card-text>
            <slot name="text">
              <p class="muted--text font-weight-bold mb-0" v-text="trans('No items yet')"></p>
              <p class="muted--text" v-text="trans('Start adding key-value pairs.')"></p>
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
            <v-btn class="mt-3" v-shortkey="['ctrl', 'd']" @shortkey="add()" :large="isLarge" @click="add()">
              <v-icon left small>{{ addButtonIcon }}</v-icon>
              {{ trans(addButtonText) }}
            </v-btn>
          </v-badge>
        </div>
      </slot>
    </template>
    <v-row align="center" :key="i" v-for="(item, i) in repeaters">
      <v-col md="4" sm="6">
        <v-text-field
          :autofocus="focus"
          :dense="dense"
          :label="trans('Key')"
          autocomplete="off"
          class="dt-text-field dt-repeater--key"
          hide-details
          outlined
          prepend-inner-icon="mdi-square-edit-outline"
          v-model="item.key"
          v-shortkey.avoid
          >
        </v-text-field>
      </v-col>
      <v-col>
        <v-text-field
          :dense="dense"
          :label="trans('Value')"
          autocomplete="off"
          class="dt-text-field dt-repeater--value"
          hide-details
          outlined
          v-model="item.value"
        ></v-text-field>
      </v-col>
      <v-col cols="auto">
        <context-prompt>
          <v-card max-width="280">
            <v-card-title>{{ trans('Remove Item') }}</v-card-title>
            <v-card-text>{{ trans('Doing so will permanently remove the key-value pair from the list. Are you sure you want to proceed?') }}</v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn large color="error" text @click.prevent="remove(i)">{{ trans('Remove') }}</v-btn>
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
            <v-btn class="mt-3" v-shortkey="['ctrl', 'd']" @shortkey="add()" :large="isLarge" @click="add()">
              <v-icon left small>{{ addButtonIcon }}</v-icon>
              {{ trans(addButtonText) }}
            </v-btn>
          </v-badge>
        </slot>
      </v-col>
    </v-row>
  </section>
</template>

<script>
import isEmpty from 'lodash/isEmpty'
import { mapGetters } from 'vuex'

export default {
  name: 'Repeater',
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
    dense: {
      type: Boolean
    },
  },
  computed: {
    ...mapGetters({
      item: 'repeater/template',
    }),

    isLarge () {
      return !this.dense
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
