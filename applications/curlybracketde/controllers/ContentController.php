<?php
	
/**
 * ContentController
 * 
 * Controller für einfache Inhalte
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyBracketDe.Controller
 * @since 1.0 - 27.11.2008
 * @version 1.0
 */
class ContentController extends CurlyBracketDe_Controller_Base {
	
    /**
     * Zeigt eine Inhaltsseite an.
     *
     * @return void
     * @param string
     * @param array
     */
    public function __call($methodName, $args) {
        if('Action'==substr($methodName, -6)) {
            $this->viewContent(substr($methodName, 0, -6));
            return;
        }
		
        parent::__call($methodName, $args);
    }
    
    /**
     * Zeigt eine Inhaltsseite an.
     * 
     * @return void
     * @param string Name der Seite
     */
    public function viewContent($name) {
    	$table=new CurlyBracketDe_Db_Table_Contents();
    	$rowset=$table->find($name);
    	
    	// Datensatz nicht vorhanden
    	if($rowset->count()<=0) {
    		throw new Zend_Controller_Action_Exception('Content '.$name.' not found', 404);
    	}
    	
    	$this->view->content=$rowset->current();
    	
    	// Action auf index zurücksetzen, um das richtige Skript zu rendern
    	$this->getRequest()->setActionName('index');
    }
	
}