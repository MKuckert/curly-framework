<?php

require_once "Curly/Controller/Action.php";
require_once "Curly/Javascript.php";
require_once "Zend/View/Helper/Doctype.php";

/**
 * CurlyBracketDe_Controller_Base
 * 
 * Basis-Controller
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyBracketDe.Controller
 * @since 1.0 - 27.11.2008
 * @version 1.0
 */
class CurlyBracketDe_Controller_Base extends Curly_Controller_Action {
	
    /**
     * Pre-dispatch Routinen.
     * 
     * @return void
     */
    public function preDispatch() {
    	parent::preDispatch();
    	
    	$this->view->headLink()->appendStylesheet(
    		$this->view->publishScriptFile('assets/css/style.css')
    	);
    	
    	$this->view->headScript()->appendFile(
    		$this->view->publishAsset(Curly_Javascript::getScriptPath('Curly'))
    	);
    	
    	$this->view->headScript()->appendFile(
    		$this->view->publishAsset(Curly_Javascript::getScriptPath('Curly.Typewriter'))
    	);
    	
    	$this->view->headScript()->appendFile(
    		$this->view->publishScriptFile('assets/js/CurlyBracketDe.js')
    	);
    	
    	$this->view->doctype(Zend_View_Helper_Doctype::XHTML1_STRICT);
    }
	
}