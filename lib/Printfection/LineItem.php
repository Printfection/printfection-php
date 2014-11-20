<?php

class Printfection_LineItem extends Printfection_ApiResource {
	/**
	 * @param array|null $params
	 * @param string|null $apiKey
	 *
	 * @return Printfection_LineItem The created lineitem.
	 */
	public static function create($params=null, $apiKey=null) {
		$class = get_class();
		
		return self::_scopedCreate($class, $params, $apiKey);
	}

	/**
	 * @param array|null $params
	 *
	 * @return Printfection_LineItem The deleted lineitem.
	 */
	public function delete($params=null) {
		$class = get_class();
		
		return self::_scopedDelete($class, $params);
	}

	/**
   	 * @return Printfection_LineItem The saved lineitem.
   	 */
	public function save() {
		$class = get_class();
		
		return self::_scopedSave($class);
	}

	/**
	 * @return string The instance URL for this resource. It needs to be special
	 *    cased because it doesn't fit into the standard resource pattern.
	 */
	public function instanceUrl() {
		$id = $this['id'];

		if (!$id) {
			$class = get_class($this);
			$msg = "Could not determine which URL to request: $class instance has invalid ID: $id";

			throw new Printfection_InvalidRequestError($msg, null);
		}

		$parent = $this['order_id'];
		$base = self::classUrl('Printfection_Order');

		$parent = Printfection_ApiRequestor::utf8($parent);
		$id = Printfection_ApiRequestor::utf8($id);
		$parentExtn = urlencode($parent);
		$extn = urlencode($id);

		$class = get_class($this);
		$self = self::_scopedLsb($class, 'className', $class);

		return $base . "/" . $parentExtn . "/" . $self . "s/" . $extn;
	}
}
