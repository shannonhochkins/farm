<?php
	
	$state = $_POST['state'];

	switch ($state) {
	    case 'ALL':
	    	$command = "sh /mnt/raid/farm_script/_apps/_scripts/farm_ping.sh";
	    	$path = "/mnt/raid/farm_script/nodes_response/";
	    	if ( ! is_dir($path)) {
		        exit('Invalid diretory path');
		    } else {
		    	echo('valid diretory path');		    	
		    	array_map('unlink', glob($path . "*"));
		    	exec($command);
		    	
		    }
	        
	        break;
	    #default:

	    	#exec($command . " " . $state);	       	
	}
	echo $_POST['state'];
?>