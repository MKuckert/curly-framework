<?php

class Test_Curly_Types_BigDecimal extends Curly_Test {
	
	public function testAdd() {
		$this->assertEqual(
			Curly_Types_BigDecimal::create('100000')
				->add('2000')
				->getValue(),
			'102000',
			'Add 1 fehlerhaft'
		);
		
		$this->assertEqual(
			Curly_Types_BigDecimal::create('-1234.5678')
				->add('-355.1')
				->getValue(),
			'-1589.6678',
			'Add 2 fehlerhaft'
		);
	}
	
	public function testSubtract() {
		$this->assertEqual(
			Curly_Types_BigDecimal::create('100000')
				->subtract('2000')
				->getValue(),
			'98000',
			'Subtract 1 fehlerhaft'
		);
		
		$this->assertEqual(
			Curly_Types_BigDecimal::create('-1234.5678')
				->subtract('-355.1')
				->getValue(),
			'-879.4678',
			'Subtract 2 fehlerhaft'
		);
	}
	
	public function testMultiply() {
		$this->assertEqual(
			Curly_Types_BigDecimal::create('100000')
				->multiply('2000')
				->getValue(),
			'200000000',
			'Multiply 1 fehlerhaft'
		);
		
		$this->assertEqual(
			Curly_Types_BigDecimal::create('-1234.5678')
				->multiply('-355.1')
				->getValue(),
			'438395.02578',
			'Multiply 2 fehlerhaft'
		);
	}
	
	public function testDivide() {
		$this->assertEqual(
			Curly_Types_BigDecimal::create('100000')
				->divide('2000')
				->getValue(),
			'50',
			'Divide 1 fehlerhaft'
		);
		
		$this->assertEqual(
			Curly_Types_BigDecimal::create('-1234.5678')
				->divide('-355.1')
				->getValue(),
			'3.4766764291',
			'Divide 2 fehlerhaft'
		);
	}
	
	public function testModulo() {
		$this->assertEqual(
			Curly_Types_BigDecimal::create('100000')
				->modulo('2000')
				->getValue(),
			'0',
			'Modulo 1 fehlerhaft'
		);
		
		$this->assertEqual(
			Curly_Types_BigDecimal::create('-1234.5678')
				->modulo('355')
				->getValue(),
			'-169',
			'Modulo 2 fehlerhaft'
		);
	}
	
	public function testPotentiate() {
		$this->assertEqual(
			Curly_Types_BigDecimal::create('100000')
				->potentiate('2000')
				->getValue(),
			'1'.str_repeat('00000', 2000),
			'Potentiate 1 fehlerhaft'
		);
		
		$this->assertEqual(
			Curly_Types_BigDecimal::create('-1234')
				->potentiate('355')
				->getValue(),
			'-2611449864462722850511665870126580580880415618664834023936703219'.
			'26092024573592943953959842546073893582283386665301315528237201039'.
			'46251521710116552330752197040645144819257982133481292328211077851'.
			'36998123336159296828460440609651575020240388269269084268124811461'.
			'23440694880600623594370690577414561010568726841487943095848468588'.
			'83238205097713957714730331471922282140146155589450839230261677692'.
			'63144947022353361018965803729452632552290631376858515861133266948'.
			'41611550638593767615218011590897648589567991234253369392661299659'.
			'09568552193844204281358938303997055293248023557820076635270753414'.
			'62490292562455803246371121099443889179166919317938369672355255651'.
			'38287019085159716483043512570463452349976106991291483413358859501'.
			'49981920538197983055301340118789082008747516271226481102743190816'.
			'92533356927441437322704217201867340250395776097612545266418913209'.
			'25429777304985281205078623957169581440746839177317396427268943536'.
			'47109728380460109790658285519376430553956203533708765236686200956'.
			'58116037221454933263492628099749305426755618407123960818719389529'.
			'20579370604243105031678908777203496618260143006701714407424',
			'Potentiate 2 fehlerhaft'
		);
	}
	
	public function testSqrt() {
		$this->assertEqual(
			Curly_Types_BigDecimal::create('100000')
				->squareRoot()
				->getValue(),
			'316.2277660168',
			'SquareRoot 1 fehlerhaft'
		);
		
		$this->assertEqual(
			Curly_Types_BigDecimal::create('-1234.5678')
				->squareRoot()
				->getValue(),
			'0',
			'SquareRoot 2 fehlerhaft'
		);
	}
	
	public function testAbsolute() {
		$this->assertEqual(
			Curly_Types_BigDecimal::create('100000')
				->absolute()
				->getValue(),
			'100000',
			'Absolute 1 fehlerhaft'
		);
		
		$this->assertEqual(
			Curly_Types_BigDecimal::create('-1234.5678')
				->absolute()
				->getValue(),
			'1234.5678',
			'Absolute 2 fehlerhaft'
		);
	}
	
	public function testCopy() {
		$bi=new Curly_Types_BigDecimal('12345678901234567890');
		$bi2=$bi->copy();
		$this->assertTrue($bi->equals($bi2), 'Equals 1 fehlerhaft');
		$this->assertTrue($bi2->equals($bi), 'Equals 2 fehlerhaft');
		
		$bi->setValue('123.1234');
		$this->assertFalse($bi->equals($bi2), 'Equals 3 fehlerhaft');
		$this->assertFalse($bi2->equals($bi), 'Equals 4 fehlerhaft');
		$this->assertEqual($bi->add($bi2)->getValue(), '12345678901234568013.1234', 'Add fehlerhaft');
	}
	
	public function testNegate() {
		$bi=new Curly_Types_BigDecimal('123');
		$bi->negate();
		$this->assertEqual($bi->getValue(), '-123', 'Negate fehlerhaft');
	}
	
}