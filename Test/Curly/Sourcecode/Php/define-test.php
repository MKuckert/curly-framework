<?php






#if USE_NS=1
// Using Namespace
class MyNs_MyClass {
#else
// Using no Namespace
class MyClass {
#endif
	
	const MyConst=123;
	
#include test-include.php
	
	public function doSomething() {
		bar();
		foo();

		echo 123;

		// FOOO

		Application::initProductiveSystem();


#if TARGET_OS="WIN"
		// This is Windows
		$eol="\r\n";
#elif TARGET_OS="MACOSX"
		// This is Mac OS X
		$eol="\r";
#endif
	}
	
}
