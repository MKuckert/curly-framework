<?php

/**
 * Curly_Sourcecode_ProcessorStrategy_Interface
 * 
 * Basisinterface für eine Sourcecode-ProcessorStrategy
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
interface Curly_Sourcecode_ProcessorStrategy_Interface {
	
	/**
	 * Wird von der Processor-Instanz nach der Registrierung dieser Instanz
	 * aufgerufen.
	 * 
	 * @return void
	 * @param Curly_Sourcecode_Processor
	 * @param string|integer Tokentyp, für den diese Instanz registriert wurde
	 */
	public function registeredAt(Curly_Sourcecode_Processor $processor, $type);
	
	/**
	 * Wird von der Processor-Instanz für jeden Token des registrierten Typs
	 * aufgerufen, um zu überprüfen, ob diese Instanz diesen Token bearbeiten
	 * wird.
	 * 
	 * @return boolean
	 * @param mixed Token
	 */
	public function acceptsToken($token);
	
	/**
	 * Wird von der Processor-Instanz für jeden Token des registrierten Typs
	 * aufgerufen.
	 * 
	 * @return void
	 * @param mixed Token
	 */
	public function processToken($token);
	
}
