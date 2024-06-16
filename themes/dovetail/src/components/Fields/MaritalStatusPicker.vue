<template>
  <validation-provider vid="details[maritalstatus]" :name="trans('Marital Status')" v-slot="{ errors }">
    <v-select
      :dense="isDense"
      :error-messages="errors"
      :items="items"
      :label="trans('Marital Status')"
      :prepend-inner-icon="changeIcon(selected)"
      append-icon="mdi-chevron-down"
      background-color="selects"
      class="dt-text-field"
      item-text="value"
      item-value="value"
      menu-props="offsetY"
      name="details[maritalstatus]"
      outlined
      return-object
      v-model="selected"
      >
      <template v-slot:item="{ item }">
        <v-list-item-icon>
          <v-icon small>{{ item.icon }}</v-icon>
        </v-list-item-icon>
        <v-list-item-content>
          <v-list-item-title>{{ item.value }}</v-list-item-title>
        </v-list-item-content>
      </template>
    </v-select>
  </validation-provider>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'MaritalStatusPicker',

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
      return item && item.icon || 'mdi-checkbox-blank-circle-outline'
    },
  },
}
</script>
