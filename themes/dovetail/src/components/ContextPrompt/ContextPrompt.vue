<template>
  <div class="dt-context-prompt">
    <v-menu bottom left>
      <template v-slot:activator="{ on }">
        <slot name="activator" :on="on">
          <v-btn
            icon
            v-on="on"
            >
            <v-icon>mdi-close-circle-outline</v-icon>
          </v-btn>
        </slot>
      </template>
      <slot></slot>
    </v-menu>
  </div>
</template>

<script>
export default {
  name: 'ContextPrompt',
}
</script>
