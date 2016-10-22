<?php

namespace Contao;

class ResizedImage
{

    protected static $originalCache = [];

    protected static $resizedCache = [];


    protected static function generateKey($originalPath, $width, $height)
    {
        $encryptionKey = Config::get('encryptionKey');
        $time = filemtime(TL_ROOT . '/' . $originalPath);
        return substr(md5($encryptionKey . $time . $originalPath . $width . $height), 0, 6);
    }


    public static function getResizedUrl($originalPath, $width = null, $height = null)
    {
        $originalPath = rawurldecode($originalPath);
        $cacheKey = $originalPath . '_' . $width . '_' . $height;

        if (static::$originalCache[$cacheKey]) {
            return static::$originalCache[$cacheKey];
        }

        // Return original path if file doesn't exist or size not set
        if (!file_exists(TL_ROOT . '/' . $originalPath) || (!$width && !$height)) {
            return System::urlEncode($originalPath);
        }

        $pathinfo = pathinfo($originalPath);
        $dirname = $pathinfo['dirname'];
        $filename = $pathinfo['filename'];
        $extension = $pathinfo['extension'];

        // Return original path if it's not an image
        if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif'])) {
            return System::urlEncode($originalPath);
        }

        $key = static::generateKey($originalPath, $width, $height);

        $resizedPath = $dirname . '/' . $filename . '-' . $width . '-' . $height . '-' . $key . '.' . $extension;

        return static::$originalCache[$cacheKey] = $resizedPath;
    }


    public static function getOriginalData($resizedPath)
    {
        $resizedPath = rawurldecode($resizedPath);

        if (static::$originalCache[$resizedPath]) {
            return static::$originalCache[$resizedPath];
        }

        $parts = explode('-', $resizedPath);

        list($width, $height, $key) = array_slice($parts, -3);

        if (!$width || !$height || !$key) {
            return null;
        }

        list($key, $extension) = explode('.', $key);

        $originalPath = implode('', array_slice($parts, 0, -3)) . '.' . $extension;

        if (static::generateKey($originalPath, $width, $height) !== $key) {
            return null;
        }

        return static::$originalCache[$resizedPath] = [
            'width' => $width ?: null,
            'height' => $height ?: null,
            'key' => $key,
            'path' => $originalPath
        ];
    }


    public static function createResizedImageAndSave($resizedPath, $originalData = null, $forceSave = false)
    {
        if (!$originalData) {
            $originalData = static::getOriginalData($resizedPath);
        }

        if (extension_loaded('imagick')) {
            $image = new \PHPixie\Image('imagick');
        } else {
            $image = new \PHPixie\Image('gd');
        }

        $img = $image->read(TL_ROOT . '/' . $originalData['path']);
        $img->fill($originalData['width'], $originalData['height']);


        if ($forceSave || !file_exists($resizedPath)) {

            $foldersPath = '';
            foreach (explode('/', pathinfo($resizedPath)['dirname']) as $folderName) {
                $foldersPath .= (strlen($foldersPath) > 0 ? '/' : '') . $folderName;
                if (file_exists($foldersPath))
                    continue;
                mkdir($foldersPath);
            }

            $img->save($resizedPath);

        }
    }

}