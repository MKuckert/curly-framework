<?php

class Test_CurlyX_Db_Statement_Parser extends Curly_Test {

	public function testParserNumeric() {
		$statement="select `param1` from `table` where id=?;";
		
		$parser=new CurlyX_Db_Statement_Parser($statement);
		$parser->parse();
		
		$this->assertEqual($parser->getNumericParams(), array(38), 'Nummerische Parameter fehlerhaft');
		
		$this->assertEqual($parser->getNamedParams(), array(), 'Benannte Parameter gefunden');
	}

	public function testParserNamed() {
		$statement="select `param1` from `table` where id=:id and foo;";
		
		$parser=new CurlyX_Db_Statement_Parser($statement);
		$parser->parse();
		
		$this->assertEqual($parser->getNamedParams(), array(array(38, ':id')), 'Benannte Parameter fehlerhaft');
		
		$this->assertEqual($parser->getNumericParams(), array(), 'Nummerische Parameter gefunden');
	}

	public function testParserNamedNumeric() {
		$statement="select `param1` from `table` where id=? and foo=:foo;";
		
		$parser=new CurlyX_Db_Statement_Parser($statement);
		$parser->parse();
		
		$this->assertEqual($parser->getNamedParams(), array(array(48, ':foo')), 'Benannte Parameter fehlerhaft');
		
		$this->assertEqual($parser->getNumericParams(), array(38), 'Nummerische Parameter fehlerhaft');
	}
	
}