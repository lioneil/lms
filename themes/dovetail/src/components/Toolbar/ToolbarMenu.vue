<template>
  <div class="sticky sheet">
    <v-toolbar
      flat
      height="auto"
      >
      <v-row align="center" justify="space-between">
        <v-col cols="12" sm="4">
          <slot name="search">
            <v-badge
              bordered
              bottom
              class="dt-badge d-block"
              color="dark"
              content="/"
              offset-x="28"
              offset-y="28"
              tile
              transition="fade-transition"
              v-model="ctrlIsPressed"
              >
              <v-text-field
                background-color="bar"
                :placeholder="trans('Search...')"
                :prepend-inner-icon="items.isSearching ? 'mdi-spin mdi-loading' : 'mdi-magnify'"
                @click:clear="search"
                @keyup="search"
                @shortkey.native="focus"
                autocomplete="off"
                class="dt-text-field__search"
                clear-icon="mdi-close-circle-outline"
                clearable
                filled
                flat
                full-width
                hide-details
                ref="tablesearch"
                single-line
                solo
                v-model="items.search"
                v-shortkey="['ctrl', '/']"
              >
              </v-text-field>
            </v-badge>
          </slot>
        </v-col>
        <v-col cols="12" sm="auto">
          <div class="d-flex justify-sm-space-between justify-end align-center">
            <v-slide-x-reverse-transition>
              <div v-if="items.bulkCount" class="px-2">{{ $tc('{number} item selected', items.bulkCount, {number: items.bulkCount}) }}</div>
            </v-slide-x-reverse-transition>
            <v-slide-x-reverse-transition>
              <v-divider v-if="items.bulkCount" vertical class="mx-2"></v-divider>
            </v-slide-x-reverse-transition>
            <v-spacer v-if="items.bulkCount"></v-spacer>

            <!-- Grid List view -->
            <template v-if="switchable">
              <template v-if="toolbar.toggleview">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on:grid }">
                    <v-btn
                      @click="toggleView"
                      class="mr-2"
                      icon
                      slot="activator"
                      v-on="grid"
                      >
                      <v-icon small>mdi-format-list-bulleted</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ __('Switch to List View') }}</span>
                </v-tooltip>
              </template>
              <template v-else>
                <v-tooltip bottom>
                  <template v-slot:activator="{ on:list }">
                    <v-btn
                      @click="toggleView"
                      class="mr-2"
                      icon
                      slot="activator"
                      v-on="list"
                      >
                      <v-icon small>mdi-view-grid-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ __('Switch to Grid View') }}</span>
                </v-tooltip>
              </template>
            </template>
            <!-- Grid List view -->

            <!-- Action buttons -->
            <v-scale-transition>
              <span v-if="items.toggleBulkEdit">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn class="mr-2" v-on="on" v-if="downloadable" icon :disabled="!items.toggleBulkEdit">
                      <v-icon small>mdi-download</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ trans('Export selected items') }}</span>
                </v-tooltip>
              </span>
            </v-scale-transition>
            <v-scale-transition>
              <span v-if="items.toggleBulkEdit">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn v-on="on" @click="askUserToBulkRestoreResources" class="mr-2" v-if="restorable" icon :disabled="!items.toggleBulkEdit">
                      <v-icon small>mdi-restore</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ trans('Restore selected items') }}</span>
                </v-tooltip>
              </span>
            </v-scale-transition>
            <v-scale-transition>
              <span v-if="items.toggleBulkEdit">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn class="mr-2" @click="askUserToBulkDestroyResources" v-if="trashable" icon v-on="on" :disabled="!items.toggleBulkEdit">
                      <v-icon small>mdi-delete-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ trans('Move selected items to trash') }}</span>
                </v-tooltip>
              </span>
            </v-scale-transition>
            <v-scale-transition>
              <span v-if="items.toggleBulkEdit">
                <v-tooltip bottom>
                  <template v-slot:activator="{ on }">
                    <v-btn class="mr-2" @click="askUserToBulkPermanentlyDeleteResources" v-if="deletable" icon v-on="on" :disabled="!items.toggleBulkEdit">
                      <v-icon small>mdi-delete-forever-outline</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ trans_choice('Permanently delete the selected item') }}</span>
                </v-tooltip>
              </span>
            </v-scale-transition>
            <!-- Action buttons -->

            <v-badge
              bordered
              bottom
              class="dt-badge d-block"
              color="dark"
              content="shift+a"
              offset-x="30"
              offset-y="20"
              tile
              transition="fade-transition"
              v-model="ctrlIsPressed"
              >
              <v-tooltip bottom>
                <template v-slot:activator="{ on }">
                  <v-btn-toggle v-if="bulk" v-model="items.toggleBulkEdit" dense rounded color="primary">
                    <v-btn
                      @shortkey="items.toggleBulkEdit = !items.toggleBulkEdit"
                      icon
                      v-on="on"
                      v-shortkey="['ctrl', 'shift', 'a']"
                      color="primary"
                      :value="true"
                      class="bar"
                      >
                      <v-icon v-if="items.toggleBulkEdit" color="primary" small>mdi-close</v-icon>
                      <v-icon v-else small>mdi-check-box-multiple-outline</v-icon>
                    </v-btn>
                  </v-btn-toggle>
                </template>
                <span>{{ trans('Toggle multiple selection') }}</span>
              </v-tooltip>
            </v-badge>

            <v-divider class="mx-2" vertical v-if="verticaldivider"></v-divider>

            <!-- filter -->
            <slot name="filter"></slot>
            <!-- filter -->
          </div>
        </v-col>
      </v-row>
    </v-toolbar>
    <v-divider v-if="bottomdivider"></v-divider>
  </div>
</template>
<script>
import { mapGetters, mapActions } from 'vuex'
import ManIcon from '@/components/Icons/ManThrowingAwayPaperIcon.vue'
import EmptyIcon from '@/components/Icons/EmptyIcon.vue'
import ProjectManager from '@/components/Icons/ProjectManager.vue'

export default {
  name: 'ToolbarMenu',
  props: {
    items: {
      type: [Object, Array],
      default: () => {
        return {}
      }
    },
    bulk: {
      type: [Boolean],
    },
    downloadable: {
      type: [Boolean],
    },
    trashable: {
      type: [Boolean],
    },
    switchable: {
      type: [Boolean],
    },
    restorable: {
      type: [Boolean],
    },
    deletable: {
      type: [Boolean],
    },
    verticaldivider: {
      type: [Boolean],
    },
    bottomdivider: {
      type: [Boolean],
      default: true
    },
  },

  data: () => ({
    dataset: {},
    trashButtonIsLoading: false,
    deleteButtonIsLoading: false,
    isSearching: false,
  }),

  methods: {
    ...mapActions({
      update: 'toolbar/update',
    }),
    search (val) {
      this.items.isSearching = true
      this.$emit('update:search', val)
    },
    focus () {
      this.$refs['tablesearch'].focus()
    },
    toggleView () {
      this.update({ toggleview: !this.toolbar.toggleview })
    },
    askUserToBulkRestoreResources () {
      if (this.items.bulkCount) {
        this.$store.dispatch('dialog/prompt', {
          show: true,
          width: 420,
          illustration: this.items.bulkCount ? ProjectManager : EmptyIcon,
          illustrationWidth: 240,
          illustrationHeight: 240,
          loading: this.restoreButtonIsLoading,
          color: 'primary',
          title: trans_choice('Restore Selected Item', this.items.bulkCount),
          text: trans_choice('Are you sure you want to restore the selected item?', this.items.bulkCount),
          buttons: {
            cancel: { show: this.items.bulkCount, color: 'link' },
            action: {
              color: this.items.bulkCount ? 'primary' : null,
              text: this.items.bulkCount ? 'Restore' : 'Okay',
              callback: () => {
                this.$store.dispatch('dialog/loading', true)
                if (!this.items.bulkCount) {
                  this.$store.dispatch('dialog/loading', false)
                  this.$store.dispatch('dialog/close')
                } else {
                  this.emitRestoreButtonClicked()
                }
              }
            }
          },
        })
      } else {
        this.$store.dispatch('snackbar/show', {
          text: trans('Select an item from the list first'),
          button: {
            text: trans('Okay'),
          },
        })
      }
    },
    askUserToBulkDestroyResources () {
      if (this.items.bulkCount) {
        this.$store.dispatch('dialog/prompt', {
          show: true,
          width: 420,
          illustration: this.items.bulkCount ? ManIcon : EmptyIcon,
          illustrationWidth: 240,
          illustrationHeight: 240,
          loading: this.trashButtonIsLoading,
          color: 'warning',
          title: trans_choice('Move the selected item to trash', this.items.bulkCount),
          text: trans_choice('Are you sure you want to move the selected item to trash?', this.items.bulkCount),
          buttons: {
            cancel: { show: this.items.bulkCount, color: 'link' },
            action: {
              color: this.items.bulkCount ? 'warning' : null,
              text: this.items.bulkCount ? 'Move to Trash' : 'Okay',
              callback: () => {
                this.$store.dispatch('dialog/loading', true)
                if (!this.items.bulkCount) {
                  this.$store.dispatch('dialog/loading', false)
                  this.$store.dispatch('dialog/close')
                } else {
                  this.emitTrashButtonClicked()
                }
              }
            }
          },
        })
      } else {
        this.$store.dispatch('snackbar/show', {
          text: trans_choice('Select an item from the list first', this.items.bulkCount),
          button: {
            text: trans('Okay'),
          },
        })
      }
    },
    askUserToBulkPermanentlyDeleteResources () {
      if (this.items.bulkCount) {
        this.$store.dispatch('dialog/prompt', {
          show: true,
          width: 420,
          illustration: this.items.bulkCount ? ManIcon : EmptyIcon,
          illustrationWidth: 240,
          illustrationHeight: 240,
          loading: this.deleteButtonIsLoading,
          color: 'error',
          title: trans_choice('Permanently delete the selected item', this.items.bulkCount),
          text: trans_choice('Are you sure you want to permanently delete the selected item?', this.items.bulkCount),
          buttons: {
            cancel: { show: this.items.bulkCount, color: 'link' },
            action: {
              color: this.items.bulkCount ? 'error' : null,
              text: this.items.bulkCount ? 'Permanently delete' : 'Okay',
              callback: () => {
                this.$store.dispatch('dialog/loading', true)
                if (!this.items.bulkCount) {
                  this.$store.dispatch('dialog/loading', false)
                  this.$store.dispatch('dialog/close')
                } else {
                  this.emitDeleteButtonClicked()
                }
              }
            }
          },
        })
      } else {
        this.$store.dispatch('snackbar/show', {
          text: trans_choice('Select an item from the list first', this.items.bulkCount),
          button: {
            text: trans('Okay'),
          },
        })
      }
    },
    emitRestoreButtonClicked () {
      this.$emit('update:restore')
    },
    emitTrashButtonClicked () {
      this.$emit('update:trash')
    },
    emitDeleteButtonClicked () {
      this.$emit('update:delete')
    },
    toggleLoadingStateOnClick () {
      this.trashButtonIsLoading = !this.trashButtonIsLoading;
    },
  },
  mounted () {
    this.dataset = Object.assign({}, this.toolbar, this.items)
  },
  computed: {
    ...mapGetters({
      ctrlIsPressed: 'shortkey/ctrlIsPressed',
      toolbar: 'toolbar/toolbar',
      app: 'app/app'
    }),
  },
  watch: {
    'items.toggleBulkEdit': function (val) {
      if (!val) {
        this.trashButtonIsLoading = false
      }
    },
  }
}
</script>
