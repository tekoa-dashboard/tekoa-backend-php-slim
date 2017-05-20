<?php
    /**
    * MIDDLEWARE to read JSON files
    */
    function getOneJSON($section) {
        // If section exists, try open JSON file
        if (isset($section)) {
            // Get the JSON file with the characteristics of the section
            $path = realpath(__DIR__ . getenv('RELATIONS_FOLDER') . $section . '.json');

            // If the JSON file be found, open
            if ($path) {
                $get = file_get_contents($path);
                $json = json_decode($get, true);

                return $json;
            }
        }

        return null;
    }

    function getAllJSONs() {
        // Get all JSON files
        $path = glob(__DIR__ . getenv('RELATIONS_FOLDER') . '*.json');

        // If the JSON file be found, open
        if ($path) {
            // Set $data as Array to recieve JSON's content
            $data = [];

            // Work on each file
            foreach($path as $section) {
                $section_filtered = basename($section, ".json");
                $section_regex = preg_replace('/(model)|(\.json)/is', "", $section_filtered);

                // if this element not match, put on array
                if($section_regex != ""){
                    $get = file_get_contents($section);
                    $json = json_decode($get, true);
                    $data[$section_filtered] = $json;
                }
            }

            return $data;
        }
    }

    $app->add(function ($request, $response, $next) {
        try {
            // Getting attributes
            $attributes = $request->getAttribute('routeInfo');

            if (isset($attributes[1]) && $attributes[1] == 'route0') {
                // Has no attributes
                // Create response
                $response = $next($request, $response);

                // send to client
                return $response;
            } else {
                // Counting attributes
                if (isset($attributes[2]) && count($attributes[2]) > 0) {
                    // Get all params
                    $params = $attributes[2];

                    // Get section param
                    $section = $params['section'];

                    // Get JSON data from section
                    $data = getOneJSON($section);

                    if ($data == null) {
                        throw new Exception("Section '" . $section . "' not found");
                    }
                } else {
                    // If has no params with section
                    // Get all JSON data
                    $data = getAllJSONs();

                    if ($data == null) {
                        throw new Exception("You not have sections to show");
                    }
                }
            }
        } catch (Exception $e) {
            // Error message
            $data = array(
                'Error' => $e->getMessage()
            );
        }

        // Create response
        $request = $request->withAttribute('jsonData', $data);
        $response = $next($request, $response);

        // send to client
        return $response;
    });

?>
