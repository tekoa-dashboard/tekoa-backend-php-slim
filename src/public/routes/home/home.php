<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * HOME
    * Route "/"
    * @param {}
    * Just a simple Hello World! It's works!
    */
    $app->get('/', function (Request $request, Response $response) {
        $data = array(
            'Home' => 'Welcome to Tekoá!',
            'Version' => '1.0',
            'Environment' => 'PHP',
            'Framework' => 'Slim',
            'Database' => 'MySQL'
        );

        $response = $response->withJson($data);

        return $response;
    });
?>
