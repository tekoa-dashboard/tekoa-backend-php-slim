<?php
    /**
    * REQUIRE composer
    * DOCS: https://getcomposer.org/
    */
    require_once '../../vendor/autoload.php';

    /**
    * REQUIRE .env file and all app configurations
    */
    require_once '../config/index.php';

    /**
    * SET Slim Framework
    * DOCS: https://www.slimframework.com
    */
    $app = new \Slim\App(["settings" => $config]);
    $container = $app->getContainer();

    /**
    * REQUIRE Idiorm and set DB configurations
    */
    require_once '../config/db-connection.php';

    /**
    * REQUIRE all routes
    */
    require_once 'routes/index.php';

    $app->run();
?>
