<?php
/*
Plugin Name: Import products with quantity merging for Woocommerce
Plugin URI: http://страница_с_описанием_плагина_и_его_обновлений
Description: ///
Version: 1.0
Author: Castetus
Author URI: https://castetus.ru
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
	add_menu_page( 'Woo Product Import', 'Woo Product Import', 'edit_others_posts', '/castetus-import/castetus-import.php', 'product_import', plugins_url( '' ), 13 ); 
}
function castetus_import_script() {

    wp_enqueue_script( 'bundle', plugin_dir_url( __FILE__ ) . 'assets/js/bundle.js', [], null, true );
    
}
add_action( 'admin_enqueue_scripts', 'castetus_import_script' );

function castetus_import_style() {
    wp_enqueue_style('mdi', 'https://cdn.jsdelivr.net/npm/@mdi/font@5.x/css/materialdesignicons.min.css');
    wp_enqueue_style('vuetify-style', plugin_dir_url( __FILE__ ) . 'assets/css/vuetify-v2.3.8.min.css');
    wp_enqueue_style('castetus-import-style', plugin_dir_url( __FILE__ ) . 'assets/css/custom-styles.css');
}
add_action('admin_print_styles', 'castetus_import_style');

require_once 'ajax-api.php';


foreach ( glob( plugin_dir_path( __FILE__ )."classes/*.php" ) as $file ){
    require_once $file;
}

function product_import(){ 
    require_once 'markup.php';
 }

 
?>