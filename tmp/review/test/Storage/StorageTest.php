<?php
	error_reporting( E_ALL );
	require "../Test.php";
	require "../../Stream/Stream.php";
	require "../../Stream/IOException.php";
	require "../../exceptions/NotFoundException.php";
	require "../../Storage/IStorage.php";
	require "../../Storage/Storage.php";
	require "../../Storage/ArrayAdapterStorage.php";
	require "../../Storage/XmlStorage.php";
	require "../../XML/XML.php";
	require "../../Core/Ensure.php";
	
	class StorageTest extends Test {
		public function testArrayAdapterStorage() {
			$storage = new ArrayAdapterStorage();
			$storage->setValue( 'foo', 'bar' );
			$this->assertEqual( $storage->getValue( 'foo' ), 'bar', 'Falschen Wert ausgelesen' );
			$storage->removeValue( 'foo' );
			try {
				$storage->getValue( 'foo' );
				$this->failure( 'Wert noch vorhanden' );
			}
			catch( Exception $ex ) {
				// Alles OK
			}
		}
		public function testXmlStorage() {
			$storage = new XmlStorage();
			$storage->setFile( 'storage.xml' );
			/*$storage->setValue( 'foo', 'bar' );
			$storage->setIndent( true );
			$storage->store();*/
			
			$storage->load();
			$this->assertEqual( $storage->getValue( 'foo' ), 'bar', 'Falschen Wert ausgelesen' );
		}
	}
	
	$test = new StorageTest();
	echo $test->run();
	
?>