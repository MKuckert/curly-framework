<?php
	error_reporting( E_ALL );
	require "../Test.php";
	require "../../Core/Object.php";
	require "../../Core/Collection.php";
	require "../../exceptions/MemberNotDefinedException.php";
	require "../../exceptions/MethodNotDefinedException.php";
	require "../../net/HttpClient.php";
	
	class HttpClientTest extends Test {
		
		private $host = 'google.de';
		
		public function testMain() {
			$client = new HttpClient();
			$client->Host = $this->host;
			$this->assertEqual( $this->host, $client->Host, 'Host nicht gleich' );
			
			$client->send();
			
			echo "Status: ".$client->getStatusCode()." ".$client->getStatusMessage()."\n";
			foreach( $client->getHeaders() as $header=>$value ) {
				echo $header.': '.$value."\n";
			}
			echo "Redirects: ".$client->getRedirects();
		}
		
	}
	
	$test = new HttpClientTest();
	echo $test->run();
	
?>