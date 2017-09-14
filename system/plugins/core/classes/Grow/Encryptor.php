<?php

namespace Grow;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class Encryptor
{

    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  string  $key An optional encryption key
     *
     * @return string
     */
    public static function encrypt($value, $key = null)
    {
        if (!$key) {
            $key = \Contao\Config::get('newEncryptionKey');
        }

        return Crypto::encrypt($value, Key::loadFromAsciiSafeString($key));
    }

    /**
     * Decrypt the given value.
     *
     * @param  mixed  $value
     * @param  string  $key An optional encryption key
     *
     * @return string
     */
    public static function decrypt($value, $key = null)
    {
        if (!$key) {
            $key = \Contao\Config::get('newEncryptionKey');
        }

        return Crypto::decrypt($value, Key::loadFromAsciiSafeString($key));
    }
}