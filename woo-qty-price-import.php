<?php
/*
Plugin Name: Import products with quantity merging for Woocommerce
Plugin URI: http://страница_с_описанием_плагина_и_его_обновлений
Description: ///
Version: 1.0
Author: Castetus
Author URI: https://castetus.ru
Text Domain: woo-qty-price-import
Domain Path: /languages
*/
?>
<?php
/*  Copyright 2020  Castetus  (email: kboikov@mail.ru)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php 
// header('Access-Control-Allow-Origin: *');
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action( 'admin_menu', 'register_woo_import_page' );

function register_woo_import_page(){
	add_menu_page( 'Woo Product Import', 'Woo Product Import', 'edit_others_posts', 'woo-qty-price-import.php', 'product_import', plugins_url( '' ), 13 ); 
}

$path = dirname( __FILE__, 2 ) . '/woocommerce/vendor/autoload.php';

require $path;
 
// use Automattic\WooCommerce\Client;
 
// $woocommerce = new Client(
//     'http://example.com', 
//     'ck_c37a7173b2532e777f2613b8307c70f7f3b3dee0', 
//     'cs_5a3cbfb21bb98ce469fe99bc31adf423931f6617',
//     [
//         'wp_api' => true,
//         'version' => 'wc/v2',
//     ]
// );
 
// // Вывести все товары
// print_r($woocommerce->get('products'));


foreach ( glob( plugin_dir_path( __FILE__ )."includes/*.php" ) as $file ){
    require_once $file;
}

function product_import(){ 

    require_once 'admin/partials/markup.php';
    wp_enqueue_script( 'bundle', plugin_dir_url( __FILE__ ) . 'admin/js/bundle.js', [], null, true );
    wp_enqueue_style('mdi', 'https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css');
    wp_enqueue_style('vuetify-style', plugin_dir_url( __FILE__ ) . 'admin/css/vuetify-v2.3.8.min.css');
    wp_enqueue_style('custom-styles', plugin_dir_url( __FILE__ ) . 'admin/css/custom-styles.css');
    
 }

 
?>