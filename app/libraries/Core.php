<?php

// Development error reporting. For a real production server, display_errors
// should normally be 0 and errors should only be written to logs.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
error_reporting(E_ALL);

class Core {
    // Default route when the URL is empty: http://localhost/Elite/
    protected $currentController = 'Home';
    protected $currentMethod = 'index';
    protected $param = [];

    public function __construct() {
        // URL format: /controller/method/param1/param2
        // Example: /player/dashboard loads Player::dashboard().
        $url = $this->getURL();
        $requestedUrl = $url;

        // First URL segment is treated as the controller name.
        // ucwords() keeps the existing project convention: player -> Player.php.
        if($url && file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
        }

        // Load the controller class file before checking whether its method exists.
        require_once '../app/controllers/' . $this->currentController . '.php';

        if(isset($url[1])) {
            // Hyphens in URLs are converted to underscores in PHP method names.
            // Example: /player/payhere-return would call payhere_return().
            $requestedMethod = str_replace('-', '_', $url[1]);

            // Only public controller methods can be routed from the browser.
            // This prevents protected/private helper methods from being called by URL.
            if(method_exists($this->currentController, $requestedMethod) && (new ReflectionMethod($this->currentController, $requestedMethod))->isPublic()) {
                $this->currentMethod = $requestedMethod;
                unset($url[1]);
            }


        }   

        if (function_exists('enforceRouteAccess')) {
            $unknownControllerRequested = $requestedUrl && !file_exists('../app/controllers/' . ucwords($requestedUrl[0]) . '.php');
            if ($unknownControllerRequested) {
                // Unknown URLs are sent to the public home page instead of exposing errors.
                redirect('');
            }

            // Central security gate: checks public routes, login status, session timeout,
            // and whether the logged-in role can access this controller.
            enforceRouteAccess($this->currentController, $this->currentMethod);
        }

        // Instantiate the controller only after route access has been checked.
        $this->currentController = new $this->currentController;

        // Remaining URL segments become method parameters.
        $this->param = $url ? array_values($url) : [];

        // Finally call the controller method.
        call_user_func_array([$this->currentController, $this->currentMethod], $this->param);
    }

    public function getURL() {
        if(isset($_GET['url'])) {
            // Sanitize and split the rewritten URL into controller/method/params.
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);

            return $url;
        }
    }
}
