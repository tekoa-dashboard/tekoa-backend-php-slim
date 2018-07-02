<?php
    /**
    * MIDDLEWARE to enable CORS
    */
    require_once 'cors/index.php';

    /**
    * MIDDLEWARE to Access-Control-Allow-Methods
    */
    require_once 'allow-methods/index.php';

    /**
    * MIDDLEWARE to read JSON files
    */
    require_once 'json/index.php';

    /**
    * MIDDLEWARE to authenticate
    */
    require_once 'auth/index.php';
?>
