<?php

class Test_Curly_Types_DummyObj1 {
	public $value;
	public function __construct($value) {
		$this->value=$value;
	}
	public function toString() {
		return((string)$this->value);
	}
}