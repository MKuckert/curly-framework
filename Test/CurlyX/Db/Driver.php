<?php

abstract class Test_CurlyX_Db_Driver extends Curly_Test {
	
	private $con;
	private $stmt;
	
	abstract protected function getCredentials();
	
	public function testSetup() {
		list($dsn, $username, $password, $options)=$this->getCredentials();
		
		$this->con=new CurlyX_Db_Connection_Front($dsn, $username, $password, $options);
	}
	
	public function testPrepare1() {
		$this->stmt=$this->con->prepare("select * from testdata order by id");
		$this->assertTrue($this->stmt instanceof CurlyX_Db_Statement_Interface, 'Keine Statement-Instanz erstellt');
	}
	
	public function testExecute1() {
		$this->assertTrue($this->stmt->execute(), 'Execute ist fehlgeschlagen');
	}
	
	public function testFetch1() {
		$data=$this->stmt->fetch();
		$this->assertEqual($data, array(
			'id'		=> 1,
			0			=> 1,
			'title'		=> 'Home',
			1			=> 'Home',
			'contents'	=> 'Die Startseite',
			2			=> 'Die Startseite',
			'created'	=> '2008-12-01 22:50:15',
			3			=> '2008-12-01 22:50:15'
		), 'Datensatz 1 ist fehlerhaft');
	}
	
	public function testFetchColumn1() {
		$data=$this->stmt->fetchColumn();
		$this->assertEqual($data, 2, 'Datenfeld ist fehlerhaft');
	}
	
	public function testFetchObject1() {
		$data=$this->stmt->fetchObject();
		$compData=new stdClass();
		$compData->id=3;
		$compData->title="Content";
		$compData->contents="Hier ist echt viel Inhalt!";
		$compData->created="2008-12-03 22:54:52";
		$this->assertEqual($data, $compData, 'Datenobjekt ist fehlerhaft');
	}
	
	public function testPrepare2() {
		$this->stmt=$this->con->prepare("select * from testdata where id<:id and id>:id2");
		$this->assertTrue($this->stmt instanceof CurlyX_Db_Statement_Interface, 'Keine Statement-Instanz erstellt');
	}
	
	public function testBindValue2() {
		$this->assertTrue($this->stmt->bindValue(':id', 3, CurlyX_Db_Connection_Interface::PARAM_INT), 'bindValue :id nicht true');
	}
	
	public function testBindParam2() {
		$value=1;
		$this->assertTrue($this->stmt->bindParam(':id2', $value, CurlyX_Db_Connection_Interface::PARAM_INT), 'bindParam :id2 nicht true');
	}
	
	public function testExecute2() {
		$this->assertTrue($this->stmt->execute(), 'Execute ist fehlgeschlagen');
	}
	
	public function testFetchAll2() {
		$data=$this->stmt->fetchAll(CurlyX_Db_Connection_Interface::FETCH_ASSOC);
		$this->assertEqual($data, array(
			array('id'=>2, 'title'=>'Impressum', 'contents'=>'Die Verantwortlichen sind:', 'created'=>'2008-12-01 10:00:00')
		), 'Datenarray ist fehlerhaft');
	}
	
	public function testTryUnbound() {
		try {
			$this->testPrepare2();
			$this->testBindValue2();
			$this->testExecute2();
			$this->failure('Keine Exception wegen ungebundenen zweiten Platzhalter geworfen');
		}
		catch(CurlyX_Db_Exception $ex) {
			// Korrekt
		}
	}
	
	public function testFetchColumn3() {
		$this->testPrepare2();
		$this->testBindValue2();
		$this->testBindParam2();
		$this->testExecute2();
		
		$data=$this->stmt->fetchColumn(1);
		$this->assertEqual($data, 'Impressum', 'Daten sind fehlerhaft');
	}
	
	public function testCloseCursor() {
		$this->stmt->closeCursor();
	}
	
}