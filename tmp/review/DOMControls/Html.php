<?php
	
	class HtmlImplementation extends DOMImplementation {
		
		static private $_autoID = 0;
		
		public function createDocumentType() {
			return( parent::createDocumentType(
				'html',
				'-//W3C//DTD XHTML 1.0 Strict//EN',
				'http://www.w3.org/TR/xhtml1/DTD/Strict.dtd'
			) );
		}
		
		public function createDocument() {
			$doc = parent::createDocument(
				'http://www.w3.org/1999/xhtml',
				'html',
				$this->createDocumentType()
			);
			$doc->encoding = 'UTF-8';
			return( new HtmlDocument( $doc ) );
		}
		
		static public function getAutoID() {
			return( '__elem_'.self::$_autoID++ );
		}
		
	}
	
	class HtmlDocument {
		const FinishedInit = 1;
		const PreRendered = 2;
		
		public $state = 0;
		
		public $doc;
		
		public function __construct( DOMDocument $doc ) {
			$this->doc = $doc;
		}
		
		public function __get( $member ) {
			return( $this->doc->$member );
		}
		
		public function __set( $member, $value ) {
			$this->doc->$member = $value;
		}
		
		public function __call( $method, $arguments ) {
			return( call_user_func_array( array( $this->doc, $method ), $arguments ) );
		}
		
		public function finishInit() {
			if( $this->state>=self::FinishedInit ) {
				throw new Exception( 'Already finished Init' );
			}
			$this->state = self::FinishedInit;
			
			foreach( $this->doc->getElementsByTagName( '*' ) as $element ) {
				if( $element instanceof HtmlElement ) {
					$element->finishInit();
				}
			}
		}
		
		public function preRender() {
			if( $this->state>=self::PreRendered ) {
				throw new Exception( 'Already pre rendered' );
			}
			if( $this->state<self::FinishedInit ) {
				$this->finishInit();
			}
			$this->state = self::PreRendered;
			
			foreach( $this->doc->getElementsByTagName( '*' ) as $element ) {
				if( $element instanceof HtmlElement ) {
					$element->preRender();
				}
			}
		}
		
		public function saveXML() {
			$args = func_get_args();
			
			if( $this->state<self::PreRendered ) {
				$this->preRender();
			}
			
			return( call_user_func_array( array( $this->doc, 'saveXML' ), $args ) );
		}
		
	}
	
	abstract class HtmlElement extends DOMElement {
		abstract public function getTagName();
		public function __construct() {
			parent::__construct( $this->getTagName() );
		}
		public function finishInit() {
			
		}
		public function preRender() {
			
		}
	}
	
	class ATag extends HtmlElement {
		public function getTagName() {
			return( 'a' );
		}
		public function getHref() {
			return( $this->getAttribute( 'href' ) );
		}
		public function setHref( $href ) {
			$this->setAttribute( 'href', $href );
		}
	}
	
	class DIVTag extends HtmlElement {
		public function getTagName() {
			return( 'div' );
		}
	}
	
	class FORMTag extends HtmlElement {
		public function getTagName() {
			return( 'form' );
		}
	}
	
	class INPUTTag extends HtmlElement {
		public function getTagName() {
			return( 'input' );
		}
		public function getType() {
			return( $this->getAttribute( 'type' ) );
		}
		public function setType( $type ) {
			$this->setAttribute( 'type', $type );
		}
		public function __construct() {
			parent::__construct();
		}
		public function getName() {
			return( $this->getAttribute( 'name' ) );
		}
		public function setName( $name ) {
			$this->setAttribute( 'name', $name );
		}
		public function getValue() {
			return( $this->getAttribute( 'value' ) );
		}
		public function setValue( $value ) {
			$this->setAttribute( 'value', $value );
		}
		
		public function finishInit() {
			if( trim($this->getName())=='' ) {
				$this->setName( HtmlImplementation::getAutoID() );
			}
		}
	}
	
?>