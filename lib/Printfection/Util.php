<?php

abstract class Printfection_Util {
	/**
	 * Whether the provided array (or other) is a list rather than a dictionary.
	 *
	 * @param array|mixed $array
	 * @return boolean True if the given object is a list.
	 */
	public static function isList($array) {
		if (!is_array($array)) {
			return false;
		}

		// TODO: generally incorrect, but it's correct given Printfection's response
		foreach (array_keys($array) as $k) {
			if (!is_numeric($k)) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Recursively converts the PHP Printfection object to an array.
	 *
	 * @param array $values The PHP Printfection object to convert.
	 * @return array
	 */
	public static function convertPrintfectionObjectToArray($values) {
		$results = array();

		foreach ($values as $k => $v) {
			// FIXME: this is an encapsulation violation
			if ($k[0] == '_') {
				continue;
			}

			if ($v instanceof Printfection_Object) {
				$results[$k] = $v->__toArray(true);
			} else if (is_array($v)) {
				$results[$k] = self::convertPrintfectionObjectToArray($v);
			} else {
				$results[$k] = $v;
			}
		}

		return $results;
	}

	/**
	 * Converts a response from the Printfection API to the corresponding PHP object.
	 *
	 * @param array $resp The response from the Printfection API.
	 * @param string $apiKey
	 * @return Printfection_Object|array
	 */
	public static function convertToPrintfectionObject($resp, $apiKey) {
		$types = array(
			'campaign' => 'Printfection_Campaign',
			'image' => 'Printfection_Image',
			'item' => 'Printfection_Item',
			'lineitem' => 'Printfection_LineItem',
			'list' => 'Printfection_List',
			'order' => 'Printfection_Order',
			'size' => 'Printfection_Size',
		);

		if (self::isList($resp)) {
			$mapped = array();
			
			foreach ($resp as $i){
				array_push($mapped, self::convertToPrintfectionObject($i, $apiKey));
			}

			return $mapped;
		} else if (is_array($resp)) {
			if (isset($resp['object']) && is_string($resp['object']) && isset($types[$resp['object']])) {
				$class = $types[$resp['object']];
			} else {
				$class = 'Printfection_Object';
			}

			return Printfection_Object::scopedConstructFrom($class, $resp, $apiKey);
		} else {
			return $resp;
		}
	}
}
