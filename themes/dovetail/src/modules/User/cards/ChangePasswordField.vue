<template>
  <v-card>
    <v-card-title>{{ trans('Change Password') }}</v-card-title>
    <v-card-subtitle class="muted--text">{{ trans("You have the ability to change other people's passwords. Leave blank to use their old password.") }}</v-card-subtitle>
    <v-card-text>
      <validation-provider vid="password" :name="trans('password')" rules="min:6" v-slot="{ errors }">
        <v-text-field
          :append-icon="inputTypeIsPassword ? 'mdi-eye-off' : 'mdi-eye'"
          :dense="isDense"
          :error-messages="errors"
          :label="trans('Password')"
          :name="passwordName"
          :type="inputType"
          @click:append="toggleVisibility"
          autocomplete="new-password"
          class="dt-text-field"
          outlined
          prepend-inner-icon="mdi-lock"
          ref="password"
          v-model="password"
          >
        </v-text-field>
      </validation-provider>
    </v-card-text>
  </v-card>
</template>

<script>
import isEmpty from 'lodash/isEmpty'

export default {
  name: 'ChangePasswordField',

  props: {
    value: {
      type: [String],
    },
  },

  computed: {
    password: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },

    passwordName: function () {
      return isEmpty(this.password) ? 'not-password' : 'password'
    },

    inputType: function () {
      return this.inputTypeIsPassword ? 'password' : 'text'
    },

    isDense: function () {
      return this.$vuetify.breakpoint.xlAndUp
    },
  },

  data: () => ({
    inputTypeIsPassword: true,
  }),

  methods: {
    toggleVisibility () {
      this.inputTypeIsPassword = !this.inputTypeIsPassword
    },
  },
}
</script>
