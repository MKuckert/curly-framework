<?php

function doc() {
	$func=new ReflectionFunction(__FUNCTION__);
    var_dump($func->getDocComment());
}

doc();