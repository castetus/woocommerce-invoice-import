<?php

define("TEMPLATES_DIR", __DIR__ . "/templates/");

add_action ('wp_ajax_send_labels', 'send_labels');
function send_labels(){
    $domain = 'woo-product-import';

    $labels = array(
        'templatesButton' => __('Templates', $domain),
        'uploadLabel' => __( 'Select file...', $domain ),
        'uploadButton' => __( 'Upload file', $domain ),
        // 'loadTemplateButton' =>  __( 'Load Template', $domain ),
        // 'saveTemplateButton' => __( 'Save Template', $domain ),
        'addCustomRuleButton' => __( 'Add Custom Rule', $domain ),
        'saveToDatabaseButton' => __( 'Save to Database', $domain ),
        'applyChangesButton' => __( 'Apply Changes', $domain ),
        'quantityFilter' => __( 'Quantity', $domain ),
        'priceFilter' => __( 'Price', $domain ),
        'modifierFilter' => __( 'Price Modifier', $domain ),
        'modifierPlaceholder' => __( 'Number or percent', $domain ),
        'customRuleSelectLabel' => __( 'Select column', $domain ),
        'customRuleValueLabel' => __( 'Value', $domain ),
        'customRuleModifier' => __( 'Price modifier', $domain ),
    );
    $obj = (object)$labels;
    $jsonlabels = json_encode($obj);

    echo $jsonlabels;

    wp_die();
}

add_action ('wp_ajax_get_data', 'get_data');

function get_data(){

    $data = $_POST('data');

    $product_rows = json_decode($data);
   
    $log = '';
     
    foreach ($product_rows as $product_row){

        $product_row = (array)$product_row;
        $save_product = new Save_Product($product_row);
        $save_product->save($product_row);

        $log .= $save_product->log_message . (PHP__OL);

    }

    $new_log = new Import_Log();
    $new_log->log_write($log);

    $file_name = $new_log->file;

    echo $file_name;

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

    $files = array_slice(scandir(TEMPLATES_DIR), 2);

    function remove_extension($item){
        $pos = strpos($item, '.json');
        $item = substr($item, 0, $pos);
        return $item;
    }

    $templates_list = json_encode(array_map('remove_extension', $files));

    echo $templates_list;

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