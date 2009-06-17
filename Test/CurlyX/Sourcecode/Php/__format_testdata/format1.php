<?php
	$foo[] = 1;
	if(count($foo)){
		do_something();
		foreach($foo as $key=>$value){
			$foo = "foo";
			echo $foo;
			echo $$foo;
			echo "foo ".$foo.PHP_EOL.123;
			if(true){
				asd();
			}
			else{
				zxy();
			}
		}
		$bar=123;
	}
	else{$huhu;
		asd();
	}define('ASD', 123);
	
	$heredoc=<<<HEREDOC
		Hier ist heredoc
		
		
		
		$foo
HEREDOC;

abstract class FOO extends ASD123 {
public function asd() {
	$this->_foo=$asd;
	$asd=new FOO(123, 123.895);
}
}

$bla=array(1,2,3,array('a','boo'), true, array(array(false)));

$bla=123 ? foo() : (X ? 'ollee' : 123);
?>