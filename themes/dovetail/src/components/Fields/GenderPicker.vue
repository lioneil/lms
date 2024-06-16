<template>
  <validation-provider vid="details[Gender]" :name="trans('Gender')" :rules="rules" v-slot="{ errors }">
    <v-select
      :dense="isDense"
      :error-messages="errors"
      :items="items"
      :label="trans('Gender')"
      :prepend-inner-icon="changeIcon(selected)"
      append-icon="mdi-chevron-down"
      background-color="selects"
      class="dt-text-field"
      item-text="value"
      item-value="value"
      menu-props="offsetY"
      name="details[Gender]"
      outlined
      return-object
      v-model="selected"
      >
      <template v-slot:item="{ item }">
        <v-list-item-icon>
          <v-icon :color="item.color || null" small v-text="item.icon"></v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title v-html="item.value || trans('None')"></v-list-item-title>
        </v-list-item-content>
      </template>
    </v-select>
  </validation-provider>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'GenderPicker',

  props: ['value', 'rules', 'items'],

  computed: {
    ...mapGetters({
      isDense: 'settings/fieldIsDense',
    }),

    selected: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      }
    },
  },

  methods: {
    changeIcon (item) {
      return item && item.icon || 'mdi-gender-male-female'
    },
  },
}
</script>
