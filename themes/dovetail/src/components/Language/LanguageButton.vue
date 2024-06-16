<template>
  <div>
    <v-btn
      v-if="localeIsEn"
      block
      color="accent"
      large
      @click="changeToAr"
      >
      <template>
        {{ trans('Switch to Arabic') }}
      </template>
    </v-btn>
    <v-btn
      v-else
      block
      color="accent"
      large
      @click="changeToEn"
      >
      <template>
        {{ trans('Switch to English') }}
      </template>
    </v-btn>
  </div>
</template>

<script>
export default {
  computed: {
    localeIsEn () {
      return this.locale == null || this.locale == 'null' || this.locale == 'en'
    },

    lang: {
      get () {
        return this.$i18n.locale == null ? $app.fallback_locale : this.$i18n.locale
      },
      set (val) {
        this.$i18n.locale = val == $app.fallback_locale ? null : val
      },
    },

    locale: function () {
      return this.lang == $app.fallback_locale && this.$i18n.locale ? null : this.lang
    }
  },

  data: () => ({
    items: Object.entries($app.language.supported).map(([value, text]) => {
      return { value, text }
    }),
  }),

  methods: {
    change: function () {
      this.$store.dispatch('app/locale', this.locale)
      this.$vuetify.rtl = is_rtl(this.locale)
      this.reload()
    },

    changeToAr: function () {
      this.lang = 'ar'
      this.$store.dispatch('app/locale', this.locale)
      this.$vuetify.rtl = is_rtl(this.locale)
      this.reload()
    },

    changeToEn: function () {
      this.lang = 'en'
      this.$store.dispatch('app/locale', this.locale)
      this.$vuetify.rtl = is_rtl(this.locale)
      this.reload()
    },

    reload: function () {
      if (this.$router.currentRoute.params.lang !== this.locale) {
        this.$router.push({ name: this.$router.currentRoute.name, params: { lang: this.locale } })
      }
    },
  },
}
</script>
