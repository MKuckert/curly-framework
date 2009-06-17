<?php

require_once "Curly/Loader/Interface.php";
require_once "Curly/Loader/Responder/Interface.php";

/**
 * Curly_Loader_Dispatcher
 * 
 * Mit Hilfe dieser Klasse kann bequem das __autoload-Feature von PHP
 * ausgenutzt werden, indem einer Curly_Loader_Dispatcher-Instanz einige
 * Curly_Loader_Interface-Instanzen zugeordnet werden und die Instanz als
 * Autoloader registriert wird.
 *
 * Durch die {@link load}-Methode werden alle Loader abgefragt und nach der
 * Klasse mit dem übergebenen Namen gesucht.
 *
 * Lädt ein Dispatcher eine Klasse, welche das
 * Curly_Loader_Responder_Interface-Interface implementiert, so wird die
 * __loaded-Methode dieser Klasse aufgerufen.
 *
 * Die Klasse kann zum Beispiel auf folgende Art verwendet werden:
 * <code>
 * 	$dispatcher = new Curly_Loader_Dispatcher();
 *  $dispatcher->registerLoader( new MyClassLoader() );
 *  $dispatcher->registerAutoloader();
 * </code>
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Loader
 * @since 1.0 - 27.04.2008
 * @version 1.0
 */
class Curly_Loader_Dispatcher {

	/**
	 * @var array Liste aller IClassLoader-Instanzen in diesem Dispatcher
	 */
	private $loader = array();

	/**
	 * @var boolean Flag, ob eine Exception ausgelöst werden soll, wenn eine
	 * Klasse nicht gefunden werden kann.
	 * @see load
	 */
	private $throwException = false;
	
	/**
	 * @var boolean Flag, ob diese Instanz bereits als Autoloader registriert
	 * ist.
	 */
	private $_registeredAsAutoloader = false;

	/**
	 * Gibt das throwException-Flag zurück.
	 *
	 * @return boolean
	 */
	public function getThrowException() {
		return( $this->throwException );
	}

	/**
	 * Setzt das throwException-Flag.
	 *
	 * @return void
	 * @param boolean
	 */
	public function setThrowException( $throwException ) {
		$this->throwException = (bool)$throwException;
	}
	
	/**
	 * Gibt das Flag, ob diese Instanz bereits als Autoloader registriert ist,
	 * zurück.
	 * 
	 * @return boolean
	 */
	public function getRegisteredAsAutoloader() {
		return $this->_registeredAsAutoloader;
	}
	
	/**
	 * Setzt das Flag, ob diese Instanz bereits als Autoloader registriert ist.
	 * 
	 * @return void
	 * @param boolean
	 */
	protected function setRegisteredAsAutoloader($value) {
		$this->_registeredAsAutoloader=$value;
	}
	
	/**
	 * Überprüft alle IClassLoader-Instanzen, ob diese die Klasse mit dem
	 * übergebenen Namen kennen und lädt die Definition dieser Klasse.
	 *
	 * Kann die Klasse nicht gefunden werden und throwException ist true, wird
	 * eine Dummy-Klasse mit dem übergebenen Namen erzeugt und eine Exception
	 * geworfen.
	 *
	 * Ist throwException false, wird dieser Fehler nicht weiter behandelt, das
	 * Skript wird also mit einem Fatal Error abbrechen.
	 *
	 * Diese Methode kann als Autoload-Funktion verwendet werden.
	 *
	 * @throws Exception
	 * @return void
	 * @param string Der Name der zu ladenden Klasse
	 */
	public function load( $class ) {
		$path = $this->getPath( $class );
		if( $path!==false ) {
			require_once $path;
			if( in_array( 'Curly_Loader_Responder_Interface', class_implements( $class ) ) ) {
				call_user_func( array( $class, '__loaded' ) );
			}
			return true;
		}
		
		if( $this->getThrowException() ) {
			$this->simulateClass( $class );
			throw new Exception( 'Unable to load the definition of the class '.$class );
		}
		else {
			return false;
		}
	}
	
	/**
	 * Überprüft alle IClassLoader-Instanzen, ob diese die Klasse mit dem
	 * übergebenen Namen kennen und gibt Pfad zur Definitionsdatei zurück.
	 *
	 * Kann die Klasse nicht gefunden werden, wird false zurückgegeben.
	 *
	 * @return mixed
	 * @param string Der Name der zu ladenden Klasse
	 */
	public function getPath( $class ) {
		$class = strval( $class );
		foreach( $this->loader as $loader ) {
			if( ( $path=$loader->getPath( $class ) )!==false ) {
				return $path;
			}
		}
		return false;
	}

	/**
	 * Wird in der {@link load}-Methode die Definition einer Klasse nicht
	 * gefunden und ist throwException auf true gesetzt, wird diese Klasse
	 * genutzt, um eine Dummy-Klasse mit dem übergebenen Namen zu erzeugen.
	 *
	 * @return void
	 */
	protected function simulateClass( $class ) {
		eval(
				"class ".$class." { public function __construct() { throw new Exception( ".
				"'Unable to load the definition of the class ".$class."' ); } }"
				);
	}

	/**
	 * Fügt dieser Instanz eine IClassLoader-Instanz hinzu.
	 *
	 * Wird die {@link load}-Methode ausgeführt, werden alle registrierten
	 * ClassLoader-Instanzen iterativ abgefragt und können den Pfad zu der
	 * Definitionsdatei der angeforderten Klasse ermitteln.
	 *
	 * @return void
	 * @param Curly_Loader_Interface Die zu registrierende ClassLoader-Instanz
	 */
	public function registerLoader( Curly_Loader_Interface $loader ) {
		if( !in_array( $loader, $this->loader, true ) ) {
			$this->loader[] = $loader;
		}
	}

	/**
	 * Fügt dieser Instanz eine IClassLoader-Instanz als erste Instanz hinzu.
	 *
	 * Wird die {@link load}-Methode ausgeführt, werden alle registrierten
	 * ClassLoader-Instanzen iterativ abgefragt und können den Pfad zu der
	 * Definitionsdatei der angeforderten Klasse ermitteln.
	 *
	 * @return void
	 * @param Curly_Loader_Interface Die zu registrierende ClassLoader-Instanz
	 */
	public function registerLoaderFirst( Curly_Loader_Interface $loader ) {
		if( !in_array( $loader, $this->loader, true ) ) {
			array_unshift($this->loader, $loader);
		}
	}

	/**
	 * Entfernt eine IClassLoader-Instanz aus dieser Instanz.
	 *
	 * @return void
	 * @param IClassLoader Die zu deregistrierende ClassLoader-Instanz
	 */
	public function unregisterLoader( Curly_Loader_Interface $loader ) {
		$key = array_search( $loader, $this->loader, true );
		if( $key!==false ) {
			unset( $this->loader[$key] );
		}
	}

	/**
	 * Registriert dieses Objekt mit Hilfe der
	 * spl_autoload_register-Funktion als Autoloader.
	 *
	 * @return void
	 */
	public function registerAsAutoloader() {
		if( spl_autoload_register( array( $this, 'load' ) ) ) {
			$this->setRegisteredAsAutoloader( true );
		}
	}

	/**
	 * Deregistriert dieses Objekt mit Hilfe der
	 * spl_autoload_unregister-Funktion, nachdem dieses als Autoloader
	 * registriert wurde.
	 *
	 * @return void
	 */
	public function unregisterAutoloader() {
		if( spl_autoload_unregister( array( $this, 'load' ) ) ) {
			$this->setRegisteredAsAutoloader( false );
		}
	}

}