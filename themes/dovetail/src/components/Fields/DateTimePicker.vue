<template>
  <div>
  <v-menu
    :close-on-content-click="false"
    max-width="300px"
    min-width="300px"
    offset-y
    origin="top right"
    ref="date-month-menu"
    transition="scale-transition"
    v-model="menu"
    >
    <template v-slot:activator="{ on }">
      <validation-provider vid="month" :name="name" v-slot="{ errors }">
        <v-text-field
          :dense="isDense"
          :error-messages="errors"
          :label="label"
          autocomplete="off"
          class="dt-text-field maskable"
          clear-icon="mdi mdi-close-circle-outline"
          clearable
          hint="E.g. January 01, 1970 12:00PM"
          persistent-hint
          :name="name"
          outlined
          :value="formatDate(dateFormatted)"
          >
        </v-text-field>
      </validation-provider>
    </template>
  </v-menu>
  </div>
</template>

<script>
import isEmpty from 'lodash/isEmpty'
import moment from 'moment'
import { mapGetters } from 'vuex'
import Inputmask from 'inputmask'

export default {
  props: ['name', 'label', 'icon', 'value'],

  data: vm => ({
    date: '',
    dateFormatted: '',
    mask: 'Aaa 99, 9999 h:s t\\m',
    menu: false,
  }),

  computed: {
    ...mapGetters({
      isDense: 'settings/fieldIsDense',
    }),
  },

  watch: {
    value (val) {
      let dateValue = val == null ? '' : val
      this.dateFormatted = this.formatDate(dateValue)
    },

    date (val) {
      this.dateFormatted = this.formatDate(val)
    },

    dateFormatted (val) {
      this.$emit('input', this.parseDate(val))
    },
  },

  methods: {
    formatDate (date) {
      if (isEmpty(date)) {
        return ''
      }

      return date ? moment(new Date(date)).format('MMM DD, YYYY hh:mm a') : ''
    },

    parseDate (date) {
      if (isEmpty(date)) {
        return ''
      }

      return date ? moment(new Date(date), 'MMM DD, YYYY hh:mm a').format('YYYY-MM-DD hh:mm:ss') : ''
    },
  },

  mounted () {
    Inputmask({
      mask: this.mask,
      alias: 'datetime',
      placeholder: '___ __, ____ __:__ __',
      hourFormat: 12,
    }).mask(document.querySelectorAll('.maskable input'))
  },
}
</script>

