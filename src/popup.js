Vue.component('saveTemplatePopup', {
    data: function () {
      return {
        // header: 'header',
      }
    },
    template: `<v-card-title class="headline grey lighten-2">
    Save template
  </v-card-title>
  <v-card-text>
    <v-text-field v-model="templateName">
  </v-card-text>
  <v-card-actions>
    <v-btn @click="saveTemplate">
        Save 
    </v-btn>
        <v-btn @click="hidePopup">
            Cancel 
        </v-btn>
  </v-card-actions>`
  });