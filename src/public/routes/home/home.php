<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * GET
    * HOME
    * JUST A SIMPLE HELLO WORLD! IT'S WORKS!
    * @route "/"
    * @params {}
    */
    $app->get('/', function (Request $request, Response $response) {
        $data = array(
            'Home' => 'Welcome to Tekoá!',
            'Version' => '1.0',
            'Environment' => 'PHP',
            'Framework' => 'Slim',
            'Database' => 'MySQL'
        );

        $response = $response->withJson($data, 200);

        return $response;
    });
?>
