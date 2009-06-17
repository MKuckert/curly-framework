<?php

require_once "CurlyX/Archive/Parser/Base/Algorithm/Interface.php";
require_once "CurlyX/Archive/Parser/Base/Algorithm/Plain.php";
require_once "CurlyX/Archive/Parser/Base/Registry.php";

/**
 * CurlyX_Archive_Parser_Base
 *
 * Einfache Archiv-Parser-Implementation f¸r flache, indizierte Archive. Dieser
 * Parser korrespondiert zum Compiler {@link CurlyX_Archive_Compiler_Base}.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Parser
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Parser_Base implements CurlyX_Archive_Parser_Interface {

	const Size=0;
	const Offset=1;
	const Name=2;
	
	/**
	 * @desc Die "magischen Bytes" dieses Formats.
	 */
	const MAGIC_BYTES='archv-base';

	/**
	 * @var CurlyX_Archive_Parser_Base_Algorithm_Interface Der zu verwendende Parser-Algorithmus
	 */
	private $_algorithm=NULL;
	
	/**
	 * @var string Magic Bytes des Archivs
	 */
	public $magicBytes='';
	
	/**
	 * @var string Verwendete Compiler-Version f¸r das Archiv.
	 */
	public $compilerVersion='';
	
	/**
	 * @var string Signatur des verwendeten Compilers
	 */
	public $compilerSignature='';
	
	/**
	 * @var string Weitere Headerdaten, zum Beispiel f¸r den verwendeten Algorithmus.
	 */
	public $additionalHeaderdata='';
	
	/**
	 * @var Curly_Stream Daten-Eingabestrom
	 */
	private $stream;
	
	/**
	 * @var integer Anzahl Datens‰tze im Archiv
	 */
	private $entries=0;
	
	/**
	 * @var array Bereits extrahierte Metadaten
	 */
	private $metadata=array();
	
	/**
	 * Gibt den zu verwendenden Parser-Algorithmus zur¸ck.
	 * 
	 * @return CurlyX_Archive_Parser_Base_Algorithm_Interface
	 */
	public function getAlgorithm() {
		return($this->_algorithm);
	}
	
	/**
	 * Setzt den zu verwendenden Parser-Algorithmus.
	 * 
	 * @return void
	 * @param CurlyX_Archive_Parser_Base_Algorithm_Interface
	 */
	protected function setAlgorithm(CurlyX_Archive_Parser_Base_Algorithm_Interface $algo) {
		$this->_algorithm=$algo;
	}
	
	/**
	 * Gibt die Original-Daten f¸r die ¸bergebenen Daten zur¸ck.
	 * 
	 * @return string
	 * @param string Der verwendete Name des Datensatzes
	 * @param string Die Daten
	 */
	protected function getDatasetContents($name, $data) {
		return($this->getAlgorithm()->getDatasetContents($name, $data, $this));
	}
	
	/**
	 * Gibt den f¸r den ¸bergebenen Namen zu verwendenden Namen zur¸ck. Diese
	 * Methode entspricht in der R¸ckgabe der gleichnamigen des
	 * CurlyX_Archive_Compiler_Base_Algorithm_Interface-Interface.
	 * 
	 * @return string
	 * @param string
	 */
	protected function getDatasetName($name) {
		return($this->getAlgorithm()->getDatasetName($name, $this));
	}
	
	/**
	 * Parst das Archiv mit dem ¸bergebenen Dateinamen.
	 * 
	 * @throws CurlyX_Archive_Exception Wenn ein Fehler auftritt
	 * @return void
	 * @param string
	 */
	public function parse($file) {
		$this->metadata=array();
			
		$this->openStream($file);
		$this->readHeader();
		$this->readMetadata();
	}

	/**
	 * L‰dt den Datensatz mit dem ¸bergebenen Namen und gibt den Inhalt zur¸ck.
	 * 
	 * @throws CurlyX_Archive_Exception Wenn der Datensatz mit dem ¸bergebenen
	 *  Namen nicht gefunden werden kann.
	 * @return string
	 * @param string
	 */
	public function load($name) {
		$archiveName=$this->getDatasetName($name);
		
		if(!isset($this->metadata[$archiveName])) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('Entry '.$name.' not found');
		}
			
		$dataset=$this->metadata[$archiveName];
		$this->stream->setPosition($dataset[self::Offset]);
		$content=$this->stream->read($dataset[self::Size]);
		$content=$this->getDatasetContents($name, $content);
		return($content);
	}
	
	/**
	 * Gibt einen boolschen Wert zur¸ck, ob die Namen der verf¸gbaren Eintr‰ge
	 * im zur¸ckgegeben werden kann. Dies ist zum Beispiel nicht der Fall, wenn
	 * die Datei verschl¸sselt ist.
	 *
	 * @return boolean
	 */
	public function canGetEntryNames() {
		return($this->getAlgorithm()->canGetEntryNames());
	}
	
	/**
	 * Gibt ein Array mit den Namen der verf¸gbaren Eintr‰ge zur¸ck.
	 * 
	 * @throws CurlyX_Archive_Exception
	 * @return array
	 */
	public function getEntryNames() {
		if(!$this->getAlgorithm()->canGetEntryNames()) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('Unable to get the entry names.');
		}
		return(array_keys($this->metadata));
	}
	
	/**
	 * ‹berpr¸ft, ob die ¸bergebene Zeichenkette den Magic Bytes dieser
	 * Parser-Klasse entspricht.
	 * 
	 * Diese Methode wird von der Parser-Registry verwendet und erh‰lt als
	 * Parameter die vordersten 32 Bytes einer Archiv-Datei.
	 * 
	 * @return boolean
	 * @param string
	 */
	static public function containsMagicBytes($bytes) {
		return(substr($bytes, 0, strlen(self::MAGIC_BYTES))==self::MAGIC_BYTES);
	}
	
	/**
	 * ÷ffnet den Eingabe-Datenstrom
	 * 
	 * @return void
	 * @param string Der Pfad zu der Eingabedatei
	 */
	protected function openStream($file) {
		$this->stream=new Curly_Stream($file, 'rb');
	}

	/**
	 * Lieﬂt die Headerdaten aus dem Archiv.
	 * 
	 * @throws CurlyX_Archive_Exception Wenn ein Fehler auftritt
	 * @return void
	 */
	protected function readHeader() {
		$this->magicBytes=$this->stream->read(10);
		$this->compilerVersion=$this->stream->read(3);
		
		$compilerSignatureLen=$this->stream->readByte();
		$this->compilerSignature=$this->stream->read($compilerSignatureLen);
		
		$algorithmIdentifier=$this->stream->readInteger();
		
		$additionalHeaderLen=$this->stream->readInteger();
		$this->additionalHeaderdata=$this->stream->read($additionalHeaderLen);
		
		$this->entries=$this->stream->readByte();
		
		$algo=$this->createAlgorithm($algorithmIdentifier);
		if($algo===NULL) {
			require_once "CurlyX/Archive/Exception.php";
			throw new CurlyX_Archive_Exception('There is no algorithm for the identifier '.$algorithmIdentifier.' registered');
		}
		$this->setAlgorithm($algo);
	}
	
	/**
	 * Erstellt den Algorithmus f¸r den ¸bergebenen Identifier.
	 * 
	 * @return CurlyX_Archive_Parser_Base_Algorithm_Interface
	 * @param integer Algorithmus-Identifier
	 */
	protected function createAlgorithm($id) {
		// Unteren 2 Byte extrahieren
		$id=$id&0xFFFF;
		
		foreach(CurlyX_Archive_Parser_Base_Registry::getRegisteredAlgorithms() as $algorithm) {
			if($algorithm->getIdentifier()==$id) {
				$algorithm=clone $algorithm;
				$algorithm->addToParser($this, $id);
				return($algorithm);
			}
		}
		return(NULL);
	}

	/**
	 * Lieﬂt die Metadaten aus dem Archiv aus.
	 * 
	 * @return void
	 */
	protected function readMetadata() {
		for($i=0; $i<$this->entries; $i++) {
			$namelen=$this->stream->readByte();
			$size=$this->stream->readInteger();
			$offset=$this->stream->readInteger();
			$info=$this->stream->readByte();
			$name=$this->stream->read($namelen);
			
			$this->metadata[$name]=array(
				self::Size		=> $size,
				self::Offset	=> $offset,
				self::Name		=> $name
			);
		}
	}

}