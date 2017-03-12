<?php
    /**
    * MIDDLEWARE to read JSON files
    */
    $app->add(function ($request, $response, $next) {
        try {
            // Getting attributes
            if (!isset($request->getAttribute('routeInfo')[2])) {
                $response = $next($request, $response);
                return $response;
            }

            // Counting attributes
            if (count($request->getAttribute('routeInfo')[2]) > 0) {
                // If has attributes
                $params = $request->getAttribute('routeInfo')[2];
                $data = null;

                // If section exists, open JSON file
                if (isset($params['section'])) {
                    // Get the JSON file with the characteristics of the section
                    $path = realpath(__DIR__ . '/../../../config/relations/json/' . $params['section'] . '.json');

                    // If the JSON file be found, open
                    if ($path) {
                        $get = file_get_contents($path);
                        $json = json_decode($get, true);
                        $data = $json;
                    }
                }
            } else {
                // If has not attributes
                // Get all JSON files
            	$path = glob(__DIR__ . '/../../../config/relations/json/*.json');

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
            }

            // If content are valid, response to client
            // If not, call the Exception
            if ($data != null) {
                // Create response
                $request = $request->withAttribute('data', $data);
            } else {
                // Call Exception
                throw new Exception("Section '" . $params['section'] . "' not found");
            }
        } catch (Exception $e) {
            // Error message
            $data = array(
                'Error' => $e->getMessage()
            );

            // Create response
            $request = $request->withAttribute('data', $data);
        }

        $response = $next($request, $response);
        return $response;
    });

?>
