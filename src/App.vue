<template>
    <v-app>
        <h1>Woocommerce Import</h1>
            <v-container d-flex>
                <v-col cols="6">
                    <v-btn @click="showData">show</v-btn>
                    <v-file-input
                        v-model="file" 
                        :label="labels.uploadLabel" 
                        accept="csv/*">
                    </v-file-input>
                </v-col>
                <v-col cols="2">
                    <v-btn color="success" 
                        @click="onUpload">
                        {{labels.uploadButton}}
                    </v-btn>
                </v-col>

                <v-col cols=4>
                    <v-btn outlined
                    @click="showPopup('templatesControl')"
                    color="success">
                    {{labels.templatesButton}}
                </v-btn>
                    
                </v-col>

                <v-dialog 
                    v-model="popup"
                    max-width="600">
                    <component 
                        :is="popupContent" 
                        v-bind="property"
                        :logName="log.file"
                        :log="log.message"
                        @removed="showPopup('templateRemoved')"
                        @hide="hidePopup" 
                        @save="saveTemplate" 
                        @load="loadTemplate">
                    </component>
                </v-dialog>
            </v-container>
            <v-container v-if="headers" d-flex>
                <v-col v-for="select in selectInputsValues" cols="3">
                    <v-select 
                    v-model="selectedHeaders[select.value]"
                    :items="headers"
                    :label="select.label"
                    bottom
                    autocomplete>
                    </v-select>
                </v-col>
            </v-container>
            <v-container d-flex v-if="headers">
                <v-col cols="3" v-if="selectedHeaders.qty">
                    {{labels.quantityFilter}}
                    <v-radio-group v-model="mode.qty">
                        <v-radio v-for="item in qtyModeValues" :label="item.label" :value="item.value"></v-radio>
                    </v-radio-group>
                </v-col>
                <v-col cols="3" v-if="selectedHeaders.price">
                    {{labels.priceFilter}}
                    <v-radio-group v-model="mode.price">
                        <v-radio v-for="item in priceModeValues" :label="item.label" :value="item.value"></v-radio>
                    </v-radio-group>
                </v-col>
                <v-col cols="4" v-if="mode.price === 'modify'">
                    {{labels.modifierFilter}}
                    <v-text-field v-model="priceModifier" :rules="[rules.required, rules.regexp]" :placeholder="labels.modifierPlaceholder"></v-text-field>
                        <v-btn class="align-self-end" color="success" @click="addCustomRule">{{labels.addCustomRuleButton}}</v-btn>
                    </v-col>
            </v-container>
                    <v-container d-flex v-for="(rule, n) in customRules">
                        <v-col cols=4>
                            <v-select :items="headers" v-model="customRules[n].column" :label="labels.customRuleSelectLabel" ></v-select>
                        </v-col>
                        <v-col cols="1" class="d-flex justify-center">
                            <!-- <v-select :items="equalitySignes" v-model="customRules[n].equality" label="Equality"></v-select> -->
                            <h3 class="align-self-center">=</h3>
                        </v-col>
                        <v-col cols="4">
                            <v-text-field v-model="customRules[n].value" :placeholder="labels.customRuleValueLabel"></v-text-field>
                        </v-col>
                        <v-col cols=2>
                            <v-text-field v-model="customRules[n].modifier" :rules="[rules.regexp]" :placeholder="labels.customRuleModifier"></v-text-field>
                                
                        </v-col>
                        <v-col cols="1" class="d-flex">
                                <v-btn class="align-self-center" icon color="error" @click="removeRule(n)">
                                    <v-icon>close</v-icon>
                                </v-btn>
                        </v-col>
                    </v-container>
                <v-container d-flex justify-center>
                    <v-btn color="success" max-width="300px" v-if="showApplyButton" @click="processPriceList">{{labels.applyChangesButton}}</v-btn>
                </v-container>
                <v-container d-flex justify-center>
                    <v-data-table v-if="showTable"
                    width="100%"
                    :headers="tableHeaders"
                    :items="importList"
                    :items-per-page="10"
                    class="elevation-1"
                  ></v-data-table>
                </v-container>
            <v-container d-flex justify-center>
                <v-btn @click="saveData" large color="success" v-if="showTable">{{labels.saveToDatabaseButton}}</v-btn>
            </v-container>
        </v-app>
</template>

<script>

import Papa from 'papaparse'
import ajaxurl from './index.js'
import templateSaved from './components/templateSaved.vue'
import templatesControl from './components/templatesControl.vue'
import templateRemoved from './components/templateRemoved.vue'
import afterDataSaved from './components/afterDataSaved.vue'
export default {
    components: {
    templateSaved,
    templateRemoved,
    templatesControl,
    afterDataSaved,
  },
  data() {
      return{
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
    content: null,
    template: null,
    // logName: '',
    log: {
        file: '',
        message: [],
    },
      }
  },

created() {
    this.$root.fetchData('send_labels', '')
    .then((data) => this.labels = data.data);
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
                complete: (results) => {

                    this.headers = results.data.shift();
                    this.items = results.data;
                }
            });
            // this.showData();
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

            this.$root.fetchData('castetus_save_template', newTemplate).then(() => {
              this.showPopup(templateSaved)
            });

        },
        loadTemplate(templateName) {

            this.$root.fetchData('castetus_load_template', templateName)
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
            this.$root.fetchData('save_data', data)
            .then(this.hidePopup())
            .then((data) => this.log = data.data)
            // .then((data) => this.log = data.data['message'])
            .then(this.showPopup('afterDataSaved'));

        },
        showData(){
            console.log(this.mode);

        }

    }
}
</script>