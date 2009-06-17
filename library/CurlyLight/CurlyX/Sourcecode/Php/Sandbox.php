<?php

require_once "CurlyX/Sourcecode/Php/Tokens.php";
require_once "CurlyX/Sourcecode/Php/Sandbox/Exception.php";

/**
 * CurlyX_Sourcecode_Php_Sandbox
 * 
 * Die is*-Methoden verwenden bei Listen von gültigen Werten und einer
 * CHECK-Option mit Hilfe des XOR-Operators.
 * 
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Sourcecode.Php
 * @since 1.0 - 09.11.2008
 * @version 1.0
 */
class CurlyX_Sourcecode_Php_Sandbox extends Curly_Sourcecode_Php_Tokens {
	
	const WHITELIST=1;
	const BLACKLIST=2;
	
	const OPT_VARS='vars';
	const OPT_VAR_CHECK='varCheck';
	const OPT_TOKENS='tokens';
	const OPT_TOKENS_CHECK='tokensCheck';
	const OPT_STRINGS='strings';
	const OPT_STRINGS_CHECK='stringsCheck';
	const OPT_DENY_INCLUSION='denyInclusion';
	const OPT_INCLUSION_TOKENS='inclusionTokens';
	const OPT_DENY_VARIABLE_VARS='denyVariableVars';
	const OPT_DENY_VARIABLE_METHOD_CALLS='denyVariableMethodCalls';
	
	protected function createDefaultOptions() {
		$options=parent::createDefaultOptions();
		$options->set(self::OPT_VARS, array('GLOBALS', 'intern_*'));
		$options->set(self::OPT_VAR_CHECK, self::BLACKLIST);
		$options->set(self::OPT_TOKENS, array(
			T_EXIT,
			T_EVAL,
			T_GLOBAL
		));
		$options->set(self::OPT_TOKENS_CHECK, self::BLACKLIST);
		$options->set(self::OPT_STRINGS, array(
			'call_user_func',
			'call_user_func_array',
			'system',
			'exec',
			'proc_open'
		));
		$options->set(self::OPT_STRINGS_CHECK, self::BLACKLIST);
		$options->set(self::OPT_DENY_INCLUSION, false);
		$options->set(self::OPT_INCLUSION_TOKENS, array(T_REQUIRE, T_REQUIRE_ONCE, T_INCLUDE, T_INCLUDE_ONCE));
		$options->set(self::OPT_DENY_VARIABLE_VARS, true);
		$options->set(self::OPT_DENY_VARIABLE_METHOD_CALLS, true);
		return($options);
	}
	
	private $token;
	private $index;
	
	public function checkCode() {
		foreach($this->tokens as $this->index=>$this->token) {
			// Token
			if($this->isDeniedToken()) {
				throw new CurlyX_Sourcecode_Php_Sandbox_Exception('Found denied token '.$this->getTokenName($this->token).' ('.$this->token[1].')');
			}
			
			// Variablen-Token
			if($this->isDeniedVar()) {
				throw new CurlyX_Sourcecode_Php_Sandbox_Exception('Found using of denied var '.$this->token[1]);
			}
			
			// String-Token
			if($this->isDeniedString()) {
				throw new CurlyX_Sourcecode_Php_Sandbox_Exception('Found denied string '.$this->token[1]);
			}
			
			// Dateieinbindung verbieten
			if($this->getOption(self::OPT_DENY_INCLUSION, false) and $this->isInclusionToken()) {
				throw new CurlyX_Sourcecode_Php_Sandbox_Exception('Found denied inclusion '.$this->token[1]);
			}
			
			// Variable Variable
			if($this->getOption(self::OPT_DENY_VARIABLE_VARS, true) and $this->isVariableVar()) {
				throw new CurlyX_Sourcecode_Php_Sandbox_Exception('Found denied variable var '.$this->token[1]);
			}
			
			// Methodenaufruf über Variable
			if($this->getOption(self::OPT_DENY_VARIABLE_METHOD_CALLS, true) and $this->isVariableMethodCall()) {
				throw new CurlyX_Sourcecode_Php_Sandbox_Exception('Found variable method call '.$this->token[1]);
			}
		}
	}
	
	/**
	 * @return boolean
	 * @param string Element
	 * @param array Liste
	 */
	protected function isInList($item, $list) {
		static $replace=array(
			'\\*' => '.*',
			'\\?' => '.',
			'\\[' => '[',
			'\\]' => ']'
		);
		
		foreach($list as $key=>$value) {
			if(
				strpos($value, '*')!==false or
				strpos($value, '?')!==false or
				strpos($value, '[')!==false
			) {
				$pattern=strtr(preg_quote($value, '#'), $replace);
				if(preg_match('#^'.$pattern.'$#i', $item)>0) {
					return(true);
				}
			}
			else if($value==$item) {
				return(true);
			}
		}
		return(false);
	}
	
	/**
	 * @return boolean
	 */
	protected function isDeniedToken() {
		return(
			in_array($this->token[0], $this->getOption(self::OPT_TOKENS, array()), true) xor
			$this->getOption(self::OPT_TOKENS_CHECK, self::WHITELIST)==self::WHITELIST
		);
	}
	
	/**
	 * @return boolean
	 */
	protected function isDeniedVar() {
		if(!$this->isTokenOf($this->token, T_VARIABLE)) {
			return(false);
		}
		
		return(
			$this->isInList(substr($this->token[1], 1), $this->getOption(self::OPT_VARS, array())) xor
			$this->getOption(self::OPT_VAR_CHECK, self::WHITELIST)==self::WHITELIST
		);
	}
	
	/**
	 * @return boolean
	 */
	protected function isDeniedString() {
		if(!$this->isStringToken($this->token)) {
			return(false);
		}
		
		return(
			$this->isInList($this->token[0], $this->getOption(self::OPT_STRINGS, array())) xor
			$this->getOption(self::OPT_STRINGS_CHECK, self::WHITELIST)==self::WHITELIST
		);
	}
	
	/**
	 * @return boolean
	 */
	protected function isInclusionToken() {
		return($this->isTokenAnyOf($this->token, $this->getOption(self::OPT_INCLUSION_TOKENS, array())));
	}
	
	/**
	 * @return boolean
	 */
	protected function isVariableVar() {
		if(!$this->isTokenOf($this->token, T_VARIABLE)) {
			return(false);
		}
		
		$index=$this->index;
		// Die vorigen Tokens überprüfen
		for($index--; $index>0; $index--) {
			if($this->isWhitespaceTokenAt($index)) {
				continue;
			}
			else if($this->isUnknownTokenAt($index) and $this->isTokenValueAt($index, '$')) {
				return(true);
			}
			else {
				break;
			}
		}
		return(false);
	}
	
	/**
	 * @return boolean
	 */
	protected function isVariableMethodCall() {
		if(!$this->isTokenOf($this->token, T_VARIABLE)) {
			return(false);
		}
		
		$index=$this->index;
		// Die nachfolgenden Tokens überprüfen
		for($index++, $c=$this->tokens->count(); $index<$c; $index++) {
			if($this->isWhitespaceTokenAt($index)) {
				continue;
			}
			else if($this->isTokenOfAt($index, T_PARENTHESIS_OPEN)) {
				return(true);
			}
			else {
				break;
			}
		}
		
		return(false);
	}
	
}