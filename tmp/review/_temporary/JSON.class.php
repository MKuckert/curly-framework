<?php
if( !defined( "_CORE_" ) ) die( "Unauthorisierter Zugriff!" );

if( !defined( "_CLASS_JSON_" ) ) {
	define( "_CLASS_JSON_", 1 );
	
	/**
	 * Kapselung der Umwandlungen von Variablen in JSON und umgekehrt
	 * 
	 * @since 0.10 - 19.04.2007
	 */
	class JSON {
		
		/**
		 * Wandelt den übergebenen Wert in eine JSON-Repräsentation um
		 * 
		 * @return string				Die JSON-Repräsentation	(null bei Fehlern)
		 * @param mixed					Der zu wandelnde Wert
		 * @since 0.10 - 19.04.2007
		 */
		static public function encode( $val ) {
			// Gibt es die Funktion js_encode?
			if( function_exists( 'js_encode' ) ) {
				return( js_encode( $val ) );
			}
			
			// Welcher Typ?
			$type = gettype( $val );
			switch( strtolower( $type ) ) {
				case 'boolean':
					return( self::encodeBoolean( $val ) );
				case 'integer':
					return( self::encodeInteger( $val ) );
				case 'double':
					return( self::encodeDouble( $val ) );
				case 'string':
					return( self::encodeString( $val ) );
				case 'array':
					return( self::encodeArray( $val ) );
				case 'object':
					return( self::encodeObject( $val ) );
				case 'null':
					return( self::encodeNull( $val ) );
				// resource oder unknown
				default:
					return( null );
			}
		}
		
		/**
		 * Konvertiert einen boolschen Wert in seine JSON-Repräsentation
		 * 
		 * @return string				Die JSON-Repräsentation	(null bei Fehlern)
		 * @param bool					Der zu wandelnde Wert
		 * @since 0.10 - 19.04.2007
		 */
		static private function encodeBoolean( $val ) {
			return( $val ? 'true' : 'false' );
		}
		
		/**
		 * Konvertiert einen Ganzzahlwert in seine JSON-Repräsentation
		 * 
		 * @return string				Die JSON-Repräsentation	(null bei Fehlern)
		 * @param int					Der zu wandelnde Wert
		 * @since 0.10 - 19.04.2007
		 */
		static private function encodeInteger( $val ) {
			return( strval( intval( $val ) ) );
		}
		
		/**
		 * Konvertiert einen Gleitkommawert in seine JSON-Repräsentation
		 * 
		 * @return string				Die JSON-Repräsentation	(null bei Fehlern)
		 * @param double				Der zu wandelnde Wert
		 * @since 0.10 - 19.04.2007
		 */
		static private function encodeDouble( $val ) {
			return( str_replace( ",", ".", strval( floatval( $val ) ) ) );
		}
		
		/**
		 * Konvertiert einen Zeichenkettenwert in seine JSON-Repräsentation
		 * 
		 * @return string				Die JSON-Repräsentation	(null bei Fehlern)
		 * @param string				Der zu wandelnde Wert
		 * @since 0.10 - 19.04.2007
		 */
		static private function encodeString( $val ) {
			return( '"' . strval( $val ) . '"' );
		}
		
		/**
		 * Konvertiert einen Array in seine JSON-Repräsentation
		 * 
		 * @return string				Die JSON-Repräsentation	(null bei Fehlern)
		 * @param array					Der zu wandelnde Wert
		 * @since 0.10 - 19.04.2007
		 */
		static private function encodeArray( $val, $assoc=false ) {
			$json = array();
			foreach( $val as $k=>$v ) {
				if( is_string( $k ) ) {
					$assoc = true;
					$json[] = self::encodeString( $k ) . ': ' . self::encode( $v );
				}
				else {
					$json[] = self::encodeInteger( $k ) . ': ' . self::encode( $v );
				}
			}
			return( $assoc ? '{'.implode( ", ", $json ).'}' : '['.implode( ", ", $json ).']' );
		}
		
		/**
		 * Konvertiert ein Objekt in seine JSON-Repräsentation
		 * 
		 * @return string				Die JSON-Repräsentation	(null bei Fehlern)
		 * @param object				Der zu wandelnde Wert
		 * @since 0.10 - 19.04.2007
		 */
		static private function encodeObject( $val ) {
			$member = get_object_vars( $val );
			return( self::encodeArray( $member, true ) );
		}
		
		/**
		 * Konvertiert einen Null-Wert in seine JSON-Repräsentation
		 * 
		 * @return string				Die JSON-Repräsentation	(null bei Fehlern)
		 * @param mixed					Der zu wandelnde Wert
		 * @since 0.10 - 19.04.2007
		 */
		static private function encodeNull( $val ) {
			return( 'null' );
		}
		
		/**
		 * Wandelt den übergebenen JSON-String in seinen Wert um
		 * 
		 * @return mixed
		 * @param string				Die JSON-Repräsentation
		 * @since 0.20 - 09.07.2007
		 */
		static public function decode( $json ) {
			if( !is_string( $json ) ) {
				return( null );
			}
			
			// Gibt es die Funktion js_decode?
			if( function_exists( 'js_decode' ) ) {
				return( js_decode( $json ) );
			}
			
			$json = trim( $json );
			
			/*
			String:  "bla"  ODER  bla
			Integer: 123
			Double:  123.123
			Array:   ["wert1","wert2"]
			Objekt:  {key:"value"}
			*/
			
			// erstes Zeichen extrahieren
			$firstChar = substr( $json, 0, 1 );
			
			// Array
			if( $firstChar=='[' ) {
				return( self::decodeArray( $json ) );
			}
			// Objekt
			else if( $firstChar=='{' ) {
				return( self::decodeObject( $json ) );
			}
			// String
			else if( $firstChar=='"' or $firstChar=="'" or !is_numeric( $json ) ) {
				return( self::decodeString( $json ) );
			}
			// Integer oder Double
			else if( is_numeric( $json ) ) {
				// Double
				if( strpos( $json, '.' )!==false or strpos( $json, ',' )!==false ) {
					return( self::decodeDouble( $json ) );
				}
				// Integer
				else {
					return( self::decodeInteger( $json ) );
				}
			}
			// Null
			else {
				return( null );
			}
		}
		
		/**
		 * Wandelt den übergebenen JSON-String in ein String um
		 * 
		 * @return string
		 * @param string				Die JSON-Repräsentation
		 * @since 0.20 - 09.07.2007
		 */
		static private function decodeString( $json ) {
			$firstChar = substr( $json, 0, 1 );
			
			// Erstes Zeichen " oder '
			if( $firstChar=='"' or $firstChar=="'" ) {
				$json = substr( $json, 1 );
			}
			
			$lastChar  = substr( $json, -1 );
			
			// Letztes Zeichen " oder '
			if( $lastChar=='"' or $lastChar=="'" ) {
				$json = substr( $json, 0, -1 );
			}
			
			return( $json );
		}
		
		/**
		 * Wandelt den übergebenen JSON-String in ein Integer um
		 * 
		 * @return int
		 * @param string				Die JSON-Repräsentation
		 * @since 0.20 - 09.07.2007
		 */
		static private function decodeInteger( $json ) {
			return( intval( $json ) );
		}
		
		/**
		 * Wandelt den übergebenen JSON-String in ein Double um
		 * 
		 * @return double
		 * @param string				Die JSON-Repräsentation
		 * @since 0.20 - 09.07.2007
		 */
		static private function decodeDouble( $json ) {
			$json = str_replace( ',', '.', $json );
			return( doubleval( $json ) );
		}
		
		/**
		 * Wandelt den übergebenen JSON-String in ein Array um
		 * 
		 * @return array
		 * @param string				Die JSON-Repräsentation
		 * @since 0.20 - 09.07.2007
		 */
		static private function decodeArray( $json ) {
			// Letztes Zeichen ist ]
			if( substr( $json, -1 )==']' ) {
				$json = substr( $json, 1, -1 );
			}
			// Letztes Zeichen ist nicht ]
			else {
				$json = substr( $json, 1 );
			}
			
			$array = array();
			
			// Arrayelemente einzeln extrahieren
			$token = strtok( $json, ',' );
			$index = 0;
			
			do {
				$explode = explode( ':', $token );
				
				// Membername und Wert
				if( count( $explode )>=2 ) {
					$member = self::decodeString( $explode[0] );
					$value = self::decode( $explode[1] );
				}
				else {
					$member = $index;
					$value = self::decode( $explode[0] );
					$index++;
				}
				
				// Zu Objekt hinzufügen
				$array[$member] = $value;
			}
			while( $token = strtok( ',' ) );
			
			return( $array );
		}
		
		/**
		 * Wandelt den übergebenen JSON-String in ein Objekt um
		 * 
		 * @return object
		 * @param string				Die JSON-Repräsentation
		 * @since 0.20 - 09.07.2007
		 */
		static private function decodeObject( $json ) {
			// Letztes Zeichen ist }
			if( substr( $json, -1 )=='}' ) {
				$json = substr( $json, 1, -1 );
			}
			// Letztes Zeichen ist nicht }
			else {
				$json = substr( $json, 1 );
			}
			
			$object = new ArrayExtended( array(), ArrayExtended::ARRAY_AS_PROPS );
			
			// Objektelemente einzeln extrahieren
			$token = strtok( $json, ',' );
			$index = 0;
			
			do {
				$explode = explode( ':', $token );
				
				// Membername und Wert
				if( count( $explode )>=2 ) {
					$member = self::decodeString( $explode[0] );
					$value = self::decode( $explode[1] );
				}
				else {
					$member = $index;
					$value = self::decode( $explode[0] );
					$index++;
				}
				
				// Zu Objekt hinzufügen
				$object->$member = $value;
			}
			while( $token = strtok( ',' ) );
			
			return( $object );
		}
	}
}
?>