<?php
if( !defined( "_CORE_" ) ) die( "Unauthorisierter Zugriff!" );

if( !defined( "_CLASS_STRING_" ) ) {
	define( "_CLASS_STRING_", 1 );
	
	/**
	 * Klasse für das Bearbeiten eines String
	 * <example>
	 * 	$str = new String( $_GET['input'] );
	 *  $str->trim();
	 *  if( $str->startsWith( 'goto' ) ) {
	 * 		doGotoAction( $str );
	 * 	}
	 * 	echo $str;	// oder echo $str->toString();
	 * </example>
	 * 
	 * @since 0.03 - 28.03.2007
	 */
	class String__old {
		/**
		 * Interner Speicher für den string
		 * 
		 * @type string
		 * @since 0.03 - 28.03.2007
		 */
		private $str;
		
		/**
		 * Konstruktor
		 * Erstellt einen neuen String
		 * Möglichkeit 1)
		 *  string übergeben, so wird ein Objekt mit dieser Zeichenkette angelegt
		 * Möglichkeit 2)
		 *  integer übergeben, so wird ein Objekt mit dieser Länge angelegt. Dabei wird das Zeichen in Parameter 2 so oft wiederholt
		 * Möglichkeit 3)
		 *  nichts übergeben, so wird ein leerer String angelegt.
		 * 
		 * @param mixed
		 * @param string
		 * @since 0.03 - 28.03.2007
		 */
		public function __construct( $_1, $_2=' ' ) {
			// Möglichkeit 1)
			if( is_string( $_1 ) ) {
				$this->str = $_1;
			}
			// Möglichkeit 2)
			else if( is_int( $_1 ) and is_string( $_2 ) ) {
				for( $i=0; $i<$_1; $i++ ) {
					$this->str += $_2;
				}
			}
			// Möglichkeit 3)
			else {
				$this->str = '';
			}
		}
		
		/**
		 * Überprüft, ob der String mit der übergebenen Zeichenkette beginnt.
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return bool
		 * @param string/String
		 * @since 0.03 - 28.03.2007
		 */
		public function startsWith( $str ) {
			if( $str instanceof String ) {
				return( substr( $this->str, 0, $str->length() )==$str->str );
			}
			else if( is_string( $str ) ){
				return( substr( $this->str, 0, strlen( $str ) )==$str );
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
		
		/**
		 * Überprüft, ob der String mit der übergebenen Zeichenkette endet.
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return bool
		 * @param string/String
		 * @since 0.03 - 28.03.2007
		 */
		public function endsWith( $str ) {
			if( $str instanceof String ) {
				return( substr( $this->str, -$str->length() )==$str->str );
			}
			else if( is_string( $str ) ){
				return( substr( $this->str, -strlen( $str ) )==$str );
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
		
		/**
		 * Gibt die Position zurück, an der der übergebene String das erste mal gefunden wird
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return integer					false, wenn gar nicht gefunden
		 * @param string/String
		 * @param integer					Offset, ab dem Gesucht wird
		 * @since 0.03 - 28.03.2007
		 */
		public function indexOf( $str, $offset=0 ) {
			if( !is_int( $offset ) ) {
				throw new InvalidArgumentException( "Argument 2 muss vom Typ Integer sein!" );
			}
			
			if( $str instanceof String ) {
				return( strpos( $this->str, $str->str, $offset ) );
			}
			else if( is_string( $str ) ){
				return( strpos( $this->str, $str, $offset ) );
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
		
		/**
		 * Gibt die Position zurück, an der der übergebene String das letzte mal gefunden wird
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return integer					false, wenn gar nicht gefunden
		 * @param string/String
		 * @param integer					Offset, ab dem Gesucht wird
		 * @since 0.03 - 28.03.2007
		 */
		public function lastIndexOf( $str, $offset=0 ) {
			if( !is_int( $offset ) ) {
				throw new InvalidArgumentException( "Argument 2 muss vom Typ Integer sein!" );
			}
			
			if( $str instanceof String ) {
				return( strrpos( $this->str, $str->str, $offset ) );
			}
			else if( is_string( $str ) ){
				return( strrpos( $this->str, $str, $offset ) );
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
		
		/**
		 * Gibt einen Unterstring zurück
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return String
		 * @param integer					Startpunkt des Unterstring
		 * @param integer					Maximallänge des Unterstring
		 * @since 0.03 - 28.03.2007
		 */
		public function getSubstring( $start=0, $length=null ) {
			if( !is_int( $start ) ) {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ Integer sein!" );
			}
			if( !is_null( $length ) and !is_int( $length ) ) {
				throw new InvalidArgumentException( "Argument 2 muss vom Typ Integer sein!" );
			}
			
			if( is_null( $length ) ) {
				return( new String( substr( $this->str, $start ) ) );
			}
			else {
				return( new String( substr( $this->str, $start, $length ) ) );
			}
		}
		
		/**
		 * Gibt das Zeichen an der Position des String zurück. Zeichen 1 entspricht dem Index 0!
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * Wirft OutOfRangeException bei ungültigem Indexwert
		 * 
		 * @return String
		 * @param integer					Indexwert
		 * @since 0.03 - 28.03.2007
		 */
		public function charAt( $index ) {
			if( !is_int( $index ) ) {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ Integer sein!" );
			}
			if( $index<0 or $index>=$this->length() ) {
				throw new OutOfRangeException( "Argument 1 ist außerhalb des Indexbereiches!" );
			}
			
			return( $this->getSubstring( $index, 1 ) );
		}
		
		/**
		 * Gibt die Länge dieser Zeichenkette zurück
		 * 
		 * @return integer
		 * @since 0.03 - 28.03.2007
		 */
		public function length() {
			return( strlen( $this->str ) );
		}
		
		/**
		 * Fügt eine Zeichenkette an diese an
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return String&			Referenz auf dieses Objekt
		 * @param string/String
		 * @since 0.03 - 28.03.2007
		 */
		public function &concat( $str ) {
			if( $str instanceof String ) {
				$this->str .= $str->str;
			}
			else if( is_string( $str ) ){
				$this->str .= $str;
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
			return( $this );
		}
		
		/**
		 * Überprüft, ob diese und die übergebene Zeichenkette gleich sind
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return bool
		 * @param string/String
		 * @since 0.03 - 28.03.2007
		 */
		public function equals( $str ) {
			if( $str instanceof String ) {
				return( $this->str == $str->str );
			}
			else if( is_string( $str ) ){
				return( $this->str == $str );
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
		
		/**
		 * Überprüft, ob diese und die übergebene Zeichenkette gleich sind. Dabei wird Gross- und Kleinschreibung ignoriert.
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return bool
		 * @param string/String
		 * @since 0.03 - 28.03.2007
		 */
		public function equalsIgnoreCase( $str ) {
			if( $str instanceof String ) {
				return( strtolower( $this->str ) == strtolower( $str->str ) );
			}
			else if( is_string( $str ) ){
				return( strtolower( $this->str ) == strtolower( $str ) );
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
		
		/**
		 * Gibt diesen String in Kleinbuchstaben zurück.
		 * 
		 * @return String
		 * @since 0.03 - 28.03.2007
		 */
		public function toLowerCase() {
			return( new String( strtolower( $this->str ) ) );
		}
		
		/**
		 * Gibt diesen String in Großbuchstaben zurück.
		 * 
		 * @return String
		 * @since 0.03 - 28.03.2007
		 */
		public function toUpperCase() {
			return( new String( strtoupper( $this->str ) ) );
		}
		
		/**
		 * Ersetzt Zeichenketten in dieser Zeichenkette.
		 * 
		 * @return String
		 * @param mixed					Zu ersetzende Zeichenkette(n)
		 * @param mixed					Ersatz-Zeichenkette(n)
		 * @since 0.03 - 28.03.2007
		 */
		public function replace( $search, $replace ) {
			return( new String( $this->str = str_replace( $search, $replace, $this->str ) ) );
		}
		
		/**
		 * Trennt diese Zeichenkette in ein Array von Zeichenketten auf
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return Array
		 * @param mixed				Trennzeichen
		 * @since 0.03 - 28.03.2007
		 */
		public function split( $split ) {
			if( $split instanceof String ) {
				return( explode( $split->str, $this->str ) );
			}
			else if( is_string( $split ) ) {
				return( explode( $split, $this->str ) );
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
		
		/**
		 * Entfernt Leerzeichen am Anfang und am Ende des Strings
		 * 
		 * @return String&			Referenz auf dieses Objekt
		 * @param mixed				Trennzeichen
		 * @since 0.03 - 28.03.2007
		 */
		public function &trim() {
			$this->str = trim( $this->str );
			return( $this );
		}
		
		/**
		 * Entfernt Leerzeichen am Anfang des Strings
		 * 
		 * @return String&			Referenz auf dieses Objekt
		 * @param mixed				Trennzeichen
		 * @since 0.03 - 28.03.2007
		 */
		public function &ltrim() {
			$this->str = ltrim( $this->str );
			return( $this );
		}
		
		/**
		 * Entfernt Leerzeichen am Ende des Strings
		 * 
		 * @return String&			Referenz auf dieses Objekt
		 * @param mixed				Trennzeichen
		 * @since 0.03 - 28.03.2007
		 */
		public function &rtrim() {
			$this->str = rtrim( $this->str );
			return( $this );
		}
		
		/**
		 * Gibt dieses Objekt als string zurück
		 * 
		 * @return string
		 * @since 0.03 - 28.03.2007
		 */
		public function toString() {
			return( $this->str );
		}
		
		/**
		 * Gibt dieses Objekt als string zurück
		 * 
		 * @return string
		 * @since 0.03 - 28.03.2007
		 */
		public function __toString() {
			return( $this->str );
		}
		
		/**
		 * Vergleicht diesen String mit einer RegEx
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return string
		 * @since 0.03 - 28.03.2007
		 */
		public function matches( $search, &$matches=null, $flags=null, $offset=null ) {
			if( $search instanceof String ) {
				if( is_null( $flags ) ) {
					return( preg_match( $search->str, $this->str, $matches ) );
				}
				else if( is_null( $offset ) ) {
					return( preg_match( $search->str, $this->str, $matches, $flags ) );
				}
				else {
					return( preg_match( $search->str, $this->str, $matches, $flags, $offset ) );
				}
			}
			else if( is_string( $search ) ) {
				if( is_null( $flags ) ) {
					return( preg_match( $search, $this->str, $matches ) );
				}
				else if( is_null( $offset ) ) {
					return( preg_match( $search, $this->str, $matches, $flags ) );
				}
				else {
					return( preg_match( $search, $this->str, $matches, $flags, $offset ) );
				}
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
		
		/**
		 * Vergleicht zwei Zeichenketten lexikograpisch.
		 * Ist diese Zeichenkette kleiner als str wird < 0 zurückgegeben, ist str kleiner > 0, und bei Gleichheit gibt die Funktion 0 zurück.
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return int
		 * @param string/String
		 * @since 0.03 - 28.03.2007
		 */
		public function compareTo( $str ) {
			if( $str instanceof String ) {
				return( strcmp( $this->str, $str->str ) );
			}
			else if( is_string( $str ) ) {
				return( strcmp( $this->str, $str ) );
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
		
		/**
		 * Vergleicht zwei Zeichenketten lexikograpisch ohne Rücksicht auf Groß- und Kleinschreibung.
		 * Ist diese Zeichenkette kleiner als str wird < 0 zurückgegeben, ist str kleiner > 0, und bei Gleichheit gibt die Funktion 0 zurück.
		 * Wirft InvalidArgumentException bei ungültigem Datentyp
		 * 
		 * @return int
		 * @param string/String
		 * @since 0.03 - 28.03.2007
		 */
		public function compareToIgnoreCase( $str ) {
			if( $str instanceof String ) {
				return( strcmp( strtolower( $this->str ), strtolower( $str->str ) ) );
			}
			else if( is_string( $str ) ) {
				return( strcmp( strtolower( $this->str ), strtolower( $str ) ) );
			}
			else {
				throw new InvalidArgumentException( "Argument 1 muss vom Typ string oder String sein!" );
			}
		}
	};
}
?>