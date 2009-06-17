<?php

require_once 'Zend/Config.php';
require_once 'Curly/Yaml/Parser.php';

/**
 * @package    Curly_Config
 * @author     Martin Kuckert
 * @since      16.10.2008
 */
class Curly_Config_Yaml extends Zend_Config {
	
	public function __construct($filename, $section=null, $allowModifications=false){
		$parser=new Curly_Yaml_Parser();
		$result=$parser->parseFile($filename);
		if(!is_array($result)){
			require_once 'Zend/Config/Exception.php';
			throw new Zend_Config_Exception('Unable to parse the file '.$filename.'. Are you sure it is valid yaml?');
		}
		
		if($section!==null){
			if(!isset($result[$section])){
				require_once 'Zend/Config/Exception.php';
				throw new Zend_Config_Exception('Unable to find the section '.$section);
			}
			$result=$result[$section];
		}
		
		parent::__construct($result,$allowModifications);
	}
	
}