<?php
	
	include dirname( __FILE__ )."/config.php";
	
	$sql = Doctrine::generateSqlFromModels( Stats::configs()->models->path );
	file_put_contents( 'schema.sql', $sql );
	
	$connection = Doctrine_Manager::connection();
	$connection->exec( 'drop table if exists statistic_item' );
	$connection->exec( $sql );
?>