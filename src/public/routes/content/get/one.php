<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    function findOne($json, $param, $value) {
        $data = array(
            'param' => $param,
            'value' => $value
        );

        return $data;
    }

    /**
    * GET
    * CONTENT/SECTION/PARAM/VALUE
    * MATCH PARAMETERS AND GET THE CONTENT ON THE DATABASE
    * @route "/content/section/param/value"
    * @params {string} section THE SECTION NAME
    * @params {string} param THE PARAM TO SEARCH IN DATABASE
    * @params {string} value THE VALUE OF THIS PARAM
    */
    $this->map(['GET', 'OPTIONS'], '/{section}/{param}/{value}', function (Request $request, Response $response) {
        try {
            // Get JSON from middleware
            $json = $request->getAttribute('jsonData');

            // Get param from route
            $param = $request->getAttribute('param');

            // Get value of the param from route
            $value = $request->getAttribute('value');

            // If content are valid, processing the response
            // If not, call the Exception
            if (!isset($json['Error'])) {
                // Find registry
                $data = findOne($json, $param, $value);

                $response = $response->withJson($data, 200);
                // $response = $response->withJson($json, 200);
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
