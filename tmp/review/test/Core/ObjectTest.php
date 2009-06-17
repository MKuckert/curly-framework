<?php
	error_reporting(E_ALL);
	include "../Test.php";
	include "../../Core/Object.php";
	include "../../exceptions/MemberNotDefinedException.php";
	include "../../exceptions/MethodNotDefinedException.php";
	
	class DummyObject extends Object {}
	
	class ObjectTest extends Test {
		
		private $instance;
		
		protected function setUp() {
			$this->instance = new DummyObject();
		}
		
		public function testGetter() {
			try {
				$this->instance->DoesNotExists;
				$this->failure( 'Keine Exception wurde geworfen' );
			}
			catch( MemberNotDefinedException $ex ) {
				// success
			}
			catch( Exception $ex ) {
				$this->failure( 'Lediglich eine Exception wurde geworfen' );
			}
		}
		
		public function testSetter() {
			try {
				$this->instance->DoesNotExists = 123;
				$this->failure( 'Keine Exception wurde geworfen' );
			}
			catch( MemberNotDefinedException $ex ) {
				// success
			}
			catch( Exception $ex ) {
				$this->failure( 'Lediglich eine Exception wurde geworfen' );
			}
		}
		
		public function testCall() {
			try {
				$this->instance->DoesNotExists();
				$this->failure( 'Keine Exception wurde geworfen' );
			}
			catch( MethodNotDefinedException $ex ) {
				// success
			}
			catch( Exception $ex ) {
				$this->failure( 'Lediglich eine Exception wurde geworfen' );
			}
		}
		
		public function testEquals() {
			$this->assertTrue( $this->instance->equals( $this->instance ), 'Gleiches Objekt ist nicht gleich' );
			$this->assertFalse( $this->instance->equals( clone $this->instance ), 'Verschiedene Objekte sind gleich' );
		}
		
	}
	
	$test = new ObjectTest();
	echo $test->run();
?>