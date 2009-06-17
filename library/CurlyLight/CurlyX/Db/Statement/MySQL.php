<?php

require_once "CurlyX/Db/Statement/SimulatePrepare.php";

/**
 * CurlyX_Db_Statement_MySQL
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Statement
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
class CurlyX_Db_Statement_MySQL extends CurlyX_Db_Statement_SimulatePrepare {
	
	/**
	 * @var resource Result-Handle
	 */
	private $resultHandle=NULL;
	
	/**
	 * @todo
	 */
	protected function fetchAssoc() {
		return mysql_fetch_assoc($this->resultHandle);
	}
	
	/**
	 * Gibt die Anzahl beeinflußter Zeilen durch die Ausführung dieses
	 * Statements zurück.
	 * 
	 * Diese Methode gibt nur für DELETE, INSERT oder UPDATE-Statements den
	 * korrekten Wert zurück. Für SELECT-Statements ist dies nicht garantiert.
	 * 
	 * @return int
	 */
	public function rowCount() {
		return mysql_affected_rows($this->resultHandle);
	}
	
	/**
	 * @todo DOC
	 */
	public function columnCount() {
		return mysql_num_fields($this->resultHandle);
	}
	
	/**
	 * @param column
	 * @todo DOC
	 */
	public function getColumnMeta($column) {
		return(array(
			'native_type'	=> 'string',
			'flags'			=> mysql_field_flags($this->resultHandle, $column),
			'name'			=> mysql_field_name($this->resultHandle, $column),
			'table'			=> mysql_field_table(resultHandle, $column),
			'len'			=> mysql_field_len(resultHandle, $column),
			'precision'		=> 0,		// TODO: Ermitteln des precision-Wertes
			'pdo_type'		=> $this->type2const(mysql_field_type(resultHandle, $column))
		));
	}
	
	/**
	 * Löst einen Typen als Zeichenkette in die entsprechende
	 * PARAM_*-Konstante auf.
	 * 
	 * @return integer
	 * @param string
	 */
	private function type2const($type) {
		switch(strtoupper($type)) {
			case 'VARCHAR':
			case 'TEXT':
			case 'DATE':
			case 'DATETIME':
			case 'TIMESTAMP':
			case 'TIME':
			case 'CHAR':
			case 'TINYBLOB':
			case 'TINYTEXT':
			case 'BLOB':
			case 'MEDIUMBLOB':
			case 'MEDIUMTEXT':
			case 'LONGBLOB':
			case 'LONGTEXT':
			case 'ENUM':
			case 'SET':
			case 'BIT':
			case 'BINARY':
			case 'VARBINARY':
				return CurlyX_Db_Connection_Interface::PARAM_STR;
			case 'TINYINT':
			case 'SMALLINT':
			case 'MEDIUMINT':
			case 'INT':
			case 'BIGINT':
			case 'YEAR':
				return CurlyX_Db_Connection_Interface::PARAM_INT;
			case 'BOOL':
				return CurlyX_Db_Connection_Interface::PARAM_BOOL;
			case 'FLOAT':
			case 'DOUBLE':
			case 'DECIMAL':
				return CurlyX_Db_Connection_Interface::PARAM_STR; // TODO: Keine bessere Lösung?
		}
	}
	
	/**
	 * Schließt den internen Datensatzzeiger und gibt alle Ressourcen zu diesem
	 * Statement frei.
	 * 
	 * @return boolean
	 */
	public function closeCursor() {
		mysql_free_result($this->resultHandle);
		$this->resultHandle=NULL;
	}
	
	/**
	 * Setzt den internen Datensatzindex intern zurück.
	 * 
	 * @return void
	 */
	protected function _rewind() {
		mysql_data_seek($this->resultHandle, 0);
	}
	
	/**
	 * Sendet die simulierte SQL-Query.
	 * 
	 * @return boolean
	 */
	protected function sendQuery($query) {
		$this->resultHandle=$this->driver->_queryStatement($query);
		return true;
	}
	
}