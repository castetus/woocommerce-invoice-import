<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
        <div id="import-plugin">
        <v-app>
            <v-container d-flex>
            <v-btn @click="showData">show</v-btn>
                <v-col cols="6">
                    <v-file-input
                        v-model="file" 
                        :label="labels.uploadLabel" 
                        accept="csv/*">
                    </v-file-input>
                </v-col>
                <v-col cols="2">
                    <v-btn color="success" @click="onUpload">{{labels.uploadButton}}</v-btn>
                </v-col>
                <v-col cols=4>
                    <v-btn outlined
                    @click="showPopup('templatesControl')"
                    color="success">
                    {{labels.templatesButton}}
                </v-btn>
                <v-dialog 
                    v-model="popup"
                    max-width="300">
                    <component 
                        :is="popupContent" 
                        v-bind="property"

                        @hide="hidePopup" 
                        @save="saveTemplate" 
                        @load="loadTemplate">
                    </component>
                </v-dialog>
            </v-container>
            <v-container v-if="headers"   d-flex>
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
                            <v-text-field v-model="customRules[n].modifier" :rules="[rules.regexp]" :placeholder="labels.customRuleModifier"></v-input>
                                
                        </v-col>
                        <v-col cols="1" class="d-flex">
                                <v-btn class="align-self-center" icon color="error" @click="removeRule(n)">
                                    <v-icon>close</v-icon>
                                </v-btn>
                        </v-col>
                    </v-container>
                <v-container d-flex justify-center>
                    <v-btn color="success" v-if="showApplyButton" @click="processPriceList">{{labels.applyChangesButton}}</v-btn>
                </v-container>
                <v-container d-flex justify-center>
                    <v-data-table v-if="showTable"
                    width="100%"
                    :headers="tableHeaders"
                    :items="importList"
                    :items-per-page="20"
                    class="elevation-1"
                  ></v-data-table>
                </v-container>
            <v-container d-flex justify-center>
                <v-btn large color="success" v-if="showTable">{{labels.saveToDatabaseButton}}</v-btn>
            </v-container>
                

        </v-app>
    </div>