<?php
    /**
    * CONFIG Idiorm Composer Package
    * DOCS: http://j4mie.github.io/idiormandparis/
    */
    ORM::configure(array(
        'connection_string' => 'mysql:host=' . getenv('DB_HOST') . ';dbname=' . getenv('DB_DBNAME'),
        'username' => getenv('DB_USER'),
        'password' => getenv('DB_PASS')
    ));
    ORM::configure('error_mode', PDO::ERRMODE_WARNING);
    ORM::configure('logger', function($log_string, $query_time) {
        echo $log_string . ' in ' . $query_time;
    });
?>
