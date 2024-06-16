<template>
  <v-dialog
    v-model="exportfile.show"
    persistent
    max-width="460"
    >
    <v-card>
      <div class="text-center pa-5">
        <download-icon :width="exportfile.iconwidth" :height="exportfile.iconheight"></download-icon>
      </div>
      <v-card-title class="pb-0">{{ trans(exportfile.title) }}</v-card-title>
      <v-card-text>{{ trans(exportfile.text) }}</v-card-text>
      <v-card-text>
        <v-text-field
          dense
          label="File Name"
          outlined
          value="filename-export-20-01-15-113820"
        ></v-text-field>
        <v-select
          item-text="name"
          :items="exportfile.items"
          append-icon="mdi-chevron-down"
          background-color="selects"
          dense
          label="Select file type"
          outlined
          >
          <template v-slot:prepend-item="{ item }">
            <div class="link--text body-2 py-3 px-5">Presentable</div>
          </template>
          <template v-slot:item ="{ item }">
            <v-list-item-icon>
              <v-icon :color="item.color" small v-text="item.icon"></v-icon>
            </v-list-item-icon>
            <v-list-item-content>
              <v-list-item-title v-html="item.name"></v-list-item-title>
            </v-list-item-content>
          </template>
          <template v-slot:prepend-item>
            <div class="link--text body-2 py-3 px-5">Presentable</div>
          </template>
        </v-select>
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>

        <v-btn
          v-if="exportfile.buttons.cancel.show"
          :color="exportfile.buttons.cancel.color"
          @click.native="exportfile.buttons.cancel.callback()"
          text
          >
          {{ trans(exportfile.buttons.cancel.text) }}
        </v-btn>

        <v-btn
          :color="exportfile.buttons.action.color"
          :disabled="exportfile.loading"
          :loading="exportfile.loading"
          @click.native="exportfile.buttons.action.callback(exportfile)"
          text
          v-if="exportfile.buttons.action.show"
          >
          {{ trans(exportfile.buttons.action.text) }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  name: 'Export',

  computed: {
    ...mapGetters({
      exportfile: 'export/exportfile'
    }),
  },
}
</script>
