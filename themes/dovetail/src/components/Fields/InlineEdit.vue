<template>
  <v-row no-gutters>
    <v-col cols="auto">
      <template v-if="isNotEditing">
        <slot></slot>
      </template>

      <template v-if="editing">
        <div class="d-flex align-center">
          <v-form v-model="valid" @submit.prevent="save">
            <validation-provider
              :name="trans('name')"
              :rules="{ required: true, max: 80 }"
              :vid="name"
              v-slot="{ errors }"
              >
              <v-text-field
                :error-messages="errors"
                :name="name"
                autofocus
                class="dt-text-field"
                dense
                :hide-details="valid"
                outlined
                v-model="dataset"
                @keyup.enter="save"
                >
              </v-text-field>
            </validation-provider>
          </v-form>

          <a @click="save" class="t-d-none success--text t-d-hover-lined d-inline-block show-btns mx-2">
            {{ trans('Update') }}
          </a>
          <a @click="cancel" class="t-d-none text--text t-d-hover-lined d-inline-block show-btns mx-2">
            {{ trans('Cancel') }}
          </a>
        </div>
      </template>
    </v-col>

    <!-- Actions -->
    <v-col>
      <div v-if="isNotEditing" class="d-flex align-center">
        <div class="d-inline-block">
          <slot name="edit" v-bind="{ edit }">
            <a
              @click="edit"
              class="t-d-none t-d-hover-lined d-inline-block show-btns mx-1"
              >
              {{ trans('Change name') }}
            </a>
          </slot>
          <slot name="actions"></slot>
        </div>
      </div>
    </v-col>
    <!-- Actions -->
  </v-row>
</template>

<script>
import isEmpty from 'lodash/isEmpty'

export default {
  props: ['name', 'value', 'id'],

  computed: {
    isNotEditing () {
      return !this.editing
    },

    dataset: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    }
  },

  data: (vm) => ({
    editing: false,
    oldDataset: null,
    valid: true,
  }),

  methods: {
    edit () {
      this.editing = true
    },

    hideEdit () {
      this.editing = false
    },

    save () {
      if (!isEmpty(this.dataset) && this.valid) {
        this.$emit('save', { text: this.dataset, id: this.id} )
        this.hideEdit()
      }
    },

    cancel () {
      this.$emit('cancel', this.dataset = this.oldDataset)
      this.hideEdit()
    },
  },

  mounted () {
    this.oldDataset = this.dataset
  }
}
</script>
