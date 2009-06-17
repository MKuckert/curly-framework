<?php

class CurlyX_Archive_Dataset_File implements CurlyX_Archive_Dataset_Interface {
	private $path = '';
	public $removeExtension = false;

	public function __construct( $path='', $removeExtension=false ) {
		$this->setPath( $path );
		$this->removeExtension = $removeExtension;
	}

	public function getContentSize() {
		return( filesize( $this->path ) );
	}

	public function getName() {
		$basename = basename( $this->path );
			
		// Dateierweiterung abtrennen
		if( $this->removeExtension ) {
			$pos = strrpos( $basename, '.' );
			if( $pos!==false ) {
				$basename = substr( $basename, 0, $pos );
			}
		}

		return( $basename );
	}

	public function getContents() {
		return( file_get_contents( $this->path ) );
	}

	public function getPath() {
		return( $this->path );
	}

	public function setPath( $filepath ) {
		$this->path = $filepath;
	}

}

?>