<?php
class Printfection_List extends Printfection_Object {
	private $_limit = 100;
	private $_offset = 0;

	public function all($params=null) {
		list($url, $params) = $this->extractPathAndUpdateParams($params);

		$requestor = new Printfection_ApiRequestor($this->_apiKey);
		list($response, $apiKey) = $requestor->request('get', $url, $params);

		return Printfection_Util::convertToPrintfectionObject($response, $apiKey);
	}

	public function retrieve($id, $params=null) {
		list($url, $params) = $this->extractPathAndUpdateParams($params);

		$requestor = new Printfection_ApiRequestor($this->_apiKey);
		$id = Printfection_ApiRequestor::utf8($id);
		$extn = urlencode($id);

		list($response, $apiKey) = $requestor->request('get', "$url/$extn", $params);

		return Printfection_Util::convertToPrintfectionObject($response, $apiKey);
	}

	public function next() {
		list($url, $params) = $this->extractPathAndUpdateParams($params);

		$requestor = new Printfection_ApiRequestor($this->_apiKey);
		$id = Printfection_ApiRequestor::utf8($id);
		$extn = urlencode($id);

		$params['limit'] = isset($params['limit']) ? $params['limit'] : 100;
		$params['offset'] = isset($params['offset']) ? $params['offset'] + $params['limit'] : $params['limit'];

		list($response, $apiKey) = $requestor->request('get', "$url/$extn", $params);

		return Printfection_Util::convertToPrintfectionObject($response, $apiKey);
	}

	private function extractPathAndUpdateParams($params) {
		$url = parse_url($this->url);

		if (!isset($url['path'])) {
			throw new Printfection_APIError("Could not parse list url into parts: $url");
		}

		if (isset($url['query'])) {
			// If the URL contains a query param, parse it out into $params so they
			// don't interact weirdly with each other.
			$query = array();
			parse_str($url['query'], $query);

			// PHP 5.2 doesn't support the ?: operator :(
			$params = array_merge($params ? $params : array(), $query);
		}
		
		return array($url['path'], $params);
	}
}