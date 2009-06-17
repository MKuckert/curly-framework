<?php
	error_reporting( E_ALL );
	require "../Test.php";
	require "../../Stream/Stream.php";
	require "../../Stream/Streams.php";
	require "../../Stream/IOException.php";
	
	class StreamTest extends Test {
		private $stream = null;
		private $testfile = 'testfile.txt';
		public function testTest() {
			try {
				$this->stream = new Stream( $this->testfile, 'w' );
				$this->assertTrue( $this->stream instanceof Stream, 'Kein Stream erstellt' );
				$this->stream->write( '0123456789' );
				
				$this->stream->setPosition( 0, Stream::OriginBegin );
				$this->assertEqual( $this->stream->getPosition(), 0, 'Position falsch gesetzt' );
				
				$this->stream = new Stream( $this->testfile, 'r+' );
				$read = $this->stream->read( 3 );
				$this->assertEqual( $read, '012', 'Falsche Daten gelesen ('.$read.')' );
				
				$byte = $this->stream->readByte();
				$this->assertEqual( $byte, ord( '3' ), 'Byte falsch gelesen' );
				
				$this->stream->setPosition( -3, Stream::OriginCurrent );
				$this->assertEqual( $this->stream->getPosition(), 1, 'Position falsch gesetzt' );
				
				$this->stream->writeByte( ord( 'x' ) );
				$this->stream->setPosition( -10, Stream::OriginEnd );
				$this->assertEqual( $this->stream->getPosition(), 0, 'Position falsch gesetzt' );
				$read = $this->stream->read( 10 );
				$this->assertEqual( $read, '0x23456789', 'Falsche Daten gelesen ('.$read.')' );
				
				$this->stream->truncate();
				$this->stream->resetPosition();
				$this->stream->writeLine( 'foo' );
				$this->stream->writeLine( 'bar' );
				$this->stream->writeLine( 'asd' );
				$this->stream->resetPosition();
				$read = $this->stream->readLine();
				$this->assertEqual( $read, 'foo', 'Falsche Daten gelesen ('.$read.')' );
				
				$read = $this->stream->read();
				$this->assertEqual( $read, "bar\nasd\n", 'Falsche Daten gelesen ('.$read.')' );
				
				$this->stream->setNewline( 'NL' );
				$this->stream->setPosition( 10 );
				$this->stream->writeLine( '123' );
				$this->stream->writeLine( '456' );
				$this->stream->resetPosition();
				$read = $this->stream->read();
				$this->assertEqual( $read, "foo\nbar\nas123NL456NL", 'Falsche Daten gelesen ('.$read.')' );
				
				$this->assertFalse( $this->stream->getHasMore(), 'Datenstrom hat fehlerhafterweise noch Daten' );
				$this->stream->resetPosition();
				$this->assertTrue( $this->stream->getHasMore(), 'Datenstrom hat fehlerhafterweise keine Daten mehr' );
			}
			catch( Exception $ex ) {
				$this->failure( $ex->getMessage() );
			}
		}
		
		public function testLock() {
			try {
				$this->stream = new Stream( $this->testfile, 'w' );
				echo "trying to acquire lock\n";
				$this->stream->lock( Stream::LockExclusive );
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
				$this->stream = new Stream( $this->testfile, 'w' );
				echo "trying to acquire lock\n";
				if( $this->stream->lock( Stream::LockExclusive, false ) ) {
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
		}
		
		public function testStdStreams() {
			Streams::Out()->writeLine( 'Please insert something: ' );
			$in = Streams::In()->readLine();
			Streams::Out()->writeLine( 'You entered '.$in );
			Streams::Err()->writeLine( 'This is an output to the error stream' );
		}
		
	}
	
	$test = new StreamTest();
	echo $test->run();
	
?>