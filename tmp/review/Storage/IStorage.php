<?php

/**
 * IStorage
 * Dies ist das Interface, welches jede Speicherklasse implementieren muss.
 * Mit einer Instanz einer Speicherklasse kann auf gleiche Art und Weise
 * auf Daten in verschiedenen Speichermedien zugegriffen werden. So kann
 * sehr flexibel entwickelt werden und Schlüssel-Werte-Paare auf
 * verschiedenste Art aber mit gleicher Schnittstelle Persistent gehalten
 * werden.
 *
 * Mit den {@link getValue} und {@link setValue} Methoden kann ein
 * Wertepaar geschrieben bzw. der Wert zu einem Schlüssel gelesen werden.
 *
 * Um einen Wert aus dem Speicher zu löschen, kann die {@link removeValue}
 * Methode verwendet werden.
 *
 * Da das Speichern eines Objektes je nach Implementierung die
 * Serialisierung des Selbigen nach sich ziehen kann, ist zu beachten, dass
 * vorhandene __sleep- und __wakeup-Methoden aufgerufen werden.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Storage
 * @since 1.0 - 02.05.2008
 * @version 1.0
 */
interface IStorage {

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
	public function getValue( $entry );

	/**
	 * Setzt den Wert zu dem Schlüssel und legt diesen im Speicher ab.
	 *
	 * @throws IOException Der Wert konnte nicht im Speicher abgelegt
	 * werden.
	 * @return void
	 * @param string Der Schlüssel, unter dem der Wert abgelegt werden soll.
	 * @param mixed Der Wert selbst.
	 */
	public function setValue( $entry, $value );

	/**
	 * Entfernt den Wert zu dem übergebenen Schlüssel aus dem Speicher.
	 *
	 * Ist dieser Schlüssel nicht vorhanden, wird dies nicht weiter
	 * beachtet.
	 *
	 * @return void
	 * @param string Der Schlüssel, unter dem der Wert abgelegt wurde.
	 */
	public function removeValue( $entry );

}

?>