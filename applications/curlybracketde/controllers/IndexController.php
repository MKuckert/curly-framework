<?php
	
	class IndexController extends CurlyBracketDe_Controller_Base {
		
		public function indexAction() {
			$this->_forward('Home', 'Content');
		}
		
	}
	
?>