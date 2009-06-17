<?php

/**
 * Curly_Types_Delegate
 * 
 * Eine Instanz dieser Klasse sammelt mehrere Curly_Types_Callback_Interface-Instanzen
 * und löst diese gemeinsam aus.
 *
 * Wichtig: Es wird lediglich die {@link offsetSet}-Methode des
 * ArrayAccess-Interface implementiert. Die anderen drei Methoden werfen
 * eine Curly_Exception-Instanz.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types
 * @since 1.0 - 07.05.2008
 * @version 1.0
 */
class Curly_Types_Delegate extends Curly_Types_Callback implements ArrayAccess {
	
	/**
	 * Konstruktor
	 * Die Verwendung dieses Konstruktors ist equivalent zu dem der
	 * Callback-Klasse, bis auf den Unterschied, das auch kein Argument
	 * übergeben werden kann.
	 */
	public function __construct( $arguments=null ) {
		$this->callback = array();
		if( !is_null( $arguments ) ) {
			if( func_num_args()>1 ) {
				$arguments = func_get_args();
			}
			$this->add( $arguments );
		}
	}
	
	/**
	 * Diese Methode führt die gekapselten Callbacks mit den übergebenen
	 * Argumenten aus. Es kann auch anstatt eines Arrays eine Liste von
	 * mehreren Argumenten verwendet werden, allerdings darf der erste
	 * Parameter dann kein Array mehr sein.
	 * 
	 * Der Rückgabewert ist ein Array mit den Rückgabewerten der einzelnen
	 * Callbacks.
	 * 
	 * @return array
	 * @param array
	 */
	public function invoke( $arguments=array() ) {
		if( !is_array( $arguments ) ) {
			$arguments = func_get_args();
		}
		
		$retvals = array();
		foreach( $this->callback as $callback ) {
			if( $callback instanceof Curly_Types_Callback_Interface ) {
				$retvals[] = $callback->invoke( $arguments );
			}
			else {
				$retvals[] = call_user_func_array( $callback, $arguments );
			}
		}
		return( $retvals );
	}
	
	/**
	 * Fügt diesem Delegat einen weiteren Callback hinzu.
	 * 
	 * Die Verwendung entspricht der des Konstruktors.
	 * 
	 * @throws InvalidArgumentException wenn die Kombination der
	 *  übergebenen Parameter nicht gültig ist.
	 * @return void
	 * @param mixed
	 */
	public function add( $arguments ) {
		if( func_num_args()>1 ) {
			$arguments = func_get_args();
		}
		
		if( !Curly_Util_TypeInfo::isCallback( $arguments ) ) {
			throw new InvalidArgumentException( 'The given arguments are not defining a valid callback' );
		}
		
		$this->callback[] = $arguments;
	}
	
	/**
	 * Entfernt ein Callback aus diesem Delegat.
	 *
	 * Die Verwendung entspricht der des Konstruktors.
	 * 
	 * @return void
	 * @param mixed
	 */
	public function remove( $argument ) {
		foreach( $this->callback as $key=>$callback ) {
			if(
				$callback instanceof Curly_Types_Callback_Interface and $callback->equals( $argument )
				or $callback==$argument
			) {
				unset( $this->callback[$key] );
			}
		}	}
	
	/**
	 * Überprüft, ob dieser Delegat über Callbacks verfügt.
	 * 
	 * @return boolean
	 */
	public function isEmpty() {
		return count( $this->callback ) <= 0;
	}
	
	/**
	 * Diese Implementierung des Arrayzugriffs ist eine alternative
	 * Aufrufsmöglichkeit für die {@link add}-Methode.
	 *
	 * Der übergebene Indexparameter $index wird dabei komplett ignoriert.
	 *
	 * @return void
	 * @param mixed
	 * @param mixed
	 */
	public function offsetSet( $index, $callback ) {
		$this->add( $callback );
	}
	
	/**#@+
	 * Diese Methode wird durch das ArrayAccess-Interface vorgeschrieben,
	 * ist jedoch nicht implementiert.
	 *
	 * @throws BadMethodCallException
	 */
	public function offsetGet( $offset ) {
		throw new Curly_Exception( 'This method is not implemented' );
	}
	public function offsetExists( $offset ) {
		throw new Curly_Exception( 'This method is not implemented' );
	}
	public function offsetUnset( $offset ) {
		throw new Curly_Exception( 'This method is not implemented' );
	}
	/**#@-*/
	
	/**
	 * Vergleicht dieses Objekt mit dem übergebenen Objekt und gibt true
	 * zurück, wenn es sich dabei um das gleiche Objekt handelt, d.h. beide
	 * Instanzen auf den gleichen Callback verweisen.
	 *
	 * @return boolean
	 * @param Object
	 */
	public function equals( $that ) {
		if( !( $that instanceof Curly_Types_Delegate ) ) {
			return false;
		}
		foreach( $this->callback as $key=>$value ) {
			if( !isset( $that->callback[$key] ) or $that->callback[$key]!=$value ) {
				return false;
			}
		}
		return true;
	}
	
}

?>