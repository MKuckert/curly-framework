<?php

require_once "CurlyBracketDe/Page/Renderer.php";
require_once "CurlyX/Sourcecode/Php/SyntaxHighlighter.php";
require_once "Curly/Stream/String.php";

/**
 * CurlyBracketDe_Page_Builder
 * 
 * Kompiliert die curlybracket.de-Seite
 * 
 * @author Martin Kuckert
 * @copyright Copyright &copy; 2008 Martin Kuckert
 * @license New BSD License
 * @package CurlyBracketDe.Page
 * @since 1.0 - 27.11.2008
 * @version 1.0
 */
class CurlyBracketDe_Page_Builder {
	
	const PARAM_BASEURL='%BASEURL%';
	
	const STREAM_HEAD=CurlyBracketDe_Page_Renderer::STREAM_HEAD;
	const STREAM_CONTENT=CurlyBracketDe_Page_Renderer::STREAM_CONTENT;
	const STREAM_HEAD_FILE='head.php';
	const STREAM_CONTENT_FILE='content.php';
	
	/**
	 * @var array
	 * Der boolsche Wert gibt an, ob die OpenCloseTags ignoriert werden sollen.
	 */
	private $streamsToHighlight=array(self::STREAM_HEAD=>false, self::STREAM_CONTENT=>true);
	
	/**
	 * @var Zend_Config
	 */
	private $_config=NULL;
	
	/**
	 * @var CurlyBracketDe_Page_Renderer
	 */
	private $_renderer=NULL;
	
	/**
	 * @var CurlyX_Sourcecode_Php_SyntaxHighlighter
	 */
	private $_highlighter=NULL;
	
	/**
	 * @var array
	 */
	private $streams=array();
	
	/**
	 * @return Zend_Config
	 */
	public function getConfig() {
		return($this->_config);
	}
	
	/**
	 * @return void
	 * @param Zend_Config
	 */
	public function setConfig(Zend_Config $value) {
		$this->_config=$value;
	}
	
	/**
	 * @return CurlyBracketDe_Page_Renderer
	 */
	public function getRenderer() {
		if($this->_renderer===NULL) {
			$this->_renderer=new CurlyBracketDe_Page_Renderer();
		}
		return($this->_renderer);
	}
	
	/**
	 * @return void
	 * @param CurlyBracketDe_Page_Renderer
	 */
	public function setRenderer(CurlyBracketDe_Page_Renderer $value) {
		$this->_renderer=$value;
	}
	
	/**
	 * @return CurlyX_Sourcecode_Php_SyntaxHighlighter
	 */
	public function getHighlighter() {
		if($this->_highlighter===NULL) {
			$this->_highlighter=new CurlyX_Sourcecode_Php_SyntaxHighlighter();
		}
		return($this->_highlighter);
	}
	
	/**
	 * @return void
	 * @param CurlyX_Sourcecode_Php_SyntaxHighlighter
	 */
	public function setHighlighter(CurlyX_Sourcecode_Php_SyntaxHighlighter $value) {
		$this->_highlighter=$value;
	}
	
	/**
	 * @return void
	 */
	public function build() {
		$this->configure();
		$this->render();
		$this->highlight();
		
		$this->buildNavigation();
		
		$this->store();
	}
	
	protected function configure() {
		$config=$this->getConfig();
		
		$count=0;
		foreach($config->navigationItems as $item) {
			if(isset($item->visible) and $item->visible==false) {
				continue;
			}
			$count++;
		}
		
		$renderer=$this->getRenderer();
		$renderer->setNavigationItemCount($count);
		$renderer->setPageTitle($config->pageTitle);
		
		$highlighter=$this->getHighlighter();
		
		$this->streams=array(
			self::STREAM_HEAD			=> new Curly_Stream_String(),
			self::STREAM_HEAD_FILE		=> new Curly_Stream($config->outputPath.DIRECTORY_SEPARATOR.self::STREAM_HEAD_FILE, 'w'),
			self::STREAM_CONTENT		=> new Curly_Stream_String(),
			self::STREAM_CONTENT_FILE	=> new Curly_Stream($config->outputPath.DIRECTORY_SEPARATOR.self::STREAM_CONTENT_FILE, 'w')
		);
		
		$renderer->setSourceOutputStreams(array(
			CurlyBracketDe_Page_Renderer::STREAM_HEAD		=> $this->streams[self::STREAM_HEAD],
			CurlyBracketDe_Page_Renderer::STREAM_CONTENT	=> $this->streams[self::STREAM_CONTENT]
		));
	}
	
	protected function render() {
		$this->getRenderer()->render();
	}
	
	protected function highlight() {
		foreach($this->streamsToHighlight as $streamToHighlight=>$ignoreOpenCloseTags) {
			$stream=$this->streams[$streamToHighlight];
			$stream->resetPosition();
			
			$highlighter=$this->getHighlighter();
			$highlighter->setStream($stream);
			$highlighter->parseStream($stream);
			$highlighter->setOption(CurlyX_Sourcecode_Php_SyntaxHighlighter::OPT_IGNORE_OPENCLOSETAGS, $ignoreOpenCloseTags);
			$stream->resetPosition();
			$stream->truncate();
			$highlighter->highlight();
		}
	}
	
	protected function buildNavigation() {
		$stream=$this->streams[self::STREAM_HEAD];
		$stream->resetPosition();
		$highlighted=$stream->getHandle();
		
		$index=0;
		foreach($this->getConfig()->navigationItems->toArray() as $item) {
			if(isset($item['visible']) and $item['visible']==false) {
				continue;
			}
			$highlighted->replace(CurlyBracketDe_Page_Renderer::getNavigationItemPlaceholder($index++), $this->buildNavigationItem($item));
		}
	}
	
	protected function buildNavigationItem(array $item) {
		$retval='';
		$label=$item['label'];
		
		if(isset($item['url'])) {
			$title=isset($item['title'])?' title="'.$item['title'].'"':'';
			
			$retval='<a href="'.$this->prepareUrl($item['url']).'"'.$title.'>';
		}
		
		$retval.=$label;
		
		if(isset($item['url'])) {
			$retval.='</a>';
		}
		
		return($retval);
	}
	
	protected function prepareUrl($url) {
		$params=array(
			self::PARAM_BASEURL		=> Zend_Controller_Front::getInstance()->getBaseUrl()
		);
		return(strtr($url, $params));
	}
	
	protected function store() {
		foreach($this->streamsToHighlight as $streamToHighlight=>$ignoreOpenCloseTags) {
			$this->streams[$streamToHighlight]->resetPosition();
		}
		
		$this->streams[self::STREAM_HEAD_FILE]->writeStream($this->streams[self::STREAM_HEAD]);
		$this->streams[self::STREAM_CONTENT_FILE]->writeStream($this->streams[self::STREAM_CONTENT]);
	}
	
}
