<template>
    <v-card>
        <v-card-title class="headline success">
            {{labels.heading}}
        </v-card-title >
        <v-card-text>
            <v-container>
                <v-row>
                    <v-col cols="6">
                        <h2 class="h2">
                            {{labels.controlTemplates}}
                        </h2>
                        <v-select 
                            :items="templates"
                            :label="labels.selectTemplate" 
                            v-model="selectedTemplate">
                        </v-select>
                    </v-col>
                    <v-col cols="6">
                        <h2 class="h2">
                            {{labels.saveTemplate}}
                        </h2>
                        <v-text-field 
                            :placeholder="labels.templateName"
                            v-model="templateName">
                        </v-text-field>
                    </v-col>
                </v-row>
            </v-container>
        </v-card-text>
        <v-divider></v-divider>
        <v-card-actions>
          <v-spacer></v-spacer>
            <v-btn 
              color="success" 
              @click="load"
              :disabled="!selectedTemplate">
              {{labels.loadTemplateButton}}
            </v-btn>
            <v-btn 
              color="red" 
              @click="removeTemplate"
              :disabled="!selectedTemplate">
              {{labels.removeTemplateButton}}
            </v-btn>
            <v-btn 
              color="success" 
              @click="save"
              :disabled="!templateName">
              {{labels.saveTemplateButton}}
            </v-btn>
            <v-btn @click="$emit('hide')">
              {{labels.closePopupButton}}
            </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
export default {
  data () {
    return {
        templateName: '',
        selectedTemplate: '',
        templates: null,
    }
  },
  props: {
    labels: {
      type: Object,
      default: {},
    }
  },
  created () {
    this.loadTemplatesList();
  },
  methods: {
    loadTemplatesList(){
      this.$root.fetchData('castetus_templates_list', '')
      .then((result) => {
        this.templates = result.data;
      });
    },
    save(){
      if (this.templateName !== ''){
        this.$emit('save', this.templateName);
      }
    },
    load(){
      this.$emit('load', this.selectedTemplate);
    },
    removeTemplate(){
      console.log(this.selectedTemplate);
      this.$root.fetchData('castetus_remove_template', this.selectedTemplate)
      .then((result) => {
        this.loadTemplatesList();
        this.$emit('removed', this.selectedTemplate);
      })
    },
  }
}
</script>

<style>
  h2{
    color: red;
  }
</style>