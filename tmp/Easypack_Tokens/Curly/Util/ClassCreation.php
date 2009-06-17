<?php

/**
 * Curly_Util_ClassCreation
 * 
 * Einfache statische Methode zum Erstellen einer Instanz mit dynamischer
 * Konstruktorparameterzahl.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Util
 * @since 1.0 - 02.12.2008
 * @version 1.0
 */
class Curly_Util_ClassCreation {
	
	/**
	 * Erstellt eine neue Instanz der Klasse $class und übergibt dem
	 * Konstruktor dabei die Argumente $args.
	 * 
	 * @throws ReflectionException Wenn die Klassendefinition nicht vorhanden
	 *  ist bzw. nicht geladen werden kann.
	 * @return object Die erzeugte Instanz
	 * @param string Der Klassenname
	 * @param array Die Konstruktorargumente
	 */
	static public function newInstance($class, array $ctorArgs=array()) {
		$reflection=new ReflectionClass($class);
		if($reflection->getConstructor()===NULL) {
			$obj=new $class();
		}
		else {
			$obj=$reflection->newInstanceArgs($ctorArgs);
		}
		return($obj);
	}
	
}