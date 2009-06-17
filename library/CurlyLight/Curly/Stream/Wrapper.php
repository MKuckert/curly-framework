<?php

/**
 * Curly_Stream_Wrapper
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
abstract class Curly_Stream_Wrapper {
	
	/**
	 * @var resource Wrapper-Kontext.
	 */
	public $context=NULL;
	
	/**
	 * Registriert eine Klasse als Streamwrapper.
	 * 
	 * @return boolean
	 * @param string Der Protokoll-Name
	 * @param string Die zu registrierende Klasse
	 */
	static public function register($protocol, $class=__CLASS__) {
		return(stream_wrapper_register($protocol, $class));
	}
	
	/**
	 * Öffnet diesen Wrapper.
	 * 
	 * @return boolean true, wenn der Stream erfolgreich geöffnet wurde.
	 * @param string Der zu verwendende Pfad
	 * @param string Der Modus, mit dem dieser Wrapper geöffnet wurde
	 * @param integer Weitere Optionen als Bitfeld. Folgende Werte können
	 *  enthalten sein:
	 * 	 - STREAM_USE_PATH: Wenn $path relativ ist, soll die
	 * 						include_path-Konfiguration zum Auflösen
	 * 						verwendet werden. Wurde der Stream korrekt
	 * 						geöffnet, soll der 4. Parameter auf den vollen Pfad
	 * 						gesetzt werden.
	 *   - STREAM_REPORT_ERRORS: Wenn dieses Flag gesetzt ist, sollen bei
	 * 						Fehlern mit Hilfe von {@link trigger_error}
	 * 						Fehlermeldungen ausgegeben werden. Sonst nicht.
	 * @param string 
	 */
	public function stream_open($path, $mode, $options, $opened_path) {
		
	}
	
	/**
	 * Schließt diesen StreamWrapper.
	 * 
	 * @return void
	 */
	public function stream_close() {
		
	}
	
	/**
	 * Ließt Maximal $count Bytes aus dem Stream und gibt diese zurück. Die
	 * aktuelle Position im Stream muss ebenfalls aktualisiert werden.
	 * Sind keine Daten mehr vorhanden, muss false oder eine leere Zeichenkette
	 * zurückgegeben werden.
	 * 
	 * @return string
	 * @param integer Maximal zu lesende Bytes
	 */
	public function stream_read($count) {
		
	}
	
	/**
	 * Schreibt die übergebenen Daten in diesen Strom und gibt die Anzahl
	 * geschriebener Bytes zurück. Können keine Daten geschrieben werden, so
	 * muss 0 zurückgegeben werden.
	 * 
	 * @return integer Anzahl geschriebener Bytes
	 * @param string Zu schreibende Daten
	 */
	public function stream_write($data) {
		
	}
	
	/**
	 * Gibt einen boolschen Wert zurück, ob die Lese-/Schreibposition am Ende
	 * des Datenstroms steht.
	 * 
	 * @return boolean
	 */
	public function stream_eof() {
		
	}
	
	/**
	 * Gibt die aktuelle Lese-/Schreibposition zurück.
	 * 
	 * @return integer
	 */
	public function stream_tell() {
		
	}
	
	/**
	 * Ändert die aktuelle Lese-/Schreibposition auf den mit den
	 * Übergabeparametern definierten Wert.
	 * 
	 * @return boolean true bei Erfolg.
	 * @param integer Offset-Wert
	 * @param integer Referenz-Position
	 */
	public function stream_seek($offset, $whence) {
		
	}
	
	/**
	 * Leert alle Datenpuffer.
	 * 
	 * @return boolean true bei Erfolg.
	 */
	public function stream_flush() {
		
	}
	
	/**
	 * Gibt statistische Daten zu diesem Stream zurück.
	 * 
	 * @return array
	 */
	public function stream_stat() {
		
	}
	
}
