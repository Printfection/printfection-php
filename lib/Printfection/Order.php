<?php

class Printfection_Order extends Printfection_ApiResource {
	/**
	 * @param array|null $params
	 * @param string|null $apiKey
	 *
	 * @return Printfection_Order The created order.
	 */
	public static function create($params=null, $apiKey=null) {
		$class = get_class();

		return self::_scopedCreate($class, $params, $apiKey);
	}

	/**
	 * @param array|null $params
	 *
	 * @return Printfection_Order The placed order.
	 */
	public function place() {
		$requestor = new Printfection_ApiRequestor($this->_apiKey);
		$url = $this->instanceUrl() . '/place';

		list($response, $apiKey) = $requestor->request('post', $url);
		$this->refreshFrom($response, $apiKey);

		return $this;
	}

	/**
	 * @param string $id The ID of the order to retrieve.
	 * @param string|null $apiKey
	 *
	 * @return Printfection_Order
	 */
	public static function retrieve($id, $apiKey=null) {
		$class = get_class();

		return self::_scopedRetrieve($class, $id, $apiKey);
	}

	/**
	 * @param array|null $params
	 * @param string|null $apiKey
	 *
	 * @return array An array of Printfection_Orders.
	 */
	public static function all($params=null, $apiKey=null) {
		$class = get_class();

		return self::_scopedAll($class, $params, $apiKey);
	}

	/**
	 * @param array|null $params
	 *
	 * @return Printfection_Order The deleted order.
	 */
	public function delete($params=null) {
		$class = get_class();

		return self::_scopedDelete($class, $params);
	}

	/**
   	 * @return Printfection_Order The saved order.
   	 */
	public function save() {
		$class = get_class();

		return self::_scopedSave($class);
	}
}
