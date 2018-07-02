<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * POST
    * AUTH/LOGIN
    * AUTHENTICATE USER
    * @route "/auth/login"
    * @params {string} user THE USER NAME OR ANOTHER FIELD DEFINED ON SETTINGS
    * @params {string} password USER PASSWORD
    */
    $this->map(['POST', 'OPTIONS'], '/login', function (Request $request, Response $response) {
        try {
              // Get request's content
              $data = $request->getParsedBody();

              // If content are valid, send to client
              // If not, call the Exception
              if (!empty($data)) {
                  // Make Login
                  $hash = makeLogin();

                  // Write response object
                  $data = array('Success' => $hash['hash']);

                  // Create response
                  $response = $response->withJson($data, 201);
              } else {
                  // Call Exception
                  throw new Exception('Data form not found. Provide some data to login.');
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
