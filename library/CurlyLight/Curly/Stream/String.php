<?php

require_once "Curly/Stream.php";
require_once "Curly/Types/String.php";

/**
 * Curly_Stream_String
 * 
 * Stream für eine String-Instanz.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 1.0 - 10.09.2008
 * @version 1.0
 */
class Curly_Stream_String extends Curly_Stream {
	
	/**
	 * @var integer Aktuelle Position im Handle.
	 */
	private $pos=0;
	
	/**
	 * Konstruktor
	 *
	 * Überschreibt den Curly_Stream-Konstruktor, so dass nur eine einzelne
	 * Zeichenkette übergeben werden muss.
	 *
	 * @throws UnexpectedValueException Die fopen-Funktion kann keinen
	 *  Datenstrom auf die übergebene URI erzeugen.
	 * @throws BadMethodCallException Es wurde eine falsche Kombination von
	 * Parametern übergeben.
	 * @param mixed Datenstrom, der in dieser Instanz verwendet werden
	 * soll, in der Regel eine Zeichenkette oder eine
	 * Curly_Types_String-Instanz.
	 */
	public function __construct($res='') {
		if(!($res instanceof Curly_Types_String)) {
			$res=new Curly_Types_String($res);
		}
		
		parent::__construct($res);
	}
	
	/**
	 * Öffnet einen Dateihandle auf die übergebene Ressource.
	 * 
	 * @return ressource
	 * @param string URI auf die zu verwendende Datenquelle.
	 * @param string Zugriffsmodus auf die Datenquelle.
	 */
	protected function open( $url, $mode ) {
		return(new Curly_Types_String($url));
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
		$this->setHandle(NULL);
	}

	/**
	 * Überprüft, ob der übergebene Parameter ein gültiger Datenstrom ist.
	 *
	 * @return boolean
	 * @param mixed $handle
	 */
	public function isValidHandle( $handle ) {
		return($handle instanceof Curly_Types_String);
	}

	/**
	 * Führt intern die eigentliche Leseoperation durch.
	 * 
	 * @return string Die gelesenen Daten.
	 * @param int Die Anzahl zu lesender Bytes.
	 */
	protected function _read( $length ) {
		$retval=$this->getHandle()->getSubstring($this->pos, $length);
		if($retval===NULL) {
			return '';
		}
		
		$this->pos+=$retval->length();
		return($retval->getValue());
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
		$handle=$this->getHandle();
		
		if($autodetectNewline) {
			$pos=array(
				$handle->positionOf("\r\n", $this->pos),
				$handle->positionOf("\n", $this->pos),
				$handle->positionOf("\r", $this->pos),
			);
			foreach($pos as $k=>$v) {
				if($v<0) {
					unset($pos[$k]);
				}
			}
			
			$skip=1;
			
			if(count($pos)<=0) {
				$pos=-1;
			}
			else {
				$min=min($pos);
				if(isset($pos[0]) and $min==$pos[0]) {
					$skip=2;
				}
				$pos=$min;
			}
		}
		else {
			$skip=strlen($this->getNewline());
			$pos=$handle->positionOf($this->getNewline(), $this->pos);
		}
		
		// Nicht gefunden
		if($pos<=0) {
			$retval=$handle->getSubstring($this->pos)->getValue();
			$this->pos=$handle->length();
		}
		else {
			$retval=$handle->getSubstring($this->pos, $pos);
			$this->pos+=$retval->length()+$skip;
			$retval=$retval->getValue();
		}
		
		return($retval);
	}
	
	/**
	 * Führt intern die eigentliche Schreiboperation durch.
	 * 
	 * @return boolean
	 * @param string Die Eingabedaten.
	 */
	protected function _write( $value ) {
		$this->getHandle()->offsetSet($this->pos, $value);
		$this->pos+=strlen($value);
		return(true);
	}
	
	/**
	 * Gibt die aktuelle Position des Dateizeigers im Datenstrom zurück.
	 * 
	 * @return integer
	 */
	protected function _tell() {
		return($this->pos);
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
		switch($origin) {
			case self::OriginBegin:
				if($offset<0) {
					return(false);
				}
				$this->pos=$offset;
				break;
			case self::OriginCurrent:
			case self::OriginEnd:
				$this->pos+=$offset;
				if($this->pos<0) {
					$this->pos=0;
				}
				break;
		}
		return(true);
	}
	
	/**
	 * Überprüft, ob noch weitere Daten in diesem Datenstrom vorhanden
	 * sind, die durch weitere Leseoperationen ausgelesen werden können.
	 *
	 * @return boolean
	 */
	public function getHasMore() {
		return($this->getHandle()->length()>$this->pos);
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
		$this->setHandle(
			$this->open(
				$this->getHandle()->getSubstring(0, Curly_Types_Ensure::int($length)), NULL
			)
		);
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
			'eof'			=> !$this->getHasMore(),
			'unread_bytes'	=> $this->getHandle()->length()-$this->pos,
			'stream_type'	=> 'String',
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
		$this->pos=0;
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
	
	/**
	 * Gibt alle Daten dieses Streams zurück.
	 * 
	 * @return string
	 */
	public function toString() {
		return $this->getHandle()->getValue();
	}

}