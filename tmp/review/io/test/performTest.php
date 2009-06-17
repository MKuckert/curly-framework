<?php
	
	error_reporting( E_ALL | E_STRICT );
	
	/**
	 * TestPerformer
	 * Stellt eine Schnittstelle zur Ausführung von Tests zur Verfügung.
	 * 
	 * @author Martin Kuckert
	 * @version 1.0
	 */
	class TestPerformer {
		
		/**
		 * Führt einen Test aus oder fordert vom Anwender eine Eingabe des
		 * auszuführenden Tests.
		 * 
		 * @return void
		 */
		public function run() {
			if( $this->isConsole() ) {
				$argv = $_SERVER['argv'];
				
				// Kein Argument übergeben
				if( count( $argv )<=1 ) {
					$input = $this->getConsoleInput();
				}
				else {
					$input = $argv[1];
				}
			}
			else {
				// Kein Argument übergeben
				if( count( $_GET )<=0 ) {
					$this->printHtmlForm();
					return;
				}
				else {
					$input = reset( $_GET );
				}
			}
			
			// Führt einen Test aus
			$this->performTest( $input );
		}
		
		/**
		 * Fordert vom Anwender die Eingabe eines Testnamens und gibt diesen
		 * zurück.
		 * 
		 * @return string
		 */
		protected function getConsoleInput() {
			echo "Enter the name of the test to be performed\n > ";
			$input = '';
			do {
				fscanf( STDIN, "%s", $input );
			}
			while( ($input=trim( $input ))=='' );
			return( $input );
		}
		
		/**
		 * Gibt ein HTML-Formular zur Eingabe eines Testnamens aus.
		 * 
		 * @return void
		 */
		protected function printHtmlForm() {
			echo $this->getHtmlTemplate( $this->getHtmlForm() );
		}
		
		/**
		 * Gibt ein HTML-Formular zur Eingabe eines Testnamens zurück.
		 * 
		 * @return void
		 */
		protected function getHtmlForm() {
			return( '		<form method="get" action="" accept-charset="UTF-8">
			<div>
				Enter the name of the test to be performed:<br />
				<input type="text" name="i" /><input type="submit" value="perform" />
			</div>
		</form>' );
		}
		
		/**
		 * Gibt das zu verwendende Template für HTML-Ausgaben zurück. Die
		 * Stelle, an der Inhalte eingefügt werden können, muss durch den
		 * Platzhalter {CONTENT} markiert werden.
		 * Wird ein Parameter übergeben, wird der Platzhalter automatisch durch
		 * diesen ersetzt.
		 * 
		 * @return string
		 * @param string
		 */
		protected function getHtmlTemplate( $content=null ) {
			$template = '<?xml version="1.0" encoding="UTF-8" ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/Strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">

	<head>
		<title>TestPerformer</title>
		<meta http-equiv="pragma" content="no-cache" />
		<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	</head>
	<body>
		{CONTENT}
	</body>
</html>';
			if( !is_null( $content ) ) {
				$template = str_replace( '{CONTENT}', $content, $template );
			}
			return( $template );
		}
		
		/**
		 * Gibt das Ergebnis des Testes aus
		 * 
		 * @return void
		 * @param string
		 */
		protected function printResult( $result ) {
			if( $this->isConsole() ) {
				echo $result;
			}
			else {
				$result = $this->getHtmlForm() . '<pre>'.$result.'</pre>';
				echo $this->getHtmlTemplate( $result );
			}
		}
		
		/**
		 * Führt den Test mit dem angegebenen Namen aus und gibt die Ergebnisse
		 * aus.
		 * 
		 * @return void
		 * @param string
		 */
		protected function performTest( $testName ) {
			$this->loadFiles( $testName );
			
			$class = $testName."Test";
			$test = new $class();
			$result = $test->run();
			
			$this->printResult( $result );
		}
		
		/**
		 * Bindet alle benötigten Dateien ein
		 * 
		 * @return void
		 * @param string Name des auszuführenden Testes
		 */
		protected function loadFiles( $testName ) {
			include_once "Test.php";
			include_once $testName."Test.php";
		}
		
		/**
		 * Überprüft, ob es sich bei dieser Skriptausführung um eine Ausführung
		 * auf der Konsole handelt. Ist dies der Fall, wird true zurückgegeben,
		 * andernfalls false.
		 * 
		 * @return boolean
		 */
		public function isConsole() {
			return( isset( $GLOBALS['argv'], $GLOBALS['argc'] )
				or php_sapi_name()=='cli' );
		}
		
	}
	
	$performer = new TestPerformer();
	$performer->run();
	
?>