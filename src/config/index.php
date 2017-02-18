<?php
    /**
    * CONFIG DotEnv Composer Package
    * DOCS: https://github.com/vlucas/phpdotenv
    */
    $dotenv = new Dotenv\Dotenv(__DIR__);
    $dotenv->load();

    /**
    * CONFIG Slim
    */
    $config['displayErrorDetails'] = true;
    $config['addContentLengthHeader'] = false;
    $config['determineRouteBeforeAppMiddleware'] = true;
?>
