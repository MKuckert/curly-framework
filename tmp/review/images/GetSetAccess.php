<?php
	
	/**
	 * GetSetAccess
	 * Bietet Memberzugriff auf Getter- und Setter-Methoden
	 * 
	 * @author Martin Kuckert
	 * @package GDImage
	 * @since 21.11.2007
	 * @copyright Copyright &copy; 2007 Martin Kuckert
	 * @license New BSD License
	 * @version 1.0
	 */
	abstract class GetSetAccess {
		
		/**#@+
		 * @throws InvalidArgumentException
		 */
		/**
		 * Stellt Memberzugriff auf Getter-Methoden zur Verfügung
		 * 
		 * @return mixed
		 * @param string Name des Member
		 */
		public function __get( $member ) {
			if( is_callable( array( $this, 'get'.$member ) ) ) {
				return( $this->{'get'.$member}() );
			}
			else {
				throw new InvalidArgumentException( 'Member '.$member.' not defined!' );
			}
		}
		
		/**
		 * Stellt Memberzugriff auf Setter-Methoden zur Verfügung
		 * 
		 * @return mixed
		 * @param string Name des Member
		 * @param mixed Zu setzender Wert für den Member
		 */
		public function __set( $member, $value ) {
			if( is_callable( array( $this, 'set'.$member ) ) ) {
				return( $this->{'set'.$member}( $value ) );
			}
			else {
				throw new InvalidArgumentException( 'Member '.$member.' not defined!' );
			}
		}
		/**#@-*/
		
	}
	
?>