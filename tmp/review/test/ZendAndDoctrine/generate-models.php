<?php
	
	include dirname( __FILE__ )."/config.php";
	
	Doctrine::generateModelsFromYaml( Stats::configs()->schema->path, Stats::configs()->models->path );
	
?>