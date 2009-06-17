<?php

require_once "Curly/Types/Ensure.php";

/**
 * Curly_Sourcecode_Tokens
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
abstract class Curly_Sourcecode_Tokens implements Countable {

	/**
	 * @var Curly_Types_Collection_Serial
	 */
	protected $tokens;

	/**
	 * @var Curly_Types_Collection
	 */
	private $options;

	/**
	 * Gibt die Collection-Instanz mit den Tokens zurück.
	 *
	 * @return Curly_Types_Collection_Serial
	 */
	public function getTokens() {
		return $this->tokens;
	}

	/**
	 * Gibt die Anzahl der Tokens zurück.
	 *
	 * @return integer
	 */
	public function count() {
		return $this->tokens->count();
	}

	/**
	 * Gibt alle Optionen zurück.
	 *
	 * @return Curly_Types_Collection
	 */
	public function getOptions() {
		if( is_null( $this->options ) ) {
			$this->options = $this->createDefaultOptions();
		}
		return $this->options;
	}

	/**
	 * Gibt die Standard-Optionswerte zurück.
	 *
	 * @return Curly_Types_Collection
	 */
	protected function createDefaultOptions() {
		return(new Curly_Types_Collection());
	}

	/**
	 * Gibt den Wert einer Option zurück. Ist kein Wert gesetzt, wird der Wert
	 * $default zurückgegeben.
	 *
	 * @return mixed
	 * @param string
	 * @param mixed
	 */
	public function getOption($name, $default=NULL) {
		return($this->getOptions()->get($name, $default));
	}

	/**
	 * Setzt den Wert einer Option.
	 *
	 * @return void
	 * @param string
	 * @param mixed
	 */
	public function setOption($name, $value) {
		$this->getOptions()->set($name, $value);
	}

	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->tokens=new Curly_Types_Collection_Serial();
	}

	/**
	 * Parst die Datei mit dem übergebenen Dateinamen
	 *
	 * @return void
	 * @param string
	 */
	public function parseFile( $file ) {
		$this->parse( file_get_contents( $file ) );
	}

	/**
	 * Parst die übergebene Stream-Instanz.
	 *
	 * @return void
	 * @param Curly_Stream
	 */
	public function parseStream( Curly_Stream $stream ) {
		$this->parse( $stream->readToEnd() );
	}

	/**
	 * Parst den übergebenen Code.
	 *
	 * @return void
	 * @param string
	 */
	abstract public function parse( $code );

	/**
	 * Rendert alle Tokens und gibt diese als Zeichenkette zurück.
	 *
	 * @return string
	 */
	abstract public function render();

	/**
	 * Rendert alle Tokens in die Datei mit dem übergebenen Pfad.
	 *
	 * @return boolean
	 * @param string Dateiname
	 */
	public function renderFile( $path ) {
		return( file_put_contents( $path, $this->render() )!==false );
	}

	/**
	 * Rendert alle Tokens in den übergebenen Stream
	 *
	 * @return void
	 * @param Curly_Stream
	 */
	public function renderStream( Curly_Stream $stream ) {
		$stream->write( $this->render() );
	}

	/**
	 * Überprüft, ob der übergebene Parameter einen gültigen Token darstellt.
	 *
	 * @return boolean
	 * @param mixed
	 */
	abstract public function validToken($token);

	/**
	 * Gibt den Tokentyp an der übergebenen Stelle zurück.
	 *
	 * @return integer|string oder NULL, wenn der Index ungültig ist.
	 * @param integer Index
	 */
	abstract public function getTokenTypeAt($index);

	/**
	 * Fügt einen Token am Ende ein.
	 *
	 * @return void
	 * @param array Der Token
	 */
	public function addToken($token) {
		if(!$this->validToken($token)) {
			require_once "Curly/Sourcecode/Exception.php";
			throw new Curly_Sourcecode_Exception('Invalid token given');
		}
		$this->tokens->append($token);
	}

	/**
	 * Fügt einen Token an der übergebenen Position ein.
	 *
	 * @return void
	 * @param integer Index, an dem der Token eingefügt werden soll.
	 * @param array Der Token
	 */
	public function insertTokenAt($index, $token) {
		if(!$this->validToken($token)) {
			require_once "Curly/Sourcecode/Exception.php";
			throw new Curly_Sourcecode_Exception('Invalid token given');
		}
		$this->tokens->insertAt($index, $token);
	}

	/**
	 * Fügt eine Reihe von Tokens an der übergebenen Position ein.
	 *
	 * @throws Curly_Sourcecode_Php_Exception
	 * @return integer Die Anzahl der eingefügten Elemente
	 * @param integer Index, an dem die Tokens eingefügt werden soll.
	 * @param array|Curly_Sourcecode_Tokens|Traversable Die Token
	 */
	public function insertTokensAt($index, $tokens) {
		if($tokens instanceof Curly_Sourcecode_Tokens) {
			$tokens=$tokens->tokens;
		}

		if(!($tokens instanceof Traversable or is_array($tokens))) {
			require_once "Curly/Sourcecode/Exception.php";
			throw new Curly_Sourcecode_Exception('Invalid second argument given');
		}

		foreach($tokens as $token) {
			if(!$this->validToken($token)) {
				require_once "Curly/Sourcecode/Exception.php";
				throw new Curly_Sourcecode_Exception('Invalid token given');
			}
		}

		$count=$this->tokens->count();
		$this->tokens->mergeWithAt($index, $tokens);
		return $this->tokens->count()-$count;
	}

	/**
	 * Entfernt den Token an der übergebenen Indexposition.
	 *
	 * @return void
	 * @param integer
	 */
	public function removeTokenAt($index) {
		$this->tokens->remove($index);
	}

	/**
	 * Gibt den Token an der übergebenen Stelle zurück.
	 *
	 * @return mixed oder NULL, wenn der Index ungültig ist.
	 * @param integer Index
	 */
	public function getTokenAt($index) {
		return $this->tokens->get($index);
	}

}