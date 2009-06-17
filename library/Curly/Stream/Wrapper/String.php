<?php

require_once "Curly/Types/String.php";
require_once "Curly/Stream/Wrapper.php";

/**
 * Curly_Stream_Wrapper_String
 *
 * Stellt eine Curly_Types_String-Instanz als Stream bereit.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Wrapper
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
class Curly_Stream_Wrapper_String extends Curly_Stream_Wrapper {

	/**
	 * @var integer Aktuelle Position des Streamzeigers
	 */
	private $_pos;

	/**
	 * @var Curly_Types_String Der zu verwendende String
	 */
	private $_inst;

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
		$this->_pos=0;
		
		if($this->context) {
			$opts=stream_context_get_options($this->context);
			if(isset($opts['string']['string'])) {
				$this->_inst=$opts['string']['string'];
				return($this->_inst instanceof Curly_Types_String);
			}
		}
		
		$pos=strpos($path, '://');
		if($pos===false) {
			return(false);
		}
		
		$this->_inst=new Curly_Types_String(substr($path, $pos+3));
		return(true);
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
		$retval=$this->_inst->getSubstring($this->_pos, $count);
		if($retval===NULL) {
			return('');
		}
		$retval=$retval->getValue();
		$this->_pos+=strlen($retval);
		return($retval);
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
		$len=strlen($data);
		$this->_inst[$this->_pos]=$data;
		return( $len );
	}

	/**
	 * Gibt die aktuelle Lese-/Schreibposition zurück.
	 * 
	 * @return integer
	 */
	public function stream_tell() {
		return($this->_pos);
	}

	/**
	 * Gibt einen boolschen Wert zurück, ob die Lese-/Schreibposition am Ende
	 * des Datenstroms steht.
	 * 
	 * @return boolean
	 */
	public function stream_eof() {
		return($this->_pos>=$this->_inst->length());
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
		$len=$this->_inst->length();
		switch($whence) {
			case SEEK_SET:
				$newPos=$offset;
				break;
			case SEEK_CUR:
				$newPos=$this->_pos+$offset;
				break;
			case SEEK_END:
				$newPos=$len+$offset;
				break;
			default:
				return(false);
		}
			
		if($newPos>=0 and $newPos<=$len) {
			$this->_pos=$newPos;
			return(true);
		}
		else {
			return(false);
		}
	}

}