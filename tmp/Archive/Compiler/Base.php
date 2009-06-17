<?php

/**
 * CurlyX_Archive_Compiler_Base
 *
 * Einfacher Archiv-Compiler zum Erstellen eines flachen, indizierten Archives.
 *
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyX.Archive.Compiler
 * @since 1.0 - 11.12.2008
 * @version 1.0
 */
class CurlyX_Archive_Compiler_Base implements CurlyX_Archive_Compiler_Interface {

	const HeaderLength = 32;
	const DatasetLength = 10;

	const FileSignature = "[@]";
	const CompilerSignature = "ac1MartinKuckert";
	const FileVersion = "0.1";

	const Dataset = 0;
	const Offset = 1;
	const Size = 2;
	const Name = 3;

	public $outputPath = '';

	private $datasets = array();

	private $nameLens = 0;
	private $stream;
	
	private $ivs = array();

	public $flags = CurlyX_Archive::TYPE_FLAT;

	public function hasFlag( $flag ) {
		return( ( $this->flags & $flag )==$flag );
	}

	public function addDataset( CurlyX_Archive_Dataset_Interface $dataset ) {
		$name = $dataset->getName();
		if( $this->hasFlag( CurlyX_Archive::HASH_NAMES ) ) {
			$name = md5( $name );
		}

		$this->datasets[$name] = array(
		self::Dataset	=> $dataset,
		self::Offset	=> 0,
		self::Size		=> 0,
		self::Name		=> $name
		);
	}

	public function addDatasets( $datasets ) {
		if( !( is_array( $datasets ) or $datasets instanceof Traversable ) ) {
			$datasets = func_get_args();
		}

		foreach( $datasets as $dataset ) {
			$this->addDataset( $dataset );
		}
	}

	public function compile() {
		$this->nameLens = 0;
			
		$this->sumNameLength();
		$this->calculateOffsets();
			
		$this->openStream();
		$this->writeHeader();
		$this->writeMetadata();
		$this->writeFiles();
		$this->closeStream();
	}
	
	protected function getDatasetContents( $entry ) {
		$content = $entry[self::Dataset]->getContents();
		$content = $this->compress( $content );
		$content = $this->encrypt( $content, $entry[self::Name] );
		return( $content );
	}

	protected function getDatasetContentSize( $entry ) {
		return( strlen( $this->getDatasetContents( $entry ) ) );
	}

	public function compress( $data ) {
		if( $this->hasFlag( CurlyX_Archive::TYPE_GZ_COMPRESSED ) ) {
			$data = gzcompress( $data, 9 );
		}
		return( $data );
	}

	public function encrypt( $data, $key ) {
		if( $this->hasFlag( CurlyX_Archive::CRYPT_BLOWFISH ) ) {
			if( !isset( $this->ivs[$key] ) ) {
				$ivSize = mcrypt_get_iv_size( MCRYPT_BLOWFISH,  MCRYPT_MODE_CFB );
				$this->ivs[$key] = mcrypt_create_iv( $ivSize, MCRYPT_RAND );
			}
			$data = mcrypt_encrypt( MCRYPT_BLOWFISH, $key, $data,  MCRYPT_MODE_CFB, $this->ivs[$key] );
			$data = $this->ivs[$key].$data;
		}
		return( $data );
	}

	protected function sumNameLength() {
		foreach( $this->datasets as $name=>$entry ) {
			$this->nameLens += strlen( $entry[self::Name] );
		}
	}

	protected function calculateOffsets() {
		$count = count( $this->datasets );
		$curOffset = self::HeaderLength + $count * self::DatasetLength + $this->nameLens;
			
		foreach( $this->datasets as $name=>$entry ) {
			$size = $this->getDatasetContentSize( $entry );
			$entry[self::Offset] = $curOffset;
			$entry[self::Size] = $size;

			$this->datasets[$name] = $entry;

			$curOffset += $size;
		}
	}

	protected function openStream() {
		$this->stream = new Stream( $this->outputPath, 'wb' );
		$this->stream->setEndian( Stream::LittleEndian );
	}

	protected function writeHeader() {
		$this->stream->write( self::FileSignature );
		$this->stream->write( self::FileVersion );
		$this->stream->writeInteger( intval( $this->flags ) );
		$this->stream->write( self::CompilerSignature );
		$this->stream->write( str_repeat( "\0", 5 ) );
		$this->stream->writeByte( count( $this->datasets ) );
	}

	protected function writeMetadata() {
		foreach( $this->datasets as $name=>$entry ) {
			$this->stream->writeByte( strlen( $entry[self::Name] ) );
			$this->stream->writeInteger( $entry[self::Size] );
			$this->stream->writeInteger( $entry[self::Offset] );
			$this->stream->writeByte( 0 );
			$this->stream->write( $entry[self::Name] );
		}
	}

	protected function writeFiles() {
		foreach( $this->datasets as $name=>$entry ) {
			$content = $this->getDatasetContents( $entry );
			$this->stream->write( $content );
		}
	}

	protected function closeStream() {
		$this->stream = null;
	}

}

?>