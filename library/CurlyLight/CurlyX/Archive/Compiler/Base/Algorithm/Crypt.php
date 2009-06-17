<?php

require_once "CurlyX/Archive/Compiler/Base/Algorithm/Interface.php";

/**
 * CurlyX_Archive_Compiler_Base_Algorithm_Crypt
 *
 * Dieser Algorithmus verschlüsselt den Dateninhalt mit dem Datensatznamen.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Compiler.Base.Algorithm
 * @since 1.0 - 12.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Compiler_Base_Algorithm_Crypt implements CurlyX_Archive_Compiler_Base_Algorithm_Interface {
	
	const IDENTIFIER=0x2;
	
	const HEADER_PREFIX='CRYPT[';
	const HEADER_POSTFIX=']';
	
	/**
	 * @var CurlyX_Archive_Compiler_Base Compiler, dem dieser Algorithmus zugeordnet ist.
	 */
	private $_compiler=NULL;
	
	/**
	 * @var string Zu verwendender Verschlüsselungsalgorithmus
	 */
	private $_cipher;
	
	/**
	 * @var array Bereits erzeugte Initialisierungsvektoren
	 */
	private $_ivs=array();
	
	/**
	 * Gibt dem diesen Algorithmus zugeordneten Compiler zurück.
	 * 
	 * @return CurlyX_Archive_Compiler_Base
	 */
	public function getCompiler() {
		return($this->_compiler);
	}
	
	/**
	 * Gibt den zu verwendenden Verschlüsselungsalgorithmus zurück.
	 * 
	 * @return string
	 */
	public function getCipher() {
		return($this->_cipher);
	}
	
	/**
	 * Konstruktor
	 * 
	 * @param CurlyX_Archive_Compiler_Base Setzt dem diesen Algorithmus zugeordneten Compiler.
	 * @param string Zu verwendender Verschlüsselungsalgorithmus
	 */
	public function __construct(CurlyX_Archive_Compiler_Base $compiler, $cipher) {
		$this->_compiler=$compiler;
		$this->_cipher=$cipher;
		
		$compiler->addAdditionalHeaderdata(self::HEADER_PREFIX.$cipher.self::HEADER_POSTFIX);
	}
	
	/**
	 * Gibt einen Integer zurück, der diesen Algorithmus identifiziert.
	 * 
	 * @return integer
	 */
	public function getIdentifier() {
		return(self::IDENTIFIER);
	}
	
	/**
	 * Gibt die zu verwendenden Daten für den übergebenen Datensatz zurück.
	 * 
	 * @return string
	 * @param string Der verwendete Name des Datensatzes
	 * @param CurlyX_Archive_Dataset_Interface Der Datensatz
	 */
	public function getDatasetContents($name, CurlyX_Archive_Dataset_Interface $dataset) {
		if(!isset($this->_ivs[$name])) {
			$ivSize=mcrypt_get_iv_size($this->getCipher(), MCRYPT_MODE_CFB);
			$this->_ivs[$name]=mcrypt_create_iv($ivSize, MCRYPT_RAND);
		}
		
		$data=mcrypt_encrypt($this->getCipher(), $name, $dataset->getContents(), MCRYPT_MODE_CFB, $this->_ivs[$name]);
		$data=$this->_ivs[$name].$data;
		return($data);
	}
	
	/**
	 * Gibt die Anzahl Bytes zurück, die ein Aufruf von
	 * {@link getDatasetContents} zurückgegeben würde.
	 * 
	 * @return integer
	 * @param string Der verwendete Name des Datensatzes
	 * @param CurlyX_Archive_Dataset_Interface Der verwendete Datensatz
	 */
	public function getDatasetSize($name, CurlyX_Archive_Dataset_Interface $dataset) {
		return(strlen($this->getDatasetContents($name, $dataset)));
	}
	
	/**
	 * Gibt den für den übergebenen Datensatz zu verwendenden Namen zurück.
	 * 
	 * @return string
	 * @param CurlyX_Archive_Dataset_Interface
	 */
	public function getDatasetName(CurlyX_Archive_Dataset_Interface $dataset) {
		return(sha1($dataset->getName(), true));
	}
	
}