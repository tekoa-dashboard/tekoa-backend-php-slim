<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * POST
    * CONTENT/SECTION
    * RECEIVE FORM DATA AND CREATE NEW ENTRY ON DATABASE
    * @route "/contents/name"
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
                throw new Exception('file not found');
            }

            $data = $request->getParsedBody();

            // If content are valid, response to client
            // If not, call the Exception
            if ($data != null) {

                $new_data = ORM::for_table($json['database']['table'])->create();

                for ($i=0; $i < count($json['fields']); $i++) {
                    if (isset($data[$json['fields'][$i]['id']])) {
                        $new_data->$json['fields'][$i]['id'] = $data[$json['fields'][$i]['id']];
                    }
                }

                $new_data->save();
                $data = $new_data->as_array();

                $new_data = ORM::for_table($json['database']['table'])->find_one($data['id']);
                $new_data->id_md5 = md5($data['id']);
                $new_data->save();
                $data = $new_data->as_array('id_md5');
                $data = array('md5' => $data['id_md5']);

                // Create response
                $response = $response->withJson($data, 201);
            } else {
                // Call Exception
                throw new Exception('data form not found');
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
