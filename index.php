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
        $targetDir = "uploads/";
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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["grayscaleButton"])) {
        $targetFile = $_POST['targetFile'];

        if (!empty($targetFile) && file_exists($targetFile)) {

            if (file_exists($targetFile)) {
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
                $grayscaleFile = "grayscale_" . basename($targetFile);
                imagejpeg($sourceImage, $grayscaleFile); 
                imagedestroy($sourceImage);

        } else {
            echo "Invalid file path.";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Image Grayscale Editor</title>
</head>
<style>
    body {
        background-color: #141d26;
        color: #fff;
        text-align: center;
        margin: 50px;
    }

    .border {
        border-color: #301E67 !important;
    }

    #imageContainer img,
    #grayscaleImageContainer img {
        max-width: 100%;
        height: auto;
    }
</style>
<body>
    <div class="container-fluid" style="background-size: cover;">
        <h1 class="text-center p-5">Image Grayscale Editor</h1>
        <div class="h-100">
            <div class="row text-center">
                <div class="col-5 border rounded-2 image1">
                    <div class="p-3">
                        <form action="" method="POST" enctype="multipart/form-data">
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
                <div class="col-2">
                </div>
                <div class="col-5 border rounded-2">
                    <div class="p-3">
                        <h2>Grayscaled Image</h2>
                        <div>
                            <?php 
                            if ($sourceImage) {
                                echo '<img src="' . $grayscaleFile . '" alt="Grayscaled Image" style="max-width: 100%; max-height: 100%;">'; 
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <form action="" method="POST">
                <input type="hidden" name="targetFile" value="<?php echo $targetFile; ?>">
                <button type="submit" name="grayscaleButton" class="btn btn-secondary mb-3">Convert to Grayscale</button>
            </form>
        </div>
    </div>
    <!-- JS Links -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>
    <script src="js/main.js"></script>
</body>
</html>
