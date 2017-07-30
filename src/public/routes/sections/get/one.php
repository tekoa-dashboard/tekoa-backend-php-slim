<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * GET
    * SECTIONS/SECTION
    * MATCH PARAMETER WITH JSON FILE NAME AND READ THE CONTENT OF THIS SECTION
    * @route "/sections/section"
    * @params {string} section THE SECTION NAME
    */
    $this->map(['GET', 'OPTIONS'], '/{section}', function (Request $request, Response $response) {
        try {
            // Get JSON from middleware
            $json = $request->getAttribute('jsonData');

            // If content are valid, send to client
            // If not, call the Exception
            if (!isset($json['Error'])) {
                // Create response
                $response = $response->withJson($json, 200);
            } else {
                // Call Exception
                throw new Exception($json['Error']);
            }
        } catch (Exception $e) {
            // Error message
            $data = array(
                'Error' => $e->getMessage()
            );

            // Create response
            $response = $response->withJson($data, 400);
        }

        // send to client
        return $response;
    });
?>
