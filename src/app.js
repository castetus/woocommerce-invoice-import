'use strict';

document.addEventListener('DOMContentLoaded', function(){

    let app = new Vue({
    el: '#import-plugin',
    // vuetify: new Vuetify(),
    data: {
        labels: {
            uploadLabel: 'Select file...',
            uploadButton: 'Upload file',
            loadTemplateButton: 'Load Template',
            saveTemplateButton: 'Save Template',
            addCustomRuleButton: 'Add Custom Rule',
            saveToDatabaseButton: 'Save to Database',
            applyChangesButton: 'Apply Changes',
            quantityFilter: 'Quantity',
            priceFilter: 'Price',
            modifierFilter: 'Price Modifier',
            modifierPlaceholder: 'Number or percent',
            customRuleSelectLabel: 'Select column',
            customRuleValueLabel: 'Value',
            customRuleModifier: 'Price modifier',
        },
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
    },
    computed: {
        showApplyButton: function () {
            if (this.selectedHeaders.name !== '' || this.selectedHeaders.sku !== ''){
                if (this.selectedHeaders.qty !== '' || this.selectedHeaders.price !== ''){
                    return true;
                }
            }
        }
    },
    methods: {
            onUpload() {
                
                if (this.file === null){
                    return false;
                }
                    let parser = Papa.parse(this.file, {
                    complete: function(results) {
                        app.headers = results.data.shift();
                        app.items = results.data;
                    }
                })
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
            showData() {
                    console.log(this.items[0]);
                    console.log(this.importList[0]);
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
                }
                if (importRow.name || importRow.sku){
                    this.importList.push(importRow);
                }
                
                if (this.importList.length > 0){
                    this.showTable = true;
                }
            },
            saveTemplate() {
                let newTemplate = [
                    this.selectedHeaders,
                    this.priceModifier,
                    this.mode,
                    this.customRules,
                ];
                console.log(newTemplate);

            },
            loadTemplate() {
                console.log('template');
            }
        }


    });

});