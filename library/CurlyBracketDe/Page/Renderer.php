<?php

require_once "Curly/Stream.php";
require_once "Zend/Text/Figlet.php";

/**
 * CurlyBracketDe_Page_Renderer
 * 
 * Erstellt die curlybracket.de-Quelldatei
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyBracketDe.Page
 * @since 1.0 - 26.04.2008
 * @version 1.0
 */
class CurlyBracketDe_Page_Renderer {
	
	const NAVIGATIONITEM_INDEX='{INDEX}';
	const NAVIGATIONITEM_PLACEHOLDER='__NAVIGATIONITEM_{INDEX}__';
	
	const CONTENT_TITLE_PLACEHOLDER='__TITLE__';
	const CONTENT_TITLE_UC_PLACEHOLDER='__TITLE_UC__';
	const CONTENT_CONTENTS_PLACEHOLDER='__CONTENTS__';
	const CONTENT_TITLESTARS=59;
	
	const STREAM_HEAD='head';
	const STREAM_CONTENT='content';
	
	/**
	 * @var string Seitentitel
	 */
	private $_pageTitle='';
	
	/**
	 * @var array
	 */
	private $sourceOutputStreams;
	
	/**
	 * @var integer
	 */
	private $_navigationItemCount=0;
		
	/**
	 * @return string
	 */
	public function getPageTitle() {
		return($this->_pageTitle);
	}
	
	/**
	 * @return void
	 * @param string
	 */
	public function setPageTitle($value) {
		$this->_pageTitle=Curly_Types_Ensure::string($value);
	}
	
	/**
	 * Gibt die Streams zur Quellcode-Ausgabe zurück.
	 * 
	 * @return array
	 */
	public function getSourceOutputStreams() {
		return($this->sourceOutputStreams);
	}
	
	/**
	 * Setzt die Streams zur Quellcode-Ausgabe.
	 * 
	 * @return void
	 * @param array
	 */
	public function setSourceOutputStreams(array $value) {
		$this->sourceOutputStreams=$value;
	}
	
	/**
	 * Gibt den Stream für die übergebene Ausgabe zurück.
	 * 
	 * @return Curly_Stream
	 * @param string
	 */
	public function getSourceOutputStream($name) {
		if(!isset($this->sourceOutputStreams[$name])) {
			$this->sourceOutputStreams[$name]=new Curly_Stream_String();
		}
		return($this->sourceOutputStreams[$name]);
	}
	
	/**
	 * Setzt den Stream für die übergebene Ausgabe.
	 * 
	 * @return void
	 * @param string
	 * @param Curly_Stream
	 */
	public function setSourceOutputStream($name, Curly_Stream $stream) {
		$this->sourceOutputStreams[$name]=$stream;
	}
	
	/**
	 * @return integer
	 */
	public function getNavigationItemCount() {
		return $this->_navigationItemCount;
	}
	
	/**
	 * @return void
	 * @param integer
	 */
	public function setNavigationItemCount($value) {
		$this->_navigationItemCount=Curly_Types_Ensure::int($value);
	}
	
	/**
	 * @return void
	 */
	public function render() {
		$this->renderHead($this->sourceOutputStreams[self::STREAM_HEAD]);
		$this->renderBanner();
		$this->renderNavigation();
		
		$this->renderContent();
	}
	
	/**
	 * Rendert den Dateikopf
	 * 
	 * @return void
	 */
	private function renderHead(Curly_Stream $stream) {
		$stream->write("<?php\n");
	}
	
	/**
	 * @return void
	 */
	private function renderBanner() {
		$figlet=new Zend_Text_Figlet(array(
			'HandleParagraphs'		=> false,
			'Justification'			=> Zend_Text_Figlet::JUSTIFICATION_LEFT,
			'OutputWidth'			=> 200,
			'SmushMode'				=> Zend_Text_Figlet::SM_BIGX,
			'Font'					=> BOOTPATH.'/figlet/small.flf'
		));
		
		$rendered=$figlet->render(strtoupper($this->getPageTitle()));
		
		$delim="//\t";
		$rendered=$delim.str_replace("\n", "\n".$delim, rtrim($rendered))."\n".$delim."\n\n";
		
		$this->sourceOutputStreams[self::STREAM_HEAD]->write($rendered);
	}
	
	/**
	 * @return void
	 */
	private function renderNavigation() {
		$stream=$this->sourceOutputStreams[self::STREAM_HEAD];
		$stream->write('$this->Menu');
		
		for($i=0, $n=$this->getNavigationItemCount(); $i<$n; $i++) {
			$stream->write('->addLink( "'.self::getNavigationItemPlaceholder($i).'" )');
		}
		
		$stream->write(";\n");
	}
	
	/**
	 * @return void
	 */
	private function renderContent() {
		$stars=str_repeat('*', self::CONTENT_TITLESTARS);
		
		$stream=$this->sourceOutputStreams[self::STREAM_CONTENT];
		$stream->writeLine('<?php');
		$stream->writeLine('/'.$stars);
		$stream->writeLine(self::CONTENT_TITLE_PLACEHOLDER);
		$stream->writeLine($stars.'/');
		$stream->writeLine('$content=<<<'.self::CONTENT_TITLE_UC_PLACEHOLDER);
		$stream->writeLine();
		$stream->writeLine(self::CONTENT_CONTENTS_PLACEHOLDER);
		$stream->writeLine();
		$stream->writeLine(self::CONTENT_TITLE_UC_PLACEHOLDER.';');
	}
	
	static public function getNavigationItemPlaceholder($index) {
		return(str_replace(self::NAVIGATIONITEM_INDEX, $index, self::NAVIGATIONITEM_PLACEHOLDER));
	}
	
}
