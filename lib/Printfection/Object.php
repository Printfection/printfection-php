<?php

class Printfection_Object implements ArrayAccess {
	/**
	 * @var Printfection_Util_Set Attributes that should not be sent to the API because
	 *    they're not updatable (e.g. API key, ID).
	 */
	public static $permanentAttributes;

	public static function init() {
		self::$permanentAttributes = new Printfection_Util_Set(array('_apiKey', 'id'));
	}

	protected $_apiKey;
	protected $_values;
	protected $_unsavedValues;
	protected $_transientValues;
	protected $_retrieveOptions;

	public function __construct($id=null, $apiKey=null) {
		$this->_apiKey = $apiKey;
		$this->_values = array();
		$this->_unsavedValues = new Printfection_Util_Set();
		$this->_transientValues = new Printfection_Util_Set();

		$this->_retrieveOptions = array();

		if (is_array($id)) {
			foreach ($id as $key => $value) {
				if ($key != 'id') {
					$this->_retrieveOptions[$key] = $value;
				}
			}

			$id = $id['id'];
		}

		if ($id !== null) {
			$this->id = $id;
		}
	}

	// Standard accessor magic methods
	public function __set($k, $v) {
		if ($v === "") {
			throw new InvalidArgumentException('You cannot set \''.$k.'\'to an empty string. We interpret empty strings as NULL in requests. You may set obj->'.$k.' = NULL to delete the property');
		}

		// TODO: may want to clear from $_transientValues (Won't be user-visible).
		$this->_values[$k] = $v;

		if (!self::$permanentAttributes->includes($k)) {
			$this->_unsavedValues->add($k);
		}
	}

	public function __isset($k) {
		return isset($this->_values[$k]);
	}

	public function __unset($k) {
		unset($this->_values[$k]);

		$this->_transientValues->add($k);
		$this->_unsavedValues->discard($k);
	}

	public function __get($k) {
		if (array_key_exists($k, $this->_values)) {
			return $this->_values[$k];
		} else if ($this->_transientValues->includes($k)) {
			$class = get_class($this);
			$attrs = join(', ', array_keys($this->_values));
			$message = "Printfection Notice: Undefined property of $class instance: $k.";

			error_log($message);

			return null;
		} else {
			$class = get_class($this);

			error_log("Printfection Notice: Undefined property of $class instance: $k");

			return null;
		}
	}

	// ArrayAccess methods
	public function offsetSet($k, $v) {
		$this->$k = $v;
	}

	public function offsetExists($k) {
		return array_key_exists($k, $this->_values);
	}

	public function offsetUnset($k) {
		unset($this->$k);
	}

	public function offsetGet($k) {
		return array_key_exists($k, $this->_values) ? $this->_values[$k] : null;
	}

	public function keys() {
		return array_keys($this->_values);
	}

	/**
	 * This unfortunately needs to be public to be used in Util.php
	 *
	 * @param string $class
	 * @param array $values
	 * @param string|null $apiKey
	 *
	 * @return Printfection_Object The object constructed from the given values.
	 */
	public static function scopedConstructFrom($class, $values, $apiKey=null) {
		$obj = new $class(isset($values['id']) ? $values['id'] : null, $apiKey);
		$obj->refreshFrom($values, $apiKey);

		return $obj;
	}

	/**
	 * @param array $values
	 * @param string|null $apiKey
	 *
	 * @return Printfection_Object The object of the same class as $this constructed
	 *    from the given values.
	 */
	public static function constructFrom($values, $apiKey=null) {
		return self::scopedConstructFrom(__CLASS__, $values, $apiKey);
	}

	/**
	 * Refreshes this object using the provided values.
	 *
	 * @param array $values
	 * @param string $apiKey
	 * @param boolean $partial Defaults to false.
	 */
	public function refreshFrom($values, $apiKey, $partial=false) {
		$this->_apiKey = $apiKey;

		// Wipe old state before setting new.  This is useful for e.g. updating a
		// customer, where there is no persistent card parameter.  Mark those values
		// which don't persist as transient
		if ($partial) {
			$removed = new Printfection_Util_Set();
		} else {
			$removed = array_diff(array_keys($this->_values), array_keys($values));
		}

		foreach ($removed as $k) {
			if (self::$permanentAttributes->includes($k)){
				continue;
			}

			unset($this->$k);
		}

		foreach ($values as $k => $v) {
			if (self::$permanentAttributes->includes($k) && isset($this[$k])){
				continue;
			}

			$this->_values[$k] = Printfection_Util::convertToPrintfectionObject($v, $apiKey);

			$this->_transientValues->discard($k);
			$this->_unsavedValues->discard($k);
		}
	}

	/**
	 * @return array A recursive mapping of attributes to values for this object,
	 *    including the proper value for deleted attributes.
	 */
	public function serializeParameters() {
		$params = array();

		if ($this->_unsavedValues) {
			foreach ($this->_unsavedValues->toArray() as $k) {
				$v = $this->$k;
				
				if ($v === NULL) {
					$v = '';
				}
				
				$params[$k] = $v;
			}
		}

		return $params;
	}

	// Pretend to have late static bindings, even in PHP 5.2
	protected function _lsb($method) {
		$class = get_class($this);
		$args = array_slice(func_get_args(), 1);

		return call_user_func_array(array($class, $method), $args);
	}

	protected static function _scopedLsb($class, $method) {
		$args = array_slice(func_get_args(), 2);

		return call_user_func_array(array($class, $method), $args);
	}

	public function __toJSON() {
		if (defined('JSON_PRETTY_PRINT')) {
			return json_encode($this->__toArray(true), JSON_PRETTY_PRINT);
		} else {
			return json_encode($this->__toArray(true));
		}
	}

	public function __toString() {
		$class = get_class($this);

		return $class . ' JSON: ' . $this->__toJSON();
	}

	public function __toArray($recursive=false) {
		if ($recursive) {
			return Printfection_Util::convertPrintfectionObjectToArray($this->_values);
		} else {
			return $this->_values;
		}
	}
}

Printfection_Object::init();
