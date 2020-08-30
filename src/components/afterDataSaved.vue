<template>
        <v-card>
        <v-card-title class="headline success">
            {{heading}}
        </v-card-title>
        <v-spacer></v-spacer>
        <v-card-text>
            {{text}}
            <v-divider></v-divider>
            <a :href="`${logName}`"
                :download=true>
                Download Log File
            </a>
            <v-divider></v-divider>
            <v-simple-table>
                <tbody>
                    <tr v-for="item in log" :key="item">
                        <td>{{item}}</td>
                    </tr>
                </tbody>
            </v-simple-table>
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
      heading: 'Data Saved',
      text: 'All data successfully saved to database!',
    }
  },
  props: ['logName', 'log'],
  methods: {

  }
}
</script>