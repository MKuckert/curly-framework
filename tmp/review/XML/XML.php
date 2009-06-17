<?php

/**
 * XML
 * Stellt kleine Hilfsmethoden für den Umgang mit XML-Daten bereit.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package XML
 * @since 1.0 - 02.05.2008
 * @version 1.0
 */
class XML {

	static public function escape( $value ) {
		return( htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ) );
	}

	static public function unescape( $value ) {
		return( html_entity_decode( $value, ENT_QUOTES, 'UTF-8' ) );
	}

	static public function getHead( $version="1.0", $encoding="UTF-8", $standalone=false ) {
		return( '<?xml version="'.$version.'" encoding="'.$encoding.'"'.( $standalone ? ' standalone="yes"' : '' ).' ?>' );
	}

}

?>