<?php
defined('BOOTPATH') or define('BOOTPATH', dirname(__FILE__));

// Includepfad erweitern
$file=BOOTPATH.DIRECTORY_SEPARATOR.'INCLUDEPATH';
if(is_file($file)) {
	$rows=file($file);
	foreach($rows as $row) {
		list($key, $path)=explode(':', $row, 2);
		$path=trim($path);
		define('INCLUDEPATH_'.strtoupper($key), $path);
		set_include_path($path.PATH_SEPARATOR.get_include_path());
	}
}

// Initialisierung durchführen
require_once "Curly/Bootstrap.php";
$bootstrap = new Curly_Bootstrap();
$bootstrap->setBootPath(BOOTPATH);
$bootstrap->dispatch();

// Aufräumen
unset($file, $bootstrap);