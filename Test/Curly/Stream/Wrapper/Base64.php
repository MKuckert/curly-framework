<?php

class Test_Curly_Stream_Wrapper_Base64 extends Curly_Test {
	
	public function testRegisterStream() {
		$this->assertTrue(Curly_Stream_Wrapper_Base64::register('base64'), 'Stream nicht registriert');
	}
	
	public function testRead_simple() {
		$valid='0123456789';
		$stream='base64://'.base64_encode($valid);
		
		$handle=fopen($stream, 'r');
		
		while(!feof($handle)) {
			$this->assertEqual(fread($handle, 1), substr($valid, 0, 1), 'Falsche Daten gelesen');
			$valid=substr($valid, 1);
		}
	}
	
	public function testRead_filegetcontents() {
		$valid='0123456789';
		$stream='base64://'.base64_encode($valid);
		
		$contents=file_get_contents($stream);
		$this->assertEqual($valid, $contents, 'Falsche Daten gelesen');
	}
	
	public function testRead_seek() {
		$valid='0123456789';
		$stream='base64://'.base64_encode($valid);
		
		$handle=fopen($stream, 'r');
		
		while(!feof($handle)) {
			$this->assertEqual(fread($handle, 1), substr($valid, 0, 1), 'Falsche Daten gelesen');
			$valid=substr($valid, 1);
		}
		
		fseek($handle, 0, SEEK_SET);
		
		$valid='0123456789';
		while(!feof($handle)) {
			$this->assertEqual(fread($handle, 1), substr($valid, 0, 1), 'Falsche Daten gelesen');
			$valid=substr($valid, 1);
		}
	}
	
	public function testRead_write() {
		$valid='0123456789';
		$stream='base64://';
		
		$handle=fopen($stream, 'w+');
		
		$this->assertEqual(fwrite($handle, $valid), strlen($valid), 'Falsche Daten geschrieben');
		
		fseek($handle, 0, SEEK_SET);
		
		$this->assertEqual(stream_get_contents($handle), $valid, 'Falsche Daten gelesen');
		
		fseek($handle, 3, SEEK_SET);
		
		$this->assertEqual(fwrite($handle, 'abc'), 3, 'Daten falsch eingefügt');
		
		fseek($handle, 0, SEEK_SET);
		
		$this->assertEqual(stream_get_contents($handle), '012abc6789', 'Falsche Daten nach einfügen gelesen');
	}
	
}
