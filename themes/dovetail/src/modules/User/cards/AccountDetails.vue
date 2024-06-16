<template>
  <v-card class="mb-6">
    <v-card-title>{{ trans('Account Details') }}</v-card-title>
    <v-card-subtitle class="muted--text">{{ trans('Fields will be used to sign in to the app') }}</v-card-subtitle>
    <v-card-text>
      <v-row>
        <v-col cols="12">
          <validation-provider vid="email" :name="trans('email address')" rules="required|email" v-slot="{ errors }">
            <v-text-field
              :dense="isDense"
              :error-messages="errors"
              :label="trans('Email address')"
              class="dt-text-field"
              name="email"
              outlined
              prepend-inner-icon="mdi-email-outline"
              type="email"
              v-model="resource.data.email"
              >
            </v-text-field>
          </validation-provider>
        </v-col>
        <v-col cols="12">
          <validation-provider vid="username" :name="trans('username')" rules="required|min:3" v-slot="{ errors }">
            <v-text-field
              :dense="isDense"
              :error-messages="errors"
              :label="trans('Username')"
              autocomplete="off"
              class="dt-text-field"
              name="username"
              outlined
              prepend-inner-icon="mdi-account-outline"
              v-model="resource.data.username"
              >
            </v-text-field>
          </validation-provider>
        </v-col>
        <v-col v-if="hasPassword" cols="12" :md="confirmed ? 6 : 12">
          <validation-provider vid="password" :name="trans('password')" rules="required|min:6" v-slot="{ errors }">
            <v-text-field
              :dense="isDense"
              :error-messages="errors"
              :label="trans('Password')"
              autocomplete="new-password"
              class="dt-text-field"
              name="password"
              outlined
              prepend-inner-icon="mdi-lock"
              ref="password"
              type="password"
              v-model="resource.data.password"
              >
            </v-text-field>
          </validation-provider>
        </v-col>
        <v-col v-if="hasPassword && confirmed" cols="12" md="6">
          <validation-provider vid="password_confirmation" :name="trans('confirm password')" rules="required|confirmed:password|min:6" v-slot="{ errors }">
            <v-text-field
              :dense="isDense"
              :error-messages="errors"
              :label="trans('Retype Password')"
              autocomplete="new-password"
              class="dt-text-field"
              name="password_confirmation"
              outlined
              prepend-inner-icon="mdi-lock"
              type="password"
              v-model="resource.data.password_confirmation"
              >
            </v-text-field>
          </validation-provider>
        </v-col>
      </v-row>
    </v-card-text>
  </v-card>
</template>

<script>
import User from '../Models/User'

export default {
  name: 'AccountDetails',

  props: {
    value: {
      type: [Array, Object],
    },
    confirmed: {
      type: [Boolean],
    },
    password: {
      type: [Boolean],
    },
  },

  computed: {
    resource: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },

    hasPassword () {
      return this.password
    },

    isDense: function () {
      return this.$vuetify.breakpoint.xlAndUp
    },
  },
}
</script>
