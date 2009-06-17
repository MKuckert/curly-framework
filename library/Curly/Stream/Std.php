<?php

require_once "Curly/Stream.php";

/**
 * Curly_Stream_Std
 * 
 * Stellt Standardein- und -ausgabeströme statisch zur Verfügung.
 * Die Methoden {@link In}, {@link Out} und {@link Err} geben jeweils eine
 * {@link Curly_Stream}-Instanz auf einen entsprechenden Datenstrom zurück.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Stream
 * @since 1.0 - 30.04.2008
 * @version 1.0
 */
class Curly_Stream_Std {

	/**
	 * @var Curly_Stream Datenstrom auf den Standardeingabestrom.
	 */
	static private $_in = null;

	/**
	 * @var Curly_Stream Datenstrom auf den Standardausgabestrom.
	 */
	static private $_out = null;

	/**
	 * @var Curly_Stream Datenstrom auf den Standardfehlerausgabestrom.
	 */
	static private $_err = null;
	
	/**
	 * @var Curly_Stream Datenstrom auf den rohen POST-Datenstrom.
	 */
	static private $_input = null;
	
	/**
	 * @var Curly_Stream Ausgabedatenstrom, der auch für echo und print
	 * verwendet wird.
	 */
	static private $_output = null;

	/**
	 * Gibt den Standardeingabestrom zurück.
	 *
	 * @return Curly_Stream
	 */
	static public function In() {
		if( is_null( self::$_in ) ) {
			if( defined( 'STDIN' ) ) {
				self::$_in = new Curly_Stream( STDIN );
			}
			else {
				self::$_in = new Curly_Stream( 'php://stdin', 'r' );
			}
		}
		return( self::$_in );
	}

	/**
	 * Gibt den Standardausgabestrom zurück.
	 *
	 * @return Curly_Stream
	 */
	static public function Out() {
		if( is_null( self::$_out ) ) {
			if( defined( 'STDOUT' ) ) {
				self::$_out = new Curly_Stream( STDOUT );
			}
			else {
				self::$_out = new Curly_Stream( 'php://stdout', 'w' );
			}
		}
		return( self::$_out );
	}

	/**
	 * Gibt den Standardfehlerausgabestrom zurück.
	 *
	 * @return Curly_Stream
	 */
	static public function Err() {
		if( is_null( self::$_err ) ) {
			if( defined( 'STDERR' ) ) {
				self::$_err = new Curly_Stream( STDERR );
			}
			else {
				self::$_err = new Curly_Stream( 'php://stderr', 'w' );
			}
		}
		return( self::$_err );
	}

	/**
	 * Gibt den Eingabedatenstrom für die rohen POST-Daten zurück.
	 *
	 * @return Curly_Stream
	 */
	static public function Input() {
		if( is_null( self::$_input ) ) {
			self::$_input = new Curly_Stream( 'php://input', 'r' );
		}
		return( self::$_input );
	}

	/**
	 * Gibt den Ausgabedatenstrom zurück, der auch für echo und print verwendet
	 * wird.
	 *
	 * @return Curly_Stream
	 */
	static public function Output() {
		if( is_null( self::$_output ) ) {
			self::$_output = new Curly_Stream( 'php://output', 'w' );
		}
		return( self::$_output );
	}

}

?>