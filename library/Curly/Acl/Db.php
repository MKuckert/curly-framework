<?php

require_once "Curly/Acl.php";

/**
 * @since 26.10.2008
 */
class Curly_Acl_Db extends Curly_Acl {
	
	const ROLES='AclRoles';
	const RESOURCES='AclResources';
	const ROLEPARENTS='AclRoleParents';
	const RULES='AclRules';
	
	const DB_ADAPTER='db';
	
	/**
	 * @var array
	 */
	private $_tableParameter=array(self::ROLES, self::RESOURCES, self::ROLEPARENTS, self::RULES);
	
	/**
	 * Konstruktor
	 */
	public function __construct() {
		$this->initOptions();
	}
	
	protected function initOptions() {
		$opt=array();
		foreach($this->_tableParameter as $param) {
			$opt[$param]=$param;
		}
		$this->setOptions($opt);
	}
	
	public function __wakeup() {
		$this->initOptions();
	}
	
	/**
	 * @return Zend_Db_Adapter_Abstract
	 */
	public function getDb() {
		if(!isset($this->_options[self::DB_ADAPTER])) {
			throw new Curly_Acl_Exception('The Db-ACL requires a Zend_Db_Adapter instance to be set as options parameter '.self::DB_ADAPTER);
		}
		$db=$this->_options[self::DB_ADAPTER];
		return($db);
	}
	
	/**
	 * @return string
	 */
	public function getTableName($type) {
		return($this->_options[$type]);
	}
	
	/**
	 * @return string
	 */
	public function getRolesTable() {
		return($this->getTableName(self::ROLES));
	}
	
	/**
	 * @return string
	 */
	public function getResourcesTable() {
		return($this->getTableName(self::RESOURCES));
	}
	
	/**
	 * @return string
	 */
	public function getRoleParentsTable() {
		return($this->getTableName(self::ROLEPARENTS));
	}
	
	/**
	 * @return string
	 */
	public function getRulesTable() {
		return($this->getTableName(self::RULES));
	}
	
	public function load() {
		$db=$this->getDb();
		
		$rolesTable=$this->getRolesTable();
		$roleParentsTable=$this->getRoleParentsTable();
		
		// Rollen laden
		$rows=$db->fetchAll(
			$db
				->select()
				->from($rolesTable)
		);
		foreach($rows as $row) {
			$parentRoles=$db->fetchAll(
				$db
					->select()
					->from($roleParentsTable, array('parent', 'child'))
					->where('child=?', $row['id'])
			);
			
			if(count($parentRoles)>0) {
				foreach($parentRoles as $parentRole) {
					parent::addRole(new Zend_Acl_Role($row['id']), $parentRole['parent']);
				}
			}
			else {
				parent::addRole(new Zend_Acl_Role($row['id']));
			}
		}
		
		// Ressourcen laden
		$resourcesTable=$this->getResourcesTable();
		
		$rows=$db->fetchAll(
			$db
				->select()
				->from($resourcesTable)
		);
		foreach($rows as $row) {
			parent::add(new Zend_Acl_Resource($row['id']), $row['parent']);
		}
		
		// Rechte laden
		$rulesTable=$this->getRulesTable();
		
		$rows=$db->fetchAll(
			$db
				->select()
				->from($rulesTable)
		);
		foreach($rows as $row) {
			$type=$row['allow']==1 ? self::TYPE_ALLOW : self::TYPE_DENY;
			parent::setRule(self::OP_ADD, $type, $row['role'], $row['resource'], $row['privilege']);
		}
	}
	
	public function truncate() {
		$db=$this->getDb();
		foreach($this->_tableParameter as $table) {
			$db->delete($table, 1);
		}
		
		$this->removeRoleAll();
		$this->removeAll();
	}
	
	public function addRole(Zend_Acl_Role_Interface $role, $parents=null) {
        parent::addRole($role, $parents);
        
        $db=$this->getDb();
        
        $id=$role->getRoleId();
        $db->insert($this->getRolesTable(), array(
        	'id' => $id
        ));
        
        if($parents!==null) {
        	if(!is_array($parents)) {
        		$parents=array($parents);
        	}
        	$roleParentTable=$this->getRoleParentsTable();
        	foreach ($parents as $parent) {
				if($parent instanceof Zend_Acl_Role_Interface) {
					$parent=$parent->getRoleId();
				}
		        $db->insert($roleParentTable, array(
					'parent'	=> $parent,
					'child'		=> $id
				));
			}
        }
        
		return($this);
	}

    public function removeRole($role) {
    	parent::removeRole($role);
    	
        if($role instanceof Zend_Acl_Role_Interface) {
        	$role=$role->getRoleId();
        }
        
        $db=$this->getDb();
        $db->delete($this->getRolesTable(), $db->quoteInto('id=?', $role));
    	
    	return($this);
    }
    
    public function removeRoleAll() {
    	parent::removeRoleAll();
    	
    	$this->getDb()->delete($this->getRolesTable(), 1);
    	
    	return($this);
    }
    
	public function add(Zend_Acl_Resource_Interface $resource, $parent=null) {
		parent::add($resource, $parent);
		
		if($parent instanceof Zend_Acl_Resource_Interface) {
			$parent=$parent->getResourceId();
		}
		else if($parent!==NULL) {
			$parent=(string)$parent;
		}
		
		$id=$resource->getResourceId();
		$this->getDb()->insert($this->getResourcesTable(), array(
			'id'			=> $id,
			'parent'		=> $parent
		));
		
		return($this);
	}
	
    public function remove($resource) {
    	parent::remove($resource);
    	
        if($resource instanceof Zend_Acl_Resource_Interface) {
        	$resource=$resource->getResourceId();
        }
        $db=$this->getDb();
        $db->delete($this->getResourcesTable(), $db->quoteInto('id=?', $resource));
    	
    	return($this);
    }
    
    public function removeAll() {
    	parent::removeAll();
    	
    	$this->getDb()->delete($this->getResourcesTable(), 1);
    	
    	return($this);
    }
    
	public function setRule($operation, $type, $roles=null, $resources=null, $privileges=null, Zend_Acl_Assert_Interface $assert=null) {
		parent::setRule($operation, $type, $roles, $resources, $privileges, $assert);
	
		if(!is_array($roles)) {
			$roles=array($roles);
		}
		if(!is_array($resources)) {
			$resources=array($resources);
		}
		if(!is_array($privileges)) {
			$privileges=array($privileges);
		}
		
		$db=$this->getDb();
		
		$rulesTable=$this->getRulesTable();
		$method=($operation==self::OP_ADD ? 'insert' : 'remove');
		$allow=($type==self::TYPE_ALLOW);
		foreach($roles as $role) {
			if($role instanceof Zend_Acl_Role_Interface) {
				$role=$role->getRoleId();
			}
			foreach($resources as $resource) {
				if($resource instanceof Zend_Acl_Resource_Interface) {
					$resource=$resource->getResourceId();
				}
				
				foreach($privileges as $privilege) {
					$db->$method($rulesTable, array(
						'privilege'	=> $privilege,
						'role'		=> $role,
						'resource'	=> $resource,
						'allow'		=> (int)$allow
					));
				}
			}
		}
		
		return($this);
	}
	
	public function __sleep() {
		return(array('_roleRegistry','_resources','_rules'));
	}
	
}