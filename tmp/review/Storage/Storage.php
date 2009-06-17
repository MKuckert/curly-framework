<?php

/**
 * Storage
 * Diese Klasse bietet die zwei Methoden {@link serialize} und
 * {@link unserialize}. In verschiedenen PHP-Versionen gibt es kleine Bugs
 * mit dem Serialisieren von Objekten. Diese Methoden umgehen diesen Bug.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Storage
 * @since 1.0 - 02.05.2008
 * @version 1.0
 */
class Storage {

	/**
	 * Serialisiert den übergebenen Wert und gibt die serialisierten Daten
	 * zurück.
	 *
	 * @return string
	 * @param mixed Die zu serialisierenden Daten.
	 */
	static public function serialize( $value ) {
		return( serialize( array( $value ) ) );
	}

	/**
	 * Deserialisiert die übergebenen serialisierten Daten und gibt die
	 * deserialisierten Werte zurück.
	 *
	 * @return mixed
	 * @param string Die serialisierten Daten.
	 */
	static public function unserialize( $serial ) {
		$arr = @unserialize( $serial );
		if( $arr===false ) {
			return( $serial );
		}
		return( isset( $arr[0] ) ? $arr[0] : null );
	}

}

?>