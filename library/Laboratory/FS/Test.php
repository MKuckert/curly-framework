<?php

class FS_Test {
	
	public function suite() {
		$test=new self();
		$test->test();
	}
	
	public function test() {
		$tree=new FS_Tree('tree');
		
		$node1=$tree->createContainer('node1');
		$tree->addChildNode($node1);
		
		$node2=$tree->createContainer('node2');
		var_dump($node1->addChildNode($node2)===$node1);
		
		var_dump($node1->getFirstChildNode()===$node2);
		var_dump($node1->getLastChildNode()===$node2);
		var_dump($node2->getParentNode()===$node1);
		var_dump($node1->hasChildNodes());
		var_dump(!$node2->hasChildNodes());
		
		$node3=$tree->createContainer('node3');
		var_dump($node1->insertChildNodeBefore($node3, $node2)===$node1);
		var_dump($node1->getFirstChildNode()===$node3);
		var_dump($node1->getLastChildNode()===$node2);
		var_dump($node3->getPreviousSibling()===NULL);
		var_dump($node3->getNextSibling()===$node2);
		var_dump($node2->getPreviousSibling()===$node3);
		var_dump($node2->getNextSibling()===NULL);
		var_dump(!$node1->isLeaf());
		var_dump($node2->isLeaf());
		var_dump($node3->isLeaf());
		
		var_dump($node1->removeChildNode($node2)===$node1);
		$cn=$node1->getChildNodes();
		var_dump($cn[0]===$node3);
		var_dump($node3->getPreviousSibling()===NULL);
		var_dump($node3->getNextSibling()===NULL);
		var_dump($node3->getParentNode()===$node1);
		var_dump($node2->getParentNode()===NULL);
		var_dump($node2->getPreviousSibling()===NULL);
		var_dump($node2->getNextSibling()===NULL);
		
		var_dump($node1->replaceChildNode($node2, $node3)===$node1);
		var_dump($node3->getPreviousSibling()===NULL);
		var_dump($node3->getNextSibling()===NULL);
		var_dump($node3->getParentNode()===NULL);
		var_dump($node2->getParentNode()===$node1);
		var_dump($node2->getPreviousSibling()===NULL);
		var_dump($node2->getNextSibling()===NULL);
		
		var_dump(!$node1->isSolo());
		var_dump(!$node2->isSolo());
		var_dump($node3->isSolo());
		
		// Clone
		$clone1=clone $node1;
		var_dump($clone1->getFirstChildNode()==$node2);
		var_dump($clone1->getFirstChildNode()!==$node2);
		
		// Visitor
		$visitor=new FS_TestVisitor();
		$tree->acceptVisitor($visitor);
		var_dump($visitor->sequence===array(
			'tree:',
			'node1',
			'node2'
		));
		
		// FindNode
		$findNode=new FS_Visitor_FindNode('tree:/node1/node2');
		$tree->acceptVisitor($findNode);
		var_dump($findNode->wasSuccessful());
		var_dump($findNode->getFoundNode()===$node2);
		
		$findNode=new FS_Visitor_FindNode('tree:/node1');
		$tree->acceptVisitor($findNode);
		var_dump($findNode->wasSuccessful());
		var_dump($findNode->getFoundNode()===$node1);
		
		// getPathToNode
		var_dump($tree->getPathToNode($node1)->toString()==='tree://node1');
		var_dump($tree->getPathToNode($tree)->toString()==='tree://');
		
		// Metadaten
		$node4=$tree
			->createLeaf('node4')
			->setMeta(new FS_Metadata_Item('node4-data'));
		$node2->addChildNode($node4);
		var_dump($node4->getMeta()->getContents()=='node4-data');
		var_dump($node4->getMeta()->getAccessTime()==0);
		var_dump($node4->getMeta()->getModificationTime()==0);
		
		// Stream
		echo file_get_contents('tree://node1/node2/node4');
	}
	
}

set_include_path(
	get_include_path().PATH_SEPARATOR.
	dirname(dirname(__FILE__)).PATH_SEPARATOR.
	'D:/dev/Curly/HEAD/library'
);
function __autoload($class) {
	require_once str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
}

FS_Test::suite();
