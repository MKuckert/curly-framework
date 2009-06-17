<?php
	
	/**
	 * Rectangle
	 */
	class Rectangle {
		
		/**
		 * @var Point
		 */
		private $_point = null;
		
		/**
		 * @var Dimension
		 */
		private $_dimension = null;
		
		public function getX() {
			return( $this->_point->getX() );
		}
		
		public function setX( $x ) {
			$this->_point->setX( $x );
			return $this;
		}
		
		public function getY() {
			return( $this->_point->getY() );
		}
		
		public function setY( $y ) {
			$this->_point->setY( $y );
			return $this;
		}
		
		public function getWidth() {
			return( $this->_dimension->getWidth() );
		}
		
		public function setWidth( $w ) {
			$this->_dimension->setWidth( $w );
			return $this;
		}
		
		public function getHeight() {
			return( $this->_dimension->getHeight() );
		}
		
		public function setHeight( $h ) {
			$this->_dimension->setHeight( $h );
			return $this;
		}
		
		public function getPoint() {
			return( $this->_point );
		}
		
		public function getDimension() {
			return( $this->_dimension );
		}
		
		public function __construct( $x=0, $y=0, $w=0, $h=0 ) {
			if( $x instanceof Point ) {
				if( $y instanceof Point ) {
					$this->_point = $x;
					$this->_dimension = new Dimension( $y->getX() - $x->getX(), $y->getY() - $y->getY() );
					return;
				}
				else if( $y instanceof Dimension ) {
					$this->_point = $x;
					$this->_dimension = $y;
					return;
				}
			}
			
			$this->_point = new Point( $x, $y );
			$this->_dimension = new Dimension( $w, $h );
		}
		
		public function add( $x, $y=0, $w=0, $h=0 ) {
			if( $x instanceof Rectangle ) {
				$this->_point->add( $x->getPoint() );
				$this->_dimension->add( $x->getDimension() );
			}
			else if( $x instanceof Point ) {
				$this->_point->add( $x->getPoint() );
			}
			else if( $x instanceof Dimension ) {
				$this->_dimension->add( $x->getDimension() );
			}
			else {
				$this->_point->add( $x, $y );
				$this->_dimension->add( $w, $h );
			}
		}
		
		public function addX( $x ) {
			$this->_point->addX( $x );
		}
		
		public function addY( $y ) {
			$this->_point->addY( $y );
		}
		
		public function addWidth( $w ) {
			$this->_dimension->addWidth( $w );
		}
		
		public function addHeight( $h ) {
			$this->_dimension->addHeight( $h );
		}
		
		public function substract( $x, $y=0, $w=0, $h=0 ) {
			if( $x instanceof Rectangle ) {
				$this->_point->substract( $x->getPoint() );
				$this->_dimension->substract( $x->getDimension() );
			}
			else if( $x instanceof Point ) {
				$this->_point->substract( $x );
			}
			else if( $x instanceof Dimension ) {
				$this->_dimension->substract( $x );
			}
			else {
				$this->_point->substract( $x, $y );
				$this->_dimension->substract( $w, $h );
			}
		}
		
		public function substractX( $x ) {
			$this->_point->substractX( $x );
		}
		
		public function substractY( $y ) {
			$this->_point->substractY( $y );
		}
		
		public function substractWidth( $w ) {
			$this->_dimension->substractWidth( $w );
		}
		
		public function substractHeight( $h ) {
			$this->_dimension->substractHeight( $h );
		}
		
		public function equals( $that ) {
			return(
				$that instanceof Rectangle
				and $that->getPoint()->equals( $this->getPoint() )
				and $that->getDimension()->equals( $this->getDimension() )
			);
		}
		
	}
	
?>