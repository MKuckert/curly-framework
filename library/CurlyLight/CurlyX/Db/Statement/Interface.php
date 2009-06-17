<?php

require_once "CurlyX/Db/Connection/Interface.php";

/**
 * CurlyX_Db_Statement_Interface
 * 
 * Basis-Interface für alle CurlyX_Db_Statement_*-Klassen.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Statement
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
interface CurlyX_Db_Statement_Interface extends Traversable {
	
	/**
	 * Führt dieses Statement aus.
	 * 
	 * @return boolean
	 * @param array Eingabeparameter
	 */
	public function execute(array $params=array());

	/**
	 * Holt den nächsten Datensatz aus den Ergebnisdaten ab.
	 * 
	 * @return mixed
	 * @param int Art der Rückgabe
	 * @param int Die Richtung, in der die Daten gelesen werden sollen.
	 * @param int Offset zur Leserichtung
	 */
	public function fetch($how=self::FETCH_BOTH, $orientation=CurlyX_Db_Connection_Interface::FETCH_ORI_NEXT, $offset=0);

	/**
	 * Bindet einen Parameter an dieses Statement.
	 * 
	 * @return boolean
	 * @param mixed Der Parameter-Identifier (:name oder 1-basierter Index)
	 * @param mixed& Die an das Statement zu bindende Referenz.
	 * @param int Der zu verwendende Datentyp
	 * @param int Die Länge des Datentyps
	 * @param mixed Treiberspezifische Daten 
	 */
	public function bindParam($paramno, &$param, $type=CurlyX_Db_Connection_Interface::PARAM_STR, $maxlen=0, $driverdata=NULL);

	/**
	 * Bindet eine Referenz an eine Spalte.
	 * 
	 * @return boolean
	 * @param mixed Der Parameter-Identifier (:name oder 1-basierter Index)
	 * @param mixed& Die an das Statement zu bindende Referenz.
	 * @param int Der zu verwendende Datentyp
	 * @param int Die Länge des Datentyps
	 * @param mixed Treiberspezifische Daten 
	 */
	public function bindColumn($paramno, &$param, $type=CurlyX_Db_Connection_Interface::PARAM_STR, $maxlen=0, $driverdata=NULL);

	/**
	 * Bindet einen Wert an dieses Statement.
	 * 
	 * @return boolean
	 * @param mixed Der Parameter-Identifier (:name oder 1-basierter Index)
	 * @param mixed& Die an das Statement zu bindende Referenz.
	 * @param int Der zu verwendende Datentyp
	 */
	public function bindValue($paramno, $param, $type=CurlyX_Db_Connection_Interface::PARAM_STR);
	
	/**
	 * Gibt die Anzahl beeinflußter Zeilen durch die Ausführung dieses
	 * Statements zurück.
	 * 
	 * Diese Methode gibt nur für DELETE, INSERT oder UPDATE-Statements den
	 * korrekten Wert zurück. Für SELECT-Statements ist dies nicht garantiert.
	 * 
	 * @return int
	 */
	public function rowCount();
	
	/**
	 * Gibt eine einzelne Spalte aus dem nächsten Datensatz zurück,
	 * 
	 * @return string oder false, wenn kein Datensatz mehr vorhanden ist.
	 * @param int Zeilennummer
	 */
	public function fetchColumn($column_number=0);

	/**
	 * Gibt ein Array mit allen Datensätzen zurück.
	 * 
	 * @return mixed
	 * @param int Der zu verwendende FETCH_*-Typ
	 * @param mixed Weiterer Parameter für den gewählten Fetch-Typ
	 * @param array Konstruktorargumente für den Fetch-Typ FETCH_CLASS
	 */
	public function fetchAll($how=CurlyX_Db_Connection_Interface::FETCH_BOTH, $fetch_arg=0, array $ctor_args=array());

	/**
	 * @param class_name
	 * @param ctor_args[optional]
	 * @todo DOC
	 */
	public function fetchObject($class_name='stdClass', array $ctor_args=array());
	
	/**
	 * @todo DOC
	 */
	public function errorCode();
	
	/**
	 * @todo DOC
	 */
	public function errorInfo();
	
	/**
	 * @param attribute
	 * @param value
	 * @todo DOC
	 */
	public function setAttribute($attribute, $value);
	
	/**
	 * @param attribute
	 * @todo DOC
	 */
	public function getAttribute($attribute);
	
	/**
	 * @todo DOC
	 */
	public function columnCount();
	
	/**
	 * @param column
	 * @todo DOC
	 */
	public function getColumnMeta($column);
	
	/**
	 * @param mode
	 * @param params[optional]
	 * @todo DOC
	 */
	public function setFetchMode($mode, $params);
	
	/**
	 * @todo DOC
	 */
	public function nextRowset();
	
	/**
	 * Schließt den internen Datensatzzeiger und gibt alle Ressourcen zu diesem
	 * Statement frei.
	 * 
	 * @return boolean
	 */
	public function closeCursor();
	
}