<?php

/**
 * CurlyX_Db_Driver_Factory_Default
 * 
 * Factory-Klasse für verfügbare Driver
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Db.Driver.Factory
 * @since 1.0 - 30.11.2008
 * @version 1.0
 */
class CurlyX_Db_Driver_Factory_Default extends CurlyX_Db_Driver_Factory_Abstract {
	
	/**
	 * @var array Liste der Dateien im Driver-Verzeichnis, die keine
	 * Verbindungsklassen sind.
	 */
	static private $noDrivers=array('factory', 'abstract');
	
	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->scanDrivers();
	}
	
	/**
	 * Scannt das Treiberverzeichnis und registriert alle Treiber in dieser
	 * Klasse.
	 * 
	 * @return void
	 */
	private function scanDrivers() {
		$driverDir=dirname(__FILE__).'/../';
		$dirs=glob($driverDir.'*.php');
		
		$drivers=array();
		foreach($dirs as $file) {
			if(!is_file($file)) {
				continue;
			}
			
			$driverName=strtolower(basename($file, '.php'));
			if(in_array($driverName, self::$noDrivers)) {
				continue;
			}
			
			$drivers[]=$driverName;
		}
		
		$this->setAvailableDrivers($drivers);
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
	public function createDriver($name, $dsn, $username=NULL, $password=NULL, array $driver_options=array()) {
		$class='CurlyX_Db_Driver_'.ucfirst($name);
		$inst=new $class($dsn, $username, $password, $driver_options);
		return $inst;
	}
	
}

