<?php

class FS_TestVisitor implements FS_Visitor_Interface {
	public $sequence=array();
	public function visitNode(FS_Node $node) {
		$this->sequence[]=$node->getName();
		return true;
	}
}