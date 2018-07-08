<?php
  /**
  * MIDDLEWARE to rewrite responses
  */
  $app->add(function ($request, $response, $next) {
    // Bypass the response
    $response = $next($request, $response);

    // Get body content
    $body = $response->getBody();

    // Rewrite JSON
    $response = $response->withJson(
      array_merge(
        json_decode($body,true),
        array('Status' => $response->getStatusCode())
      )
    );

    // send to client
    return $response;
  });

?>
