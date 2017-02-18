<?php
    /**
    * GET
    * HOME
    * JUST A SIMPLE HELLO WORLD! IT'S WORKS!
    * @route "/"
    * @params {}
    */
    require_once 'home/home.php';

    /**
    * GROUP to /sections
    */
    $app->group('/sections', function () {
        /**
        * GET
        * SECTIONS
        * LIST ALL SECTIONS
        * @route "/sections"
        * @params {}
        */
        require_once 'sections/get/all-sections.php';

        /**
        * GET
        * SECTIONS/SECTION
        * MATCH PARAMETER WITH JSON FILE NAME AND READ THE CONTENT OF THIS SECTION
        * @route "/sections/name"
        * @params {string} name THE SECTION NAME
        */
        require_once 'sections/get/one-section.php';
    });

    /**
    * GROUP to /content
    */
    $app->group('/content', function () {
        /**
        * POST
        * CONTENT/SECTION
        * RECEIVE FORM DATA AND CREATE NEW ENTRY ON DATABASE
        * @route "/contents/name"
        * @params {string} name THE SECTION NAME
        */
        require_once 'content/post/one-content.php';
    });
?>
