<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * GET
    * SECTIONS
    * LIST ALL SECTIONS
    * @route "/sections"
    * @params {}
    */
    $this->map(['GET', 'OPTIONS'], '', function (Request $request, Response $response, $params) {
        try {
            $data = null;

            // Get all JSON files
        	$path = glob(__DIR__ . '/../../../../config/relations/json/*.json');

            // If the JSON file be found, open
            if ($path) {
                // Set $data as Array to recieve JSON's content
                $data = [];

            	// Work on each file
            	foreach($path as $section) {
                    $section_filtered = basename($section, ".json");
                    $section_regex = preg_replace('/(model)|(\.json)/is', "", $section_filtered);

                    if($section_regex != ""){
            			$get = file_get_contents($section);
            			$json = json_decode($get, true);
                        $data[$section_filtered] = $json;
                    }
            	}
            }

            // If content are valid, response to client
            // If not, call the Exception
            if ($data != null) {
                // Create response
                $response = $response->withJson($data, 200);
            } else {
                // Call Exception
                throw new Exception('File not found');
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
