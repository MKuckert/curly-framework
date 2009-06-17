<?php
error_reporting(E_ALL);
include "../Test.php";
include "../../Core/String.php";
include "../../Core/StringIterator.php";

class DummyObj1 {
	public $value;
	public function __construct($value) {
		$this->value=$value;
	}
	public function toString() {
		return((string)$this->value);
	}
}

class DummyObj2 {
	public $value;
	public function __construct($value) {
		$this->value=$value;
	}
	public function __toString() {
		return((string)$this->value);
	}
}

class DummyIterator extends StringIterator {}

class StringTest extends Test {

	private $instance;
	
	protected function setUp() {
		$this->instance=new String();
	}
	
	private function _checkValue($val) {
		$val=$this->instance->ensure($val);
		foreach(array('getValue', 'toString', '__toString') as $method) {
			$this->assertEqual($this->instance->$method(), $val, $method.' für Wert '.$val.' ist fehlerhaft');
		}
		$this->assertEqual((string)$this->instance, $val, 'Magic toString ist fehlerhaft');
	}
	
	public function testEnsure() {
		$values=array(
			array(true, 'TRUE'),
			array(false, 'FALSE'),
			array(1, '1'),
			array(-1, '-1'),
			array(1.2345678, '1.2345678'),
			array(1E123, '1.0E+123'),
			array('foo', 'foo'),
			array(array(1,2,3), '123'),
			array(array(array(1,2,3), array(4,5,6), array(7,8,9)), '123456789'),
			array(new DummyObj1('FromObj1'), 'FromObj1'),
			array(new DummyObj2('FromObj2'), 'FromObj2'),
			array(STDIN, 'stream')
		);
		
		foreach($values as $value) {
			$in=$value[0];
			$out=$value[1];
			$ret=$this->instance->ensure($in);
			$this->assertEqual($ret, $out, 'ensure für '.print_r($in, true).' ist fehlerhaft');
		}
	}
	
	public function testGetValue() {
		$this->_checkValue('');
	}
	
	public function testSetValue() {
		foreach(array('fooBar', 123, array(1,2,3,4)) as $val) {
			$this->assertEqual($this->instance->setValue($val), $this->instance, 'setValue gibt falsches Objekt zurück');
			$this->_checkValue($val);
		}
	}
	
	public function testLength() {
		$this->instance->setValue('123456789');
		$this->assertEqual($this->instance->length(), 9, 'Length ist fehlerhaft');
		$this->assertEqual($this->instance->length(), $this->instance->count(), 'Count als Methode ist fehlerhaft');
		$this->assertEqual($this->instance->length(), count($this->instance), 'Count als Funktion ist fehlerhaft');
	}
	
	public function testAppend() {
		$this->instance->setValue('Foo');
		$this->instance->append('Bar');
		$this->assertEqual($this->instance->getValue(), 'FooBar', 'Append ist fehlerhaft');
	}
	
	public function testPrepend() {
		$this->instance->setValue('Foo');
		$this->instance->prepend('Bar');
		$this->assertEqual($this->instance->getValue(), 'BarFoo', 'Prepend ist fehlerhaft');
	}
	
	public function testInsertAt() {
		$this->instance->setValue('Foo');
		$this->instance->insertAt(1, 'Bar');
		$this->assertEqual($this->instance->getValue(), 'FBaroo', 'InsertAt ist fehlerhaft');
		
		$this->instance->setValue('Foo');
		$this->instance->insertAt(3, 'Bar');
		$this->assertEqual($this->instance->getValue(), 'FooBar', 'InsertAt mit Append ist fehlerhaft');
		
		$this->instance->setValue('Foo');
		$this->instance->insertAt(0, 'Bar');
		$this->assertEqual($this->instance->getValue(), 'BarFoo', 'InsertAt mit Prepend ist fehlerhaft');
	}
	
	public function testTrim() {
		$this->assertEqual($this->instance->setValue('  foo    ')->trim(), 'foo', 'trim ist fehlerhaft');
		$this->assertEqual($this->instance->setValue("\n\r\tfoo\n\r\t ")->trim(), 'foo', 'trim ist fehlerhaft');
	}
	
	public function testRTrim() {
		$this->assertEqual($this->instance->setValue('  foo    ')->rtrim(), '  foo', 'rtrim ist fehlerhaft');
		$this->assertEqual($this->instance->setValue("\n\r\tfoo\n\r\t ")->rtrim(), "\n\r\tfoo", 'rtrim ist fehlerhaft');
	}
	
	public function testLTrim() {
		$this->assertEqual($this->instance->setValue('  foo    ')->ltrim(), 'foo    ', 'ltrim ist fehlerhaft');
		$this->assertEqual($this->instance->setValue("\n\r\tfoo\n\r\t ")->ltrim(), "foo\n\r\t ", 'ltrim ist fehlerhaft');
	}
	
	public function testReplace() {
		$this->assertEqual($this->instance->setValue('ABCABCABC')->replace('ABC', 'FOO', $count), $this->instance, 'replace gibt falsches Objekt zurück');
		$this->assertEqual($this->instance->getValue(), 'FOOFOOFOO', 'replace ist fehlerhaft');
		$this->assertEqual($count, 3, 'replace $count ist fehlerhaft');
	}
	
	public function testOffsetGet() {
		$this->instance->setValue('0123456789');
		
		for($i=0; $i<10; $i++) {
			$this->assertEqual($this->instance[$i], (string)$i, 'Index '.$i.' ist fehlerhaft');
		}
		
		$this->assertEqual($this->instance['5th Index'], '5', 'Stringindex ist fehlerhaft');
		
		try {
			$unused=$this->instance[-1];
			$this->failure('Keine Exception für Index -1');
		}
		catch(OutOfBoundsException $ex) {
			// Korrekt
		}
		
		try {
			$unused=$this->instance[10];
			$this->failure('Keine Exception für zu großen Index');
		}
		catch(OutOfBoundsException $ex) {
			// Korrekt
		}
	}
	
	public function testOffsetSet() {
		$this->instance->setValue('0123456789');
		
		$this->instance[3]='!';
		$this->assertEqual($this->instance->getValue(), '012!456789', 'Einfaches offsetSet ist fehlerhaft');
		
		$this->instance[3]='...';
		$this->assertEqual($this->instance->getValue(), '012...6789', 'offsetSet mit replace ist fehlerhaft');
		
		$this->instance[12]='!';
		$this->assertEqual($this->instance->getValue(), '012...6789  !', 'offsetSet mit append ist fehlerhaft');
		
		$this->instance->setValue('0123456789');
		$this->instance[8]='...';
		$this->assertEqual($this->instance->getValue(), '01234567...', 'offsetSet mit append/replace ist fehlerhaft');
		
		$this->instance->setValue('0123456789');
		try {
			$this->instance[-1]='WRONG!';
			$this->failure('Keine Exception für negativen Index');
		}
		catch(OutOfBoundsException $ex) {
			// Korrekt
		}
		
		$this->instance[]='APPEND';
		$this->assertEqual($this->instance->getValue(), '0123456789APPEND', 'offsetSet mit append ist fehlerhaft');
	}
	
	public function testOffsetUnset() {
		$this->instance->setValue('0123456789');
		unset($this->instance[5]);
		$this->assertEqual($this->instance->getValue(), '01234 6789', 'Wert nicht korrekt entfernt');
		
		try {
			unset($this->instance[-1]);
		}
		catch(Exception $ex) {
			$this->failure('Exception für Index -1');
		}
		
		try {
			unset($this->instance[10]);
		}
		catch(Exception $ex) {
			$this->failure('Exception für zu großen Index');
		}
	}
	
	public function testOffsetExists() {
		$this->instance->setValue('0123456789');
		
		for($i=0; $i<10; $i++) {
			$this->assertTrue(isset($this->instance[$i]), 'Index '.$i.' nicht gefunden');
		}
		
		$this->assertFalse(isset($this->instance[-1]), 'Index -1 gefunden');
		$this->assertFalse(isset($this->instance[10]), 'Index 10 gefunden');
		
		$this->assertTrue(isset($this->instance['4th Index']), 'Index 4 als String nicht gefunden');
	}
	
	public function testIterator() {
		$values=array(0,1,2,3,4,5,6,7,8,9);
		$this->instance->setValue($values);
		
		foreach($this->instance as $key=>$value) {
			$this->assertEqual((string)$key, $value, 'Wert zu Index '.$key.' ist fehlerhaft');
			unset($values[$key]);
		}
		
		$this->assertTrue(count($values)==0, 'Nicht über alle Elemente iteriert');
	}
	
	public function testEquals() {
		$this->instance->setIgnoreCase(false);
		$value='FooBar';
		
		$this->instance->setValue($value);
		$instance2=new String($value);
		
		$this->assertTrue($this->instance->equals($instance2), 'Equals ist fehlerhaft (1)');
		
		$instance2->setValue('Bar');
		$this->assertFalse($this->instance->equals($instance2), 'Equals ist fehlerhaft (2)');
		
		$instance2->setValue('foobar');
		$this->instance->setIgnoreCase(true);
		$this->assertTrue($this->instance->equals($instance2), 'Equals ist fehlerhaft (3)');
		$this->assertFalse($instance2->equals($this->instance), 'Equals ist fehlerhaft (4)');
	}
	
	public function testCompareTo() {
		$this->instance->setIgnoreCase(false);
		$value='FooBar';
		
		$this->instance->setValue($value);
		$instance2=new String($value);
		
		$this->assertEqual($this->instance->compareTo($instance2), 0, 'CompareTo ist fehlerhaft (1)');
		
		$instance2->setValue('BBB');
		$this->assertHigher($this->instance->compareTo($instance2), 0, 'CompareTo ist fehlerhaft (2)');
		
		$instance2->setValue('XXX');
		$this->assertLower($this->instance->compareTo($instance2), 0, 'CompareTo ist fehlerhaft (3)');
		
		$instance2->setValue('fooBAR');
		$this->instance->setIgnoreCase(true);
		$this->assertEqual($this->instance->compareTo($instance2), 0, 'CompareTo ist fehlerhaft (4)');
		$this->assertHigher($instance2->compareTo($this->instance), 0, 'CompareTo ist fehlerhaft (5)');
	}
	
	public function testRevert() {
		$this->instance->setValue('0123456789');
		$this->instance->revert();
		$this->assertEqual($this->instance->getValue(), '9876543210', 'Revert ist fehlerhaft');
	}
	
	public function testClear() {
		$this->instance->setValue('0123');
		$this->instance->clear();
		$this->assertEqual($this->instance->getValue(), '', 'Clear ist fehlerhaft');
	}
	
	public function testDuplicate() {
		$this->instance->setIgnoreCase(true);
		$this->instance->setIteratorClass('DummyIterator');
		$clone=$this->instance->duplicate();
		
		$this->assertNotStrictEqual($clone, $this->instance, 'Instanz nicht verschieden');
		$this->assertEqual($clone->getValue(), $this->instance->getValue(), 'Value verschieden');
		$this->assertEqual($clone->getIgnoreCase(), $this->instance->getIgnoreCase(), 'IgnoreCase verschieden');
		$this->assertEqual($clone->getIteratorClass(), $this->instance->getIteratorClass(), 'IteratorClass verschieden');
		
		$this->instance->setIteratorClass(String::DEFAULT_ITERATOR);
	}
	
	public function testEndsOrStartsWithOrIncludes() {
		$this->instance->setValue('0123456789');
		
		$this->assertTrue($this->instance->endsWith('789'), 'EndsWith ist fehlerhaft (1)');
		$this->assertTrue($this->instance->endsWith('0123456789'), 'EndsWith ist fehlerhaft (1)');
		$this->assertTrue($this->instance->startsWith('0123'), 'StartsWith ist fehlerhaft (1)');
		$this->assertTrue($this->instance->startsWith('0123456789'), 'StartsWith ist fehlerhaft (2)');
		$this->assertTrue($this->instance->includes('456'), 'Includes ist fehlerhaft(1)');
		$this->assertTrue($this->instance->includes('0123'), 'Includes ist fehlerhaft(2)');
		$this->assertTrue($this->instance->includes('789'), 'Includes ist fehlerhaft(3)');
		
		$this->assertFalse($this->instance->endsWith('01234567890123456789'), 'EndsWith ist fehlerhaft (3)');
		$this->assertFalse($this->instance->startsWith('01234567890123456789'), 'StartsWith ist fehlerhaft (3)');
		$this->assertFalse($this->instance->includes('01234567890'), 'Includes ist fehlerhaft(4)');
	}
	
	public function testSplit() {
		$this->instance->setValue('xxxaxxxbxxxcxxx');
		
		$split=$this->instance->split('[a-c]');
		$this->assertEqual($split, array_fill(0, 4, 'xxx'), 'Split ist fehlerhaft');
	}
	
	public function testLowerUpperConvert() {
		$this->instance->setValue('hello world');
		$this->assertEqual($this->instance->firstToUpperCase()->getValue(), 'Hello world', 'firstToUpperCase ist fehlerhaft');
		
		$this->instance->setValue('Hello World');
		$this->assertEqual($this->instance->firstToLowerCase()->getValue(), 'hello World', 'firstToLowerCase ist fehlerhaft');
		
		$this->instance->setValue('hello world');
		$this->assertEqual($this->instance->wordsToUpperCase()->getValue(), 'Hello World', 'wordsToUpperCase ist fehlerhaft');
		
		$this->instance->setValue('Hello World');
		$this->assertEqual($this->instance->wordsToLowerCase()->getValue(), 'hello world', 'wordsToLowerCase ist fehlerhaft');
	}
	
}

$test=new StringTest();
echo $test->run();