<?php
	error_reporting(E_ALL);
	include "../Test.php";
	include "../../Core/Object.php";
	include "../../exceptions/MethodNotDefinedException.php";
	include "../../autoload/IClassLoader.php";
	include "../../autoload/ClassLoader.php";
	include "../../autoload/ClassLoadDispatcher.php";
	include "../../autoload/IncludePathClassLoader.php";
	include "../../autoload/PathClassLoader.php";
	include "../../autoload/PearStyleClassLoader.php";
	
	class ClassLoadDispatcherTest extends Test {
		public function testPathClassLoader() {
			$loader = new PathClassLoader();
			$loader->addIncludePath( 'testDir/' );
			$loader->setBasePath( './' );
			
			$p = $loader->getPath( 'TestClass' );
			$this->assertEqual( $p, './testDir/TestClass.php', 'Falscher Pfad '.$p );
			
			$dispatcher = new ClassLoadDispatcher();
			$dispatcher->registerLoader( $loader );
			
			$dispatcher->load( 'TestClass' );
			
			$this->assertTrue( class_exists( 'TestClass' ), 'TestClass nicht geladen' );
		}
		public function testAutoLoad() {
			$loader = new IncludePathClassLoader();
			$loader->addIncludePath( './testDir/' );
			
			$dispatcher = new ClassLoadDispatcher();
			$dispatcher->registerLoader( $loader );
			
			$dispatcher->registerAutoloader();
			
			// class_exists löst Autoloader aus!
			$this->assertTrue( class_exists( 'TestClass2' ), 'TestClass2 nicht geladen' );
			
			$dispatcher->unregisterAutoloader();
		}
		public function testPearStyle() {
			$loader = new PearStyleClassLoader();
			$loader->setBasePath( './' );
			
			$dispatcher = new ClassLoadDispatcher();
			$dispatcher->registerLoader( $loader );
			$dispatcher->load( 'testDir_TestClass3' );
			
			$this->assertTrue( class_exists( 'testDir_TestClass3' ), 'TestClass3 nicht geladen' );
		}
	}
	
	$test = new ClassLoadDispatcherTest();
	echo $test->run();
?>