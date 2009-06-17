<?php
require_once "../lib/Curly/FrameworkBundler.php";
require_once "../lib/Curly/SPL/NegateRegexIterator.php";

$bundler=new Curly_FrameworkBundler();
$bundler->setBasePath('F:/dev/Zend');
$bundler->setWantedPackages(array(
	'Zend_XmlRpc'
));
$bundler->bundle();
?>