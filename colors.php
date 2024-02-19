<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $redRange = $_POST['redRange'];
        $greenRange = $_POST['greenRange'];
        $blueRange = $_POST['blueRange'];
        $opacityRange = $_POST['opacityRange'];
        $saturationRange = $_POST['saturationRange'];
        $brightnessRange = $_POST['brightnessRange'];  
        $blurRange = $_POST['blurRange'];
        $targetFile = $_POST['targetFile'];
    
        // $image = imagecreatefromjpeg($targetFile); 
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($imageFileType == "jpg" || $imageFileType == "jpeg") {
            $image = imagecreatefromjpeg($targetFile);
        } elseif ($imageFileType == "png") {
            $image = imagecreatefrompng($targetFile);
        } elseif ($imageFileType == "gif") {
            $image = imagecreatefromgif($targetFile);
        } else {
            echo "Invalid file type.";
            exit;
        }
    
        if ($image === false) {
            echo 'Error loading image';
            exit;
        }
    
        if($redRange || $greenRange || $blueRange){
            imagefilter($image, IMG_FILTER_COLORIZE, $redRange * 5, $greenRange * 5, $blueRange * 5); 

        }

        if ($brightnessRange) {
            imagefilter($image, IMG_FILTER_BRIGHTNESS, $brightnessRange);
        }

        if ($blurRange) {
            for ($i = 0; $i < $blurRange; $i++) {
                imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR);
            }
        }

        if ($opacityRange >= 0 && $opacityRange <= 127) {
            $width = imagesx($image);
            $height = imagesy($image);
            $outputImage = imagecreatetruecolor($width, $height);

            $transparentBackground = imagecolorallocatealpha($outputImage, 0, 0, 0, 127);
            imagefill($outputImage, 0, 0, $transparentBackground);

            imagecopymerge($outputImage, $image, 0, 0, 0, 0, $width, $height, 100 - ($opacityRange / 127 * 100));

            imagepng($newImage, $savePath);
            imagedestroy($image);
            imagedestroy($newImage);
        }

    
        $adjustedFileName = 'adjusted_' . basename($targetFile);
        $savePath = 'img/color_adjusted/' . $adjustedFileName;
    
        imagejpeg($image, $savePath);
        imagedestroy($image);
    
        echo $adjustedFileName;
    }

}
?>
