<?php

require_once "Curly/Types/Collection.php";

/**
 * Curly_Expression_Evaluator
 *
 * Diese Klasse erlaubt die Auswertung einfacher Ausdrücke. Es sind die
 * Operatoren >=, <=, =, > und < erlaubt. Dabei wird der entsprechende
 * PHP-Operator (außer für = der ==-Operator) verwendet, d.h. es wird von der
 * automatischen Typkonversion von PHP gebrauch gemacht. Weiter werden die
 * Ausdrücke true, false und NULL durch ihre entsprechenden Werte ersetzt, auch
 * wenn eine Variable mit diesem Namen in der Value-Collection vorhanden ist.
 * 
 * Beispielausdrücke:
 * <code>
 * MyVar = 1
 * Foo = Bar
 * __OS__ = "Win"
 * PHP_VERSION >= 5.2
 * </code>
 * 
 * Eine nicht gesetzte Variable evaluiert dabei zu einer leeren Zeichenkette
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Expression
 * @since 1.0 - 22.02.2009
 * @version 1.0
 */
class Curly_Expression_Evaluator {
	
	/**
	 * @var Curly_Types_Collection Liste gesetzter Variablen in Ausdrücken.
	 */
	private $_values=NULL;
	
	/**
	 * @var array Operatoren
	 */
	private $_operators=array('>=', '<=', '=', '>', '<');
	
	/**
	 * @var array Liste der reservierten Worte und deren Werteersetzungen.
	 */
	private $reserved=array(
		'true'	=> true,
		'false'	=> false,
		'null'	=> NULL
	);
	
	/**
	 * @var string Der linke Ausdruck
	 */
	private $_leftExpression;
	
	/**
	 * @var string Der rechte Ausdruck
	 */
	private $_rightExpression;
	
	/**
	 * @var string Der zu verwendende Operator
	 */
	private $_operator;
	
	/**
	 * Konstruktor
	 * 
	 * @param Curly_Types_Collection Liste gesetzter Variablen in Ausdrücken.
	 */
	public function __construct(Curly_Types_Collection $values=NULL) {
		if($values!==NULL) {
			$this->setValues($values);
		}
	}
	
	/**
	 * Gibt die Liste gesetzter Variablen in Ausdrücken zurück.
	 * 
	 * @return Curly_Types_Collection
	 */
	public function getValues() {
		if($this->_values==NULL) {
			$this->_values=new Curly_Types_Collection();
		}
		return $this->_values;
	}
	
	/**
	 * Setzt die Liste gesetzter Variablen in Ausdrücken.
	 * 
	 * @return Curly_Expression_Evaluator
	 * @param Curly_Types_Collection
	 */
	public function setValues(Curly_Types_Collection $collection) {
		$this->_values=$collection;
		return $this;
	}
	
	/**
	 * Wertet den übergebenen Ausdruck aus.
	 * 
	 * @throws Curly_Expression_Exception wenn ein Fehler auftritt
	 * @return boolean
	 * @param string
	 */
	public function evaluate($expression) {
		// Ausdruck parsen
		if(!$this->parseExpression($expression)) {
			require_once "Curly/Expression/Exception.php";
			throw new Curly_Expression_Exception('Unable to parse the expression '.$expression);
		}
		
		// Wert des linken Ausdrucks vorbereiten
		if(!$this->prepareExpression($this->_leftExpression)) {
			require_once "Curly/Expression/Exception.php";
			throw new Curly_Expression_Exception('The left expression is invalid');
		}
		
		// Wert des rechten Ausdrucks vorbereiten
		if(!$this->prepareExpression($this->_rightExpression)) {
			require_once "Curly/Expression/Exception.php";
			throw new Curly_Expression_Exception('The right expression is invalid');
		}
		
		// Ausdruck evaluieren
		return $this->evaluateExpression();
	}
	
	/**
	 * Parst den übergebenen Ausdruck in die Eigenschaften leftExpression,
	 * rightExpression und operator.
	 * 
	 * @return boolean Flag, ob der Ausdruck geparst werden konnte
	 * @param string Der zu parsende Ausdruck
	 */
	protected function parseExpression($expression) {
		foreach($this->_operators as $operator) {
			$pos=strpos($expression, $operator);
			if($pos!==false) {
				$this->_leftExpression=trim(substr($expression, 0, $pos));
				$this->_rightExpression=trim(substr($expression, $pos+1));
				$this->_operator=$operator;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Bereitet den übergebenen Ausdruck vor. Hierzu werden Zeichenketten von
	 * den Anführungszeichen " oder ' befreit und Variablen durch ihre
	 * entsprechenden Werte ersetzt.
	 * 
	 * @return boolean false, wenn der Ausdruck ungültig ist, zum Beispiel eine
	 * leere Zeichenkette enthält.
	 * @param string&
	 */
	public function prepareExpression(&$expression) {
		$strlen=strlen($expression);
		
		// Ist leere Zeichenkette
		if($strlen<=0) {
			return false;
		}
		
		// Ist einzelnes Zeichen
		else if($strlen==1) {
			return true;
		}
		
		// Ist Zeichenkette
		if($this->prepareExpressionAsString($expression)) {
			return true;
		}
		
		// Ist reserviertes Wort
		if($this->prepareExpressionAsReservedWord($expression)) {
			return true;
		}
		
		// Ist Variable
		if($this->prepareExpressionAsVar($expression)) {
			return true;
		}
		
		// Ist einfacher Wert
		return true;
	}
	
	/**
	 * Versucht, den übergebenen Ausdruck als Zeichenkette aufzubereiten.
	 * Dies ist erfolgreich, wenn der Ausdruck von Anführungszeichen umschlossen
	 * ist.
	 * 
	 * @return boolean true, wenn der Ausdruck als Zeichenkette aufbereitet
	 * wurde.
	 * @param string&
	 */
	protected function prepareExpressionAsString(&$expression) {
		$firstChar=substr($expression, 0, 1);
		$lastChar=substr($expression, -1);
		if(
			($firstChar=='"' or $firstChar=="'")
			and $firstChar==$lastChar
		) {
			$expression=substr($expression, 1, -1);
			// maskierte Anführungszeichen ersetzen
			$expression=str_replace('\\'.$firstChar, $firstChar, $expression);
			// maskierten Backslash ersetzen
			$expression=str_replace('\\\\', '\\', $expression);
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Versucht, den übergebenen Ausdruck als reserviertes Wort aufzubereiten.
	 * Diesist erfolgreich, wenn der Ausdruck der Zeichenkette true, false oder
	 * NULL entspricht.
	 * 
	 * @return boolean true, wenn der Ausdruck als reserviertes Wort
	 * aufbereitet wurde.
	 * @param string&
	 */
	protected function prepareExpressionAsReservedWord(&$expression) {
		foreach($this->reserved as $reserved=>$value) {
			if(strcasecmp($expression, $reserved)==0) {
				$expression=$value;
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Versucht, den übergebenen Ausdruck als Variable aufzubereiten. Dies
	 * ist erfolgreich, wenn der Ausdruck als Schlüssel in der Values-Collection
	 * gesetzt ist.
	 * 
	 * @return boolean true, wenn der Ausdruck als Variable aufbereitet wurde.
	 * @param string&
	 */
	protected function prepareExpressionAsVar(&$expression) {
		if($this->getValues()->keyExists($expression)) {
			$expression=$this->getValues()->get($expression);
			return true;
		}
		return false;
	}
	
	/**
	 * Ermittelt das Ergebnis der Eigenschaften leftExpression und
	 * rightExpression nach einer Auswertung mit dem Operator operator.
	 * 
	 * @return boolean
	 */
	protected function evaluateExpression() {
		switch($this->_operator) {
			case '=':
				return $this->_leftExpression == $this->_rightExpression;
			case '>':
				return $this->_leftExpression >  $this->_rightExpression;
			case '<':
				return $this->_leftExpression <  $this->_rightExpression;
			case '>=':
				return $this->_leftExpression >= $this->_rightExpression;
			case '<=':
				return $this->_leftExpression <= $this->_rightExpression;
			default:
				return false;
		}
	}
	
}
