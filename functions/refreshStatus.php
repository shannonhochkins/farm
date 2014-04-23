<?php
	// State will be all or an ip address
	$state = $_POST['state'];
	$path = "/mnt/raid/farm_script/nodes_status/";


	if ( ! is_dir($path)) {
        exit('Invalid diretory path');
    } else {	
		switch ($state) {
		    case 'ALL':
		    	$files = glob($path . "*");
		    	foreach($files as $file){ // iterate files
				  	if(is_file($file)) {
					    unlink($file); // delete file
					}
				}    			    	
			    	
			    	
			     
		        break;   	
		    default:
		    	$file = glob($path . $state);		    	
	    		if(is_file($file[0])) {			  		
				    unlink($file[0]); // delete file
				}
			    	

		    	break;
		}
	}
	
?>