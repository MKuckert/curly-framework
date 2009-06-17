<?php

require_once "Curly/Event/Dispatcher.php";

class Test_Curly_Event_Dispatcher extends Curly_Event_Dispatcher {
	
	/**
	 * @var string
	 */
	public $dispatchString='';
	
	/**
	 * @return boolean
	 * @param Test_Curly_Event_Parameter
	 */
	protected function onNumber(Test_Curly_Event_Parameter $event) {
		$this->dispatchString.='n';
		
		if(($event->getNum()%2)==0) {
			$this->dispatchEvent('even', $event);
		}
		else {
			$this->dispatchEvent('odd', $event);
		}
		
		return true;
	}
	
	/**
	 * @return boolean
	 * @param Test_Curly_Event_Parameter
	 */
	protected function onOdd(Test_Curly_Event_Parameter $event) {
		$this->dispatchString.='o';
		return true;
	}
	
	/**
	 * @return boolean
	 * @param Test_Curly_Event_Parameter
	 */
	protected function onEven(Test_Curly_Event_Parameter $event) {
		$this->dispatchString.='e';
		return true;
	}
	
	/**
	 * @return void
	 */
	public function count($n=10) {
		for($i=0; $i<$n; $i++) {
			$this->dispatchEvent('number', new Test_Curly_Event_Parameter($this, $i));
		}
	}
	
}