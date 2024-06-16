<template>
  <validation-observer ref="signin-form" v-slot="{ passes }">
    <v-form :disabled="loading" v-on:submit.prevent="submit">
      <validation-provider mode="lazy" name="username" rules="required" v-slot="{ errors }">
        <v-text-field
          :error-messages="errors"
          :label="$t('Username or email')"
          autofocus
          class="mb-3"
          outlined
          v-model="auth.username"
        ></v-text-field>
      </validation-provider>

      <validation-provider name="password" rules="required" v-slot="{ errors }">
        <v-text-field
          :error-messages="errors"
          :label="$t('Password')"
          :type="showPassword ? 'text' : 'password'"
          class="mb-3"
          outlined
          password
          v-model="auth.password"
          >
          <!-- <template v-slot:append>
            <v-btn tabindex="-1" icon @click="showPassword = !showPassword">
              <v-icon v-if="showPassword"> mdi-eye </v-icon>
              <v-icon v-else> mdi-eye-off </v-icon>
            </v-btn>
          </template> -->
        </v-text-field>
      </validation-provider>

      <v-btn
        type="submit"
        :disabled="loading"
        :loading="loading"
        color="primary"
        x-large block
        >
        {{ $t('Sign in') }}
        <template v-slot:loader>
          <v-slide-x-transition><v-icon dark class="mdi-spin mr-3">mdi-loading</v-icon></v-slide-x-transition>
          <span>{{ $t('Signing in...') }}</span>
        </template>
      </v-btn>
    </v-form>
  </validation-observer>
</template>

<script>
import $api from '@/routes/api'
import $auth from '@/core/Auth/auth'

export default {
  name: 'Login',

  data: () => ({
    auth: {
      username: '',
      password: '',
    },
    loading: false,
    showPassword: false,
  }),

  computed: {
    isMobile: function () {
      return this.$vuetify.breakpoint.smAndDown;
    }
  },

  methods: {
    load (val = true) {
      this.loading = val
    },

    submit (e) {
      const { username, password } = this.auth

      this.load()
      this.$store
        .dispatch('auth/login', { username, password })
        .then(() => {
          this.$router.push({name: 'courses.all'})
          this.$store.dispatch('snackbar/show', {
            text: $t('Welcome back, ') + $auth.getUser().firstname
          })
        })
        .catch(err => {
          if (err.response) {
            this.$refs['signin-form'].setErrors(err.response.data.errors)
          }
        })
        .finally(() => {
          this.load(false)
        })

      e.preventDefault()
    }
  },
}
</script>
