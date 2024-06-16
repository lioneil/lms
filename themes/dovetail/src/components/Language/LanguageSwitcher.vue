<template>
  <section>
    <h4 class="mb-3">{{ __('Select Language') }}</h4>
    <v-select prepend-icon="mdi mdi-translate" filled hide-details outlined v-model="lang" :items="items" @change="change"></v-select>
  </section>
</template>

<script>
export default {
  name: 'LanguageSwitcher',

  data: () => ({
    items: Object.entries($app.language.supported).map(([value, text]) => {
      return { value, text }
    }),
  }),

  computed: {
    lang: {
      get () {
        return this.$i18n.locale == null ? $app.fallback_locale : this.$i18n.locale
      },
      set (val) {
        this.$i18n.locale = val == $app.fallback_locale ? null : val
      },
    },

    locale: function () {
      return this.lang == $app.fallback_locale ? null : this.lang
    }
  },

  methods: {
    change: function () {
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
