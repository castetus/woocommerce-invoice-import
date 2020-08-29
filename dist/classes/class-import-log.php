<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Import_Log{

    public $file;

    public function __construct(){

        $date = date('d-m-Y_h:i:s');
        $this->file = __DIR__ . '../import-log_' . $date . '.txt';

    }

    public function log_write($log){
        
        file_put_contents($this->file, $log);

    }

}