<?php

class Test_Curly_Types_DummyObj2 {
	public $value;
	public function __construct($value) {
		$this->value=$value;
	}
	public function __toString() {
		return((string)$this->value);
	}
}