<?php

require_once "Curly/Sourcecode/Minimizer.php";
require_once "Curly/Sourcecode/Php/Tokens.php";

/**
 * Curly_Sourcecode_Minimizer_Php
 * 
 * Dieser Minimizer verkleinert die Datengröße eines PHP-Skripts
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode
 * @since 1.0 - 14.11.2008
 * @version 1.0
 */
class Curly_Sourcecode_Minimizer_Php extends Curly_Sourcecode_Minimizer {
	
	/**
	 * Diese Methode ließt die zu minimierenden Daten aus dem Datenstrom und
	 * verkleinert diese nach dem Schema dieser Klasse.
	 *
	 * @return boolean
	 */
	public function minimize() {
		parent::minimize();
		$source=$this->getSourceStream();
		$target=$this->getTargetStream();
			
		$tokens=new Curly_Sourcecode_Php_Tokens();
		$tokens->parseStream($source);
		$tokens->stripWhitespace();
		$tokens->renderStream($target);
			
		return(true);
	}
	
}