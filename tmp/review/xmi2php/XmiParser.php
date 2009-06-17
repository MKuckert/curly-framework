<?php
	
	class XmiParser {
		
		private $internals = array( 'int', 'integer', 'void', 'double', 'float', 'array', 'mixed', 'bool', 'boolean' );
		private $doc = null;
		private $xpath = null;
		private $classDef = null;
		private $idLookup = array();
		public function getClassDef() {
			return( $this->classDef );
		}
		
		public function parse( $file ) {
			$this->doc = new DOMDocument();
			$this->doc->encoding = 'UTF-8';
			$this->doc->load( $file );
			
			$this->xpath = new DOMXPath( $this->doc );
			$this->xpath->registerNamespace( 'UML', 'org.omg.xmi.namespace.UML' );
			
			$result = $this->xpath->query( '//UML:Class' );
			if( is_null( $result ) ) {
				return;
			}
			foreach( $result as $node ) {
				$this->parseNode( $node );
			}
		}
		
		protected function parseNode( DOMNode $node ) {
			$value = $node->getAttribute( 'name' );
			if(
				$value==''
				or in_array( strtolower( $value ), $this->internals )
				or class_exists( $value )
			) {
				return;
			}
			
			$this->classDef[] = array(
				'name'			=> $value,
				'abstract'		=> $node->getAttribute( 'isAbstract' )=='true',
				'methods'		=> $this->parseMethods( $node ),
				'attributes'	=> $this->parseAttributes( $node )
			);
		}
		
		protected function parseVisibility( $visibility ) {
			$visibility = strtolower( trim( $visibility ) );
			switch( $visibility ) {
				case 'public':
				case 'private':
				case 'protected':
					return( $visibility );
				default:
					return( 'public' );
			}
		}
		
		protected function parseMethods( DOMNode $node ) {
			$methods = array();
			
			$result = $this->xpath->query( 'UML:Classifier.feature/UML:Operation', $node );
			if( is_null( $result ) ) {
				return( array() );
			}
			foreach( $result as $rNode ) {
				$methods[] = $this->parseMethod( $rNode );
			}
			return( $methods );
		}
		
		protected function parseMethod( DOMNode $node ) {
			return( array(
				'name'			=> $node->getAttribute( 'name' ),
				'visibility'	=> $this->parseVisibility( $node->getAttribute( 'visibility' ) ),
				'abstract'		=> $node->getAttribute( 'isAbstract' )=='true',
				'parameter'		=> $this->parseMethodParameter( $node )
			) );
		}
		
		protected function parseMethodParameter( DOMNode $node ) {
			$parameter = array();
			
			$result = $this->xpath->query( 'UML:BehavioralFeature.parameter/UML:Parameter[not(@kind="return")]', $node );
			if( is_null( $result ) ) {
				return( array() );
			}
			foreach( $result as $rNode ) {
				$parameter[] = array(
					'name'			=> $rNode->getAttribute( 'name' )
				);
			}
			return( $parameter );
		}
		
		protected function parseAttributes( DOMNode $node ) {
			$attributes = array();
			
			$result = $this->xpath->query( 'UML:Classifier.feature/UML:Attribute', $node );
			if( is_null( $result ) ) {
				return( array() );
			}
			
			foreach( $result as $rNode ) {
				$attributes[] = $this->parseAttribute( $rNode );
			}
			
			return( $attributes );
		}
		
		protected function parseAttribute( DOMNode $node ) {
			return( array(
				'name'			=> $node->getAttribute( 'name' ),
				'visibility'	=> $this->parseVisibility( $node->getAttribute( 'visibility' ) )
			) );
		}
		
	}
	
?>