<?php
	
	include "../Stream.php";
	include "../FileMode.php";
	include "../FileAccess.php";
	include "../FileStream.php";
	include "../Path.php";
	include "../File.php";
	
	class FileStreamTest extends Test {
		
		public function testOpenRead() {
			$stream = new FileStream( 'testfile.txt', FileMode::Open, FileAccess::Read );
			$this->assertEqual( $stream->readByte(), ord( 'T' ), 'Byte nicht korrekt gelesen!' );
			$buffer = '';
			$this->assertEqual( $stream->read( $buffer, 10 ), 8, 'Keine 8 Byte gelesen!' );
			$this->assertEqual( $buffer, 'estFile!', 'Datei nicht korrekt gelesen!' );
			$this->assertEqual( $stream->read( $buffer, 10 ), 0, 'Keine 0 Byte gelesen!' );
			$this->assertEqual( $buffer, '', 'Datei nicht korrekt gelesen!' );
			$stream->close();
		}
		
		public function testOpenWrite() {
			$stream = new FileStream( 'testfile.txt', FileMode::Append, FileAccess::Write );
			$stream->write( 'ASD' );
			$stream->close();
		}
		
	}
	
?>