<?php

/**
 * Curly_Util_TypeInfo
 * 
 * Diese Klasse bietet statische Methoden, mit denen schnell und einfach
 * Werte auf bestimmte Kriterien überprüft werden können. Diese gehen von
 * Standardmethoden wie {@link isInt} oder {@link isNull} hin zu Methoden
 * wie {@link isInRange} oder {@link isArrayOf}.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Util
 * @since 1.0 - 30.04.2008
 * @version 1.0
 */
final class Curly_Util_TypeInfo {

	/**
	 * Überprüft, ob der übergebene Parameter vom Typ Integer ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isInt( $value ) {
		return( is_int( $value ) );
	}

	/**
	 * Überprüft, ob der übergebene Parameter vom Typ Double ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isDouble( $value ) {
		return( is_float( $value ) );
	}

	/**
	 * Überprüft, ob der übergebene Parameter vom Typ String ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isString( $value ) {
		return( is_string( $value ) );
	}

	/**
	 * Überprüft, ob der übergebene Parameter vom Typ Array ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isArray( $value ) {
		return( is_array( $value ) );
	}

	/**
	 * Überprüft, ob der übergebene Parameter ein Objekt ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isObject( $value ) {
		return( is_object( $value ) );
	}

	/**
	 * Überprüft, ob der übergebene Parameter eine Resource ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isResource( $value ) {
		return( is_resource( $value ) );
	}

	/**
	 * Überprüft, ob der übergebene Parameter der Wert NULL ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isNull( $value ) {
		return( is_null( $value ) );
	}

	/**
	 * Überprüft, ob der übergebene Parameter vom Typ Integer und im
	 * positiven Wertebereich ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isUInt( $value ) {
		return( is_int( $value ) and $value>=0 );
	}

	/**
	 * Überprüft, ob der übergebene Parameter größergleich $min und
	 * kleinergleich $max ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isInRange( $value, $min, $max ) {
		return( $value>=$min and $value<=$max );
	}

	/**
	 * Überprüft, ob der übergebene Parameter lediglich weiße Zeichen
	 * enthält.
	 *
	 * Weiße Zeichen sind die Zeichen
	 * - Leerzeichen (0x20; " "),
	 * - Tabulatorzeichen (0x09; "\t"),
	 * - Zeilenvorschub (0x0A; "\n"),
	 * - Wagenrücklauf (0x0D; "\r"),
	 * - Nullbyte (0x00; "\0"),
	 * - vertikales Tabulatorzeichen (0x0B; "\x0B").
	 *
	 * Eine leere Zeichenkette ist ungültig.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isWhiteString( $value ) {
		return( $value!='' and trim( $value )=='' );
	}

	/**
	 * Überprüft, ob $value ein Array ist und jeder Wert in diesem vom Typ
	 * $type ist.
	 *
	 * @return boolean
	 * @param mixed
	 * @param string Ein Typ (siehe {@link http://php.net/gettype}) oder
	 *  ein Klassenname.
	 */
	static public function isArrayOf( $value, $type ) {
		if( !is_array( $value ) ) {
			return( false );
		}
			
		foreach( $value as $v ) {
			if( $v instanceof $type ) {
				continue;
			}
			else if( gettype( $v )!=$type ) {
				return( false );
			}
		}
			
		return( true );
	}

	/**
	 * Überprüft, ob $value ein Array ist und jeder Wert in diesem ein
	 * Integer ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isIntArray( $value ) {
		if( !is_array( $value ) ) {
			return( false );
		}
			
		foreach( $value as $v ) {
			if( !is_int( $v ) ) {
				return( false );
			}
		}
			
		return( true );
	}

	/**
	 * Überprüft, ob $value ein Array ist und jeder Wert in diesem ein
	 * positiver Integer ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isUIntArray( $value ) {
		if( !is_array( $value ) ) {
			return( false );
		}
			
		foreach( $value as $v ) {
			if( !is_int( $v ) or $v<0 ) {
				return( false );
			}
		}
			
		return( true );
	}

	/**
	 * Überprüft, ob $value ein Array ist und jeder Wert in diesem ein
	 * String ist.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isStringArray( $value ) {
		if( !is_array( $value ) ) {
			return( false );
		}
			
		foreach( $value as $v ) {
			if( !is_string( $v ) ) {
				return( false );
			}
		}
			
		return( true );
	}

	/**
	 * @todo: Implementieren!
	 */
	static public function isInEnum( $value ) {
			
	}

	/**
	 * Überprüft, ob der übergebene Parameter vom Typ Integer ist und im
	 * Wertebereich eines Bytes liegt (im Interval [0-255]).
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isByte( $value ) {
		return( is_int( $value ) and $value>=0 and $value<256 );
	}

	/**
	 * Überprüft, ob der übergebene Parameter vom Typ $type ist.
	 *
	 * @return boolean
	 * @param mixed
	 * @param string Ein Typ (siehe {@link http://php.net/gettype}) oder
	 *  ein Klassenname.
	 */
	static public function isOfType( $value, $type ) {
		return(
			$value instanceof $type or
			gettype( $value )==$type
		);
	}

	/**
	 * Überprüft, ob der übergebene Parameter ein gültiger Callback-Typ
	 * ist.
	 *
	 * Dies ist ein einfacher String oder ein Array aus zwei Elementen,
	 * wobei das erste Element ein String oder ein Objekt und das zweite
	 * Element ein String ist. Alternativ kann $callback eine Instanz der
	 * Klasse {@link Curly_Types_Callback} sein.
	 *
	 * @return boolean
	 * @param mixed
	 */
	static public function isCallback( $callback ) {
		return(
			is_callable( $callback ) or
			$callback instanceof Curly_Types_Callback
		);
	}
	
	/**
	 * Überprüft, ob der übergebene Parameter ein gültiger Traversable-Typ
	 * ist.
	 * 
	 * Dies ist der Fall, wenn dies eine Instanz eines Klasse ist, die das
	 * Traversable, respektive Iterator oder IteratorAggregate-Interface
	 * implementiert oder ein Array ist.
	 * 
	 * @return boolean
	 * @param mixed
	 */
	static public function isTraversable( $parameter ) {
		return(
			is_array( $parameter )
			or (
				is_object( $parameter )
				and $parameter instanceof Traversable
			)
		);
	}
	
	/**
	 * Überprüft, ob die Klasse mit dem Namen $class die Klasse $parent
	 * erweitert.
	 * 
	 * @return boolean
	 * @param string
	 * @param string
	 */
	static public function classExtends( $class, $parent ) {
		return( in_array( $parent, class_parents( $class ) ) );
	}
	
	/**
	 * Überprüft, ob die Klasse mit dem Namen $class das Interface $parent
	 * implementiert.
	 * 
	 * @return boolean
	 * @param string
	 * @param string
	 */
	static public function classImplements( $class, $parent ) {
		return( in_array( $parent, class_implements( $class ) ) );
	}

}

?>