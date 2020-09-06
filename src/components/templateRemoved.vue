<template>
        <v-card>
        <v-card-title class="headline success text-center">
            {{labels.templateRemovedHeading}}
        </v-card-title>
        <v-spacer></v-spacer>
        <v-card-text>
            {{templateName}} {{labels.templateRemovedMessage}}
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
        <v-spacer></v-spacer>
          <v-btn @click="$emit('hide')">
            {{labels.closePopupButton}}
          </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
  props: {
    labels: {
      type: Object,
      default: {},
    },
    templateName: {
      type: String,
      default: ''
    }
  }
}
</script>