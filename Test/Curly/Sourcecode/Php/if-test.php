<?php

#define CONST_VALUE 123
#define USE_NS 0
#define OS "LINUX"
#define TARGET_OS OS


// Using Namespace
class MyNs_MyClass {

	
	const MyConst=CONST_VALUE;
	
#include test-include.php
	
	public function doSomething() {
		bar();
#ifdef DEBUG
#ifdef Otherstuff
		Application::trace('Doing something');
#endif
#endif
		foo();
#ifdef CONST_VALUE
		echo CONST_VALUE;
#endif
		// FOOO
#ifndef DEBUG
		Application::initProductiveSystem();
#endif


		// This is Mac OS X
		$eol="\r";

	}
	
}
