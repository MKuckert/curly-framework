<?php

require_once "Curly/Types/Callback/Interface.php";

/**
 * Curly_Types_Callback
 * 
 * Diese Klasse kapselt einen Callback und erlaubt das Ausführen dieses.
 *
 * Php bietet mehrere Typen von Callbacks an:
 *  - Callbacks von Funktionen
 *  - Callbacks von statischen Methoden
 *  - Callbacks von Objektmethoden
 * Um diese Typen einheitlich behandeln zu können, kann diese Klasse
 * eingesetzt werden.
 *
 * Beispiel für die Verwendung dieser Klasse:
 * <code>
 *  class MyClass {
 *      public function myMethod() {}
 *  }
 *
 *  $class = new MyClass();
 *  // Erzeugung mit einem Array-Parameter:
 *  $callback = new Curly_Types_Callback( array( $class, 'myMethod' ) );
 *  // Equivalent mit zwei Parametern:
 *  $callback = new Curly_Types_Callback( $class, 'myMethod' );
 *
 *  // Ausführung mit einem Array-Parameter:
 *  $callback->invoke( array( 1, 2, 3 ) );
 *  // Equivalent mit einzelnen Parametern:
 *  $callback->invoke( 1, 2, 3 );
 * </code>
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types
 * @since 1.0 - 07.05.2008
 * @version 1.0
 */
class Curly_Types_Callback implements Curly_Types_Callback_Interface {

	/**
	 * @var mixed Der gekapselte Callback.
	 */
	protected $callback;

	/**
	 * Konstruktor
	 * Der Aufruf dieses Konstruktors ist recht flexibel gehalten. Es kann
	 * eine Zeichenkette mit dem Namen einer Funktion, zwei Zeichenketten
	 * mit dem Namen einer Klasse und einer statischen Methode, oder ein
	 * Objekt und eine Zeichenkette mit dem Namen einer Methode übergeben
	 * werden.
	 * Ebenfalls können diese drei Varianten als Array übergeben werden.
	 *
	 * @throws InvalidArgumentException wenn die Kombination der
	 *  übergebenen Parameter nicht gültig ist.
	 * @param mixed
	 */
	public function __construct( $arguments ) {
		if( func_num_args()>1 ) {
			$arguments = func_get_args();
		}
			
		if( !Curly_Util_TypeInfo::isCallback( $arguments ) ) {
			throw new InvalidArgumentException( 'The given arguments are not defining a valid callback' );
		}
			
		$this->callback = $arguments;
	}

	/**
	 * Diese Methode führt den gekapselten Callback mit den übergebenen
	 * Argumenten aus. Es kann auch anstatt eines Arrays eine Liste von
	 * mehreren Argumenten verwendet werden, allerdings darf der erste
	 * Parameter dann kein Array mehr sein.
	 *
	 * Der Rückgabewert ist der des Callbacks.
	 *
	 * @return mixed
	 * @param array
	 */
	public function invoke( $arguments=array() ) {
		if( func_num_args()>1 or !is_array($arguments) ) {
			$arguments = func_get_args();
		}
			
		if( $this->callback instanceof Curly_Types_Callback ) {
			return $this->callback->invoke( $arguments );
		}
		else {
			return call_user_func_array( $this->callback, $arguments );
		}
	}
	
	/**
	 * Magische Methode für direkten Aufruf einer Instanz dieser Klasse als
	 * Funktor. Dieses Feature ist ab PHP 5.3 verfügbar.
	 * 
	 * @see http://docs.php.net/manual/language.oop5.magic.php#language.oop5.magic.invoke
	 * @return mixed
	 * @param array
	 */
	public function __invoke( $arguments=array() ) {
		$arguments=func_get_args();
		return call_user_func_array(array($this, 'invoke'), $arguments);
	}

	/**
	 * Vergleicht dieses Objekt mit dem übergebenen Objekt und gibt true
	 * zurück, wenn es sich dabei um das gleiche Objekt handelt, d.h. beide
	 * Instanzen auf den gleichen Callback verweisen.
	 *
	 * @return boolean
	 * @param Object
	 */
	public function equals( $that ) {
		return( $that instanceof Curly_Types_Callback and $this->callback===$that->callback );
	}
	
	/**
	 * Gibt ein Array zurück, welches für Funktionen wie call_user_func
	 * verwendet werden kann, also einen PHP-Callback repräsentiert.
	 * 
	 * @return array
	 */
	public function caller() {
		return array($this, 'invoke');
	}

}