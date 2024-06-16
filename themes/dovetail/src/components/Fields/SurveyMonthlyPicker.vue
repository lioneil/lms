<template>
  <div>
    <div class="body-2 mb-2">{{ __('Site visit date') }}:</div>
    <v-menu
      :close-on-content-click="false"
      ref="survey"
      name="survey"
      v-model="resource.menu"
      transition="scale-transition"
      offset-y
      min-width="290px"
      >
      <template v-slot:activator="{ on }">
        <v-text-field
          background-color="selects"
          class="dt-text-field"
          hint="YYYY/MM/DD format"
          dense
          outlined
          append-icon="mdi-calendar"
          readonly
          v-model="resource.date"
          v-on="on"
        ></v-text-field>
        <input type="hidden" name="remarks" :value="resource.date">
      </template>
      <v-date-picker
        v-model="resource.date"
        show-current
        no-title
        scrollable
        @input="resource.menu = false"
        >
      </v-date-picker>
    </v-menu>
  </div>
</template>

<script>
export default {
  data: (vm) => ({
    resource: {
      date: new Date().toISOString().substr(0, 10),
      survey: false,
    },
  }),
}
</script>
