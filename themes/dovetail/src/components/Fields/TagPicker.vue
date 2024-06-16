<template>
  <div>
    <validation-provider :vid="inputName" :name="inputName" v-slot="{ errors }">
      <v-combobox
        :items="tags"
        :search-input.sync="query"
        @change="onChangeHandler"
        @focus="getTagsData"
        @update:search-input="search"
        append-icon="mdi-chevron-down"
        background-color="selects"
        class="dt-text-field"
        clear-icon="mdi-close-circle-outline"
        clearable
        hide-selected
        label="Select or create a tag"
        multiple
        outlined
        persistent-hint
        v-model="tag"
        >
        <template v-slot:selection="data">
          <v-chip
            :disabled="data.disabled"
            :input-value="data.selected"
            :key="JSON.stringify(data.item)"
            @click:close="data.parent.selectItem(data.item)"
            close
            color="sheet"
            label
            v-bind="data.attrs"
          >
            {{ data.item }}
          </v-chip>
        </template>
        <template v-slot:no-data>
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>
                No results matching "<strong>{{ query }}</strong>".
                Press <kbd>enter</kbd> to create a new tag.
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </template>
      </v-combobox>
    </validation-provider>
    <input type="hidden" name="tags[]" v-for="(tag, i) in returnedTag" :value="tag" :key="i">
  </div>
</template>

<script>
import isEmpty from 'lodash/isEmpty'
import debounce from 'lodash/debounce'

export default {
  name: 'TagPicker',

  props: {
    value: {
      type: [Array, Object, String, Number],
    },
    url: {
      type: String,
    },
    name: {
      type: String,
      default: 'tag',
    },
    errors: {
      type: [Array, Object],
    },
    lazyLoad: {
      type: Boolean,
    },
  },

  computed: {
    tag: {
      get () {
        return this.value
      },
      set (value) {
        this.$emit('input', value)
      },
    },

    inputName () {
      return this.tag && this.tag.hasOwnProperty('value') ? `${this.name}_id` : this.name;
    },

    returnedTag () {
      return this.tag && this.tag.hasOwnProperty('value') ? this.tag.value : this.tag
    },

    tags () {
      return this.items.map(function (item) {
        return item.name
      })
    }
  },

  data: () => ({
    items: [],
    query: null,
  }),

  methods: {
    filterTag (tag) {
      let c = tag && tag.hasOwnProperty('text') ? tag : this.tags.filter(c => c.value == tag).shift()

      if (c && c.hasOwnProperty('text')) {
        return c.text
      }

      return tag
    },

    getTagsData () {
      axios.get(this.url, { params: { per_page: '5', search: this.query || null } })
        .then(response => {
          this.items = response.data.data
        })
    },

    search: debounce(function (e) {
      this.getTagsData()
    }, 200),

    onChangeHandler () {
      this.query = null
    }
  },

  mounted () {
    if (!this.lazyLoad) {
      this.getTagsData()
    }
  }
}
</script>
