<?php

if(!isset($_GET['test'])) {
	die('usage: '.basename(__FILE__).'?test=&lt;test&gt;');
}

$testname=$_GET['test'];

$result = include "dispatch.php";
echo '<pre>'.htmlspecialchars($result).'</pre>';