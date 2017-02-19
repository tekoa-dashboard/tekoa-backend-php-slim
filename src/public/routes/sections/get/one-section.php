<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * GET
    * SECTIONS/SECTION
    * MATCH PARAMETER WITH JSON FILE NAME AND READ THE CONTENT OF THIS SECTION
    * @route "/sections/name"
    * @params {string} name THE SECTION NAME
    */
    $this->map(['GET', 'OPTIONS'], '/{name}', function (Request $request, Response $response, $params) {
        try {
            $data = null;

            // Get the JSON file with the characteristics of the section
            $path = realpath(__DIR__ . '/../../../relations/json/' . $params['name'] . '.json');

            // If the JSON file be found, open
            if ($path) {
                $get = file_get_contents($path);
            	$json = json_decode($get, true);
                $data = $json;
            }

            // If content are valid, response to client
            // If not, call the Exception
            if ($data != null) {
                // Create response
                $response = $response->withJson($data, 200);
            } else {
                // Call Exception
                throw new Exception('File not found');
            }
        } catch (Exception $e) {
            // Error message
            $data = array(
                'Error' => $e->getMessage()
            );
            // Create response
            $response = $response->withJson($data, 400);
        }

        // Response to client
        return $response;
    });
?>
