<?php

/**
 * MemberNotDefinedException
 * Zeigt an, das eine Membervariable nicht definiert ist und nicht aufgerufen
 * werden kann.
 * Mit der {@link getMember} -Methode kann auf den Namen der Variable
 * zugegriffen werden.
 *
 * @author Martin Kuckert
 */
class MemberNotDefinedException extends LogicException {


	/**
	 * @var string Der Name der Membervariable.
	 */
	protected $member;

	/**
	 * Gibt den Namen der Membervariable zurück.
	 *
	 * @return string
	 */
	public function getMember() {
		return( $this->member );
	}

	/**
	 * Konstruktor
	 *
	 * @param string Der Nachrichtentext der Ausnahme
	 * @param integer Der Fehlercode der Ausnahme
	 * @param string Der Name der Membervariable.
	 */
	public function __construct( $message=NULL, $code=0, $member=NULL ) {
		parent::__construct( $message, $code );
			
		$this->member = $member;
	}

}

?>