<?php

namespace Grow;


/**
 * Generates and validates request tokens
 *
 * The class tries to read and validate the request token from the user session
 * and creates a new token if there is none.
 *
 * Usage:
 *
 *     if (!RequestToken::validate('TOKEN'))
 *     {
 *         throw new Exception("Invalid request token");
 *     }
 *
 */
class RequestToken
{

	/**
	 * Token
	 * @var string
	 */
	protected static $strToken;


    public function __construct()
    {
        throw new \Exception('Can not instantiate a static class');
    }


	/**
	 * Read the token from the session or generate a new one
	 */
	public static function initialize()
	{
		static::$strToken = @$_SESSION['REQUEST_TOKEN'];

		// Backwards compatibility
		if (is_array(static::$strToken))
		{
			static::$strToken = null;
			unset($_SESSION['REQUEST_TOKEN']);
		}

		// Generate a new token
		if (static::$strToken == '')
		{
			static::$strToken = md5(uniqid(mt_rand(), true));
			$_SESSION['REQUEST_TOKEN'] = static::$strToken;
		}

		// Set the REQUEST_TOKEN constant
		if (!defined('REQUEST_TOKEN'))
		{
			define('REQUEST_TOKEN', static::$strToken);
		}
	}


	/**
	 * Return the token
	 *
	 * @return string The request token
	 */
	public static function get()
	{
		return static::$strToken;
	}


	/**
	 * Validate a token
	 *
	 * @param string $strToken The request token
	 *
	 * @return boolean True if the token matches the stored one
	 */
	public static function validate($strToken)
	{
		// The feature has been disabled
		if (Config::get('disableRefererCheck'))
		{
			return true;
		}

		// Validate the token
		if ($strToken != '' && static::$strToken != '' && $strToken == static::$strToken)
		{
			return true;
		}

		return false;
	}
    
}
