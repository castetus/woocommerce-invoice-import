<?php

define("TEMPLATES_DIR", dirname( __FILE__, 2 ) . "/templates/");
define("LOGS_DIR", dirname( __FILE__, 2 ) . "/logs/");

add_action ('wp_ajax_send_labels', 'send_labels');
function send_labels(){

    $labels = array(
        'headersInstruction' => __('Select the columns that contain name (optional), sku (required), quantity and price of your products', 'woo-qty-price-import'),
        'rulesInstruction' => __('Select import options. To merging quantities in stock and in file, select "Merge price". To modify prices, select "Modify price".', 'woo-qty-price-import'),
        'customRulesInstruction' => __('You can add your custom price modifying rules for individual products or products groups by your criteria. Add new custom rule, then select the column and value of condition. Custom rules has priority over common price modifier.', 'woo-qty-price-import'),
        'logsViewButton' => __('Logs', 'woo-qty-price-import'),
        'templatesButton' => __('Templates', 'woo-qty-price-import'),
        'uploadLabel' => __( 'Select file...', 'woo-qty-price-import' ),
        'uploadButton' => __( 'Upload file', 'woo-qty-price-import' ),
        'addCustomRuleButton' => __( 'Add Custom Rule', 'woo-qty-price-import' ),
        'saveToDatabaseButton' => __( 'Save to Database', 'woo-qty-price-import' ),
        'applyChangesButton' => __( 'Apply Changes', 'woo-qty-price-import' ),
        'quantityFilter' => __( 'Quantity', 'woo-qty-price-import' ),
        'priceFilter' => __( 'Price', 'woo-qty-price-import' ),
        'modifierFilter' => __( 'Price Modifier', 'woo-qty-price-import' ),
        'modifierPlaceholder' => __( 'Number or percent', 'woo-qty-price-import' ),
        'customRuleSelectLabel' => __( 'Select column', 'woo-qty-price-import' ),
        'customRuleValueLabel' => __( 'Value', 'woo-qty-price-import' ),
        'customRuleModifier' => __( 'Price modifier', 'woo-qty-price-import' ),
        'newImportButton' => __( 'New Import', 'woo-qty-price-import' ),
        'templatesLabels' => array(
            'heading' => __('Templates Control', 'woo-qty-price-import'),
            'controlTemplates' => __('Templates', 'woo-qty-price-import'),
            'saveTemplate' => __( 'Save Template', 'woo-qty-price-import'),
            'saveTemplateButton' => __( 'save', 'woo-qty-price-import'),
            'closePopupButton' => __( 'close', 'woo-qty-price-import'),
            'loadTemplateButton' => __( 'load', 'woo-qty-price-import'),
            'removeTemplateButton' => __( 'remove', 'woo-qty-price-import'),
            'selectTemplate' => __('Select template...', 'woo-qty-price-import'),
            'templateName' => __('Template name', 'woo-qty-price-import'),
            'templateRemovedHeading' => __('Template removed', 'woo-qty-price-import'),
            'templateSavedHeading' => __('Template saved', 'woo-qty-price-import'),
            'templateSavedMessage' => __(' saved succesfully!', 'woo-qty-price-import'),
            'templateRemovedMessage' => __(' removed!', 'woo-qty-price-import'),
            'dataSavedHeading' => __('Data saved to database', 'woo-qty-price-import'),
            'dataSavedLink' => __("Download log file", 'woo-qty-price-import'),
            'logsViewHeading' => __("Log files", 'woo-qty-price-import'),
            'logViewButton' => __("Show log", 'woo-qty-price-import'),
            'logsListViewButton' => __("Show files", 'woo-qty-price-import'),
        ),
        'selectInputsValues' => array(
            ['label' => __('Name', 'woo-qty-price-import'), 'value' => 'name'],
            ['label' => __('SKU', 'woo-qty-price-import'), 'value' => 'sku'],
            ['label' => __('Quantity', 'woo-qty-price-import'), 'value' => 'qty'],
            ['label' => __('Price', 'woo-qty-price-import'), 'value' => 'price'] ,
        ),
        'tableHeaders' => array(
            ['text' => __('Product name', 'woo-qty-price-import'), 'value' => 'name'],
            ['text' => __('Product SKU', 'woo-qty-price-import'), 'value' => 'sku'],
            ['text' => __('Product quantity', 'woo-qty-price-import'), 'value' => 'qty'],
            ['text' => __('Product price', 'woo-qty-price-import'), 'value' => 'price'],
        ),
        'qtyModeValues' => array(
            ['value' => 'not', 'label' => __('Do not import', 'woo-qty-price-import')],
            ['value' => 'native', 'label' => __('Import from file', 'woo-qty-price-import')],
            ['value' => 'merge', 'label' => __('Merge quantity', 'woo-qty-price-import')],
        ),
        'priceModeValues' => array(
            ['value' => 'not', 'label' => __('Do not import', 'woo-qty-price-import')],
            ['value' => 'native', 'label' => __('Import from file', 'woo-qty-price-import')],
            ['value' => 'modify', 'label' => __('Modify price', 'woo-qty-price-import')],
        )
    );
    $obj = (object)$labels;
    $jsonlabels = json_encode($obj); 

    echo $jsonlabels;

    wp_die();
}

add_action ('wp_ajax_save_data', 'save_data');

function save_data(){

    $data = $_POST['data'];

    $product_rows = json_decode(stripslashes($data));
   
    $log = '';
    $message = [];
     
    foreach ($product_rows as $product_row){

        $product_row = (array)$product_row;
        $save_product = new Save_Product($product_row);
        $save_product->save($product_row);

        $log .= $save_product->log_message . (PHP_EOL);

        $message[] = $save_product->log_message;

    }

    $new_log = new Import_Log();
    $new_log->log_write($log);

    $file_url = LOGS_DIR . basename($new_log->file);

    $send_data['file'] = $file_url;
    $send_data['message'] = $message;

    echo json_encode($send_data);

    wp_die();
 
}

add_action('wp_ajax_castetus_save_template', 'castetus_save_template');

function castetus_save_template(){

    $data = stripslashes($_POST['data']);

    $decoded_data = json_decode($data, true);

    $template_name = $decoded_data['name'];
    $template = $decoded_data['values'];


    if (!file_exists(TEMPLATES_DIR)){
        mkdir($templates_dir, 0777);
    } 

    $file = TEMPLATES_DIR. $template_name . '.json';

    file_put_contents($file, json_encode($template));

    echo $template_name;

    wp_die();

}

add_action('wp_ajax_castetus_templates_list', 'castetus_templates_list');

function castetus_templates_list(){

    if (file_exists(TEMPLATES_DIR)){
        $files = array_slice(scandir(TEMPLATES_DIR), 2);

        function remove_extension($item){
            $pos = strpos($item, '.json');
            $item = substr($item, 0, $pos);
            return $item;
        }
    
        $templates_list = json_encode(array_map('remove_extension', $files));

        echo $templates_list;
    } 

    wp_die();
}

add_action('wp_ajax_castetus_remove_template', 'castetus_remove_template');

function castetus_remove_template(){

    $template_name = $_POST['data'];

    unlink(TEMPLATES_DIR . $template_name . '.json');
}

add_action('wp_ajax_castetus_load_template', 'castetus_load_template');

function castetus_load_template(){

    $template_name = $_POST['data'];
    $file = TEMPLATES_DIR . $template_name . '.json';

    $template = json_encode(file_get_contents($file));

    echo $template;

    wp_die();

}

add_action('wp_ajax_templates_list', 'templates_list');

function templates_list(){

    $directory = dir(plugin_dir_url( __FILE__ ) . '/templates');
    $list = json_encode($directory);
    echo $list;

}

add_action('wp_ajax_loglist', 'loglist');

function loglist(){

    $loglist;

    if (file_exists(LOGS_DIR) && count(scandir(LOGS_DIR)) > 0){

        $files = array_slice(scandir (LOGS_DIR), 2);

        foreach ($files as $file){

            $loglist[] = array(
                'fileName' => $file,
                'fileUri' => plugin_dir_url(__DIR__) . 'logs/' . $file,
            );

        }

    } else {
        $loglist = LOGS_DIR;
    }

    echo json_encode($loglist);

    wp_die();
}