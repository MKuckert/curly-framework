<?php

/**
 * @since 18.10.2008
 * @todo create in Untermethoden umlagern, damit Bootstrap_Sections diese
 * nutzen können.
 */
class Curly_Bootstrap_InstanceCreator {
	
	/**
	 * @var Zend_Config
	 */
	private $config;
	
	/**
	 * @return Zend_Config
	 */
	public function getConfig() {
		return($this->config);
	}
	
	/**
	 * @return void
	 * @param Zend_Config
	 */
	public function setConfig(Zend_Config $config) {
		$this->config=$config;
	}
	
	public function __construct(Zend_Config $config) {
		$this->setConfig($config);
	}
	
	public function createInstances() {
		$this->createDbAdapter();
		$this->createCache();
		$this->createLog();
		$this->createTranslate();
	}
	
	// Datenbankadapter einrichten
	protected function createDbAdapter() {
		if(!$this->config->__isset('database')){
			return;
		}
		
		$items=$this->config->get('database');
	
		// Nur einen Parameter übergeben
		if(array_keys($items->toArray())!=range(0,$items->count()-1)){
			$items=array($items);
		}
		
		$c=count($items);
		if($c<=0){
			return;
		}
		
		$adapters=array();
		foreach($items as $key=>$item){
			$adapter=Zend_Db::factory($item);
			
			$id=$this->determineID($item->get('id'), $c, 'database', $key);
			
			$adapters[$id]=$adapter;
			Zend_Registry::set($id, $adapter);
		}
		
		// Nur ein Adapter
		if($c==1){
			Zend_Db_Table::setDefaultAdapter($adapter);
		}
		// Mehrere Adapter
		else{
			// Default ist angegeben.
			if($this->config->__isset('defaultDbAdapter')
				and isset($adapters[$id=$config->get('defaultDbAdapter')])
			){
				Zend_Db_Table::setDefaultAdapter($adapters[$id]);
			}
			// Ersten Adapter verwenden
			else {
				Zend_Db_Table::setDefaultAdapter(reset($adapters));
			}
		}
	}
	
	protected function createCache() {
		if(!$this->config->__isset('cache')){
			return;
		}
		
		$items=$this->config->get('cache');
		
		// Nur einen Parameter übergeben
		if(array_keys($items->toArray())!=range(0,$items->count()-1)){
			$items=array($items);
		}
		
		$c=count($items);
		if($c<=0){
			return;
		}
		
		foreach($items as $key=>$item){
			$frontend=$item->get('frontend');
			$backend=$item->get('backend');
			
			$frontendOptions=$frontend->get('params', array());
			if($frontendOptions instanceof Zend_Config){
				$frontendOptions=$frontendOptions->toArray();
			}
			
			$backendOptions=$backend->get('params', array());
			if($backendOptions instanceof Zend_Config){
				$backendOptions=$backendOptions->toArray();
			}
			
			$instance=Zend_Cache::factory(
				$frontend->get('type', 'Core'),
				$backend->get('type', 'File'),
				$frontendOptions,
				$backendOptions
			);
			
			$id=$this->determineID($item->get('id'), $c, 'cache', $key);
			
			Zend_Registry::set($id, $instance);
		}
	}
	
	protected function createLog() {
		if(!$this->config->__isset('log')){
			return;
		}
		
		$items=$this->config->get('log');
		
		// Nur einen Parameter übergeben
		if(array_keys($items->toArray())!=range(0,$items->count()-1)){
			$items=array($items);
		}
		
		$c=count($items);
		if($c<=0){
			return;
		}
		
		foreach($items as $key=>$item){
			$instance=new Zend_Log();
			
			// Writer erzeugen
			$writerItems=$item->get('writer');
			
			if(array_keys($writerItems->toArray())!=range(0,$writerItems->count()-1)){
				$instance->addWriter($this->createLogWriter($writerItems));
			}
			else if($writerItems instanceof Zend_Config) {
				foreach($writerItems as $writerItem) {
					$instance->addWriter($this->createLogWriter($writerItem));
				}
			}
			
			// Filter
			if($item->__isset('filter')) {
				$filterItems=$item->get('filter');
				if(array_keys($filterItems->toArray())!=range(0,$filterItems->count()-1)){
					$instance->addFilter($this->createLogFilter($filterItems));
				}
				else {
					foreach($filterItems as $filterItem) {
						$instance->addFilter($this->createLogFilter($filterItem));
					}
				}
			}
			
			// Prioritäten
			if($item->__isset('priorities')){
				$priorities=$item->get('priorities');
				if($priorities instanceof Zend_Config) {
					$priorities=$priorities->toArray();
				}
				
				if(is_array($priorities)) {
					foreach($priorities as $name=>$priority) {
						$instance->addPriority($name, (int)$priority);
					}
				}
				else {
					throw new Curly_Bootstrap_Exception('The priorities-entry has to be an array');
				}
			}
			
			$id=$this->determineID($item->get('id'), $c, 'log', $key);
			
			Zend_Registry::set($id, $instance);
		}
	}
	
	protected function createLogWriter(Zend_Config $item) {
		$type=strtolower($item->get('type'));
		$writer=null;
		switch($type) {
			case 'stream':
				$writer=new Zend_Log_Writer_Stream(
					$item->get('url', 'php://output'),
					$item->get('mode', 'a')
				);
				break;
			case 'db':
				$adapterID=$item->get('adapterID');
				if(!Zend_Registry::isRegistered($adapterID)) {
					throw new Curly_Bootstrap_Exception('Database adapter with id '.$adapterID.' not found');
				}
				
				$map=$item->get('columnMapping');
				$writer=new Zend_Log_Writer_Db(
					Zend_Registry::get($adapterID),
					$item->get('tableName'),
					$map
				);
				break;
			default:
				// Klassenname angegeben
				if(strpos($type,'_')!==false) {
					$writer=new $type();
				}
				else {
					$class='Zend_Log_Writer_'.ucfirst($type);
					$writer=new $class();
				}
		}
		
		// Formatter
		if($item->__isset('formatter')) {
			$writer->setFormatter($this->createLogFormatter($item->get('formatter')));
		}
		
		// Filter
		if($item->__isset('filter')) {
			$filterItems=$item->get('filter');
			if(array_keys($filterItems->toArray())!=range(0,$filterItems->count()-1)){
				$writer->addFilter($this->createLogFilter($filterItems));
			}
			else {
				foreach($filterItems as $filterItem) {
					$writer->addFilter($this->createLogFilter($filterItem));
				}
			}
		}
		
		return($writer);
	}
	
	protected function createLogFormatter(Zend_Config $item) {
		$type=strtolower($item->get('type'));
		$formatter=null;
		switch($type) {
			case 'simple':
				$formatter=new Zend_Log_Formatter_Simple(
					$item->get('format')
				);
				break;
			case 'xml':
				$map=$item->get('elementMap');
				if($map instanceof Zend_Config) {
					$map=$map->toArray();
				}
				$formatter=new Zend_Log_Formatter_Xml(
					$item->get('rootElement', 'logEntry'),
					$map
				);
				break;
			default:
				// Klassenname angegeben
				if(strpos($type,'_')!==false) {
					$formatter=new $type();
				}
				else {
					$class='Zend_Log_Formatter_'.ucfirst($type);
					$formatter=new $class();
				}
		}
		
		return($formatter);
	}
	
	protected function createLogFilter(Zend_Config $item) {
		$type=strtolower($item->get('type'));
		$filter=null;
		switch($type) {
			case 'message':
				$filter=new Zend_Log_Filter_Message(
					$item->get('regexp')
				);
				break;
			case 'priority':
				$filter=new Zend_Log_Filter_Priority(
					(int)$item->get('priority', Zend_Log::DEBUG),
					$item->get('operator', '<=')
				);
				break;
			case 'suppress':
				$suppress=$item->get('suppress', false);
				if(strtolower($suppress)=='false') {
					$suppress=false;
				}
				$filter=new Zend_Log_Filter_Suppress(
					$suppress
				);
				break;
			default:
				// Klassenname angegeben
				if(strpos($type,'_')!==false) {
					$filter=new $type();
				}
				else {
					$class='Zend_Log_Filter_'.ucfirst($type);
					$filter=new $class();
				}
		}
		
		return($filter);
	}
	
	public function determineID($config, $count, $default, $key) {
		if($config!==null) {
			return($config);
		}
		elseif($count==1) {
			return($default);
		}
		else {
			return($default.'_'.$key);
		}
	}
	
	protected function createTranslate() {
		if(!$this->config->__isset('translate')){
			return;
		}
		
		$items=$this->config->get('translate');
		
		// Nur einen Parameter übergeben
		if(array_keys($items->toArray())!=range(0,$items->count()-1)){
			$items=array($items);
		}
		
		$c=count($items);
		if($c<=0){
			return;
		}
		
		foreach($items as $key=>$item){
			$adapter=$item->get('adapter');
			if(!($adapter instanceof Zend_Config)){
				throw new Curly_Bootstrap_Exception('Every translate settings requires an adapter section');
			}
			
			$type=strtolower($adapter->get('type', 'array'));
			$instance=$this->createTranslateAdapter($type,$adapter);
			
			$id=$this->determineID($item->get('id'), $c, 'translate', $key);
			
			Zend_Registry::set($id, $instance);
		}
	}
	
	protected function createTranslateAdapter($type, Zend_Config $config) {
		if($config->__isset('translations')) {
			$translations=$config->get('translations')->toArray();
		}
		else {
			$translations=array(
				array(
					'data'		=> $config->get('data'),
					'locale'	=> $config->get('locale', 'de'),
					'options'	=> $config->get('options', array())
				)
			);
		}
		
		foreach($translations as $translation) {
			if(!isset($translation['data'])) {
				throw new Curly_Bootstrap_Exception('Every translation setting requires a data value');
			}
			
			// Werte separieren
			$data=$translation['data'];
			if($data instanceof Zend_Config) {
				$data=$data->toArray();
			}
			
			$locale=isset($translation['locale'])?$translation['locale']:'de';
			if(isset($translation['options'])) {
				$options=$translation['options'];
				if($options instanceof Zend_Config) {
					$options=$options->toArray();
				}
			}
			else {
				$options=array();
			}
			
			// Initialisieren
			if(isset($instance)) {
				$instance->addTranslation(
					$data,
					$locale,
					$options
				);
			}
			else {
				$instance=new Zend_Translate(
					$type,
					$data,
					$locale,
					$options
				);
			}
		}
		
		return($instance);
	}
	
}