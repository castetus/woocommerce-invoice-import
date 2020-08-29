'use strict';

import Vue from 'vue';
import axios from 'axios'
import Papa from 'papaparse'
import vuetify from './plugins/vuetify'

import templateSaved from './components/templateSaved.vue'
import templatesControl from './components/templatesControl.vue'
import templateRemoved from './components/templateRemoved.vue'
import afterDataSaved from './components/afterDataSaved.vue'

let ajaxurl = 'http://cd57456.tmweb.ru/wp-admin/admin-ajax.php';

let app = new Vue({
vuetify,

components: {
    templateSaved,
    templateRemoved,
    templatesControl,
    afterDataSaved,
  },
  data: {
    templateName: '',
    labels: {},
    file: null,
    headers: null,
    items: null,
    selectedHeaders: {
            name: '',
            sku: '',
            qty: '',
            price: '',
        },
    selectInputsValues: [
        {label: 'Name', value: 'name'},
        {label: 'SKU', value: 'sku'},
        {label: 'Quantity', value: 'qty'},
        {label: 'Price', value: 'price'},
    ],
    qtyModeValues: [
            {value: 'not', label: 'Do not import'},
            {value: 'native', label: 'Import from file'},
            {value: 'merge', label: 'Merge quantity'},
        ],
    priceModeValues: [
            {value: 'not', label: 'Do not import'},
            {value: 'native', label: 'Import from file'},
            {value: 'modify', label: 'Modify price'},
        ],
    priceModifier: '',
    rules: {
        required: value => !!value || 'Required',
        regexp: value => {
          const pattern = /^-?([0-9?,.]+(\.[0-9]{2})?\%?)$/
          return pattern.test(value) || 'Invalid' }
        },
    mode: {
            qty: '',
            price: '',
            change: '',
        },
    equalitySignes: ['=', '>', '<'],
    customRules: [],
    columnNumbers: {},
    importList: [],
    showTable: false,
    tableHeaders: [
        { text: 'Product name', value: 'name' },
        { text: 'Product SKU', value: 'sku' },
        { text: 'Product quantity', value: 'qty' },
        { text: 'Product price', value: 'price' },
    ],
    popup: false,
    popup1: false,
    content: null,
    template: null,
  },

beforeCreate() {

    let data = new FormData();
    data.append('action', 'send_labels');

    loadLabels();

    async function loadLabels(){
        let response = await fetch(ajaxurl, {
            method: 'POST',
            body: data,
        });
        if (response.ok){
            let labels = await response.json();
            app.labels = labels;
        }
    }
    
},

computed: {
    showApplyButton: function () {
        if (this.selectedHeaders.name !== '' || this.selectedHeaders.sku !== ''){
            if (this.selectedHeaders.qty !== '' || this.selectedHeaders.price !== ''){
                return true;
            }
        }
    },
    popupContent: function(){
        return this.content;
    },
    property: function(){
        return {templateName: this.templateName}
    }
},
methods: {
        showPopup(content){
            this.popup = true;
            this.content = content;
        },
        hidePopup(){
            this.popup = false;
            this.content = null;
        },

        onUpload() {
            
            if (this.file === null){
                return false;
            }
                let parser = Papa.parse(this.file, {
                complete: function(results) {

                    app.headers = results.data.shift();
                    app.items = results.data;
                }
            });
            this.showData();
        },
        addCustomRule() {
            let rule = {
                column: '',
                equality: '',
                value: '',
                modifier: '',
            };
            this.customRules.push(rule);
        },
        removeRule (n) {
            this.customRules.splice(n, 1);
        },
        processPriceList() {

            this.onUpload();

            let rows = this.items.slice();

            this.setColumnNumbers(); 

            rows = rows.map(row =>
                this.setQuantity(row)
            );

            if (this.customRules.length === 0){
                rows = rows.map(row =>
                    this.setPrice(row)
                );
            } else {

                for (let row of rows){
                    let customRules = this.customRules;
                    for (let customRule of customRules){
                        let customIdentifier = this.headers.indexOf(customRule.column);
                        if (row[customIdentifier] == customRule.value){
                            row[this.columnNumbers.price] = this.setNewPrice(row[this.columnNumbers.price], customRule.modifier);
                            if (row.indexOf('custom') === -1){
                                row.push('custom');
                            }
                        } 
                    }
                    if (row.indexOf('custom') === -1){
                        row.push(row.indexOf('custom'));
                        row[this.columnNumbers.price] = this.setNewPrice(row[this.columnNumbers.price], this.priceModifier);
                    }
                }
            }
            if (this.importList.length > 0){
                this.importList = [];
            }
            rows = rows.map(row =>
                this.addToList(row)
            );

        },
        setColumnNumbers() {

            for (let key in this.selectedHeaders){
                let number = this.headers.indexOf(this.selectedHeaders[key]);
                this.columnNumbers[key] = number;
                };

        },
        setQuantity(row) {
            if (this.mode.qty == 'not'){
                row[this.columnNumbers.qty] = '';
            } else if (this.mode.qty == 'merge'){
                row['merge'] = 'merge';
            }
            
            return row;
        },
        setPrice(row) {

            if (this.mode.price == 'not'){
                row[this.columnNumbers.price] = '';
            } else if (this.mode.price == 'modify'){
                row[this.columnNumbers.price] = this.setNewPrice(row[this.columnNumbers.price], this.priceModifier);
            }

            return row;
        },
        setNewPrice(price, modifier) {

            let percent = modifier.indexOf('%', 0);
            if (percent === -1){
                price = +price + +modifier;
            } else {
                modifier = modifier.substr(0, percent);
                price = +price + (price / 100 * +modifier);
            }
            if (isNaN(price)){
                price = '';
            }

            return price; 

        },
        addToList(row) {
            let importRow = {
                name: row[this.columnNumbers.name],
                sku: row[this.columnNumbers.sku],
                qty: row[this.columnNumbers.qty],
                price: row[this.columnNumbers.price],
                merge: row.merge,
            }
            if (importRow.name || importRow.sku){
                this.importList.push(importRow);
            }
            
            if (this.importList.length > 0){
                this.showTable = true;
            }
        },
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
        saveTemplate(templateName) {

            this.templateName = templateName;

            let newTemplate = JSON.stringify({
                name: templateName,
                values: [
                this.selectedHeaders,
                this.priceModifier,
                this.mode,
                this.customRules,
                ]
            });

            this.hidePopup();

            this.fetchData('castetus_save_template', newTemplate).then(() => {
              this.showPopup(templateSaved)
            });

        },
        loadTemplate(templateName) {

            this.fetchData('castetus_load_template', templateName)
            .then((response) => {this.template = JSON.parse(response.data)})
            .then(() => {
                if (this.template !== null){
                    this.selectedHeaders = this.template[0];
                    this.priceModifier = this.template[1];
                    this.mode = this.template[2];
                    this.customRules = this.template[3];
                }
            });

            this.hidePopup();
        },

        saveData(){
            let data = JSON.stringify(this.importList);
            this.fetchData('save_data', data)
            .then(this.hidePopup())
            .then(this.showPopup('afterDataSaved'));

        },
        showData(){
            console.log(this.mode);

        }

    }
}).$mount('#import-plugin');

