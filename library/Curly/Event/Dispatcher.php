<?php

require_once "Curly/Event/Dispatcher/Interface.php";

/**
 * Curly_Event_Dispatcher
 * 
 * Eine Basisimplementierung des
 * {@link Curly_Types_Event_Interface}-Interfaces.
 *
 * Eine Instanz dieser Klasse akzeptiert keine beliebigen Werte für den
 * Ereignistyp, sondern zu einem Event muss eine Methode besitzen, deren
 * Name mit der Zeichenkette "on" beginnt. Die dispatchEvent-Methode ruft
 * vor dem Aufruf der registrierten Callbacks die entsprechende Methode
 * auf. Gibt diese Methode den boolschen Wert false zurück, so werden die
 * Callbacks nicht aufgerufen.
 *
 * Diese Klasse verwendet intern Curly_Types_Delegate-Instanzen, um die
 * Callbacks zu verwalten.
 *
 * Beispiel einer ableitenden Klasse:
 * <code>
 *  class EventDispatcherExample extends Curly_Event_Dispatcher {
 *      public function onMyEvent( Curly_Event_Parameter $param ) {
 *          echo "MyEvent ausgelöst - ";
 *      }
 *      public function doSomething() {
 *          echo "doSomething - ";
 *          $this->dispatchEvent( 'MyEvent' );
 *      }
 *  }
 *  function myCallback() {
 *      echo "myCallback - ";
 *  }
 *  function myCallback2() {
 *      echo "myCallback2";
 *  }
 *  $example = new EventDispatcherExample();
 *  $example->addEventListener( 'MyEvent', 'myCallback' );
 *  $example->addEventListener( 'MyEvent', 'myCallback2' );
 *  $example->doSomething();
 *  // Erzeugt die Ausgabe:
 *  // doSomething - MyEvent ausgelöst - myCallback - myCallback2
 * </code>
 *
 * Durch die magischen Methoden {@link __get} und {@link __set} und der
 * ArrayAccess-Implementierung in der Delegate-Klasse sind folgende Aufrufe
 * equivalent:
 * <code>
 *  $example->addEventListener( 'MyEvent', 'myCallback' );
 *  $example->MyEvent = 'myCallback';
 *  $example->OnMyEvent = 'myCallback';
 *  $example->MyEvent[] = 'myCallback';
 *  $example->OnMyEvent[] = 'myCallback';
 * </code>
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Events
 * @since 1.0 - 07.05.2008
 * @version 1.0
 */
abstract class Curly_Event_Dispatcher implements Curly_Event_Dispatcher_Interface {

	/**
	 * @var array Liste der Delegate zu den einzelnen Eventtypen.
	 */
	private $eventListener = array();
	
	/**
	 * Fügt diesem Ereignisauslöser den Callback $callback für das Event
	 * $type hinzu.
	 *
	 * Vorsicht: Diese Methode prüft nicht, ob der übergebene Callback an
	 * dem Ereignis registriert ist. D.h. es kann vorkommen, dass ein
	 * Callback bei einem Ereignis mehrfach ausgeführt wird.
	 *
	 * @throws InvalidArgumentException wenn kein Ereignis vom Typ $type
	 *  vorhanden ist oder $callback kein gültiger Callback ist.
	 * @return void
	 * @param string
	 * @param callback
	 */
	public function addEventListener( $type, $callback ) {
		if( !Curly_Util_TypeInfo::isCallback( $callback ) ) {
			throw new InvalidArgumentException( 'The parameter $callback is no valid callback' );
		}
			
		$type = $this->validateType( $type );
		if( !isset( $this->eventListener[$type] ) ) {
			$this->eventListener[$type] = new Curly_Types_Delegate();
		}
		$this->eventListener[$type]->add( $callback );
	}

	/**
	 * Löst das Ereignis vom Typ $type aus. Dadurch wird die Methode mit
	 * dem Namen on$type aufgerufen. Gibt diese Methode einen Wert ungleich
	 * false zurück, werden alle registrierten Callbacks aufgerufen und
	 * diesem die Curly_Event_Parameter_Interface-Instanz $event übergeben.
	 *
	 * @throws InvalidArgumentException wenn kein Ereignis vom Typ $type
	 *  vorhanden ist.
	 * @return void
	 * @param string
	 * @param Curly_Event_Parameter_Interface
	 */
	public function dispatchEvent( $type, Curly_Event_Parameter_Interface $event = null ) {
		$type = $this->validateType( $type );
		if( $this->{'on'.$type}( $event )!==false ) {
			if( !isset( $this->eventListener[$type] ) ) {
				return;
			}
			$this->eventListener[$type]->invoke( $event );
		}
	}
	
	/**
	 * Überprüft, ob zu dem Ereignis vom Typ $type Callbacks registriert
	 * sind und gibt entsprechend einen boolschen Wert zurück.
	 *
	 * @throws InvalidArgumentException wenn kein Ereignis vom Typ $type
	 *  vorhanden ist.
	 * @return boolean
	 * @param string
	 */
	public function hasEventListener( $type ) {
		$type = $this->validateType( $type );
		return(
			isset( $this->eventListener[$type] )
			and !$this->eventListener[$type]->isEmpty()
		);
	}

	/**
	 * Entfernt den Callback $callback von dem Ereignis vom Typ $type.
	 *
	 * @throws InvalidArgumentException wenn kein Ereignis vom Typ $type
	 *  vorhanden ist.
	 * @return void
	 * @param string
	 * @param callback
	 */
	public function removeEventListener( $type, $callback ) {
		$type = $this->validateType( $type );
		if( !isset( $this->eventListener[$type] ) ) {
			return;
		}
		
		$this->eventListener[$type]->remove( $callback );
	}

	/**
	 * Mit dieser Methode wird die Delegate-Instanz zu dem Ereignis vom Typ
	 * $type zurückgegeben.
	 *
	 * @throws InvalidArgumentException wenn kein Ereignis vom Typ $type
	 *  vorhanden ist.
	 * @return Delegate
	 * @param string
	 */
	public function getEventListener( $type ) {
		$type = $this->validateType( $type );
		if( !isset( $this->eventListener[$type] ) ) {
			$this->eventListener[$type] = new Delegate();
		}
		return( $this->eventListener[$type] );
	}

	/**
	 * Diese Methode ist eine alternative Aufrufmöglichkeit für die
	 * {@link getEventListener}-Methode. Sie ermöglicht das Lesen einer
	 * Delegate-Instanz in Form einer Eigenschaftsleseoperation. Die
	 * Eigenschaft ist dabei der Name des Ereignisses oder der zugehörigen
	 * Ereignismethode.
	 *
	 * Folgende Aufrufe sind equivalent:
	 * <code>
	 *  $listener = $dispatcher->getEventListener( 'MyEvent' );
	 *  $listener = $dispatcher->MyEvent;
	 *  $listener = $dispatcher->OnMyEvent;
	 * </code>
	 *
	 * @throws InvalidArgumentException wenn kein Ereignis vom Typ $type
	 *  vorhanden ist.
	 * @return Delegate
	 * @param string
	 */
	public function __get( $type ) {
		return( $this->getEventListener( $type ) );
	}

	/**
	 * Diese Methode ist eine alternative Aufrufmöglichkeit für die
	 * {@link addEventListener}-Methode. Sie ermöglicht die
	 * Callbackanfügung in Form einer Eigenschaftsmanipulation. Die
	 * Eigenschaft ist dabei der Name des Ereignisses oder der zugehörigen
	 * Ereignismethode.
	 *
	 * Folgende Aufrufe sind equivalent:
	 * <code>
	 *  $dispatcher->addEventListener( 'MyEvent', 'myCallback );
	 *  $dispatcher->MyEvent = 'myCallback';
	 *  $dispatcher->OnMyEvent = 'myCallback';
	 * </code>
	 *
	 * @throws InvalidArgumentException wenn kein Ereignis vom Typ $type
	 *  vorhanden ist oder $callback kein gültiger Callback ist.
	 * @return void
	 * @param string
	 * @param callback
	 */
	public function __set( $type, $callback ) {
		$this->addEventListener( $type, $callback );
	}

	/**
	 * Diese Methode prüft, ob ein Ereignis vom Typ $type vorhanden ist.
	 * Ist dies der Fall, so wird der korrekte Typ dieses Ereignisses
	 * zurückgegeben, andernfalls wird eine InvalidArgumentException
	 * geworfen.
	 *
	 * @throws InvalidArgumentException wenn kein Ereignis vom Typ $type
	 *  vorhanden ist.
	 * @return string
	 * @param string
	 */
	public function validateType( $type ) {
		$type = strtolower( Curly_Types_Ensure::string( $type ) );
		if( substr( $type, 0, 2 )!='on' ) {
			$method = 'on'.$type;
		}
		else {
			$method = $type;
			$type = substr( $type, 2 );
		}
		if( !method_exists( $this, $method ) ) {
			throw new InvalidArgumentException( 'There is no event of type '.$type.' defined' );
		}
		return( $type );
	}
	
}

?>