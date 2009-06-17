<?php

class Test_Curly_Types_Delegate extends Curly_Test {
	
	private $counter=0;
	
	public function testFunction() {
		$delegate=new Curly_Types_Delegate();
		$delegate->add('trim');
		$delegate->add('strtolower');
		$delegate->add('strtoupper');
		
		$input="  fOObAr\t";
		
		$retval=$delegate->invoke($input);
		$shouldBe=array(
			trim($input),
			strtolower($input),
			strtoupper($input)
		);
		
		$this->assertEqual($retval, $shouldBe, 'Rückgabewert ist fehlerhaft');
	}
	
	public function testMethod() {
		$delegate=new Curly_Types_Delegate();
		$delegate->add($this, 'incrementCounter');
		$delegate->add($this, 'incrementCounter');
		$delegate->add($this, 'incrementCounter');
		
		$retval=$delegate->invoke();
		$shouldBe=array( 0, 1, 2 );
		
		$this->assertEqual($retval, $shouldBe, 'Rückgabewert ist fehlerhaft');
		$this->assertEqual($this->counter, 3, 'Aufrufe fehlerhaft');
	}
	
	public function testCallbackDelegate() {
		$this->counter=0;
		
		$delegate=new Curly_Types_Delegate();
		$delegate->add(new Curly_Types_Callback($this, 'incrementCounter'));
		
		$retval=$delegate->invoke();
		$shouldBe=array( 0 );
		
		$this->assertEqual($retval, $shouldBe, 'Rückgabewert ist fehlerhaft');
		$this->assertEqual($this->counter, 1, 'Aufrufe fehlerhaft');
	}
	
	public function testNested() {
		$delegate=new Curly_Types_Delegate();
		$delegate->add($this, 'incrementCounter');
		$delegate->add($this, 'checkCounter');
		$delegate->add($delegate);
		
		$this->counter=0;
		
		try {
			$delegate->invoke();
			$this->failure('Diese Stelle sollte niemals erreicht werden!');
		}
		catch(Exception $ex) {
			$this->assertEqual($this->counter, 10, 'Counter fehlerhaft');
		}
	}
	
	public function incrementCounter() {
		return($this->counter++);
	}
	
	public function checkCounter() {
		if($this->counter>=10) {
			throw new Exception('STOP');
		}
	}
	
}