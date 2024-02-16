<?php 
session_start();
function generateUniqueFileName($originalFileName)
{
    $extension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $timestamp = time();
    $randomString = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 10);
    return $timestamp . "_" . $randomString . "." . $extension;
}

$targetFile = ""; 
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
  
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $targetDir = "img/uploads/";
        $targetFile = $targetDir . generateUniqueFileName($_FILES["image"]["name"]);

        
        if (file_exists($targetFile)) {
            echo "Sorry, file already exists.";
        } else {
       
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
                echo "The file " . generateUniqueFileName($_FILES["image"]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "No file uploaded or an error occurred.";
    }
}

$sourceImage = "";
$editedImage='';
if(isset($_POST["selectButton"])){
    $targetFile = $_POST['targetFile'];
    $convertionType = $_POST['type'];

    if ($convertionType == 'Grayscale') {
    
        $imageFileType = strtolower(pathinfo($targetFile,PATHINFO_EXTENSION));
                    
        if ($imageFileType == "jpg" || $imageFileType == "jpeg") {
            $sourceImage = imagecreatefromjpeg($targetFile);
        } elseif ($imageFileType == "png") {
            $sourceImage = imagecreatefrompng($targetFile);
        } elseif ($imageFileType == "gif") {
            $sourceImage = imagecreatefromgif($targetFile);
        } else {
            echo "Invalid file type.";
            exit;
        }
        imagefilter($sourceImage, IMG_FILTER_GRAYSCALE);
        $editedImage = "img/grayscaled/grayscale_" . basename($targetFile);
        imagejpeg($sourceImage, $editedImage); 
        imagedestroy($sourceImage);
    }
    else if($convertionType == 'Black and White'){
        
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if ($imageFileType == "jpg" || $imageFileType == "jpeg") {
            $sourceImage = imagecreatefromjpeg($targetFile);
        } elseif ($imageFileType == "png") {
            $sourceImage = imagecreatefrompng($targetFile);
        } elseif ($imageFileType == "gif") {
            $sourceImage = imagecreatefromgif($targetFile);
        } else {
            echo "Invalid file type.";
            exit;
        }
        $imageWidth = imagesx($sourceImage);
        $imageHeight = imagesy($sourceImage);
        $outputImage = imagecreatetruecolor($imageWidth, $imageHeight);

        
        $black = imagecolorallocate($outputImage, 0, 0, 0);
        $white = imagecolorallocate($outputImage, 255, 255, 255);

        $threshold = 127;
        for ($x = 0; $x < $imageWidth; $x++) {
            for ($y = 0; $y < $imageHeight; $y++) {
                $rgb = imagecolorat($sourceImage, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
              
                $gray = round(0.299 * $r + 0.587 * $g + 0.114 * $b);
               
                $bwColor = ($gray < $threshold) ? $black : $white;
                imagesetpixel($outputImage, $x, $y, $bwColor);
            }
        }
        
        $editedImage = "img/BNW/bnw_" . basename($targetFile);
        imagejpeg($outputImage, $editedImage);
        imagedestroy($sourceImage);
        imagedestroy($outputImage);
    }
}


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["download"])){

    $editedImage = $_POST['grayscaledfile'];

    // echo $editedImage;
    
    $editedImagePath = $editedImage;
    header('Content-Type: image/jpeg');
    header('Content-Disposition: attachment; filename="downloaded_image_'.time().'.jpeg"');

    readfile($editedImagePath);
}
?>