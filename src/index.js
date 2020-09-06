'use strict';

import Vue from 'vue';
import vuetify from './plugins/vuetify'
import axios from 'axios'
import App from './App.vue'

let importPlugin = new Vue({
    vuetify,
    components: {
        App
    },
    methods: {
        fetchData(action, data){

            let formdata = new FormData();
            formdata.append('action', action);
            formdata.append('data', data);

            return axios({
                url: ajaxurl,
                method: 'post',
                data: formdata,
            })
        },
    }
}).$mount('#import-plugin');

