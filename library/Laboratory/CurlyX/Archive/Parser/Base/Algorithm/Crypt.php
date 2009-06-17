<?php

require_once "CurlyX/Archive/Compiler/Base/Algorithm/Interface.php";

/**
 * CurlyX_Archive_Parser_Base_Algorithm_Crypt
 *
 * Dieser Algorithmus entschlüsselt den Dateninhalt mit dem Datensatznamen.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Parser.Base.Algorithm
 * @since 1.0 - 12.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Parser_Base_Algorithm_Crypt implements CurlyX_Archive_Parser_Base_Algorithm_Interface {
	
	const IDENTIFIER=0x2;
	
	const HEADER_PREFIX='CRYPT[';
	const HEADER_POSTFIX=']';
	
	/**
	 * Gibt einen Integer zurück, der diesen Algorithmus identifiziert.
	 * 
	 * @return integer
	 */
	public function getIdentifier() {
		return(self::IDENTIFIER);
	}
	
	/**
	 * @var string Die zu verwendende Cipher.
	 */
	private $_cipher='';
	
	/**
	 * Gibt die zu verwendende Cipher zurück.
	 * 
	 * @return string
	 */
	public function getCipher() {
		return($this->_cipher);
	}
	
	/**
	 * Gibt die Original-Daten für die übergebenen Daten zurück.
	 * 
	 * @return string
	 * @param string Der verwendete Name des Datensatzes
	 * @param string Die Daten
	 */
	public function getDatasetContents($name, $data) {
		$name=$this->getDatasetName($name);
		
		$ivSize=mcrypt_get_iv_size($this->_cipher, MCRYPT_MODE_CFB);
		$iv=substr($data, 0, $ivSize);
		
		$data=substr($data, $ivSize);
		$data=mcrypt_decrypt($this->_cipher, $name, $data, MCRYPT_MODE_CFB, $iv);
		return($data);
	}
	
	/**
	 * Gibt den für den übergebenen Datensatz zu verwendenden Namen zurück. Diese
	 * Methode entspricht in der Rückgabe der gleichnamigen des
	 * CurlyX_Archive_Compiler_Base_Algorithm_Interface-Interface.
	 * 
	 * @return string
	 * @param string
	 */
	public function getDatasetName($name) {
		return(sha1($name, true));
	}
	
	/**
	 * Gibt einen boolschen Wert zurück, ob die Namen der verfügbaren Einträge
	 * im zurückgegeben werden kann. Dies ist zum Beispiel nicht der Fall, wenn
	 * die Datei verschlüsselt ist.
	 *
	 * @return boolean
	 */
	public function canGetEntryNames() {
		return(false);
	}
	
	/**
	 * Wird aufgerufen, bevor dieser Algorithmus von einem Parser verwendet
	 * wird.
	 * 
	 * @return void
	 * @param CurlyX_Archive_Parser_Base
	 * @param integer Der Identifier, mit dem dieser Algorithmus identifiziert
	 *  wurde.
	 */
	public function addToParser(CurlyX_Archive_Parser_Base $parser, $id) {
		$header=$parser->additionalHeaderdata;
		
		$prefix=str_replace(array('[', ']'), array('\\[', '\\]'), self::HEADER_PREFIX);
		$postfix=str_replace(array('[', ']'), array('\\[', '\\]'), self::HEADER_POSTFIX);
		
		if(!preg_match('/'.$prefix.'([a-z0-9\\-]+)'.$postfix.'/i', $header, $matches, PREG_OFFSET_CAPTURE)) {
			throw new CurlyX_Archive_Exception('Unable to match the additional header');
		}
		
		$this->_cipher=$matches[1][0];
		
		// Daten aus dem Header löschen. So kann der Chain-Algorithmus mehrere
		// Crypt-Algorithmen verknüpfen.
		$pos=$matches[1][1];
		$end=strpos($header, self::HEADER_POSTFIX, $pos);
		$parser->additionalHeaderdata=
			substr($header, 0, $pos-strlen(self::HEADER_PREFIX))
			.substr($header, $end+strlen(self::HEADER_POSTFIX));
	}
	
}