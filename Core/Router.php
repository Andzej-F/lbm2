<?php

namespace Core;

/**
 * Router
 * 
 * PHP version 8.1.1
 */
class Router
{
    /**
     * Associative array of routes (the routing table)
     * @var array
     */
    protected $routes = [];

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $params = [];

    /**
     * Add a route to the routing table
     * 
     * @param string $route The route URL
     * @param array $params (Optional) Parameters (controller, action, etc.)
     * 
     * @return void
     */
    public function add($route, $params = [])
    {
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);

        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z-]+)\}/', '(?P<\1>[a-z-]+)', $route);

        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);

        // Add a start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    /**
     * Get all the routes from the routing table
     * 
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Match the route to the routes in the routing table, setting the $params
     * property if a route is found.
     * 
     * @param string $url The route URL
     * 
     * @return boolean true if a match is found, false otherwise
     */
    // $url='signup/create';
    public function match($url)
    {
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url, $matches)) {

                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }

        return false;
    }

    /**
     * Get the currently matched parameters
     * 
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Dispatch the route, i.e. dynamically create the controller
     * object and run the action method
     * 
     * @param string $url The route URL
     * 
     * @return void
     */
    // $url='signup/create';
    public function dispatch($url)
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)) {
            $controller = $this->params['controller'];
            $controller = $this->convertToStudlyCaps($controller);
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)) {
                // Dynamically create controller object
                $controller_object = new $controller($this->params);

                $action = $this->params['action'];

                $action = $this->convertToCamelCase($action);

                if (preg_match('/action$/i', $action) == 0) {
                    $action = $action . "Action";
                    $controller_object->$action();
                } else {
                    throw new \Exception("Method $action in controller $controller cannot be called directly - remove the Action suffix to call this method");
                }
            } else {
                // echo "Controller class $controller not found";
                throw new \Exception("Controller class $controller not found");
            }
        } else {
            // echo "No route matched";
            throw new \Exception("No route matched.", 404);
        }
    }

    /**
     * Convert the string with hyphens to StudlyCaps
     * e.g post-authors => PostAuthors
     * 
     * @param string $string The string to convert
     * 
     * @return string
     */
    protected function convertToStudlyCaps($string)
    {
        // Remove dashes
        $string = str_replace('-', ' ', $string);

        // Capitalise the first letter
        $string = ucwords($string);

        // Remove spaces
        $string = str_replace(' ', '', $string);

        return $string;
    }

    /**
     * Convert the string with hyphens to camelCase,
     * e.e add-new => addNew
     * 
     * @param string $string the string to convert
     * 
     * @return string
     */
    protected function convertToCamelCase($string)
    {
        return lcfirst($this->convertToStudlyCaps($string));
    }

    /**
     * Remove the query string variables from the URL (if any). As the full 
     * query string is used for the route, any variables at the end will need
     * to be removed before the route is matched to the routing table. For
     * example:
     * 
     * URL                          $_SERVER['QUERY_STRING']      Route
     * --------------------------------------------------------------------
     * localhost/                          ''                      ''
     * localhost/?                         ''                      ''
     * localhost/?page=1                   page=1                   ''
     * localhost/posts?page=1              posts&page=1             posts
     * localhost/posts/index               posts/index              posts/index
     * localhost/posts/index?page=1        posts/index&page=1       posts/index
     * 
     * A URL of the format localhost/?page (one variable name, no value) won't
     * work however. (The .htaccess file converts the first ? to a & when 
     * it's passed through to the $_SERVER variable).
     * 
     * @param string $url The full URL (e.g. authors/delete&add=5,
     * authors/delete&add=5?id_number=6)
     * 
     * @return string The URL with the query string variables removed
     */
    protected function removeQueryStringVariables($url)
    {
        if ($url != '') {
            $parts = explode('&', $url, 2);

            if (strpos($parts[0], '=') === false) {
                $url = $parts[0];
            } else {
                $url = '';
            }
        }

        return $url;
    }

    /**
     * Get the namespace for the controller class. The namespace defined in the
     * route parameters is added if present
     * 
     * @return string The request URL
     */
    protected function getNamespace()
    {
        $namespace = "App\Controllers\\";

        if (array_key_exists('namespace', $this->params)) {
            $namespace = $namespace . $this->params['namespace'] . '\\';
        }

        return $namespace;
    }
}
