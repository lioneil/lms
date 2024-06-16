<template>
  <v-menu
    :close-on-content-click="false"
    max-width="300px"
    min-width="300px"
    offset-y
    origin="top right"
    ref="birthday-picker-menu"
    transition="scale-transition"
    v-model="menu"
    >
    <template v-slot:activator="{ on }">
      <validation-provider vid="details[Birthday][value]" :name="trans('Birthday')" v-slot="{ errors }">
        <v-text-field
          :dense="isDense"
          :error-messages="errors"
          :label="trans('Birthday')"
          @blur="date = parseDate($event.target.value)"
          @focus="date = parseDate($event.target.value)"
          autocomplete="off"
          class="dt-text-field"
          clear-icon="mdi mdi-close-circle-outline"
          clearable
          name="details[Birthday][value]"
          outlined
          prepend-inner-icon="mdi-cake-variant"
          v-mask="mask"
          :value="formatDate(dateFormatted)"
        >
          <template v-slot:append>
            <v-icon v-on="on">mdi-calendar</v-icon>
          </template>
        </v-text-field>
      </validation-provider>
      <input type="hidden" name="details[Birthday][key]" value="Birthday">
      <input type="hidden" name="details[Birthday][icon]" value="mdi-cake-variant">
    </template>
    <v-date-picker v-model="date" width="300px" no-title @input="menu = false">
      <v-spacer></v-spacer>
      <v-btn text color="link" @click="menu = false">{{ trans('Cancel') }}</v-btn>
    </v-date-picker>
  </v-menu>
</template>

<script>
import isEmpty from 'lodash/isEmpty'
import moment from 'moment'
import { mapGetters } from 'vuex'
import { mask } from 'vue-the-mask'

export default {
  directives: {
    mask,
  },

  props: ['value'],

  data: vm => ({
    date: '',
    dateFormatted: '',
    mask: 'Aaa ##, ####',
    menu: false,
  }),

  computed: {
    ...mapGetters({
      isDense: 'settings/fieldIsDense',
    }),
  },

  watch: {
    value (val) {
      let dateValue = val.value == 'null' ? '' : val.value
      this.dateFormatted = this.formatDate(dateValue)
    },

    date (val) {
      this.dateFormatted = this.formatDate(val)
    },

    dateFormatted (val) {
      this.$emit('input', {
        key: this.value.key,
        icon: this.value.icon,
        value: this.parseDate(val),
      })
    },
  },

  methods: {
    formatDate (date) {
      if (isEmpty(date)) {
        return ''
      }

      return date ? moment(new Date(date)).format('MMM DD, YYYY') : ''
    },

    parseDate (date) {
      if (isEmpty(date)) {
        return ''
      }

      return date ? moment(new Date(date), 'MMM DD, YYYY').format('YYYY-MM-DD') : ''
    },
  },
}
</script>
