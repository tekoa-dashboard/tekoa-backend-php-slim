<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    function findAll($json, $orderby, $ascdesc, $limit, $offset) {
        // Extract table name from database
        $table = $json['database']['table'];
        $fields = $json['fields'];
        $data = [];
        $raw = 'SELECT * FROM ' . $table;

        // // Querying from database
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

        $query = ORM::for_table($table)->raw_query($raw)->find_many()->as_array();

        // // Set the id value from query result
        // $data['id'] = $query['md5']
        //     ? $query['md5']
        //     : $query['id'];
        //
        // For each field, verify if are public and create the value
        for ($i = 0; $i < $query; $i++) {
            $data[$query[i]] = $query[i];
        }

        $data = array(
            'orderby' => $orderby,
            'ascdesc' => $ascdesc,
            'limit' => $limit,
            'offset' => $offset,
            'data' => $data
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
