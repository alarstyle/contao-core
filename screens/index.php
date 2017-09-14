<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once '../vendor/autoload.php';
require_once '../system/initialize.php';

//$str = serialize(['/asdasdqw/asdqweqw', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg', '/adqwdasd/dqwrrrdfsdfsf_sfdewwefwe.jpg']);
//
//echo base64_encode($str);
//echo '<br>';
//echo strlen(base64_encode($str));
//echo '<br>';
//echo strlen($str);
//echo '<br>';
//
//$temp = \Grow\Encryptor::encrypt($str);
//
//echo $temp;
//echo '<br>';
//echo strlen($temp);
//echo '<br>';
//
//
//$temp = \Contao\Encryption::encrypt($str);
//
//echo '<br>';
//echo $temp;
//echo '<br>';
//echo strlen($temp);
//echo '<br>';
//
//exit;

$data = $_GET['data'];
$fileName = str_replace('/screens/files/', '', explode('?', rawurldecode($_SERVER['REQUEST_URI']))[0]);

if (!$data || !$fileName) {
    http_response_code(400);
    echo 'Error 400. Wrong arguments.';
    exit;
}

try {
    $pageUrl = \Grow\Encryptor::decrypt($data);
} catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $exception) {
    http_response_code(400);
    echo 'Error 400. Wrong arguments.';
    exit;
}

$params = http_build_query([
    "url" => $pageUrl,
    "width" => 1800,
    "height" => 945,
    "access_key" => "b3d6e1b37b54442186ebdd33f2192ca8"
]);

$imageData = @file_get_contents("https://apileap.com/api/screenshot/v1/urltoimage?" . $params);

if (!$imageData) {
    header('Content-Type: image/jpeg');
    readfile("default.jpg");
} else {
    file_put_contents("files/" . $fileName, $imageData);
    header('Content-Type: image/jpeg');
    readfile("files/" . $fileName);
}



exit;

/*

// Retrieving original data from URL
$originalData = \Contao\ResizedImage::getOriginalData($resizedPath);


// 404 if no original data or original file doesn't exist
if (!$originalData || !file_exists(TL_ROOT . '/' . $originalData['path'])) {
    http_response_code(400);
    echo 'Error 400. Wrong arguments.';
    exit;
}


// Redirect to image if it was already created
if (file_exists($resizedPath)) {
    sleep(1);
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}


// Path to marker. If file exists - resizing is in progress.
$resizingMarker = TL_ROOT . '/resized/resizing_' . $originalData['key'];


// If marker exists - wait and reload the page
if (file_exists($resizingMarker)) {
    sleep(4);
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}


// Creating marker file
touch($resizingMarker);


// Resize image and save if not exist
\Contao\ResizedImage::createResizedImageAndSave($resizedPath, $originalData);


// Deleting marker file
unlink($resizingMarker);
*/
