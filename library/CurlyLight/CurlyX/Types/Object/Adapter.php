<?php

/**
 * CurlyX_Types_Object_Adapter
 * 
 * Stellt einen Adapter auf ein Objekt dar. Alle Funktionsaufrufe und Eigenschaftenzugriffe
 * werden direkt an das adaptierte Objekt weitergeleitet. Dieses Objekt kann für Debugzwecke oder
 * ein Dekorator-Designpattern verwendet werden.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Types.Object
 * @since 1.0 - 29.03.2008
 * @version 1.0
 */
class CurlyX_Types_Object_Adapter {

	/**
	 * @var Object Die zu adaptierende Objektinstanz.
	 */
	private $adapted = null;

	/**
	 * Gibt die adaptierte Objektinstanz zurück.
	 * Wurde noch keine Instanz festgelegt, wird NULL zurückgegeben.
	 *
	 * @return Object
	 */
	public function getAdapted() {
		return( $this->adapted );
	}

	/**
	 * Setzt die zu adaptierende Objektinstanz.
	 * Ist das übergebene Objekt nicht zulässig für diesen Adapter, wird eine
	 * InvalidArgumentException geworfen.
	 *
	 * @return void
	 * @param Object
	 */
	public function setAdapted( $adapted ) {
		if( !$this->isValidAdapted( $adapted ) ) {
			throw new InvalidArgumentException( 'The given object is not allowed for this adapter' );
		}
		$this->adapted = $adapted;
	}

	/**
	 * Überprüft, ob die übergebene Object-Instanz als adaptierte Instanz dieses Objektes zulässig ist.
	 * Die Standardimplementierung sieht jedes Objekt als zulässig an.
	 *
	 * @return boolean
	 * @param Object
	 */
	public function isValidAdapted( $adapted ) {
		return( true );
	}

	/**
	 * Konstruktor
	 * Das adaptierte Objekt kann direkt gesetzt werden.
	 *
	 * @param Object
	 */
	public function __construct( $adapted=null ) {
		if( !is_null( $adapted ) ) {
			$this->setAdapted( $adapted );
		}
	}

	/**
	 * Ruft die Membervariable mit dem übergebenen Namen an dem adaptierten Objekt auf.
	 * Wurde noch kein adaptiertes Objekt gesetzt, wird eine AdapterException geworfen.
	 *
	 * @throws AdapterException
	 * @return mixed
	 * @param string Der Name der Membervariable
	 */
	public function __get( $member ) {
		$adapted = $this->getAdapted();
		if( is_null( $adapted ) ) {
			throw new AdapterException( 'There is no adapted instance for this adapter' );
		}
		return( $adapted->$member );
	}

	/**
	 * Ruft die Membervariable mit dem übergebenen Namen an dem adaptierten Objekt auf und
	 * versucht dieser den übergebenen Wert zuzuweisen.
	 * Wurde noch kein adaptiertes Objekt gesetzt, wird eine AdapterException geworfen.
	 *
	 * @throws AdapterException
	 * @return mixed
	 * @param string Der Name der Membervariable
	 * @param mixed Der Wert, den die Membervariable erhalten soll
	 */
	public function __set( $member, $value ) {
		$adapted = $this->getAdapted();
		if( is_null( $adapted ) ) {
			throw new AdapterException( 'There is no adapted instance for this adapter' );
		}
		$adapted->$member = $value;
	}

	/**
	 * Ruft die Methode mit dem übergebenen Namen an dem adaptierten Objekt auf.
	 * Wurde noch kein adaptiertes Objekt gesetzt, wird eine AdapterException geworfen.
	 *
	 * @throws AdapterException
	 * @return mixed
	 * @param string Der Name der Methode
	 * @param array Array der übergebenen Argumente für die Methode
	 */
	public function __call( $method, $arguments ) {
		$adapted = $this->getAdapted();
		if( is_null( $adapted ) ) {
			throw new AdapterException( 'There is no adapted instance for this adapter' );
		}
		return( call_user_func_array( array( $adapted, $method ), $arguments ) );
	}

	/**
	 * Klont dieses Objekt und gibt den Klon zurück.
	 * Wurde diesem Adapter ein zu adaptierendes Objekt zugewiesen, wird dieses ebenfalls geklont.
	 *
	 * @return ObjectAdapter
	 */
	public function __clone() {
		$adapted = $this->getAdapted();
		if( !is_null( $adapted ) ) {
			$this->setAdapted( clone $adapted );
		}
	}

}

?>