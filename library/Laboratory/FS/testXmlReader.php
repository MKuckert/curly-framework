<?php

set_include_path(get_include_path().PATH_SEPARATOR.dirname(dirname(__FILE__)));
function __autoload($class) {
	require_once str_replace('_', DIRECTORY_SEPARATOR, $class).'.php';
}

$reader=new FS_Serialization_Xml_Reader();
$tree=$reader->readFile(dirname(__FILE__).'/serialization-example.xml');
