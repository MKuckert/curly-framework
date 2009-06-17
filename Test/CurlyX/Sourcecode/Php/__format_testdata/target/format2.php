<?php
$foo[]=1;
if(count($foo)) {
	do_something();
	foreach($foo as $key=>$value) {
		$foo="foo";
		echo $$foo;
		if(true) {
			asd();
		}
		else {
			zxy();
		}
	}
}
else {
	asd();
}
