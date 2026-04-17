<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
    class Controller {
        //to load a model
        public function model($model) {
            require_once '../app/models/' . $model . '.php';    
            // Instantiate the model and pass it to the controller member variable
            return new $model();
    }
        public function view($view, $data = []) {
            $viewPath = APPROOT . '/views/' . $view . '.php';
            if (file_exists($viewPath)) {
                require_once $viewPath;
        }
        else {
            die('corresponding view does not exist');
        }
    }
}

?>