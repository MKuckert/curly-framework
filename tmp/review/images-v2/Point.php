<?php
	
	/**
	 * Point
	 */
	class Point {
		
		private $_x = 0;
		private $_y = 0;
		
		public function getX() {
			return( $this->_x );
		}
		
		public function setX( $x ) {
			$this->_x = abs( $x );
			return $this;
		}
		
		public function getY() {
			return( $this->_y );
		}
		
		public function setY( $y ) {
			$this->_y = abs( $y );
			return $this;
		}
		
		public function __construct( $x=0, $y=0 ) {
			$this->setX( $x );
			$this->setY( $y );
		}
		
		public function add( $x, $y=0 ) {
			if( $x instanceof Point ) {
				$this->addX( $x->getX() );
				$this->addY( $x->getY() );
			}
			else {
				$this->addX( $x );
				$this->addY( $y );
			}
			return $this;
		}
		
		public function addX( $x ) {
			$this->_x += abs( $x );
			return $this;
		}
		
		public function addY( $y ) {
			$this->_y += abs( $y );
			return $this;
		}
		
		public function substract( $x, $y=0 ) {
			if( $x instanceof Point ) {
				$this->substractX( $x->getX() );
				$this->substractY( $x->getY() );
			}
			else {
				$this->substractX( $x );
				$this->substractY( $y );
			}
			return $this;
		}
		
		public function substractX( $x ) {
			$this->setX( $this->_x - abs( $x ) );
			return $this;
		}
		
		public function substractY( $y ) {
			$this->setY( $this->_y - abs( $y ) );
			return $this;
		}
		
		public function equals( $that ) {
			return(
				$that instanceof Point
				and $that->getX()==$this->getX()
				and $that->getY()==$this->getY()
			);
		}
		
	}
	
?>