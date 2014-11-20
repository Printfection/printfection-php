<?php

class Printfection_Campaign extends Printfection_ApiResource {
	/**
	 * @param string $id The ID of the campaign to retrieve.
	 * @param string|null $apiKey
	 *
	 * @return Printfection_Campaign
	 */
	public static function retrieve($id, $apiKey=null) {
		$class = get_class();
		
		return self::_scopedRetrieve($class, $id, $apiKey);
	}

	/**
	 * @param array|null $params
	 * @param string|null $apiKey
	 *
	 * @return array An array of Printfection_Campaigns.
	 */
	public static function all($params=null, $apiKey=null) {
		$class = get_class();
		
		return self::_scopedAll($class, $params, $apiKey);
	}
}
