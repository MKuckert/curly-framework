<?php
	error_reporting(E_ALL);
	include "../Test.php";
	include "../../autoload/ClassLoadDispatcher.php";
	include "../../core/Object.php";
	include "../../autoload/IClassLoader.php";
	include "../../autoload/ClassLoader.php";
	include "../../autoload/RecursiveClassLoader.php";
	
	$dispatcher = new ClassLoadDispatcher();
	$loader = new RecursiveClassLoader();
	$loader->setBasePath( '../../' );
	$dispatcher->registerLoader( $loader );
	$dispatcher->registerAutoloader();
	
	class EPDOTest extends Test {
		
		private $dsn = 'mysql:host=localhost;dbname=test';
		private $username = 'root';
		private $password = '';
		/**
		 * @var EPDO
		 */
		private $epdo = null;
		
		public function testConnect() {
			$this->epdo = new EPDO( $this->dsn, $this->username, $this->password );
			$this->epdo->setCharset( 'utf8' );
			$this->epdo->connect();
		}
		
		public function testReconnect() {
			$this->epdo->disconnect();
			$this->epdo->connect();
		}
		
		public function testSimplePrepare() {
			$query = 'select count(*) as c from test';
			
			$statement = $this->epdo->prepare( $query );
			$statement->execute();
			
			$this->assertEqual( $statement->fetchColumn( 0 ), 7, 'Falsche Zeilenzahl erhalten' );
		}
		
		public function testStaticParameter() {
			$query = 'select $rows$ from $table$';
			$statics = array(
				'rows'	=> 'id, name, value',
				'table'	=> 'test'
			);
			
			$statement = $this->epdo->prepare( $query, $statics );
			$statement->execute();
			
			$this->assertEqual( count( $statement->fetchAll() ), 7, 'Falsche Zeilenzahl erhalten' );
		}
		
	}
	
	$test = new EPDOTest();
	echo $test->run();
?>