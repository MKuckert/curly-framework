<?php

require_once "CurlyX/Db/Exception.php";
require_once "CurlyX/Db/Connection/Interface.php";
require_once "CurlyX/Db/Statement/SimulatePrepare.php";

/**
 * CurlyX_Db_Connection_Abstract
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Connection
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
class CurlyX_Db_Driver_MySQL extends CurlyX_Db_Driver_Abstract {
	
	/**
	 * @var boolean Flag, ob dieses Verbindungsobjekt über Transaktionen
	 * verfügt.
	 */
	protected $haveTransactions=true;
	
	/**
	 * @var resource Verbindungs-Handle
	 */
	private $link=NULL;
	
	/**
	 * Stellt die Datenbankverbindung her
	 * 
	 * @param string Datasourcename
	 * @param string Benutzername
	 * @param string Passwort
	 * @param array Treiberspezifische Optionen
	 */
	protected function connect($dsn, $username, $password, array $driver_options) {
		$uri=parse_url($dsn);
		$query=array();
		foreach(explode(';', $uri['path']) as $item) {
			list($key, $value)=explode('=', $item, 2);
			$query[$key]=$value;
		}
		
		$server=isset($query['host'])?$query['host']:'localhost';
		
		$link=mysql_connect($server, $username, $password);
		if($link===false) {
			$this->throwException();
		}
		$this->link=$link;
		
		if(isset($query['dbname'])) {
			if(!mysql_select_db($query['dbname'], $this->link)) {
				$this->throwException();
			}
		}
	}
	
	/**
	 * Gibt den Verbindungs-Handle zurück.
	 * 
	 * @return resource
	 */
	public function getLink() {
		return $this->link;
	}
	
	/**
	 * Startet eine Transaktion,
	 * 
	 * @return boolean
	 */
	public function beginTransaction() {
		$this->exec("START TRANSACTION");
		return true;
	}
	
	/**
	 * Beendet eine Transaktion und übernimmt alle Datensatz-Änderungen.
	 * 
	 * @return boolean
	 */
	public function commit() {
		$this->exec("COMMIT");
		return true;
	}
	
	/**
	 * Gibt erweiterte Fehlerinformationen zu der letzten Operation am
	 * Datenbank-Handle zurück.
	 * 
	 * @return array
	 */
	public function errorInfo() {
		return(array(
			$this->sqlstate(),
			$this->error(),
			$this->errno()
		));
	}
	
	/**
	 * Wirft eine Ausnahme mit der letzten Fehlermeldung.
	 * 
	 * @return void
	 */
	protected function throwException() {
		throw new CurlyX_Db_Exception($this->error(), $this->errno(), $this->errorInfo());
	}
	
	/**
	 * Gibt den letzten SQL-State zurück.
	 * 
	 * @return string
	 */
	protected function sqlstate() {
		return 'unkno';
	}
	
	/**
	 * Gibt die letzte Fehlermeldung zurück.
	 * 
	 * @return string
	 */
	protected function error() {
		return $this->link?mysql_error($this->link):mysql_error();
	}
	
	/**
	 * Gibt den letzten Fehlercode zurück.
	 * 
	 * @return integer
	 */
	protected function errno() {
		return $this->link?mysql_errno($this->link):mysql_errno();
	}
	
	/**
	 * Führt das übergebene SQL-Statement aus und gibt die Anzahl der
	 * beeinflussten Zeilen zurück.
	 * 
	 * @return int
	 * @param string
	 */
	public function exec($statement) {
		return(mysql_num_rows($this->_queryStatement($statement)));
	}
	
	/**
	 * Führt das übergebene SQL-Statement aus und gibt eine Result-Resource
	 * zurück.
	 * 
	 * Diese Methode sollte nur von der CurlyX_Db_Statement_MySQL-Klasse
	 * aufgerufen werden!
	 * 
	 * @ignore
	 * @return boolean
	 * @param string
	 */
	public function _queryStatement($statement) {
		$result=mysql_query($statement, $this->link);
		if(!$result) {
			$this->throwException();
		}
		
		return $result;
	}
	
	/**
	 * Gibt die zuletzt erzeugte ID zurück.
	 * 
	 * @return string
	 * @param string Optionaler Name der Sequenz. Von manchen DBMS benötigt.
	 */
	public function lastInsertId($name=NULL) {
		return(mysql_insert_id($this->link));
	}
	
	/**
	 * Bereitet ein SQL-Statement für die Ausführung vor und gibt ein
	 * Statement-Objekt zurück.
	 * 
	 * @return CurlyX_Db_Statement_Interface
	 * @param string SQL-Statement
	 * @param array Treiberspezifische Optionen
	 */
	public function prepare($statement, array $driver_options=array()) {
		return new CurlyX_Db_Statement_MySQL($this, $statement, $driver_options);
	}
	
	/**
	 * Maskiert den übergebenen Parameter für die Nutzung in einer Query.
	 * 
	 * @return string
	 * @param string Der Parameter
	 * @param int Typ des Parameters
	 */
	public function quote($string, $parameterType=self::PARAM_STR) {
		switch($parameterType) {
			case self::PARAM_BOOL:
			case self::PARAM_INT:
				return((int)$string);
			case self::PARAM_NULL:
				return(NULL);
			case self::PARAM_STR:
			default:
				return(mysql_real_escape_string($string, $this->link));
		}
	}
	
	/**
	 * Beendet die aktuelle Transaktion und verwirft alle Änderungen.
	 * 
	 * @return boolean
	 */
	public function rollBack() {
		$this->exec('ROLLBACK');
		return(true);
	}
	
	/**
	 * Setzt ein Datenbankverbindungsattribut
	 * 
	 * @return boolean
	 * @param int
	 * @param mixed
	 */
	public function setAttribute($attribute, $value) {
		parent::setAttribute($attribute, $value);
		
		switch($attribute) {
			case CurlyX_Db_Connection_Interface::ATTR_AUTOCOMMIT:
				$this->setAutocommit((boolean)$value);
				break;
		}
	}
	
	/**
	 * Setzt den Autocommit-Wert.
	 * 
	 * @return void
	 * @param boolean
	 */
	protected function setAutocommit($value) {
		$this->exec('SET autocommit='.$value);
	}
	
	/**
	 * Gibt den Namen dieses Treibers zurück.
	 * 
	 * @return string
	 */
	public function getDriverName() {
		return('mysql');
	}
	
	/**
	 * Gibt die Version der Client-Bibliothek zurück.
	 * 
	 * @return string
	 */
	public function getClientVersion() {
		return(mysql_get_client_info());
	}
	
	/**
	 * Gibt die Version der Server-Bibliothek zurück.
	 * 
	 * @return string
	 */
	public function getServerVersion() {
		return(mysql_get_server_info());
	}
	
	/**
	 * Gibt Informationen über den Server zurück.
	 * 
	 * @return string
	 */
	public function getServerInfo() {
		return(implode(' ',mysql_stat($this->link)));
	}
	
	/**
	 * Gibt den aktuellen Status der Verbindung zurück.
	 * 
	 * @return string
	 */
	public function getConnectionStatus() {
		return(''); // TODO: Wie finde ich das raus?
	}
	
}