<template>
        <v-card>
        <v-card-title class="headline success">
            {{labels.dataSavedHeading}}
        </v-card-title>
        <v-spacer></v-spacer>
        <v-card-text class="mt-5">
            <a class="float-right"
              :href="`${logName}`"
              download>
              {{labels.dataSavedLink}}
            </a>
            <v-btn 
              color="success"
              @click="expand = !expand"
              >{{labels.logViewButton}}
            </v-btn>
            <v-expand-transition>
              <v-list>
                <v-list-item 
                  v-show="expand"
                  v-for="item in log" 
                  :key="item">
                  {{item}}
                </v-list-item>
              </v-list>
            </v-expand-transition>
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
  data(){
    return{
      expand: false
    }
  },
    props: {
    labels: {
      type: Object,
      default: {},
    },
    logName: {
      type: String,
      default: ''
    },
    log: {
      type: Array,
      default: []
    }
  }
}
</script>