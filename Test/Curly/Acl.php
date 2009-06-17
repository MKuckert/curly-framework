<?php

function errorHandler() {
	var_dump(func_get_args());
}
set_error_handler('errorHandler');

class Test_Curly_Acl extends Curly_Test {
	
	const ROLES=15;
	const RESOURCES=15;
	
	private $db=NULL;
	private $acl=NULL;
	private $serialFile;
	
	protected function setUp() {
		$this->serialFile=dirname(__FILE__).'/acl-serial.txt';
		$this->acl=new Curly_Acl_Db();
		$this->initAcl();
		
		$this->acl->truncate();
		$this->acl->load();
	}
	
	protected function initAcl() {
		$params=array(
			'dbname'   => 'curly_acl',
			'username' => 'root',
			'password' => ''
		);
		$this->db=Zend_Db::factory('PDO_Mysql', $params);
		
		$this->acl->setOptions(array(
			'db' => $this->db
		));
	}
	
	public function testSetup() {
		$start=microtime(true);
		
		for($i=0; $i<self::ROLES; $i++) {
			$this->acl->addRole(new Zend_Acl_Role('user'.$i));
		}
		
		for($i=0; $i<self::RESOURCES; $i++) {
			$this->acl->add(new Zend_Acl_Resource('resource'.$i));
		}
		
		for($i=0; $i<self::ROLES; $i++) {
			for($j=0; $j<self::RESOURCES; $j++) {
				$this->acl->{rand()%2?'allow':'deny'}('user'.$i, 'resource'.$j);
			}
		}
		
		$stop=microtime(true);
		
		echo 'init: '.($stop-$start).'s'.PHP_EOL;
		
		$this->acl->truncate();
	}
	
	public function testSetup2() {
		$start=microtime(true);
		
		$lr=NULL;
		for($i=0; $i<self::ROLES; $i++) {
			$r=new Zend_Acl_Role('user'.$i);
			$this->acl->addRole($r, $lr);
			$lr=$r;
		}
		
		for($i=0; $i<self::RESOURCES; $i++) {
			$this->acl->add(new Zend_Acl_Resource('resource'.$i));
		}
		
		for($i=0; $i<self::ROLES; $i++) {
			for($j=0; $j<self::RESOURCES; $j++) {
				$this->acl->{rand()%2?'allow':'deny'}('user'.$i, 'resource'.$j);
			}
		}
		
		$stop=microtime(true);
		
		echo 'init: '.($stop-$start).'s'.PHP_EOL;
		
		$this->acl->truncate();
	}
	
	public function testSetup3() {
		$start=microtime(true);
		
		for($i=0; $i<self::ROLES; $i++) {
			$this->acl->addRole(new Zend_Acl_Role('user'.$i));
		}
		
		$lr=NULL;
		for($i=0; $i<self::RESOURCES; $i++) {
			$r=new Zend_Acl_Resource('resource'.$i);
			$this->acl->add($r, $lr);
			$lr=$r;
		}
		
		for($i=0; $i<self::ROLES; $i++) {
			for($j=0; $j<self::RESOURCES; $j++) {
				$this->acl->{rand()%2?'allow':'deny'}('user'.$i, 'resource'.$j);
			}
		}
		
		$stop=microtime(true);
		
		echo 'init: '.($stop-$start).'s'.PHP_EOL;
		
		$this->acl->truncate();
	}
	
	public function testSetup4() {
		$start=microtime(true);
		
		$lr=NULL;
		for($i=0; $i<self::ROLES; $i++) {
			$r=new Zend_Acl_Role('user'.$i);
			$this->acl->addRole($r, $lr);
			$lr=$r;
		}
		
		$lr=NULL;
		for($i=0; $i<self::RESOURCES; $i++) {
			$r=new Zend_Acl_Resource('resource'.$i);
			$this->acl->add($r, $lr);
			$lr=$r;
		}
		
		for($i=0; $i<self::ROLES; $i++) {
			for($j=0; $j<self::RESOURCES; $j++) {
				$this->acl->{rand()%2?'allow':'deny'}('user'.$i, 'resource'.$j);
			}
		}
		
		$stop=microtime(true);
		
		echo 'init: '.($stop-$start).'s'.PHP_EOL;
	}
	
	/*public function testCheckRights() {
		$this->assertFalse(
			$this->acl->isAllowed('user1', 'someResource'),
			'Gastuser sollte Zugang verwehrt sein'
		);
		$this->assertTrue(
			$this->acl->isAllowed('user2', 'someResource'),
			'Memberuser sollte Zugang erlaubt sein'
		);
	}*/
	
	public function testSerialize() {
		$start=microtime(true);
		file_put_contents($this->serialFile, serialize($this->acl));
		$stop=microtime(true);
		echo 'serialize: '.($stop-$start).'s'.PHP_EOL;
	}
	
	public function testUnserialize() {
		$start=microtime(true);
		$this->acl=unserialize(file_get_contents($this->serialFile));
		$this->initAcl();
		$stop=microtime(true);
		echo 'unserialize: '.($stop-$start).'s'.PHP_EOL;
	}
	
	/*public function testCheckRightsAfterSerialize() {
		$this->testCheckRights();
	}*/
	
	public function testAddRoleAfterSerialize() {
		$this->acl->addRole(new Zend_Acl_Role('fooUser'), array('user1'));
	}
	
}
