<?php

class Test_CurlyX_Db_Driver_MySQL extends Test_CurlyX_Db_Driver {
	
	protected function getCredentials() {
		return(array(
			'mysql:host=localhost;dbname=test',
			'root',
			'',
			array()
		));
	}
	
}