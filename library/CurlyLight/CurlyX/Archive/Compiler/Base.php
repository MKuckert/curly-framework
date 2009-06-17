<?php

require_once "CurlyX/Archive/Compiler/Interface.php";
require_once "CurlyX/Archive/Compiler/Base/Algorithm/Interface.php";
require_once "CurlyX/Archive/Compiler/Base/Algorithm/Plain.php";
require_once "CurlyX/Archive/Dataset/Interface.php";

/**
 * CurlyX_Archive_Compiler_Base
 *
 * Einfacher Archiv-Compiler, der eine einfache indizierte Archiv-Datei mit
 * einem Compiler-Algorithmus erzeugt. 
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Compiler
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Compiler_Base implements CurlyX_Archive_Compiler_Interface, CurlyX_Archive_Compiler_Base_Algorithm_Interface {
	
	const Dataset = 0;
	const Offset = 1;
	const Size = 2;
	const Name = 3;
	
	/**
	 * @desc Die Länge der Metadaten zu einem Datensatz.
	 */
	const DATASET_METADATA_LENGTH=10;
	
	/**
	 * @desc Die "magischen Bytes"
	 */
	const MAGIC_BYTES='archv-base';
	
	/**
	 * @desc Die Version dieses Formates
	 */
	const VERSION='0.2';
	
	/**
	 * @var CurlyX_Archive_Compiler_Base_Algorithm_Interface Der zu verwendende Compiler-Algorithmus
	 */
	private $_algorithm=NULL;
	
	/**
	 * @var array Bereits hinzugefügte Datensätze
	 */
	private $datasets = array();
	
	/**
	 * @var string Weitere Headerdaten, zum Beispiel vom verwendete Algorithmus.
	 */
	private $additionalHeaderdata='';
	
	/**
	 * @var integer Die Gesamtlänge aller Datensatznamen
	 */
	private $nameLens=0;
	
	/**
	 * @var integer Name der insgesamt geschriebenen Headerdaten
	 */
	private $headerLen=0;
	
	/**
	 * @var Curly_Stream Ausgabedatenstrom
	 */
	private $stream=NULL;
	
	/**
	 * Gibt den zu verwendenden Compiler-Algorithmus zurück.
	 * 
	 * @return CurlyX_Archive_Compiler_Base_Algorithm_Interface
	 */
	public function getAlgorithm() {
		if($this->_algorithm==NULL) {
			$this->_algorithm=new CurlyX_Archive_Compiler_Base_Algorithm_Plain();
		}
		return($this->_algorithm);
	}
	
	/**
	 * Setzt den zu verwendenden Compiler-Algorithmus.
	 * 
	 * @return void
	 * @param CurlyX_Archive_Compiler_Base_Algorithm_Interface
	 */
	public function setAlgorithm(CurlyX_Archive_Compiler_Base_Algorithm_Interface $algo) {
		$this->_algorithm=$algo;
	}
	
	/**
	 * Gibt die Signatur dieses Compilers zurück.
	 * 
	 * @return string
	 */
	public function getSignature() {
		return('BASE_'.self::VERSION);
	}
	
	/**
	 * Gibt einen Integer zurück, der diesen Algorithmus identifiziert.
	 * 
	 * @return integer
	 */
	public function getIdentifier() {
		return($this->getAlgorithm()->getIdentifier());
	}
	
	/**
	 * Gibt die zu verwendenden Daten für den übergebenen Datensatz zurück.
	 * 
	 * @return string
	 * @param string Der verwendete Name des Datensatzes
	 * @param CurlyX_Archive_Dataset_Interface Der Datensatz
	 */
	public function getDatasetContents($name, CurlyX_Archive_Dataset_Interface $dataset) {
		return($this->getAlgorithm()->getDatasetContents($name, $dataset));
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
		return($this->getAlgorithm()->getDatasetSize($name, $dataset));
	}
	
	/**
	 * Gibt den für den übergebenen Datensatz zu verwendenden Namen zurück.
	 * 
	 * @return string
	 * @param CurlyX_Archive_Dataset_Interface
	 */
	public function getDatasetName(CurlyX_Archive_Dataset_Interface $dataset) {
		return($this->getAlgorithm()->getDatasetName($dataset));
	}
	
	/**
	 * Fügt diesem Compiler den übergebenen Datensatz hinzu.
	 * 
	 * @return void
	 * @param CurlyX_Archive_Dataset_Interface
	 */
	public function addDataset(CurlyX_Archive_Dataset_Interface $dataset) {
		$name=$this->getDatasetName($dataset);
		
		$this->datasets[$name] = array(
			self::Dataset	=> $dataset,
			self::Offset	=> 0,
			self::Size		=> 0,
			self::Name		=> $name
		);
	}
	
	/**
	 * Gibt die diesem Compiler hinzugefügten weiteren Headerdaten zurück.
	 * 
	 * @return string
	 */
	public function getAdditionalHeaderdata() {
		return($this->additionalHeaderdata);
	}
	
	/**
	 * Setzt die weiteren Headerdaten dieses Compilers.
	 * 
	 * @return void
	 * @param string
	 */
	protected function setAdditionalHeaderdata($value) {
		$this->additionalHeaderdata=(string)$value;
	}
	
	/**
	 * Fügt diesem Compiler weitere Headerdaten hinzu.
	 * 
	 * @return void
	 * @param string
	 */
	public function addAdditionalHeaderdata($data) {
		$this->additionalHeaderdata.=$data;
	}
	
	/**
	 * Kompiliert dieses Archiv.
	 *
	 * @throws CurlyX_Archive_Exception Bei einem Fehler
	 * @return boolean
	 * @param string Dateiname, unter dem das Archiv erstellt werden soll.
	 * @param integer Flags für den Kompiliervorgang.
	 */
	public function compile($file, $flags=0) {
		$this->nameLens=0;
		
		$header=$this->getAdditionalHeaderdata();
		
		$this->sumNameLength();
		$this->calculateOffsets();
			
		$this->openStream($file);
		$this->setAdditionalHeaderdata($header);
		$this->writeHeader();
		$this->writeMetadata();
		$this->writeDatasets();
		$this->closeStream();
	}
	
	/**
	 * Summiert die Länge aller Datensatznamen.
	 * 
	 * @return void
	 */
	protected function sumNameLength() {
		foreach($this->datasets as $name=>$entry) {
			$this->nameLens+=strlen($entry[self::Name]);
		}
	}
	
	/**
	 * Berechnet den Offset und die Länge für jeden Datensatz im Archiv.
	 * 
	 * @return void
	 */
	protected function calculateOffsets() {
		$count=count($this->datasets);
		$curOffset=$count*self::DATASET_METADATA_LENGTH+$this->nameLens;
		
		foreach($this->datasets as $name=>$entry) {
			$size=$this->getDatasetSize($entry[self::Name], $entry[self::Dataset]);
			$entry[self::Offset]=$curOffset;
			$entry[self::Size]=$size;
			
			$this->datasets[$name]=$entry;
			
			$curOffset+=$size;
		}
	}
	
	/**
	 * Öffnet den Ausgabe-Datenstrom
	 * 
	 * @return void
	 * @param string Pfad zu der zu verwendenden Ausgabedatei
	 */
	protected function openStream($file) {
		$this->stream=new Curly_Stream($file, 'wb');
		$this->stream->setEndian(Curly_Stream::LittleEndian);
	}
	
	/**
	 * Schreibt die Header-Daten in den Ausgabe-Datenstrom
	 * 
	 * @throws CurlyX_Archive_Exception Bei einem Fehler
	 * @return void
	 */
	protected function writeHeader() {
		$this->stream->write(self::MAGIC_BYTES);
		$this->stream->write(self::VERSION);
		
		$signature=$this->getSignature();
		$signatureLen=strlen($signature);
		if($signatureLen>0xFF) {
			require_once 'CurlyX/Archive/Exception.php';
			throw new CurlyX_Archive_Exception('The signature value is to long');
		}
		$this->stream->writeByte($signatureLen);
		$this->stream->write($signature);
		
		$this->stream->writeInteger($this->getAlgorithm()->getIdentifier());
		
		$additional=$this->getAdditionalHeaderdata();
		$additionalLen=strlen($additional);
		$this->stream->writeInteger($additionalLen);
		$this->stream->write($additional);
		$this->stream->writeByte(count($this->datasets));
		
		$this->headerLen=
			strlen(self::MAGIC_BYTES)+
			strlen(self::VERSION)+
			$signatureLen+
			$additionalLen+
			10 // 2*Integer + 2*Byte
		;
	}
	
	/**
	 * Schreibt die Metadaten zu den Datensätzen in den Ausgabe-Datenstrom
	 * 
	 * @return void
	 */
	protected function writeMetadata() {
		foreach($this->datasets as $name=>$entry) {
			$this->stream->writeByte(strlen($entry[self::Name]));
			$this->stream->writeInteger($entry[self::Size]);
			$this->stream->writeInteger($entry[self::Offset]+$this->headerLen);
			$this->stream->writeByte(0);
			$this->stream->write($entry[self::Name]);
		}
	}
	
	/**
	 * Schreibt die Datensätze in den Ausgabe-Datenstrom
	 * 
	 * @return void
	 */
	protected function writeDatasets() {
		foreach($this->datasets as $name=>$entry) {
			$content=$this->getDatasetContents($entry[self::Name], $entry[self::Dataset]);
			$this->stream->write($content);
		}
	}
	
	/**
	 * Schließt den Ausgabe-Datenstrom
	 * 
	 * @return void
	 */
	protected function closeStream() {
		$this->stream->flush();
		$this->stream=null;
	}
	
}
