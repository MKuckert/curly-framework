<?php
	error_reporting(E_ALL);
	include "../Test.php";
	include "../../Core/Collection.php";
	include "../../Core/Ensure.php";
	
	class CollectionTest extends Test {
		private $collection;
		public function testCreate() {
			$this->collection = new Collection();
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
			$this->assertEqual( $this->collection->toArray(), $arr, 'Collectioninhalt nicht korrekt' );
		}
		
		public function testJoin() {
			$this->assertEqual(
				$this->collection->join(),
				implode( ', ', array( 1, 2, 3, 4 ) ),
				'Join ist fehlerhaft!'
			);
		}
		
		public function testJoinComplex() {
			$assert = array(
				array( 1, 'foo' ),
				array( 'asd',
					array( 1, 2, 3 )
				)
			);
			$impl = '1, foo, asd, 1, 2, 3';
			
			$this->collection->clear();
			$this->collection->mergeWith( $assert );
			
			$this->assertEqual( $this->collection->join(), $impl, 'Join ist fehlerhaft!' );
		}
		
		public function testClear() {
			$this->collection->clear();
			$this->assertEqual( $this->collection->count(), 0, 'Collection hat noch Werte' );
		}
		
	}
	
	$test = new CollectionTest();
	echo $test->run();
?>