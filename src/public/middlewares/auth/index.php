<?php
  /**
  * MIDDLEWARE to authenticate
  */
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
      // Return that user already logged in
      return array(
        'logged' => true
      );
    }
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
