<template>
            <v-card>
        <v-card-title class="headline success">
            {{labels.logsViewHeading}}
        </v-card-title>
        <v-spacer></v-spacer>
        <v-card-text>
              <v-list>
                <v-list-item 
                  v-for="item in logList" 
                  :key="item.fileName">
                <a download
                    :href="item.fileUri">
                    {{item.fileName}}
                </a>
                </v-list-item>
              </v-list>
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
            logList: {
                type: Array,
                default: [],
            }
        }
}
</script>