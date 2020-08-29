<template>
    <v-card>
        <v-card-title class="headline success lighten-2">
            {{heading}}
        </v-card-title>
        <v-spacer></v-spacer>
        <v-card-text>
            Template {{templateName}} saved succesfully!
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
        <v-spacer></v-spacer>
          <v-btn @click="$emit('hide')">
            Close
          </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
  data () {
    return {
      heading: 'Template Saved',
    }
  },
  props: ['templateName']
}
</script>