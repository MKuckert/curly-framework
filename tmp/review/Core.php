<?php
	
	class Core {
		
		/**
		 * @var RecursiveClassLoader
		 */
		static private $_libraryClassLoader = null;
		static private $_basePath = '';
		/**
		 * @var ClassLoadDispatcher
		 */
		static private $_libraryClassLoadDispatcher = null;
		
		static public function getLibraryClassLoader() {
			return( self::$_libraryClassLoader );
		}
		
		static public function initialize() {
			self::$_basePath = dirname( __FILE__ );
			
			self::includeRequiredFiles();
			
			self::$_libraryClassLoader = new RecursiveClassLoader();
			self::$_libraryClassLoader->setBasePath( self::$_basePath );
			
			self::$_libraryClassLoadDispatcher = new ClassLoadDispatcher();
			self::$_libraryClassLoadDispatcher->registerLoader( self::$_libraryClassLoader );
			self::$_libraryClassLoadDispatcher->registerAutoloader();
		}
		
		static public function includeRequiredFiles() {
			require_once self::$_basePath.'/Core/Object.php';
			require_once self::$_basePath.'/autoload/IClassLoader.php';
			require_once self::$_basePath.'/autoload/ClassLoader.php';
			require_once self::$_basePath.'/autoload/RecursiveClassLoader.php';
			require_once self::$_basePath.'/autoload/ClassLoadDispatcher.php';
		}
		
	}
	
?>