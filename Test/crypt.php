<?php
$cipher=MCRYPT_BLOWFISH;
$name='';
$data='';
$iv='aaaaaaaa';
$decrypt=mcrypt_decrypt($cipher, $name, $data, MCRYPT_MODE_CFB, $iv);

echo $decrypt;