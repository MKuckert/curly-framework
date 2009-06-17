<?php

class Test_Curly_Types_Callback extends Curly_Test {
	
	public function testFunction() {
		$callback=new Curly_Types_Callback('trim');
		
		$input='   fooo   ';
		$this->assertEqual(trim($input), $callback->invoke($input), 'Callback fehlerhaft ausgeführt');
	}
	
	public function testMethod() {
		$callback=new Curly_Types_Callback($this, 'rotate');
		
		$input='foobar';
		$this->assertEqual(strrev($input), $callback->invoke($input, $this), 'Callback fehlerhaft ausgeführt');
		
		$callback=new Curly_Types_Callback(array($this, 'rotate'));
		$this->assertEqual(strrev($input), $callback->invoke($input, $this), 'Callback fehlerhaft ausgeführt');
	}
	
	public function testStaticMethod() {
		$callback=new Curly_Types_Callback(get_class($this), 'toUpper');
		
		$input='foobar';
		$this->assertEqual(strtoupper($input), $callback->invoke($input), 'Callback fehlerhaft ausgeführt');
		
		$callback=new Curly_Types_Callback(array(get_class($this), 'toUpper'));
		$this->assertEqual(strtoupper($input), $callback->invoke($input), 'Callback fehlerhaft ausgeführt');
	}
	
	public function rotate($input, $inst) {
		$this->assertStrictEqual($inst, $this, 'Callback hat Objektreferenz verloren');
		return(strrev($input));
	}
	
	static public function toUpper($input) {
		return(strtoupper($input));
	}
	
}