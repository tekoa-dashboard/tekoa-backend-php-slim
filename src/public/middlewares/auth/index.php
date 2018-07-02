<?php
    /**
    * MIDDLEWARE to authenticate
    */
    $app->add(function ($request, $response, $next) {
        try {
            // Getting attributes
            $attributes = $request->getAttribute('routeInfo');

            // die(var_dump($attributes));
        } catch (Exception $e) {
            // Error message
            $data = array(
                'Error' => $e->getMessage()
            );
        }

        // Create response
        $request = $request->withAttribute('auth', $data);
        $response = $next($request, $response);

        // send to client
        return $response;
    });

?>
