<?php

namespace Gambling;

use \Grow\Encryptor;

class ScreenImage
{
    public static function generateImageSrc($url = null)
    {
        if (!$url) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . explode('?', rawurldecode($_SERVER['REQUEST_URI']))[0];
        }

        $imageName = substr(md5($url), 0, 16);

        $data = Encryptor::encrypt($url);

        return '/screens/files/' . $imageName . '.jpg?data=' . $data;
    }
}