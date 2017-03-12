<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * POST
    * CONTENT/SECTION
    * RECEIVE FORM DATA AND CREATE NEW ENTRY ON DATABASE
    * @route "/content/name"
    * @params {string} name THE SECTION NAME
    */
    $this->map(['POST', 'OPTIONS'], '/{name}', function (Request $request, Response $response, $params) {
        try {
            $data = null;

            // Get the JSON file with the characteristics of the section
            $path = realpath(__DIR__ . '/../../../relations/json/' . $params['name'] . '.json');

            // If the JSON file be found, open
            if ($path) {
                $get = file_get_contents($path);
            	$decode = json_decode($get, true);
                $json = $decode;
            } else {
                throw new Exception('File not found');
            }

            // Get request's content
            $data = $request->getParsedBody();

            // If content are valid, response to client
            // If not, call the Exception
            if (!empty($data)) {

                // Create a new empty entry
                $new_data = ORM::for_table($json['database']['table'])->create();

                // Iterate fields provided by JSON file
                for ($i=0; $i < count($json['fields']); $i++) {
                    // // Verifying if form passing something on the value
                    // if (isset($data[$json['fields'][$i]['id']])) {
                    //     // Verifying if validation is enabled on JSON file
                    //     if ($json['fields'][$i]['validation']['required']) {
                    //         // Verifying if has minimum
                    //         if (isset($json['fields'][$i]['validation']['length']['min'])) {
                    //             // If form data length is major than minimum
                    //             if (strlen($data[$json['fields'][$i]['id']]) > $json['fields'][$i]['validation']['length']['min']) {
                    //                 // Verifying if has maximum
                    //                 if ($json['fields'][$i]['validation']['length']['max']) {
                    //                     // If form data length is minor than maximum
                    //                     if (strlen($data[$json['fields'][$i]['id']]) <= $json['fields'][$i]['validation']['length']['max']) {
                    //                         // Write in database object
                    //                         $new_data->$json['fields'][$i]['id'] = $data[$json['fields'][$i]['id']];
                    //                     } else {
                    //                         // If form data length is major than maximum
                    //                         // throw new Exception($json['fields'][$i]['id'] . ": " . $json['fields'][$i]['validation']['length']['max'] . " chars maximum");
                    //                         throw new Exception($json['fields'][$i]['validation']['message']['default']);
                    //                     }
                    //                 } else {
                    //                     // Write in database object if has no maximum
                    //                     $new_data->$json['fields'][$i]['id'] = $data[$json['fields'][$i]['id']];
                    //                 }
                    //             } else {
                    //                 // If form data length is minor than minimum
                    //                 // throw new Exception($json['fields'][$i]['id'] . ": " . $json['fields'][$i]['validation']['length']['min'] . " chars minimum");
                    //                 throw new Exception($json['fields'][$i]['validation']['message']['default']);
                    //             }
                    //         } else {
                    //             // If validation is enabled but haven't information to compare values
                    //             throw new Exception("Validation field it's enabled but has not minimum value to compare data form. Verify your section's configuration and try again.");
                    //         }
                    //     } else {
                    //         // Write in database object if validation is not enabled
                    //         $new_data->$json['fields'][$i]['id'] = $data[$json['fields'][$i]['id']];
                    //     }
                    // }

                    if (!isset($data[$json['fields'][$i]['id']]) && $json['fields'][$i]['validation']['required']) {
                        // If has no value but field is required
                        throw new Exception($json['fields'][$i]['validation']['message']['default']);
                        exit;
                    }

                    if (!$json['fields'][$i]['public']) {
                        // If JSON file has not the requested key
                        throw new Exception("Can't save data in the field '" . $json['fields'][$i]['id'] . "', he's not public");
                        exit;
                    }

                    if (!$json['fields'][$i]['validation']['required']) {
                        // Write in database object if validation is not enabled
                        $new_data->$json['fields'][$i]['id'] = $data[$json['fields'][$i]['id']];

                        // return;
                    }

                    if ($json['fields'][$i]['validation']['required'] && !isset($json['fields'][$i]['validation']['length']['min'])) {
                        // If validation is enabled but haven't minimum to compare values
                        throw new Exception("Validation field it's enabled but has not minimum value to compare data form. Verify your section's configuration and try again.");
                    }

                    if (strlen($data[$json['fields'][$i]['id']]) < $json['fields'][$i]['validation']['length']['min']) {
                        // If form data length is minor than minimum
                        throw new Exception($json['fields'][$i]['validation']['message']['default']);
                    }

                    if (!isset($json['fields'][$i]['validation']['length']['max'])) {
                        // Write in database object if has no maximum
                        $new_data->$json['fields'][$i]['id'] = $data[$json['fields'][$i]['id']];

                        // return;
                    }

                    if (strlen($data[$json['fields'][$i]['id']]) <= $json['fields'][$i]['validation']['length']['max']) {
                        // Write in database object if form data length is minor than maximum
                        $new_data->$json['fields'][$i]['id'] = $data[$json['fields'][$i]['id']];

                        // return;
                    } else {
                        // If form data length is major than maximum
                        throw new Exception($json['fields'][$i]['validation']['message']['default']);
                    }
                }

                //set autoReleased
                if ($json['database']['autoReleased'] == true) {
                    $new_data->released = 1;
                } else {
                    $new_data->released = 0;
                }

                //set hidden
                $new_data->hidden = 0;

                // Finish database object and persist the data
                // $new_data->save();
                // Receiving information from the database
                $data = $new_data->as_array();
                // $data = $new_data;

                // // Get last entry
                // $new_data = ORM::for_table($json['database']['table'])->find_one($data['id']);
                // // Convert ID on MD5
                // $new_data->id_md5 = md5($data['id']);
                // // Finish database object and persist the data
                // $new_data->save();
                // // Receiving information from the database
                // $data = $new_data->as_array('id_md5');
                //
                // // Write response object
                // $data = array('md5' => $data['id_md5']);

                // Create response
                $response = $response->withJson($data, 201);
            } else {
                // Call Exception
                throw new Exception('Data form not found. Provide some data to save.');
                // throw new Exception(!empty($data));
            }
        } catch (Exception $e) {
            // Error message
            $data = array(
                'Error' => $e->getMessage()
            );

            // Create response
            $response = $response->withJson($data, 400);
        }

        // Response to client
        return $response;
    });
?>
