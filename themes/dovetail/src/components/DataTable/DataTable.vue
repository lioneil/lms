<template>
  <v-fade-transition mode="in-out">
    <v-card>
      <v-data-table
        :headers="dataset.headers"
        :items="dataset.items"
        sort-by="name"
        >
        <template v-slot:item.action="{ item }">
          <v-tooltip bottom v-if="dataset.edit">
            <template v-slot:activator="{ on }">
              <v-btn small icon v-on="on">
                <v-icon small>mdi-pencil-outline</v-icon>
              </v-btn>
            </template>
            <span>{{ __('Edit this user') }}</span>
          </v-tooltip>

          <v-btn small icon v-if="dataset.trash">
            <v-tooltip bottom>
              <template v-slot:activator="{ on }">
                <v-btn small icon v-on="on">
                  <v-icon small>mdi-delete-outline</v-icon>
                </v-btn>
              </template>
              <span>{{ __('Move this user to trash') }}</span>
            </v-tooltip>
          </v-btn>
        </template>
        <slot></slot>
      </v-data-table>
    </v-card>
  </v-fade-transition>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'DataTable',

  props: {
    width: {
      type: [Number, String],
      default: 120
    },
    height: {
      type: [Number, String],
      default: 120
    },
    items: {
      type: [Object, Array],
      default: () => {
        return {
        }
      }
    }
  },

  data () {
    return {
      dataset: {},
    }
  },

  mounted () {
    this.dataset = Object.assign({}, this.datatable, this.items)
  },

  computed: {
    ...mapGetters({
      datatable: 'datatable/datatable',
    })
  },
  methods: {}
}
</script>
