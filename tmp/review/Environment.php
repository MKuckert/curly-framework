<?php
	
	/**
	 * Environment
	 * Stellt Informationen zu der Anwendungsumgebung bereit
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	class Environment {
		
		static public function isUnix() {
			
		}
		
		static public function isWindows() {
			if( isset( $_SERVER['SERVER_SOFTWARE'] ) ) {
			
			}
		}
		
	}
	
?>