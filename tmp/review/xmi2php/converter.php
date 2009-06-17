<?php
	error_reporting( E_ALL | E_STRICT );
	
	if( $argc<3 ) {
		die( usage() );
	}
	else if( !is_file( $argv[1] ) ) {
		die( 'File '.$argv[1].' not found!' );
	}
	else if( !is_dir( $argv[2] ) ) {
		die( 'Directory '.$argv[2].' not found!' );
	}
	
	include "XmiParser.php";
	include "ClassBuilder.php";
	
	$parser = new XmiParser();
	$parser->parse( $argv[1] );
	echo "Found ".count( $parser->getClassDef() )." class definitions.";
	
	$builder = new ClassBuilder();
	$builder->setClassDef( $parser->getClassDef() );
	$builder->build( $argv[2] );
	
	function usage() {
		global $argv;
		return <<<RET
usage: $argv[0] <xmi-file> <output-dir>
    xmi-file      Path to the xmi-file to be parsed.
    output-dir    Path to the directory where generated class-files should
                  be stored.
RET;
	}
?>