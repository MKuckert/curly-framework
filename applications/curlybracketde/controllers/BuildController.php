<?php

require_once "Curly/Controller/Action.php";
require_once "Zend/View/Helper/Doctype.php";
require_once "Zend/Registry.php";
require_once "CurlyBracketDe/Page/Builder.php";

class BuildController extends Curly_Controller_Action {
	
    /**
     * Pre-dispatch Routinen.
     * 
     * @return void
     */
    public function preDispatch() {
    	$this->view->doctype(Zend_View_Helper_Doctype::XHTML1_STRICT);
    	parent::preDispatch();
    }
	
	public function indexAction() {
		$builder=new CurlyBracketDe_Page_Builder();
		$builder->setConfig(Zend_Registry::get('config')->builder);
		$builder->build();
	}
	
}