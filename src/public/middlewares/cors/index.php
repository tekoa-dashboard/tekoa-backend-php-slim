<?php
    /**
    * MIDDLEWARE to enable CORS
    */
    $app->add(function ($request, $response, $next) {
        $response = $next($request, $response);
        return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization');
    });

?>
