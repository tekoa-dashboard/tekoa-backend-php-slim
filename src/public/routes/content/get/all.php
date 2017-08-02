<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    function findAll($json, $section, $orderby, $ascdesc, $limit, $offset) {
        // Extract table name from database
        $table = $json['database']['table'];
        $fields = $json['fields'];
        $data = [];
        $raw = 'SELECT * FROM ' . $table . ' WHERE hidden = 0 ';

        // Create an raw query from database
        if ($orderby) {
            $raw .= ' ORDER BY ' . $orderby;
        }

        if ($ascdesc == 'asc') {
            $raw .= ' ASC ';
        } else if ($ascdesc == 'desc') {
            $raw .= ' DESC ';
        }

        if ($limit) {
            $raw .= ' LIMIT ' . $limit;
        }

        if ($offset) {
            $raw .= ' OFFSET ' . $offset;
        }

        // Execute the query
        $query = ORM::for_table($table)->raw_query($raw)->find_many()->as_array();

        // Iterate on result to remove the 'md5' and 'hidden' fields
        for ($i = 0; $i < count($query); $i++) {
            $result = $query[$i]->as_array();
            $md5 = $result['md5'];

            // Remove the 'md5' and the 'hidden' fields from results
            array_splice($result, array_search('md5', array_keys($result)), 1);
            array_splice($result, array_search('hidden', array_keys($result)), 1);

            // Change the 'id' content to 'md5'
            $result['id'] = $md5;

            // For each result, get the key name
            foreach (array_keys($result) as $res) {
                // For each field, verify if are public and create the value
                foreach ($fields as $field) {
                    if ($res == $field['id']) {
                        $public = $field['public'];

                        // If are private field, remove from results
                        if (!$public) {
                            array_splice($result, array_search($res, array_keys($result)), 1);
                        }
                    }
                }
            }

            $data[$i] = $result;
        }

        // Create the response
        $data = array(
            $section => array(
                'records' => count($data),
                'data' => $data
            )
        );

        return $data;
    }

    /**
    * GET
    * CONTENT/SECTION
    * LIST ALL CONTENT FROM THIS SECTION MATCHING PARAMETERS
    * @route "/content/section/orderby/ascdesc/limit/offset"
    * @params {string} section THE SECTION NAME
    * @params {string} orderBy ORDERY DATA BY
    * @params {string} ascDesc SET IF DATA IS ASCENDING OR DESCENDING
    * @params {string} limit RESULTS LIMIT
    * @params {string} offset RESULTS OFFSET
    */
    $this->map(['GET', 'OPTIONS'], '/{section}/all[/{orderby}[/{ascdesc}[/{limit}[/{offset}]]]]', function (Request $request, Response $response) {
        try {
            // Get JSON from middleware
            $json = $request->getAttribute('jsonData');

            // Get section from route
            $section = $request->getAttribute('section');

            // Get orderBy from route
            $orderby = $request->getAttribute('orderby');

            // Get ascending or descending param from route
            $ascdesc = $request->getAttribute('ascdesc');

            // Get limit from route
            $limit = $request->getAttribute('limit');

            // Get offset from route
            $offset = $request->getAttribute('offset');

            // If content are valid, processing the response
            // If not, call the Exception
            if (!isset($json['Error'])) {
                // Find registry
                $data = findAll($json, $section, $orderby, $ascdesc, $limit, $offset);

                $response = $response->withJson($data, 200);
            } else {
                // Call Exception
                throw new Exception($json['Error']);
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
