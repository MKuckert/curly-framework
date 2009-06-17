<?php

require_once "Curly/Sourcecode/Php/Tokens.php";

/**
 * CurlyX_Sourcecode_Php_Formatter
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Sourcecode.Php
 * @since 1.0 - 01.11.2008
 * @version 1.0
 */
class CurlyX_Sourcecode_Php_Formatter {

	/**
	 * @var Curly_Sourcecode_Php_Tokens Quell-Tokens
	 */
	private $tokens=NULL;

	/**
	 * @var Curly_Sourcecode_Php_Tokens Formatierte Tokens
	 */
	private $formattedTokens=NULL;

	/**
	 * @var integer Aktuelle Einrückung
	 */
	private $indentation = 0;

	/**
	 * @var string Das für Einrückungen zu verwendende Zeichen.
	 */
	private $indentationChar="\t";

	/**
	 * @var integer Anzahl, wie oft das für Einrückungen zu verwendende Zeichen
	 *  für eine Einrückung verwendet werden soll.
	 */
	private $indentationCharCount=1;

	/**
	 * @var integer
	 */
	private $curIndex = 0;

	/**
	 * @var array Zu ignorierende Tokens
	 */
	private $_ignoreTokens;

	/**
	 * @var array String-Tokens, die nicht direkt aufeinander folgen dürfen.
	 */
	private $_stringTokens;

	/**
	 * @var array Tokens, die neue Zeile erzeugen und Einrückung verringern.
	 */
	private $_endTokens;
	
	/**
	 * Gibt das für Einrückungen zu verwendende Zeichen zurück.
	 * 
	 * @return string
	 */
	public function getIndentationChar() {
		return $this->indentationChar;
	}
	
	/**
	 * Setzt das für Einrückungen zu verwendende Zeichen.
	 * 
	 * @return CurlyX_Sourcecode_Php_Formatter
	 * @param string
	 */
	public function setIndentationChar($value) {
		$this->indentationChar=Curly_Types_Ensure::string($value);
		return $this;
	}
	
	/**
	 * Gibt die Anzahl, wie oft das für Einrückungen zu verwendende Zeichen für
	 * eine Einrückung verwendet werden soll zurück.
	 * 
	 * @return integer
	 */
	public function getIndentationCharCount() {
		return $this->indentationCharCount;
	}
	
	/**
	 * Setzt die Anzahl, wie oft das für Einrückungen zu verwendende Zeichen
	 * für eine Einrückung verwendet werden soll.
	 * 
	 * @return CurlyX_Sourcecode_Php_Formatter
	 * @param integer
	 */
	public function setIndentationCharCount($value) {
		$this->indentationCharCount=Curly_Types_Ensure::integer($value);
		return $this;
	}
	
	/**
	 * Parst den übergebenen Code.
	 *
	 * @return Curly_Sourcecode_Php_Tokens
	 * @param string
	 */
	public function parse( $code ) {
		$this->tokens=new Curly_Sourcecode_Php_Tokens();
		$this->tokens->parse($code);

		$this->formattedTokens=new Curly_Sourcecode_Php_Tokens();

		$this->initTokens();
		$this->format();

		$this->tokens=NULL;

		return $this->formattedTokens;
	}

	/**
	 * @return void
	 */
	protected function initTokens() {
		$this->_ignoreTokens=array(
			T_WHITESPACE
		);
		$this->_stringTokens=array(
			T_AS,
			T_ABSTRACT,
			T_CLASS,
			T_CLONE,
			T_CONST,
			T_ECHO,
			T_EXTENDS,
			T_FINAL,
			T_IMPLEMENTS,
			T_INCLUDE,
			T_INCLUDE_ONCE,
			T_INTERFACE,
			T_LOGICAL_AND,
			T_LOGICAL_OR,
			T_LOGICAL_XOR,
			T_NEW,
			T_RETURN,
			T_SWITCH,
			T_THROW,
			T_TRY,
			T_STATIC,
			T_STRING,
			T_USE,
			T_VAR,
			T_WHILE,
			T_PRINT,
			T_PRIVATE,
			T_PUBLIC,
			T_PROTECTED,
			T_REQUIRE,
			T_REQUIRE_ONCE,
			T_VARIABLE,
			T_FOR,
			T_FOREACH,
			T_GLOBAL,
			T_GOTO,
			T_IF,
			T_ELSE,
			T_ELSEIF,
			T_FUNCTION,
			T_CLOSE_TAG
		);
		$this->_endTokens=array(
			T_ENDFOR,
			T_ENDFOREACH,
			T_ENDIF,
			T_ENDSWITCH,
			T_ENDWHILE,
			T_CURLY_BRACKET_CLOSE
		);
	}

	protected function format() {
		$thisTokens=$this->tokens;
		$thisTokensCount=$thisTokens->count();

		for($this->curIndex=0; $this->curIndex<$thisTokensCount; $this->curIndex++) {
			$token=$thisTokens->getTokenAt($this->curIndex);
			$this->formatToken($token);
		}
	}

	protected function formatToken($token) {
		// Token ignorieren
		if(in_array($token[0], $this->_ignoreTokens)) {
			return;
		}
		// Ist String-Token
		else if(in_array($token[0], $this->_stringTokens) or $token[0]==T_DOLLAR) {
			// Voriger Token String-Token?
			$last=$this->getPreviousToken();
			if($last!==NULL and in_array($last[0], $this->_stringTokens)) {
				$this->addToken(T_WHITESPACE, ' ');
			}
			$this->addCurrentToken();
		}
		// Ist End-Token
		else if(in_array($token[0], $this->_endTokens)) {
			// Falschen weißen Token entfernen
			$last=$this->getPreviousToken();
			if($last[0]==T_WHITESPACE) {
				$this->removePreviousToken();
			}

			$this->outdent();
			$this->addNLIndentationToken();
			$this->addCurrentToken();
			$this->addNLIndentationToken();
		}
		// Kommentar
		else if($token[0]==T_COMMENT or $token[0]==T_DOC_COMMENT) {
			$this->addNLIndentationToken();
			$this->addCurrentToken();
			$this->addNLIndentationToken();
		}
		// Ist öffnende geschweifte Klammer
		else if($token[0]==T_CURLY_BRACKET_OPEN or $token[0]==T_CURLY_OPEN) {
			$this->addToken(T_WHITESPACE, ' ');
			$this->addCurrentToken();
			$this->indent();
			$this->addNLIndentationToken();
		}
		// Ist Komma
		else if($token[0]==T_OPERATOR_COMMA) {
			$this->addCurrentToken();
			$this->addToken(T_WHITESPACE, ' ');
		}
		// Ternärer Operator
		else if($token[0]==T_OPERATOR_QUESTION_MARK or $token[0]==T_OPERATOR_COLON) {
			$this->addToken(T_WHITESPACE, ' ');
			$this->addCurrentToken();
			$this->addToken(T_WHITESPACE, ' ');
		}
		// Ist Semikolon
		else if($token[0]==T_SEMICOLON) {
			$this->addCurrentToken();
			$this->addNLIndentationToken();
		}
		// Direkt übernehmen
		else {
			$this->addCurrentToken();
		}
	}

	/**
	 * Erhöht die Einrückung.
	 *
	 * @return void
	 */
	private function indent() {
		$this->indentation++;
	}

	/**
	 * Verringert die Einrückung.
	 *
	 * @return void
	 */
	private function outdent() {
		if($this->indentation>0) {
			$this->indentation--;
		}
	}

	/**
	 * Gibt den vorher eingefügten Token zurück.
	 *
	 * @return array
	 */
	protected function getPreviousToken() {
		return $this->formattedTokens->getTokens()->last();
	}

	/**
	 * Entfernt den vorher eingefügten Token.
	 *
	 * @return void
	 */
	protected function removePreviousToken() {
		$this->formattedTokens->removeTokenAt($this->formattedTokens->count()-1);
	}

	/**
	 * Gibt die Zeichenkette, welche für die Einrückung verwendet werden soll, zurück.
	 *
	 * @return string
	 */
	protected function getIndentation() {
		return str_repeat($this->indentationChar, $this->indentation*$this->indentationCharCount);
	}

	/**
	 * @return void
	 * @param integer
	 * @param string
	 */
	protected function addToken($type, $content) {
		$this->formattedTokens->addToken(array(
			$type,
			$content
		));
	}

	/**
	 * @return void
	 */
	protected function addCurrentToken() {
		$this->formattedTokens->addToken($this->tokens->getTokenAt($this->curIndex));
	}

	/**
	 * @return void
	 */
	protected function addIndentationToken() {
		$this->addToken(T_WHITESPACE, $this->getIndentation());
	}

	/**
	 * @return void
	 */
	protected function addNewlineToken() {
		$this->addToken(T_WHITESPACE, PHP_EOL);
	}

	/**
	 * @return void
	 */
	protected function addNLIndentationToken() {
		$this->addToken(T_WHITESPACE, PHP_EOL.$this->getIndentation());
	}

}