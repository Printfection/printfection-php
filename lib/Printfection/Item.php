<?php

class Printfection_Item extends Printfection_ApiResource {
	/**
	 * @param string $id The ID of the item to retrieve.
	 * @param string|null $apiKey
	 *
	 * @return Printfection_Item
	 */
	public static function retrieve($id, $apiKey=null) {
		$class = get_class();
		
		return self::_scopedRetrieve($class, $id, $apiKey);
	}

	/**
	 * @param array|null $params
	 * @param string|null $apiKey
	 *
	 * @return array An array of Printfection_Items.
	 */
	public static function all($params=null, $apiKey=null) {
		$class = get_class();
		
		return self::_scopedAll($class, $params, $apiKey);
	}
}
