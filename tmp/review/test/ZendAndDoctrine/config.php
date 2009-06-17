<?php
	if( !defined( __CONFIG_PHP__ ) ) {
		define( '__CONFIG_PHP__', true );
		
		// Include-Pfade hinzufügen
		if( !defined( 'DOCTRINE_PATH' ) ) {
			define( 'DOCTRINE_PATH', 'C:/home/infomantis/mk/test/Doctrine-1.0.2/lib' );
			set_include_path( DOCTRINE_PATH.PATH_SEPARATOR.get_include_path() );
		}
		if( !defined( 'ZEND_PATH' ) ) {
			define( 'ZEND_PATH', 'C:/home/infomantis/mk/test/ZendFramework-1.6.0RC3/library' );
			set_include_path( ZEND_PATH.PATH_SEPARATOR.get_include_path() );
		}
		if( !defined( 'STATS_PATH' ) ) {
			define( 'STATS_PATH', 'C:/home/infomantis/mk/test/stats' );
			set_include_path( STATS_PATH.PATH_SEPARATOR.get_include_path() );
		}
		
		// Hauptdateien laden
		/*if( !class_exists( 'Zend_Loader' ) ) {
			include ZEND_PATH.'/Zend/Loader.php';
			spl_autoload_register( array( 'Zend_Loader', 'autoload' ) );
		}*/
		
		// Konfigurationen laden
		Stats::setConfigs( new Zend_Config_Ini( STATS_PATH.'/config.ini' ) );
		
		// Datenbankverbindung konfigurieren
		Doctrine_Manager::connection( Stats::configs()->connection->uri );
		
		// MVC-Layout initialisieren
		Zend_Layout::startMvc();
	}
?>