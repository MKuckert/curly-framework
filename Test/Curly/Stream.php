<?php

class Test_Curly_Stream extends Curly_Test {
	
	/**
	 * @var Curly_Stream Zu verwendende Stream-Instanz
	 */
	private $stream = null;
	
	/**
	 * @var string Testdatei
	 */
	private $testfile = 'testfile.txt';
	
	public function testMain() {
		try {
			$this->stream = new Curly_Stream( $this->testfile, 'w' );
			$this->assertTrue( $this->stream instanceof Curly_Stream, 'Kein Stream erstellt' );
			$this->stream->write( '0123456789' );
			
			$this->stream->setPosition( 0, Curly_Stream::OriginBegin );
			$this->assertEqual( $this->stream->getPosition(), 0, 'Position falsch gesetzt' );
			
			$this->stream = new Curly_Stream( $this->testfile, 'r+' );
			$read = $this->stream->read( 3 );
			$this->assertEqual( $read, '012', 'Falsche Daten gelesen 1 ('.$read.')' );
			
			$byte = $this->stream->readByte();
			$this->assertEqual( $byte, ord( '3' ), 'Byte falsch gelesen' );
			
			$this->stream->setPosition( -3, Curly_Stream::OriginCurrent );
			$this->assertEqual( $this->stream->getPosition(), 1, 'Position falsch gesetzt' );
			
			$this->stream->writeByte( ord( 'x' ) );
			$this->stream->setPosition( -10, Curly_Stream::OriginEnd );
			$this->assertEqual( $this->stream->getPosition(), 0, 'Position falsch gesetzt' );
			$read = $this->stream->read( 10 );
			$this->assertEqual( $read, '0x23456789', 'Falsche Daten gelesen 2 ('.$read.')' );
			
			$this->stream->truncate();
			$this->stream->resetPosition();
			$this->stream->writeLine( 'foo' );
			$this->stream->writeLine( 'bar' );
			$this->stream->writeLine( 'asd' );
			$this->stream->resetPosition();
			$read = $this->stream->readLine();
			$this->assertEqual( $read, 'foo', 'Falsche Daten gelesen 3 ('.$read.')' );
			
			$read = $this->stream->read();
			$this->assertEqual( $read, "bar".PHP_EOL."asd".PHP_EOL, 'Falsche Daten gelesen 4 ('.$read.')' );
			
			$this->stream->setNewline( 'NL' );
			$this->stream->setPosition( 10 );
			$this->stream->writeLine( '123' );
			$this->stream->writeLine( '456' );
			$this->stream->resetPosition();
			$read = $this->stream->read();
			$this->assertEqual( $read, "foo".PHP_EOL."bar".PHP_EOL."123NL456NL", 'Falsche Daten gelesen 5 ('.$read.')' );
			
			$this->assertFalse( $this->stream->getHasMore(), 'Datenstrom hat fehlerhafterweise noch Daten' );
			$this->stream->resetPosition();
			$this->assertTrue( $this->stream->getHasMore(), 'Datenstrom hat fehlerhafterweise keine Daten mehr' );
		}
		catch( Exception $ex ) {
			$this->failure( $ex->getMessage() );
		}
	}
	
	/*public function testLock() {
		try {
			$this->stream = new Curly_Stream( $this->testfile, 'w' );
			echo "trying to acquire lock\n";
			$this->stream->lock( Curly_Stream::LockExclusive );
			echo "aquired lock\n";
			$this->stream->write( 'Important data from '.microtime( true ) );
			sleep( 2 );
			echo "slept\n";
			$this->stream->unlock();
			echo "unlocked\n";
		}
		catch( Exception $ex ) {
			$this->failure( $ex->getMessage() );
		}
	}
	
	public function testLock2() {
		try {
			$id = uniqid( rand() );
			$this->stream = new Curly_Stream( $this->testfile, 'w' );
			echo "trying to acquire lock\n";
			if( $this->stream->lock( Curly_Stream::LockExclusive, false ) ) {
				echo "aquired lock\n";
				$this->stream->write( 'Important data from '.microtime( true ) );
				sleep( 2 );
				echo "slept\n";
				$this->stream->unlock();
				echo "unlocked\n";
			}
			else {
				echo "aquiring lock failed\n";
			}
		}
		catch( Exception $ex ) {
			$this->failure( $ex->getMessage() );
		}
	}*/
	
	public function testGZStream() {
		$data='This should be gzipped';
		$this->stream=new Curly_Stream_GZ($this->testfile, 'w');
		$this->stream->write($data);
		$this->stream->close();
		
		$this->stream=new Curly_Stream_GZ($this->testfile, 'r');
		$this->assertEqual($this->stream->read(), $data, 'Eingelesene Daten stimmen nicht mit Quelldaten überein');
	}
	
	/*public function testStdStreams() {
		Curly_Stream_Std::Out()->writeLine( 'Please insert something: ' );
		$in = Curly_Stream_Std::In()->readLine();
		Curly_Stream_Std::Out()->writeLine( 'You entered '.$in );
		Curly_Stream_Std::Err()->writeLine( 'This is an output to the error stream' );
	}*/
	
	public function testStringStream() {
		try {
			$this->stream = new Curly_Stream_String();
			$this->assertTrue( $this->stream instanceof Curly_Stream, 'Kein Stream erstellt' );
			$this->stream->write( '0123456789' );
			
			$this->stream->setPosition( 0, Curly_Stream::OriginBegin );
			$this->assertEqual( $this->stream->getPosition(), 0, 'Position falsch gesetzt' );
			
			$this->stream = new Curly_Stream_String( $this->stream->getHandle() );
			$read = $this->stream->read( 3 );
			$this->assertEqual( $read, '012', 'Falsche Daten gelesen 1 ('.$read.')' );
			
			$byte = $this->stream->readByte();
			$this->assertEqual( $byte, ord( '3' ), 'Byte falsch gelesen' );
			
			$this->stream->setPosition( -3, Curly_Stream::OriginCurrent );
			$this->assertEqual( $this->stream->getPosition(), 1, 'Position falsch gesetzt' );
			
			$this->stream->writeByte( ord( 'x' ) );
			$this->stream->setPosition( -10, Curly_Stream::OriginEnd );
			$this->assertEqual( $this->stream->getPosition(), 0, 'Position falsch gesetzt' );
			$read = $this->stream->read( 10 );
			$this->assertEqual( $read, '0x23456789', 'Falsche Daten gelesen 2 ('.$read.')' );
			
			$this->stream->truncate();
			$this->stream->resetPosition();
			$this->stream->writeLine( 'foo' );
			$this->stream->writeLine( 'bar' );
			$this->stream->writeLine( 'asd' );
			$this->stream->resetPosition();
			$read = $this->stream->readLine();
			$this->assertEqual( $read, 'foo', 'Falsche Daten gelesen 3 ('.$read.')' );
			
			$read = $this->stream->read();
			$this->assertEqual( $read, "bar".PHP_EOL."asd".PHP_EOL, 'Falsche Daten gelesen 4 ('.$read.')' );
			
			$this->stream->setNewline( 'NL' );
			$this->stream->setPosition( 10 );
			$this->stream->writeLine( '123' );
			$this->stream->writeLine( '456' );
			$this->stream->resetPosition();
			$read = $this->stream->read();
			$this->assertEqual( $read, "foo".PHP_EOL."bar".PHP_EOL."123NL456NL", 'Falsche Daten gelesen 5 ('.$read.')' );
			
			$this->assertFalse( $this->stream->getHasMore(), 'Datenstrom hat fehlerhafterweise noch Daten' );
			$this->stream->resetPosition();
			$this->assertTrue( $this->stream->getHasMore(), 'Datenstrom hat fehlerhafterweise keine Daten mehr' );
		}
		catch( Exception $ex ) {
			$this->failure( $ex->getMessage() );
		}
	}
	
}