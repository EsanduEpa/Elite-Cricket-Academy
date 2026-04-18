<?php

// Log errors and display them during debugging (you can set back to 0 later)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

class Core {
    // URL format -> /controller/method/params
    protected $currentController = 'Home';
    protected $currentMethod = 'index';
    protected $param = [];

    public function __construct() {
        // print_r($this->getURL());

        $url = $this->getURL();
        $requestedUrl = $url;

        // Check if URL exists and has a controller
        if($url && file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
            // If the controller exists, then load it
            $this->currentController = ucwords($url[0]);
            // Unset the controller in the URL
            unset($url[0]);
        }

        // Call the controller
        require_once '../app/controllers/' . $this->currentController . '.php';

        if(isset($url[1])) {
            $requestedMethod = str_replace('-', '_', $url[1]);

            // Check if the method exists in the controller
            if(method_exists($this->currentController, $requestedMethod) && (new ReflectionMethod($this->currentController, $requestedMethod))->isPublic()) {
                $this->currentMethod = $requestedMethod;
                unset($url[1]);
            }


        }   

        if (function_exists('enforceRouteAccess')) {
            $unknownControllerRequested = $requestedUrl && !file_exists('../app/controllers/' . ucwords($requestedUrl[0]) . '.php');
            if ($unknownControllerRequested) {
                redirect('');
            }

            enforceRouteAccess($this->currentController, $this->currentMethod);
        }

        // Instantiate the controller only after auth has been checked.
        $this->currentController = new $this->currentController;

        // get the parameters
        $this->param = $url ? array_values($url) : [];

        // call method and pass parameters 
        call_user_func_array([$this->currentController, $this->currentMethod], $this->param);
    }

    public function getURL() {
        if(isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            return $url;
        }
    }
}
