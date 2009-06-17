<?php

require_once "CurlyX/Sourcecode/Php/Tokens.php";

/**
 * CurlyX_Sourcecode_Php_SyntaxHighlighter
 * 
 * Unterstützte Options-Parameter für die Formatierung:
 * <code>
 * Parametername                | Standardwert          | Bedeutung
 * -----------------------------+-----------------------+----------------------------
 * style.syntax                 | -                     | Allgemeines Syntaxelement
 * style.syntax.reserved        | sr                    | Reservierte Worte (class, extends, ...)
 * style.syntax.language        | sl                    | Sprachelement (for, if, while, ...)
 * style.syntax.brackets        | sb                    | Klammerelement ((, ), {, }, ...)
 * style.syntax.brackets.round  | -                     | Rundes Klammernelement
 * style.syntax.brackets.squared| -                     | Eckiges Klammernelement
 * style.syntax.brackets.curly  | -                     | Geschweiftes Klammernelement
 * style.syntax.operator        | so                    | Operator (+, &&, or, ...)
 * style.syntax.operator.cast   | -                     | Cast-Operator ((boolean), ...)
 * style.constants              | cn                    | Konstante (__FILE__, ...)
 * style.comment                | cm                    | Kommentar
 * style.comment.default        | -                     | Einfacher Kommentar (//, #, /*)
 * style.comment.doc            | -                     | Doc-Kommentar (/**)
 * style.type                   | -                     | Typisierter Token
 * style.type.string            | st                    | Zeichenkette ("foo", 'bar', ...)
 * style.type.integer           | in                    | Ganzzahl (123, 0xAC, ...)
 * style.type.float             | fl                    | Gleitkommazahl (0.12, ...)
 * style.var                    | va                    | Variable ($foo, ...)
 * style.special                | sp                    | Spezielle Elemente (<?php, ?>, ...)
 * style.others                 | ot                    | T_BAD_CHARACTER
 * </code>
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Sourcecode.Php
 * @since 1.0 - 17.11.2008
 * @version 1.0
 * @todo HTML-BR in der formatToken-Methode konfigurierbar machen
 * @todo Stile konfigurierbar halten, d.h. optional abschaltbar
 * @todo setStream erweitern um einfache Pfad-Setter
 */
class CurlyX_Sourcecode_Php_SyntaxHighlighter extends Curly_Sourcecode_Php_Tokens {
	
	const STYLEOPT_PREFIX='style.';
	const STYLEOPT_OTHERS='style.others';
	
	const OPT_INDENTION_WIDTH='indention.width';
	const OPT_INDENTION_LINEBREAK='indention.linebreak';
	const OPT_INDENTION_CHAR='indention.char';
	const OPT_FORMAT_TAG='formattag';
	const OPT_IGNORE_OPENCLOSETAGS='ignore.openclosetag';
	
	/**
	 * @var Stream Zu verwendender Ausgabestrom
	 */
	private $stream;
	
	/**
	 * @var string Zu verwendende Zeichenkette für die Einrückung
	 */
	private $indention='';
	
	/**
	 * @return array Tokens und deren zugehörige Stil-Option.
	 */
	private function getTokenStyles() {
		return array(
			'syntax' => array(
				'reserved' => array(
					T_ARRAY,
					T_ECHO,
					T_EMPTY,
					T_EVAL,
					T_EXIT,
					T_HALT_COMPILER,
					T_INCLUDE,
					T_INCLUDE_ONCE,
					T_ISSET,
					T_LIST,
					T_PRINT,
					T_REQUIRE,
					T_REQUIRE_ONCE,
					T_UNSET
				),
				'language' => array(
					T_ABSTRACT,
					T_AS,
					T_BREAK,
					T_CASE,
					T_CATCH,
					T_CLASS,
					T_CONST,
					T_CONTINUE,
					T_DEFAULT,
					T_DO,
					T_ELSE,
					T_ELSEIF,
					T_ENDFOR,
					T_ENDFOREACH,
					T_ENDIF,
					T_ENDSWITCH,
					T_ENDWHILE,
					T_END_HEREDOC,
					T_EXTENDS,
					T_FINAL,
					T_FOR,
					T_FOREACH,
					T_FUNCTION,
					T_GLOBAL,
					T_GOTO,
					T_IF,
					T_IMPLEMENTS,
					T_INTERFACE,
					T_NEW,
					T_OLD_FUNCTION,
					T_PRIVATE,
					T_PUBLIC,
					T_PROTECTED,
					T_RETURN,
					T_PLUS_EQUAL,
					T_START_HEREDOC,
					T_STATIC,
					T_SWITCH,
					T_THROW,
					T_TRY,
					T_USE,
					T_VAR,
					T_WHILE
				),
				'operator' => array(
					T_AND_EQUAL,
					T_BOOLEAN_AND,
					T_BOOLEAN_OR,
					T_CLONE,
					T_CONCAT_EQUAL,
					T_DEC,
					T_DIV_EQUAL,
					T_DOUBLE_ARROW,
					T_DOUBLE_COLON,
					T_PAAMAYIM_NEKUDOTAYIM,
					T_INC,
					T_LOGICAL_AND,
					T_LOGICAL_OR,
					T_LOGICAL_XOR,
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
					T_INSTANCEOF,
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
					'cast' => array(
						T_ARRAY_CAST,
						T_BOOL_CAST,
						T_DOUBLE_CAST,
						T_INT_CAST,
						T_OBJECT_CAST,
						T_STRING_CAST,
						T_UNSET_CAST
					)
				),
				'brackets' => array(
					'round' => array(
						T_PARENTHESIS_OPEN,
						T_PARENTHESIS_CLOSE
					),
					'squared' => array(
						T_SQUARED_BRACKET_OPEN,
						T_SQUARED_BRACKET_CLOSE
					),
					'curly' => array(
						T_CURLY_OPEN,
						T_CURLY_BRACKET_OPEN,
						T_CURLY_BRACKET_CLOSE
					)
				)
			),
			'constants' => array(
				T_CLASS_C,
				T_DIR,
				T_FILE,
				T_FUNC_C,
				T_LINE,
				T_METHOD_C,
				T_NS_C
			),
			'comment' => array(
				'default' => array(
					T_COMMENT,
					T_ML_COMMENT
				),
				'doc' => array(
					T_DOC_COMMENT
				)
			),
			'type' => array(
				'string' => array(
					T_CONSTANT_ENCAPSED_STRING,
					T_ENCAPSED_AND_WHITESPACE,
					T_INLINE_HTML,
					T_NUM_STRING
				),
				'integer' => array(
					T_LNUMBER
				),
				'float' => array(
					T_DNUMBER
				)
			),
			'var' => array(
				T_DOLLAR_OPEN_CURLY_BRACES,
				T_VARIABLE
			),
			'special' => array(
				T_CLOSE_TAG,
				T_DECLARE,
				T_ENDDECLARE,
				T_OPEN_TAG,
				T_OPEN_TAG_WITH_ECHO
			),
			'others' => array(
				T_BAD_CHARACTER,
				T_CHARACTER,
				T_STRING,
				T_STRING_VARNAME
			)
		);
	}
	
	/**
	 * @var array Aus der {@link $_tokenStyles}-Eigenschaft generierte
	 * Lookup-Liste.
	 */
	private $_styleLookup=NULL;
	
	/**
	 * Parst den übergebenen Code.
	 * 
	 * @return void
	 * @param string
	 * @param boolean true, wenn reiner PHP-Code erzwungen werden soll,
	 * 	d.h. die PHP-Tags ignoriert werden sollen.
	 */
	public function parse($code, $ignoreTags=false) {
		parent::parse($code, $ignoreTags);
		if($this->addedOpenTag) {
			$this->setOption(self::OPT_IGNORE_OPENCLOSETAGS, true);
		}
	}
	
	/**
	 * @return Curly_Stream Gibt den zu verwendenden Ausgabestrom zurück
	 */
	public function getStream() {
		return($this->stream);
	}
	
	/**
	 * @return void
	 * @param Curly_Stream
	 */
	public function setStream(Curly_Stream $value) {
		$this->stream=$value;
	}
	
	/**
	 * Erstellt die Standard-Optionen.
	 * 
	 * @return Collection
	 */
	protected function createDefaultOptions() {
		$options=parent::createDefaultOptions();
		$options->set('style.syntax', 			'sy');
		$options->set('style.syntax.reserved',	'sr');
		$options->set('style.syntax.language',	'sl');
		$options->set('style.syntax.brackets',	'sb');
		$options->set('style.syntax.operator',	'so');
		$options->set('style.constants',		'cn');
		$options->set('style.comment',			'cm');
		$options->set('style.type.string',		'st');
		$options->set('style.type.integer',		'in');
		$options->set('style.type.float',		'fl');
		$options->set('style.var',				'va');
		$options->set('style.special',			'sp');
		$options->set('style.others',			'ot');
		return($options);
	}
	
	/**
	 * @return string
	 * @param string
	 */
	protected function getStyleOption($option) {
		$opt=$this->getOptions();
		
		do {
			if($opt->keyExists($option)) {
				return($opt->get($option));
			}
			
			$pos=strrpos($option, '.');
			if($pos===false) {
				$option='';
			}
			else {
				$option=substr($option, 0, $pos);
			}
		} while($option!='');
		return($opt->get(self::STYLEOPT_OTHERS, 'other'));
	}
	
	/**
	 * @return void
	 */
	protected function createStyleLookup() {
		$stack=array();
		$stack[]=array(
			$this->getTokenStyles(),
			''
		);
		
		while(count($stack)>0) {
			$top=array_pop($stack);
			
			foreach($top[0] as $deeperStyle=>$token) {
				if(is_array($token)) {
					$stack[]=array(
						$token,
						$top[1]==''?$deeperStyle:$top[1].'.'.$deeperStyle
					);
				}
				else {
					$this->_styleLookup[$token]=self::STYLEOPT_PREFIX.$top[1];
				}
			}
		}
	}
	
	/**
	 * @return void
	 */
	public function highlight() {
		if($this->_styleLookup===NULL) {
			$this->createStyleLookup();
		}
		
		$this->indention=str_repeat(
			$this->getOption(self::OPT_INDENTION_CHAR, "&nbsp;"),
			$this->getOption(self::OPT_INDENTION_WIDTH, 4)
		);
		
		$ignoreOpenTag=$this->getOption(self::OPT_IGNORE_OPENCLOSETAGS, false);
		foreach($this->tokens as $token) {
			if($ignoreOpenTag and (
				$token[0]==T_OPEN_TAG or
				$token[0]==T_OPEN_TAG_WITH_ECHO or
				$token[0]==T_CLOSE_TAG
			) ) {
				continue;
			}
			
			$this->stream->write(
				$this->formatToken($token)
			);
		}
	}
	
	/**
	 * Formatiert den übergebenen Token und gibt das Ergebnis zurück.
	 * 
	 * @return string
	 * @param array
	 */
	protected function formatToken(array $token) {
		$value=htmlspecialchars($token[1], ENT_QUOTES);
		
		/*$value=str_replace("\t", $this->indention, $value);
		$value=str_replace(
			array("\r\n", "\r", "\n"),
			$this->getOption(self::OPT_INDENTION_LINEBREAK, '<br />'),
			$value
		);*/
		$value=str_replace(
			array("\r\n", "\r", "\n"),
			"\n",
			$value
		);
		
		if($token[0]==T_WHITESPACE) {
			return($value);
		}
		
		$style=isset($this->_styleLookup[$token[0]]) ? $this->_styleLookup[$token[0]] : self::STYLEOPT_OTHERS;
		$style=$this->getStyleOption($style);
		return($this->createTag($style, $value));
	}
	
	/**
	 * Erstellt ein Style-Tag für einen Token.
	 * 
	 * @return string
	 * @param string
	 * @param string
	 */
	protected function createTag($style, $token) {
		$tag=$this->getOption(self::OPT_FORMAT_TAG, 'span');
		return('<'.$tag.' class="'.$style.'">'.$token.'</'.$tag.'>');
	}
	
}


