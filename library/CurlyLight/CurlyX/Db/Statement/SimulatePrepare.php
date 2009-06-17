<?php

require_once "CurlyX/Db/Statement/Abstract.php";
require_once "CurlyX/Db/Statement/Parser.php";
require_once "Curly/Types/String.php";

/**
 * CurlyX_Db_Statement_SimulatePrepare
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Statement
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
abstract class CurlyX_Db_Statement_SimulatePrepare extends CurlyX_Db_Statement_Abstract {
	
	/**
	 * @var string Aktuell vorbereitete Query
	 */
	private $_query=NULL;
	
	/**
	 * Führt dieses Statement aus.
	 * 
	 * @return boolean
	 * @param array Eingabeparameter
	 */
	public function execute(array $params=array()) {
		foreach($params as $name=>$param) {
			$this->bindParam($name, $param);
		}
		
		$this->prepareQuery();
		return $this->sendQuery($this->_query);
	}
	
	/**
	 * Simuliert das Binden der Parameter an die Query.
	 * 
	 * @return void
	 */
	protected function prepareQuery() {
		// Statement parsen
		$parser=new CurlyX_Db_Statement_Parser($this->queryString);
		$parser->parse();
		
		$this->_query=$this->queryString;
		
		// Die gefundene Parameter in umgekehrter Reihenfolge verarbeiten, um die Indizes korrekt zu halten
		$params=array_reverse($parser->getParams());
		
		foreach($params as $param) {
			$index=$param[0];
			$paramName=$param[1];
			
			// Gebundener Parameter
			if(isset($this->boundParams[$paramName])) {
				$this->insertValue($index, $this->boundParams[$paramName]);
			}
			// Gebunderer Wert
			else if(isset($this->boundValues[$paramName])) {
				$this->insertValue($index, $this->boundValues[$paramName]);
			}
			// Nicht gebunden
			else {
				throw new CurlyX_Db_Exception('No value or parameter bound for parameter '.$paramName);
			}
		}
	}
	
	/**
	 * Fügt einen Wert in das simulierte Statement ein
	 * 
	 * @return void
	 * @param integer Die Indexposition, an der der Wert eingefügt werden soll.
	 * @param array Der eigentliche Parameter
	 */
	private function insertValue($index, $param) {
		// Länge des herauszuschneidenden Teils
		$name=$param[0];
		if(is_int($name)) {
			$remove=1;
		}
		else {
			$remove=strlen($name);
		}
		
		// Typ
		$type=$param[2];
		
		// Input-Output extrahieren (nur für fetch wichtig)
		if(($type&CurlyX_Db_Connection_Interface::PARAM_INPUT_OUTPUT)==CurlyX_Db_Connection_Interface::PARAM_INPUT_OUTPUT) {
			$type^=CurlyX_Db_Connection_Interface::PARAM_INPUT_OUTPUT;
		}
		
		// Wert maskieren
		$value=$this->driver->quote($param[1], $type);
		
		$this->_query=substr($this->_query, 0, $index).$value.substr($this->_query, $index+$remove);
	}
	
	/**
	 * Sendet die simulierte SQL-Query.
	 * 
	 * @return boolean
	 */
	abstract protected function sendQuery($query);
	
}