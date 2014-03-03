<?php
        require_once "config.php";
        function pictureHandling($id, $picture)
        {
                if ($picture == NULL) return ""; // No file uploaded

                //echo 'valid picture <br />';
                if ($picture['size'] == 0) {
                        echo "Problem: uploaded file is zero length";
                        return "";
                }

                if (($picture['type'] != "image/jpeg") &&
                        ($picture['type'] != "image/jpg") &&
                        ($picture['type'] != "image/png") &&
                        ($picture['type'] != "image/gif")) {
                        echo "Problem: file is not a proper png, gif, jpg, or jpeg";
                        return "";
                }

                if (!is_uploaded_file($picture['tmp_name'])) {
                        echo "Problem: possible file upload attack";
                        return "";
                }

                $upfile = picName($id, $picture['name']);
                $thumb = thumbName($id);

                if (!move_uploaded_file($picture['tmp_name'], $upfile)) {
                        echo "Problem: Could not move picture into pictures directory";
                        return "";
                }

                makeThumb($upfile, $thumb);

                return $upfile;
        }

        function picName($id, $name)
        {
                return PICPATH . $id . "--" . $name;
        }

        function thumbName($id)
        {
                return PICPATH . "thumbs/$id.jpg";
        }

        function makeThumb($uploaded, $thumbName)
        {
                $maxW = 120;
                $maxH = 120;

                list($width, $height, $type) = getimagesize($uploaded);

                // If the image is too big, scale it down
                // From kvslaap on http://us2.php.net/manual/en/function.imagecopyresized.php
                $imgratio = ($width / $height);
                if ($imgratio > 1) {
                        $newW = $maxW;
                        $newH = ($maxW / $imgratio);
                } else {
                        $newH = $maxH;
                        $newW = ($maxH * $imgratio);
                }

                $thumb = imagecreatetruecolor($newW, $newH);

                if ($type == IMAGETYPE_JPEG) {
                        $source = imagecreatefromjpeg($uploaded);
                } else if ($type == IMAGETYPE_GIF) {
                        $source = imagecreatefromgif($uploaded);
                } else if ($type == IMAGETYPE_PNG) {
                        $source = imagecreatefrompng($uploaded);
                } else {
                        echo "Unrecognized file type.";
                        exit(1);
                }

                imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newW, $newH, $width, $height);
                imagejpeg($thumb, $thumbName);
        }
?>
