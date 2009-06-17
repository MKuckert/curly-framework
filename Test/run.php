<?php
// Keine Argumente übergeben
if($argc<=1) {
	die(<<<__USAGE
usage: {$argv[0]} <testname>
__USAGE
	);
}

$testname=$argv[1];

$result = include "dispatch.php";
echo $result;