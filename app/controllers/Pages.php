<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

class Pages {
    public function __construct() {
        //echo "This is the pages constructor method in the Pages controller.";
    }

    public function index() {
        
    }
    public function about($name) {
        echo "Hi, I am " . $name;
    }
}
?>