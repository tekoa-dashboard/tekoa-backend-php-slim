<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    function manipulateImage($state, $type, $target, $newImage) {
        // Get compression value from .env
        $newQuality = getenv('UPLOADS_IMAGE_COMPRESSION');

        // Fix JPEG extension and choose the correct function to work on image
        if ($type == 'jpeg') $type = 'jpg';
        switch ($type) {
            case 'bmp':
                if ($state == 'blank')
                    return imagecreatefrombmp($target);
                else if ($state == 'finalize')
                    return imagewbmp($newImage, $target);

                break;

            case 'gif':
                if ($state == 'blank')
                    return imagecreatefromgif($target);
                else if ($state == 'finalize')
                    return imagegif($newImage, $target);

                break;

            case 'jpg':
                if ($state == 'blank')
                    return imagecreatefromjpeg($target);
                else if ($state == 'finalize') {
                    if (!$newQuality) $newQuality = 100;

                    return imagejpeg($newImage, $target, $newQuality);
                }

                break;

            case 'png':
                if ($state == 'blank')
                    return imagecreatefrompng($target);
                else if ($state == 'finalize') {
                    $newQuality = round($newQuality / 10);
                    $newQuality = 10 ? 9 : $newQuality;

                    return imagepng($newImage, $target, $newQuality);
                }

                break;
        }
    }

    function chageSize($target, $type, $newWidth){
        // Create an temporary blank container image
        $temp = manipulateImage('blank', $type, $target, '');

        // Get sizes anchors
        list($x, $y) = getimagesize($target);

        // Make the proportion size
        $newHeight = ($newWidth * $y) / $x;

        // Create a new image on memory
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Resize the new image
        imagecopyresampled($newImage, $temp, 0, 0, 0, 0, $newWidth, $newHeight, $x, $y);

        // Finalize the proccess and create real file
        manipulateImage('finalize', $type, $target, $newImage);

        // Destroy the temporary image
        imagedestroy($temp);
    }

    function getExtension($data) {
        // Proccess file and get the extension
        $extension = end((explode('.', strtolower($data))));

        // Get flag to able or not the extentions validation from .env
        $validation = getenv('UPLOADS_VALIDATE_EXTENSION');

        // Get list of valid extensions from .env
        $fileTypesEnv = strtolower(getenv('UPLOADS_EXTENSIONS_AVAILABLE'));

        // Creat an array with this list
        $fileTypes = explode(',', strval($fileTypesEnv));

        // Verify if JPEG extension already inside of list, if not, put in there
        $jpeg = array_search('jpeg', $fileTypes);
        if (empty($jpeg) && $jpeg !== 0) {
            array_push($fileTypes,'jpeg');
        }

        if ($validation == 'true') {
            // If validation is enabled, verify the list of file types
            if (count($fileTypes) > 0) {
                $type = array_search($extension, $fileTypes);

                // If extension not match with the list
                if (empty($type) && $type !== 0) {
                    // Send error message
                    throw new Exception("You try send a '." . $extension . "' file, but only '." . implode("' or '.", $fileTypes) . "' are supported");
                    return;
                }
            }
        }

        // Return the real extension
        return $extension;
    }

    function setNewName($data) {
        // Create a new name to file based on md5 and a random number
        $newName = md5($data + rand()). '.' . getExtension($data);

        return $newName;
    }

    function uploadFile($file, $newName) {
        // Set the folder to upload file
        $target = realpath(__DIR__ . '/../../../' . getenv('UPLOADS_FOLDER'));

        // Concatenate path with the new name
        $target = $target . '/' . $newName;

        // Upload file
        if (move_uploaded_file($file, $target)) {
            // Get new image width from .env
            $newWidth = getenv('UPLOADS_IMAGE_RESIZE_WIDTH');

            if ($newWidth) {
                // Get the actual extention
                $extension = getExtension($newName);

                // List the supported by PHP images extions
                $imagesSupported = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                $type = array_search($extension, $imagesSupported);

                // If actual extension match with PHP support extensions
                if ($type || $type === 0) {
                    // Resize the image
                    chageSize($target, $imagesSupported[$type], $newWidth);
                }
            }

            return true;
        } else {
            return false;
        }
    }

    /**
    * POST
    * UPLOAD/SECTION
    * RECEIVE FILE AND PERSIST ON DISK
    * @route '/upload/'
    * @params {}
    */
    $this->map(['POST', 'OPTIONS'], '/', function (Request $request, Response $response) {
        try {
            $data = null;
            $file = null;

            // Get request's content
            $file = $request->getUploadedFiles();
            $data = $request->getParsedBody();

            // If content are valid, send to client
            // If not, call the Exception
            if ($data != null && $file != null) {
                $file = $file['file']->file;
                $data = $data['name'];

                // Get and check extension
                $extension = getExtension($data);

                // Create new name
                $newName = setNewName($data);

                // Upload file
                $upload = uploadFile($file, $newName);

                if ($upload) {
                    // Create message
                    $data = array('file' => $newName);
            	} else {
                    throw new Exception('Error sending you file, try again');
                }

                // Create response
                $response = $response->withJson($data, 201);
            } else {
                // Call Exception
                throw new Exception('You need put file attached');
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
