<?php

require_once "Curly/Test/Exception.php";

/**
 * Curly_Test
 * 
 * Vorlage für Test-Cases
 * 
 * @author Martin Kuckert
 * @version 1.0
 */
abstract class Curly_Test {
	
	/**
	 * @var integer Anzahl fehlgeschlagener Tests
	 */
	protected $_failed = 0;
	
	/**
	 * @var integer Anzahl erfolgreicher Tests
	 */
	protected $_success = 0;
	
	/**
	 * @var integer Anzahl durchgeführter Tests
	 */
	protected $_tested = 0;
	
	/**
	 * @var array Aufrufslog
	 */
	protected $_log = array();
	
	/**
	 * @var array Übergabeparameter von Außen
	 */
	protected $args=array();
	
	/**
	 * Konstruktor
	 * 
	 * @param array Übergabeparameter von Außen
	 */
	public function __construct(array $args=array()) {
		$this->args=$args;
	}
	
	/**
	 * Optionale Vorbereitungsaufgaben
	 * 
	 * @return void
	 */
	protected function setUp() {
		// Kann in Unterklassen überladen werden
	}
	
	/**
	 * Startet den Test und führt die einzelnen Testaufgaben durch
	 * 
	 * @return void
	 */
	public function run() {
		$this->setUp();
		
		$reflection = new ReflectionClass( get_class( $this ) );
		foreach( $reflection->getMethods() as $method ) {
			if( substr( $method->getName(), 0, 4 )=='test' and $method->isPublic() ) {
				$log = array( 'call' => substr( $method->getName(), 4 ) );
				try {
					$method->invoke( $this );
					$this->_success++;
					$log['result'] = 'success';
				}
				catch( Curly_Test_Exception $ex ) {
					$this->_failed++;
					$log['result'] = 'failure';
					$log['message'] = $ex->getMessage();
				}
				$this->_log[] = $log;
				$this->_tested++;
			}
		}
		
		return( $this->getResult() );
	}
	
	/**
	 * Erstellt die Ausgabe der aufgetretenen Fehler
	 * 
	 * @return void
	 */
	public function getResult() {
		$row = '';
		foreach( $this->_log as $log ) {
			$row .= $log['call'];
			if( $log['result']=='success' ) {
				$row .= ": succeeded\n";
			}
			else {
				$row .= ': failed with message "'.$log['message'].'"'."\n";
			}
		}
		return( "Result for test ".substr(get_class( $this ),5)."\n=============\n"
			.$row."\n=============\n"
			.$this->_success." of ".$this->_tested." tests succeed. "
			.$this->_failed." failed"
		);
	}
	
	/**
	 * Wird ausgeführt, wenn ein Test fehlschlägt, um die Fehlermeldung
	 * auszugeben
	 * 
	 * @return void
	 * @param string
	 */
	public function failure( $msg ) {
		throw new Curly_Test_Exception( $msg );
	}
	
	/**#@+
	 * Schlägt der Test in dieser Methode fehl, wird eine entsprechende
	 * Meldung zurückgegeben. Weiter werden die $_failed und
	 * $_success-Zähler modifiziert
	 */
	/**
	 * Testet, ob beide Eingabeparameter gleich sind. Es wird der
	 * ==-Operator verwendet.
	 * 
	 * @return void
	 * @param mixed
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertEqual( $var1, $var2, $msg ) {
		if( $var1==$var2 ) {
			return;
		}
		else {
			$this->failure( $msg );
		}
	}
	
	/**
	 * Testet, ob beide Eingabeparameter ungleich sind. Es wird der
	 * !=-Operator verwendet.
	 * 
	 * @return void
	 * @param mixed
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertNotEqual( $var1, $var2, $msg ) {
		if( $var1!=$var2 ) {
			return;
		}
		else {
			$this->failure( $msg );
		}
	}
	
	/**
	 * Testet, ob beide Eingabeparameter strikt gleich sind. Es wird der
	 * ===-Operator verwendet.
	 * 
	 * @return void
	 * @param mixed
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertStrictEqual( $var1, $var2, $msg ) {
		if( $var1===$var2 ) {
			return;
		}
		else {
			$this->failure( $msg );
		}
	}
	
	/**
	 * Testet, ob beide Eingabeparameter strikt ungleich sind. Es wird der
	 * !==-Operator verwendet.
	 * 
	 * @return void
	 * @param mixed
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertNotStrictEqual( $var1, $var2, $msg ) {
		if( $var1!==$var2 ) {
			return;
		}
		else {
			$this->failure( $msg );
		}
	}
	
	/**
	 * Testet, ob der linke Parameter kleiner dem rechten ist.
	 * 
	 * @return void
	 * @param mixed
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertLower( $var1, $var2, $msg ) {
		if( $var1 < $var2 ) {
			return;
		}
		else {
			$this->failure( $msg );
		}
	}
	
	/**
	 * Testet, ob der linke Parameter kleiner oder gleich dem rechten ist.
	 * 
	 * @return void
	 * @param mixed
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertLowerEqual( $var1, $var2, $msg ) {
		if( $var1 <= $var2 ) {
			return;
		}
		else {
			$this->failure( $msg );
		}
	}
	
	/**
	 * Testet, ob der linke Parameter größer dem rechten ist.
	 * 
	 * @return void
	 * @param mixed
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertHigher( $var1, $var2, $msg ) {
		if( $var1 > $var2 ) {
			return;
		}
		else {
			$this->failure( $msg );
		}
	}
	
	/**
	 * Testet, ob der linke Parameter größer oder gleich dem rechten ist.
	 * 
	 * @return void
	 * @param mixed
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertHigherEqual( $var1, $var2, $msg ) {
		if( $var1 >= $var2 ) {
			return;
		}
		else {
			$this->failure( $msg );
		}
	}
	
	/**
	 * Testet, ob der Eingabeparameter true ist.
	 * 
	 * @return void
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertTrue( $var, $msg ) {
		$this->assertEqual( $var, true, $msg );
	}
	
	/**
	 * Testet, ob der Eingabeparameter false ist.
	 * 
	 * @return void
	 * @param mixed
	 * @param string Fehlermeldung, wenn dieser Test fehlschägt
	 */
	public function assertFalse( $var, $msg ) {
		$this->assertEqual( $var, false, $msg );
	}
	/**#@-*/
	
}