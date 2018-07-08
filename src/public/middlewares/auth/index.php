<?php
  /**
  * MIDDLEWARE to authenticate
  */
  function validateToken($token) {
    // JWS encoded string
    $token = sscanf($token[0], 'Bearer %s')[0];

    if (count($token) > 0) {
      // Secret key
      $key = getenv('HASH_KEY');

      try {
        // Decode token
        $jws = new \Gamegos\JWS\JWS();
        $jwtContent = $jws->verify($token, $key);
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

      if ($jwtContent['payload']['iat'] &&
          $jwtContent['payload']['iat'] > time() - 3600) {
        // Return that user already logged in
        return array(
          'logged' => true
        );
      }
    }
  }

  $app->add(function ($request, $response, $next) {
    // Get header
    $auth = $request->getHeader('Authorization');

    try {
      // Verify token
      $data = validateToken($auth);

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
