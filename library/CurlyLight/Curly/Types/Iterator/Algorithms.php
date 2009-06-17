<?php

/**
 * Curly_Types_Iterator_Algorithms
 * 
 * Implementiert Algorithmen an Iterator-Instanzen.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Types.Iterator
 * @since 1.0 - 30.12.2008
 * @version 1.0
 */
class Curly_Types_Iterator_Algorithms {
	
	/**
	 * Wendet einen Callback auf jedes Element eines Iterators an.
	 * 
	 * Dem Callback werden drei Parameter übergeben. Das erste Element ist der
	 * aktuelle Schlüssel, das zweite der aktuelle Wert, das dritte der Iterator
	 * selbst.
	 * 
	 * @throws Curly_Exception
	 * @return void
	 * @param Traversable|array
	 * @param Callback
	 */
	static public function map($iterator, $callback) {
		if(!Curly_Util_TypeInfo::isTraversable($iterator)) {
			require_once 'Curly/Exception.php';
			throw new Curly_Exception('The given parameter is not traversable');
		}
		if(!Curly_Util_TypeInfo::isCallback($callback)) {
			require_once 'Curly/Exception.php';
			throw new Curly_Exception('The given parameter is no valid callback');
		}
		
		$callback=new Curly_Types_Callback($callback);
		foreach($iterator as $key=>$value) {
			$callback->invoke($key, $value, $iterator);
		}
	}
	
}
