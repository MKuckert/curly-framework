<?php
	error_reporting(E_ALL);
	include "../Test.php";
	include "../../Core/Collection.php";
	include "../../Core/SerialCollection.php";
	
	class SerialCollectionTest extends Test {
		private $collection;
		public function testCreate() {
			$this->collection = new SerialCollection();
			$this->assertEqual( $this->collection->count(), 0, 'Collection hat bereits Werte' );
			$this->assertEqual( count( $this->collection ), 0, 'Collection hat bereits Werte' );
		}
		
		public function testAddValue() {
			$this->collection[] = 123;
			$this->assertEqual( $this->collection->count(), 1, 'Collection hat keinen Wert' );
			$this->assertEqual( count( $this->collection ), 1, 'Collection hat keinen Wert' );
			$this->assertEqual( $this->collection[0], 123, 'Wert ist fehlerhaft' );
			$this->assertEqual( $this->collection->offsetGet( 0 ), 123, 'Wert ist fehlerhaft' );
		}
		
		public function testSetValue() {
			$this->collection->offsetSet( 0, "asd" );
			$this->assertEqual( $this->collection->count(), 1, 'Collection hat keinen Wert' );
			$this->assertEqual( count( $this->collection ), 1, 'Collection hat keinen Wert' );
			$this->assertEqual( $this->collection[0], "asd", 'Wert ist fehlerhaft' );
			$this->assertEqual( $this->collection->offsetGet( 0 ), "asd", 'Wert ist fehlerhaft' );
		}
		
		public function testRemoveValue() {
			$this->collection->offsetUnset( 0 );
			$this->assertEqual( $this->collection->count(), 0, 'Collection hat keine Werte mehr' );
		}
		
		public function testMergeWith() {
			$arr = array( 1, 2, 3, 4 );
			$this->collection->mergeWith( $arr );
			$this->assertEqual( $this->collection->getArrayCopy(), $arr, 'Collectioninhalt nicht korrekt' );
		}
		
		public function testJoin() {
			$this->assertEqual(
				$this->collection->join(),
				implode( ', ', array( 1, 2, 3, 4 ) ),
				'Join ist fehlerhaft!'
			);
		}
		
		public function testClear() {
			$this->collection->clear();
			$this->assertEqual( $this->collection->count(), 0, 'Collection hat noch Werte' );
		}
		
		public function testInsertAt() {
			try {
				$this->collection->insertAt( 0, 'foo' );
			}
			catch( Exception $ex ) {
				$this->failure( 'Exception bei korrektem insertAt' );
			}
			
			try {
				$this->collection->insertAt( 123, 'foo' );
			}
			catch( Exception $ex ) {
				return;
			}
			$this->failure( 'Keine Exception bei fehlerhaftem insertAt' );
		}
		
		public function testRemoveAt() {
			try {
				$this->collection->removeAt( 0 );
			}
			catch( Exception $ex ) {
				$this->failure( 'Exception bei korrektem removeAt' );
			}
			
			try {
				$this->collection->removeAt( 123 );
			}
			catch( Exception $ex ) {
				return;
			}
			$this->failure( 'Keine Exception bei fehlerhaftem removeAt' );
		}
		
	}
	
	$test = new SerialCollectionTest();
	echo $test->run();
?>