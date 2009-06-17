<?php

class Test_Curly_Types_Collection extends Curly_Test {
	private $collection;
	public function testCreate() {
		$this->collection = new Curly_Types_Collection();
		$this->assertEqual( $this->collection->count(), 0, 'Curly_Types_Collection hat bereits Werte' );
		$this->assertEqual( count( $this->collection ), 0, 'Curly_Types_Collection hat bereits Werte' );
	}
	
	public function testAddValue() {
		$this->collection[] = 123;
		$this->assertEqual( $this->collection->count(), 1, 'Curly_Types_Collection hat keinen Wert' );
		$this->assertEqual( count( $this->collection ), 1, 'Curly_Types_Collection hat keinen Wert' );
		$this->assertEqual( $this->collection[0], 123, 'Wert ist fehlerhaft' );
		$this->assertEqual( $this->collection->offsetGet( 0 ), 123, 'Wert ist fehlerhaft' );
	}
	
	public function testSetValue() {
		$this->collection->offsetSet( 0, "asd" );
		$this->assertEqual( $this->collection->count(), 1, 'Curly_Types_Collection hat keinen Wert' );
		$this->assertEqual( count( $this->collection ), 1, 'Curly_Types_Collection hat keinen Wert' );
		$this->assertEqual( $this->collection[0], "asd", 'Wert ist fehlerhaft' );
		$this->assertEqual( $this->collection->offsetGet( 0 ), "asd", 'Wert ist fehlerhaft' );
	}
	
	public function testRemoveValue() {
		$this->collection->offsetUnset( 0 );
		$this->assertEqual( $this->collection->count(), 0, 'Curly_Types_Collection hat keine Werte mehr' );
	}
	
	public function testMergeWith() {
		$arr = array( 1, 2, 3, 4 );
		$this->collection->mergeWith( $arr );
		$this->assertEqual( $this->collection->toArray(), $arr, 'Curly_Types_Collectioninhalt nicht korrekt' );
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
		$this->assertEqual( $this->collection->count(), 0, 'Curly_Types_Collection hat noch Werte' );
	}
	
}