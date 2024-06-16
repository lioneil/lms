<template>
  <v-fade-transition mode="out-in">
    <v-data-iterator
      :items-per-page.sync="dataset.itemsPerPage"
      :items="dataset.items"
      >
      <template v-slot:default="props">
        <v-row>
          <v-col
            :key="dataset.name"
            cols="12"
            lg="3"
            md="4"
            sm="6"
            v-for="item in props.items"
            >
            <v-card
              height="100%"
              :hover="dataset.hover"
              :href="dataset.hover ? dataset.url : ''"
              >
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-img
                    v-on="on"
                    :height="dataset.thumbnailHeight"
                    :src="item.thumbnail"
                    >
                  </v-img>
                </template>
                <span v-text="trans(item.title)"></span>
              </v-tooltip>

              <v-card-text>
                <p class="body-2 secondary--text mb-1" v-text="trans(item.category)"></p>
                <p class="font-weight-bold" v-text="trans(item.title)"></p>
                <p class="text--ellipsis" v-text="trans(item.description)"></p>
              </v-card-text>

              <slot name="actions"></slot>
            </v-card>
          </v-col>
        </v-row>
      </template>
    </v-data-iterator>
  </v-fade-transition>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'DataIterator',

  props: {
    items: {
      type: [Object, Array],
      default: () => {
        return {}
      }
    }
  },

  data () {
    return {
      dataset: {}
    }
  },

  mounted () {
    this.dataset = Object.assign({}, this.dataiterator, this.items)
  },

  computed: {
    ...mapGetters({
      dataiterator: 'dataiterator/dataiterator'
    })
  },
  methods: {
    show () {
      this.$store.dispatch('dataiterator/PROMPT_DIALOG', { model: true })
    },

    hide () {
      this.$store.dispatch('dataiterator/PROMPT_DIALOG', { model: false })
    }
  }
}
</script>
