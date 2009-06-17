<?php

require_once "Zend/Controller/Action.php";

/**
 * Curly.Controller
 * 
 * Basis-Controller
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Controller
 * @since 1.0 - 28.11.2008
 * @version 1.0
 */
class Curly_Controller_Action extends Zend_Controller_Action {
	
    /**
     * Pre-dispatch Routinen.
     * 
     * @return void
     */
    public function preDispatch() {
    	$this->view->addHelperPath(INCLUDEPATH_CURLY.'/Curly/View/Helper/', 'Curly_View_Helper');
    	
    	$request=$this->getRequest();
    	$this->view->httpHost=$request->getHttpHost();
    	$this->view->protocol=$request->isSecure()?'https':'http';
    	$this->view->baseUrl=$request->getBaseUrl();
    	
    	parent::preDispatch();
    }
	
}