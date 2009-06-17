<?php

require_once 'Curly/Sourcecode/Php/Tokens.php';

/**
 * Curly_Util_Tokens
 *
 * Ersetzt innerhalb einer Curly_Sourcecode_Php_Tokens-Instance gewisse Tokens.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package Curly.Sourcecode.Php
 * @since 1.0 - 30.12.2008
 * @version 1.0
 */
class Curly_Util_Tokens {
	
	/**
	 * @var array Ersatztokens für die in den Schlüssel stehenden Tokens.
	 */
	private $_replaceTokens=array();
	
	/**
	 * @var integer Mode für die mkdir-Funktion in der
	 * {@link replaceRequireTokensInDir}-Methode.
	 */
	private $_mkdirMode=0777;
	
	/**
	 * Gibt die Ersatztokens für die in den Schlüssel stehenden Tokens zurück.
	 * 
	 * @return array
	 */
	public function getReplaceTokens() {
		return($this->_replaceTokens);
	}
	
	/**
	 * Setzt die Ersatztokens für die in den Schlüssel stehenden Tokens.
	 * 
	 * @return void
	 * @param array
	 */
	public function setReplaceTokens(array $value) {
		$this->_replaceTokens=$value;
	}
	
	/**
	 * Gibt den Mode für die mkdir-Funktion in der
	 * {@link replaceRequireTokensInDir}-Methode zurück.
	 * 
	 * @return integer
	 */
	public function getMkdirMode() {
		return($this->_mkdirMode);
	}
	
	/**
	 * Setzt den Mode für die mkdir-Funktion in der
	 * {@link replaceRequireTokensInDir}-Methode.
	 * 
	 * @return void
	 * @param integer
	 */
	public function setMkdirMode($value) {
		$this->_mkdirMode=$value;
	}
	
	/**
	 * Ersetzt alle require, require_once, include und include_once-Aufrufe
	 * durch entsprechende Aufrufe der übergebenen Funktionen.
	 *
	 * @return Curly_Sourcecode_Php_Tokens
	 * @param mixed
	 * @param string Ersatz für den require-Token
	 * @param string Ersatz für den require_once-Token
	 * @param string Ersatz für den include-Token
	 * @param string Ersatz für den include_once-Token
	 */
	public function replaceRequireTokens($phpTokens, $require=NULL, $requireOnce=NULL, $include=NULL, $includeOnce=NULL) {
		if($phpTokens instanceof Curly_Sourcecode_Php_Tokens) {
			$tokens=$phpTokens->getTokens();
		}
		else if($phpTokens instanceof Curly_Types_Collection_Serial) {
			$tokens=$phpTokens;
		}
		else {
			$tmp=new Curly_Sourcecode_Php_Tokens();
			$tmp->parse($phpTokens);
			$phpTokens=$tmp;
			$tokens=$phpTokens->getTokens();
		}
		
		$replace=$this->getReplaceTokens();
		if($require!==NULL) {
			$replace[T_REQUIRE]=$require;
		}
		if($requireOnce!==NULL) {
			$replace[T_REQUIRE_ONCE]=$requireOnce;
		}
		if($include!==NULL) {
			$replace[T_INCLUDE]=$include;
		}
		if($includeOnce!==NULL) {
			$replace[T_INCLUDE_ONCE]=$includeOnce;
		}
		
		for($i=0; $i<$tokens->count(); $i++) {
			$token=$tokens->get($i);
			$tokenID=$token[0];
				
			if(
				isset($replace[$tokenID])
				and (
					$tokenID==T_REQUIRE
					or $tokenID==T_REQUIRE_ONCE
					or $tokenID==T_INCLUDE
					or $tokenID==T_INCLUDE_ONCE
				)
			) {
				$newToken=array(T_STRING, $replace[$tokenID]);
				$tokens->set($i, $newToken);

				$newToken=array(T_PARENTHESIS_OPEN, '(');
				$tokens->insertAt($i+1, $newToken);

				// Bis zum Semikolen weiterlaufen
				for(; $i<$tokens->count(); $i++) {
					$token=$tokens->get($i);
					if($token[0]==T_SEMICOLON) {
						$newToken=array(T_PARENTHESIS_CLOSE, ')');
						$tokens->insertAt($i, $newToken);
						break;
					}
				}
			}
		}
		
		return($phpTokens);
	}
	
	/**
	 * Ersetzt alle require, require_once, include und include_once-Aufrufe
	 * durch entsprechende Aufrufe der übergebenen Funktionen in allen Dateien
	 * eines Verzeichnisses. Dieses Verzeichnis wird rekursiv durchwandert.
	 * 
	 * @return boolean
	 * @param string Pfad zum zu verwendenden Verzeichnis
	 * @param string Pfad zum zu verwendenden Ziel-Verzeichnis, d.h. dort, wo
	 *  die Dateien mit ersetzten Tokens abgelegt werden sollen.
	 * @param string Ersatz für den require-Token
	 * @param string Ersatz für den require_once-Token
	 * @param string Ersatz für den include-Token
	 * @param string Ersatz für den include_once-Token
	 */
	public function replaceRequireTokensInDir($sourceDir, $targetDir, $require=NULL, $requireOnce=NULL, $include=NULL, $includeOnce=NULL) {
		$iterator=new RegexIterator(
			new RecursiveIteratorIterator(
				new RecursiveDirectoryIterator(
					$sourceDir
				),
				RecursiveIteratorIterator::SELF_FIRST
			),
			'/\.php$/i',
			RegexIterator::MATCH,
			RegexIterator::USE_KEY
		);
		
		$sourceDirLen=strlen($sourceDir);
		
		foreach($iterator as $item) {
			$path=$item->getPathName();
			$relPath=substr($path, $sourceDirLen);
			
			$target=$targetDir.DIRECTORY_SEPARATOR.$relPath;
			
			$tokens=$this->replaceRequireTokens(file_get_contents($path), $require, $requireOnce, $include, $includeOnce);
			
			$tdir=dirname($target);
			if(!is_dir($tdir)) {
				if(!mkdir($tdir, $this->getMkdirMode(), true)) {
					return(false);
				}
			}
			if(!$tokens->renderFile($target)) {
				return(false);
			}
		}
		
		return(true);
	}

}
