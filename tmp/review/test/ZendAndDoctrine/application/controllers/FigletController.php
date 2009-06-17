<?php
	
	require_once 'Zend/Controller/Action.php';

	class FigletController extends Zend_Controller_Action {
		
		public $figletFilesDirectory = 'figlet';
		
		public function preRenderAction() {
			$this->view->request = $this->getFrontController()->getRequest();
		}
		
		public function formAction() {
			$this->preRenderAction();
			
			$this->view->fonts = $this->getInstalledFigletFonts();
		}
		
		public function submitAction() {
			$this->preRenderAction();
			
			$valid = $this->isValid();
			$this->view->valid = $valid;
			
			if( $valid ) {
				$figlet = new Zend_Text_Figlet();
				$figlet->setFont( $this->figletFilesDirectory.DIRECTORY_SEPARATOR.$this->getRequest()->font );
				$this->view->figletRenderResult = $figlet->render( $this->getRequest()->text );
			}
		}
		
		public function isValid() {
			$req = $this->getRequest();
			
			$inArrayValidator = new Zend_Validate_InArray( $this->getInstalledFigletFonts() );
			$notEmptyValidator = new Zend_Validate_NotEmpty();
			
			return $inArrayValidator->isValid( $req->font )
			   and $notEmptyValidator->isValid( $req->text )
			;
		}
		
		public function getInstalledFigletFonts() {
			$figletFonts = array();
			
			foreach( glob( $this->figletFilesDirectory.DIRECTORY_SEPARATOR.'*.flf' ) as $file ) {
				$file = basename( $file );
				$figletFonts[$file] = $file;
			}
			
			return $figletFonts;
		}
		
	}
	
?>