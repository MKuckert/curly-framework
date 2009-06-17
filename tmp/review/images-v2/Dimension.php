<?php
	
	/**
	 * Dimension
	 */
	class Dimension {
		
		private $_w = 0;
		private $_h = 0;
		
		public function getWidth() {
			return( $this->_w );
		}
		
		public function setWidth( $w ) {
			$this->_w = abs( $w );
			return $this;
		}
		
		public function getHeight() {
			return( $this->_h );
		}
		
		public function setHeight( $h ) {
			$this->_h = abs( $h );
			return $this;
		}
		
		public function __construct( $w=0, $h=0 ) {
			$this->setWidth( $w );
			$this->setHeight( $h );
		}
		
		public function add( $w, $h=0 ) {
			if( $x instanceof Dimension ) {
				$this->addWidth( $x->getWidth() );
				$this->addHeight( $x->getHeight() );
			}
			else {
				$this->addWidth( $w );
				$this->addHeight( $h );
			}
			return $this;
		}
		
		public function addWidth( $w ) {
			$this->_w += abs( $w );
			return $this;
		}
		
		public function addHeight( $h ) {
			$this->_h += abs( $h );
			return $this;
		}
		
		public function substract( $w, $h=0 ) {
			if( $x instanceof Dimension ) {
				$this->substractWidth( $x->getWidth() );
				$this->substractHeight( $x->getHeight() );
			}
			else {
				$this->substractWidth( $w );
				$this->substractHeight( $h );
			}
			return $this;
		}
		
		public function substractWidth( $w ) {
			$this->setWidth( $this->_w - abs( $w ) );
			return $this;
		}
		
		public function substractHeight( $h ) {
			$this->setHeight( $this->_h - abs( $h ) );
			return $this;
		}
		
		public function equals( $that ) {
			return(
				$that instanceof Dimension
				and $that->getWidth()==$this->getWidth()
				and $that->getHeight()==$this->getHeight()
			);
		}
		
	}
	
?>