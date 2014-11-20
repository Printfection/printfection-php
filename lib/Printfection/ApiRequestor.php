<?php

class Printfection_ApiRequestor {
	/**
	 * @var string $apiKey The API key that's to be used to make requests.
	 */
	public $apiKey;

	public function __construct($apiKey = null) {
		$this->_apiKey = $apiKey;
	}

	/**
	 * @param string $url The path to the API endpoint.
	 *
	 * @returns string The full path.
	 */
	public static function apiUrl($url='') {
		$apiBase = Printfection::$apiBase;

		return "$apiBase$url";
	}

	/**
	 * @param string|mixed $value A string to UTF8-encode.
	 *
	 * @returns string|mixed The UTF8-encoded string, or the object passed in if
	 *    it wasn't a string.
	 */
	public static function utf8($value) {
		if (is_string($value) && mb_detect_encoding($value, "UTF-8", TRUE) != "UTF-8") {
			return utf8_encode($value);
		} else {
			return $value;
		}
	}

	private static function _encodeObjects($d) {
		if ($d instanceof Printfection_ApiResource) {
			return self::utf8($d->id);
		} else if ($d === true) {
			return 'true';
		} else if ($d === false) {
			return 'false';
		} else if (is_array($d)) {
			$res = array();

			foreach ($d as $k => $v) {
				$res[$k] = self::_encodeObjects($v);
			}

			return $res;
		} else {
			return self::utf8($d);
		}
	}

	/**
	 * @param array $arr An map of param keys to values.
	 * @param string|null $prefix (It doesn't look like we ever use $prefix...)
	 *
	 * @returns string A querystring, essentially.
	 */
	public static function encode($arr, $prefix=null) {
		if (!is_array($arr)) {
			return $arr;
		}

		$r = array();

		foreach ($arr as $k => $v) {
			if (is_null($v)) {
				continue;
			}

			if ($prefix && $k && !is_int($k)) {
				$k = $prefix."[".$k."]";
			} else if ($prefix) {
				$k = $prefix."[]";
			}

			if (is_array($v)) {
				$r[] = self::encode($v, $k, true);
			} else {
				$r[] = urlencode($k)."=".urlencode($v);
			}
		}

		return implode("&", $r);
	}

	/**
	 * @param string $method
	 * @param string $url
	 * @param array|null $params
	 *
	 * @return array An array whose first element is the response and second
	 *    element is the API key used to make the request.
	 */
	public function request($method, $url, $params=null) {
		if (!$params) {
			$params = array();
		}

		list($rbody, $rcode, $myApiKey) = $this->_requestRaw($method, $url, $params);
		$resp = $this->_interpretResponse($rbody, $rcode);

		return array($resp, $myApiKey);
	}


	/**
	 * @param string $rbody A JSON string.
	 * @param int $rcode
	 * @param array $resp
	 *
	 * @throws Printfection_InvalidRequestError if the error is caused by the user.
	 * @throws Printfection_AuthenticationError if the error is caused by a lack of
	 *    permissions.
	 * @throws Printfection_ApiError otherwise.
	 */
	public function handleApiError($rbody, $rcode, $resp) {
		if (!is_array($resp) || !isset($resp['error'])) {
			$msg = "Invalid response object from API: $rbody (HTTP response code was $rcode)";

			throw new Printfection_ApiError($msg, $rcode, $rbody, $resp);
		}

		$error = $resp['error'];
		$msg = isset($error['message']) ? $error['message'] : null;
		$param = isset($error['param']) ? $error['param'] : null;

		switch ($rcode) {
			case 404:
				throw new Printfection_InvalidRequestError($msg, $param, $rcode, $rbody, $resp);
			case 401:
				throw new Printfection_AuthenticationError($msg, $rcode, $rbody, $resp);
			default:
				throw new Printfection_ApiError($msg, $rcode, $rbody, $resp);
		}
	}

	private function _requestRaw($method, $url, $params) {
		$myApiKey = $this->_apiKey;

		if (!$myApiKey) {
			$myApiKey = Printfection::$apiKey;
		}

		if (!$myApiKey) {
			$msg = 'No API key provided';

			throw new Printfection_AuthenticationError($msg);
		}

		$absUrl = $this->apiUrl($url);
		$params = self::_encodeObjects($params);
		$langVersion = phpversion();
		$uname = php_uname();

		$ua = array(
				'bindings_version' => Printfection::VERSION,
				'lang' => 'php',
				'lang_version' => $langVersion,
				'publisher' => 'printfection',
				'uname' => $uname,
		);

		$headers = array(
				'X-Printfection-Client-User-Agent: ' . json_encode($ua),
				'User-Agent: Printfection/v2 PhpBindings/' . Printfection::VERSION,
				'Authorization: Bearer ' . $myApiKey,
				'Content-Type: application/json',
		);

		list($rbody, $rcode) = $this->_curlRequest($method, $absUrl, $headers, $params);

		return array($rbody, $rcode, $myApiKey);
	}

	private function _interpretResponse($rbody, $rcode) {
		try {
			$resp = json_decode($rbody, true);
		} catch (Exception $e) {
			$msg = "Invalid response body from API: $rbody (HTTP response code was $rcode)";
			throw new Printfection_ApiError($msg, $rcode, $rbody);
		}

		if ($rcode < 200 || $rcode >= 300) {
			$this->handleApiError($rbody, $rcode, $resp);
		}

		return $resp;
	}

	private function _curlRequest($method, $absUrl, $headers, $params) {
		$curl = curl_init();
		$method = strtolower($method);
		$opts = array();

		if ($method == 'get') {
			$opts[CURLOPT_HTTPGET] = 1;

			if (count($params) > 0) {
				$encoded = self::encode($params);
				$absUrl = "$absUrl?$encoded";
			}
		} else {
			if ($method == 'post') {
				$opts[CURLOPT_CUSTOMREQUEST] = 'POST';
			} else if ($method == 'patch') {
				$opts[CURLOPT_CUSTOMREQUEST] = 'PATCH';
			} else if ($method == 'delete') {
				$opts[CURLOPT_CUSTOMREQUEST] = 'DELETE';
			} else {
				throw new Printfection_ApiError("Unrecognized method $method");
			}

			if (count($params) > 0) {
				$encoded = json_encode($params);
				$opts[CURLOPT_POSTFIELDS] = $encoded;
			}

			$headers[] = 'Content-Length: ' . strlen($encoded);
		}

		$absUrl = self::utf8($absUrl);
		
		$opts[CURLOPT_URL] = $absUrl;
		$opts[CURLOPT_RETURNTRANSFER] = true;
		$opts[CURLOPT_CONNECTTIMEOUT] = 30;
		$opts[CURLOPT_TIMEOUT] = 80;
		$opts[CURLOPT_RETURNTRANSFER] = true;
		$opts[CURLOPT_HTTPHEADER] = $headers;

		if (!Printfection::$verifySslCerts) {
			$opts[CURLOPT_SSL_VERIFYPEER] = false;
		}

		curl_setopt_array($curl, $opts);
		$rbody = curl_exec($curl);

		if ($rbody === false) {
			$errno = curl_errno($curl);
			$message = curl_error($curl);
			curl_close($curl);
			$this->handleCurlError($errno, $message);
		}

		$rcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);
		
		return array($rbody, $rcode);
	}

	/**
	 * @param number $errno
	 * @param string $message
	 * @throws Printfection_ApiConnectionError
	 */
	public function handleCurlError($errno, $message) {
		$apiBase = Printfection::$apiBase;

		switch ($errno) {
			case CURLE_COULDNT_CONNECT:
			case CURLE_COULDNT_RESOLVE_HOST:
			case CURLE_OPERATION_TIMEOUTED:
				$msg = "Could not connect to Printfection ($apiBase). Please check your internet connection and try again.";
				break;
			case CURLE_SSL_CACERT:
			case CURLE_SSL_PEER_CERTIFICATE:
				$msg = "Could not verify Printfection's SSL certificate. Please make sure that your network is not intercepting certificates.";
				break;
			default:
				$msg = "Unexpected error communicating with Printfection.";
				break;
		}

		$msg .= "\n\n(Network error [errno $errno]: $message)";
		
		throw new Printfection_ApiConnectionError($msg);
	}
}
