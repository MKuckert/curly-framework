<?php

require_once "CurlyX/Archive/Compiler/Base/Algorithm/Interface.php";

/**
 * CurlyX_Archive_Parser_Base_Algorithm_Compress
 *
 * Dieser Algorithmus dekomprimiert den Dateninhalt.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Parser.Base.Algorithm
 * @since 1.0 - 12.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Parser_Base_Algorithm_Compress implements CurlyX_Archive_Parser_Base_Algorithm_Interface {
	
	const IDENTIFIER=0x4;
	
	/**
	 * Gibt einen Integer zurück, der diesen Algorithmus identifiziert.
	 * 
	 * @return integer
	 */
	public function getIdentifier() {
		return(self::IDENTIFIER);
	}
	
	/**
	 * Gibt die Original-Daten für die übergebenen Daten zurück.
	 * 
	 * @return string
	 * @param string Der verwendete Name des Datensatzes
	 * @param string Die Daten
	 */
	public function getDatasetContents($name, $data) {
		return($this->uncompress($data));
	}
	
	/**
	 * Dekomprimiert die übergebene Zeichenkette.
	 * 
	 * @return string
	 * @param string
	 */
	private function uncompress($data) {
		return(gzinflate($data));
	}
	
	/**
	 * Gibt den für den übergebenen Datensatz zu verwendenden Namen zurück. Diese
	 * Methode entspricht in der Rückgabe der gleichnamigen des
	 * CurlyX_Archive_Compiler_Base_Algorithm_Interface-Interface.
	 * 
	 * @return string
	 * @param string
	 */
	public function getDatasetName($name) {
		return($name);
	}
	
	/**
	 * Gibt einen boolschen Wert zurück, ob die Namen der verfügbaren Einträge
	 * im zurückgegeben werden kann. Dies ist zum Beispiel nicht der Fall, wenn
	 * die Datei verschlüsselt ist.
	 *
	 * @return boolean
	 */
	public function canGetEntryNames() {
		return(true);
	}
	
	/**
	 * Wird aufgerufen, bevor dieser Algorithmus von einem Parser verwendet
	 * wird.
	 * 
	 * @return void
	 * @param CurlyX_Archive_Parser_Base
	 * @param integer Der Identifier, mit dem dieser Algorithmus identifiziert
	 *  wurde.
	 */
	public function addToParser(CurlyX_Archive_Parser_Base $parser, $id) {
		// Nix zu tun
	}
	
}