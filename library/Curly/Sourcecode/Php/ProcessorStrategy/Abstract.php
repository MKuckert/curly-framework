<?php

require_once "Curly/Sourcecode/ProcessorStrategy/Abstract.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_Abstract
 * 
 * Stellt sicher, dass die Klassen dieses Namensraumes nur mit einer
 * Php-Tokens-Instanz arbeiten können.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.Php.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
abstract class Curly_Sourcecode_Php_ProcessorStrategy_Abstract extends Curly_Sourcecode_ProcessorStrategy_Abstract {
	
	/**
	 * Wird von der Processor-Instanz nach der Registrierung dieser Instanz
	 * aufgerufen.
	 * 
	 * @return void
	 * @param Curly_Sourcecode_Processor
	 * @param string|integer Tokentyp, für den diese Instanz registriert wurde
	 */
	public function registeredAt(Curly_Sourcecode_Processor $processor, $type) {
		if(!($processor->getTokens() instanceof Curly_Sourcecode_Php_Tokens)) {
			require_once "Curly/Sourcecode/Php/Exception.php";
			throw new Curly_Sourcecode_Php_Exception('Classes in this namespace can only work with an Php-Tokens instance');
		}
		parent::registeredAt($processor, $type);
	}
	
}
