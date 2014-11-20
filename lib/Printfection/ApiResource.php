<?php

abstract class Printfection_ApiResource extends Printfection_Object {
	protected static function _scopedRetrieve($class, $id, $apiKey=null) {
		$instance = new $class($id, $apiKey);
		$instance->refresh();

		return $instance;
	}

	/**
	 * @returns Printfection_ApiResource The refreshed resource.
	 */
	public function refresh() {
		$requestor = new Printfection_ApiRequestor($this->_apiKey);
		$url = $this->instanceUrl();

		list($response, $apiKey) = $requestor->request('get', $url, $this->_retrieveOptions);
		$this->refreshFrom($response, $apiKey);

		return $this;
	}

	/**
	 * @param string $class
	 *
	 * @returns string The name of the class, with namespacing and underscores
	 *    stripped.
	 */
	public static function className($class) {
		// Useful for namespaces: Foo\Printfection_Charge
		if ($postfixNamespaces = strrchr($class, '\\')) {
			$class = substr($postfixNamespaces, 1);
		}

		// Useful for underscored 'namespaces': Foo_Printfection_Charge
		if ($postfixFakeNamespaces = strrchr($class, 'Printfection_')) {
			$class = $postfixFakeNamespaces;
		}

		if (substr($class, 0, strlen('Printfection')) == 'Printfection') {
			$class = substr($class, strlen('Printfection'));
		}

		$class = str_replace('_', '', $class);
		$name = urlencode($class);
		$name = strtolower($name);

		return $name;
	}

	/**
	 * @param string $class
	 *
	 * @returns string The endpoint URL for the given class.
	 */
	public static function classUrl($class) {
		$base = self::_scopedLsb($class, 'className', $class);

		return "/v2/${base}s";
	}

	/**
	 * @returns string The full API URL for this API resource.
	 */
	public function instanceUrl() {
		$id = $this['id'];
		$class = get_class($this);

		if ($id === null) {
			$message = "Could not determine which URL to request: $class instance has invalid ID: $id";
			
			throw new Printfection_InvalidRequestError($message, null);
		}

		$id = Printfection_ApiRequestor::utf8($id);
		$base = $this->_lsb('classUrl', $class);
		$extn = urlencode($id);

		return "$base/$extn";
	}

	private static function _validateCall($method, $params=null, $apiKey=null) {
		if ($params && !is_array($params)) {
			$message = "You must pass an array as the first argument to Printfection API method calls.";
			
			throw new Printfection_Error($message);
		}

		if ($apiKey && !is_string($apiKey)) {
			$message = 'The second argument to Printfection API method calls is an optional per-request apiKey, which must be a string.';
			
			throw new Printfection_Error($message);
		}
	}

	protected static function _scopedAll($class, $params=null, $apiKey=null) {
		self::_validateCall('all', $params, $apiKey);

		$requestor = new Printfection_ApiRequestor($apiKey);
		$url = self::_scopedLsb($class, 'classUrl', $class);

		list($response, $apiKey) = $requestor->request('get', $url, $params);

		return Printfection_Util::convertToPrintfectionObject($response, $apiKey);
	}

	protected static function _scopedCreate($class, $params=null, $apiKey=null) {
		self::_validateCall('create', $params, $apiKey);

		$requestor = new Printfection_ApiRequestor($apiKey);
		$url = self::_scopedLsb($class, 'classUrl', $class);

		list($response, $apiKey) = $requestor->request('post', $url, $params);

		return Printfection_Util::convertToPrintfectionObject($response, $apiKey);
	}

	protected function _scopedSave($class, $apiKey=null) {
		self::_validateCall('save');
		
		$requestor = new Printfection_ApiRequestor($apiKey);
		$params = $this->serializeParameters();

		if (count($params) > 0) {
			$url = $this->instanceUrl();
			list($response, $apiKey) = $requestor->request('patch', $url, $params);
			$this->refreshFrom($response, $apiKey);
		}
		
		return $this;
	}

	protected function _scopedDelete($class, $params=null) {
		self::_validateCall('delete');

		$requestor = new Printfection_ApiRequestor($this->_apiKey);
		$url = $this->instanceUrl();

		list($response, $apiKey) = $requestor->request('delete', $url, $params);

		$this->refreshFrom($response, $apiKey);

		return $this;
	}
}
