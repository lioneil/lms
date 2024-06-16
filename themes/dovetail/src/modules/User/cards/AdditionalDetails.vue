<template>
  <v-card>
    <v-card-title class="pb-0">{{ trans('Additional Background Details') }}</v-card-title>
    <v-card-text>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            disabled
            label="Mobile Phone"
            class="dt-text-field"
            outlined
            :dense="isDense"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="8">
          <v-text-field
          class="dt-text-field"
            outlined
            :dense="isDense"
          ></v-text-field>
        </v-col>
      </v-row>

      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            disabled
            label="Home Address"
            class="dt-text-field"
            outlined
            :dense="isDense"
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="8">
          <v-text-field
          class="dt-text-field"
            outlined
            :dense="isDense"
          ></v-text-field>
        </v-col>
      </v-row>
      <repeater fields="1"></repeater>
    </v-card-text>
  </v-card>
</template>

<script>
import User from '../Models/User'

export default {
  computed: {
    resource: function () {
      return User
    },

    isDense: function () {
      return this.$vuetify.breakpoint.xlAndUp
      // return this.$vuetify.breakpoint.smAndUp
    },
  },
}
</script>
