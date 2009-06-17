<?php

require_once "CurlyX/Db/Statement/Interface.php";
require_once "CurlyX/Db/Connection/Interface.php";
require_once "Curly/Util/ClassCreation.php";

/**
 * CurlyX_Db_Statement_Abstract
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Statement
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
abstract class CurlyX_Db_Statement_Abstract implements Iterator, CurlyX_Db_Statement_Interface {
	
	/**
	 * @var integer Aktuell zu verwendende Fetch-Richtung.
	 */
	protected $orientation;
	
	/**
	 * @var integer Offset-Wert zu FETCH_ORI_ABS oder FETCH_ORI_REL
	 */
	protected $offset;
	
	/**
	 * @var array Statement-Attribute
	 */
	private $attributes=array();
	
	/**
	 * @var int Standard-Fetchmode
	 */
	private $fetchMode=CurlyX_Db_Connection_Interface::FETCH_BOTH;
	
	/**
	 * @var mixed Fetchmode-Parameter
	 */
	private $fetchModeParam=NULL;
	
	/**
	 * @var array
	 */
	protected $boundColumns=array();
	
	/**
	 * @var array
	 */
	protected $boundParams=array();
	
	/**
	 * @var array
	 */
	protected $boundValues=array();
	
	/**
	 * @var CurlyX_Db_Driver_Abstract Treiberinstanz, zu der dieses Statement gehört.
	 */
	protected $driver=NULL;
	
	/**
	 * @var string Auszuführendes Statement
	 */
	public $queryString="";
	
	/**
	 * @var array Treiberoptionen
	 */
	protected $driverOptions=array();
	
	/**
	 * @var mixed Aktueller Datensatz
	 */
	protected $currentRowset=NULL;
	
	/**
	 * @var integer Index des aktuellen Datensatzes
	 */
	protected $currentRowsetIndex=-1;
	
	/**
	 * Konstruktor
	 * 
	 * @param CurlyX_Db_Driver_Abstract Treiberinstanz, zu der dieses Statement gehört.
	 * @param string Auszuführendes Statement
	 * @param array Treiberoptionen
	 */
	public function __construct(CurlyX_Db_Driver_Abstract $driver, $queryString, array $driverOptions) {
		$this->driver=$driver;
		$this->queryString=$queryString;
		$this->driverOptions=$driverOptions;
	}
	
	/**
	 * Holt den nächsten Datensatz aus den Ergebnisdaten ab.
	 * 
	 * @return mixed
	 * @param int Art der Rückgabe
	 * @param int Die Richtung, in der die Daten gelesen werden sollen.
	 * @param int Offset zur Leserichtung
	 */
	public function fetch($how=NULL, $orientation=CurlyX_Db_Connection_Interface::FETCH_ORI_NEXT, $offset=0) {
		if($how===NULL) {
			$how=$this->getFetchMode();
		}
		
		$fetchClasstype=false;
		if(($how & CurlyX_Db_Connection_Interface::FETCH_CLASSTYPE)==CurlyX_Db_Connection_Interface::FETCH_CLASSTYPE) {
			$fetchClasstype=true;
			$how^=CurlyX_Db_Connection_Interface::FETCH_CLASSTYPE;
		}
		
		$this->orientation=$orientation;
		$this->offset=$offset;
		
		switch($how) {
			case CurlyX_Db_Connection_Interface::FETCH_ASSOC:
				return $this->fetchAssoc();
			case CurlyX_Db_Connection_Interface::FETCH_BOUND:
				return $this->fetchBound();
			case CurlyX_Db_Connection_Interface::FETCH_CLASS:
				return $this->fetchClass($fetchClasstype);
			case CurlyX_Db_Connection_Interface::FETCH_INTO:
				return $this->fetchInto();
			case CurlyX_Db_Connection_Interface::FETCH_LAZY:
				return $this->fetchLazy();
			case CurlyX_Db_Connection_Interface::FETCH_NUM:
				return $this->fetchNum();
			case CurlyX_Db_Connection_Interface::FETCH_OBJ:
				return $this->fetchObject();
			case CurlyX_Db_Connection_Interface::FETCH_BOTH:
			default:
				return $this->fetchBoth();
		}
	}
	
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
	public function bindParam($paramno, &$param, $type=CurlyX_Db_Connection_Interface::PARAM_STR, $maxlen=0, $driverdata=NULL) {
		$this->boundParams[$paramno] = array(
			$paramno,
			&$param,
			$type,
			$maxlen,
			$driverdata
		);
		return true;
	}

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
	public function bindColumn($paramno, &$param, $type=CurlyX_Db_Connection_Interface::PARAM_STR, $maxlen=0, $driverdata=NULL) {
		$this->boundColumns[$paramno] = array(
			$paramno,
			&$param,
			$type,
			$maxlen,
			$driverdata
		);
		return true;
	}

	/**
	 * Bindet einen Wert an dieses Statement.
	 * 
	 * @return boolean
	 * @param mixed Der Parameter-Identifier (:name oder 1-basierter Index)
	 * @param mixed& Die an das Statement zu bindende Referenz.
	 * @param int Der zu verwendende Datentyp
	 */
	public function bindValue($paramno, $param, $type=CurlyX_Db_Connection_Interface::PARAM_STR) {
		$this->boundValues[$paramno] = array(
			$paramno,
			$param,
			$type
		);
		return true;
	}

	/**
	 * Gibt eine einzelne Spalte aus dem nächsten Datensatz zurück,
	 * 
	 * @return string oder false, wenn kein Datensatz mehr vorhanden ist.
	 * @param int Zeilennummer
	 */
	public function fetchColumn($column_number=0) {
		$row=$this->fetch(CurlyX_Db_Connection_Interface::FETCH_NUM);
		return $row[$column_number];
	}
	
	/**
	 * @todo
	 */
	abstract protected function fetchAssoc();
	
	/**
	 * @todo
	 */
	protected function fetchBound() {
		// TODO: fetchBound implementieren
	}
	
	/**
	 * Lädt den nächsten Datensatz im FETCH_BOTH-Modus.
	 * 
	 * @return array
	 */
	protected function fetchBoth() {
		$fetch=$this->fetchAssoc();
		if(!is_array($fetch)) {
			return false;
		}
		
		$retval=array();
		foreach($fetch as $name=>$value) {
			$retval[$name]=$value;
			$retval[]=$value;
		}
		return $retval;
	}
	
	/**
	 * @todo
	 */
	protected function fetchClass($fetchClasstype) {
		$data=$this->fetchAssoc();
		if(!is_array($data)) {
			return false;
		}
		
		if($fetchClasstype) {
			$key=key($data);
			$class=$data[$key];
			unset($data[$key]);
		}
		else {
			$class=$this->getFetchModeParam();
		}
		
		$obj=new $class();
		return $this->fetchInto($obj, $data);
	}
	
	/**
	 * @todo
	 */
	protected function fetchInto($obj=NULL, array $data=NULL) {
		if($obj===NULL) {
			$obj=$this->getFetchModeParam();
		}
		
		if($data===NULL) {
			$data=$this->fetchAssoc();
		}
		if(!is_array($data)) {
			return false;
		}
		
		foreach($data as $key=>$value) {
			$obj->$key=$value;
		}
		
		return $obj;
	}
	
	/**
	 * @todo
	 */
	protected function fetchLazy() {
		// TODO: fetchLazy implementieren
	}
	
	/**
	 * @todo
	 */
	protected function fetchNum() {
		$dataset=$this->fetchAssoc();
		if(!is_array($dataset)) {
			return $dataset;
		}
		
		return array_values($dataset);
	}
	
	/**
	 * Gibt ein Array mit allen Datensätzen zurück.
	 * 
	 * @param int Der zu verwendende FETCH_*-Typ
	 * @param mixed Weiterer Parameter für den gewählten Fetch-Typ
	 * @param array Konstruktorargumente für den Fetch-Typ FETCH_CLASS
	 */
	public function fetchAll($how=NULL, $fetch_arg=0, array $ctor_args=array()) {
		if($how===NULL) {
			$how=$this->getFetchMode();
		}
		
		//TODO: FETCH_GROUP, FETCH_UNIQUE, FETCH_COLUMN
		$allData=array();
		while(($dataset=$this->fetch($how))!==false) {
			$allData[]=$dataset;
		}
		return $allData;
	}
	
	/**
	 * @param class_name
	 * @param ctor_args[optional]
	 * @todo DOC
	 */
	public function fetchObject($class='stdClass', array $ctorArgs=array()) {
		$data=$this->fetchAssoc();
		if(!is_array($data)) {
			return false;
		}
		
		$obj=Curly_Util_ClassCreation::newInstance($class, $ctorArgs);
		return $this->fetchInto($obj, $data);
	}
	
	/**
	 * @todo DOC
	 */
	public function errorCode() {
		return $this->driver->errorCode();
	}
	
	/**
	 * @todo DOC
	 */
	public function errorInfo() {
		return $this->driver->errorInfo();
	}
	
	/**
	 * @param attribute
	 * @todo DOC
	 */
	public function getAttribute($attribute) {
		if(isset($this->attributes[$attribute])) {
			return $this->attributes[$attribute];
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * @return boolean
	 * @param attribute
	 * @param value
	 * @todo DOC
	 */
	public function setAttribute($attribute, $value) {
		$this->attributes[$attribute]=$value;
		return true;
	}
	
	/**
	 * @return int
	 * @todo DOC
	 */
	protected function getFetchMode() {
		return $this->fetchMode;
	}
	
	/**
	 * @return mixed
	 * @todo DOC
	 */
	protected function getFetchModeParam() {
		return $this->fetchModeParam;
	}
	
	/**
	 * @return void
	 * @param int
	 * @param mixed
	 * @todo DOC
	 */
	public function setFetchMode($mode, $params) {
		$this->fetchMode=$mode;
		$this->fetchModeParam=$params;
	}
	
	/**
	 * @todo DOC
	 */
	public function nextRowset() {
		if($this->driver->getAttribute(CurlyX_Db_Connection_Interface::ATTR_ERRMODE))
		throw new CurlyX_Db_Exception('Optional feature not implemented', 0, array('HYC00', 0, 'Optional feature not implemented'));
	}
	
	/**
	 * Schließt den internen Datensatzzeiger und gibt alle Ressourcen zu diesem
	 * Statement frei.
	 * 
	 * @return boolean
	 */
	public function closeCursor() {
		while($this->fetch());
	}
	
	/**#@+
	 * Iterator-Methode
	 */
	/**
	 * Gibt den aktuellen Datensatz zurück.
	 * 
	 * @return mixed
	 */
	public function current() {
		return $this->currentRowset;
	}
	
	/**
	 * Lädt den nächsten Datensatz.
	 * 
	 * @return void
	 */
	public function next() {
		$this->currentRowset=$this->fetch();
		$this->currentRowsetIndex++;
	}
	
	/**
	 * Gibt den aktuellen Datensatzindex zurück.
	 * 
	 * @return integer
	 */
	public function key() {
		return $this->currentRowsetIndex;
	}
	
	/**
	 * Überprüft, ob der aktuelle Datensatz gültig ist.
	 * 
	 * @return boolean
	 */
	public function valid() {
		return $this->currentRowset!==false;
	}
	
	/**
	 * Setzt den internen Datensatzindex zurück.
	 * 
	 * @return void
	 */
	public function rewind() {
		$this->currentRowsetIndex=-1;
		$this->_rewind();
	}
	/**#@-*/
	
	/**
	 * Setzt den internen Datensatzindex intern zurück.
	 * 
	 * @return void
	 */
	abstract protected function _rewind();
	
}