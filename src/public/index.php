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
    * REQUIRE all middlewares
    */
    require_once 'middlewares/index.php';

    /**
    * REQUIRE all routes
    */
    require_once 'routes/index.php';

    $app->run();
?>
