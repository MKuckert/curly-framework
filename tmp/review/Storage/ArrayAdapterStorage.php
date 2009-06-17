<?php

/**
 * ArrayAdapterStorage
 * Implementiert das {@link IStorage} Interface auf Basis eines
 * assoziativen Arrays.
 *
 * Dieses Array kann über die Eigenschaft {@link getAdapted Adapted}
 * direkt modifiziert werden.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Storage
 * @since 1.0 - 02.05.2008
 * @version 1.0
 */
class ArrayAdapterStorage implements IStorage {

	/**
	 * @var array Das diesem Speicher zugrundeliegende Array.
	 */
	private $_adapted = array();

	/**
	 * Gibt das diesem Speicher zugrundeliegende Array zurück.
	 *
	 * @return array&
	 */
	public function &getAdapted() {
		return $this->_adapted;
	}

	/**
	 * Setzt das diesem Speicher zugrundeliegende Array.
	 *
	 * @return void
	 * @param array&
	 */
	public function setAdapted( array &$adapted ) {
		$this->_adapted = &$adapted;
	}

	/**
	 * Lädt einen Wert zu einem Schlüssel aus dem Speicher und gibt diesen
	 * zurück.
	 *
	 * @throws NotFoundException Der Wert wurde nicht im Speicher gefunden
	 * bzw. es wurde noch kein Wert unter dem übergebenen Schlüssel
	 * gespeichert.
	 * @return mixed
	 * @param string Der Schlüssel, zu dem der Wert geladen werden soll.
	 */
	public function getValue( $entry ) {
		if( !isset( $this->_adapted[$entry] ) ) {
			throw new NotFoundException( 'There is no value for the key '.$entry.' stored' );
		}
		return( Storage::unserialize( $this->_adapted[$entry] ) );
	}

	/**
	 * Setzt den Wert zu dem Schlüssel und legt diesen im Speicher ab.
	 *
	 * @throws IOException Der Wert konnte nicht im Speicher abgelegt
	 * werden.
	 * @return void
	 * @param string Der Schlüssel, unter dem der Wert abgelegt werden soll.
	 * @param mixed Der Wert selbst.
	 */
	public function setValue( $entry, $value ) {
		$type = gettype( $value );
		if( $type=="array" or $type=="object" or $type=="resource" or $type=="NULL" or $type=="unknown type" ) {
			$value = Storage::serialize( $value );
		}

		$this->_adapted[$entry] = $value;
	}

	/**
	 * Entfernt den Wert zu dem übergebenen Schlüssel aus dem Speicher.
	 *
	 * Ist dieser Schlüssel nicht vorhanden, wird dies nicht weiter
	 * beachtet.
	 *
	 * @return void
	 * @param string Der Schlüssel, unter dem der Wert abgelegt wurde.
	 */
	public function removeValue( $entry ) {
		unset( $this->_adapted[$entry] );
	}

}

?>