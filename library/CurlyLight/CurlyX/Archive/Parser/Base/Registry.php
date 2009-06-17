<?php

require_once "CurlyX/Archive/Parser/Base/Algorithm/Plain.php";

/**
 * CurlyX_Archive_Parser_Base_Registry
 * 
 * Registry für Algorithmen, die der Base-Parser verwenden kann.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Parser.Base
 * @since 1.0 - 12.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Parser_Base_Registry {
	
	/**
	 * @var array Registrierte Algorithmen
	 */
	static private $_registered=array();
	
	/**
	 * Registriert einen Algorithmus, so dass der Base-Parser diesen verwenden
	 * kann.
	 * 
	 * @return void
	 * @param CurlyX_Archive_Parser_Base_Algorithm_Interface Der Algorithmus
	 */
	static public function registerAlgorithm(CurlyX_Archive_Parser_Base_Algorithm_Interface $algo) {
		if(!in_array($algo, self::$_registered, true)) {
			self::$_registered[]=$algo;
		}
	}
	
	/**
	 * Entfernt einen registrierten Algorithmus aus dieser Registry.
	 * 
	 * @return void
	 * @param CurlyX_Archive_Parser_Base_Algorithm_Interface Der Algorithmus
	 */
	static public function unregisterAlgorithm(CurlyX_Archive_Parser_Base_Algorithm_Interface $algo) {
		$index=array_search($algo, self::$_registered, true);
		if($index!==false) {
			unset(self::$_registered[$index]);
		}
	}
	
	/**
	 * Gibt die registrierten Algorithmen zurück.
	 * 
	 * @return array
	 */
	static public function getRegisteredAlgorithms() {
		if(count(self::$_registered)<=0) {
			self::$_registered[]=new CurlyX_Archive_Parser_Base_Algorithm_Plain();
			self::$_registered[]=new CurlyX_Archive_Parser_Base_Algorithm_Crypt();
			self::$_registered[]=new CurlyX_Archive_Parser_Base_Algorithm_Compress();
			self::$_registered[]=new CurlyX_Archive_Parser_Base_Algorithm_Chain();
		}
		return(self::$_registered);
	}
	
}