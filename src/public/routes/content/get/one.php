<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * GET
    * CONTENT/SECTION/PARAM/VALUE
    * MATCH PARAMETERS AND GET THE CONTENT ON THE DATABASE
    * @route "/content/section/param/value"
    * @params {string} section THE SECTION NAME
    * @params {string} param THE PARAM TO SEARCH IN DATABASE
    * @params {string} value THE VALUE OF THIS PARAM
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