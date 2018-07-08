<?php
  use \Psr\Http\Message\ServerRequestInterface as Request;
  use \Psr\Http\Message\ResponseInterface as Response;

  function validateFields($data) {
    // Acquire data from form
    $user = $data['user'];
    $password = $data['password'];

    // If don't have user or password, call exception
    if ((!isset($user) ||
        !isset($password)) ||
        (empty($user) ||
        empty($password))) {
      // Call Exception
      throw new Exception("You need provide an user and password to login");
      exit;
    }

    // Encrypt password using key from env to compare on database
    $password = encryptPass($password);

    // Return data
    return array(
      'user' => $user,
      'password' => $password
    );
  }

  function makeJWT($data) {
    // Acquire data from database
    $id = $data['hash'];
    $user = $data['user'];
    $password = $data['password'];

    // Encrypt password again using key from env to write on JWT
    $password = encryptPass($password);

    // Config JWT encoder
    $headers = array(
      'alg' => 'HS256',
      'typ' => 'JWT'
    );

    // Content to serialize
    $payload = array(
      'info' => array(
        'id' => $id,
        'user' => $user,
        'password' => $password
      ),
      'sub' => $user,
      'iat' => time(),
      'exp' => time() + getenv('JWT_EXPIRATION_TIME')
    );

    // Secret key
    $key = getenv('HASH_JWT_KEY');

    // Create JWT
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
      // Get JWT info from middleware
      $jwt = $request->getAttribute('jwt');

      // Get request's content
      $data = $request->getParsedBody();

      // If user's already logged in
      if ($jwt['logged']) {
        // Call Exception
        throw new Exception('You already logged in!');
        exit;
      }

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

      // Validate fields, Verify credentials on database, and Make JWT
      $hash = makeJWT(
        verifyCredentials(
          validateFields(
            $data
          )
        )
      );

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
