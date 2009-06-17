<?php

/**
 * CurlyX_Archive_Parser_Registry
 *
 * Registry für die möglichen Archiv-Parser-Klassen.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Parser
 * @since 1.0 - 29.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Parser_Registry {
	
	/**
	 * @var array Liste der bereits registrierten Parser-Klassen.
	 */
	static private $_registered=array();
	
	/**
	 * Registriert eine weitere Parser-Klasse an dieser Registry.
	 * 
	 * @return void
	 * @param string Name der Klasse
	 */
	static public function register($class) {
		if(in_array($class, self::$_registered, true)) {
			return;
		}
		self::$_registered[]=$class;
	}
	
	/**
	 * De-Registriert eine Parser-Klasse von dieser Registry.
	 * 
	 * @return void
	 * @param string Name der Klasse
	 */
	static public function unregister($class) {
		$index=array_search($class, self::$_registered, true);
		if($index!==false) {
			unset(self::$_registered[$index]);
		}
	}
	
	/**
	 * Gibt alle registrierten Klassen zurück.
	 * 
	 * @return array
	 */
	static public function getRegistered() {
		return(self::$_registered);
	}
	
	/**
	 * Gibt die Parser-Klasse zu der übergebenen Zeichenkette zurück.
	 * 
	 * Diese Methode verwendet die statische containsMagicBytes-Methode der
	 * Parser-Klasse und überprüft alle registrierten Klassen auf
	 * Übereinstimmung.
	 * 
	 * @return string oder NULL, wenn keine Übereinstimmung gefunden wird.
	 * @param string
	 */
	static public function findForMagicBytes($bytes) {
		foreach(self::$_registered as $reg) {
			if(call_user_func(array($reg, 'containsMagicBytes'), $bytes)) {
				return($reg);
			}
		}
		return(NULL);
	}
	
	/**
	 * Gibt die Parser-Klasse zu der übergebenen Datei zurück.
	 * 
	 * Diese Methode greift auf die {@link findForMagicBytes}-Methode zurück.
	 * 
	 * @throws CurlyX_Archive_Exception Wenn ein Fehler mit der Datei auftritt.
	 * @return string oder NULL, wenn keine Übereinstimmung gefunden wird.
	 * @param string
	 */
	static public function findForFile($file) {
		if(!is_file($file)) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('File '.$file.' not found');
		}
		if(!is_readable($file)) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('File '.$file.' is not readable');
		}
		
		// Ersten 32 Bytes auslesen
		$length=min(filesize($file), 32);
		
		$handle=fopen($file, 'r');
		if(!is_resource($handle)) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('Unable to open file '.$file);
		}
		
		$bytes=fread($handle, $length);
		if($bytes===false) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('Unable to read from file '.$file);
		}
		
		fclose($handle);
		
		return(self::findForMagicBytes($bytes));
	}
	
}
