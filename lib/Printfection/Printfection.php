<?php

abstract class Printfection {
	/**
	 * @var string The Printfection API key to be used for requests.
	 */
	public static $apiKey;

	/**
	 * @var string The base URL for the Printfection API.
	 */
	public static $apiBase = 'https://api.printfection.com';

	/**
	 * @var boolean Defaults to true.
	 */
	public static $verifySslCerts = true;
	const VERSION = '1.0.0';

	/**
	 * @return string The API key used for requests.
	 */
	public static function getApiKey() {
		return self::$apiKey;
	}

	/**
	 * Sets the API key to be used for requests.
	 *
	 * @param string $apiKey
	 */
	public static function setApiKey($apiKey) {
		self::$apiKey = $apiKey;
	}

	/**
	 * @return boolean
	 */
	public static function getVerifySslCerts() {
		return self::$verifySslCerts;
	}

	/**
	 * @param boolean $verify
	 */
	public static function setVerifySslCerts($verify) {
		self::$verifySslCerts = $verify;
	}
}
