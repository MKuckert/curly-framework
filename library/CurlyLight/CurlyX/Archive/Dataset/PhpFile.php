<?php

require_once "CurlyX/Archive/Dataset/File.php";
require_once "Curly/Sourcecode/Php/Tokens.php";

/**
 * CurlyX_Archive_Dataset_PhpFile
 *
 * Stellt die Daten eines PHP-Skriptes zur Verfügung. Vor der Übernahme des
 * Inhaltes werden unnötige weiße Zeichen und Kommentare aus dem Skript
 * entfernt, so dass der Speicher optimal genutzt werden kann.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Dataset
 * @since 1.0 - 14.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Dataset_PhpFile extends CurlyX_Archive_Dataset_File {
	
	/**
	 * Gibt die Größe der referenzierten Datei zurück.
	 * 
	 * @return integer
	 */
	public function getContentSize() {
		return(strlen($this->getContents()));
	}
	
	/**
	 * Gibt den Inhalt der referenzierten Datei zurück.
	 * 
	 * @return string
	 */
	public function getContents() {
		$tokens=new Curly_Sourcecode_Php_Tokens();
		$tokens->parseFile($this->getPath());
		$tokens->stripWhitespace();
		return($tokens->render());
	}
	
}