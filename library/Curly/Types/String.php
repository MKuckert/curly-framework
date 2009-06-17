<?php

require_once "Curly/Types/String/Iterator.php";

/**
 * Curly_Types_String
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @package Curly.Types
 * @since 1.0 - 15.11.2008
 * @version 1.0
 */
class Curly_Types_String implements ArrayAccess, IteratorAggregate, Countable {
	
	//TODO: Folgende Methoden implementieren
	//camelize			foo-bar -> fooBar
	//capitalize		fooBar -> Foobar (besser ucwords...)
	//dasherize			foo_bar -> foo-bar
	//truncate			fooooooo -> foo...
	//underscore		fooBar -> foo_bar
	
	
	/**#@+
	 * Mögliche Fehlerkonstanten
	 */
	const ERR_OUTOFBOUNDS='The given index is out of bounds';
	const ERR_INDEXNEGATIVE='The given index is not positive or zero';
	const ERR_ITERATORCLASS='The iterator class has to implement the Iterator interface';
	const ERR_EXPLODEFAILED='Failed to explode this value with the given delimiter';
	const ERR_SPLIT='Failed to split the string with the given pattern';
	/**#@-*/
	
	const NULLBYTE="\0";
	const SPACE=" ";
	const DEFAULT_ITERATOR='Curly_Types_String_Iterator';
	
	/**
	 * @var string
	 */
	private $_s='';
	
	/**
	 * @var string
	 */
	private $_iteratorClass;
	
	/**
	 * @var boolean
	 */
	private $_ignoreCase=false;
	
	/**
	 * @param string
	 * @param string
	 * @param boolean
	 */
	public function __construct($value='', $iteratorClass=self::DEFAULT_ITERATOR, $caseless=false) {
		$this->setValue($value);
		$this->setIteratorClass($iteratorClass);
		$this->setIgnoreCase($caseless);
	}
	
	/**
	 * @return string
	 */
	public function getValue() {
		return($this->_s);
	}
	
	/**
	 * @return String
	 * @param string
	 */
	public function setValue($value) {
		$this->_s=$this->ensure($value);
		return($this);
	}

	/**
	 * @return string
	 */
	public function __toString() {
		return($this->getValue());
	}
	
	/**
	 * @return string
	 */
	public function toString() {
		return($this->getValue());
	}
	
	/**
	 * @return string
	 * @param mixed
	 */
	public function ensure($value) {
		switch(gettype($value)) {
			case 'boolean':
				return($value?'TRUE':'FALSE');
			case 'integer':
			case 'double':
				return((string)$value);
			case 'string':
				return($value);
			case 'array':
				$ret='';
				foreach($value as $val) {
					$ret.=$this->ensure($val);
				}
				return($ret);
			case 'object':
				$m='toString';
				if(method_exists($value, $m)) {
					return((string)$value->$m());
				}
				return((string)$value);
			case 'resource':
				return(get_resource_type($value));
		}
		
	}
	
	/**
	 * @return integer
	 */
	public function length() {
		return(strlen($this->_s));
	}
	
	/**
	 * @return integer
	 */
	public function count() {
		return(strlen($this->_s));
	}
	
	/**
	 * @return String
	 */
	public function clear() {
		$this->_s='';
		return($this);
	}
	
	/**
	 * @return Iterator
	 */
	public function getIterator() {
		$class=$this->_iteratorClass;
		return(new $class($this->_s));
	}
	
	/**
	 * @return string
	 */
	public function getIteratorClass() {
		return($this->_iteratorClass);
	}
	
	/**
	 * @throws InvalidArgumentException
	 * @return String
	 * @param string
	 */
	public function setIteratorClass($value) {
		$implements=class_implements($value);
		if(!isset($implements['Iterator'])) {
			throw new InvalidArgumentException(self::ERR_ITERATORCLASS);
		}
		$this->_iteratorClass=$value;
		
		return($this);
	}
	
	/**
	 * @return boolean
	 */
	public function getIgnoreCase() {
		return($this->_ignoreCase);
	}
	
	/**
	 * @return void
	 * @param boolean
	 */
	public function setIgnoreCase($value) {
		$this->_ignoreCase=(boolean)$value;
	}
	
	/**
	 * @return String
	 * @param mixed
	 */
	public function append($value) {
		$this->_s.=$this->ensure($value);
		return($this);
	}
	
	/**
	 * @return String
	 * @param mixed
	 */
	public function prepend($value) {
		$this->_s=$this->ensure($value).$this->_s;
		return($this);
	}
	
	/**
	 * @throws OutOfBoundsException
	 * @return String
	 * @param integer
	 * @param mixed
	 */
	public function insertAt($index, $value) {
		$index=(int)$index;
		$thisLen=$this->length();
		if($index<0 or $index>$thisLen) {
			throw new OutOfBoundsException(self::ERR_OUTOFBOUNDS);
		}
		
		if($index==$thisLen) {
			return($this->append($value));
		}
		else {
			$this->_s=
				substr($this->_s, 0, $index).
				$value.
				substr($this->_s, $index);
		}
		
		return($this);
	}
	
	/**
	 * @return String
	 */
	public function duplicate() {
		return(clone $this);
	}
	
	/**
	 * @return String
	 */
	public function revert() {
		$this->_s=strrev($this->_s);
		return($this);
	}
	
	/**
	 * @return String
	 */
	public function toLowerCase() {
		$this->_s=strtolower($this->_s);
		return($this);
	}
	
	/**
	 * @return String
	 */
	public function toUpperCase() {
		$this->_s=strtoupper($this->_s);
		return($this);
	}
	
	/**
	 * Trennt diese Zeichenkette anhand des Zeichens $delimiter in Maximal
	 * $limit Teile und gibt diese als Array zurück.
	 * 
	 * @throws UnexpectedValueException
	 * @return array
	 */
	public function explode($delimiter=',', $limit=NULL) {
		if($limit===NULL) {
			$retval=explode($delimiter, $this->_s);
		}
		else {
			$retval=explode($delimiter, $this->_s, $limit);
		}
		
		if(!is_array($retval)) {
			throw new UnexpectedValueException(self::ERR_EXPLODEFAILED);
		}
		
		return($retval);
	}
	
	/**
	 * Trennt diese Zeichenkette in Einzelteile der übergebenen Länge und gibt
	 * diese als Array zurück.
	 * 
	 * @throws OutOfRangeException
	 * @return array
	 * @param int
	 */
	public function toArray($length=1) {
		if($length<0) {
			throw new OutOfRangeException(self::ERR_INDEXNEGATIVE);
		}
		return(str_split($this->_s, $length));
	}
	
	/**
	 * Trennt diese Zeichenkette anhand eines regulären Ausdrucks in
	 * Einzelteile und gibt diese als Array zurück.
	 * 
	 * @throws UnexpectedValueException
	 * @return array
	 * @param string Regulärer Ausdruck (ohne Delimiter!)
	 * @param string Modifier für den Ausdruck
	 */
	public function split($pattern, $modifier=NULL, $limit=-1, $flags=0) {
		if($modifier===NULL) {
			if($this->_ignoreCase) {
				$modifier='i';
			}
			else {
				$modifier='';
			}
		}
		
		$pattern='/'.str_replace('/', '\\/', $pattern).'/'.$modifier;
		$split=preg_split($pattern, $this->_s, $limit, $flags);
		if(!is_array($split)) {
			throw new UnexpectedValueException(self::ERR_SPLIT);
		}
		
		return($split);
	}
	
	/**
	 * Überprüft, ob diese Zeichenkette lediglich aus "weißen Zeichen" besteht
	 * oder eine leere Zeichenkette ist.
	 * 
	 * @return boolean
	 */
	public function isBlank() {
		return(trim($this->_s)=='');
	}
	
	/**
	 * Überprüft, ob diese Zeichenkette eine leere Zeichenkette ist.
	 * 
	 * @return boolean
	 */
	public function isEmpty() {
		return($this->length()==0);
	}
	
	/**
	 * Überprüft, ob diese Zeichenkette mit der übergebenen Zeichenkette beginnt.
	 * 
	 * @return boolean
	 * @param string
	 * @todo IgnoreCase
	 */
	public function startsWith($value) {
		$value=$this->ensure($value);
		
		return(substr($this->_s, 0, strlen($value))==$value);
	}
	
	/**
	 * Überprüft, ob diese Zeichenkette mit der übergebenen Zeichenkette endet.
	 * 
	 * @return boolean
	 * @param string
	 * @todo IgnoreCase
	 */
	public function endsWith($value) {
		$value=$this->ensure($value);
		
		return(substr($this->_s, -strlen($value))==$value);
	}
	
	/**
	 * Überprüft, ob diese Zeichenkette die übergebene Zeichenkette enthält.
	 * 
	 * @return boolean
	 * @param string
	 * @todo IgnoreCase
	 */
	public function includes($value) {
		$value=$this->ensure($value);
		
		return(strpos($this->_s, $value)!==false);
	}
	
	/**
	 * Überprüft, ob diese Zeichenkette mit der übergebenen Zeichenkette
	 * übereinstimmt.
	 * 
	 * @return boolean
	 * @param string
	 */
	public function equals($value) {
		if($this->_ignoreCase) {
			return($this->equalsIgnoreCase($value));
		}
		else {
			return($this->equalsRespectCase($value));
		}
	}
	
	/**
	 * Überprüft, ob diese Zeichenkette mit der übergebenen Zeichenkette
	 * übereinstimmt und nimmt dabei keine Rücksicht auf Groß- und
	 * Kleinschreibung. 
	 * 
	 * @return boolean
	 * @param string
	 */
	public function equalsIgnoreCase($value) {
		return($this->compareToIgnoreCase($value)==0);
	}
	
	/**
	 * Überprüft, ob diese Zeichenkette mit der übergebenen Zeichenkette
	 * übereinstimmt und nimmt dabei Rücksicht auf Groß- und Kleinschreibung. 
	 * 
	 * @return boolean
	 * @param string
	 */
	public function equalsRespectCase($value) {
		return($this->_s==$this->ensure($value));
	}
	
	/**
	 * Vergleicht die übergebene Zeichenkette mit dieser lexikograpisch.
	 * Ist diese Zeichenkette kleiner als $value wird ein Wert <0
	 * zurückgegeben, ist $value kleiner >0, und bei Gleichheit wird 0 zurück
	 * gegeben.
	 * 
	 * @return int
	 * @param string
	 */
	public function compareTo($value) {
		if($this->_ignoreCase) {
			return($this->compareToIgnoreCase($value));
		}
		else {
			return($this->compareToRespectCase($value));
		}
	}
	
	/**
	 * Vergleicht die übergebene Zeichenkette mit dieser lexikograpisch
	 * und nimmt dabei keine Rücksicht auf Groß- und Kleinschreibung.
	 * 
	 * @return int
	 * @param string
	 */
	public function compareToIgnoreCase($value) {
		return(strcasecmp($this->_s, $this->ensure($value)));
	}
	
	/**
	 * Vergleicht die übergebene Zeichenkette mit dieser lexikograpisch
	 * und nimmt dabei Rücksicht auf Groß- und Kleinschreibung.
	 * 
	 * @return int
	 * @param string
	 */
	public function compareToRespectCase($value) {
		return(strcmp($this->_s, $this->ensure($value)));
	}
	
	/**
	 * Vergleicht die übergebene Zeichenkette mit dieser in natürlicher
	 * Ordnung.
	 * 
	 * @return int
	 * @param string
	 */
	public function compareNaturallyTo($value) {
		if($this->_ignoreCase) {
			return($this->compareNaturallyToIgnoreCase($value));
		}
		else {
			return($this->compareNaturallyToRespectCase($value));
		}
	}
	
	/**
	 * Vergleicht die übergebene Zeichenkette mit dieser in natürlicher
	 * Ordnung und nimmt dabei keine Rücksicht auf Groß- und Kleinschreibung.
	 * 
	 * @return int
	 * @param string
	 */
	public function compareNaturallyToIgnoreCase($value) {
		return(strnatcasecmp($this->_s, $this->ensure($value)));
	}
	
	/**
	 * Vergleicht die übergebene Zeichenkette mit dieser in natürlicher
	 * Ordnung und nimmt dabei Rücksicht auf Groß- und Kleinschreibung.
	 * 
	 * @return int
	 * @param string
	 */
	public function compareNaturallyToRespectCase($value) {
		return(strnatcmp($this->_s, $this->ensure($value)));
	}
	
	/**
	 * Sucht das erste Vorkommen der übergebenen Zeichenkette in dieser
	 * Zeichenkette und gibt den entsprechenden Index zurück.
	 * Kann $value nicht gefunden werden, wird -1 zurückgegeben (und
	 * unterscheidet sich damit von der strpos-Funktion von PHP).
	 * 
	 * @return int
	 * @param string
	 * @param int Offset für den Suchbeginn
	 */
	public function positionOf($value, $offset=0) {
		if($this->_ignoreCase) {
			return($this->positionOfIgnoreCase($value, $offset));
		}
		else {
			return($this->positionOfRespectCase($value, $offset));
		}
	}
	
	/**
	 * Sucht das erste Vorkommen der übergebenen Zeichenkette in dieser
	 * Zeichenkette und gibt den entsprechenden Index zurück.
	 * Nimmt keine Rücksicht auf Groß- und Kleinschreibung
	 * 
	 * @return int
	 * @param string
	 * @param int Offset für den Suchbeginn
	 */
	public function positionOfIgnoreCase($value, $offset=0) {
		$retval=strpos($this->_s, $value, $offset);
		if($retval===false) {
			return(-1);
		}
		else {
			return($retval);
		}
	}
	
	/**
	 * Sucht das erste Vorkommen der übergebenen Zeichenkette in dieser
	 * Zeichenkette und gibt den entsprechenden Index zurück.
	 * Nimmt Rücksicht auf Groß- und Kleinschreibung
	 * 
	 * @return int
	 * @param string
	 * @param int Offset für den Suchbeginn
	 */
	public function positionOfRespectCase($value, $offset=0) {
		$retval=stripos($this->_s, $value, $offset);
		if($retval===false) {
			return(-1);
		}
		else {
			return($retval);
		}
	}
	
	/**
	 * Sucht das letzte Vorkommen der übergebenen Zeichenkette in dieser
	 * Zeichenkette und gibt den entsprechenden Index zurück.
	 * 
	 * @return int
	 * @param string
	 * @param int Offset für den Suchbeginn
	 */
	public function lastPositionOf($value, $offset=0) {
		if($this->_ignoreCase) {
			return($this->lastPositionOfIgnoreCase($value, $offset));
		}
		else {
			return($this->lastPositionOfRespectCase($value, $offset));
		}
	}
	
	/**
	 * Sucht das letzte Vorkommen der übergebenen Zeichenkette in dieser
	 * Zeichenkette und gibt den entsprechenden Index zurück.
	 * Nimmt keine Rücksicht auf Groß- und Kleinschreibung
	 * 
	 * @return int
	 * @param string
	 * @param int Offset für den Suchbeginn
	 */
	public function lastPositionOfIgnoreCase($value, $offset=0) {
		$retval=strrpos($this->_s, $value, $offset);
		if($retval===false) {
			return(-1);
		}
		else {
			return($retval);
		}
	}
	
	/**
	 * Sucht das letzte Vorkommen der übergebenen Zeichenkette in dieser
	 * Zeichenkette und gibt den entsprechenden Index zurück.
	 * Nimmt Rücksicht auf Groß- und Kleinschreibung
	 * 
	 * @return int
	 * @param string
	 * @param int Offset für den Suchbeginn
	 */
	public function lastPositionOfRespectCase($value, $offset=0) {
		$retval=strripos($this->_s, $value, $offset);
		if($retval===false) {
			return(-1);
		}
		else {
			return($retval);
		}
	}
	
	/**
	 * Gibt einen Teil dieser Zeichenkette als einzelne String-Instanz zurück.
	 * 
	 * @return Curly_Types_String
	 * @param int
	 * @param int
	 */
	public function getSubstring($start, $length=NULL) {
		if($length===NULL) {
			$retval=substr($this->_s, $start);
		}
		else {
			$retval=substr($this->_s, $start, $length);
		}
		
		if($retval===false) {
			return(NULL);
		}
		
		return($this->duplicate()->setValue($retval));
	}
	
	/**
	 * Konvertiert XML-Sonderzeichen in deren Entitäten.
	 * 
	 * @return String
	 */
	public function escapeXML() {
		$this->_s=htmlspecialchars($this->_s, ENT_QUOTES, 'UTF-8');
		return($this);
	}
	
	/**
	 * Konvertiert XML-Entitäten in deren Sonderzeichen.
	 * 
	 * @return String
	 */
	public function unescapeXML() {
		$this->_s=htmlspecialchars_decode($this->_s, ENT_QUOTES);
		return($this);
	}
	
	/**
	 * @return String
	 * @param string
	 */
	public function trim($charlist=NULL) {
		if($charlist==NULL) {
			$this->_s=trim($this->_s);
		}
		else {
			$this->_s=trim($this->_s, $this->ensure($charlist));
		}
		return($this);
	}
	
	/**
	 * @return String
	 * @param string
	 */
	public function rtrim($charlist=NULL) {
		if($charlist==NULL) {
			$this->_s=rtrim($this->_s);
		}
		else {
			$this->_s=rtrim($this->_s, $this->ensure($charlist));
		}
		return($this);
	}
	
	/**
	 * @return String
	 * @param string
	 */
	public function ltrim($charlist=NULL) {
		if($charlist==NULL) {
			$this->_s=ltrim($this->_s);
		}
		else {
			$this->_s=ltrim($this->_s, $this->ensure($charlist));
		}
		return($this);
	}
	
	/**
	 * Verwandelt das erste Zeichen dieser Zeichenkette in einen
	 * Großbuchstaben.
	 * 
	 * @return String
	 */
	public function firstToUpperCase() {
		$this->_s=ucfirst($this->_s);
		return($this);
	}
	
	/**
	 * Verwandelt das erste Zeichen dieser Zeichenkette in einen
	 * Kleinbuchstaben.
	 * 
	 * @return String
	 */
	public function firstToLowerCase() {
		$this->_s=$this->_lcfirst($this->_s);
		
		return($this);
	}
	
	/**
	 * Eigene lcfirst-Implementierung, falls diese in PHP nicht vorhanden ist.
	 * 
	 * @return string
	 * @param string
	 */
	protected function _lcfirst($value) {
		if(function_exists('lcfirst')) {
			return(lcfirst($value));
		}
		else {
			$value[0]=strtolower($value[0]);
			return($value);
		}
	}
	
	/**
	 * Wandelt jeden ersten Buchstaben eines Wortes innerhalb dieser
	 * Zeichenkette in einen Großbuchstaben.
	 * 
	 * @return String
	 */
	public function wordsToUpperCase() {
		$this->_s=ucwords($this->_s);
		return($this);
	}
	
	/**
	 * Wandelt jeden ersten Buchstaben eines Wortes innerhalb dieser
	 * Zeichenkette in einen Kleinbuchstaben.
	 * 
	 * @return String
	 */
	public function wordsToLowerCase() {
		if(function_exists('lcwords')) {
			$this->_s=lcwords($this->_s);
		}
		else {
			$split=$this->split('(\\W+)', NULL, -1, PREG_SPLIT_DELIM_CAPTURE);
			
			for($i=0, $n=count($split); $i<$n; $i++) {
				if($i%1==0) {
					$split[$i]=$this->_lcfirst($split[$i]);
				}
			}
			
			$this->_s=implode('', $split);
		}
		
		return($this);
	}
	
	/**
	 * @return String
	 * @param mixed
	 * @param mixed
	 * @param integer&
	 */
	public function replace($search, $replace, &$count=0) {
		if($this->_ignoreCase) {
			return($this->replaceIgnoreCase($search, $replace, $count));
		}
		else {
			return($this->replaceRespectCase($search, $replace, $count));
		}
	}
	
	/**
	 * @return String
	 * @param mixed
	 * @param mixed
	 * @param integer&
	 */
	public function replaceRespectCase($search, $replace, &$count=0) {
		$this->_s=str_replace($search, $replace, $this->_s, $count);
		return($this);
	}
	
	/**
	 * @return String
	 * @param mixed
	 * @param mixed
	 * @param integer&
	 */
	public function replaceIgnoreCase($search, $replace, &$count=0) {
		$this->_s=str_ireplace($search, $replace, $this->_s, $count);
		return($this);
	}
		
	/**
	 * @return boolean
	 * @param string
	 * @param array&
	 * @param integer
	 * @param integer
	 */
	public function matches($pattern, $modifier=NULL, &$matches=null, $flags=0, $offset=0) {
		if($modifier===NULL and $this->_ignoreCase) {
			$modifier='i';
		}
		
		return(preg_match(
			'/'.str_replace('/', '\\/', $pattern).'/'.$modifier,
			$this->_s,
			$matches,
			(int)$flags,
			(int)$offset
		)>=0);
	}
	
	/**#@+
	 * ArrayAccess-Interfacemethode
	 */
	/**
	 * @throws OutOfBoundsException 
	 * @return char
	 * @param integer
	 */
	public function offsetGet($index) {
		$index=(int)$index;
		if($index<0 or $index>=$this->length()) {
			throw new OutOfBoundsException(self::ERR_OUTOFBOUNDS);
		}
		return($this->_s[$index]);
	}
	
	/**
	 * @throws OutOfBoundsException 
	 * @return void
	 * @param integer
	 * @param mixed
	 */
	public function offsetSet($index, $value) {
		if($index===NULL) {
			$this->append($value);
			return;
		}
		
		$index=(int)$index;
		$value=$this->ensure($value);
		if($index<0) {
			throw new OutOfBoundsException(self::ERR_INDEXNEGATIVE);
		}
		
		$len=strlen($value);
		// Ein Zeichen
		if($len==1) {
			$this->_s[$index]=$value;
		}
		// Leere Zeichenkette
		else if($len<=0) {
			$this->_s[$index]=self::SPACE;
		}
		// Mehrere Zeichen
		else {
			// TODO: In Methode auslagern
			$thisLen=$this->length();
			if($thisLen<$index) {
				$this->_s.=str_repeat(self::SPACE, $index-$thisLen).$value;
			}
			else {
				$pre=substr($this->_s, 0, $index);
				
				$postPos=$index+strlen($value);
				if($thisLen>$postPos) {
					$post=substr($this->_s, $postPos);
					$this->_s=$pre.$value.$post;
				}
				else {
					$this->_s=$pre.$value;
				}
			}
		}
	}
	
	/**
	 * @return void
	 * @param integer
	 */
	public function offsetUnset($index) {
		$index=(int)$index;
		if($index<0 or $index>=$this->length()) {
			return;
		}
		
		$this->_s[$index]=self::SPACE;
	}
	
	/**
	 * @return boolean
	 * @param integer
	 */
	public function offsetExists($index) {
		$index=(int)$index;
		return($index>=0 and $index<$this->length());
	}
	/**#@-*/
	
}
