<?php

/**
 * Curly_Types_Collection
 * 
 * Eigene ArrayObject-Implementierung mit weiteren Funktionen.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types
 * @since 1.0 - 15.03.2008
 * @version 1.1
 */
class Curly_Types_Collection implements IteratorAggregate, ArrayAccess, Countable {
	
	/**
	 * @var array Daten dieser Collection
	 */
	private $_l = array();
	
	/**
	 * @var boolean Flag, ob die Collection-Elemente über den Zugriffsoperator
	 * erreicht werden können.
	 */
	private $_arrayAsProps = false;
	
	/**
	 * @var string Trennzeichen für die {@link join}-Methode.
	 */
	private $_delimiter;
	
	/**
	 * @var string Die zu verwendende Iterator-Klasse
	 */
	private $_iteratorClass = 'ArrayIterator';
	
	/**
	 * Konstruktor
	 * 
	 * @param mixed Entweder array oder Traversable. Ansonsten werden alle
	 *  übergebenen Parameter als Array verwendet.
	 */
	public function __construct( $def=array() ) {
		if( !is_array( $def ) and !( $def instanceof Traversable ) ) {
			$def = func_get_args();
		}
		$this->mergeWith( $def );
	}

	/**
	 * Fügt diesem Collection-Objekt eine Sammlung von Daten hinzu.
	 *
	 * @return void
	 * @param mixed Die anzufügenden Daten
	 * @param boolean Wird dieses Flag auf true gesetzt, werden die Indizes
	 *  ignoriert und die Datensätze einzeln angefügt.
	 */
	public function mergeWith( $data, $ignoreIndizes=false ) {
		$data = Curly_Types_Ensure::arr( $data );
		
		if( $ignoreIndizes ) {
			foreach( $data as $value ) {
				$this->append( $value );
			}
		}
		else {
			foreach( $data as $key=>$value ) {
				$this->set( $key, $value );
			}
		}
	}
	
	/**
	 * Tauscht den Inhalt dieser Instanz aus.
	 * 
	 * @return void
	 * @param mixed
	 */
	public function exchangeArray( $data ) {
		$this->clear();
		$this->mergeWith( $data );
	}

	/**
	 * Verknüpft alle Elemente in dieser Collection mit der übergebenen Zeichenkette und
	 * gibt ein String zurück.
	 *
	 * @return string
	 * @param string Trennzeichenkette. Standardwert ist ", "
	 */
	public function join( $delimiter=', ' ) {
		$this->_delimiter = $delimiter;
		return( $this->_join( $this->_l ) );
	}
	
	/**
	 * Interne Hilfsmethode für die {@link join}-Methode.
	 * 
	 * @return string
	 * @param array
	 */
	protected function _join( array $list ) {
		foreach( $list as $key=>$value ) {
			if( is_array( $value ) ) {
				$list[$key] = $this->_join( $value );
			}
			else if( is_object( $value ) ) {
				$list[$key] = $this->_join( Curly_Types_Ensure::arr( $value ) );
			}
		}
		return( implode( $this->_delimiter, $list ) );
	}

	/**
	 * Verwirft den Inhalt dieser Collection.
	 *
	 * @return void
	 */
	public function clear() {
		$this->_l = array();
	}
	
	/**
	 * Überprüft, ob in dieser Collection der übergebene Schlüssel gesetzt ist.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	public function keyExists( $offset ) {
		return( array_key_exists( Curly_Types_Ensure::string( $offset ), $this->_l ) );
	}
	
	/**
	 * Gibt den Wert zu dem übergebenen Schlüssel zurück.
	 * Ist der Wert noch nicht vorhanden, so wird der Wert $default
	 * zurückgegeben.
	 * 
	 * @return mixed
	 * @param mixed
	 */
	public function get( $offset, $default=null ) {
		$offset = Curly_Types_Ensure::string( $offset );
		return( isset( $this->_l[$offset] ) ? $this->_l[$offset] : $default );
	}
	
	/**
	 * Gibt den Wert zu dem übergebenen Schlüssel als Referenz zurück.
	 * Ist der Wert noch nicht vorhanden, so wird der Wert $default
	 * zurückgegeben.
	 * 
	 * @return mixed&
	 * @param mixed&
	 */
	public function &getRef( $offset, &$default=null ) {
		$offset = Curly_Types_Ensure::string( $offset );
		return isset( $this->_l[$offset] ) ? $this->_l[$offset] : $default;
	}
	
	/**
	 * Setzt den Wert zu dem übergebenen Schlüssel.
	 * 
	 * @return mixed
	 * @param mixed
	 * @param mixed
	 */
	public function set( $offset, $value ) {
		$offset = Curly_Types_Ensure::string( $offset );
		return( $this->_l[$offset] = $value );
	}
	
	/**
	 * Setzt den Wert zu dem übergebenen Schlüssel als Referenz.
	 * 
	 * @return mixed&
	 * @param mixed
	 * @param mixed&
	 */
	public function &setRef( $offset, &$value ) {
		$offset = Curly_Types_Ensure::string( $offset );
		$this->_l[$offset] = &$value;
		return $this->_l[$offset];
	}
	
	/**
	 * Entfernt den übergebenen Schlüssel aus dieser Collection.
	 * 
	 * @return void
	 * @param mixed
	 */
	public function remove( $offset ) {
		unset( $this->_l[Curly_Types_Ensure::string( $offset )] );
	}
	
	/**
	 * Fügt dieser Collection einen neuen Wert hinzu.
	 * 
	 * @return void
	 * @param mixed
	 */
	public function append( $value ) {
		$this->_l[] = $value;
	}
	
	/**#@+
	 * Funktion für das IteratorAggregate-Interface
	 * 
	 * Gibt einen Iterator auf diese Instanz zurück.
	 * 
	 * @return Iterator
	 */
	public function getIterator() {
		$class = $this->_iteratorClass;
		return( new $class( $this->toArray() ) );
	}
	
	/**
	 * Gibt die zu verwendende Iteratorklasse zurück.
	 * 
	 * @return string
	 */
	public function getIteratorClass() {
		return( $this->_iteratorClass );
	}
	
	/**
	 * Setzt die zu verwendende Iteratorklasse.
	 * 
	 * @return void
	 * @param string
	 */
	public function setIteratorClass( $class ) {
		$this->_iteratorClass = Curly_Types_Ensure::string( $class );
	}
	
	/**
	 * Gibt oder setzt das ArrayAsProps-Flag.
	 * 
	 * Wird ein Parameter übergeben, so wird dieser Wert gesetzt. Der alte
	 * Wert wird zurückgegeben.
	 * 
	 * @return boolean
	 * @param boolean
	 */
	public function useArrayAsProps( $flag=null ) {
		$val = $this->_arrayAsProps;
		if( !is_null( $flag ) ) {
			$this->_arrayAsProps = (bool)$flag;
		}
		return( $val );
	}
	
	/**#@+
	 * Funktion für das ArrayAccess-Interface
	 */
	/**
	 * Überprüft, ob in dieser Collection der übergebene Schlüssel gesetzt ist.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	public function offsetExists( $offset ) {
		return( $this->keyExists( $offset ) );
	}
	
	/**
	 * Gibt den Wert zu dem übergebenen Schlüssel zurück.
	 * Ist der Wert noch nicht vorhanden, so wird der Wert $default
	 * zurückgegeben.
	 * 
	 * @return mixed
	 * @param mixed
	 */
 	public function offsetGet( $offset ) {
 		return( $this->get( $offset ) );
 	}
	
	/**
	 * Setzt den Wert zu dem übergebenen Schlüssel.
	 * 
	 * @return mixed
	 * @param mixed
	 * @param mixed
	 */
 	public function offsetSet( $offset, $value ) {
 		if( $offset===NULL ) {
 			$this->append( $value );
		}
		else {
 			$this->set( $offset, $value );
		}
 	}
	
	/**
	 * Entfernt den übergebenen Schlüssel aus dieser Collection.
	 * 
	 * @return void
	 * @param mixed
	 */
 	public function offsetUnset( $offset ) {
 		$this->remove( $offset );
 	}
	/**#@-*/
	
	/**
	 * Überprüft, ob in dieser Collection der übergebene Schlüssel gesetzt ist,
	 * wenn das {@link useArrayAsProps}-Flag gesetzt ist.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	public function __isset( $offset ) {
		if( $this->_arrayAsProps ) {
			return( $this->keyExists( $offset ) );
		}
		else {
			throw new MemberNotDefinedException( 'Unable to check the existence of the item '.$offset );
		}
	}
	
	/**
	 * Gibt den Wert zu dem übergebenen Schlüssel zurück.
	 * Ist der Wert noch nicht vorhanden, so wird der Wert $default
	 * zurückgegeben.
	 * 
	 * @return mixed
	 * @param mixed
	 */
 	public function __get( $offset ) {
		if( $this->_arrayAsProps ) {
			return( $this->get( $offset ) );
		}
		else {
			throw new MemberNotDefinedException( 'Unable to get the item '.$offset );
		}
 	}
	
	/**
	 * Setzt den Wert zu dem übergebenen Schlüssel.
	 * 
	 * @return mixed
	 * @param mixed
	 * @param mixed
	 */
 	public function __set( $offset, $value ) {
		if( $this->_arrayAsProps ) {
	 		$this->set( $offset, $value );
		}
		else {
			throw new MemberNotDefinedException( 'Unable to set the item '.$offset );
		}
 	}
	
	/**
	 * Entfernt den übergebenen Schlüssel aus dieser Collection.
	 * 
	 * @return void
	 * @param mixed
	 */
 	public function __unset( $offset ) {
		if( $this->_arrayAsProps ) {
 			$this->remove( $offset );
		}
		else {
			throw new MemberNotDefinedException( 'Unable to unset the item '.$offset );
		}
 	}
	
	/**
	 * Funktion für das Countable-Interface
	 * 
	 * Gibt die Anzahl Elemente in dieser Collection zurück.
	 * 
	 * @return integer
	 */
	public function count() {
		return( count( $this->_l ) );
	}
	
	/**
	 * Gibt den Inhalt dieser Collection als Array zurück.
	 * 
	 * @return array
	 */
	public function toArray() {
		return( $this->_l );
	}
	
	/**
	 * Gibt den Inhalt dieser Collection als String zurück.
	 * 
	 * @return string
	 */
	public function toString() {
		return( $this->join() );
	}
	
	/**
	 * Gibt den Inhalt dieser Collection als String zurück.
	 * 
	 * @return string
	 */
	public function __toString() {
		return( $this->join() );
	}
	
	/**
	 * Ändert die Groß- und Kleinschreibung aller Schlüssel.
	 * 
	 * @return void
	 * @param integer CASE_UPPER oder CASE_LOWER (Standardwert)
	 */
	public function changeKeyCase( $case=CASE_LOWER ) {
		$this->_l = array_change_key_case( $this->_l, $case );
	}
	
	/**
	 * Ändert die Groß- und Kleinschreibung aller Schlüssel in Großbuchstaben
	 * 
	 * @return void
	 */
	public function changeKeyCaseToUpper() {
		$this->_l = array_change_key_case( $this->_l, CASE_UPPER );
	}
	
	/**
	 * Ändert die Groß- und Kleinschreibung aller Schlüssel in Kleinbuchstaben
	 * 
	 * @return void
	 */
	public function changeKeyCaseToLower() {
		$this->_l = array_change_key_case( $this->_l, CASE_LOWER );
	}
	
	/**
	 * Splittet diese Collection in mehrere Teile mit der Größe $size auf.
	 * 
	 * @return array
	 * @param integer
	 * @param boolean
	 */
	public function chunk( $size, $preserveKeys=false ) {
		// @TODO Implementieren
	}

}