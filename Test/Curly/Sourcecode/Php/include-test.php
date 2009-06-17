<?php

#define CONST_VALUE 123
#define USE_NS 0
#define OS "LINUX"
#define TARGET_OS OS

#if USE_NS=1
// Using Namespace
class MyNs_MyClass {
#else
// Using no Namespace
class MyClass {
#endif
	
	const MyConst=CONST_VALUE;
	
	public function fromInclude() {
		// This is from include
	}
	
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

#if TARGET_OS="WIN"
		// This is Windows
		$eol="\r\n";
#elif TARGET_OS="MACOSX"
		// This is Mac OS X
		$eol="\r";
#endif
	}
	
}
