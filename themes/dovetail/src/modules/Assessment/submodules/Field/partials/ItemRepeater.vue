<template>
  <section>
    <small v-text="trans('Check the correct answer/s.')"></small>
    <v-row align="start" :key="i" v-for="(item, i) in repeaters">
      <v-col>
        <validation-provider :vid="`text${i}`" rules="required" :name="trans('text')" v-slot="{ errors }">
          <v-text-field
            :error-messages="errors"
            :label="trans('Answer')"
            :name="`metadata[items][${i}][text]`"
            :title="item.text"
            @input="update"
            autocomplete="off"
            class="dt-text-field dt-repeater--key"
            outlined
            :prepend-inner-icon="!readonly ? 'mdi-square-edit-outline' : ''"
            v-model="item.text"
            v-shortkey.avoid
            :readonly="readonly"
            >
          </v-text-field>
        </validation-provider>
      </v-col>
      <v-col cols="auto">
        <v-checkbox
          :name="`metadata[items][${i}][answer]`"
          @input="update"
          hide-details
          v-model="item.answer"
          value="1"
        ></v-checkbox>
      </v-col>
      <v-col cols="auto" class="mt-3">
        <context-prompt v-if="!noRemove">
          <v-card max-width="280">
            <v-card-title v-text="trans('Remove Item')"></v-card-title>
            <v-card-text v-text="trans('Doing so will permanently remove this item from the list. Are you sure you want to proceed?')"></v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn large color="error" text @click.prevent="remove(i)" v-text="trans('Remove')"></v-btn>
            </v-card-actions>
          </v-card>
        </context-prompt>
      </v-col>
    </v-row>

    <v-badge
      bottom
      class="dt-badge"
      color="dark"
      offset-x="20"
      offset-y="20"
      tile
      transition="fade-transition"
      v-model="$store.getters['shortkey/ctrlIsPressed']"
      >
      <template v-slot:badge>
        <div class="small" style="font-size: 11px">d</div>
      </template>
      <v-btn
        @click="add()"
        @shortkey="add()"
        class="mt-3"
        large
        v-if="!removeAddItem"
        v-shortkey="['ctrl', 'd']"
        v-text="trans('Add Item')"
        >
      </v-btn>
    </v-badge>
  </section>
</template>

<script>
export default {
  props: ['value', 'removeAddItem', 'readonly', 'noRemove'],

  data: () => ({
    repeaters: []
  }),

  methods: {
    add: function (i) {
      this.repeaters.push({ text: '' })
    },

    remove: function (i) {
      if(this.repeaters.length > 2)
        this.repeaters.splice(i, 1)
    },

    update () {
      this.$emit('input', this.repeaters)
    },

    initRepeaters () {
      this.repeaters = this.value || [ { text: '' }, { text: '' } ]
    }
  },

  mounted () {
    this.initRepeaters()
    this.$emit('input', this.repeaters)
  },

  watch: {
    'value': {
      handler (val) {
        this.initRepeaters()
      },
      deep: true
    }
  },
}
</script>
