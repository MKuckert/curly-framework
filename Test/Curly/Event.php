<?php
class Test_Curly_Event extends Curly_Test {
	
	private $dispatcher;
	private $dispatchString='';
	private $odds=array();
	private $evens=array();
	
	public function testBase() {
		$this->dispatchString='';
		
		$this->dispatcher=new Test_Curly_Event_Dispatcher();
		$this->dispatcher->addEventListener('odd', array($this, 'onOdd'));
		$this->dispatcher->even=array($this, 'onEven');
		$this->dispatcher->count();
		
		$this->assertEqual($this->dispatchString, 'eoeoeoeoeo', 'DispatchString fehlerhaft');
		$this->assertEqual($this->dispatcher->dispatchString, 'nenonenonenonenoneno', 'DispatchString fehlerhaft');
	}
	
	public function testBase2() {
		$this->dispatchString='';
		
		// onOdd anstatt odd als Eventname verwenden
		$this->dispatcher=new Test_Curly_Event_Dispatcher();
		$this->dispatcher->addEventListener('onOdd', array($this, 'onOdd'));
		$this->dispatcher->onEven=array($this, 'onEven');
		$this->dispatcher->count();
		
		$this->assertEqual($this->dispatchString, 'eoeoeoeoeo', 'DispatchString fehlerhaft');
		$this->assertEqual($this->dispatcher->dispatchString, 'nenonenonenonenoneno', 'DispatchString fehlerhaft');
	}
	
	public function testBase3() {
		$this->dispatchString='';
		
		// onOdd anstatt odd als Eventname verwenden
		$this->dispatcher=new Test_Curly_Event_Dispatcher();
		$this->dispatcher->addEventListener('onOdd', array($this, 'onOdd'));
		$this->dispatcher->onEven=array($this, 'onEven');
		$this->assertTrue($this->dispatcher->hasEventListener('onEven'), 'Erkennt Eventlistener nicht');
		$this->dispatcher->removeEventListener('onEven', array($this, 'onEven'));
		$this->assertFalse($this->dispatcher->hasEventListener('onEven'), 'Erkennt noch Eventlistener');
		$this->dispatcher->count();
		
		$this->assertEqual($this->dispatchString, 'ooooo', 'DispatchString fehlerhaft');
		$this->assertEqual($this->dispatcher->dispatchString, 'nenonenonenonenoneno', 'DispatchString fehlerhaft');
	}
	
	public function onOdd(Test_Curly_Event_Parameter $event) {
		$this->dispatchString.='o';
		$this->odds[]=$event->getNum();
		$this->assertTrue($this->dispatcher===$event->getSource(), 'Eventquelle fehlerhaft');
	}
	
	public function onEven(Test_Curly_Event_Parameter $event) {
		$this->dispatchString.='e';
		$this->evens[]=$event->getNum();
		$this->assertTrue($this->dispatcher===$event->getSource(), 'Eventquelle fehlerhaft');
	}
	
}