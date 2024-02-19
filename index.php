<?php
session_start();
error_reporting(0);
// include 'filters.php';
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
                $_SESSION['uploadedFilePaths'] = $targetFile; 
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "No file uploaded or an error occurred.";
    }
}
// $_SESSION['uploadedFilePaths'] = $targetFile; 

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

        $threshold = 110;
        for ($x = 0; $x < $imageWidth; $x++) {
            for ($y = 0; $y < $imageHeight; $y++) {
                $rgb = imagecolorat($sourceImage, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
              
                $gray = round(0.299 * $r + 0.587 * $g + 0.114 * $b);
                // $gray = round(0.350 * $r + 0.750 * $g + 0.114 * $b);

               
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
$convertionType = '';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(isset($_POST["download"])) {
        $editedImage = $_POST['grayscaledfile'];

        // echo $editedImage;
        
        $editedImagePath = $editedImage;
        header('Content-Type: image/jpeg');
        header('Content-Disposition: attachment; filename="downloaded_image_'.time().'.jpeg"');

        readfile($editedImagePath);
    }else if(isset($_POST["download-filters-btn"])){

        $filteredImages = $_POST['download-filters'];

        $editedImagePath = $filteredImages;
        header('Content-Type: image/jpeg');
        header('Content-Disposition: attachment; filename="downloaded_image_'.time().'.jpeg"');

        readfile($editedImagePath);
    }
}
if (isset($_SESSION['thumbnails'])) {

    include 'filters.php';
    $thumbnails = $_SESSION['thumbnails'];

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <title>PicSure</title>
</head>
<style>
    body {
        background-color: #141d26;
        color: #fff;
        text-align: center;
        margin: 50px;
        
    }
    .title{
        font-family: "Black Ops One", system-ui;
        font-weight: 400;
        font-style: normal;
    }

    .border {
        border-color: #301E67 !important;
    }

    #imageContainer img,
    #grayscaleImageContainer img {
        max-width: 100%;
        height: auto;
    }
    .img-filter{
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
        cursor: pointer;   
    }
    .form-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        background: #ff0000; 
        cursor: pointer; 
        border-radius: 50%; 
    }.filter-img:hover{
        transform: scale(1.15);
        transition: .5s;
        cursor: pointer;
    }
</style>
<body>
    <div class="container-fluid" style="background-size: cover;">
        <h1 class="text-center p-2 title">PicSure Photo Editor</h1>
        <form action="" method="POST">
            <div class="d-flex flex-row mb-3 justify-content-center">
                <div class="p-3">
                        <select name="type" class="form-select" required>
                            <option>Select</option>
                            <option>Grayscale</option>
                            <option>Black and White</option>
                            <!-- <option>Filters</option> -->
                        </select>
                </div>
                <div class="p-3">
                    <input type="hidden" name="targetFile" value="<?php echo $targetFile; ?>">
                    <button type="submit" name="selectButton" class="btn btn-secondary mb-3">Convert Now</button>
                </div>
            </div>
        </form>
        <div class="h-100">
            <div class="row text-center">
                <div class="col-md-5 border rounded-2 image1">
                    <div class="p-3">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="uid" value="<?php echo $uid; ?>">
                            <input type="file" name="image">
                            <button type="submit" name="submit">Upload</button>
                        </form>
                        <div class="mt-2">
                            <?php 
                            if ($targetFile) {
                                echo '<img src="' . $targetFile . '" alt="Uploaded Image" style="max-width: 100%; max-height: 100%;">'; 
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 p-2">
                </div>
                <div class="col-md-5 border rounded-2">
                    <div class="p-1">
                            <div class="d-flex">
                                <div class="w-100">
                                    <h2>Converted Image</h2>
                                </div>
                                <div class="flex-shrink-1">
                                    <a class="m-3" type="button" data-bs-toggle="dropdown" aria-expanded="false" ><i class="fa-solid fa-ellipsis-vertical fa-rotate-by fa-lg" style="color: #ffffff; --fa-rotate-angle: 301E67deg;""></i></a>
                                    <ul class="dropdown-menu">
                                        <form method="POST">
                                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filtersModal" type="button" name="filters">Edit Filters</button></li>
                                            <input type="hidden" name="grayscaledfile" value="<?php echo $editedImage; ?>">
                                            <li><button class="dropdown-item" name="download" type="submit">Download Image</button></li>
                                        </form>
                                    </ul>
                                </div>
                                <div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen" style="background-color: transparent;">
                                        <div class="modal-content" style="background-color: #141d26;">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="filtersModalLabel">Edit Filters</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                        <div class="col-md-4">
                                                            <h3>Filters</h3>
                                                            <?php if (isset($_SESSION['thumbnails'])): ?>
                                                                <div class="border rounded-2 p-3">
                                                                    <div class="row">
                                                                        <?php $counter = 0; ?>
                                                                        <?php foreach ($_SESSION['thumbnails'] as $filterName => $thumbnailPath): ?>
                                                                            <?php if ($counter > 0 && $counter % 4 == 0): ?>
                                                                                </div><div class="row">
                                                                            <?php endif; ?>
                                                                            <div class="col-md-3 filter-img mb-3" onclick="updateMainImage('<?php echo htmlspecialchars($thumbnailPath, ENT_QUOTES, 'UTF-8'); ?>')">
                                                                                <img src="<?php echo htmlspecialchars($thumbnailPath, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($filterName, ENT_QUOTES, 'UTF-8'); ?>" class="img-fluid img-filter">
                                                                            </div>
                                                                            <?php $counter++; ?>
                                                                        <?php endforeach; ?>
                                                                    </div>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                
                                                        <div class="col-md-4">
                                                            <?php if ($targetFile): ?>
                                                                <img src="<?php echo htmlspecialchars($targetFile, ENT_QUOTES, 'UTF-8'); ?>" alt="Uploaded Image" id="mainImage" class="" style="max-width: 100%; height: 90%;">
                                                            <?php endif; ?>
                                                        </div>
                                                    <div class="col-md-4">
                                                            <h3 class="text-align-center">Adjust</h3>
                                                            <div class="border rounded-2 p-3 m-4">
                                                                <div class="list-group list-group-horizontal mb-3" id="list-tab" role="tablist">
                                                                    <a class="list-group-item list-group-item-action active" id="list-colors-list" data-bs-toggle="list" href="#list-colors" role="tab" aria-controls="list-home">Colors</a>
                                                                    <a class="list-group-item list-group-item-action" id="list-profile-list" data-bs-toggle="list" href="#list-profile" role="tab" aria-controls="list-profile">Advance</a>
                                                                </div>
                                                                <div class="col-12">
                                                                <div class="tab-content" id="nav-tabContent">
                                                                    <div class="tab-pane fade show active" id="list-colors" role="tabpanel" aria-labelledby="list-colors-list">
                                                                        <input type="range" class="form-range" min="0" max="50" value="0" id="redRange">
                                                                        <label for="redRange" class="form-label">Red</label>
                                                                        <input type="range" class="form-range" min="0" max="50" value="0" id="blueRange">
                                                                        <label for="blueRange" class="form-label">Blue</label>
                                                                        <input type="range" class="form-range" min="0" max="50" value="0" id="greenRange">
                                                                        <label for="greenRange" class="form-label">Green</label>
                                                                    </div>
                                                                    <div class="tab-pane fade" id="list-profile" role="tabpanel" aria-labelledby="list-profile-list">
                                                                        <input type="range" class="form-range" min="0" max="127" value="0" id="opacityRange">
                                                                        <label for="opacityRange" class="form-label">Opacity</label>
                                                                        <input type="range" class="form-range" min="0" max="50" value="0" id="brightnessRange">
                                                                        <label for="brightnessRange" class="form-label">Brightness</label>
                                                                        <input type="range" class="form-range disable"  min="0" max="255" value="0" id="saturationRange">
                                                                        <label for="saturationRange" class="form-label">Saturation</label>
                                                                        <input type="range" class="form-range" min="0" max="30" value="0" id="blurRange">
                                                                        <label for="blurRange" class="form-label">Blur</label>

                                                                    </div>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> -->
                                                    <form action="" method="POST">
                                                        <input type="hidden" name="download-filters" value="<?php echo $targetfile; ?>">
                                                        <button type="submit" class="btn btn-primary" name="download-filters-btn" >Save changes</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="p-2">
                            <?php 
                            if ($sourceImage) {
                                echo '<img src="' . $editedImage . '" alt="Grayscaled Image" style="max-width: 100%; max-height: 100%;">'; 
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JS Links -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="js/main.js"></script>
    <script>
    function updateMainImage(imagePath) {
    var mainImage = document.getElementById('mainImage');
    mainImage.src = imagePath;
    }
    document.addEventListener('DOMContentLoaded', function() {

    document.getElementById('redRange').addEventListener('input', applyColorAdjustments);
    document.getElementById('greenRange').addEventListener('input', applyColorAdjustments);
    document.getElementById('blueRange').addEventListener('input', applyColorAdjustments);
    document.getElementById('opacityRange').addEventListener('input', applyColorAdjustments);
    document.getElementById('saturationRange').addEventListener('input', applyColorAdjustments);
    document.getElementById('brightnessRange').addEventListener('input', applyColorAdjustments);
    document.getElementById('blurRange').addEventListener('input', applyColorAdjustments);

    function applyColorAdjustments() {
        var redRange = document.getElementById('redRange').value;
        var greenRange = document.getElementById('greenRange').value;
        var blueRange = document.getElementById('blueRange').value;
        var saturationRange = document.getElementById('opacityRange').value;
        var saturationRange = document.getElementById('saturationRange').value;
        var brightnessRange = document.getElementById('brightnessRange').value;
        var blurRange = document.getElementById('blurRange').value;
        var targetFile = '<?php echo $targetFile; ?>';

        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'colors.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function() {
            if (this.status == 200) {
                document.getElementById('mainImage').src = 'img/color_adjusted/' + this.responseText + '?t=' + new Date().getTime();
            }
        };

        xhr.send(`redRange=${redRange}&greenRange=${greenRange}&blueRange=${blueRange}&opacityRange=${opacityRange}&saturationRange=${saturationRange}&brightnessRange=${brightnessRange}&blurRange=${blurRange}&targetFile=${targetFile}`);
    }
});


    </script>
</body>
</html>
