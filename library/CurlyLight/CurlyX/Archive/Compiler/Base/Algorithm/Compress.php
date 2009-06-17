<?php

require_once "CurlyX/Archive/Compiler/Base/Algorithm/Interface.php";

/**
 * CurlyX_Archive_Compiler_Base_Algorithm_Compress
 *
 * Dieser Algorithmus komprimiert den Dateninhalt.
 * 
 * Dieser Algorithmus erfordert die Zlib-Extension von PHP.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Compiler.Base.Algorithm
 * @since 1.0 - 12.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Compiler_Base_Algorithm_Compress implements CurlyX_Archive_Compiler_Base_Algorithm_Interface {
	
	const IDENTIFIER=0x4;
	
	/**
	 * @var CurlyX_Archive_Compiler_Base Compiler, dem dieser Algorithmus zugeordnet ist.
	 */
	private $_compiler=NULL;
	
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
		return($this->compress($dataset->getContents()));
	}
	
	/**
	 * Komprimiert die übergebene Zeichenkette.
	 * 
	 * @return string
	 * @param string
	 */
	private function compress($data) {
		return(gzdeflate($data, 9));
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
		return(strlen($this->getDatasetContents($name, $dataset)));
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