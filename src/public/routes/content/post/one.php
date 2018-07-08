<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    function validationCase($validation, $data, $defaultMessage) {
        switch ($validation['type']) {
            case 'integer':
                if (((intval($data) < $validation['range']['min']) ||
                    (intval($data) > $validation['range']['max'])) &&
                    intval($validation['range']['max']) != 0) {
                    throw new Exception($defaultMessage);
                    exit;
                }
                break;

            case 'string':
                if (((strlen($data) < $validation['length']['min']) ||
                    (strlen($data) > $validation['length']['max'])) &&
                    intval($validation['length']['max']) != 0) {
                    throw new Exception($defaultMessage);
                    exit;
                }
                break;

            case 'boolean':
                if ($data != var_export($validation['options']['right'], 1)) {
                    throw new Exception($defaultMessage);
                    exit;
                }
                break;
        }
    }

    function iterateData($field, $data) {
        $id = $field['id'];
        $validation = $field['validation'];
        $defaultMessage = $validation['message']['default'];

        if (isset($data[$id]) && !$field['public']) {
            // If the field has value, but is not public
            throw new Exception("Can't save data in the field '" . $id . "', he's not public");
            exit;
        }

        if (!isset($data[$id]) && $validation['required']) {
            // If has no value but field is required
            throw new Exception($defaultMessage);
            exit;
        }

        if (!isset($data[$id])) {
            // If has no value
            return;
        }

        if ($validation['required']) {
            validationCase($validation, $data[$id], $defaultMessage);
        }

        // If all validations are ok, write a new value
        return $data[$id];
    }

    function ingestingData($json, $data) {
        $database = $json['database'];
        $table = $database['table'];

        // Create a new empty entry
        $newData = ORM::for_table($table)->create();

        // Iterate and mix data with JSON schema
        // Iterate fields provided by JSON file
        for ($i=0; $i < count($json['fields']); $i++) {
            $field = $json['fields'][$i];
            $id = $field['id'];
            // If all validations are ok, write a new value
            $newData->$id = iterateData($field, $data);
        }

        // Set auto release on field
        if ($database['autoReleased'] == true) {
            $newData->released = 1;
        } else {
            $newData->released = 0;
        }

        // Set hidden
        $newData->hidden = 0;

        // Finish database object and persist the data
        $newData->save();

        // Receiving information from the database
        return $newData->as_array();
    }

    function makeHash($json, $data) {
        $database = $json['database'];
        $hashKey = $database['hashKey'];
        $table = $database['table'];

        // Get last entry
        $newData = ORM::for_table($table)->find_one($data[$hashKey]);

        // Convert value on Hash
        $newData->hash = hash_hmac('sha256', $data[$hashKey] . time(), getenv('HASH_DATA_KEY'));

        // Finish database object and persist the data
        $newData->save();

        // Receiving information from the database
        return $newData->as_array('hash');
    }

    /**
    * POST
    * CONTENT/SECTION
    * RECEIVE FORM DATA AND CREATE NEW ENTRY ON DATABASE
    * @route "/content/section"
    * @params {string} section THE SECTION NAME
    */
    $this->map(['POST', 'OPTIONS'], '/{section}', function (Request $request, Response $response) {
        try {
            // Get JSON from middleware
            $json = $request->getAttribute('jsonData');

            // If content are valid, send to client
            // If not, call the Exception
            if (!isset($json['Error'])) {
                // Get request's content
                $data = $request->getParsedBody();

                // If content are valid, send to client
                // If not, call the Exception
                if (!empty($data)) {
                    // Ingesting data
                    $data = ingestingData($json, $data);

                    // Make Hash
                    $hash = makeHash($json, $data);

                    // Write response object
                    $data = array('Success' => $hash['hash']);

                    // Create response
                    $response = $response->withJson($data, 201);
                } else {
                    // Call Exception
                    throw new Exception('Data form not found. Provide some data to save.');
                    exit;
                }
            } else {
                // Call Exception
                throw new Exception($json['Error']);
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
