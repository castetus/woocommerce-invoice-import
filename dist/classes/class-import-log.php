<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Import_Log{

    public $file;
    public $log_dir;
    public $test;

    public function __construct(){

        $date = date('d-m-Y_h:i:s');

        $this->log_dir = dirname( __FILE__, 2) . '/logs';

        if ( !file_exists($this->log_dir)){
            mkdir($this->log_dir); 
        }

        $this->file = $this->log_dir . '/import-log_' . $date . '.txt';

    }

    public function log_write($log){
        
        file_put_contents($this->file, $log);

    }

}