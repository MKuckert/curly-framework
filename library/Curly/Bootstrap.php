<?php

require_once "Curly/Bootstrap/InstanceCreator.php";


/**
 * @since 14.10.2008
 */
class Curly_Bootstrap {
	
	const DEFAULT_APPLICATION_ID='default';
	
	const RUNTIME_MODE_DEBUG='debug';
	const RUNTIME_MODE_PERFORMANCE='performance';
	
	const APPID_PARAM='app';
	
	private $_lastDispatchedInstance=null;
	private $_bootPath;
	private $_runtimeMode=self::RUNTIME_MODE_DEBUG;
	private $_applicationID=self::DEFAULT_APPLICATION_ID;
	private $_creator=null;
	private $_config=null;
	
	public function getLastDispatchedInstance() {
		return($this->_lastDispatchedInstance);
	}
	
	protected function setLastDispatchedInstance($value) {
		$this->_lastDispatchedInstance=$value;
	}
	
	public function getBootPath() {
		return($this->_bootPath);
	}
	
	public function setBootPath($value) {
		$this->_bootPath=$value;
	}
	
	public function getRuntimeMode() {
		return($this->_runtimeMode);
	}
	
	public function setRuntimeMode($value) {
		$this->_runtimeMode=$value;
	}
	
	public function getApplicationID() {
		return($this->_applicationID);
	}
	
	public function setApplicationID($value) {
		$this->_applicationID=$value;
	}
	
	public function getConfig() {
		return($this->_config);
	}
	
	public function dispatch() {
		// Autoloader registrieren
		$this->registerAutoloader();
		
		// Konfigurationen laden
		$baseconfig=$this->loadAndRegisterConfig('baseconfig','baseconfig');
		
		$this->determineApplicationID();
		
		$runtimeMode=$baseconfig->get('runtimeMode');
		if($runtimeMode!==null) {
			$runtimeMode=$runtimeMode->get($this->getApplicationID(), self::RUNTIME_MODE_DEBUG);
		}
		else {
			$runtimeMode=self::RUNTIME_MODE_DEBUG;
		}
		$this->setRuntimeMode($runtimeMode);
		
		$appconfig=$this->loadAndRegisterConfig('appconfig', $this->getApplicationID().'.config', $runtimeMode);
		
		$this->_config=clone $baseconfig;
		$this->_config->merge($appconfig);
		Zend_Registry::set('config', $this->_config);
		
		// Errorreporting setzen
		$er=$this->_config->get('errorReporting', E_ALL|E_NOTICE);
		if(defined($er)) { // Konstante
			error_reporting(constant($er));
		}
		else if(is_string($er) and ((string)(int)$er)!=$er) { // Formel
			error_reporting(eval($er)); // Eval is Evil, aber in diesem Kontext legitim
		}
		else {
			error_reporting((int)$er);
		}
		
		// Standardgebietsschema setzen
		Zend_Locale::$compatibilityMode = false;
		Zend_Locale::setDefault($this->_config->get('defaultLocale', 'en'));
		
		// Anwendungsgebietsschema setzen
		if($this->_config->__isset('setAppLocale') and $this->isTrue($this->_config->get('setAppLocale'))) {
			Zend_Registry::set('Zend_Locale', new Zend_Locale(key(Zend_Locale::getDefault())));
		}
		
		// Standardzeitzone setzen
		date_default_timezone_set($this->_config->get('defaultTimezone', 'UTC'));
		
		// Instanzen erstellen
		$this->_creator=new Curly_Bootstrap_InstanceCreator($this->_config);
		$this->_creator->createInstances();
		
		// FrontController initialisieren
		$this->initFrontController();
		
		// Instanz registrieren
		$this->registerAsLastDispatchedInstance();
		
		// Weitere Bootstrapper aufrufen
		if($this->_config->__isset('additionalSections')) {
			$additionalSection=$this->_config->get('additionalSections');
			if(array_keys($additionalSection->toArray())==range(0,$additionalSection->count()-1)){
				foreach($additionalSection as $item) {
					$this->dispatchBootstrapSection($item);
				}
			}
			else {
				$this->dispatchBootstrapSection($additionalSection);
			}
		}
	}
	
	protected function dispatchBootstrapSection($type) {
		if(strpos($type, '_')===false) {
			$type='Curly_Bootstrap_Section_'.ucfirst($type);
		}
		
		Zend_Loader::loadClass($type);
		$instance=new $type();
		$instance->dispatch($this);
	}
	
	protected function registerAutoloader() {
		require_once 'Zend/Loader.php';
		Zend_Loader::registerAutoload();
	}
	
	protected function determineApplicationID() {
		$baseconfig=Zend_Registry::get('baseconfig');
		$apps=$baseconfig->get('applications');
		if($apps===null) {
			return;
		}
		
		if(isset($_REQUEST[self::APPID_PARAM])
			and ctype_alpha($_REQUEST[self::APPID_PARAM])
			and $apps->__isset($_REQUEST[self::APPID_PARAM])
		) {
			$this->setApplicationID($id=$_REQUEST[self::APPID_PARAM]);
		}
		else {
			$this->setApplicationID($id=$baseconfig->get('defaultApplication'));
		}
		
		if(!$apps->__isset($id)) {
			throw new Curly_Bootstrap_Exception('Application with ID '.$id.' not found');
		}
		else if(!$this->isTrue($apps->get($id)->get('active',false))) {
			throw new Curly_Bootstrap_Exception('Application with ID '.$id.' is not active');
		}
	}
	
	protected function loadAndRegisterConfig($name, $filename, $section=null) {
		$configs=$this->loadConfigFile($filename, $section);
		Zend_Registry::set($name, $configs);
		return($configs);
	}
	
	protected function loadConfigFile($filename, $section) {
		$filename=$this->getBootPath().DIRECTORY_SEPARATOR.$filename;
		// INI-Datei
		if(is_file($filename.'.ini')){
			$config=new Zend_Config_Ini($filename.'.ini', $section, true);
		}
		// XML-Datei
		else if(is_file($filename.'.xml')){
			$config=new Zend_Config_Xml($filename.'.xml', $section, true);
		}
		// YAML-Datei
		else if(is_file($filename.'.yml')){
			$config=new Curly_Config_Yaml($filename.'.yml', $section, true);
		}
		else{
			$config=new Zend_Config(array(), true);
		}
		
		return($config);
	}
	
	// ControllerFront einrichten	
	protected function initFrontController() {
		$frontController=Zend_Controller_Front::getInstance();
		
		// Standardwerte
		$throwExceptions=false;
		$params=array();
		$baseUrl=null;
		
		// Konfiguration lesen
		if($this->_config->__isset('frontController')) {
			$frontControllerConfig=$this->_config->get('frontController');
			
			$throwExceptions=$this->isTrue($frontControllerConfig->get('throwExceptions', $throwExceptions));
			$params=$frontControllerConfig->get('params', array());
			$baseUrl=$frontControllerConfig->get('baseUrl');
		}
		
		// Konfigurationswerte übernehmen
		$frontController
			->throwExceptions($throwExceptions)
			->setParams($params)
			->setBaseUrl($baseUrl);
		
		// Applikationspfad hinzufügen
		$appID=$this->_applicationID;
		$default=$this->getBootPath().DIRECTORY_SEPARATOR
						.'applications'.DIRECTORY_SEPARATOR
						.$appID;
		$appPath=$this->_config
					->get('applications')
					->get($appID)
					->get('directory', $default);
		$frontController->addControllerDirectory($appPath.DIRECTORY_SEPARATOR.'controllers');
		$appModulePath=$appPath.DIRECTORY_SEPARATOR.'modules';
		if(is_dir($appModulePath)) {
			$frontController->addModuleDirectory($appModulePath);
		}
		
		// TODO: Wirklich hinzufügen?
		$path=$this->getBootPath().DIRECTORY_SEPARATOR.'modules';
		$frontController->addModuleDirectory($path);
	}
	
	protected function registerAsLastDispatchedInstance() {
		$this->_lastDispatchedInstance=$this;
	}
	
	protected function isTrue($value) {
		return(trim(strtolower($value))=='true' or intval($value)!=0);
	}
	
	public function determineID($config, $count, $default, $key) {
		return($this->_creator->determineID($config, $count, $default, $key));
	}
	
}