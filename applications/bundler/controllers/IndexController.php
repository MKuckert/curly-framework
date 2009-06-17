<?php
	
	require_once "Curly/FrameworkBundler.php";
	
	class IndexController extends Zend_Controller_Action {
		
		/**
		 * @var Curly_FrameworkBundler
		 */
		private $bundler=NULL;
		
		private function getBundler() {
			if($this->bundler===NULL) {
				$this->bundler=new Curly_FrameworkBundler();
				$this->bundler->setBasePath(INCLUDEPATH_ZEND);
			}
			return($this->bundler);
		}
		
		public function initView() {
			$this->view->headTitle('Bundler - CurlyBracket.de');
			$this->view->headScript()->appendFile('assets/script.js');
			$this->view->headLink()->appendStylesheet('assets/style.css');
		}
		
		public function indexAction() {
			$this->initView();
			$this->view->packages=$this->getBundler()->getPackages();
		}
		
		public function bundleAction() {
			$items=$this->getRequest()->getParam('BundleItem');
			if(!is_array($items)) {
				header("HTTP/1.1 400 Bad Request");
				exit;
			}
			
			$name=uniqid($_SERVER['REMOTE_ADDR'].'_'.microtime(true), rand(0, 1000));
			$filename='../tmp/'.$name.'.zip';
			
			$bundler=$this->getBundler();
			$bundler->setWantedPackages($items);
			$bundler->setArchiveFile($filename);
			$bundler->bundle();
			
			header('HTTP/1.1 200 OK'); 
			header('Content-type: application/octet-stream'); 
			header('Content-Disposition: attachment; filename=Bundle.zip');
			readfile($filename);
			unlink($filename);
			exit;
		}
		
	}
	
?>