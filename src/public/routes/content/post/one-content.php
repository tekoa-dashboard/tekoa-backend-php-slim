<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * POST
    * CONTENT/SECTION
    * RECEIVE FORM DATA AND CREATE NEW ENTRY ON DATABASE
    * @route "/content/section"
    * @params {string} section THE SECTION NAME
    */
    $this->map(['POST', 'OPTIONS'], '/{section}', function (Request $request, Response $response, $params) {
        try {
            // Get JSON from middleware
            $json = $request->getAttribute('jsonData');

            // If content are valid, send to client
            // If not, call the Exception
            if (!isset($json['Error'])) {
                // Create response
                // Get request's content
                $data = $request->getParsedBody();

                // If content are valid, send to client
                // If not, call the Exception
                if (!empty($data)) {
                    // Create a new empty entry
                    $new_data = ORM::for_table($json['database']['table'])->create();

                    // Iterate fields provided by JSON file
                    for ($i=0; $i < count($json['fields']); $i++) {
                        if (isset($data[$json['fields'][$i]['id']]) && !$json['fields'][$i]['public']) {
                            // If the field has value, but is not public
                            throw new Exception("Can't save data in the field '" . $json['fields'][$i]['id'] . "', he's not public");
                            exit;
                        }

                        if (!isset($data[$json['fields'][$i]['id']]) && $json['fields'][$i]['validation']['required']) {
                            // If has no value but field is required
                            throw new Exception($json['fields'][$i]['validation']['message']['default']);
                            exit;
                        }

                        if (!isset($data[$json['fields'][$i]['id']])) {
                            // If has no value
                            continue;
                        }

                        if ($json['fields'][$i]['validation']['required']) {
                            switch ($json['fields'][$i]['validation']['type']) {
                                case 'integer':
                                    if (((intval($data[$json['fields'][$i]['id']]) < $json['fields'][$i]['validation']['range']['min']) || (intval($data[$json['fields'][$i]['id']]) > $json['fields'][$i]['validation']['range']['max'])) && intval($json['fields'][$i]['validation']['range']['max']) != 0) {
                                        throw new Exception($json['fields'][$i]['validation']['message']['default']);
                                        exit;
                                    }
                                    break;

                                case 'string':
                                    if (((strlen($data[$json['fields'][$i]['id']]) < $json['fields'][$i]['validation']['length']['min']) || (strlen($data[$json['fields'][$i]['id']]) > $json['fields'][$i]['validation']['length']['max'])) && intval($json['fields'][$i]['validation']['length']['max']) != 0) {
                                        throw new Exception($json['fields'][$i]['validation']['message']['default']);
                                        exit;
                                    }
                                    break;

                                case 'boolean':
                                    if ($data[$json['fields'][$i]['id']] != var_export($json['fields'][$i]['validation']['options']['right'], 1)) {
                                        throw new Exception($json['fields'][$i]['validation']['message']['default']);
                                        exit;
                                    }
                                    break;
                            }
                        }

                        // If all validations are ok, write a new value
                        $new_data->$json['fields'][$i]['id'] = $data[$json['fields'][$i]['id']];
                    }

                    //get autoReleased
                    if ($json['database']['autoReleased'] == true) {
                        $new_data->released = 1;
                    } else {
                        $new_data->released = 0;
                    }

                    //set hidden
                    $new_data->hidden = 0;

                    // Finish database object and persist the data
                    $new_data->save();
                    // Receiving information from the database
                    $data = $new_data->as_array();

                    // Get last entry
                    $new_data = ORM::for_table($json['database']['table'])->find_one($data['id']);
                    // Convert ID on MD5
                    $new_data->id_md5 = md5($data['id']);
                    // Finish database object and persist the data
                    $new_data->save();
                    // Receiving information from the database
                    $data = $new_data->as_array('id_md5');

                    // Write response object
                    $data = array('md5' => $data['id_md5']);

                    // Create response
                    $response = $response->withJson($data, 201);
                } else {
                    // Call Exception
                    throw new Exception('Data form not found. Provide some data to save.');
                    // throw new Exception(!empty($data));
                }
            } else {
                // Call Exception
                throw new Exception($data['Error']);
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
