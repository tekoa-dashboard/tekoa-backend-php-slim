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
      return $jws->encode($headers, $payload, $key);
      // die(var_dump($jws));
    }

    function validateCookie($cookie) {
      if (count($cookie) > 0 &&
          (!empty($_COOKIE['tekoá']) ||
            isset($_COOKIE['tekoá']))) {
        // Secret key
        $key = getenv('HASH_KEY');

        //jws encoded string
        $jwsString = $_COOKIE['tekoá'];

        $jws = new \Gamegos\JWS\JWS();
        $teste = $jws->verify($jwsString, $key);

        die(var_dump($teste));
        // Call Exception
        throw new Exception('You already logged');
        exit;
      }

      return true;
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
          // Verify cookie
          validateCookie($request->getHeader('Cookie'));

          // Get request's content
          $data = $request->getParsedBody();

          // If content are valid, send to client
          // If not, call the Exception
          if (!empty($data)) {
              // Make Login
              $hash = makeLogin($data);

              if ($hash) {
                // Write response object
                $data = array('Success' => $hash);

                // Set cookie on client
                setcookie('tekoá', $hash, time()+3600);

                // Create response
                $response = $response->withJson($data, 201);
              } else {
                // Call Exception
                throw new Exception('Was not possible to make login. Try again.');
                exit;
              }
          } else {
              // Call Exception
              throw new Exception('Data form not found. Provide some data to login.');
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
