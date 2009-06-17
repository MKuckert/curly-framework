<?php

require_once "Curly/Types/String.php";
require_once "Curly/Stream/Wrapper.php";

/**
 * Curly_Stream_Wrapper_Base64
 *
 * Die PHP-Streamwrapper konvertieren vor dem Öffnen des Streams den übergebenen
 * Pfad, d.h. bei binären Daten kann der String-StreamWrapper nicht korrekt
 * aufgerufen werden. Mit dieser Klasse kann die String-Instanz über eine
 * Base64-Enkodierte Zeichenkette initialisiert werden.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream.Wrapper
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
class Curly_Stream_Wrapper_Base64 extends Curly_Stream_Wrapper_String {

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
	 * Öffnet den Stream
	 * Wird der Stream mit einem Kontext geöffnet, so kann unter dem Parameter "string" ein String abgelegt werden.
	 *
	 * @return boolean true, wenn der Stream erfolgreich geöffnet werden konnte
	 * @param string URL, mit der dieser Stream erzeugt wurde
	 * @param string Öffnungsmodus, mit dem dieser Stream geöffnet wurde. Wird nicht beachtet, d.h. jeder Modus ist gleich
	 * @param integer Weitere Streamoptionen
	 * @param string& Wird nach dem Öffnen des Streams auf die verwendete URL gesetzt, wenn diese von $path abweicht
	 */
	public function stream_open($path, $mode, $options, $opened_path) {
		$stream='';
	
		$pos=strpos($path, '://');
		if($pos===false) {
			return(false);
		}
			
		$stream=substr($path, $pos+3);
		if($stream!='') {
			$stream=base64_decode($stream);
		}
		return(parent::stream_open(substr($path, 0, $pos).'://'.$stream, $mode, $options, $opened_path));
	}

}
