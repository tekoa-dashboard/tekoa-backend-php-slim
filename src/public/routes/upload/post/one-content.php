<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    /**
    * POST
    * UPLOAD/SECTION
    * RECEIVE FILE AND PERSIST ON DISK
    * @route "/upload/name"
    * @params {string} name THE SECTION NAME
    */
    $this->map(['POST', 'OPTIONS'], '/{name}', function (Request $request, Response $response, $params) {
        try {
            $data = null;
            $file = null;

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
            $file = $request->getUploadedFiles();
            $data = $request->getParsedBody();

            // If content are valid, response to client
            // If not, call the Exception
            if ($data != null && $file != null) {
                $file = $file['file']->file;
                $data = $data['name'];

                //Function to resize images that were sent
                function chageSize($target, $width){
                	$temp = imagecreatefromjpeg($target);
                	$x = imagesx($temp);
                	$y = imagesy($temp);
                	$height = ($width * $y)/$x;
                	$newImage = imagecreatetruecolor($width, $height);
                	imagecopyresampled($newImage, $temp, 0, 0, 0, 0, $width, $height, $x, $y);
                	imagejpeg($newImage, $target);
                	imagedestroy($temp);
                }

                //Proccess file and get the extension
                $extension = end((explode(".", $data)));
                //enable to include extension in final file
                $newName = md5($data + rand()). "." . $extension;

                //Set the folder to upload file
                $target = realpath(__DIR__ . '/../../../uploads');
                $target = $target . '/' . $newName;

                //Verify the extension, only can be uploaded JPG files
                if ($extension != "jpg") {
                	//Send error message
                	throw new Exception("You try send a " . $extension . " file, but only jpg it's accepted");
                } else {
                	//Upload file, resize and send success message
                	if (move_uploaded_file($file, $target)) {
                		chageSize($target, "1920");

                        $data = array('file' => $newName);
                	} else {
                        throw new Exception('Error sending you file, try again');
                    }
                }

                // Create response
                $response = $response->withJson($data, 201);
            } else {
                // Call Exception
                throw new Exception('Uploaded file not found');
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