<?php
	error_reporting( E_ALL );
	
	require "../Test.php";
	require "../../Core.php";
	
	Core::initialize();
	
	class TreeTest extends Test {
		public function buildTree( $children ) {
			$tree = new Tree();
			$root = $tree->getRoot();
			
			for( $i=0; $i<$children; $i++ ) {
				$root->getChildNodes()->add( $tree->createNode() );
			}
			
			return( $tree );
		}
		
		public function testBuild() {
			$count = 10;
			$tree = $this->buildTree( $count );
			$root = $tree->getRoot();
			$node2 = $root->getChildNodes()->getAt( 1 );
			
			$this->assertEqual( $node2->getNextSibling()->getID(), 3, 'getNextSibling: Baum fehlerhaft' );
			$this->assertEqual( $node2->getPreviousSibling()->getID(), 1, 'getPreviousSibling: Baum fehlerhaft' );
			$this->assertEqual( $node2->getParentNode()->getID(), 0, 'getParentNode: Baum fehlerhaft' );
			$this->assertEqual( $root->getChildNodes()->getCount(), $count, 'getChildNodes: Baum fehlerhaft' );
			
			$root->getChildNodes()->removeAt( 3 );
			$this->assertEqual( $root->getChildNodes()->getCount(), $count-1, 'getChildNodes: Baum fehlerhaft' );
			
			$node2->setNextSibling( $root->getChildNodes()->getAt( 6 ) );
			$this->assertEqual( $node2->getNextSibling()->getID(), 8, 'setNextSibling: Baum fehlerhaft' );
			
			$tree->dump();
		}
		
	}
	
	$test = new TreeTest();
	echo $test->run();
	
?>