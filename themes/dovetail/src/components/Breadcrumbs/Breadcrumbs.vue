<template>
  <div>
    <v-breadcrumbs
      v-show="breadcrumbs.show"
      class="pl-0"
      divider="/"
      :items="crumbs"
      >
      <template v-slot:item="{ item }">
        <v-breadcrumbs-item
          exact
          :to="item.to"
          >
          <small v-text="item.text"></small>
        </v-breadcrumbs-item>
      </template>
    </v-breadcrumbs>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import metatags from '@/routes/helpers/metatags'

export default {
  name: 'Breadcrumbs',

  computed: {
    ...mapGetters({
      breadcrumbs: 'breadcrumbs/breadcrumbs',
      lang: 'app/locale',
      title: 'metatag/title',
    }),

    crumbs: function () {
      return this.$route.matched.map((route, i) => {
        return {
          name: route.name,
          text: trans(metatags.gettext(route, this.title)),
          disabled: false,
          to: {name: route.name, query: this.$route.query},
          href: route.path,
        }
      })
    }
  },
}
</script>
