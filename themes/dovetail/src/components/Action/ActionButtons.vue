<template>
  <div class="text-no-wrap">
    <template v-if="trashed">
      <div class="text-no-wrap">
        <!-- Restore -->
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn @click="emitRestore" icon v-on="on">
              <v-icon class="mdi-spin" small v-if="dataset.loading">mdi-loading</v-icon>
              <v-icon small v-else>mdi-restore</v-icon>
            </v-btn>
          </template>
          <span v-text="trans('Restore', 1)"></span>
        </v-tooltip>
        <!-- Restore -->

        <!-- Permanently Delete -->
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn @click="emitDelete" icon v-on="on">
              <v-icon small>mdi-delete-forever-outline</v-icon>
            </v-btn>
          </template>
          <span v-text="trans('Permanently delete', 1)"></span>
        </v-tooltip>
        <!-- Permanently Delete -->
      </div>
    </template>

    <template v-else-if="deleted">
      <div class="text-no-wrap">
        <!-- Permanently Delete -->
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn @click="emitPermanentlyDelete" icon v-on="on">
              <v-icon small>mdi-delete-forever-outline</v-icon>
            </v-btn>
          </template>
          <span>{{ trans_choice('Permanently delete', 1) }}</span>
        </v-tooltip>
        <!-- Permanently Delete -->
      </div>
    </template>

    <template v-else>
      <slot name="start"></slot>

      <!-- Preview -->
      <template v-if="details">
        <can :code="`${name}.show`">
          <v-tooltip bottom>
            <template v-slot:activator="{ on }">
              <v-btn
                :to="{name: `${name}.show`, params: {id: dataset.id}, query: {from: $route.fullPath}}"
                icon
                v-on="on"
                >
                <v-icon small>mdi-open-in-new</v-icon>
              </v-btn>
            </template>
            <span v-text="trans('Show Details')"></span>
          </v-tooltip>
        </can>
      </template>
      <!-- Preview -->

      <slot name="middle"></slot>

      <!-- Move to Trash -->
      <can :code="`${name}.destroy`">
        <v-tooltip bottom>
          <template v-slot:activator="{ on }">
            <v-btn @click="emitDestroy" icon v-on="on">
              <v-icon small>mdi-delete-outline</v-icon>
            </v-btn>
          </template>
          <span v-text="trans('Move to trash')"></span>
        </v-tooltip>
      </can>
      <!-- Move to Trash -->

      <slot name="end"></slot>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    name: {
      type: String,
    },
    value: {
      type: Object,
    },
    trashed: {
      type: Boolean,
      default: false,
    },
    deleted: {
      type: Boolean,
      default: false,
    },
    details: {
      type: Boolean,
      default: true,
    }
  },

  computed: {
    dataset: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },
  },

  methods: {
    emitDestroy () {
      this.$emit('item:destroy', this.value)
    },
    emitRestore () {
      this.$emit('item:restore', this.value)
    },
    emitDelete () {
      this.$emit('item:delete', this.value)
    },
    emitPermanentlyDelete () {
      this.$emit('item:permanentdelete', this.value)
    }
  }
}
</script>
