<?php

/**
 * CurlyX_Db_Exception
 * 
 * Ausnahmen-Klasse für das CurlyX.Db-Paket
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
class CurlyX_Db_Exception extends Exception {
	
	/**
	 * @var string Fehlerinformationen
	 */
	public $errorInfo;
	
	/**
	 * Konstruktor
	 * 
	 * @param string Fehlernachricht
	 * @param integer Fehlercode
	 * @param array Fehlerinformationen
	 */
	public function __construct($message, $code=0, array $info=NULL) {
		parent::__construct($message, $code);
		
		if($info===NULL) {
			$info=array(
				'02000',
				0,
				''
			);
		}
		$this->errorInfo=$info;
	}
	
}