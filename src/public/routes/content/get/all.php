<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    function findAll($json, $orderby, $ascdesc, $limit, $offset) {
        // Extract table name from database
        $table = $json['database']['table'];
        $fields = $json['fields'];
        $data = [];

        // Querying from database
        $query = ORM::for_table($table)
            ->where_equal($param, $value)
            ->find_one();

        // If query result something, transform in array
        // else, just print the 'null' result
        $query
            ? $query->as_array()
            : $query;

        // Set the id value from query result
        $data['id'] = $query['md5']
            ? $query['md5']
            : $query['id'];

        // For each field, verify if are public and create the value
        foreach ($fields as $key) {
            $public = $key['public'];

            if ($public) {
                $data[$key['id']] = $query[$key['id']];
            }
        }


        $data = array(
            'orderby' => $orderby,
            'ascdesc' => $ascdesc,
            'limit' => $limit,
            'offset' => $offset,
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
    $this->map(['GET', 'OPTIONS'], '/{section}/all[/{orderby}/{ascdesc}/{limit}/{offset}]', function (Request $request, Response $response) {
        try {
            // Get JSON from middleware
            $json = $request->getAttribute('jsonData');

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
                $data = findAll($json, $orderby, $ascdesc, $limit, $offset);

                $response = $response->withJson($data, 200);
                // $response = $response->withJson($json, 200);
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
