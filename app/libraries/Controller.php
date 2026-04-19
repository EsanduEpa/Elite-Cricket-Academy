<?php
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
    class Controller {
        // Load a model class from app/models and return an object.
        // Controllers call this as $this->model('M_Users') when they need database logic.
        public function model($model) {
            require_once '../app/models/' . $model . '.php';    
            return new $model();
    }
        // Load a view file from app/views and pass data to it.
        // The $data array becomes available inside the view for rendering HTML.
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
