<?php

/**
 * @since 19.10.2008
 */
class Curly_Bootstrap_Section_Auth implements Curly_Bootstrap_Section_Interface {
	
	public function dispatch(Curly_Bootstrap $bootstrap) {
		$config=$bootstrap->getConfig();
		if(!$config->__isset('auth')) {
			return;
		}
		
		$items=$config->get('auth');
		if(array_keys($items->toArray())!=range(0,$items->count()-1)){
			$items=array($items);
		}
		foreach($items as $key=>$item) {
			$id=$bootstrap->determineID($item->get('id'), count($items), 'auth', $key);
			$this->createAuthAdapter($id, $item, $bootstrap);
		}
	}
	
	protected function createAuthAdapter($id, Zend_Config $config, Curly_Bootstrap $bootstrap) {
		$adapter=$config->get('adapter');
		if(!($adapter instanceof Zend_Config) or !$adapter->__isset('type')) {
			throw new Curly_Bootstrap_Exception('The adapter value of an auth section is invalid');
		}
		
		$type=$adapter->get('type');
		$authAdapter=null;
		switch(strtolower($type)) {
			case 'dbtable':
				$dbAdapterID=$adapter->get('adapterID', 'database');
				$dbAdapter=Zend_Registry::get($dbAdapterID);
				if(!($dbAdapter instanceof Zend_Db_Adapter_Abstract)) {
					throw new Curly_Bootstrap_Exception('Invalid database adapter id '.$dbAdapterID.' used in auth section');
				}
				
				$authAdapter=new Zend_Auth_Adapter_DbTable(
					$dbAdapter,
					$adapter->get('tableName'),
					$adapter->get('identityColumn'),
					$adapter->get('credentialColumn'),
					$adapter->get('credentialTreatment')
				);
				break;
			case 'digest':
				$authAdapter=new Zend_Auth_Adapter_Digest(
					$adapter->get('filename'),
					$adapter->get('realm'),
					$adapter->get('identity'),
					$adapter->get('credential')
				);
				break;
			case 'http':
				$authAdapter=new Zend_Auth_Adapter_Http(
					$adapter->toArray()
				);
				
				// Resolver setzen
				if($adapter->__isset('resolver')) {
					$resolver=$adapter->get('resolver');
					foreach($resolver as $resType=>$res) {
						$resolverInstance=$this->createAuthHttpResolver($res);
						if($resType=='basic') {
							$authAdapter->setBasicResolver($resolverInstance);
						}
						else {
							$authAdapter->setDigestResolver($resolverInstance);
						}
					}
				}
				break;
			default:
				// Klassenname angegeben
				Zend_Loader::loadClass($type);
				$authAdapter=new $type($adapter->toArray());
		}
		
		Zend_Registry::set($id, $authAdapter);
	}
	
	protected function createAuthHttpResolver(Zend_Config $config) {
		$type=$config->get('type');
		$resolver=null;
		switch(strtolower($type)) {
			case 'file':
				$filename=$config->get('filename', '');
				$resolver=new Zend_Auth_Adapter_Http_Resolver_File($filename);
				break;
			default:
				// Klassenname angegeben
				Zend_Loader::loadClass($type);
				$resolver=new $type($config->toArray());
		}
		
		return($resolver);
	}
	
}