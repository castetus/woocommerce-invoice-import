<?php 

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Save_Product{

    public $product_data;
    public $log_message;
    public $product_id;

    public function __construct($product_row){

        $this->product_data = $product_row;

    if (!empty($this->product_data['name'])){
        $this->log_message = $this->product_data['name'] . ': ';
    } else {
        $this->log_message = 'Unnamed product: ';
    }
        $this->product_id = $this->check_product($this->product_data);

        if (in_array('merge', $this->product_data)){

            $this->product_data['qty'] = $this->merge_quantity($this->product_id);
           
        }

    }

    private function check_product($data){

        $sku = $data['sku'];

        if (!isset($sku) || $sku === ''){
             $this->log_message .= 'Unable to identificate product - empty SKU';
             return false;
        }

        $product_id = wc_get_product_id_by_sku( $sku );

        if (!$product_id ) {
            $this->log_message .= 'Unable to identificate product - uncorrect SKU';
            return false;
        }

        return $product_id;

    }

    private function merge_quantity($product_id){

        $product = wc_get_product($product_id);

        if (!$product){

            return false;
        }

        $stock_quantity = $product->get_stock_quantity();
        $import_quantity = $this->product_data['qty'];
        $new_stock = (int)$stock_quantity + (int)$import_quantity;

        return $new_stock;
    }

    public function save(){

        $product = wc_get_product($this->product_id);

        if (!$product){

            return false;
        }

        $product->set_stock_quantity($this->product_data['qty']);
        $product->set_price($this->product_data['price']);
        $product->save();

        $this->log_message .= 'quantity updated with value ' . $this->product_data['qty'] . ' and price updated with value ' . $this->product_data['price'];

    }
}

