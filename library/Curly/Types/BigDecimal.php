<?php

/**
 * Curly_Types_BigDecimal
 * 
 * Implementiert einen Gleitkommazahldatentyp auf Basis der bcmath-Erweiterung.
 * 
 * Aus Performancegründen erzeugen die einzelnen Operationen keine neue
 * Instanz und geben diese zurück, wie dies zum Beispiel bei Java der Fall ist,
 * sondern die verwendete Instanz wird modifiziert. Dadurch erlaubt diese Klasse
 * auch das Feature des Chainings, wie im Beispiel zu sehen ist.
 * 
 * Beispiel:
 * <code>
 *  $bi=new Curly_Types_BigDecimal('1000000');
 *  $bi->add('1000');
 *  
 *  // Soll die aktuelle Instanz nicht geändert werden, sondern eine Kopie
 *  // verwendet werden:
 * 	$bi2=$bi->copy()->add('1000');
 *  
 *  // Chaining
 *  echo $bi->setScale(20)
 *          ->add('1000')
 *          ->multiply(30402)
 *          ->getValue();
 * </code>
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types
 * @since 1.0 - 08.12.2008
 * @version 1.0
 * @todo Implement me
 */
class Curly_Types_BigDecimal {
	
	/**
	 * @var string Aktueller Wert dieser Instanz.
	 */
	private $_value;
	
	/**
	 * @var integer Zu verwendende Genauigkeit.
	 */
	private $_scale;
	
	/**
	 * Gibt den aktuellen Wert dieser Instanz zurück.
	 * 
	 * @return string
	 */
	public function getValue() {
		return($this->_value);
	}
	
	/**
	 * Setzt den aktuellen Wert dieser Instanz.
	 * 
	 * @return Curly_Types_BigDecimal
	 * @param string
	 */
	public function setValue($value) {
		$this->_value=$this->ensure($value);
		return($this);
	}
	
	/**
	 * Gibt die zu verwendende Genauigkeit zurück.
	 * 
	 * @return integer
	 */
	public function getScale() {
		return($this->_scale);
	}
	
	/**
	 * Setzt die zu verwendende Genauigkeit.
	 * 
	 * @return Curly_Types_BigDecimal
	 * @param integer
	 */
	public function setScale($value) {
		$this->_scale=abs(Curly_Types_Ensure::int($value));
		return($this);
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param mixed Initialer Wert dieses Objektes
	 * @param integer Zu verwendende Genauigkeit. Standard ist 10
	 */
	public function __construct($value, $scale=10) {
		$this->setValue($value);
		$this->setScale($scale);
	}
	
	/**
	 * Führt eine Konversion nach String durch.
	 * 
	 * @return string
	 */
	public function __toString() {
		return($this->getValue());
	}
	
	/**
	 * Dupliziert diese Instanz und gibt das Duplikat zurück.
	 * 
	 * @return Curly_Types_BigDecimal
	 */
	public function copy() {
		return(clone $this);
	}
	
	/**
	 * Statische Factory-Methode für einfacheres Chaining.
	 * 
	 * @return Curly_Types_BigDecimal
	 */
	static public function create($value, $scale=10) {
		return(new Curly_Types_BigDecimal($value, $scale));
	}
	
	/**
	 * @return string
	 * @param mixed
	 */
	public function ensure($value) {
		if($value instanceof Curly_Types_BigDecimal) {
			return($value->getValue());
		}
		
		$value=strval($value);
		
		// Zeichen validieren
		if(!preg_match('/^-?[0-9]+(\\.[0-9]+)?$/', $value)) {
			return('0');
		}
		
		if(strpos($value, '.')!==false) {
			$value=rtrim($value, '0');
		}
		if($value=='') {
			return('0');
		}
		return($value);
	}
	
	/**
	 * Addiert den übergebenen Wert zu diesem Objekt hinzu.
	 * 
	 * @return Curly_Types_BigDecimal
	 * @param mixed
	 */
	public function add($value) {
		$this->setValue(bcadd($this->getValue(), $this->ensure($value), $this->getScale()));
		return($this);
	}
	
	/**
	 * Subtrahiert den übergebenen Wert von diesem Objekt.
	 * 
	 * @return Curly_Types_BigDecimal
	 * @param mixed
	 */
	public function subtract($value) {
		$this->setValue(bcsub($this->getValue(), $this->ensure($value), $this->getScale()));
		return($this);
	}
	
	/**
	 * Multipliziert den übergebenen Wert zu diesem Objekt hinzu.
	 * 
	 * @return Curly_Types_BigDecimal
	 * @param mixed
	 */
	public function multiply($value) {
		$this->setValue(bcmul($this->getValue(), $this->ensure($value), $this->getScale()));
		return($this);
	}
	
	/**
	 * Dividiert den übergebenen Wert von diesem Objekt.
	 * 
	 * @return Curly_Types_BigDecimal
	 * @param mixed
	 */
	public function divide($value) {
		$this->setValue(bcdiv($this->getValue(), $this->ensure($value), $this->getScale()));
		return($this);
	}
	
	/**
	 * Setzt dieses Objekt auf den Modulos von diesem Objekt unter Verwendung
	 * der Übergabe.
	 * 
	 * @return Curly_Types_BigDecimal
	 * @param mixed
	 */
	public function modulo($value) {
		$this->setValue(bcmod($this->getValue(), $this->ensure($value)));
		return($this);
	}
	
	/**
	 * Potenziert dieses Objekt mit dem übergebenen Wert.
	 * 
	 * @return Curly_Types_BigDecimal
	 * @param mixed Muss ein Ganzzahlwert sein
	 */
	public function potentiate($value) {
		$this->setValue(bcpow($this->getValue(), $this->ensure($value), $this->getScale()));
		return($this);
	}
	
	/**
	 * Potenziert dieses Objekt mit dem übergebenen Wert, vermindert um den
	 * angegebenen Modulo-Wert.
	 * 
	 * Hinweis: Dieses Objekt muss einen Ganzzahlwert repräsentieren
	 * 
	 * @return Curly_Types_BigDecimal
	 * @param mixed Muss ein Ganzzahlwert sein
	 * @param mixed Muss ein Ganzzahlwert sein
	 */
	public function potentiateModulo($value, $modulo) {
		$this->setValue(bcpowmod($this->getValue(), $this->ensure($value), $this->ensure($modulo), $this->getScale()));
		return($this);
	}
	
	/**
	 * Zieht die Quadratwurzel von diesem Objekt
	 * 
	 * @return Curly_Types_BigDecimal
	 */
	public function squareRoot() {
		$this->setValue(bcsqrt($this->getValue(), $this->getScale()));
		return($this);
	}
	
	/**
	 * Negiert das Vorzeichen dieses Objektes.
	 * 
	 * @return Curly_Types_BigDecimal
	 */
	public function negate() {
		return($this->multiply(-1));
	}
	
	/**
	 * Wandelt dieses Objekt in einen positiven Wert.
	 * 
	 * @return Curly_Types_BigDecimal
	 */
	public function absolute() {
		if(substr($v=$this->getValue(), 0, 1)=='-') {
			$this->setValue(substr($v, 1));
		}
		return($this);
	}
	
	/**
	 * Vergleich dieses Objekt mit dem übergebenen Wert. Als Ergebnis wird
	 * 	0 zurückgegeben, wenn beide den gleichen Wert repräsentieren
	 *  1, wenn dieses Objekt größer ist,
	 *  -1 sonst.
	 * 
	 * @return integer
	 * @param mixed
	 */
	public function compareTo($value) {
		return(bccomp($this->getValue(), $this->ensure($value), $this->getScale()));
	}
	
	/**
	 * Vergleich dieses Objekt mit dem übergebenen Wert auf Gleichheit.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	public function equals($value) {
		return($this->compareTo($value)==0);
	}
	
}
