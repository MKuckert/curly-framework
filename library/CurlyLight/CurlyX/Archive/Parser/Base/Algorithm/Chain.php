<?php

require_once "CurlyX/Archive/Compiler/Base/Algorithm/Interface.php";

/**
 * CurlyX_Archive_Parser_Base_Algorithm_Chain
 *
 * Dieser Algorithmus kombiniert mehrere einzelne Algorithmen
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Parser.Base.Algorithm
 * @since 1.0 - 12.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Parser_Base_Algorithm_Chain implements CurlyX_Archive_Parser_Base_Algorithm_Interface {
	
	const IDENTIFIER=0xFF;
	
	const HEADER_PREFIX='CHAIN[';
	const HEADER_POSTFIX=']';
	
	/**
	 * @var array Die zu verknüpfenden Algorithmen.
	 */
	private $_algorithms=array();
	
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
		foreach($this->_algorithms as $algo) {
			$data=$algo->getDatasetContents($name, $data);
		}
		return($data);
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
		foreach($this->_algorithms as $algo) {
			$name=$algo->getDatasetName($name);
		}
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
		foreach($this->_algorithms as $algo) {
			if(!$algo->canGetEntryNames()) {
				return(false);
			}
		}
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
	public function addToParser(CurlyX_Archive_Parser_Base $parser, $identifier) {
		// Zu verwendende Algorithmen ermitteln
		$header=$parser->additionalHeaderdata;
		
		$prefix=str_replace(array('[', ']'), array('\\[', '\\]'), self::HEADER_PREFIX);
		$postfix=str_replace(array('[', ']'), array('\\[', '\\]'), self::HEADER_POSTFIX);
		if(!preg_match('/'.$prefix.'([0-9,]+)'.$postfix.'/i', $header, $matches)) {
			throw new CurlyX_Archive_Exception('Unable to match the additional header');
		}
		
		$algos=array();
		$ids=explode(',', $matches[1]);
		foreach($ids as $id) {
			$id=$id&0xFF;
			foreach(CurlyX_Archive_Parser_Base_Registry::getRegisteredAlgorithms() as $algo) {
				if($algo->getIdentifier()==$id) {
					$algo=clone $algo;
					$algo->addToParser($parser, $id);
					$algos[]=$algo;
					continue 2;
				}
			}
			throw new CurlyX_Archive_Exception('No algorithm for identifier '.$id.' found');
		}
		
		$this->_algorithms=array_reverse($algos);
	}
	
}