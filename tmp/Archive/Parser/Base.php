<?php

/**
 * CurlyX_Archive_Parser_Base
 *
 * Einfache Archiv-Parser-Implementation für flache, indizierte Archive. Dieser
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

	const Size = 0;
	const Offset = 1;
	const Name = 2;

	public $inputFile = array();

	public $fileSignature = '';
	public $version = '';
	public $flags = 0;
	public $compilerSignature = '';

	private $stream;
	private $entries = 0;
	private $metadata = array();

	public function hasFlag( $flag ) {
		return( ( $this->flags & $flag )==$flag );
	}

	public function parse() {
		$this->metadata = array();
			
		$this->openStream();
		$this->readHeader();
		$this->readMetadata();
	}

	public function load( $name ) {
		if( $this->hasFlag( CurlyX_Archive::HASH_NAMES ) ) {
			$name = md5( $name );
		}

		if( !isset( $this->metadata[$name] ) ) {
			throw new Exception( 'Entry '.$name.' not found' );
		}
			
		$dataset = $this->metadata[$name];
		$this->stream->setPosition( $dataset[self::Offset] );
		$content = $this->stream->read( $dataset[self::Size] );
		$content = $this->decrypt( $content, $name );
		$content = $this->uncompress( $content );
		return( $content );
	}
	
	public function loadPHP( $name ) {
		echo 'loading '.$name."<br />";
		$start = microtime( true );
		$content = $this->load( $name );
		eval( $content );
		echo 'needed '.((microtime(true)-$start)*1000)."ms<br />";
	}

	public function uncompress( $data ) {
		if( $this->hasFlag( CurlyX_Archive::TYPE_GZ_COMPRESSED ) ) {
			$data = gzuncompress( $data );
		}
		return( $data );
	}

	public function decrypt( $data, $key ) {
		if( $this->hasFlag( CurlyX_Archive::CRYPT_BLOWFISH ) ) {
			$ivSize = mcrypt_get_iv_size( MCRYPT_BLOWFISH,  MCRYPT_MODE_CFB );
			$iv = substr( $data, 0, $ivSize );
			$data = substr( $data, $ivSize );
			$data = mcrypt_decrypt( MCRYPT_BLOWFISH, $key, $data,  MCRYPT_MODE_CFB, $iv );
			$data = rtrim( $data, "\0" );
		}
		return( $data );
	}

	protected function openStream() {
		$this->stream = new Stream( $this->inputFile, 'rb' );
	}

	protected function readHeader() {
		$this->fileSignature = $this->stream->read( 3 );
		$this->version = $this->stream->read( 3 );
		$this->flags = $this->stream->readInteger();
		$this->compilerSignature = rtrim( $this->stream->read( 16 ) );
		$trash = $this->stream->read( 5 );
		$this->entries = $this->stream->readByte();
	}

	protected function readMetadata() {
		for( $i=0; $i<$this->entries; $i++ ) {
			$namelen = $this->stream->readByte();
			$size = $this->stream->readInteger();
			$offset = $this->stream->readInteger();
			$info = $this->stream->readByte();
			$name = $this->stream->read( $namelen );

			$this->metadata[$name] = array(
			self::Size		=> $size,
			self::Offset	=> $offset,
			self::Name		=> $name
			);
		}
	}

}

?>