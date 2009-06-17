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
		
		$content=$this->toArray();
		$value=$content[$index];
		unset($content[$index]);
		$content=array_values($content);
		$this->exchangeArray($content);
		return($value);
	}

}

?>