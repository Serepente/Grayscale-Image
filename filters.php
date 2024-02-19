<?php
session_start();
error_reporting(0); 

$_SESSION['test'] = 'Session is working';
echo $_SESSION['test'];
if (isset($_SESSION['uploadedFilePaths'])) {
    $targetFile = $_SESSION['uploadedFilePaths'];
    // echo $targetFile;

    $thumbnails = generateThumbnails($targetFile);
    $_SESSION['thumbnails'] = $thumbnails;

}else{
    header('location: index.php');
}
// Natural
function applyNaturalFilter($image, $targetFile) {

    imagefilter($image, IMG_FILTER_CONTRAST, -10);
    imagefilter($image, IMG_FILTER_BRIGHTNESS, 5);
    $editedImageNatural = "img/natural/natural_" . basename($targetFile);
    imagejpeg($image, $editedImageNatural);
    return $editedImageNatural;
}
// Fresh
function applyFreshFilter($image, $targetFile) {
    imagefilter($image, IMG_FILTER_COLORIZE, 0, 15, 15, 30);
    $editedImageFresh = "img/fresh/fresh_" . basename($targetFile);
    imagejpeg($image, $editedImageFresh);
    return $editedImageFresh;
}
// brightened
function applyBrightnessFilter($image, $targetFile) {
    imagefilter($image, IMG_FILTER_BRIGHTNESS, 100);
    $editedImageBrightness = "img/brightened/brightened_" . basename($targetFile);
    imagejpeg($image, $editedImageBrightness);

    return $editedImageBrightness;
}
// contrast
function applyContrastFilter($image, $targetFile) {
    imagefilter($image, IMG_FILTER_CONTRAST, -50); 
    $editedImageContrast = "img/contrasted/contrasted_" . basename($targetFile);
    imagejpeg($image, $editedImageContrast);

    return $editedImageContrast;
}
// sepia
function applySepiaFilter($image, $targetFile) {
    imagefilter($image, IMG_FILTER_GRAYSCALE);
    imagefilter($image, IMG_FILTER_COLORIZE, 100, 50, 0);
    $editedImageSepia = "img/sepia/sepia_" . basename($targetFile);
    imagejpeg($image, $editedImageSepia);

    return $editedImageSepia;
}
// sunglow
function applySunglowFilter($image, $targetFile) {
   
    imagefilter($image, IMG_FILTER_COLORIZE, 50, 30, 0);
    $editedImageSunglow = "img/sunglowed/sunglowed_" . basename($targetFile);
    imagejpeg($image, $editedImageSunglow);

    return $editedImageSunglow;
}
// Warm
function applyWarmFilter($image, $targetFile) {
 
    imagefilter($image, IMG_FILTER_COLORIZE, 60, 15, 0);
    $editedImageWarm = "img/warmed/warmed_" . basename($targetFile);
    imagejpeg($image, $editedImageWarm);

    return $editedImageWarm;
}
// Cool
function applyCoolFilter($image, $targetFile) {

    imagefilter($image, IMG_FILTER_COLORIZE, 0, 0, 50);
    $editedImageCool = "img/cool/cool_" . basename($targetFile);
    imagejpeg($image, $editedImageCool);

    return $editedImageCool;
}
// Autumn
function applyAutumnFilter($image, $targetFile) {
    
    imagefilter($image, IMG_FILTER_COLORIZE, 80, 30, 0);
    $editedImageAutumn = "img/autumn/autumn_" . basename($targetFile);
    imagejpeg($image, $editedImageAutumn);

    return $editedImageAutumn;
}
// Negative
function applyNegativeFilter($image, $targetFile) {
    
    imagefilter($image, IMG_FILTER_NEGATE);
    $editedImageNegative = "img/negative/negative_" . basename($targetFile);
    imagejpeg($image, $editedImageNegative);

    return $editedImageNegative;
}
// Noir
function applyNoirFilter($image, $targetFile) {
    
    imagefilter($image, IMG_FILTER_GRAYSCALE);
    imagefilter($image, IMG_FILTER_CONTRAST, -50);
    imagefilter($image, IMG_FILTER_BRIGHTNESS, -10);
    $editedImageNoir = "img/noir/noir_" . basename($targetFile);
    imagejpeg($image, $editedImageNoir);

    return $editedImageNoir;
}
// Draw
function applyDrawFilter($image, $targetFile) {
    
    imagefilter($image, IMG_FILTER_GRAYSCALE);
    imagefilter($image, IMG_FILTER_CONTRAST, -50);
    imagefilter($image, IMG_FILTER_MEAN_REMOVAL);
    $editedImageDraw = "img/draw/draw_" . basename($targetFile);
    imagejpeg($image, $editedImageDraw);

    return $editedImageDraw;
}

function generateThumbnails($targetFile) {
    $filters = ['Natural', 'Fresh', 'Brightness', 'Contrast', 'Sepia', 'Sunglow', 'Warm', 'Cool', 'Autumn', 'Negative', 'Noir', 'Draw'];
    $thumbnails = [];

    foreach ($filters as $filter) {
        $image = imagecreatefromjpeg($targetFile);
        $functionName = "apply" . $filter . "Filter";
        if (function_exists($functionName)) {
        
            $thumbnailPath = $functionName($image, $targetFile);
            imagedestroy($image); 
            $thumbnails[$filter] = $thumbnailPath;
        }
    }

    return $thumbnails;
}


?>
