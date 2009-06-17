<?php

/**
 * MethodNotDefinedException
 * Zeigt an, das eine Methode nicht definiert ist und nicht aufgerufen
 * werden kann.
 * Mit den {@link getMethod} und {@link getArguments}-Methoden kann auf den
 * Aufruf der Methode zugegriffen werden.
 *
 * @author Martin Kuckert
 */
class MethodNotDefinedException extends LogicException {

	/**
	 * @var string Der Name der Methode.
	 */
	protected $method;

	/**
	 * @var array Die Argumente der Methode.
	 */
	protected $arguments;

	/**
	 * Gibt den Namen der Methode zurück.
	 *
	 * @return string
	 */
	public function getMethod() {
		return( $this->method );
	}

	/**
	 * Gibt die Argumente der Methode zurück.
	 *
	 * @return array
	 */
	public function getArguments() {
		return( $this->arguments );
	}

	/**
	 * Konstruktor
	 *
	 * @param string Der Nachrichtentext der Ausnahme
	 * @param integer Der Fehlercode der Ausnahme
	 * @param string Der Name der Methode, auf die sich diese Ausnahme
	 * bezieht.
	 * @param array Ein Array der Argumente, mit der die Methode aufgerufen
	 * werden sollte.
	 */
	public function __construct( $message=NULL, $code=0, $method=NULL, array $arguments=array() ) {
		parent::__construct( $message, $code );
			
		$this->method = $method;
		$this->arguments = $arguments;
	}
}

?>