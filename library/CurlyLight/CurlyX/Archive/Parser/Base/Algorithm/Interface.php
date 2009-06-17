<?php

/**
 * CurlyX_Archive_Parser_Base_Algorithm_Interface
 *
 * Interface für einen Algorithmus, der in der
 * CurlyX_Archive_Parser_Base-Klasse verwendet werden kann.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Parser.Base.Algorithm
 * @since 1.0 - 12.12.2008
 * @version 1.0
 */
interface CurlyX_Archive_Parser_Base_Algorithm_Interface {
	
	/**
	 * Gibt einen Integer zurück, der diesen Algorithmus identifiziert.
	 * 
	 * @return integer
	 */
	public function getIdentifier();
	
	/**
	 * Gibt die Original-Daten für die übergebenen Daten zurück.
	 * 
	 * @return string
	 * @param string Der verwendete Name des Datensatzes
	 * @param string Die Daten
	 */
	public function getDatasetContents($name, $data);
	
	/**
	 * Gibt den für den übergebenen Namen zu verwendenden Namen zurück. Diese
	 * Methode entspricht in der Rückgabe der gleichnamigen des
	 * CurlyX_Archive_Compiler_Base_Algorithm_Interface-Interface.
	 * 
	 * @return string
	 * @param string
	 */
	public function getDatasetName($name);
	
	/**
	 * Gibt einen boolschen Wert zurück, ob die Namen der verfügbaren Einträge
	 * im zurückgegeben werden kann. Dies ist zum Beispiel nicht der Fall, wenn
	 * die Datei verschlüsselt ist.
	 *
	 * @return boolean
	 */
	public function canGetEntryNames();
	
	/**
	 * Wird aufgerufen, bevor dieser Algorithmus von einem Parser verwendet
	 * wird.
	 * 
	 * @return void
	 * @param CurlyX_Archive_Parser_Base
	 * @param integer Der Identifier, mit dem dieser Algorithmus identifiziert
	 *  wurde.
	 */
	public function addToParser(CurlyX_Archive_Parser_Base $parser, $id);
	
}