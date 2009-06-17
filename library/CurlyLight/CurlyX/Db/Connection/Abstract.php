<?php

require_once "CurlyX/Db/Exception.php";
require_once "CurlyX/Db/Connection/Interface.php";
require_once "CurlyX/Db/Driver/Factory/Default.php";

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
abstract class CurlyX_Db_Connection_Abstract implements CurlyX_Db_Connection_Interface {
	
	const ERR_NO_TRANSACTIONS=10001;
	
	const SQLSTATE_FEATURE_NOT_SUPPORTED='0A000';
	
	/**
	 * @var boolean Flag, ob dieses Verbindungsobjekt über Transaktionen
	 * verfügt.
	 */
	protected $haveTransactions=false;
	
	/**
	 * @var CurlyX_Db_Driver_Factory_Abstract Zu verwendende Factory-Instanz.
	 */
	protected $factory=NULL;
	
	/**
	 * @var array Attribute
	 */
	protected $attributes=array();
	
	/**
	 * Konstruktor
	 * 
	 * @param string Datasourcename
	 * @param string Benutzername
	 * @param string Passwort
	 * @param array Treiberspezifische Optionen
	 */
	public function __construct($dsn, $username=NULL, $password=NULL, array $driver_options=array()) {
		$this->attributes=$this->getDefaultAttributes();
		$this->factory=$this->createFactory();
		$this->connect($dsn, $username, $password, $driver_options);
	}
	
	/**
	 * Destruktor
	 */
	public function __destruct() {
		$this->disconnect();
	}
	
	/**
	 * Gibt die Standardattribute zurück.
	 * 
	 * @return array
	 */
	protected function getDefaultAttributes() {
		return(array(
			self::ATTR_AUTOCOMMIT		=> 0,
			self::ATTR_CASE				=> self::CASE_NATURAL,
			self::ATTR_ERRMODE			=> self::ERRMODE_SILENT
		));
	}
	
	/**
	 * Stellt die Datenbankverbindung her
	 * 
	 * @return void
	 * @param string Datasourcename
	 * @param string Benutzername
	 * @param string Passwort
	 * @param array Treiberspezifische Optionen
	 */
	abstract protected function connect($dsn, $username, $password, array $driver_options);
	
	/**
	 * Schließt die Datenbankverbindung
	 * 
	 * @return void
	 */
	protected function disconnect() {
		// Keine Aktion in der Basisimplementation
	}
	
	/**
	 * Erstellt die zu verwendende Factory-Instanz.
	 * 
	 * @return CurlyX_Db_Driver_Factory_Abstract
	 */
	protected function createFactory() {
		return(new CurlyX_Db_Driver_Factory_Default());
	}
	
	/**
	 * Startet eine Transaktion,
	 * 
	 * @return boolean
	 */
	public function beginTransaction() {
		if(!$this->haveTransactions) {
			$this->_errNoTransactions();
		}
	}
	
	protected function _errNoTransactions() {
		throw new CurlyX_Db_Exception('This driver implements no transactions', self::ERR_NO_TRANSACTIONS, array(
			self::SQLSTATE_FEATURE_NOT_SUPPORTED,
			self::ERR_NO_TRANSACTIONS,
			'This driver implements no transactions'
		));
	}
	
	/**
	 * Beendet eine Transaktion und übernimmt alle Datensatz-Änderungen.
	 * 
	 * @return boolean
	 */
	public function commit() {
		if(!$this->haveTransactions) {
			$this->_errNoTransactions();
		}
	}
	
	/**
	 * Gibt den SQLSTATE der letzten Operation am Datenbank-Handle zurück.
	 * 
	 * @return string
	 */
	public function errorCode() {
		$info=$this->errorInfo();
		return $info[0];
	}
	
	/**
	 * Gibt ein Array mit den verfügbaren Treibernamen zurück.
	 * 
	 * @return array
	 */
	public function getAvailableDrivers() {
		return $this->factory->getAvailableDrivers();
	}
	
	/**
	 * Führt ein SQL-Statement aus und gibt ein Statement-Objekt für die
	 * Ergebnisabfrage zurück.
	 * 
	 * @return CurlyX_Db_Statement_Interface
	 * @param string SQL-Statement
	 */
	public function query($statement) {
		$stmt=$this->prepare($statement);
		$stmt->execute();
		return $stmt;
	}
	
	/**
	 * Beendet die aktuelle Transaktion und verwirft alle Änderungen.
	 * 
	 * @return boolean
	 */
	public function rollBack() {
		if(!$this->haveTransactions) {
			$this->_errNoTransactions();
		}
	}
	
	/**
	 * Gibt den Wert eines Datenbankverbindungsattributes zurück.
	 * 
	 * @return mixed
	 * @param int
	 */
	public function getAttribute($attribute) {
		switch($attribute) {
			case self::ATTR_CLIENT_VERSION:
				return $this->getClientVersion();
			case self::ATTR_DRIVER_NAME:
				return $this->getDriverName();
			case self::ATTR_SERVER_INFO:
				return $this->getServerInfo();
			case self::ATTR_SERVER_VERSION:
				return $this->getServerVersion();
			case self::ATTR_CONNECTION_STATUS:
				return $this->getConnectionStatus();
		}
		
		
		if(isset($this->attributes[$attribute])) {
			return($this->attributes[$attribute]);
		}
		else {
			return(NULL);
		}
	}
	
	/**
	 * Setzt ein Datenbankverbindungsattribut
	 * 
	 * @return boolean
	 * @param int
	 * @param mixed
	 */
	public function setAttribute($attribute, $value) {
		switch($attribute) {
			case self::ATTR_AUTOCOMMIT:
				return $this->setAutocommit($value);
		}
		
		$this->attributes[$attribute]=$value;
		return(true);
	}
	
	/**
	 * Setzt den Autocommit-Wert.
	 * 
	 * @return void
	 * @param boolean
	 */
	abstract protected function setAutocommit($value);
	
	/**
	 * Gibt den Namen dieses Treibers zurück.
	 * 
	 * @return string
	 */
	abstract public function getDriverName();
	
	/**
	 * Gibt die Version der Client-Bibliothek zurück.
	 * 
	 * @return string
	 */
	abstract public function getClientVersion();
	
	/**
	 * Gibt die Version der Server-Bibliothek zurück.
	 * 
	 * @return string
	 */
	abstract public function getServerVersion();
	
	/**
	 * Gibt Informationen über den Server zurück.
	 * 
	 * @return string
	 */
	abstract public function getServerInfo();
	
	/**
	 * Gibt den aktuellen Status der Verbindung zurück.
	 * 
	 * @return string
	 */
	abstract public function getConnectionStatus();
	
}