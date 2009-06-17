<?php

/**
 * CurlyX_Db_Driver_Factory_Abstract
 * 
 * Abstrakte Vorlage für die Factory-Klasse für verfügbare Driver
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Driver.Factory
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
abstract class CurlyX_Db_Driver_Factory_Abstract {
	
	/**
	 * @var array Liste der vorhandenen Treiber
	 */
	private $availableDrivers=array();
	
	/**
	 * Gibt eine Liste der verfügbaren Treiber zurück.
	 * 
	 * @return array
	 */
	public function getAvailableDrivers() {
		return $this->availableDrivers;
	}
	
	/**
	 * Setzt die Liste der verfügbaren Treiber.
	 * 
	 * @return void
	 * @param array
	 */
	protected function setAvailableDrivers(array $value) {
		$this->availableDrivers=$value;
	}
	
	/**
	 * Erstellt eine Instanz der Treiberklasse mit dem übergebenen Namen.
	 * 
	 * @return CurlyX_Db_Driver_Abstract
	 * @param string Der Treibername
	 * @param string Datasourcename
	 * @param string Benutzername
	 * @param string Passwort
	 * @param array Treiberspezifische Optionen
	 */
	abstract public function createDriver($name, $dsn, $username=NULL, $password=NULL, array $driver_options=array());
	
}

