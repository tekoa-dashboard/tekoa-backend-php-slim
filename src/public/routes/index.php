<?php
    /**
    * GET
    * HOME
    * JUST A SIMPLE HELLO TEKOÁ! IT'S WORKS!
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
        * @route "/sections/"
        * @params {}
        */
        require_once 'sections/get/all.php';

        /**
        * GET
        * SECTIONS/SECTION
        * MATCH PARAMETER WITH JSON FILE NAME AND READ THE CONTENT OF THIS SECTION
        * @route "/sections/section"
        * @params {string} section THE SECTION NAME
        */
        require_once 'sections/get/one.php';
    });

    /**
    * GROUP to /content
    */
    $app->group('/content', function () {
        /**
        * POST
        * CONTENT/SECTION
        * RECEIVE FORM DATA AND CREATE NEW ENTRY ON DATABASE
        * @route "/content/section"
        * @params {string} section THE SECTION NAME
        */
        require_once 'content/post/one.php';

        /**
        * GET
        * CONTENT/SECTION
        * LIST ALL CONTENT FROM THIS SECTION MATCHING PARAMETERS
        * @route "/content/section/orderby/ascdesc/limit/offset"
        * @params {string} section THE SECTION NAME
        * @params {string} orderBy ORDERY DATA BY
        * @params {string} ascDesc SET IF DATA IS ASCENDING OR DESCENDING
        * @params {string} limit RESULTS LIMIT
        * @params {string} offset RESULTS OFFSET
        */
        require_once 'content/get/all.php';

        /**
        * GET
        * CONTENT/SECTION/PARAM/VALUE
        * MATCH PARAMETERS AND GET THE CONTENT ON THE DATABASE
        * @route "/content/section/param/value"
        * @params {string} section THE SECTION NAME
        * @params {string} param THE PARAM TO SEARCH IN DATABASE
        * @params {string} value THE VALUE OF THIS PARAM
        */
        require_once 'content/get/one.php';
    });

    /**
    * GROUP to /upload/
    */
    $app->group('/upload', function () {
        /**
        * POST
        * UPLOAD/SECTION
        * RECEIVE FILE AND PERSIST ON DISK
        * @route "/upload/"
        * @params {string} name THE SECTION NAME
        */
        require_once 'upload/post/one.php';
    });
?>
