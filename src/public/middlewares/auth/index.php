<?php
  /**
  * MIDDLEWARE to authenticate
  */
  function encryptPass($password) {
    // Encrypt password usign env content key
    return hash_hmac('sha256', $password, getenv('HASH_CONTENT_KEY'));
  }

  function getToken($header) {
    // Found JWS encoded string from header
    return sscanf($header, 'Bearer %s')[0];
  }

  function isLogged($jwt) {
    // If don't have token, user are not logged in
    if (count($jwt) == 0 ||
        !isset($jwt) ||
        empty($jwt)) {
      return array(
        'logged' => false
      );
    }

    // If have payload, verify the content and calc the expiration time
    if ($jwt['payload']['iat'] &&
        $jwt['payload']['exp'] &&
        $jwt['payload']['exp'] > time()) {

      $verifyCredentials = verifyCredentials($jwt['payload']['info']);
      // Return that user already logged in
      return array(
        'logged' => true
      );
    }
  }

  function verifyCredentials($data) {
    // Acquire data from payload
    $id = $data['id'];
    $user = $data['user'];
    $password = $data['password'];

    // Querying from database
    $query = ORM::for_table(
      getenv('DB_USERS_TABLE')
    );

    // If have ID, password it's not necessary now
    if (isset($id) &&
        !empty($id)) {
      $query->where(
        array(
          'hash' => $id,
          getenv('DB_USER_DEFAULT_FIELD') => $user
        )
      );
    } else {
      $query->where(
        array(
          getenv('DB_USER_DEFAULT_FIELD') => $user,
          'password' => $password
        )
      );
    }
    $query->find_one();

    if (!$query ||
        (isset($id) &&
        !empty($id) &&
        encryptPass($query['password']) !== $password)) {
      // Call Exception
      throw new Exception("User or password are incorrect. Is not possible make login now, verify the information provided and try again.");
      exit;
    }

    // If query result something, transform in array
    // else, just return the 'null' result
    return $query
      ? $query->as_array()
      : $query;
  }

  function validateToken($token) {
    // Validate if don't have token
    if (empty($token) ||
        !isset($token) ||
        empty($token)) {
      return;
    }

    // Secret key
    $key = getenv('HASH_JWT_KEY');

    try {
      // Decode token
      $jws = new \Gamegos\JWS\JWS();
      $jwt = $jws->verify($token, $key);
    } catch (Exception $e) {
      // TODO: Improve this errors messages
      switch ($e) {
        case $e instanceof Gamegos\JWS\Exception\InvalidSignatureException:
          // Call Exception
          throw new Exception($e->getMessage());
          exit;
          break;

        case $e instanceof Gamegos\JWS\Exception\MalformedSignatureException:
          // Call Exception
          throw new Exception($e->getMessage());
          exit;
          break;

        case $e instanceof Gamegos\JWS\Exception\UnspecifiedAlgorithmException:
          // Call Exception
          throw new Exception($e->getMessage());
          exit;
          break;

        case $e instanceof Gamegos\JWS\Exception\UnsupportedAlgorithmException:
          // Call Exception
          throw new Exception($e->getMessage());
          exit;
          break;

        default:
          // Call Exception
          throw new Exception('Was impossible check your token and provide your authentication. Try again.');
          exit;
          break;
      }
    }

    return $jwt;
  }

  $app->add(function ($request, $response, $next) {
    // Get header
    $auth = $request->getHeader('Authorization')[0];

    try {
      // Verify token
      $data = isLogged(
        validateToken(
          getToken(
            $auth
          )
        )
      );

    } catch (Exception $e) {
      // Error message
      $data = array(
        'Error' => $e->getMessage()
      );
    }

    // Create response
    $request = $request->withAttribute('jwt', $data);
    $response = $next($request, $response);

    // send to client
    return $response;
  });

?>
