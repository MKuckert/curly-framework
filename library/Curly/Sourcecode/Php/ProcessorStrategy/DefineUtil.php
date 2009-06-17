<?php

require_once "Curly/Sourcecode/Php/ProcessorStrategy/DefineCollection.php";
require_once "Curly/Sourcecode/Php/ProcessorStrategy/IfDef.php";
require_once "Curly/Sourcecode/Php/ProcessorStrategy/IfNDef.php";
require_once "Curly/Sourcecode/Php/ProcessorStrategy/Define.php";
require_once "Curly/Sourcecode/Php/ProcessorStrategy/DefineValue.php";

/**
 * Curly_Sourcecode_Php_ProcessorStrategy_DefineUtil
 * 
 * Implementation einer schnellen Erstellung aller Define-Strategien.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.ProcessorStrategy
 * @since 1.0 - 20.02.2009
 * @version 1.0
 */
class Curly_Sourcecode_Php_ProcessorStrategy_DefineUtil {

	/**
	 * Erstellt die Strategien IfDef, IfNDef, Define, DefineValue und gibt diese
	 * als Array zurück.
	 * 
	 * @return array Ein Array mit den erstellten Strategy-Instanzen
	 */
	static public function create() {
		$collection=new Curly_Sourcecode_Php_ProcessorStrategy_DefineCollection();
		$ifdef=new Curly_Sourcecode_Php_ProcessorStrategy_IfDef($collection);
		$ifndef=new Curly_Sourcecode_Php_ProcessorStrategy_IfNDef($collection);
		$define=new Curly_Sourcecode_Php_ProcessorStrategy_Define($collection);
		$defval=new Curly_Sourcecode_Php_ProcessorStrategy_DefineValue($collection);
		
		return array(
			'ifdef'			=> $ifdef,
			'ifndef'		=> $ifndef,
			'define'		=> $define,
			'definevalue'	=> $defval
		);
	}
	/**
	 * Erstellt die Strategien IfDef, IfNDef, Define, DefineValue und
	 * registriert diese an der übergebenen Processor-Instanz.
	 * 
	 * @return array Ein Array mit den erstellten Strategy-Instanzen
	 * @param Curly_Sourcecode_Processor
	 */
	static public function registerAt(Curly_Sourcecode_Processor $processor) {
		$strategies=self::create();
		
		$processor->registerStrategy(T_COMMENT, $strategies['ifdef']);
		$processor->registerStrategy(T_COMMENT, $strategies['ifndef']);
		$processor->registerStrategy(T_COMMENT, $strategies['define']);
		$processor->registerStrategy(T_STRING, $strategies['definevalue']);
		
		return $strategies;
	}
	
}
