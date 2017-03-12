<?php
    /**
    * CONFIG Slim Framework
    * DOCS: https://www.slimframework.com
    */
    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;
    $config['determineRouteBeforeAppMiddleware'] = true;

    /**
    * SET Slim Framework
    */
    $app = new \Slim\App(["settings" => $config]);
    $container = $app->getContainer();
?>
