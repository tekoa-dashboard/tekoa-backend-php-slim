<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * GET
    * SECTIONS
    * LIST ALL SECTIONS
    * @route "/sections"
    * @params {}
    */
    $this->map(['GET', 'OPTIONS'], '', function (Request $request, Response $response, $params) {
        try {
            // Get JSON from middleware
            $json = $request->getAttribute('data');

            // If content are valid, response to client
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

        // Response to client
        return $response;
    });
?>
