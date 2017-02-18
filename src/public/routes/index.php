<?php
    /**
    * HOME
    * Route "/"
    * @params {}
    */
    require_once 'home/home.php';

    /**
    * GET
    * SECTIONS
    * LIST ALL SECTIONS
    * @route "/sections"
    * @params {}
    */
    require_once 'sections/get-all-sections.php';

    /**
    * GET
    * SECTIONS/SECTION
    * MATCH PARAMETER WITH JSON FILE NAME AND READ THE CONTENT OF THIS SECTION
    * @route "/sections/name"
    * @params {string} id THE SECTION NAME
    */
    require_once 'sections/get-one-section.php';
?>
