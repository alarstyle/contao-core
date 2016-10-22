<?php

require_once '../vendor/autoload.php';
require_once '../system/initialize.php';

//$originalPath = 'files/f 1.1/img 01.JPEG';
//$resizedPath = 'files/f 1.1/img 01-1000-1000-91c211.JPEG';

$resizedPath = $_SERVER['REQUEST_URI'];

if ($resizedPath[0] === '/') {
    $resizedPath = substr($resizedPath, 9);
}

$resizedPath = rawurldecode($resizedPath);

//$start = microtime(true);

//$resizedPath = \Contao\ResizedImage::getResizedUrl($originalPath, 1000);
//var_dump($resizedPath);
//exit;


// Retrieving original data from URL
$originalData = \Contao\ResizedImage::getOriginalData($resizedPath);


// 404 if no original data or original file doesn't exist
if (!$originalData || !file_exists(TL_ROOT . '/' . $originalData['path'])) {
    http_response_code(404);
    echo '404';
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


//$time_elapsed_secs = microtime(true) - $start;


//echo '<br>' . $time_elapsed_secs . 's<br>';
