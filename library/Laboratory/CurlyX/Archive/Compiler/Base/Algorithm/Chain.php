<?php

require_once "CurlyX/Archive/Compiler/Base/Algorithm/Interface.php";

/**
 * CurlyX_Archive_Compiler_Base_Algorithm_Chain
 *
 * Dieser Algorithmus verknüpft eine Reihe einzelner Algorithmen
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Compiler.Base.Algorithm
 * @since 1.0 - 13.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Compiler_Base_Algorithm_Chain implements CurlyX_Archive_Compiler_Base_Algorithm_Interface {
	
	const IDENTIFIER=0xFF;
	
	const HEADER_PREFIX='CHAIN[';
	const HEADER_POSTFIX=']';
	
	/**
	 * @var array Die verknüpften Algorithmen
	 */
	private $_algos=array();
	
	/**
	 * @var CurlyX_Archive_Compiler_Base Der Compiler, zu dem dieser
	 *  Algorithmus gehört.
	 */
	private $_compiler=NULL;
	
	/**
	 * Gibt die verknüpften Algorithmen zurück.
	 * 
	 * @return array
	 */
	public function getAlgorithms() {
		return($this->_algos);
	}
	
	/**
	 * Konstruktor
	 * 
	 * @throws CurlyX_Archive_Exception Wenn einer der übergebenen Algorithmen
	 *  nicht das nötige Interface implementiert.
	 * @param CurlyX_Archive_Compiler_Base Der Compiler, zu dem dieser
	 *  Algorithmus gehört.
	 * @param array Die zu verwendenden Algorithmen
	 */
	public function __construct(CurlyX_Archive_Compiler_Base $compiler, array $args) {
		foreach($args as $algo) {
			if(!($algo instanceof CurlyX_Archive_Compiler_Base_Algorithm_Interface)) {
				throw new CurlyX_Archive_Exception('One of the given algorithms does not implement the CurlyX_Archive_Compiler_Base_Algorithm_Interface interface');
			}
			$this->_algos[]=$algo;
		}
		$this->_compiler=$compiler;
		
		$ids=array();
		// Identifier in die Header hängen
		foreach($this->_algos as $algo) {
			$ids[]=$algo->getIdentifier();
		}
		$this->_compiler->addAdditionalHeaderdata(self::HEADER_PREFIX.implode(',', $ids).self::HEADER_POSTFIX);
	}
	
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
		// Daten in eigenen Datensatz übernehmen
		$ownDataset=new CurlyX_Archive_Dataset_String($dataset->getName(), $dataset->getContents());
		
		foreach($this->_algos as $algo) {
			$ownDataset->setContents($algo->getDatasetContents($name, $ownDataset));
		}
		
		return($ownDataset->getContents());
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
		// Daten in eigenen Datensatz übernehmen
		$ownDataset=new CurlyX_Archive_Dataset_String($dataset->getName(), $dataset->getContents());
		
		foreach($this->_algos as $algo) {
			$ownDataset->setName($algo->getDatasetName($ownDataset));
		}
		
		return($ownDataset->getName());
	}
	
}