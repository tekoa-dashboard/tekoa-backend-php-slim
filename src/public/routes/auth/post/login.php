<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    function makeLogin($data) {
      $user = $data['user'];
      $password = $data['password'];

      if ((!isset($user) ||
          !isset($password)) ||
          (empty($user) ||
          empty($password))) {
        throw new Exception("You need provide an user and password to login");
        exit;
      }

      // Config JWT encoder
      $headers = array(
        'alg' => 'HS256',
        'typ' => 'JWT'
      );

      // Content to serialize
      $payload = array(
        'sub' => $user,
        'iat' => time()
      );

      // Secret key
      $key = getenv('HASH_KEY');

      $jws = new \Gamegos\JWS\JWS();
      $hash = $jws->encode($headers, $payload, $key);

      return $hash;
    }

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
          $jwt = $request->getAttribute('jwt');

          // Get request's content
          $data = $request->getParsedBody();

          // If middleware return some error, call the Exception
          if (isset($jwt['Error'])) {
            // Call Exception
            throw new Exception($jwt['Error']);
            exit;
          }

          // If have no data, call the Exception
          if (empty($data)) {
            // Call Exception
            throw new Exception('Data form not found. Provide some data to login.');
            exit;
          }

          // Make Login
          $hash = makeLogin($data);

          if ($hash) {
            // Write response object
            $data = array('Success' => $hash);

            // Create response
            $response = $response->withJson($data, 201);
          } else {
            // Call Exception
            throw new Exception('Was not possible to make login. Try again.');
            exit;
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
