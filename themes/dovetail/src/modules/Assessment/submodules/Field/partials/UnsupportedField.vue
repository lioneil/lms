<template>
  <v-row justify="center" align="center">
    <v-card flat color="transparent" class="text-center muted--text">
      <div class="icon--disabled">
        <sad-icon height="200" width="200" class="primary--text"></sad-icon>
      </div>
      <v-card-text>
        <p class="muted--text" v-text="trans('This field is currently unsupported.')"></p>
        <v-btn
          large
          color="primary"
          exact
          :to="{name: 'fields.index'}">
          <v-icon small left>mdi-file-document-outline</v-icon>
          <span v-text="trans('Go to Fields')"></span>
        </v-btn>
      </v-card-text>
    </v-card>
  </v-row>
</template>
