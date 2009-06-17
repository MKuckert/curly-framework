<?php

class Test_CurlyX_Xml_Parser_EventDriven extends Curly_Test {
	
	private $testfile;
	
	private $parser;
	
	protected function setUp() {
		$this->testfile=dirname(__FILE__).'/testfile.xml';
	}
		
	public function testSimple() {
		$this->parser=new CurlyX_Xml_Parser_EventDriven(CurlyX_Xml_Parser_Interface::ENCODING_UTF8, true);
		$this->parser->addEventListener(CurlyX_Xml_Parser_EventDriven::EVENT_START_ELEMENT, array($this, 'onStartElement'));
		$this->parser->addEventListener(CurlyX_Xml_Parser_EventDriven::EVENT_END_ELEMENT, array($this, 'onEndElement'));
		$this->parser->addEventListener(CurlyX_Xml_Parser_EventDriven::EVENT_CDATA, array($this, 'onCData'));
		$this->parser->addEventListener(CurlyX_Xml_Parser_EventDriven::EVENT_DEFAULT, array($this, 'onDefault'));
		$this->parser->addEventListener(CurlyX_Xml_Parser_EventDriven::EVENT_EXT_ENTITY_REF, array($this, 'onExtEntityRef'));
		$this->parser->addEventListener(CurlyX_Xml_Parser_EventDriven::EVENT_NOTATION_DECL, array($this, 'onNotationDecl'));
		$this->parser->addEventListener(CurlyX_Xml_Parser_EventDriven::EVENT_PROCESSING_INSTR, array($this, 'onProcessingInstr'));
		$this->parser->addEventListener(CurlyX_Xml_Parser_EventDriven::EVENT_UNPARSED_ENTITY_DECL, array($this, 'onUnparsedEntityDecl'));
		$this->parser->addEventListener(CurlyX_Xml_Parser_EventDriven::EVENT_START_NS_DECL, array($this, 'onStartNS'));
		
		$stream=new Curly_Stream($this->testfile, 'r');
		while($stream->getHasMore()) {
			$this->parser->parse($stream->readLine());
		}
	}
	
	private function assertValidEvent(CurlyX_Xml_Parser_Event  $event, array $params) {
		$found=0;
		foreach($event->getAvailableParams() as $param) {
			if(in_array($param, $params, true)) {
				$found++;
			}
			else {
				var_dump($event->getAvailableParams(), $params);
				$this->failure('Parameter '.$param.' nicht erwartet');
			}
		}
		$this->assertEqual($found, count($params), 'Nicht alle Parameter erhalten');
		$this->assertEqual($this->parser, $event->getSource(), 'Source-Parameter fehlerhaft');
	}
	
	private function getAvailableEventParams(CurlyX_Xml_Parser_Event $event) {
		$out='';
		foreach($event->getAvailableParams() as $param) {
			$out.=$param.'=';
			if(is_array($event->$param)) {
				$out.='[';
				foreach($event->$param as $k=>$v) {
					$out.=$k.'=>'.$v.' ';
				}
				$out.=']';
			}
			else {
				$out.='"'.$event->$param.'"';
			}
			$out.=' ';
		}
		return $out;
	}
	
	private function dump($func, CurlyX_Xml_Parser_Event $event) {
		echo $func.': '.$this->getAvailableEventParams($event).PHP_EOL;
	}
	
	public function onStartElement(CurlyX_Xml_Parser_Event $event) {
		$this->dump(__FUNCTION__, $event);
		
		$wanted=array('name', 'attributes');
		$this->assertValidEvent($event, $wanted);
	}
	
	public function onEndElement(CurlyX_Xml_Parser_Event $event) {
		$this->dump(__FUNCTION__, $event);
		
		$wanted=array('name');
		$this->assertValidEvent($event, $wanted);
	}
	
	public function onCData(CurlyX_Xml_Parser_Event $event) {
		$this->dump(__FUNCTION__, $event);
		
		$wanted=array('data');
		$this->assertValidEvent($event, $wanted);
	}
	
	public function onDefault(CurlyX_Xml_Parser_Event $event) {
		$this->dump(__FUNCTION__, $event);
		
		$wanted=array('data');
		$this->assertValidEvent($event, $wanted);
	}
	
	public function onExtEntityRef(CurlyX_Xml_Parser_Event $event) {
		$this->dump(__FUNCTION__, $event);
		
		$wanted=array('openEntityNames', 'base', 'systemID', 'publicID');
		$this->assertValidEvent($event, $wanted);
	}
	
	public function onNotationDecl(CurlyX_Xml_Parser_Event $event) {
		$this->dump(__FUNCTION__, $event);
		
		$wanted=array('notationName', 'base', 'systemID', 'publicID');
		$this->assertValidEvent($event, $wanted);
	}
	
	public function onProcessingInstr(CurlyX_Xml_Parser_Event $event) {
		$this->dump(__FUNCTION__, $event);
		
		$wanted=array('target', 'data');
		$this->assertValidEvent($event, $wanted);
	}
	
	public function onUnparsedEntityDecl(CurlyX_Xml_Parser_Event $event) {
		$this->dump(__FUNCTION__, $event);
		
		$wanted=array('entityName', 'base', 'systemID', 'publicID', 'notationName');
		$this->assertValidEvent($event, $wanted);
	}
	
	public function onStartNS(CurlyX_Xml_Parser_Event $event) {
		$this->dump(__FUNCTION__, $event);
		
		$wanted=array('prefix', 'uri');
		$this->assertValidEvent($event, $wanted);
	}
	
}