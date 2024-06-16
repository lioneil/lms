<template>
  <div>
    <v-card>
      <v-card-text v-html="item.text"></v-card-text>
    </v-card>
  </div>
</template>

<script>
  export default {
    name: 'Card',
    data () {
      return {
        item: {
          text: 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Repellendus natus odit quo cumque aperiam est veritatis in, porro et minus, tempore non harum nulla dicta doloremque voluptate saepe. Quia, inventore.'
        }
      }
    }
  }
</script>
