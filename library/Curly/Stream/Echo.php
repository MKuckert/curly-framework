<?php

require_once "Curly/Stream.php";

/**
 * Curly_Stream_Echo
 * 
 * Stream für direkte Ausgabe
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 1.0 - 10.09.2008
 * @version 1.0
 */
class Curly_Stream_Echo extends Curly_Stream {
	
	/**
	 * Konstruktor
	 *
	 * Überschreibt den Curly_Stream-Konstruktor, so dass dieser ohne Parameter
	 * auskommt.
	 *
	 * @throws UnexpectedValueException Die fopen-Funktion kann keinen
	 *  Datenstrom auf die übergebene URI erzeugen.
	 * @throws BadMethodCallException Es wurde eine falsche Kombination von
	 * Parametern übergeben.
	 */
	public function __construct() {
		parent::__construct(NULL);
	}
	
	/**
	 * Öffnet einen Dateihandle auf die übergebene Ressource.
	 * 
	 * @return ressource
	 * @param string URI auf die zu verwendende Datenquelle.
	 * @param string Zugriffsmodus auf die Datenquelle.
	 */
	protected function open( $url, $mode ) {
		return(NULL);
	}
	
	/**
	 * Schließt diesen Datenstrom.
	 * 
	 * Hinweis: Der Datenstrom darf anschließend weder lesend noch schreibend
	 * verwendet werden.
	 * 
	 * @return void
	 */
	public function close() {
		// Nichts zu tun
	}

	/**
	 * Überprüft, ob der übergebene Parameter ein gültiger Datenstrom ist.
	 *
	 * @return boolean
	 * @param mixed $handle
	 */
	public function isValidHandle( $handle ) {
		return(true);
	}

	/**
	 * Führt intern die eigentliche Leseoperation durch.
	 * 
	 * @return string Die gelesenen Daten.
	 * @param int Die Anzahl zu lesender Bytes.
	 */
	protected function _read( $length ) {
		return('');
	}

	/**
	 * Führt intern die eigentliche Leseoperation von einer Zeile durch.
	 * 
	 * @return string Die gelesenen Daten.
	 * @param int Die Anzahl zu lesender Bytes.
	 * @param boolean true, wenn das Newline-Zeichen automatisch erkannt werden
	 *  soll. Wird false übergeben, so wird {@link getNewline Newline} als
	 *  Zeichen verwendet.
	 */
	protected function _readline( $length, $autodetectNewline ) {
		return('');
	}
	
	/**
	 * Führt intern die eigentliche Schreiboperation durch.
	 * 
	 * @return boolean
	 * @param string Die Eingabedaten.
	 */
	protected function _write( $value ) {
		echo $value;
		return(true);
	}
	
	/**
	 * Gibt die aktuelle Position des Dateizeigers im Datenstrom zurück.
	 * 
	 * @return integer
	 */
	protected function _tell() {
		return(0);
	}

	/**
	 * Setzt die aktuelle Position des Dateizeigers im Datenstrom um.
	 *
	 * @return boolean
	 * @param int Der zu verwendende Offset, relativ zu der mit $origin
	 *  angegebenen Position.
	 * @param int Die Position, zu der relativ die neue Position gesetzt werden
	 *  soll.
	 */
	protected function _seek( $offset, $origin ) {
		return(true);
	}
	
	/**
	 * Überprüft, ob noch weitere Daten in diesem Datenstrom vorhanden
	 * sind, die durch weitere Leseoperationen ausgelesen werden können.
	 *
	 * @return boolean
	 */
	public function getHasMore() {
		return(false);
	}
	
	/**
	 * Löscht sämtliche Puffer für diesen Datenstrom und veranlasst die
	 * Ausgabe aller gepufferten Daten an das Dateisystem.
	 *
	 * @return void
	 */
	public function flush() {
		// Nichts zu tun
	}
	
	/**
	 * Führt intern die truncate-Operation aus.
	 * 
	 * @return boolean
	 * @param integer
	 */
	protected function _truncate( $length ) {
		return(true);
	}
	
	/**
	 * Gibt intern die Metadaten zurück.
	 * 
	 * @return array
	 */
	protected function _getMetaData() {
		return( array(
			'timed_out'		=> false,
			'blocked'		=> false,
			'eof'			=> true,
			'unread_bytes'	=> 0,
			'stream_type'	=> 'Echo',
			'wrapper_type'	=> 'Stream',
			'wrapper_data'	=> '',
			'filters'		=> array(),
			'mode'			=> 'a+',
			'seekable'		=> true,
			'uri'			=> 'unknown'
		) );
	}
	
	/**
	 * Führt intern die resetPosition-Operation aus.
	 * 
	 * @return boolean
	 */
	protected function _resetPosition() {
		return(true);
	}

	/**
	 * Diese Methode ist für diesen Stream nicht implementiert.
	 *
	 * @throws BadMethodCallException
	 */
	public function lock( $mode, $block=true ) {
		throw new BadMethodCallException('The '.__METHOD__.' method is not implemented');
	}

	/**
	 * Gibt eine Zugriffssperre auf diesen Datenstrom wieder frei.
	 *
	 * @return boolean
	 */
	public function unlock() {
		throw new BadMethodCallException('The '.__METHOD__.' method is not implemented');
	}

}