<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    function findMe() {
    }

    /**
    * GET
    * AUTH/ME
    * LIST USER INFO
    * @route "/auth/me"
    * @params {}
    * @header {string} Authorization BEARER JWT IS REQUIRED
    */
    $this->map(['GET', 'OPTIONS'], '/me', function (Request $request, Response $response) {
        try {
            // Get value of the param from route
            $value = $request->getAttribute('value');

            // If content are valid, processing the response
            // If not, call the Exception
            if (!isset($json['Error'])) {
                // Find registry
                $data = findMe();

                $response = $response->withJson($data, 200);
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
