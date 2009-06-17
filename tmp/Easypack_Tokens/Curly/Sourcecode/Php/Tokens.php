<?php

require_once "Curly/Types/Ensure.php";

/**
 * Curly_Sourcecode_Php_Tokens
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.Php
 * @since 1.0 - 01.11.2008
 * @version 1.0
 */
class Curly_Sourcecode_Php_Tokens {
	
	/**
	 * @desc Der erste Index-Wert für die erweiterten Tokens.
	 */
	const ADDITIONAL_TOKENS_FIRST_INDEX=1000;
	
	/**
	 * @var Curly_Types_Collection_Serial
	 */
	protected $tokens;
	
	/**
	 * @var boolean true, wenn der parse-Methode das ignoreTags-Flag
	 *  mitgegeben und im Code ein entsprechendes Tag hinzugefügt wurde.
	 */
	protected $addedOpenTag=false;
	
	/**
	 * @var Curly_Types_Collection
	 */
	private $options;
	
	/**
	 * @var array Weitere Token
	 */
	static private $ADDITIONAL_TOKENS = array(
		'[' => 'T_SQUARED_BRACKET_OPEN',
		']' => 'T_SQUARED_BRACKET_CLOSE',
		'(' => 'T_PARENTHESIS_OPEN',
		')' => 'T_PARENTHESIS_CLOSE',
		'{' => 'T_CURLY_BRACKET_OPEN',
		'}' => 'T_CURLY_BRACKET_CLOSE',
		';' => 'T_SEMICOLON',
		'=' => 'T_OPERATOR_EQUALS',
		'!' => 'T_OPERATOR_EXCLAMATION_MARK',
		'~' => 'T_OPERATOR_TILDE',
		'@' => 'T_OPERATOR_AT',
		'*' => 'T_OPERATOR_STAR',
		'/' => 'T_OPERATOR_SLASH',
		'%' => 'T_OPERATOR_PERCENT',
		'+' => 'T_OPERATOR_PLUS',
		'-' => 'T_OPERATOR_MINUS',
		'.' => 'T_OPERATOR_DOT',
		'<' => 'T_OPERATOR_LESS_THAN',
		'>' => 'T_OPERATOR_GREATER_THAN',
		'&' => 'T_OPERATOR_AMPERSAND',
		'^' => 'T_OPERATOR_CIRCUMFLEX',
		'|' => 'T_OPERATOR_VERTICAL_LINE',
		'?' => 'T_OPERATOR_QUESTION_MARK',
		':' => 'T_OPERATOR_COLON',
		',' => 'T_OPERATOR_COMMA',
		// Konstanten zur Kompatibilität
		'old_function'	=> 'T_OLD_FUNCTION',
		T_COMMENT		=> 'T_ML_COMMENT',
		'__DIR__'		=> 'T_DIR',
		'goto'			=> 'T_GOTO',
		'__NAMESPACE__'	=> 'T_NS_C',
		'use'			=> 'T_USE'
	);
	
	/**
	 * @var boolean Flag, ob die weiteren Tokens bereits registriert wurden.
	 */
	static private $additionalTokensRegistered = false;
	
	/**
	 * Gibt die Collection-Instanz mit den Tokens zurück.
	 * 
	 * @return Curly_Types_Collection_Serial
	 */
	public function getTokens() {
		return $this->tokens;
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
	 * Registriert weitere Token-Konstanten.
	 * 
	 * @return void
	 */
	static public function registerAdditionalTokens() {
		if(self::$additionalTokensRegistered) {
			return;
		}
		
		self::$additionalTokensRegistered = true;
		$i = self::ADDITIONAL_TOKENS_FIRST_INDEX;
		foreach( self::$ADDITIONAL_TOKENS as $value=>$token ) {
			if(!defined($token)) {
				define( $token, $i );
			}
			$i++;
		}
		define( 'T_UNKNOWN', $i );
	}
	
	/**
	 * Parst die Datei mit dem übergebenen Dateinamen
	 * 
	 * @return void
	 * @param string
	 * @param boolean true, wenn reiner PHP-Code erzwungen werden soll,
	 * 	d.h. die PHP-Tags ignoriert werden sollen.
	 */
	public function parseFile( $file, $ignoreTags=false ) {
		$this->parse( file_get_contents( $file ), $ignoreTags );
	}
	
	/**
	 * Parst die übergebene Stream-Instanz.
	 * 
	 * @return void
	 * @param Curly_Stream
	 * @param boolean true, wenn reiner PHP-Code erzwungen werden soll,
	 * 	d.h. die PHP-Tags ignoriert werden sollen.
	 */
	public function parseStream( Curly_Stream $stream, $ignoreTags=false ) {
		$this->parse( $stream->readToEnd(), $ignoreTags );
	}
	
	/**
	 * Parst den übergebenen Code.
	 * 
	 * @return void
	 * @param string
	 * @param boolean true, wenn reiner PHP-Code erzwungen werden soll,
	 * 	d.h. die PHP-Tags ignoriert werden sollen.
	 */
	public function parse( $code, $ignoreTags=false ) {
		self::registerAdditionalTokens();
		
		$this->addedOpenTag=false;
		if($ignoreTags) {
			$substr=substr($code, 0, 5);
			if($substr!='<?php' and substr($substr, 0, 2)!='<?') {
				$code='<?php '.$code;
				$this->addedOpenTag=true;
			}
		}
		
		$this->tokens = new Curly_Types_Collection_Serial( token_get_all( $code ) );
		
		$this->replaceAdditionalTokens();
	}
	
	/**
	 * Rendert alle Tokens und gibt diese als Zeichenkette zurück.
	 * 
	 * @return string
	 */
	public function render() {
		$out = '';
		foreach( $this->tokens as $token ) {
			$out .= $token[1];
		}
		return $out;
	}
	
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
		foreach( $this->tokens as $token ) {
			$stream->write( $token[1] );
		}
	}
	
	/**
	 * Gibt den Namen zu dem übergebenen Token (Array oder Integer-Wert) zurück.
	 * 
	 * @return string
	 * @param mixed
	 */
	public function getTokenName($token) {
		if(is_array($token)) {
			$token=$token[0];
		}
		
		if( $token>=self::ADDITIONAL_TOKENS_FIRST_INDEX ) {
			$tokens = array_values( self::$ADDITIONAL_TOKENS );
			return($tokens[$token-self::ADDITIONAL_TOKENS_FIRST_INDEX]);
		}
		else {
			return(token_name($token));
		}
	}
	
	/**
	 * Ersetzt alle weiteren Tokens, so dass jeder Token aus einem Array
	 * besteht.
	 * 
	 * @return void
	 */
	protected function replaceAdditionalTokens() {
		foreach( $this->tokens as $index=>$token ) {
			if( is_array( $token ) ) {
				switch( $token[0] ) {
					case T_OPEN_TAG:
						$token[1] = '<?php'.PHP_EOL;
						break;
					case T_OPEN_TAG_WITH_ECHO:
						$token[1] = '<?=';
						break;
				}
			}
			// Token erstellen
			else {
				if( isset( self::$ADDITIONAL_TOKENS[$token] ) ) {
					$token = array(
						constant( self::$ADDITIONAL_TOKENS[$token] ),
						$token
					);
				}
				else {
					$token = array( T_UNKNOWN, $token );
				}
			}
			
			$this->tokens[$index] = $token;
		}
	}
	
	/**
	 * Entfernt unnötige Whitespace-Tokens aus diesen Tokens.
	 * 
	 * @return void
	 */
	public function stripWhitespace() {
		// Um diese Tokens müssen keine Whitespace-Tokens vorhanden sein.
		static $noWhitespace=array(
			T_OPEN_TAG,
			T_OPEN_TAG_WITH_ECHO,
			T_AND_EQUAL,
			T_BOOLEAN_AND,
			T_BOOLEAN_OR,
			T_CONCAT_EQUAL,
			T_DEC,
			T_DIV_EQUAL,
			T_DOUBLE_ARROW,
			T_DOUBLE_COLON,
			T_PAAMAYIM_NEKUDOTAYIM,
			T_INC,
			T_SEMICOLON,
			T_OPERATOR_EQUALS,
			T_OPERATOR_EXCLAMATION_MARK,
			T_OPERATOR_TILDE,
			T_OPERATOR_AT,
			T_OPERATOR_STAR,
			T_OPERATOR_SLASH,
			T_OPERATOR_PERCENT,
			T_OPERATOR_PLUS,
			T_OPERATOR_MINUS,
			T_OPERATOR_DOT,
			T_OPERATOR_LESS_THAN,
			T_OPERATOR_GREATER_THAN,
			T_OPERATOR_AMPERSAND,
			T_OPERATOR_CIRCUMFLEX,
			T_OPERATOR_VERTICAL_LINE,
			T_OPERATOR_QUESTION_MARK,
			T_OPERATOR_COLON,
			T_OPERATOR_COMMA,
			T_IS_EQUAL,
			T_IS_GREATER_OR_EQUAL,
			T_IS_IDENTICAL,
			T_IS_NOT_EQUAL,
			T_IS_NOT_IDENTICAL,
			T_IS_SMALLER_OR_EQUAL,
			T_MINUS_EQUAL,
			T_MOD_EQUAL,
			T_MUL_EQUAL,
			T_OBJECT_OPERATOR,
			T_OR_EQUAL,
			T_SL,
			T_SL_EQUAL,
			T_SR,
			T_SR_EQUAL,
			T_XOR_EQUAL,
			T_ARRAY_CAST,
			T_BOOL_CAST,
			T_DOUBLE_CAST,
			T_INT_CAST,
			T_OBJECT_CAST,
			T_STRING_CAST,
			T_UNSET_CAST,
			T_PARENTHESIS_OPEN,
			T_PARENTHESIS_CLOSE,
			T_SQUARED_BRACKET_OPEN,
			T_SQUARED_BRACKET_CLOSE,
			T_CURLY_OPEN,
			T_CURLY_BRACKET_OPEN,
			T_CURLY_BRACKET_CLOSE,
			T_DOLLAR_OPEN_CURLY_BRACES,
			T_CONSTANT_ENCAPSED_STRING,
			T_ENCAPSED_AND_WHITESPACE
		);
		
		$lookup=array_flip($noWhitespace);
		
		for($i=0; $i<($c=$this->tokens->count());) {
			$token=$this->tokens->get($i);
			
			$isLast=($i+1)>=$c;
			$isNoWhitespaceToken=isset($lookup[$token[0]]);
			
			if(!$isLast and $isNoWhitespaceToken and $this->isWhitespaceTokenAt($i+1)) {
				$this->tokens->remove($i+1);
			}
			else if($i>0 and $isNoWhitespaceToken and $this->isWhitespaceTokenAt($i-1)) {
				$this->tokens->remove($i-1);
				$i-=2;
			}
			else if($this->isCommentToken($token)) {
				$this->tokens->remove($i);
				$i--;
			}
			else if($this->isTokenOf($token, T_OPEN_TAG)) {
				$token[1]=rtrim($token[1]).' ';
				$this->tokens->set($i, $token);
				$i++;
			}
			// Whitespace nach geschweifter Klammer
			else if(!$isLast and $this->isTokenOf($token, T_CURLY_BRACKET_OPEN) and $this->isWhitespaceTokenAt($i+1)) {
				$this->tokens->remove($i+1);
			}
			else {
				// Whitespace-Tokens normalisieren
				if($this->isWhitespaceToken($token)) {
					$token[1]=' ';
					$this->tokens->set($i, $token);
				}
				$i++;
			}
		}
	}
	
	/**
	 * Fügt einen Token an der übergebenen Position ein.
	 * 
	 * @return void
	 * @param integer Index, an dem die Einrückung eingefügt werden soll.
	 * @param array Der Token
	 */
	protected function insertTokenAt( $index, $token ) {
		$this->tokens->insertAt( $index, $token );
	}
	
	/**
	 * Überprüft, ob der übergebene Token vom übergebenen Typ ist.
	 * 
	 * @return boolean
	 * @param mixed
	 * @param integer
	 */
	protected function isTokenOf($token, $type) {
		return(is_array($token)?$token[0]==$type:$token==$type);
	}
	
	/**
	 * Überprüft, ob der Token am übergebenen Index vom übergebenen Typ ist.
	 * 
	 * @return boolean
	 * @param integer
	 * @param integer
	 */
	protected function isTokenOfAt($index, $type) {
		return($this->isTokenOf($this->tokens[$index], $type));
	}
	
	/**
	 * Überprüft, ob der übergebene Token von einem der übergebenen Typen ist.
	 * 
	 * @return boolean
	 * @param mixed
	 * @param array
	 */
	protected function isTokenAnyOf($token, $types) {
		if(is_array($token)) {
			$token=$token[0];
		}
		
		foreach($types as $type) {
			if($token==$type) {
				return(true);
			}
		}
		return(false);
	}
	
	/**
	 * Überprüft, ob der Wert des übergebenen Token dem übergebenen Wert
	 * entspricht.
	 * 
	 * @return boolean
	 * @param mixed
	 * @param string
	 */
	protected function isTokenValue($token, $value) {
		return(is_array($token)?$token[1]==$value:$token==$value);
	}
	
	/**
	 * Überprüft, ob der Wert des Tokens an der übergebenen Indexposition mit
	 * dem übergebenen Wert übereinstimmt.
	 * 
	 * @return boolean
	 * @param integer
	 * @param integer
	 */
	protected function isTokenValueAt($index, $value) {
		return($this->isTokenValue($this->tokens[$index], $value));
	}
	
	/**
	 * Überprüft, ob der übergebene Token ein Whitespace-Token ist.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	protected function isWhitespaceToken($token) {
		return($this->isTokenOf($token, T_WHITESPACE));
	}
	
	/**
	 * Überprüft, ob der Token an der übergebenen Index-Position ein
	 * Whitespace-Token ist.
	 * 
	 * @return boolean
	 * @param integer
	 */
	protected function isWhitespaceTokenAt($index) {
		return($this->isTokenOfAt($index, T_WHITESPACE));
	}
	
	/**
	 * Überprüft, ob der übergebene Token ein Kommentar-Token ist.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	protected function isCommentToken($token) {
		return(
			$this->isTokenOf($token, T_COMMENT)
			or $this->isTokenOf($token, T_DOC_COMMENT)
		);
	}
	
	/**
	 * Überprüft, ob der Token an der übergebenen Index-Position ein
	 * Kommentar-Token ist.
	 * 
	 * @return boolean
	 * @param integer
	 */
	protected function isCommentTokenAt($index) {
		return(
			$this->isTokenAt($index, T_COMMENT)
			or $this->isTokenAt($index, T_DOC_COMMENT)
		);
	}
	
	/**
	 * Überprüft, ob der übergebene Token ein String-Token ist.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	protected function isStringToken($token) {
		return($this->isTokenOf($token, T_STRING));
	}
	
	/**
	 * Überprüft, ob der Token an der übergebenen Index-Position ein
	 * String-Token ist.
	 * 
	 * @return boolean
	 * @param integer
	 */
	protected function isStringTokenAt($index) {
		return($this->isTokenOfAt($index, T_STRING));
	}
	
	/**
	 * Überprüft, ob der übergebene Token ein Unknown-Token ist.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	protected function isUnknownToken($token) {
		return($this->isTokenOf($token, T_UNKNOWN));
	}
	
	/**
	 * Überprüft, ob der Token an der übergebenen Index-Position ein
	 * Unknown-Token ist.
	 * 
	 * @return boolean
	 * @param integer
	 */
	protected function isUnknownTokenAt($index) {
		return($this->isTokenOfAt($index, T_UNKNOWN));
	}
	
	/**
	 * Gibt alle Tokens zurück, die vom übergebenen Typ sind.
	 * 
	 * @return array
	 * @param int
	 * @param boolean true, wenn nicht nur der Wert sondern der gesamte Token
	 *  zurückgegeben werden soll.
	 */
	public function getTokensOfType($type, $fullToken=false) {
		$type=Curly_Types_Ensure::int($type);
		$found=array();
		foreach($this->tokens as $token) {
			if($token[0]==$type) {
				if($fullToken) {
					$found[]=$token;
				}
				else {
					$found[]=$token[1];
				}
			}
		}
		return($found);
	}
	
	/**
	 * Gibt alle Tokens zurück, die von einem der übergebenen Typen sind.
	 * 
	 * @return array
	 * @param array
	 * @param boolean true, wenn nicht nur der Wert sondern der gesamte Token
	 *  zurückgegeben werden soll.
	 */
	public function getTokensOfAnyType(array $types, $fullToken=false) {
		$types=array_map(array('Curly_Types_Ensure', 'int'), Curly_Types_Ensure::arr($types));
		$found=array();
		foreach($this->tokens as $token) {
			if(in_array($token[0], $types, true)) {
				if($fullToken) {
					$found[]=$token;
				}
				else {
					$found[]=$token[1];
				}
			}
		}
		return($found);
	}
	
}