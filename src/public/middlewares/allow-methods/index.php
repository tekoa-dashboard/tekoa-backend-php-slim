<?php
    /**
    * MIDDLEWARE to Access-Control-Allow-Methods
    */
    $app->add(function($request, $response, $next) {
        $route = $request->getAttribute("route");

        $methods = [];

        if (!empty($route)) {
            $pattern = $route->getPattern();

            foreach ($this->router->getRoutes() as $route) {
                if ($pattern === $route->getPattern()) {
                    $methods = array_merge_recursive($methods, $route->getMethods());
                }
            }
            //Methods holds all of the HTTP Verbs that a particular route handles.
        } else {
            $methods[] = $request->getMethod();
        }

        $response = $next($request, $response);
        $response->withHeader("Access-Control-Allow-Methods", implode(",", $methods));

        // send to client
        return $response;
    });
?>
