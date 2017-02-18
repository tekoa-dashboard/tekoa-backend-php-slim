<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    $app->get('/hello/{name}', function (Request $request, Response $response) {
        $name = $request->getAttribute('name');
        $data = array('name' => 'Bob', 'age' => 40);
        $response = $response->withJson($data);

        return $response; 
    });
?>
