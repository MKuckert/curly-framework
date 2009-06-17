<?php

require_once "CurlyX/Archive/Compiler/Base/Algorithm/Interface.php";

/**
 * CurlyX_Archive_Compiler_Base_Algorithm_Plain
 *
 * Ein einfacher Algorithmus, der den Archiv-Datensatz nicht weiter modifiziert.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Compiler.Base.Algorithm
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Compiler_Base_Algorithm_Plain implements CurlyX_Archive_Compiler_Base_Algorithm_Interface {
	
	const IDENTIFIER=0x1;
	
	/**
	 * Gibt einen Integer zurück, der diesen Algorithmus identifiziert.
	 * 
	 * @return integer
	 */
	public function getIdentifier() {
		return(self::IDENTIFIER);
	}
	
	/**
	 * Gibt die zu verwendenden Daten für den übergebenen Datensatz zurück.
	 * 
	 * @return string
	 * @param string Der verwendete Name des Datensatzes
	 * @param CurlyX_Archive_Dataset_Interface Der Datensatz
	 */
	public function getDatasetContents($name, CurlyX_Archive_Dataset_Interface $dataset) {
		return($dataset->getContents());
	}
	
	/**
	 * Gibt die Anzahl Bytes zurück, die ein Aufruf von
	 * {@link getDatasetContents} zurückgegeben würde.
	 * 
	 * @return integer
	 * @param string Der verwendete Name des Datensatzes
	 * @param CurlyX_Archive_Dataset_Interface Der verwendete Datensatz
	 */
	public function getDatasetSize($name, CurlyX_Archive_Dataset_Interface $dataset) {
		return($dataset->getContentSize());
	}
	
	/**
	 * Gibt den für den übergebenen Datensatz zu verwendenden Namen zurück.
	 * 
	 * @return string
	 * @param CurlyX_Archive_Dataset_Interface
	 */
	public function getDatasetName(CurlyX_Archive_Dataset_Interface $dataset) {
		return($dataset->getName());
	}
	
}