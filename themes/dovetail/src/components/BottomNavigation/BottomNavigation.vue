<template>
  <v-bottom-navigation fixed>
    <v-row justify="center" align="center">
      <v-col cols="12" md="6">
        <div class="d-flex align-center mx-4">
          <span class="mr-3 link--text" v-html="`${value}%`">0 %</span>
          <v-progress-linear
            color="teal"
            :value="`${value}%`"
            rounded
            height="6"
          ></v-progress-linear>
        </div>
      </v-col>
    </v-row>
  </v-bottom-navigation>
</template>

<script>
export default {
  props: ['value']
}
</script>
