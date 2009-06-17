<?php

class CurlyX_Archive_Dataset_String implements CurlyX_Archive_Dataset_Interface {
	private $name = '';
	private $contents = '';

	public function __construct( $name='', $contents='' ) {
		$this->setName( $name );
		$this->setContents( $contents );
	}

	public function getContentSize() {
		return( strlen( $this->contents ) );
	}

	public function getName() {
		return( $this->name );
	}

	public function setName( $name ) {
		$this->name = $name;
	}

	public function getContents() {
		return( $this->contents );
	}

	public function setContents( $contents ) {
		$this->contents = $contents;
	}
}

?>