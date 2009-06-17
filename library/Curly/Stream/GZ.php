<?php

require_once "Curly/Stream.php";

/**
 * Curly_Stream_GZ
 * 
 * Wrapperklasse um einen mit der ZLib-Extension komprimierter Datenstrom.
 * 
 * Hinweis:
 * 
 * Diese Implementierung unterstützt den $autodetectNewline-Parameter der
 * readline-Methode nicht und nimmt immer den Wert true an.
 * 
 * Es ist nur das direkte Setzen der Streamposition mit der
 * {@link setPosition}-Methode möglich, d.h. der $origin-Parameter nimmt immer
 * den Wert OriginBegin an.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 1.0 - 10.09.2008
 * @version 1.0
 */
class Curly_Stream_GZ extends Curly_Stream {
	
	/**
	 * Öffnet einen Dateihandle auf die übergebene Ressource.
	 * 
	 * @return ressource
	 * @param string URI auf die zu verwendende Datenquelle.
	 * @param string Zugriffsmodus auf die Datenquelle.
	 */
	protected function open( $url, $mode ) {
		return( gzopen( $url, $mode ) );
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
		gzclose( $this->getHandle() );
		$this->setHandle( NULL );
	}

	/**
	 * Führt intern die eigentliche Leseoperation durch.
	 * 
	 * @return string Die gelesenen Daten.
	 * @param int Die Anzahl zu lesender Bytes.
	 */
	protected function _read( $length ) {
		return( gzread( $this->getHandle(), $length ) );
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
		return( gzgets( $this->getHandle(), $length ) );
	}
	
	/**
	 * Führt intern die eigentliche Schreiboperation durch.
	 * 
	 * @return boolean
	 * @param string Die Eingabedaten.
	 */
	protected function _write( $value ) {
		return( gzwrite( $this->getHandle(), $value, strlen( $value ) )!==false );
	}
	
	/**
	 * Gibt die aktuelle Position des Dateizeigers im Datenstrom zurück.
	 * 
	 * @return integer
	 */
	protected function _tell() {
		return( gztell( $this->getHandle() ) );
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
		return( gzseek( $this->getHandle(), $offset )==0 );
	}

}