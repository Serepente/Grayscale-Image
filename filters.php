<?php
session_start();
if (isset($_SESSION['uploadedFilePath'])) {
    $targetFile = $_SESSION['uploadedFilePath'];
}
// echo $targetFile;
$image = imagecreatefromjpeg($targetFile);

// Brightness Filter
imagefilter($image, IMG_FILTER_BRIGHTNESS, 100);
$editedImageBrightness = "img/brightened/brightened_" . basename($targetFile);
imagejpeg($image, $editedImageBrightness);
$_SESSION["editedImageBrightness"] = $editedImageBrightness;
echo $editedImageBrightness;
imagedestroy($image);

// VIVID
// SEPIA
// MONOCHROME
// NATURAL
// Sunglow
// Fresh
// WARM
// cooL
// AUTUMN
// cONTRAST
?>