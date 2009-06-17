<?php

/**
 * CurlyX_Archive_Compiler_Zip
 *
 * Stellt einen Wrapper für die ZipArchive-Klasse von PHP als Parser-Compiler
 * zur Verfügung.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Compiler
 * @since 1.0 - 29.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Compiler_Zip implements CurlyX_Archive_Compiler_Interface {
	
	/**
	 * @var array Die bereits diesem Compiler hinzugefügten Datensätze.
	 */
	private $_datasets=array();
	
	/**
	 * Fügt diesem Archiv einen Datensatz hinzu.
	 *
	 * @return void
	 * @param CurlyX_Archive_Dataset_Interface
	 */
	public function addDataset(CurlyX_Archive_Dataset_Interface $dataset) {
		$this->_datasets[]=$dataset;
	}
	
	/**
	 * Kompiliert dieses Archiv.
	 * 
	 * @throws CurlyX_Archive_Exception Wenn ein Fehler auftritt.
	 * @return boolean
	 * @param string Dateiname, unter dem das Archiv erstellt werden soll.
	 * @param integer Flags für den Kompiliervorgang.
	 */
	public function compile($file, $flags=0) {
		// Archiv öffnen
		$zip=new ZipArchive();
		$ret=$zip->open($file, ZipArchive::OVERWRITE);
		if($ret!==true) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('Error during opening the zip archive', $ret);
		}
		
		foreach($this->_datasets as $dataset) {
			if(!$zip->addFromString($dataset->getName(), $dataset->getContents())) {
				require_once "CurlyX/Archive/Exception.php";
				throw new CurlyX_Archive_Exception('Failed to add the dataset with name '.$dataset->getName().' to the archive');
			}
		}
		
		$zip->close();
		return(true);
	}
	
}