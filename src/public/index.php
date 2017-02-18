<?php
    /**
    * REQUIRE composer
    */
    require '../../vendor/autoload.php';

    /**
    * REQUIRE .env file and all app configurations
    */
    require '../config/index.php';

    $app = new \Slim\App(["settings" => $config]);
    $container = $app->getContainer();

    /**
    * REQUIRE PDO container and set DB configurations
    */
    require 'containers/pdo.php';

    /**
    * REQUIRE all routes
    */
    require 'routes/index.php';

    $app->run();
?>
