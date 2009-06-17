<?php
	error_reporting( E_ALL );
	require "../Test.php";
	$path = '../../Generation/';
	set_include_path( get_include_path() . PATH_SEPARATOR . $path );
	
	class GenerationTest extends Test {
		
		public function testFunction() {
			$func = new GenerationFunction();
			$func->setName( 'MyFunc' );
			$param = new GenerationParameter();
			$param->setName( 'param1' );
			$func->Parameters[] = $param;
		}
		
	}
	
	$test = new GenerationTest();
	echo $test->run();
	
?>