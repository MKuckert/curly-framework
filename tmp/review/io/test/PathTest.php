<?php
	
	include "../Path.php";
	
	class PathTest extends Test {
		/*public function testChangeExtension() {
			$tests = array(
				array( 'path/to/file.php', '.gif', 'path/to/file.gif' ),
				array( 'path/to/.php', '.gif', 'path/to/.php' )
			);
			
			foreach( $tests as $test ) {
				$new = Path::changeExtension( $test[0], $test[1] );
				$this->assertEqual( $new, $test[2], $test[2].' expected, '.$new.' given!' );
			}
			
		}*/
		
		public function testisPathRooted() {
			$tests = array(
				array( 'path/to/file.php', false ),
				array( '../path/to/file.php', false ),
				array( './path/to/file.php', false ),
				array( '/path/to/file.php', true ),
				array( '\\path\\to\\file.php', true ),
				array( '\\\\path\\to\\file.php', true ),
				array( 'C:\\path\\to\\file.php', true ),
				array( 'C:/path/to/file.php', true ),
				array( 'C:path/to/file.php', false )
			);
			
			foreach( $tests as $test ) {
				$new = Path::isPathRooted( $test[0] );
				$this->assertEqual( $new, $test[1], $test[1].' expected, '.$new.' given!' );
			}
		}
		
		public function testgetFileName() {
			$tests = array(
				array( 'path/to/file.php', 'file.php' ),
				array( '\\\\path\\to\\file.php', 'file.php' ),
				array( 'path/to/', '' ),
				array( '\\\\path\\to\\', '' )
			);
			
			foreach( $tests as $test ) {
				$new = Path::getFileName( $test[0] );
				$this->assertEqual( $new, $test[1], $test[1].' expected, '.$new.' given!' );
			}
		}
		
		public function testgetFullPath() {
			$tests = array(
				array( 'path/to/file.php', 'C:/dev/php-dev/io/test/path/to/file.php' ),
				array( 'C:/path/to/file.php', 'C:/path/to/file.php' ),
				array( '/path/to/file.php', '/path/to/file.php' ),
				array( '//path/to/file.php', '//path/to/file.php' ),
				array( 'path/../to/file.php', 'C:/dev/php-dev/io/test/to/file.php' ),
				array( 'path\\..\\to\\file.php', 'C:/dev/php-dev/io/test/to/file.php' ),
				array( 'path///to/file.php', 'C:/dev/php-dev/io/test/path/to/file.php' ),
			);
			
			foreach( $tests as $test ) {
				$new = Path::getFullPath( $test[0] );
				$this->assertEqual( $new, $test[1], $test[1].' expected, '.$new.' given!' );
			}
		}
		
		public function testgetPathRoot() {
			$tests = array(
				array( 'path/to/file.php', 'C:/dev/php-dev/io/test/path/to/file.php' ),
				array( 'C:/path/to/file.php', 'C:/path/to/file.php' ),
				array( '/path/to/file.php', '/path/to/file.php' ),
				array( '//path/to/file.php', '//path/to/file.php' ),
				array( 'path/../to/file.php', 'C:/dev/php-dev/io/test/to/file.php' ),
				array( 'path\\..\\to\\file.php', 'C:/dev/php-dev/io/test/to/file.php' ),
				array( 'path///to/file.php', 'C:/dev/php-dev/io/test/path/to/file.php' ),
			);
			
			foreach( $tests as $test ) {
				$new = Path::getFullPath( $test[0] );
				$this->assertEqual( $new, $test[1], $test[1].' expected, '.$new.' given!' );
			}
		}
		
		/*public function testgetTempFileName() {
			$tempname = Path::getTempFileName();
			$handle = fopen( $tempname, 'w' );
			fwrite( $handle, 'asd' );
			fclose( $handle );
			echo $tempname;
		}*/
		
		public function testhasExtension() {
			$tests = array(
				array( 'path/to/file.php', true ),
				array( 'path\\..\\to\\file.php', true ),
				array( 'path///to/file', false ),
				array( 'path///to/.htaccess', false ),
			);
			
			foreach( $tests as $test ) {
				$new = Path::hasExtension( $test[0] );
				$this->assertEqual( $new, $test[1], $test[1].' expected, '.$new.' given!' );
			}
		}
		
		public function testgetExtension() {
			$tests = array(
				array( 'path/to/file.php', 'php' ),
				array( 'path\\..\\to\\file.gif', 'gif' ),
				array( 'path///to/file', '' ),
				array( 'path///to/.htaccess', '' ),
				array( 'path\\..\\to\\file.class.inc', 'inc' ),
				array( 'path\\..\\to\\any.', '' ),
			);
			
			foreach( $tests as $test ) {
				$new = Path::getExtension( $test[0] );
				$this->assertEqual( $new, $test[1], $test[1].' expected, '.$new.' given!' );
			}
		}
		
		public function testchangeExtension() {
			$tests = array(
				array( 'path/to/file.php', '.gif', 'path/to/file.gif' ),
				array( 'path/to/.php', '.gif', 'path/to/.gif' ),
				array( 'path/to/file', '.gif', 'path/to/file.gif' )
			);
			
			foreach( $tests as $test ) {
				$new = Path::changeExtension( $test[0], $test[1] );
				$this->assertEqual( $new, $test[2], $test[2].' expected, '.$new.' given!' );
			}
		}
		
		public function testcombine() {
			$tests = array(
				array( 'path/to', 'file.php', 'path/to/file.php' ),
				array( 'path/to', '/absolute/path/file.php', '/absolute/path/file.php' ),
				array( 'path/to/', 'file.php', 'path/to/file.php' ),
				array( 'path/to', '*.php', 'path/to/*.php' ),
			);
			
			foreach( $tests as $test ) {
				$new = Path::combine( $test[0], $test[1] );
				$this->assertEqual( $new, $test[2], $test[2].' expected, '.$new.' given!' );
			}
		}
		
	}
	
/*
getDirectorySeparatorChar()
getAltDirectorySeparatorChar()
getInvalidPathChars()
getInvalidFileNameChars()
changeExtension( $path, $extension )
combine( $path1, $path2 )
getDirectoryName( $path )
getExtension( $path )
getFileName( $path )
getFileName( $path )
getFullPath( $path )
getPathRoot( $path )
getRandomFileName( $path )
getTempFileName()
getTempPath()
hasExtension( $path )
isPathRooted( $path )
*/
?>