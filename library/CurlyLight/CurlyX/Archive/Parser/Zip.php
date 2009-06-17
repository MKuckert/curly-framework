<?php

require_once "CurlyX/Archive/Parser/Base/Algorithm/Interface.php";

/**
 * CurlyX_Archive_Parser_Zip
 *
 * Diese Archiv-Parser-Klasse kapselt die ZipArchive-Klasse von PHP.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Parser
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Parser_Zip implements CurlyX_Archive_Parser_Interface {
	
	const MAGIC_BYTES="PK\x03\x04";
	
	/**
	 * @var ZipArchive Die gekapselte ZipArchive-Instanz.
	 */
	private $_zip=NULL;
	
	/**
	 * Parst das Archiv mit der übergebenen URL. Diese Methode extrahiert alle
	 * Basisinformationen aus dem Archiv, damit Aufrufe der
	 * {@link load}-Methode folgen können.
	 * 
	 * @throws CurlyX_Archive_Exception Wenn ein Fehler auftritt
	 * @return boolean
	 * @param string
	 */
	public function parse($file) {
		$this->_zip=new ZipArchive();
		$ret=$this->_zip->open($file);
		if($ret!==true) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('Error during opening the zip archive', $ret);
		}
		return(true);
	}
	
	/**
	 * Lädt den Eintrag mit dem übergebenen Namen aus dem Archiv und gibt den
	 * zugehörigen Wert zurück.
	 * 
	 * @throws CurlyX_Archive_Exception Wenn der Eintrag nicht vorhanden ist.
	 * @return string
	 * @param string
	 */
	public function load($name) {
		$content=$this->_zip->getFromName($name);
		if($content===false) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('Entry '.$name.' not found');
		}
		
		return($content);
	}
	
	/**
	 * Gibt einen boolschen Wert zurück, ob die Namen der verfügbaren Einträge
	 * im zurückgegeben werden kann. Für diese Klasse ist dies immer false.
	 *
	 * @return boolean
	 */
	public function canGetEntryNames() {
		return(false);
	}
	
	/**
	 * Gibt ein Array mit den Namen der verfügbaren Einträge zurück.
	 *
	 * @return array
	 */
	public function getEntryNames() {
		require_once "CurlyX/Archive/Exception.php";
		throw new CurlyX_Archive_Exception('Unable to get the entry names.');
	}
	
	/**
	 * Überprüft, ob die übergebene Zeichenkette den Magic Bytes dieser
	 * Parser-Klasse entspricht.
	 * 
	 * Diese Methode wird von der Parser-Registry verwendet und erhält als
	 * Parameter die vordersten 32 Bytes einer Archiv-Datei.
	 * 
	 * @return boolean
	 * @param string
	 */
	static public function containsMagicBytes($bytes) {
		return(substr($bytes, 0, strlen(self::MAGIC_BYTES))==self::MAGIC_BYTES);
	}
	
}
