<?php

class Test_CurlyX_Sourcecode_Php_SyntaxHighlighter extends Curly_Test {
	
	private $sourceDir='';
	private $targetDir='';
	
	protected function setUp() {
		$base=dirname(__FILE__);
		$this->sourceDir=$base.'/__highlight_testdata/';
		$this->targetDir=$base.'/__highlight_testdata/target/';
	}
	
	public function testFullDir() {
		foreach(glob($this->sourceDir.'*.php') as $file) {
			$input=$file;
			$output=$this->targetDir.basename($file);
			
			$stream=new Curly_Stream($output, 'w');
			$stream->write($this->getHtmlPre());
			
			$highlighter=new CurlyX_Sourcecode_Php_SyntaxHighlighter();
			$highlighter->parseFile($input, true);
			$highlighter->setStream($stream);
			$highlighter->highlight();
			
			$stream->write($this->getHtmlPost());
		}
	}
	
	private function getHtmlPre() {
		return(
'<html>
<head>
	<title>Highlight Test</title>
	<style type="text/css">
'.$this->getCSS().'
	</style>
</head>
<body>
	<pre>
');
	}
	
	private function getHtmlPost() {
		return(
'
	</pre>
</body>
</html>');
	}
	
	private function getCSS() {
		return('* {
	padding:		0;
	margin:			0;
}

pre {
	font-size:		8pt;
}

.sr {
	color:			blue;
}

.sl {
	color:			blue;
}

.sb {
	
}

.so {
	color:			#555;
}

.cn {
	
}

.cm {
	color:			gray;
	font-style:		italic;
}

.st {
	color:			green;
}

.in, .fl {
	color:			fuchsia;
}

.va {
	color:			darkred;
}

.sp {
	color:			red;
}

.ot {
	
}');
	}
	
}