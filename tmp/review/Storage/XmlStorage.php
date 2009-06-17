<?php

/**
 * XmlStorage
 * Implementiert {@link load Lade}- und {@link store Speicher}-Operationen
 * für die {@link ArrayAdapterStorage}-Klasse auf Basis von XML-Dateien.
 *
 * Über die {@link getFile File}-Eigenschaft kann der Dateiname gesetzt
 * werden, in dem die Speicherdaten abgelegt werden sollen bzw. aus der
 * gelesen wird.
 *
 * Die Speicherdaten werden im Speicher gehalten. Erst durch aufrufen der
 * store-Methode werden alle Daten in die Datei geschrieben.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Storage
 * @since 1.0 - 02.05.2008
 * @version 1.0
 */
class XmlStorage extends ArrayAdapterStorage {

	/**
	 * @var string Die zu verwendende Datei für Lese- und Schreiboperationen.
	 */
	private $_file = '';

	/**
	 * @var boolean Flag, ob die Inhalte in der Datei eingerückt werden sollen.
	 */
	private $_indent = false;

	/**
	 * @desc Name für das Wurzeltag.
	 */
	const ROOT = 'root';

	/**
	 * @desc Name für das Paar-Tag.
	 */
	const PAIR = 'pair';

	/**
	 * @desc Name für das Schlüssel-Tag.
	 */
	const KEY = 'key';

	/**
	 * @desc Name für das Werte-Tag.
	 */
	const VALUE = 'value';

	/**
	 * Gibt die zu verwendende Datei für Lese- und Schreiboperationen
	 * zurück.
	 *
	 * @return string
	 */
	public function getFile() {
		return( $this->_file );
	}

	/**
	 * Setzt die zu verwendende Datei für Lese- und Schreiboperationen.
	 *
	 * @return void
	 * @param string
	 */
	public function setFile( $value ) {
		$this->_file = Ensure::string( $value );
	}

	/**
	 * Gibt das Flag, ob die Inhalte in der Datei eingerückt werden sollen, zurück.
	 *
	 * @return boolean
	 */
	public function getIndent() {
		return( $this->_indent );
	}

	/**
	 * Setzt das Flag, ob die Inhalte in der Datei eingerückt werden sollen.
	 *
	 * @return void
	 * @param boolean
	 */
	public function setIndent( $indent ) {
		$this->_indent = Ensure::boolean( $indent );
	}

	/**
	 * Lädt die Datei mit dem Dateinamen {@link getFile File} und
	 * initialisiert das interne Array.
	 *
	 * @throws UnexpectedValueException Es kann kein Datenstrom zu der
	 *  Datei geöffnet werden.
	 * @throws IOException Es kann nicht aus der Datei gelesen werden.
	 * @throws Exception Die Daten in der Datei können nicht gelesen
	 *  werden, da diese kein gültiges XML oder fehlerhaft sind.
	 * @return boolean
	 */
	public function load() {
		$storage = array();
		$xml = new SimpleXMLElement( $this->_file, 0, true );
			
		foreach( $xml->children() as $pair ) {
			$storage[strval( reset( $pair->children() ) )] = strval( next( $pair->children() ) );
		}
			
		$this->setAdapted( $storage );
	}

	/**
	 * Speichert die Daten des internen Arrays in die Datei mit dem
	 * Dateinamen {@link getFile File}.
	 *
	 * @throws UnexpectedValueException Es kann kein Datenstrom zu der
	 * Datei geöffnet werden.
	 * @throws IOException Es kann nicht in die Datei geschrieben werden.
	 * @return boolean
	 */
	public function store() {
		$stream = new Stream( $this->_file, 'w' );
			
		if( $this->getIndent() ) {
			$stream->writeLine( XML::getHead() );
			$stream->writeLine( '<'.self::ROOT.'>' );
			foreach( $this->getAdapted() as $key=>$value ) {
				$stream->writeLine( "\t<".self::PAIR.'>' );
				$stream->writeLine( "\t\t<".self::KEY.'>'.$this->escape( $key ).'</'.self::KEY.'>' );
				$stream->writeLine( "\t\t<".self::VALUE.'>'.$this->escape( $value ).'</'.self::VALUE.'>' );
				$stream->writeLine( "\t</".self::PAIR.'>' );
			}
		}
		else {
			$stream->write( XML::getHead() );
			$stream->write( '<'.self::ROOT.'>' );
			foreach( $this->getAdapted() as $key=>$value ) {
				$stream->write(
						'<'.self::PAIR.'><'.self::KEY.'>'
						.$this->escape( $key ).'</'.self::KEY.'><'.self::VALUE.'>'
						.$this->escape( $value ).'</'.self::VALUE.'></'.self::PAIR.'>'
						);
			}
		}
		$stream->write( '</'.self::ROOT.'>' );
			
		return( true );
	}

	/**
	 * Escapt den Inhalt vor dem Speichern in einem XML-Dokument und gibt
	 * das Ergebnis zurück.
	 *
	 * @return string
	 * @param string
	 */
	protected function escape( $value ) {
		return( XML::escape( $value ) );
	}

}

?>