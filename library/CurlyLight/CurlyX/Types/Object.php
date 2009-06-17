<?php

/**
 * CurlyX_Types_Object
 *
 * Basisklasse für Objekte.
 *
 * Bildet Getter- und Setter-Methoden auf Eigenschaften ab, so dass diese virtuell
 * typisiert werden können.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Types
 * @since 1.0 - 27.03.2008
 * @version 1.0
 */
abstract class CurlyX_Types_Object {

	/**
	 * Bildet Getter-Methoden auf Eigenschaften ab.
	 * Wird die Eigenschaft mit dem übergebenen Namen nicht auf eine Getter-Methode
	 * abgebildet, wird eine MemberNotDefinedException geworfen.
	 *
	 * @throws MemberNotDefinedException
	 * @return mixed
	 * @param string Der Name der Membervariable
	 */
	public function __get( $member ) {
		$method = 'get'.$member;
		if( TypeInfo::isCallback( array( $this, $method ) ) ) {
			return( $this->$method() );
		}
		else {
			throw new MemberNotDefinedException(
					'The requested member '.$member.' is not defined for this object',
			0,
			$member
			);
		}
	}

	/**
	 * Bildet Setter-Methoden auf Eigenschaften ab.
	 * Wird die Eigenschaft mit dem übergebenen Namen nicht auf eine Setter-Methode
	 * abgebildet, wird eine MemberNotDefinedException geworfen.
	 *
	 * @throws MemberNotDefinedException
	 * @return void
	 * @param string Der Name der Membervariable
	 * @param mixed Der Wert, den die Membervariable erhalten soll
	 */
	public function __set( $member, $value ) {
		$method = 'set'.$member;
		if( TypeInfo::isCallback( array( $this, $method ) ) ) {
			$this->$method( $value );
		}
		else {
			throw new MemberNotDefinedException(
					'The requested member '.$member.' is not defined for this object',
			0,
			$member
			);
		}
	}

	/**
	 * Diese Methode kann in Unterklassen so überladen werden, dass auf
	 * den Zugriff auf nichtexistierende Methode reagiert wird.
	 * Diese Basisimplementation wirft lediglich eine MethodNotDefinedException.
	 *
	 * @throws MethodNotDefinedException
	 * @return mixed
	 * @param string Der Name der Methode
	 * @param array Array der übergebenen Argumente für die Methode
	 */
	public function __call( $method, $arguments ) {
		throw new MethodNotDefinedException(
				'The requested method '.$method.' is not defined for this object',
		0,
		$method,
		$arguments
		);
	}

	/**
	 * Überprüft, ob die übergebene Instanz und diese Instanz als Gleich gelten.
	 * Diese Methode kann in Unterklassen je nach Bedarf angepasst werden.
	 * Die Standardimplementierung dieser Klasse verwendet hierzu den ===-Operator.
	 *
	 * @return boolean
	 * @param Object Das mit diesem Objekt zu vergleichende Objekt.
	 */
	public function equals( Object $that ) {
		return( $this===$that );
	}

	/**
	 * Dekonstruktor
	 * In Unterklassen sollte anstatt dieser Methode die dispose-Methode
	 * überschrieben werden.
	 */
	public function __destruct() {
		unset( $this );
	}

}

?>