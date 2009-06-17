<?php

/**
 * Curly_Types_Collection_Serial
 * 
 * Stellt eine Erweiterung der Curly_Types_Collection-Klasse dar, die
 * ausschlieﬂlich Inhalte mit nummerischem, fortlaufenden Index erlaubt.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types.Collection
 * @since 1.0 - 15.03.2008
 * @version 1.0
 */
class Curly_Types_Collection_Serial extends Curly_Types_Collection {

	/**
	 * Konstruktor
	 *
	 * @throws InvalidArgumentException
	 * @param array|Traversable
	 */
	public function __construct( $def=array() ) {
		if( is_array( $def ) ) {
			parent::__construct( array_values( $def ) );
		}
		else if( $def instanceof Traversable ) {
			parent::__construct();
			$this->mergeWith( $def );
		}
		else {
			throw new InvalidArgumentException( 'The given value is not an array or Traversable!' );
		}
	}

	/**
	 * Kontrolliert, das ausschlieﬂlich nummerische Indizes
	 * verwendet werden.
	 *
	 * @throws OutOfRangeException
	 * @return void
	 * @param mixed Index
	 * @param mixed Wert
	 */
	public function offsetSet( $index, $value ) {
		$index = intval( $index );
		if( $index<0 ) {
			throw new OutOfRangeException( 'The given index '.$index.' has to be a positive integer!' );
		}
		else if( $index>$this->count() ) {
			throw new OutOfRangeException( 'The given index '.$index.' has to be smaller than the actual array size!' );
		}
		parent::offsetSet( $index, $value );
	}

	/**
	 * F¸gt einen Wert an dem ¸bergebenen Index ein und verschiebt
	 * alle nachfolgenden Werte nach hinten.
	 * Wirft eine OutOfRangeException, wenn der ¸bergebene Index nicht
	 * g¸ltig ist.
	 *
	 * @throws OutOfRangeException
	 * @return void
	 * @param mixed Index
	 * @param mixed Wert
	 */
	public function insertAt( $index, $value ) {
		if( $index==$this->count() ) {
			$this->offsetSet( $index, $value );
			return;
		}
		else if( !$this->offsetExists( $index ) ) {
			throw new OutOfRangeException( 'There is no value at index '.$index );
		}
			
		for( $i=$this->count(); $i>=$index; $i-- ) {
			$this->offsetSet( $i, $this->offsetGet( $i-1 ) );
		}
			
		$this->offsetSet( $index, $value );
	}

	/**
	 * F¸gt diesem Collection-Objekt eine Sammlung von Daten hinzu.
	 *
	 * @return void
	 * @param mixed Die anzuf¸genden Daten
	 * @param boolean Wird in dieser Implementation nicht genutzt
	 */
	public function mergeWith( $data, $IGNORED_PARAM=false ) {
		parent::mergeWith($data, true);
	}

	/**
	 * F¸gt diesem Collection-Objekt eine Sammlung von Daten an der ¸bergebenen
	 * Position hinzu, d.h. das Element welches sich jetzt an der Position
	 * befindet und alle nachfolgenden werden nach hinten verschoben.
	 *
	 * @return void
	 * @param mixed Der Index, ab dem die Daten eingef¸gt werden sollen.
	 * @param mixed Die anzuf¸genden Daten
	 */
	public function mergeWithAt( $index, $data ) {
		$data=Curly_Types_Ensure::arr($data);
		array_splice($this->_l, $index, 0, $data);
	}
	
	/**
	 * Tauscht den Inhalt dieser Instanz aus.
	 * 
	 * @return void
	 * @param mixed
	 */
	public function exchangeArray( $data ) {
		$data=Curly_Types_Ensure::arr($data);
		$this->_l=array_values($data);
	}

	/**
	 * Entfernt einen Wert an dem ¸bergebenen Index und verschiebt
	 * alle nachfolgenden Werte nach vorne.
	 * Wirft eine OutOfRangeException, wenn der ¸bergebene Index nicht
	 * g¸ltig ist.
	 *
	 * @throws OutOfRangeException
	 * @return mixed Der entfernte Wert
	 * @param mixed Index
	 */
	public function remove( $index ) {
		if( !$this->offsetExists( $index ) ) {
			throw new OutOfRangeException( 'There is no value at index '.$index );
		}
		
		$retval=$this->splice($index, 1);
		return(reset($retval));
	}

	/**
	 * Entfernt eine Reihe von Werten ab dem ¸bergebenen Index und verschiebt
	 * alle nachfolgenden Werte nach vorne.
	 * Wirft eine OutOfRangeException, wenn der ¸bergebene Index nicht
	 * g¸ltig ist.
	 *
	 * @throws OutOfRangeException
	 * @return array Die entfernten Werte
	 * @param integer Index
	 * @param integer Anzahl zu lˆschender Elemente
	 */
	public function removeRange( $index, $count ) {
		return $this->splice($index, $count);
	}

	/**
	 * Entfernt eine Reihe von Werten ab dem ¸bergebenen Index und verschiebt
	 * alle nachfolgenden Werte nach vorne. Die Werte des dritten Arguments
	 * werden neu in diese Collection eingef¸gt.
	 * Wirft eine OutOfRangeException, wenn der ¸bergebene Index nicht
	 * g¸ltig ist.
	 *
	 * @throws OutOfRangeException
	 * @return array Die entfernten Werte
	 * @param integer Index
	 * @param integer Anzahl zu lˆschender Elemente
	 * @param array Liste einzuf¸gender Werte.
	 */
	public function splice( $index, $count, $insert=array() ) {
		if( !$this->offsetExists( $index ) ) {
			throw new OutOfRangeException( 'There is no value at index '.$index );
		}
		
		$retval=array_splice($this->_l, $index, $count, $insert);
		$this->_l=array_values($this->_l);
		return($retval);
	}

}

?>