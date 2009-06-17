<?php
	
	class Stats {
		
		/**
		 * @var Zend_Config
		 */
		static private $_configs = null;
		
		/**
		 * @return Zend_Config
		 */
		static public function configs() {
			return( self::$_configs );
		}
		
		/**
		 * @return Zend_Config
		 */
		static public function setConfigs( Zend_Config $config ) {
			self::$_configs = $config;
		}
		
	}
	
?>