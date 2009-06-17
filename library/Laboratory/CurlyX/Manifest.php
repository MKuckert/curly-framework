<?php

/**
 * CurlyX_Manifest
 * 
 * Stellt den Inhalt einer Manifest-Datei zur Verfügung.
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Installer
 * @since 1.0 - 19.12.2008
 * @version 1.0
 * @todo Getter-Methoden
 */
class CurlyX_Manifest {
	
	/**
	 * @desc Namensraum der Manifest-Daten im XML-Dokument.
	 * @todo Benötigt?
	 */
	const MANIFEST_NAMESPACE='http://schema.curlybracket.de/Installer_v0.1.xsd';
	
	/**
	 * @var DOMDocument Die intern verwendete XML-Dokument-Instanz
	 */
	private $_doc=NULL;
	
	/**
	 * @var integer Zu verwendende Flags beim Laden der Inhalte.
	 */
	private $_loadFlags=0;
	
	/**
	 * @var boolean Flag, ob die XML-Dokumente auf Validität überprüft werden
	 *  sollen.
	 */
	private $_validateSchema=false;
	
	/**
	 * @var string Pfad zu dem zu verwendenden Schema-Dokument.
	 */
	private $_schemaValidationSource=self::MANIFEST_NAMESPACE;
	
	/**
	 * Gibt die zu verwendenden Flags beim Laden der Inhalte zurück.
	 * 
	 * @return integer
	 */
	public function getLoadFlags() {
		return($this->_loadFlags);
	}
	
	/**
	 * Setzt die zu verwendenden Flags beim Laden der Inhalte.
	 * 
	 * @return void
	 * @param integer
	 */
	public function setLoadFlags($value) {
		$this->_loadFlags=(int)$value;
	}
	
	/**
	 * Gibt das Flag, ob die XML-Dokumente auf Validität überprüft werden
	 *  sollen, zurück.
	 * 
	 * @return boolean
	 */
	public function getValidateSchema() {
		return($this->_validateSchema);
	}
	
	/**
	 * Setzt das Flag, ob die XML-Dokumente auf Validität überprüft werden
	 *  sollen.
	 * 
	 * @return void
	 * @param boolean
	 */
	public function setValidateSchema($value) {
		$this->_validateSchema=(boolean)$value;
	}
	
	/**
	 * Gibt den Pfad zu dem zu verwendenden Schema-Dokument zurück.
	 * 
	 * @return string
	 */
	public function getSchemaValidationSource() {
		return($this->_schemaValidationSource);
	}
	
	/**
	 * Setzt den Pfad zu dem zu verwendenden Schema-Dokument.
	 * 
	 * @return void
	 * @param string
	 */
	public function setSchemaValidationSource($value) {
		$this->_schemaValidationSource=(string)$value;
	}
	
	/**
	 * Lädt die Datei mit dem übergebenen Dateinamen.
	 * 
	 * @return boolean
	 * @param string
	 */
	public function loadFile($filename) {
		$this->createDoc();
		
		if(!$this->_doc->load($filename, $this->_loadFlags)) {
			return(false);
		}
		return($this->validateSchema());
	}
	
	/**
	 * Lädt die übergebene Zeichenkette
	 * 
	 * @return boolean
	 * @param string
	 */
	public function loadString($string) {
		$this->createDoc();
		
		if(!$this->_doc->loadXML($string, $this->_loadFlags)) {
			return(false);
		}
		return($this->validateSchema());
	}
	
	/**
	 * Überprüft, falls aktiviert, ob das XML-Dokument valide ist.
	 * 
	 * @return void
	 */
	protected function validateSchema() {
		if(!$this->getValidateSchema()) {
			return(true);
		}
		
		return($this->_doc->schemaValidate($this->getSchemaValidationSource()));
	}
	
	/**
	 * Erstellt die intern zu verwendende XML-Dokument-Instanz.
	 * 
	 * @return void
	 */
	protected function createDoc() {
		$this->_doc=new DOMDocument();
		$this->_doc->preserveWhiteSpace=false;
	}
	
	/**
	 * Gibt die Liste der referenzierten Dateinamen zurück.
	 * 
	 * @return array
	 */
	public function getFileList() {
		$list=array();
		$filesNodes=$this->_doc
			->getElementsByTagName('package')->item(0)
			->getElementsByTagName('files');
		if($filesNodes->length>0) {
			foreach($filesNodes->item(0)->childNodes as $fileNode) {
				$list[]=array(
					'name'	=> (string)$fileNode->getAttribute('name'),
					'base'	=> (string)$fileNode->getAttribute('base')
				);
			}
		}
		return($list);
	}
	
}
