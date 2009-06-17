<?php
	
	class ErrorController extends Zend_Controller_Action {
		
		public function errorAction() {
			$exceptions=$this->getResponse()->getException();
			$c=count($exceptions);
			
			if($c>0) {
				$lastException=$exceptions[$c-1];
				
				$this->getResponse()->setHttpResponseCode($lastException->getCode());
				
				$this->view->httpCode=$lastException->getCode();
				$this->view->httpMessage=$lastException->getMessage();
			}
		}
		
	}
	
?>