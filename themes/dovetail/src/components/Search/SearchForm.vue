<template>
  <v-form :loading="loading">
    <v-menu v-model="open" bottom offset-y>
      <template v-slot:activator="{ on }">
        <v-text-field
          :placeholder="trans(`ctrl+/`)"
          :shaped="open"
          autocomplete="off"
          clear-icon="mdi-close-circle-outline"
          clearable
          dense
          filled
          hide-details
          prepend-inner-icon="mdi mdi-magnify"
          ref="searchform"
          rounded
          single-line
          title="ctrl+/ to focus"
          v-model="query"
          v-on="on"
          v-shortkey="['ctrl', '/']"
          @blur="focused = false"
          @focus="focused = true"
          @keydown.native="search"
          @shortkey.native="focus"
        ></v-text-field>
      </template>
      <v-list dense>
        <template v-for="(item, index) in results">
          <v-subheader
            :key="index"
            v-if="item.length"
            v-text="index"
          ></v-subheader>
          <v-list-item
            v-for="(item, i) in item"
            :key="`${index}-${i}`"
            :to="{name: 'tests.index'}"
          >
            {{ item.text }}
          </v-list-item>
          <v-list-item
            @click=""
          >
            View All
          </v-list-item>
        </template>
      </v-list>
    </v-menu>
  </v-form>
</template>

<script>
import debounce from 'lodash/debounce'
import isEmpty from 'lodash/isEmpty'
import $api from '@/routes/api'

export default {
  name: 'SearchForm',

  data: () => ({
    focused: false,
    loading: false,
    open: false,
    query: '',
    results: [],
  }),

  methods: {
    focus (e) {
      this.$refs.searchform.focus()
    },

    search: debounce(function () {
      if (!isEmpty(this.query)) {
        axios.post($api.search, {search: this.query})
          .then((response) => {
            this.open = true
            this.results = response.data
          })
      }
    }, 1200)
  }
}
</script>
