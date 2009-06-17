<?php
	
	/**
	 * GetSetHasRemoveAccess
	 * Bietet Memberzugriff auf Getter- und Setter-Methoden. Weiter steht Zugriff
	 * auf has*- und remove*-Methoden zur Verfügung.
	 * 
	 * @author Martin Kuckert
	 * @package GDImage
	 * @since 21.11.2007
	 * @copyright Copyright &copy; 2007 Martin Kuckert
	 * @license New BSD License
	 * @version 1.0
	 */
	abstract class GetSetHasRemoveAccess extends GetSetAccess {
		
		/**#@+
		 * @throws InvalidArgumentException
		 */
		/**
		 * Führt eine Überprüfungsfunktion has* aus
		 * 
		 * @return boolean
		 * @param string Name des Member
		 */
		public function __isset( $member ) {
			if( is_callable( array( $this, 'has'.$member ) ) ) {
				return( $this->{'has'.$member}() );
			}
			else {
				throw new InvalidArgumentException( 'Member '.$member.' not defined!' );
			}
		}
		
		/**
		 * Führt eine Entfernfunktion remove* aus
		 * 
		 * @return void
		 * @param string Name des Member
		 */
		public function __unset( $member ) {
			if( is_callable( array( $this, 'remove'.$member ) ) ) {
				$this->{'remove'.$member}();
			}
			else {
				throw new InvalidArgumentException( 'Member '.$member.' not defined!' );
			}
		}
		/**#@-*/
		
	}
	
?>