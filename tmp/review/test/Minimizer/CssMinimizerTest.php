<?php
	error_reporting( E_ALL );
	require "../Test.php";
	require "../../Core/Object.php";
	require "../../Core/Ensure.php";
	require "../../exceptions/MemberNotDefinedException.php";
	require "../../Minimizer/Minimizer.php";
	require "../../Minimizer/CssMinimizer.php";
	require "../../Stream/Stream.php";
	require "../../Stream/IOException.php";
	
	class CssMinimizerTest extends Test {
		public function testCssMinimizer() {
			$source = new Stream( 'in.css', 'r' );
			$target = new Stream( 'out.css', 'w' );
			
			$minimizer = new CssMinimizer();
			$minimizer->SourceStream = $source;
			$minimizer->TargetStream = $target;
			$minimizer->minimize();
			
			$in = filesize( 'in.css' );
			$out = filesize( 'out.css' );
			echo "Minimized from ".$in." bytes to ".$out." bytes (".round( $out/$in*100, 2 )."%)\n";
		}
	}
	
	$test = new CssMinimizerTest();
	echo $test->run();
	
?>