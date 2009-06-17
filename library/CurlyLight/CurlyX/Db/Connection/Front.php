<?php

/**
 * CurlyX_Db_Connection_Front
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Connection
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
class CurlyX_Db_Connection_Front extends CurlyX_Db_Connection_Abstract {
	
	/**
	 * @var CurlyX_Db_Connection_Interface Verbindungstreiber
	 */
	private $driver=NULL;
	
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
		
		$driverName=$uri['scheme'];
		
		$drivers=$this->getAvailableDrivers();
		if(!in_array($driverName, $drivers)) {
			throw new CurlyX_Db_Exception('Driver '.$driverName.' not found');
		}
		
		$this->driver=$this->factory->createDriver($driverName, $dsn, $username, $password, $driver_options);
	}
	
	public function beginTransaction() {
		return($this->driver->beginTransaction());
	}
	
	public function commit() {
		return($this->driver->commit());
	}
	
	public function errorInfo() {
		return($this->driver->errorInfo());
	}
	
	public function exec($statement) {
		return($this->driver->exec($statement));
	}
	
	public function getAttribute($attribute) {
		return($this->driver->getAttribute($attribute));
	}
	
	public function lastInsertId($name=NULL) {
		return($this->driver->lastInsertId($name));
	}
	
	public function prepare($statement, array $driver_options=array()) {
		return($this->driver->prepare($statement, $driver_options));
	}
	
	public function query($statement) {
		return($this->driver->query($statement));
	}
	
	public function quote($string, $parameter_type=self::PARAM_STR) {
		return($this->driver->quote($string, $parameter_type));
	}
	
	public function rollBack() {
		return($this->driver->rollBack());
	}
	
	public function setAttribute($attribute, $value) {
		return($this->driver->setAttribute($attribute, $value));
	}
	
	protected function setAutocommit($value) {
		return($this->driver->setAutocommit($value));
	}
	
	public function getDriverName() {
		return($this->driver->getDriverName());
	}
	
	public function getClientVersion() {
		return($this->driver->getClientVersion());
	}
	
	public function getServerVersion() {
		return($this->driver->getServerVersion());
	}
	
	public function getServerInfo() {
		return($this->driver->getServerInfo());
	}
	
	public function getConnectionStatus() {
		return($this->driver->getConnectionStatus());
	}
	
}