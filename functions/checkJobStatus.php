<?php

	function checkStatus() {
//		echo "s";
		$state = 'ALL';
		
		$jobStatus = array();

		switch ($state) {
		    case 'ALL':
		    	
		    	$path = "/mnt/raid/farm_script/logs/";
		    	if ( ! is_dir($path)) {
			        exit('Invalid diretory path');
			    } else {
			    	$files = glob($path . "*");
			    	foreach($files as $file){ // iterate files
					  	if(is_file($file)) {					  		
							$stringIP = substr(strrchr($file,'/'), 1);
							$stringIP = str_replace('.txt', '', $stringIP);
							
							$jobStatus[$stringIP] = 'noError';							

							if(grepText("Fatal Error.", $file)) {					
								$jobStatus[$stringIP] = 'fatalError';		
							}
							if(grepText("File not found:", $file)) {					
								$jobStatus[$stringIP] = 'fileNotFound';		
							}
							if(grepText("/makeCameraRenderable.mel line ", $file)) {					
								$jobStatus[$stringIP] = 'cameraNotFound';		
							}
							if(grepText("Failed Before Start Frame.", $file)) {					
								$jobStatus[$stringIP] = 'failedBeforeStart';
							}
							if (grepText(".mb completed.", $file)) {
								$jobStatus[$stringIP] = 'renderCompleted';
							}
							if (grepText("Invalid flag: ", $file)) {
								$jobStatus[$stringIP] = 'invalidFlag';
							}

							
						}
					}
			    }	        
		        break;		    
		    default:
		    	// This would be reserved for checking a single nodes status.
		}

		
		return $jobStatus;
	}

	function grepText($text, $f) {
		$found = exec('grep '.escapeshellarg($text).' ' . $f);
		return $found;
	}

?>