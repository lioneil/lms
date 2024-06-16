<template>
  <div>
    <validation-provider :vid="inputName" :name="inputName" v-slot="{ errors }">
      <v-combobox
        :items="categories"
        :multiple="multiple"
        :search-input.sync="search"
        @focus="getCategoriesData"
        append-icon="mdi-chevron-down"
        class="dt-text-field"
        hide-selected
        hide-details
        item-text="text"
        item-value="value"
        label="Select or create a category"
        outlined
        background-color="selects"
        persistent-hint
        v-model="category"
        >
        <template v-slot:selection="data">
          <v-chip
            class="primary primary--text text--lighten-5"
            :disabled="data.disabled"
            small
            :input-value="data.selected"
            :key="JSON.stringify(data.item)"
            v-bind="data.attrs"
            @click:close="data.parent.selectItem(data.item)"
          >
            {{ filterCategory(data.item) }}
          </v-chip>
        </template>
        <template v-slot:no-data>
          <v-list-item>
            <v-list-item-content>
              <v-list-item-title>
                No results matching "<strong>{{ search }}</strong>".
                Press <kbd>enter</kbd> to create a new category.
              </v-list-item-title>
            </v-list-item-content>
          </v-list-item>
        </template>
      </v-combobox>
    </validation-provider>
    <input type="hidden" name="category_id" :value="returnedCategory">
  </div>
</template>

<script>
import isEmpty from 'lodash/isEmpty'

export default {
  name: 'CategoryPicker',

  props: {
    value: {
      type: [Array, Object, String, Number],
    },
    url: {
      type: String,
    },
    name: {
      type: String,
      default: 'category',
    },
    multiple: {
      type: [Boolean, Number],
      default: false,
    },
    errors: {
      type: [Array, Object],
    },
    lazyLoad: {
      type: Boolean,
    },
  },

  computed: {
    category: {
      get () {
        return this.value
      },
      set (value) {
        this.$emit('input', value)
      },
    },

    inputName () {
      return this.category && this.category.hasOwnProperty('value') ? `${this.name}_id` : this.name;
    },

    returnedCategory () {
      return this.category && this.category.hasOwnProperty('value') ? this.category.value : this.category
    },

    categories () {
      return this.items.map(function (category) {
        return {
          value: category.id,
          text: category.name,
        }
      })
    }
  },

  data: () => ({
    items: [],
    search: null,
  }),

  methods: {
    filterCategory (category) {
      let c = category && category.hasOwnProperty('text') ? category : this.categories.filter(c => c.value == category).shift()

      if (c && c.hasOwnProperty('text')) {
        return c.text
      }

      return category
    },

    getCategoriesData () {
      if (isEmpty(this.items)) {
        axios.get(this.url, { params: { per_page: '-1' } })
          .then(response => {
            this.items = response.data.data
          })
      }
    },
  },

  mounted () {
    if (!this.lazyLoad) {
      this.getCategoriesData()
    }
  }
}
</script>
