<?php

class TClass extends ReflectionClass {
	
	/**
	 * Gibt den Pfad zu einer Klassenressource zurück.
	 * 
	 * @return string oder NULL, wenn die Ressource nicht gefunden wird
	 * @param string Name der Ressource
	 */
	public function getResourcePath($name) {
		$filename=dirname($this->getFileName()).DIRECTORY_SEPARATOR.$name;
		if(!is_file($filename)) {
			return(NULL);
		}
		return($filename);
	}
	
}